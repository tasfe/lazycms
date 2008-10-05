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
 * 文档管理
 *
 * @copyright   Copyright (c) 2007-2008 lazycms.net All rights reserved.
 * @author      Lukin <mylukin@gmail.com>
 * @date        2008-8-26
 */
// lazy_before *** *** www.LazyCMS.net *** ***
function lazy_before(){
    check_login('onepage');
    // 设置公共菜单
    $menus = array(); $model = array();
    foreach (Content_Model::getModels('page') as $v) {
        $model[] = $v['modelename'];
        $menus[] = L('common/add').$v['modelname'].':onepage.php?action=edit&model='.$v['modelename'];
    }
    G('MODEL',$model);
    G('TABS',L('onepage/@title').':onepage.php;'.implode(';',$menus));
    G('SCRIPT','LoadScript("content.onepage");');
}
// lazy_default *** *** www.LazyCMS.net *** ***
function lazy_default(){
    $model   = isset($_GET['model'])?$_GET['model']:null;
    $fields  = isset($_GET['fields'])?$_GET['fields']:null;
    if (empty($model)) {
        $textarea = null;
        $models   = Content_Model::getModels('page');
        $hl = '<form id="form1" name="form1" method="get" action="'.PHP_FILE.'">';
        $hl.= '<fieldset><legend><a class="collapsed" rel=".show" cookie="false">'.L('onepage/@title').'</a></legend>';
        $hl.= '<div class="show">';
        if ($models) {
            $hl.= '<p><label>'.L('onepage/search/model').':</label><select name="model" id="model" onchange="$(this).viewFields();">';
            foreach ($models as $v) {
                $textarea.= '<textarea class="hide" checked="'.$v['setkeyword'].'" id="fields_'.$v['modelename'].'">'.$v['modelfields'].'</textarea>';
                $hl.= '<option value="'.$v['modelename'].'">'.$v['modelname'].'</option>';
            }
            $hl.= '</select></p>';
            $hl.= '<p><label>'.L('onepage/search/fields').':</label><span id="fields" tip="'.L('onepage/search/fields').'::'.L('onepage/search/fields/@tip').'"></span></p>';
            $hl.= '<p><label>&nbsp;</label><button type="submit">'.L('onepage/search/submit').'</button></p>';
        } else {
            $hl.= '<p class="empty"><strong>'.L('common/model').'</strong> <a href="model.php">&gt;&gt;&gt;</a></p>';
        }
        $hl.= '</div></fieldset>';
        $hl.= '</form>'.$textarea;
        $hl.= '<script type="text/javascript">$(\'#model\').viewFields();</script>';
        print_x(L('onepage/@title'),$hl);
    } else {
        $model  = Content_Model::getModel($model);
        $table  = Content_Model::getDataTableName($model['modelename']);
        $length = count($fields);
        $query  = null;
        foreach ($fields as $k=>$v) {
            $query .= '&fields'.rawurlencode("[{$k}]").'='.rawurlencode($v);
        }
        $db = get_conn();
        $ds = new Recordset();
        $ds->create("SELECT * FROM `{$table}` ORDER BY `order` DESC,`id` DESC");
        $ds->action = PHP_FILE.'?action=set&model='.$model['modelename'];
        $ds->url = PHP_FILE.'?model='.$model['modelename'].$query.'&page=$';
        $ds->but = $ds->button('create:生成').$ds->plist();
        // 循环自定义显示字段
        for ($i=0; $i<$length; $i++) {
            if ($i==0) {
                $ds->td  = "cklist(K[0]) + K[0] + ') <a href=\"".PHP_FILE."?action=edit&model=".$model['modelename']."&id=' + K[0] + '\">' + K[".($i+4)."] + '</a>'";
            } else {
                $ds->td  = "K[".($i+4)."]";
            }
        }
        if ($length==0) {
            $ds->td  = "'<div class=\"fl\">' + cklist(K[0]) + K[0] + ') </div><div class=\"dir\">' + (K[2]?icon('link',K[1]):icon('link-error','javascript:alert(\'create\');')) + '<a href=\"".PHP_FILE."?action=edit&model=".$model['modelename']."&id=' + K[0] + '\">' + K[1] + '</a></div>'";
        } else {
            $ds->td  = "(K[2]?icon('link',K[1]):icon('link-error','javascript:alert(\'create\');')) + K[1]";
        }
        $ds->td  = "K[3]";
        $ds->td  = "icon('edit','".PHP_FILE."?action=edit&model=".$model['modelename']."&id=' + K[0])";
        $ds->open();
        $ds->thead = '<tr>'; $i=0;
        foreach ($fields as $field=>$label) {
            $ds->thead.= '<th>'.($i==0?'ID) ':null).$label.'</th>'; $i++;
        }
        $ds->thead.= '<th>'.($length==0?'ID) ':null).L('article/list/path').'</th><th>'.L('article/list/date').'</th><th>'.L('common/action','system').'</th></tr>';
        while ($rs = $ds->result()) {
            $K = null;
            foreach ($fields as $field=>$label) {
                $K.= ",'".t2js(h2encode($rs[$field]))."'";
            }
            $ds->tbody = "E(".$rs['id'].",'".SITE_BASE.$rs['path']."',".(is_file(LAZY_PATH.$rs['path'])?1:0).",'".date('Y-m-d H:i:s',$rs['date'])."'{$K});";
        }
        $ds->close();
        print_x(L('onepage/@title'),$ds->fetch());
    }
}
// lazy_set *** *** www.LazyCMS.net *** ***
function lazy_set(){
    $db = get_conn();
    $submit = isset($_POST['submit']) ? strtolower($_POST['submit']) : null;
    $lists  = isset($_POST['lists']) ? $_POST['lists'] : null;
    $model  = isset($_GET['model'])?$_GET['model']:null;
    switch($submit){
        case 'delete':
            empty($lists) ? echo_json(L('onepage/pop/select'),0) : null ;
            $table  = Content_Model::getDataTableName($model);
            $jtable = Content_Model::getJoinTableName($model);
            $db->delete($table,"`id` IN({$lists})");
            $db->delete($jtable,array("`tid` IN({$lists})"));
            echo_json(array(
                'text' => L('onepage/pop/deleteok'),
                'url'  => $_SERVER["HTTP_REFERER"],
            ),1);
            break;
        default :
            echo_json(L('error/invalid','system'));
            break;
    }
}
// lazy_edit *** *** www.LazyCMS.net *** ***
function lazy_edit(){
    $db = get_conn(); $data = array();
    $m  = isset($_REQUEST['model']) ? strtolower($_REQUEST['model']) : null;
    $id = isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : null;
    $n  = array_search($m,G('MODEL'))+2;
    $model = Content_Model::getModel($m); if (!$model) { trigger_error(L('error/invalid','system')); }
    $title = (empty($id) ? L('common/add') : L('common/edit')).$model['modelname'];
    $path  = isset($_POST['path']) ? $_POST['path'] : null;
    $table = Content_Model::getDataTableName($model['modelename']);
    $jtable  = Content_Model::getJoinTableName($model['modelename']);
    $sortids = isset($_POST['sortids']) ? $_POST['sortids'] : null;
    $description = isset($_POST['description']) ? $_POST['description'] : null;
    // 加载字段解析类
    import('system.field2tag');
    import('system.keywords');
    $key = new Keywords($model['modelename']);
    $tag = new Field2Tag($model);
    $val = $tag->getVal();
    if ($val->method()) {
        // 获取自定义字段的数据
        $data  = $tag->_POST();
        // 解析后的字段数组
        $fields= $tag->_Fields();
        // 路径转换
        $maxid = $db->max('id',$table);
        $path  = Content_Article::formatPath($maxid,$path,$data[$model['setkeyword']]);
        // 验证路径不能重复
        $val->check('path|0|'.L('onepage/check/path').';path|5|'.L('onepage/check/path1').';path|4|'.L('onepage/check/path2')."|SELECT COUNT(*) FROM `{$table}` WHERE `path`=".DB::quote($path).(empty($id)?null:" AND `id` <> {$id}"))
            ->check('description|1|'.L('onepage/check/description').'|0-250');
        if ($val->isVal()) {
            $val->out();
        } else {
            $editor = $tag->getEditors();
            foreach ($editor as $k=>$e) {
                // 下载远程图片
                if ($e->snapimg) {
                    $snapimg = isset($_POST[$k.'_attr']['snapimg']) ? $_POST[$k.'_attr']['snapimg'] : false;
                    if ($snapimg) {
                        $data[$k] = snapImg($data[$k]);
                    }
                }
                // 删除站外连接
                if ($e->dellink) {
                    $data[$k] = preg_replace('/<a([^>]*)href=["\']*(http|https)\:\/\/(?!'.preg_quote($_SERVER['HTTP_HOST'],'/').')([^>]*)>(.*)<\/a>/isU','$4',$data[$k]);
                }
                // 自动截取简述
                if (!empty($model['description']) && $model['description']==$k) {
                    $description = (strlen($description)==0) ? left(cls(preg_replace('/<[^>]*>/iU','',$data[$k])),180) : $description;
                }
            }
            // 将数据写入数据库
            if (empty($id)) {
                $row = array(
                    'order'=> $maxid,
                    'date' => now(),
                    'path' => $path,
                    'description' => $description,
                );
                if (!empty($data)) {
                    $row = array_merge($row,$data);
                }
                $db->insert($table,$row);
                $id = $db->lastId();
                $text = L('onepage/pop/addok');
            } else {
                $row = array(
                    'path' => $path,
                    'description' => $description,
                );
                if (!empty($data)) {
                    $row = array_merge($row,$data);
                }
                $db->update($table,$row,DB::quoteInto('`id` = ?',$id));
                $text = L('onepage/pop/editok');
            }
            // 自动获取关键词
            if (!empty($model['setkeyword'])) {
                $keywords = isset($_POST['keywords']) ? $_POST['keywords'] : null;
                $autokeys = isset($_POST['autokeywords']) ? $_POST['autokeywords'] : null;
                if ($autokeys && empty($keywords)) {
                    $keywords = System::getKeywords($data[$model['setkeyword']]);
                    $keywords = implode(',',$keywords);
                }
                $key->save($id,$keywords,C('GET_RELATED_KEY'));
            }
            $query = empty($model['setkeyword'])?null:'&'.rawurlencode('fields['.$model['setkeyword'].']').'='.rawurlencode($fields[$model['setkeyword']]->label);
            // 输出执行结果
            echo_json(array(
                'text' => $text,
                'url'  => PHP_FILE."?model={$m}{$query}",
            ),1);
        }
    } else {
        if (!empty($id)) {
            $res = $db->query("SELECT * FROM `{$table}` WHERE `id`=?",$id);
            if ($data = $db->fetch($res)) {
                $path   = h2encode($data['path']);
                if (!empty($model['setkeyword'])) {
                    $keywords = $key->get($id);
                }
                $description = $data['description'];
            }
        }
    }
    
    $hl = '<form id="form1" name="form1" method="post" action="">';
    $hl.= '<fieldset><legend rel="tab"><a class="collapsed" rel=".more-attr" cookie="false">'.$title.'</a></legend>';
    $hl.= '<div class="more-attr">';
    $hl.= $tag->fetch('<p><label>{label}:</label>{object}</p>',$data);
    $hl.= '<p><label>'.L('onepage/add/path').':</label><input tip="::300::'.ubbencode(L('model/add/path/@tip')).'<br/>'.h2encode(L('onepage/add/path/@tip')).'" class="in4" type="text" name="path" id="path" value="'.(empty($path)?$model['modelpath']:$path).'" /></p>';
    $hl.= '</div></fieldset>';

    $hl.= '<fieldset><legend><a class="collapse" rel=".more-attr">'.L('common/attr').'</a></legend>';
    $hl.= '<div class="more-attr">';
    if (!empty($model['setkeyword'])) {
        $hl.= '<p><label>'.L('onepage/add/keywords').':</label><input tip="'.L('onepage/add/keywords').'::250::'.L('onepage/add/keywords/@tip').'" class="in4" type="text" name="keywords" id="keywords" value="'.$keywords.'" />&nbsp;<button type="button" onclick="$(\'#keywords\').getKeywords(\'#'.$model['setkeyword'].'\')" tip="'.L('common/get/@tip','system').'">'.L('common/get','system').'</button></p>';
    }
    $hl.= '<p><label>'.L('onepage/add/description').':</label><textarea tip="'.L('onepage/add/description').'::'.L('onepage/add/description/@tip').'" name="description" id="description" rows="5" class="in4">'.$description.'</textarea></p>';
    $hl.= '</div></fieldset>';
    $hl.= but('save').'<input name="id" type="hidden" value="'.$id.'" /></form>';
    
    print_x($title,$hl,$n);
}
