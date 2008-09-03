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
 * 分类管理
 *
 * @copyright   Copyright (c) 2007-2008 lazycms.net All rights reserved.
 * @author      Lukin <mylukin@gmail.com>
 * @date        2008-8-24
 */

// lazy_before *** *** www.LazyCMS.net *** ***
function lazy_before(){
    check_login('category');
    // 设置公共菜单
    G('TABS',
        L('sort/@title').':sort.php;'.
        L('sort/add/@title').':sort.php?action=edit'
    );
}
// lazy_default *** *** www.LazyCMS.net *** ***
function lazy_default(){ 
    print_x(L('sort/@title'),'开发中...');
}
// lazy_edit *** *** www.LazyCMS.net *** ***
function lazy_edit(){
    $hl = '<form id="form1" name="form1" method="post" action="">';
    $hl.= '<fieldset><legend><a class="collapsed" rel=".show" cookie="false">'.L('sort/add/@title').'</a></legend>';
    $hl.= '<div class="show">';
    $hl.= '<p><label>'.L('sort/add/sort').'：</label>';
    $hl.= '<select name="sortid" id="sortid">';
    $hl.= '<option value="0">--- '.L('sort/add/topsort').' ---</option>';
    $hl.= Article::__sort(0,0,$sortid);
    $hl.= '</select></p>';
    $hl.= '<p><label>'.L('sort/add/name').'：</label><input class="in2" type="text" name="sortname" id="sortname" value="'.$sortname.'" /></p>';
    $hl.= '<p><label>'.L('sort/add/path').'：</label><input tip="'.L('sort/add/path').'::300::'.h2encode(L('sort/add/path/@tip')).'" class="in4" type="text" name="sortpath" id="sortpath" value="'.$sortpath.'" /></p>';
    $hl.= '<p><label>'.L('sort/add/model').'：</label><span tip="'.L('sort/add/model').'::'.L('sort/add/model/@tip').'">';
    foreach (Model::getModel() as $model) {
        $hl.= '<input type="checkbox" name="model['.$model['modelename'].']" id="model['.$model['modelename'].']" value="" /><label for="model['.$model['modelename'].']">'.$model['modelname'].'</label> ';
    }
    $hl.= '</span></p>';
    $hl.= '<p><label>'.L('sort/add/sortemplate').'：</label>';
    $hl.= '<select name="sortemplate" id="sortemplate" tip="'.L('sort/add/sortemplate').'::'.L('sort/add/sortemplate/@tip').'">';
    $hl.= form_opts('themes/'.C('TEMPLATE'),'*','<option value="#value#"#selected#>#name#</option>',$sortemplate);
    $hl.= '</select></p>';
    $hl.= '<p><label>'.L('sort/add/pagetemplate').'：</label>';
    $hl.= '<select name="pagetemplate" id="pagetemplate" tip="'.L('sort/add/pagetemplate').'::'.L('sort/add/pagetemplate/@tip').'">';
    $hl.= form_opts('themes/'.C('TEMPLATE'),'*','<option value="#value#"#selected#>#name#</option>',$pagetemplate);
    $hl.= '</select></p>';
    $hl.= '</div></fieldset>';
    $hl.= but('save').'<input name="sortid" type="hidden" value="'.$sortid.'" /></form>';
    print_x(L('sort/add/@title'),$hl);
}