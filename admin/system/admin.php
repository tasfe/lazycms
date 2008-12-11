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
    System::purview('System.Admin');
    System::tabs(
        l('Admins').':admin.php;'.
        l('Admins add').':admin.php?action=edit;'
    );
}
// *** *** www.LazyCMS.net *** *** //
function lazy_main(){
    System::header(l('Admins'));
    $db = get_conn();
    $ds = new Recordset();
    $ds->create("SELECT * FROM `#@_system_admin` ORDER BY `adminid` DESC");
    $ds->action = PHP_FILE."?action=set";
    $ds->but = $ds->button('unlock:'.l('Unlock').'|lock:'.l('Lock').'').$ds->plist();
    $ds->td("cklist(K[0]) + K[0] + ') <a href=\"".PHP_FILE."?action=edit&adminid=' + K[0] + '\">' + K[1] + '</a>'");
    $ds->td("K[2]");
    $ds->td("K[3]");
    $ds->td("lock(K[4])");
    $ds->open();
    $ds->thead = '<tr><th>ID) '.t('Admins name').'</th><th>'.t('Admins email').'</th><th>'.t('Admins language').'</th><th>'.t('Admins state').'</th></tr>';
    while ($rs = $ds->result()) {
        $ds->tbody("E(".$rs['adminid'].",'".t2js(h2c($rs['adminname']))."','".t2js(h2c($rs['adminmail']))."','".t2js(h2c(langbox($rs['language'])))."',".$rs['islocked'].");");
    }
    $ds->close();
    $ds->display();
}
// *** *** www.LazyCMS.net *** *** //
function lazy_set(){
    $db = get_conn();
    $submit = isset($_POST['submit']) ? strtolower($_POST['submit']) : null;
    $lists  = isset($_POST['lists']) ? $_POST['lists'] : null;
    empty($lists) ? alert(t('Admins alert not select')) : null ;
    switch($submit){
        case 'lock':
            $db->update('#@_system_admin',array('islocked'=>1),"`adminid` IN({$lists})");
            success(t('Admins execute lock success'),1);
            break;
        case 'unlock':
            $db->update('#@_system_admin',array('islocked'=>0),"`adminid` IN({$lists})");
            success(t('Admins execute unlock success'),1);
            break;
        case 'delete':
            $db->delete('#@_system_admin',"`adminid` IN({$lists})");
            success(t('Admins execute delete success'),1);
            break;
        default :
            error(l('Error invalid'));
            break;
    }
}
// *** *** www.LazyCMS.net *** *** //
function lazy_edit(){
    $db = get_conn();
    $adminid    = isset($_REQUEST['adminid']) ? $_REQUEST['adminid'] : 0;
    $adminname = isset($_POST['adminname']) ? $_POST['adminname'] : null;
    $adminpass = isset($_POST['adminpass']) ? $_POST['adminpass'] : null;
    $adminmail = isset($_POST['adminmail']) ? $_POST['adminmail'] : null;
    $language  = isset($_POST['language']) ? $_POST['language'] : null;
    $title     = empty($adminid) ? l('Admins add') : l('Admins edit');
    $val = new Validate();
    if ($val->method()) {
        $inSQL = !empty($adminid) ? " AND `adminid`<>'{$adminid}'" : null;
        $val->check('adminname|1|'.l('Admins check name').'|1-30;adminname|4|'.l('Admins check name already exist')."|SELECT COUNT(adminid) FROM `#@_system_admin` WHERE `adminname`='#pro#'{$inSQL}");
        if (empty($adminid)) {
            $val->check('adminpass|1|'.l('Admins check password length error').'|6-30;adminpass|2|'.l('Admins check password inconsistent').'|adminpass1');    
        }
        $val->check('adminmail|0|'.l('Admins check email').';adminmail|validate|'.l('Admins check email error format').'|4');
        if ($val->isVal()) {
            $val->out();
        } else {
            if (empty($adminid)) {
                $db->insert('#@_system_admin',array(
                    'adminname' => $adminname,
                    'adminpass' => md5($adminpass),
                    'userkey'  => '',
                    'adminmail' => $adminmail,
                    'language' => $language,
                    'regdate'  => now(),
                ));
                $text = l('Admins execute add success');
            } else {
                $row = array(
                    'adminname' => $adminname,
                    'adminmail' => $adminmail,
                    'language' => $language,
                );
                if (!empty($adminpass)) {
                    $row = array_merge($row,array(
                        'adminpass' => md5($adminpass),
                        'userkey'  => '',
                    ));
                }
                $db->update('#@_system_admin',$row,DB::quoteInto('`adminid` = ?',$adminid));
                $text = l('Admins execute edit success');
            }
            alert($text,0);
        }
    } else {
        if (!empty($adminid)) {
            $res = $db->query("SELECT * FROM `#@_system_admin` WHERE `adminid`=?",$adminid);
            if ($rs = $db->fetch($res)) {
                $adminname = h2c($rs['adminname']);
                $adminmail = h2c($rs['adminmail']);
                $language  = h2c($rs['language']);
            }
        }
    }
    
    System::header($title);
    
    echo '<form id="form1" name="form1" method="post" action="">';
    
    echo '<fieldset><legend rel="tab">'.$title.'</legend>';

    echo '<p><label>'.l('Admins name').':</label><input class="in w200" type="text" name="adminname" id="adminname" value="'.$adminname.'" /></p>';
    echo '<p><label>'.l('Admins password').':</label><input class="in w200" type="password" name="adminpass" id="adminpass" /></p>';
    echo '<p><label>'.l('Admins repeat password').':</label><input class="in w200" type="password" name="adminpass1" id="adminpass1" /></p>';
    echo '<p><label>'.l('Admins email').':</label><input class="in w300" type="text" name="adminmail" id="adminmail" value="'.$adminmail.'" /></p>';
    
    echo '<p><label>'.l('Admins language').':</label>';
    echo '<select name="language" id="language">';
    echo form_opts('@.language','lang','<option value="#value#"#selected#>#name#</option>',$language);
    echo '</select></p>';

    echo '</fieldset>';
    
    echo but('save').'<input name="adminid" type="hidden" value="'.$adminid.'" /></form>';
}
// *** *** www.LazyCMS.net *** *** //
function lazy_after(){
    System::footer();
}