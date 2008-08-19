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
 * | Copyright (c) 2007-2008 LazyCMS.net All rights reserved.                  |
 * +---------------------------------------------------------------------------+
 * | 许可协议，请查看源代码中附带的 LICENSE.txt 文件，                         |
 * | 或者访问 http://www.lazycms.net/ 获得详细信息。                           |
 * +---------------------------------------------------------------------------+
 */
require '../../global.php';
/**
 * 系统设置
 *
 * @copyright   Copyright (c) 2007-2008 lazycms.net All rights reserved.
 * @author      Lukin <mylukin@gmail.com>
 * @date        2008-6-26
 */

// lazy_before *** *** www.LazyCMS.net *** ***
function lazy_before(){
    check_login('model');
    // 设置公共菜单
    G('TABS',
        L('model/@title').':model.php;'.
        L('model/add/@title').':model.php?action=edit'
    );
    G('SCRIPT','LoadScript("content.model");');
}
// lazy_default *** *** www.LazyCMS.net *** ***
function lazy_default(){ 
    $db = get_conn();
    $ds = new Recordset();
    $ds->create("SELECT * FROM `#@_content_model` WHERE 1=1 ORDER BY `modelid` DESC");
    $ds->action = PHP_FILE."?action=set";
    $ds->but = $ds->button('unlock:启用|lock:禁用');
    $ds->td  = "cklist(K[0]) + K[0] + ') <a href=\"".PHP_FILE."?action=edit&modelid=' + K[0] + '\">' + K[1] + '</a>'";
    $ds->td  = "K[2]";
    $ds->td  = "K[3]";
    $ds->td  = "(K[4]?icon('tick'):icon('stop'))";
    $ds->td  = "icon('edit','".PHP_FILE."?action=edit&modelid=' + K[0])";
    $ds->open();
    $ds->thead = '<tr><th>ID) '.L('model/list/name').'</th><th>'.L('model/list/ename').'</th><th>'.L('model/list/table').'</th><th>'.L('model/list/state').'</th><th>'.L('common/action','system').'</th></tr>';
    while ($rs = $ds->result()) {
        $ds->tbody = "E(".$rs['modelid'].",'".t2js(h2encode($rs['modelname']))."','".t2js(h2encode($rs['modelename']))."','".t2js(h2encode(Model::getDBName($rs['modelename'])))."',".$rs['modelstate'].");";
    }
    $ds->close();

    print_x(L('model/@title'),$ds->fetch());
}