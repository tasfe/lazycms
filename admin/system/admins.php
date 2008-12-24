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
    System::purview('system::admins');
    System::tabs(
        t('admins').':admins.php;'.
        t('admins/add').':admins.php?action=edit;'
    );
}
// *** *** www.LazyCMS.net *** *** //
function lazy_main(){
    System::header(t('admins'));
    $db = get_conn();
    $ds = new Recordset();
    $ds->create("SELECT * FROM `#@_system_admin` ORDER BY `adminid` DESC");
    $ds->action = PHP_FILE."?action=set";
    $ds->but = $ds->button('unlock:'.t('system::unlock').'|lock:'.t('system::lock').'').$ds->plist();
    $ds->td("cklist(K[0]) + K[0] + ') <a href=\"".PHP_FILE."?action=edit&adminid=' + K[0] + '\">' + K[1] + '</a>'");
    $ds->td("K[2]");
    $ds->td("K[3]");
    $ds->td("lock(K[4])");
    $ds->open();
    $ds->thead = '<tr><th>ID) '.t('admins/name').'</th><th>'.t('admins/email').'</th><th>'.t('admins/language').'</th><th>'.t('admins/state').'</th></tr>';
    while ($rs = $ds->result()) {
        $ds->tbody("E(".$rs['adminid'].",'".t2js(h2c($rs['adminname']))."','".t2js(h2c($rs['adminmail']))."','".t2js(h2c(langbox($rs['language'])))."',".$rs['islocked'].");");
    }
    $ds->close();
    $ds->display();
}
// *** *** www.LazyCMS.net *** *** //
function lazy_set(){
    $db = get_conn();
    $_USER  = System::getAdmin();
    $submit = isset($_POST['submit']) ? strtolower($_POST['submit']) : null;
    $lists  = isset($_POST['lists']) ? $_POST['lists'] : null;
    empty($lists) ? ajax_alert(t('admins/alert/noselect')) : null ;
    switch($submit){
        case 'lock':
            $db->update('#@_system_admin',array('islocked'=>1),array("`adminid`<>{$_USER['adminid']}","`adminid` IN({$lists})"));
            ajax_success(t('admins/alert/lock'),1);
            break;
        case 'unlock':
            $db->update('#@_system_admin',array('islocked'=>0),array("`adminid`<>{$_USER['adminid']}","`adminid` IN({$lists})"));
            ajax_success(t('admins/alert/unlock'),1);
            break;
        case 'delete':
            $db->delete('#@_system_admin',array("`adminid`<>{$_USER['adminid']}","`adminid` IN({$lists})"));
            ajax_success(t('admins/alert/delete'),1);
            break;
        default :
            ajax_error(t('system::error/invalid'));
            break;
    }
}
// *** *** www.LazyCMS.net *** *** //
function lazy_edit(){
    $db = get_conn();
    $adminid   = isset($_REQUEST['adminid']) ? $_REQUEST['adminid'] : 0;
    $adminname = isset($_POST['adminname']) ? $_POST['adminname'] : null;
    $adminpass = isset($_POST['adminpass']) ? $_POST['adminpass'] : null;
    $adminmail = isset($_POST['adminmail']) ? $_POST['adminmail'] : null;
    $purview   = isset($_POST['purview']) ? $_POST['purview'] : null;
    $purview   = is_array($purview) ? implode(',',$purview) : null;
    $language  = isset($_POST['language']) ? $_POST['language'] : null;
    $title     = empty($adminid) ? t('admins/add') : t('admins/edit');
    $val = new Validate();
    if ($val->method()) {
        $inSQL = !empty($adminid) ? " AND `adminid`<>'{$adminid}'" : null;
        $val->check('adminname|1|'.t('admins/check/name').'|1-30;adminname|4|'.t('admins/check/name1')."|SELECT COUNT(adminid) FROM `#@_system_admin` WHERE `adminname`='#pro#'{$inSQL}");
        if (empty($adminid)) {
            $val->check('adminpass|1|'.t('admins/check/password').'|6-30;adminpass|2|'.t('admins/check/repassword').'|adminpass1');    
        }
        $val->check('adminmail|0|'.t('admins/check/email').';adminmail|validate|'.t('admins/check/email1').'|4');
        if ($val->isVal()) {
            $val->out();
        } else {
            if (empty($adminid)) {
                $db->insert('#@_system_admin',array(
                    'adminname' => $adminname,
                    'adminpass' => md5($adminpass),
                    'adminkey'  => '',
                    'adminmail' => $adminmail,
                    'purview'   => $purview,
                    'language' => $language,
                ));
                $text = t('admins/alert/add');
            } else {
                $row = array(
                    'adminname' => $adminname,
                    'adminmail' => $adminmail,
                    'purview'   => $purview,
                    'language' => $language,
                );
                if (!empty($adminpass)) {
                    $row = array_merge($row,array(
                        'adminpass' => md5($adminpass),
                        'adminkey'  => '',
                    ));
                }
                $db->update('#@_system_admin',$row,DB::quoteInto('`adminid` = ?',$adminid));
                $text = t('admins/alert/edit');
            }
            ajax_success($text,0);
        }
    } else {
        if (!empty($adminid)) {
            $res = $db->query("SELECT * FROM `#@_system_admin` WHERE `adminid`=?",$adminid);
            if ($rs = $db->fetch($res)) {
                $adminname = h2c($rs['adminname']);
                $adminmail = h2c($rs['adminmail']);
                $purview   = h2c($rs['purview']);
                $language  = h2c($rs['language']);
            }
        }
    }
    
    System::script('LoadScript("system.admins");');
    System::header($title);
    
    echo '<form id="form1" name="form1" method="post" action="">';
    
    echo '<fieldset><legend rel="tab">'.$title.'</legend>';

    echo '<p><label>'.t('admins/name').':</label><input class="in w200" type="text" name="adminname" id="adminname" value="'.$adminname.'" /></p>';
    echo '<p><label>'.t('admins/password').':</label><input class="in w200" type="password" name="adminpass" id="adminpass" /></p>';
    echo '<p><label>'.t('admins/repassword').':</label><input class="in w200" type="password" name="adminpass1" id="adminpass1" /></p>';
    echo '<p><label>'.t('admins/email').':</label><input class="in w300" type="text" name="adminmail" id="adminmail" value="'.$adminmail.'" /></p>';
    echo '<p><label>'.t('admins/purview').':</label><div class="box purview">';
    // 导入权限列表
    $config = array();
    $modules= glob(COM_PATH.'/modules/*',GLOB_ONLYDIR);
    foreach ($modules as $v) {
        $k = substr($v,strrpos($v,'/')+1);
        $v = include_file($v.'/config.php');
        if (isset($v['purview'])) {
            echo '<input type="checkbox" name="'.$k.'" id="'.$k.'" class="__bigP" onclick="var checked = this.checked;$.each($(\'input.__'.$k.'\'),function(){ this.checked = checked; });" /><label for="'.$k.'"><strong>'.t("{$k}::name").'</strong></label><br/>';
            foreach ($v['purview'] as $i=>$p) {
                $checked = instr($purview,"{$k}::{$p}") ? ' checked="checked"' : null;
                echo '<input type="checkbox" name="purview[]" id="'.$k.'['.$i.']" class="__'.$k.'" onclick="$.Purview();" value="'.$k.'::'.$p.'"'.$checked.' /><label for="'.$k.'['.$i.']">'.t("{$k}::{$p}").'</label>';    
            }
            echo '<br/>';
        }
    }
    echo '</div></p>';
    echo '<p><label>'.t('admins/language').':</label>';
    echo '<select name="language" id="language">';
    echo form_opts('@.language','lang','<option value="#value#"#selected#>#name#</option>',$language);
    echo '</select></p>';

    echo '</fieldset>';
    
    echo but('system::save').'<input name="adminid" type="hidden" value="'.$adminid.'" /></form>';
    echo '<script type="text/javascript">$.Purview();</script>';
}
// *** *** www.LazyCMS.net *** *** //
function lazy_after(){
    System::footer();
}