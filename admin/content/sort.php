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
    check_login('sort');
    // 设置公共菜单
    $menus = array();
    foreach (Model::getModels('list') as $v) {
        $menus[] = L('common/add').$v['modelname'].':article.php?action=edit&model='.$v['modelename'];
    }
    G('TABS',
        L('sort/@title').':sort.php;'.
        L('article/@title').':article.php;'.
        L('sort/add/@title').':sort.php?action=edit;'.implode(';',$menus)
    );
    G('SCRIPT','LoadScript("content.sort");');
}
// lazy_default *** *** www.LazyCMS.net *** ***
function lazy_default(){ 
    $db = get_conn();
    $ds = new Recordset();
    $ds->create("SELECT * FROM `#@_content_sort` WHERE `parentid`=0 ORDER BY `sortid` ASC");
    $ds->action = PHP_FILE."?action=set";
    $ds->but = $ds->button();
    $ds->td  = "'<div class=\"fl\">' + cklist(K[0]) + '</div><div class=\"dir\">' + icon('dir'+K[4]) + K[0] + ') <a href=\"".PHP_FILE."?action=edit&sortid=' + K[0] + '\">' + K[1] + '</a></div>'";
    $ds->td  = "K[5]";
    $ds->td  = "K[6]";
    $ds->td  = "(K[3]?icon('link',K[2]):icon('link-error','javascript:alert(\'create\');')) + K[2]";
    $ds->td  = "icon('edit','".PHP_FILE."?action=edit&sortid=' + K[0])";
    $ds->open();
    $ds->thead = '<tr><th>ID) '.L('sort/list/name').'</th><th>'.L('sort/list/model').'</th><th>'.L('sort/list/count').'</th><th>'.L('sort/list/path').'</th><th>'.L('common/action','system').'</th></tr>';
    while ($rs = $ds->result()) {
        $isSub = Article::__sub($rs['sortid']);
        $model = implode(',',Article::getModels($rs['sortid'],'modelname'));
        $count = Article::count($rs['sortid'],implode(',',Article::getModels($rs['sortid'],'modelename')));
        $ds->tbody = "E(".$rs['sortid'].",'".t2js(h2encode($rs['sortname']))."','".t2js(h2encode(SITE_BASE.$rs['sortpath']))."',".(is_file(LAZY_PATH.$rs['sortpath'])?1:0).",{$isSub},'".(empty($model)?'&nbsp;':$model)."',{$count});$('#list_".$rs['sortid']."').addSub(".$rs['sortid'].",1,{$isSub});";
    }
    $ds->close();

    print_x(L('sort/@title'),$ds->fetch());
}
// lazy_set *** *** www.LazyCMS.net *** ***
function lazy_set(){
    $db = get_conn();
    $submit = isset($_POST['submit']) ? strtolower($_POST['submit']) : null;
    $lists  = isset($_POST['lists']) ? $_POST['lists'] : null;
    switch($submit){
        case 'delete':
            empty($lists) ? echo_json(L('sort/pop/select'),0) : null ;
            // 取得要删除分类的所有子类，进行删除
            $db->update('#@_content_sort',array('parentid'=>0),"`parentid` IN({$lists})");
            $db->delete('#@_content_sort',"`sortid` IN({$lists})");
            $db->delete('#@_content_sort_model',"`sortid` IN({$lists})");
            echo_json(array(
                'text' => L('sort/pop/deleteok'),
                'url'  => $_SERVER["HTTP_REFERER"],
            ),1);
            break;
        case 'getsub':
            $space  = isset($_POST['space']) ? $_POST['space'] : 1;
            $result = $db->query("SELECT * FROM `#@_content_sort` WHERE `parentid`=? ORDER BY `sortid` ASC",$lists);
            $array  = array();
            while ($rs = $db->fetch($result)) {
                $isSub = Article::__sub($rs['sortid']);
                $model = implode(',',Article::getModels($rs['sortid'],'modelname'));
                $count = Article::count($rs['sortid'],implode(',',Article::getModels($rs['sortid'],'modelename')));
                $array[] = array(
                    'id'    => $rs['sortid'],
                    'sub'   => $isSub,
                    'code'  => "R(".$rs['sortid'].",'".t2js(h2encode($rs['sortname']))."','".t2js(h2encode(SITE_BASE.$rs['sortpath']))."',".(is_file(LAZY_PATH.$rs['sortpath'])?1:0).",{$isSub},'".(empty($model)?'&nbsp;':$model)."',{$count});",
                );
            }
            echo(json_encode($array));
            break;
        default :
            echo_json(L('error/invalid','system'));
            break;
    }
}
// lazy_edit *** *** www.LazyCMS.net *** ***
function lazy_edit(){
    $db = get_conn();
    $models   = Model::getModels('list');
    $sortid   = isset($_REQUEST['sortid']) ? $_REQUEST['sortid'] : 0;
    $title    = empty($sortid) ? L('sort/add/@title') : L('sort/edit/@title');
    $parentid = isset($_POST['parentid']) ? $_POST['parentid'] : 0;
    $sortname = isset($_POST['sortname']) ? $_POST['sortname'] : null;
    $sortpath = isset($_POST['sortpath']) ? $_POST['sortpath'] : null;
    $model    = isset($_POST['model']) ? $_POST['model'] : array();
    $sortemplate  = isset($_POST['sortemplate']) ? $_POST['sortemplate'] : null;
    $pagetemplate = isset($_POST['pagetemplate']) ? $_POST['pagetemplate'] : null;
    $val = new Validate();
    if ($val->method()) {
        $val->check('sortname|1|'.L('sort/check/name').'|1-50')
            ->check('sortpath|0|'.L('sort/check/path').';sortpath|5|'.L('sort/check/path1').';sortpath|4|'.L('sort/check/path2')."|SELECT COUNT(`sortid`) FROM `#@_content_sort` WHERE `sortpath`='#pro#'".(empty($sortid)?null:" AND `sortid` <> {$sortid}"));
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
                $text = L('sort/pop/addok');
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
                        $db->delete('#@_content_sort_model',array('`sortid`='.DB::quote($sortid),'`modelid`='.DB::quote($modelid)));
                    }
                }
                $text = L('sort/pop/editok');
            }
            // 录入相关记录
            if (is_array($model)) {
                foreach ($model as $modelid) {
                    if ($db->count("SELECT * FROM `#@_content_sort_model` WHERE `sortid`=".DB::quote($sortid)." AND `modelid`=".DB::quote($modelid).";")==0) {
                        $db->insert('#@_content_sort_model',array(
                            'sortid' => $sortid,
                            'modelid' => $modelid,
                        ));
                    }
                }
            }
            // 输出执行结果
            echo_json(array(
                'text' => $text,
                'url'  => PHP_FILE,
            ),1);
        }
    } else {
        if (!empty($sortid)) {
            $res = $db->query("SELECT * FROM `#@_content_sort` WHERE `sortid`=?",$sortid);
            if ($rs = $db->fetch($res)) {
                $parentid = $rs['parentid'];
                $sortname = h2encode($rs['sortname']);
                $sortpath = h2encode($rs['sortpath']);
                $sortemplate  = h2encode($rs['sortemplate']);
                $pagetemplate = h2encode($rs['pagetemplate']);
                $getModels    = Article::getModels($sortid);
            }
        }
    }

    $hl = '<form id="form1" name="form1" method="post" action="">';
    $hl.= '<fieldset><legend rel="tab"><a class="collapsed" rel=".show" cookie="false">'.$title.'</a></legend>';
    $hl.= '<div class="show">';
    $hl.= '<p><label>'.L('sort/add/sort').':</label>';
    $hl.= '<select name="parentid" id="parentid" onchange="$(this).selectModels();">';
    $hl.= '<option models="" value="0">--- '.L('sort/add/topsort').' ---</option>';
    $hl.= Article::__sort(0,0,$sortid,$parentid);
    $hl.= '</select></p>';
    $hl.= '<p><label>'.L('sort/add/name').':</label><input class="in2" type="text" name="sortname" id="sortname" value="'.$sortname.'" /></p>';
    $hl.= '<p><label>'.L('sort/add/path').':</label><input tip="'.L('sort/add/path').'::300::'.h2encode(L('sort/add/path/@tip')).'" class="in4" type="text" name="sortpath" id="sortpath" value="'.$sortpath.'" /></p>';
    $hl.= '<p><label>'.L('sort/add/model').':</label><span id="models" tip="'.L('sort/add/model').'::'.L('sort/add/model/@tip').'">';
    foreach ($models as $model) {
        $checked = instr($getModels,$model['modelid'])?' checked="checked"':null;
        $hl.= '<input type="checkbox" name="model['.$model['modelename'].']" id="model['.$model['modelename'].']" value="'.$model['modelid'].'"'.$checked.' /><label for="model['.$model['modelename'].']">'.$model['modelname'].'</label> ';
    }
    $hl.= '</span></p>';
    $hl.= '</div></fieldset>';

    $hl.= '<fieldset><legend><a class="collapse" rel=".more-attr">'.L('common/attr').'</a></legend>';
    $hl.= '<div class="more-attr">';
    $hl.= '<p><label>'.L('sort/add/sortemplate').':</label>';
    $hl.= '<select name="sortemplate" id="sortemplate" tip="'.L('sort/add/sortemplate').'::'.L('sort/add/sortemplate/@tip').'">';
    $hl.= '<option value="">'.L('sort/add/defaultemplate').'</option>';
    $hl.= form_opts(C('TEMPLATE'),'*','<option value="#value#"#selected#>#name#</option>',$sortemplate);
    $hl.= '</select></p>';
    $hl.= '<p><label>'.L('sort/add/pagetemplate').':</label>';
    $hl.= '<select name="pagetemplate" id="pagetemplate" tip="'.L('sort/add/pagetemplate').'::'.L('sort/add/pagetemplate/@tip').'">';
    $hl.= '<option value="">'.L('sort/add/defaultemplate').'</option>';
    $hl.= form_opts(C('TEMPLATE'),'*','<option value="#value#"#selected#>#name#</option>',$pagetemplate);
    $hl.= '</select></p>';
    $hl.= '</div></fieldset>';
    $hl.= but('save').'<input name="sortid" type="hidden" value="'.$sortid.'" /></form>';
    print_x($title,$hl);
}
