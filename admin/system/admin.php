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
    System::header(l('Admins add'));
}
// *** *** www.LazyCMS.net *** *** //
function lazy_after(){
    System::footer();
}