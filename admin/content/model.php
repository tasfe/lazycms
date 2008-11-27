<?php
/**
 * +---------------------------------------------------------------------------+
 * | LL                             LLLL   LL     L   LLLL                 LL  |
 * | LL                            LL   L  LLL   LL  LL   L               LLL  |
 * | LL      LLLL   LLLLL  LL  LL  LL      LLLL LLL  LL          LL  LL    LL  |
 * | LL         LL     LL  LL  LL  LL      L LLL LL  LLLLL       LL  LL    LL  |
 * | LL      LLLLL    LL    LLLL   LL      L  L  LL   LLLLL      LL  LL    LL  |
 * | LL     LL  LL   LL     LLLL   LL      L     LL      LL       LLLL     LL  |
 * | LL     LL  LL  LL       LL    LL   L  L     LL  L   LL       LLLL     LL  |
 * | LLLLLL  LLLLL  LLLLL    LL     LLLL   L     LL   LLLL         LL     LLLL |
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
 * 模型管理
 * 
 */
function lazy_before(){
    System::tabs(
        t('Model').':model.php;'.
        t('Model import').':model.php?action=import;'.
        t('Model add list').':model.php?action=edit&type=list;'.
        t('Model add page').':model.php?action=edit&type=page;'
    );
    System::script('LoadScript("content.model");');
}
function lazy_main(){
    System::header(t('Model'));
    $db = get_conn();
    $ds = new Recordset();
    $ds->create("SELECT * FROM `#@_content_model` ORDER BY `modelid` ASC");
    $ds->action = PHP_FILE."?action=set";
    $ds->but = $ds->button('unlock:'.l('common/unlock').'|lock:'.l('common/lock').'');
    $ds->td  = "cklist(K[0]) + K[0] + ') <a href=\"".PHP_FILE."?action=edit&modelid=' + K[0] + '\">' + K[1] + '</a>'";
    $ds->td  = "K[2]";
    $ds->td  = "K[3]";
    $ds->td  = "(K[4]?icon('tick'):icon('stop'))";
    $ds->td  = "icon('edit','".PHP_FILE."?action=edit&modelid=' + K[0]) + icon('export','".PHP_FILE."?action=export&model=' + K[2])";
    $ds->open();
    $ds->thead = '<tr><th>ID) '.l('model/list/name').'</th><th>'.l('model/list/ename').'</th><th>'.l('model/list/table').'</th><th>'.l('model/list/state').'</th><th>'.l('common/action','system').'</th></tr>';
    while ($rs = $ds->result()) {
        $ds->tbody = "E(".$rs['modelid'].",'".t2js(h2encode($rs['modelname']))."','".t2js(h2encode($rs['modelename']))."','".t2js(h2encode(Content_Model::getDataTableName($rs['modelename'])))."',".$rs['modelstate'].");";
    }
    $ds->close();
}
function lazy_after(){
    System::footer();
}