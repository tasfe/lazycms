<?php
/**
 * +---------------------------------------------------------------------------+
 * | LL                             LLLL   LL     L   LLLL               LLLL  |
 * | LL                            LL   L  LLL   LL  LL   L             LL  LL |
 * | LL      LLLL   LLLLL  LL  LL  LL      LLLL LLL  LL         LL  LL      LL |
 * | LL         LL     LL  LL  LL  LL      L LLL LL  LLLLL      LL  LL     LL  |
 * | LL      LLLLL    LL    LLLL   LL      L  L  LL   LLLLL     LL  LL    LL   |
 * | LL     LL  LL   LL     LLLL   LL      L     LL      LL      LLLL    LL    |
 * | LL     LL  LL  LL       LL    LL   L  L     LL  L   LL      LLLL   LL     |
 * | LLLLLL  LLLLL  LLLLL    LL     LLLL   L     LL   LLLL        LL    LLLLLL |
 * |                        LL                                                 |
 * |                        LL                                                 |
 * +---------------------------------------------------------------------------+
 * | Copyright (C) 2007-2008 LazyCMS.net All rights reserved.                  |
 * +---------------------------------------------------------------------------+
 * | LazyCMS is free software. This version use Apache License 2.0             |
 * | See LICENSE.txt for copyright notices and details.                        |
 * +---------------------------------------------------------------------------+
 */
require '../../global.php';
/**
 * 管理员管理
 * 
 */
// *** *** www.LazyCMS.net *** *** //
function lazy_before(){
    System::purview();
    System::tabs(
        t('myaccount').':myaccount.php'
    );
}
// *** *** www.LazyCMS.net *** *** //
function lazy_main(){
    $db = get_conn();
    $_USER     = System::getAdmin();
    $adminid   = $_USER['adminid'];
    $oldpass   = isset($_POST['oldpass']) ? $_POST['oldpass'] : null;
    $newpass   = isset($_POST['newpass']) ? $_POST['newpass'] : null;
    $newpass1  = isset($_POST['newpass1']) ? $_POST['newpass1'] : null;
    $adminmail = isset($_POST['adminmail']) ? $_POST['adminmail'] : null;
    $language  = isset($_POST['language']) ? $_POST['language'] : null;
    $val = new Validate();
    if ($val->method()) {
        if (!empty($oldpass) || !empty($newpass) || !empty($newpass1)) {
            $isoldpass = false;
            $res = $db->query("SELECT `adminpass`,`adminkey` FROM `#@_system_admin` WHERE `adminname`=? LIMIT 0,1;",$_USER['adminname']);
            if ($rs = $db->fetch($res)) {
                $md5pass = md5($oldpass.$rs['adminkey']);
                if ($md5pass == $rs['adminpass']) {
                    $newkey  = substr($md5pass,0,6);
                    $newpass = md5($newpass.$newkey);
                    $isoldpass = true;
                }
            }
            $val->check('oldpass|1|'.t('myaccount/check/oldpass').'|6-30;oldpass|3|'.t('myaccount/check/oldpass1').'|'.$isoldpass);
            $val->check('newpass|1|'.t('myaccount/check/password').'|6-30;newpass|2|'.t('myaccount/check/repassword').'|newpass1');
        }
        $val->check('adminmail|0|'.t('myaccount/check/email').';adminmail|validate|'.t('myaccount/check/email1').'|4');
        if ($val->isVal()) {
            $val->out();
        } else {
            $row = array(
                'adminmail' => $adminmail,
                'language' => $language,
            );
            if (!empty($newpass) && !empty($newkey)) {
                $row = array_merge($row,array(
                    'adminpass' => $newpass,
                    'adminkey'  => $newkey,
                ));
                // 重置登陆信息
                Cookie::set('adminpass',$newpass);
            }
            Cookie::set('language',$language);
            $db->update('#@_system_admin',$row,DB::quoteInto('`adminid` = ?',$adminid));
            success(t('myaccount/alert/success'),0);
        }
    } else {
        $adminname = h2c($_USER['adminname']);
        $adminmail = h2c($_USER['adminmail']);
        $language  = h2c($_USER['language']);

    }
    System::header(t('myaccount'));
    
    echo '<form id="form1" name="form1" method="post" action="">';
    
    echo '<fieldset><legend rel="tab">'.t('myaccount').'</legend>';

    echo '<p><label>'.t('myaccount/name').':</label><strong>'.$adminname.'</strong></p>';
    echo '<p><label>'.t('myaccount/oldpass').':</label><input class="in w200" type="password" name="oldpass" id="oldpass" /></p>';
    echo '<p><label>'.t('myaccount/newpass').':</label><input class="in w200" type="password" name="newpass" id="newpass" /></p>';
    echo '<p><label>'.t('myaccount/renewpass').':</label><input class="in w200" type="password" name="newpass1" id="newpass1" /></p>';
    echo '<p><label>'.t('myaccount/email').':</label><input class="in w300" type="text" name="adminmail" id="adminmail" value="'.$adminmail.'" /></p>';
    echo '<p><label>'.t('myaccount/language').':</label>';
    echo '<select name="language" id="language">';
    echo form_opts('@.language','lang','<option value="#value#"#selected#>#name#</option>',$language);
    echo '</select></p>';

    echo '</fieldset>';
    
    echo but('save').'</form>';
}
// *** *** www.LazyCMS.net *** *** //
function lazy_after(){
    System::footer();
}