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
    check_login('article'); $menu = null;
    // 设置公共菜单
    foreach (Model::getModel() as $k=>$v) { $menu.= L('common/add').$v['modelname'].':article.php?action=edit&model='.$v['modelename'].';'; }
    G('TABS',L('article/@title').':article.php;'.$menu);
}
// lazy_default *** *** www.LazyCMS.net *** ***
function lazy_default(){ 
    print_x(L('article/@title'),'开发中...');
}
// lazy_edit *** *** www.LazyCMS.net *** ***
function lazy_edit(){
    $model = isset($_REQUEST['model'])?strtolower($_REQUEST['model']):0;
    $db = get_conn(); $M = Model::getModel($model);
    print_x(L('common/add').$M['modelname'],L('common/add').$M['modelname'].'开发中...',$M['i']+2);
}