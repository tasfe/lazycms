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
 * 分类管理
 * 
 */
// *** *** www.LazyCMS.net *** *** //
function lazy_before(){
    no_cache();// 禁止缓存
    System::purview('content::sort');
    // 设置公共菜单
    $menus = array(); $model = array();
    foreach (Content_Model::getModelsByType('list') as $v) {
        $model[] = $v['modelename'];
        $menus[] = t('system::add').$v['modelname'].':article.php?action=edit&model='.$v['modelename'];
    }
    // 设置公共菜单
    System::tabs(
        t('sort').':sort.php;'.
        t('article').':article.php;'.
        t('sort/add').':sort.php?action=edit;'.implode(';',$menus)
    );
}
// *** *** www.LazyCMS.net *** *** //
function lazy_main(){
    System::loadScript('content.sort');
    System::header(t('sort'));
    $db = get_conn();
    $ds = new Recordset();
    $ds->create("SELECT * FROM `#@_content_sort` WHERE `parentid`=0 ORDER BY `sortid` ASC");
    $ds->action = PHP_FILE."?action=set";
    $ds->but = $ds->button();
    $ds->td("cklist(K[0]) + icon('d'+K[4]) + K[0] + ') <a href=\"".PHP_FILE."?action=edit&sortid=' + K[0] + '\">' + K[1] + '</a>'");
    $ds->td("K[5]");
    $ds->td("K[6]");
    $ds->td("(K[3]?icon('b3',K[2]):icon('b4','javascript:alert(\'create\');')) + K[2]");
    $ds->td("icon('a5','".PHP_FILE."?action=edit&sortid=' + K[0])");
    $ds->open();
    $ds->thead = '<tr><th>ID) '.t('sort/name').'</th><th>'.t('sort/model').'</th><th>'.t('sort/count').'</th><th>'.t('sort/path').'</th><th>'.t('system::Manage').'</th></tr>';
    while ($rs = $ds->result()) {
        $isSub = Content_Sort::isSubSort($rs['sortid']);
        $model = implode(',',Content_Model::getModelsBySortId($rs['sortid'],'modelname'));
        $count = Content_Article::count($rs['sortid'],implode(',',Content_Model::getModelsBySortId($rs['sortid'],'modelename')));
        $ds->tbody("E(".$rs['sortid'].",'".t2js(h2c($rs['sortname']))."','".t2js(h2c(SITE_BASE.$rs['sortpath']))."',".(is_file(LAZY_PATH.$rs['sortpath'])?1:0).",{$isSub},'".(empty($model)?'&nbsp;':$model)."',{$count});\$(function(){\$('#list_".$rs['sortid']."').addSub(".$rs['sortid'].",1,{$isSub});});");
    }
    $ds->close();
    $ds->display();
}
// *** *** www.LazyCMS.net *** *** //
function lazy_set(){
    $db = get_conn();
    $submit = isset($_POST['submit']) ? strtolower($_POST['submit']) : null;
    $lists  = isset($_POST['lists']) ? $_POST['lists'] : null;
    switch($submit){
        case 'delete':
            empty($lists) ? ajax_alert(t('sort/alert/noselect')) : null ;
            // 取得要删除分类的所有子类，进行删除
            $sortids = implode(',',Content_Sort::getSortIdsBySortIds($lists));
            // 删除文章
            $res = $db->query("SELECT `modelid`,`sortid` FROM `#@_content_sort_join` WHERE `sortid` IN({$sortids});");
            while ($rs = $db->fetch($res,0)) {
                $model  = Content_Model::getModelById($rs[0]);
                $table  = Content_Model::getDataTableName($model['modelename']);
                $jtable = Content_Model::getJoinTableName($model['modelename']);
                $res1 = $db->query("SELECT `tid`,`sid` FROM `{$jtable}` WHERE `type`=1 AND `sid`=?;",$rs[1]);
                while ($rs1 = $db->fetch($res1,0)) {
                    $db->delete($table,"`id`='".$rs1[0]."'");
                }
                $db->delete($jtable,array("`type`=1","`sid`='".$rs[1]."'"));
            }
            // 删除分类与模型之间的关联关系
            $db->delete('#@_content_sort_join',"`sortid` IN({$sortids})");
            // 删除分类
            $db->delete('#@_content_sort',"`sortid` IN({$sortids})");
            ajax_success(t('sort/alert/delete'),0);
            break;
        case 'getsub':
            $space  = isset($_POST['space']) ? $_POST['space'] : 1;
            $result = $db->query("SELECT * FROM `#@_content_sort` WHERE `parentid`=? ORDER BY `sortid` ASC",$lists);
            $array  = array();
            while ($rs = $db->fetch($result)) {
                $isSub = Content_Sort::isSubSort($rs['sortid']);
                $model = implode(',',Content_Model::getModelsBySortId($rs['sortid'],'modelname'));
                $count = Content_Article::count($rs['sortid'],implode(',',Content_Model::getModelsBySortId($rs['sortid'],'modelename')));
                $arr[] = array(
                    'id'    => $rs['sortid'],
                    'sub'   => $isSub,
                    'code'  => "R(".$rs['sortid'].",'".t2js(h2c($rs['sortname']))."','".t2js(h2c(SITE_BASE.$rs['sortpath']))."',".(is_file(LAZY_PATH.$rs['sortpath'])?1:0).",{$isSub},'".(empty($model)?'&nbsp;':$model)."',{$count});"
                );
            }
            ajax_result($arr);
            break;
        default :
            ajax_error(t('system::error/invalid'));
            break;
    }
}
// *** *** www.LazyCMS.net *** *** //
function lazy_edit(){
    $db = get_conn();
    $models   = Content_Model::getModelsByType('list');
    $sortid   = isset($_REQUEST['sortid']) ? $_REQUEST['sortid'] : 0;
    $title    = empty($sortid) ? t('sort/add') : t('sort/edit');
    $parentid = isset($_POST['parentid']) ? $_POST['parentid'] : 0;
    $sortname = isset($_POST['sortname']) ? $_POST['sortname'] : null;
    $sortpath = isset($_POST['sortpath']) ? $_POST['sortpath'] : null;
    $model    = isset($_POST['model']) ? $_POST['model'] : array();
    $sortemplate  = isset($_POST['sortemplate']) ? $_POST['sortemplate'] : null;
    $pagetemplate = isset($_POST['pagetemplate']) ? $_POST['pagetemplate'] : null;
    $val = new Validate();
    if ($val->method()) {
        $val->check('sortname|1|'.t('sort/check/name').'|1-50');
        $val->check('sortpath|0|'.t('sort/check/path').';sortpath|5|'.t('sort/check/path1').';sortpath|4|'.t('sort/check/path2')."|SELECT COUNT(`sortid`) FROM `#@_content_sort` WHERE `sortpath`='#pro#'".(empty($sortid)?null:" AND `sortid` <> {$sortid}"));
        if ($val->isVal()) {
            $val->out();
        } else {
            if (empty($sortid)) {
                $db->insert('#@_content_sort',array(
                    'parentid' => $parentid,
                    'sortname' => $sortname,
                    'sortpath' => $sortpath,
                    'sortemplate' => $sortemplate,
                    'pagetemplate'=> $pagetemplate,
                ));
                $sortid = $db->lastId();
                $text = t('sort/alert/add');
            } else {
                $db->update('#@_content_sort',array(
                    'parentid' => $parentid,
                    'sortname' => $sortname,
                    'sortpath' => $sortpath,
                    'sortemplate' => $sortemplate,
                    'pagetemplate'=> $pagetemplate,
                ),DB::quoteInto('`sortid` = ?',$sortid));
                // 删除没选中的
                $allModels = array(); foreach ($models as $v) { $allModels[$v['modelename']] = $v['modelid']; }
                $delModels = array_diff($allModels,$model);
                if (is_array($delModels) && !empty($delModels)) {
                    foreach ($delModels as $modelid) {
                        $db->delete('#@_content_sort_join',array('`sortid`='.DB::quote($sortid),'`modelid`='.DB::quote($modelid)));
                    }
                }
                $text = t('sort/alert/edit');
            }
            // 录入相关记录
            if (is_array($model)) {
                foreach ($model as $modelid) {
                    if ($db->count("SELECT * FROM `#@_content_sort_join` WHERE `sortid`=".DB::quote($sortid)." AND `modelid`=".DB::quote($modelid).";")==0) {
                        $db->insert('#@_content_sort_join',array(
                            'sortid' => $sortid,
                            'modelid' => $modelid,
                        ));
                    }
                }
            }
            // 输出执行结果
            ajax_success($text,0);
        }
    } else {
        if (!empty($sortid)) {
            $res = $db->query("SELECT * FROM `#@_content_sort` WHERE `sortid`=?",$sortid);
            if ($rs = $db->fetch($res)) {
                $parentid = $rs['parentid'];
                $sortname = h2c($rs['sortname']);
                $sortpath = h2c($rs['sortpath']);
                $sortemplate  = h2c($rs['sortemplate']);
                $pagetemplate = h2c($rs['pagetemplate']);
                $getModels    = Content_Model::getModelsBySortId($sortid);
            }
        }
    }
    System::loadScript('content.sort');
    System::header($title);
    echo '<form id="form1" name="form1" method="post" action="">';
    echo '<fieldset><legend rel="tab"><a rel=".show" cookie="false"><img class="a2 os" src="../system/images/white.gif" />'.$title.'</a></legend>';
    echo '<div class="show">';
    echo '<p><label>'.t('sort/belong').':</label>';
    echo '<select name="parentid" id="parentid" onchange="$(this).selectModels();">';
    echo '<option models="" value="0">--- '.t('sort/topsort').' ---</option>';
    echo Content_Sort::getSortOptionByParentId(0,0,$sortid,$parentid);
    echo '</select></p>';
    echo '<p><label>'.t('sort/name').':</label><input class="in w200" type="text" name="sortname" id="sortname" value="'.$sortname.'" /></p>';
    echo '<p><label>'.t('sort/path').':</label><input help="sort/path" class="in w400" type="text" name="sortpath" id="sortpath" value="'.$sortpath.'" /></p>';
    if (!empty($models)) {
        echo '<p><label>'.t('sort/model').':</label><span id="models" help="sort/model">';
        foreach ($models as $model) {
            $checked = instr($getModels,$model['modelid'])?' checked="checked"':null;
            echo '<input type="checkbox" name="model['.$model['modelename'].']" id="model['.$model['modelename'].']" value="'.$model['modelid'].'"'.$checked.' /><label for="model['.$model['modelename'].']">'.$model['modelname'].'</label> ';
        }
        echo '</span></p>';
    }
    echo '</div></fieldset>';

    echo '<fieldset><legend><a rel=".more-attr"><img class="a1 os" src="../system/images/white.gif" />'.t('system::moreattr').'</a></legend>';
    echo '<div class="more-attr">';
    echo '<p><label>'.t('sort/sortemplate').':</label>';
    echo '<select name="sortemplate" id="sortemplate">';
    echo '<option value="">'.t('sort/defaultemplate').'</option>';
    echo form_opts(c('TEMPLATE'),c('TEMPLATE_EXTS'),'<option value="#value#"#selected#>#name#</option>',$sortemplate);
    echo '</select></p>';
    echo '<p><label>'.t('sort/pagetemplate').':</label>';
    echo '<select name="pagetemplate" id="pagetemplate">';
    echo '<option value="">'.t('sort/defaultemplate').'</option>';
    echo form_opts(C('TEMPLATE'),c('TEMPLATE_EXTS'),'<option value="#value#"#selected#>#name#</option>',$pagetemplate);
    echo '</select></p>';
    echo '</div></fieldset>';
    echo but('system::save').'<input name="sortid" type="hidden" value="'.$sortid.'" /></form>';
}
// *** *** www.LazyCMS.net *** *** //
function lazy_after(){
    System::footer();
}