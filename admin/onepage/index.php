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
    check_login('onepage');
    // 设置公共菜单
    G('TABS',
        L('title').':index.php;'.
        L('add/@title').':index.php?action=edit'
    );
}
// lazy_default *** *** www.LazyCMS.net *** ***
function lazy_default(){ 
    $db = get_conn();
    $ds = new Recordset();
    $ds->create("SELECT * FROM `#@_onepage` WHERE `oneid1`=0 ORDER BY `oneorder` DESC");
    $ds->action = PHP_FILE."?action=set";
    $ds->but = $ds->button();
    $ds->td  = "cklist(K[0]) + K[0] + ') <a href=\"users.php?action=user_list&groupid=' + K[0] + '\">' + K[1] + '</a>'";
    $ds->td  = "K[2]";
    $ds->td  = "icon('add','users.php?action=user_edit&groupid=' + K[0]) + icon('edit','users.php?action=group_edit&groupid=' + K[0])";
    $ds->open();
    $ds->thead = '<tr><th>ID) '.L('list/name').'</th><th>'.L('list/path').'</th><th>'.L('common/action','system').'</th></tr>';
    while ($rs = $ds->result()) {
        $ds->tbody = "ll(".$rs['oneid'].",'".t2js(h2encode($rs['onename']))."','".t2js(h2encode($rs['onepath']))."',".$rs['oneid1'].",".$rs['oneiopen'].");";
    }
    $ds->close();

    print_x(L('title'),$ds->fetch());
}
// lazy_edit *** *** www.LazyCMS.net *** ***
function lazy_edit(){
    $db = get_conn();
    $oneid  = isset($_REQUEST['oneid']) ? $_REQUEST['oneid'] : 0;
    $title  = empty($oneid) ? L('add/@title') : L('edit/@title');
    $hl = '<form id="form1" name="form1" method="post" action="">';
    $hl.= '<fieldset><legend rel="tab">'.$title.'</legend>';
    $hl.= '<p><label>'.L('add/name').'：</label><input tip="'.L('add/name').'::'.L('add/name/@tip').'" class="in3" type="text" name="onename" id="onename" value="" /></p>';
    $hl.= '<p><label>'.L('add/title').'：</label><input tip="'.L('add/title').'::'.h2encode(L('add/title/@tip')).'" class="in4" type="text" name="onetitle" id="onetitle" value="" /></p>';
    $hl.= '<p><label>'.L('add/path').'：</label><input tip="'.L('add/path').'::300::'.h2encode(L('add/path/@tip')).'" class="in5" type="text" name="onepath" id="onepath" value="" /></p>';
    $hl.= '<p><label>'.L('add/content').'：</label><div class="box">'.editor('onecontent',array('editor'=>'fckeditor')).'</div></p>';
    $hl.= '</fieldset>';
    $hl.= '<fieldset><legend>更多属性</legend>';
    $hl.= '<p><label>'.L('add/keywords').'：</label><input tip="'.L('add/keywords').'::'.L('add/keywords/@tip').'" class="in4" type="text" name="keywords" id="keywords" value="" />&nbsp;<button type="button" tip="点击获取推荐的关键词">获取</button></p>';
    $hl.= '<p><label>'.L('add/description').'：</label><textarea name="description" id="description" rows="5" class="in4"></textarea></p>';
    $hl.= '<p><label>'.L('add/template').'：</label><input class="in4" type="text" name="onetemplate" id="onetemplate" value="" />&nbsp;<button type="button">浏览...</button></p>';
    $hl.= '</fieldset>';
    $hl.= but('save').'<input name="oneid" type="hidden" value="'.$oneid.'" /></form>';
    print_x($title,$hl);
}