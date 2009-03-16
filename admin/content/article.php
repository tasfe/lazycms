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
    System::purview('content::article');
    $menus = array(); $model = array();
    foreach (Content_Model::getModelsByType('list') as $v) {
        $model[] = $v['modelename'];
        $menus[] = t('system::add').$v['modelname'].':article.php?action=edit&model='.$v['modelename'];
    }
    g('MODEL',$model);
    // 设置公共菜单
    System::tabs(
        t('sort').':sort.php;'.
        t('article').':article.php;'.
        t('sort/add').':sort.php?action=edit;'.implode(';',$menus)
    );
}
// *** *** www.LazyCMS.net *** *** //
function lazy_main(){
    System::loadScript('content.article');
    System::header(t('article'));
    $size    = isset($_GET['size'])?$_GET['size']:15;
    $model   = isset($_GET['model'])?$_GET['model']:null;
    $sortid  = isset($_GET['sortid'])?$_GET['sortid']:0;
    $keyword = isset($_GET['keyword'])?$_GET['keyword']:null;
    $fields  = isset($_GET['fields'])?$_GET['fields']:null;
    if (empty($model)) {
        $textarea = null;
        $models   = Content_Model::getModelsByType('list');
        echo '<form id="form1" name="form1" method="get" action="'.PHP_FILE.'">';
        echo '<fieldset><legend><a class="collapsed" rel=".show" cookie="false">'.t('article').'</a></legend>';
        echo '<div class="show">';
        if ($models) {
            echo '<p><label>'.t('article/model').':</label><select name="model" id="model" onchange="$(this).viewFields();">';
            foreach ($models as $v) {
                $textarea.= '<textarea class="hide" checked="'.$v['setkeyword'].'" id="fields_'.$v['modelename'].'">'.$v['modelfields'].'</textarea>';
                echo '<option value="'.$v['modelename'].'">'.$v['modelname'].'</option>';
            }
            echo '</select></p>';
            echo '<p><label>'.t('article/sort').':</label><select name="sortid"><option value="0">'.t('article/sortall').'</option>'.Content_Sort::getSortOptionByParentId(0,0,false).'</select></p>';
            echo '<p><label>'.t('article/keyword').':</label><input class="in2" type="text" name="keyword" id="keyword" value="" /></p>';
            echo '<p><label>'.t('article/size').':</label><select name="size" edit="true">';
            foreach (array(10,15,20,25,30,40,50) as $i) {
                $selected = $i==$size?' selected="selected"':null;
                echo '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
            }
            echo '</select></p>';
            echo '<p><label>'.t('article/fields').':</label><span id="fields"></span></p>';
            echo '<p><label>&nbsp;</label><button type="submit">'.t('article/view').'</button></p>';
        } else {
            echo '<p class="empty"><strong>'.t('nomodel').'</strong> <a href="model.php?action=edit&type=list">&gt;&gt;&gt;</a></p>';
        }
        echo '</div></fieldset>';
        echo '</form>'.$textarea;
        echo '<script type="text/javascript">$(\'#model\').viewFields();</script>';
    } else {
        $model  = Content_Model::getModelByEname($model);
        $table  = Content_Model::getDataTableName($model['modelename']);
        $jtable = Content_Model::getJoinTableName($model['modelename']);
        $length = count($fields);
        $query  = null; $inSQL = null;
        $inLike = empty($keyword)?null:"BINARY UCASE(`description`) LIKE UCASE('%{$keyword}%')";
        foreach ($fields as $k=>$v) {
            $query .= '&fields'.rawurlencode("[{$k}]").'='.rawurlencode($v);
            if ($keyword!='') {
                $inLike.= (empty($inLike)?null:" OR ")."BINARY UCASE(`{$k}`) LIKE UCASE('%{$keyword}%')";
            }
        }
        $inSQL.= empty($inLike)?null:' AND ('.$inLike.')';
        $inSQL.= ($sortid==0?null:" AND `sortid`=".DB::quote($sortid));

        $db = get_conn();
        $ds = new Recordset();
        $ds->create("SELECT * FROM `{$table}` WHERE `passed`=0 {$inSQL} ORDER BY `order` DESC,`id` DESC");
        $ds->action = PHP_FILE.'?action=set&model='.$model['modelename'];
        $ds->url = PHP_FILE.'?model='.$model['modelename'].'&sortid='.$sortid.'&keyword='.$keyword.'&size='.$size.$query.'&page=$';
        $ds->but = $ds->button('create:'.t('system::create').'|move:'.t('system::move')).$ds->plist();
        // 循环自定义显示字段
        for ($i=0; $i<$length; $i++) {
            if ($i==0) {
                $ds->td("cklist(K[0]) + K[0] + ') <a href=\"".PHP_FILE."?action=edit&model=".$model['modelename']."&id=' + K[0] + '\">' + K[".($i+6)."] + '</a>'");
            } else {
                $ds->td("K[".($i+6)."]");
            }
        }
        if ($length==0) {
            $ds->td("cklist(K[0]) + K[0] + ') ' + (K[2]?icon('b3',K[1]):icon('b4','javascript:;','\$(this).ajaxLink(\'create\',' + K[0] + ');')) + '<a href=\"".PHP_FILE."?action=edit&model=".$model['modelename']."&id=' + K[0] + '\">' + K[1] + '</a>'");
        } else {
            $ds->td("(K[2]?icon('b3',K[1],'_blank'):icon('b4','javascript:;','\$(this).ajaxLink(\'create\',' + K[0] + ');')) + (K[2]?'<a href=\"' + K[1] + '\" target=\"_blank\">' + K[1] + '</a>':K[1])");
        }
        $ds->td("K[3]");
        $ds->td("K[4]");
        $ds->td("K[5]");
        $ds->td("icon('a5','".PHP_FILE."?action=edit&model=".$model['modelename']."&id=' + K[0])");
        $ds->open();
        $ds->thead = '<tr>'; $i=0;
        foreach ($fields as $field=>$label) {
            $ds->thead.= '<th>'.($i==0?'ID) ':null).$label.'</th>'; $i++;
        }
        $ds->thead.= '<th>'.($length==0?'ID) ':null).t('article/path').'</th><th>'.t('article/hits').'</th><th>'.t('article/digg').'</th><th>'.t('article/date').'</th><th>'.t('system::Manage').'</th></tr>';
        while ($rs = $ds->result()) {
            $K = null;
            foreach ($fields as $field=>$label) {
                $K.= ",'".t2js(h2c($rs[$field]))."'";
            }
            $ds->tbody("E(".$rs['id'].",'".t2js(SITE_BASE.$rs['path'])."',".(is_file(LAZY_PATH.'/'.$rs['path'])?1:0).",".$rs['hits'].",".$rs['digg'].",'".date('Y-m-d H:i:s',$rs['date'])."'{$K});");
        }
        $ds->close();
        $ds->display();
    }
}
// *** *** www.LazyCMS.net *** *** //
function lazy_set(){
    $db = get_conn();
    $submit = isset($_POST['submit']) ? strtolower($_POST['submit']) : null;
    $lists  = isset($_POST['lists']) ? $_POST['lists'] : null;
    $model  = isset($_GET['model'])?$_GET['model']:null;
    empty($lists) ? ajax_alert(t('article/alert/noselect')) : null ;
    switch($submit){
        case 'create':
            if (Content_Article::create($model,$lists)) {
                ajax_success(t('article/alert/create'),1);
            }
            break;
        case 'delete':
            $table  = Content_Model::getDataTableName($model);
            $jtable = Content_Model::getJoinTableName($model);
            // 删除文件
            $result = $db->query("SELECT * FROM `{$table}` WHERE `id` IN({$lists});");
            while ($rs = $db->fetch($result)) {
                $file = LAZY_PATH.'/'.$rs['path'];
                if (is_file($file)){ unlink($file); }
            }
            // 删除记录
            $db->delete($table,"`id` IN({$lists})");
            $db->delete($jtable,array("`tid` IN({$lists})"));
            ajax_success(t('article/alert/delete'),1);
            break;
        default :
            ajax_error(t('system::error/invalid'));
            break;
    }
}
// *** *** www.LazyCMS.net *** *** //
function lazy_edit(){
    $db = get_conn(); $data = array(); $_USER = System::getAdmin();
    $mName  = isset($_REQUEST['model']) ? strtolower($_REQUEST['model']) : null;
    $docId  = isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : null;
    $selTab = array_search($mName,g('MODEL'))+4;
    $model  = Content_Model::getModelByEname($mName); if (!$model) { trigger_error(t('system::error/invalid')); }
    $sorts  = $db->count("SELECT * FROM `#@_content_sort_join` WHERE `modelid`=".DB::quote($model['modelid']).";");
    $title  = (empty($docId) ? t('system::add') : t('system::edit')).$model['modelname'];
    $path   = isset($_POST['path']) ? $_POST['path'] : null;
    $table  = Content_Model::getDataTableName($model['modelename']);
    $sortid = isset($_POST['sortid']) ? $_POST['sortid'] : 0;
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
        $path  = Content_Article::formatPath($maxid,$path,$data[$model['iskeyword']]);
        // 验证路径不能重复
        $val->check('path|0|'.t('article/check/path').';path|5|'.t('article/check/path1').';path|4|'.t('article/check/path2')."|SELECT COUNT(*) FROM `{$table}` WHERE `path`=".DB::quote($path).(empty($docId)?null:" AND `id` <> {$docId}"));
        $val->check('description|1|'.t('article/check/description').'|0-250');
        if ($val->isVal()) {
            $val->out();
        } else {
            $editor = $tag->getEditors();
            foreach ($editor as $k=>$e) {
                // 下载远程图片
                if ($e->snapimg) {
                    $snapimg = isset($_POST[$k.'_attr']['snapimg']) ? $_POST[$k.'_attr']['snapimg'] : false;
                    if ($snapimg) {
                        $data[$k] = snap_img($data[$k]);
                    }
                }
                // 删除站外连接
                if ($e->dellink) {
                    $data[$k] = preg_replace('/<a([^>]*)href=["\']*(http|https)\:\/\/(?!'.preg_quote($_SERVER['HTTP_HOST'],'/').')([^>]*)>(.*)<\/a>/isU','$4',$data[$k]);
                }
                // 自动截取简述
                if (!empty($model['description']) && $model['description']==$k) {
                    $description = (strlen($description)==0) ? left(clear_blank(preg_replace('/<[^>]*>/iU','',$data[$k])),180) : $description;
                }
            }
            // 将数据写入数据库
            if (empty($docId)) {
                $row = array(
                    'sortid'    => $sortid,
                    'order'     => now(),
                    'date'      => now(),
                    'path'      => $path,
                    'userid'    => $_USER['adminid'],
                    'description'   => $description,
                );
                if (!empty($data)) {
                    $row = array_merge($row,$data);
                }
                $db->insert($table,$row);
                $docId = $db->lastId();
                $text = t('article/alert/add');
            } else {
                $row = array(
                    'sortid'  => $sortid,
                    'path'    => $path,
                    'userid'  => $_USER['adminid'],
                    'description' => $description,
                );
                if (!empty($data)) {
                    $row = array_merge($row,$data);
                }
                $db->update($table,$row,DB::quoteInto('`id` = ?',$docId));
                $text = t('article/alert/edit');
            }
            // 自动获取关键词
            if (!empty($model['iskeyword'])) {
                $keywords = isset($_POST['keywords']) ? $_POST['keywords'] : null;
                $autokeys = isset($_POST['autokeys']) ? $_POST['autokeys'] : null;
                if ($autokeys && empty($keywords)) {
                    $keywords = System::getKeywords($data[$model['iskeyword']]);
                    $keywords = implode(',',$keywords);
                }
                $key->save($docId,$keywords,c('GET_RELATED_KEY'));
            }
            $referer = isset($_POST['__referer'])?$_POST['__referer']:PHP_FILE;
			$referer = strpos($referer,basename(PHP_FILE))!==false?$referer:PHP_FILE;
            // 生成页面
			if (Content_Article::create($mName,$docId)) {
				// 输出执行结果
				ajax_success($text,$referer);
			}
        }
    } else {
        if (!empty($docId)) {
            $res = $db->query("SELECT * FROM `{$table}` WHERE `id`=?",$docId);
            if ($data = $db->fetch($res)) {
                $path   = h2c($data['path']);
                $sortid = $data['sortid'];
                if (!empty($model['iskeyword'])) {
                    $keywords = $key->get($docId);
                }
                $description = $data['description'];
            }
        }
    }
    System::style('
        #__sortname{ width:150px; height:23px; display:block; cursor:default; line-height:23px; letter-spacing:1px; padding:0px 4px; border:1px solid #c6d9e7; color:#333333; background:url(../../common/images/buttons-bg.png) repeat-x; }
        #__sorts .body a.disabled{ color:#ccc; }
        #__sorts .body a.disabled:hover{ background:#fff;color:#ccc; cursor:default; }
        #__sorts .body a{ text-decoration:none; line-height:150%; display:block;}
        #__sorts .body a:hover{background:#316AC5; color:#FFFFFF;}
        
    ');
    System::loadScript('content.article');
    System::header($title,$selTab);

    echo '<form id="form1" name="form1" method="post" action="">';
    echo '<fieldset><legend rel="tab"><a rel=".show" cookie="false"><img class="a2 os" src="../system/images/white.gif" />'.$title.'</a></legend>';
    echo '<div class="show">';

    if ($sorts > 0) {
        echo '<p><label>'.t('article/sort').':</label><span class="box"><div id="__sortname" onclick="$(this).toggleSorts();">'.t('article/select').'</div>';
        echo '<div id="__sorts" class="panel" style="display:none;">';
        echo '<div class="head"><strong>'.t('article/select').'</strong><a href="javascript:;" onclick="$(\'#__sorts\').toggle();"></a></div><div class="body">';
        echo '<a href="javascript:;" onclick="$(this).setSortId();" value="0" label="'.t('article/select').'">--- '.t('article/nosort').' ---</a>';
        echo Content_Sort::getSortListByParentId($model['modelid'],$sortid);
        echo '</div></div><input name="sortid" type="hidden" id="sortid" value="'.$sortid.'" /><script type="text/javascript">$(\'#__sorts a[value='.$sortid.']\').setSortId();</script></span></p>';
    }

    echo $tag->fetch('<p><label>{label}:</label>{object}</p>',$data);
    echo '<p><label>'.t('article/path').':</label><input help="article/path" class="in w500" type="text" name="path" id="path" value="'.(empty($path)?$model['modelpath']:$path).'" /></p>';
    echo '</div></fieldset>';

    echo '<fieldset><legend><a rel=".more-attr"><img class="a2 os" src="../system/images/white.gif" />'.t('system::moreattr').'</a></legend>';
    echo '<div class="more-attr">';
    if (!empty($model['iskeyword'])) {
        echo '<p><label>'.t('article/keyword').':</label><input class="in w400" type="text" name="keywords" id="keywords" value="'.$keywords.'" />&nbsp;<button type="button" onclick="$(\'#keywords\').getKeywords(\'#'.$model['iskeyword'].'\')">'.t('system::get').'</button></p>';
    }
    echo '<p><label>'.t('article/description').':</label><textarea name="description" id="description" rows="5" class="in w400">'.$description.'</textarea></p>';
    echo '</div></fieldset>';
    echo but('system::save').'<input name="id" type="hidden" value="'.$docId.'" /><input name="__referer" type="hidden" value="'.$_SERVER['HTTP_REFERER'].'" /></form>';
}
// *** *** www.LazyCMS.net *** *** //
function lazy_after(){
    System::footer();
}