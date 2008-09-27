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
 * 文档管理
 *
 * @copyright   Copyright (c) 2007-2008 lazycms.net All rights reserved.
 * @author      Lukin <mylukin@gmail.com>
 * @date        2008-8-26
 */
// lazy_before *** *** www.LazyCMS.net *** ***
function lazy_before(){
    check_login('article');
    // 设置公共菜单
    $menus = array(); $model = array();
    foreach (Model::getModels('list') as $v) {
        $model[] = $v['modelename'];
        $menus[] = L('common/add').$v['modelname'].':article.php?action=edit&model='.$v['modelename'];
    }
    G('MODEL',$model);
    G('TABS',
        L('sort/@title').':sort.php;'.
        L('article/@title').':article.php;'.
        L('sort/add/@title').':sort.php?action=edit;'.implode(';',$menus)
    );
    G('SCRIPT','LoadScript("content.article");');
}
// lazy_default *** *** www.LazyCMS.net *** ***
function lazy_default(){
    /*
    import('system.parsetags');
    $ph = new ParseTags();
    $ph->loadHTML(LAZY_PATH.'/themes/'.C('TEMPLATE').'/tags.html');
    $tag = $ph->fetch('foreach');
    print_r($tag);
    $tag = $ph->fetch('select');
    print_r($tag);
    */
    $hl = '<form id="form1" name="form1" method="post" action="">';
    $hl.= '<fieldset><legend><a class="collapsed" rel=".show" cookie="false">'.L('article/@title').'</a></legend>';
    $hl.= '<div class="show">';
    $hl.= '开发中...';
    $hl.= '</div></fieldset>';
    $hl.= '</form>';
    print_x(L('article/@title'),$hl);
}
// lazy_edit *** *** www.LazyCMS.net *** ***
function lazy_edit(){
    G('HEAD','
        <style type="text/css">
        #toggleSorts{ display:none; width:500px; left:140px; top:55px; z-index:100; }
	    #toggleSorts .head{ width:495px;}
        #toggleSorts .body{ padding:5px;}
        #toggleSorts ul{margin:0 0 0 20px;padding:0;}
        </style>
    ');
    $db = get_conn(); $data = array();
    $m  = isset($_REQUEST['model']) ? strtolower($_REQUEST['model']) : null;
    $id = isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : null;
    $n  = array_search($m,G('MODEL'))+4;
    $model = Model::getModel($m); if (!$model) { trigger_error(L('error/invalid','system')); }
    $sort  = Article::sort($model['modelid']);
    $title = (empty($id) ? L('common/add') : L('common/edit')).$model['modelname'];
    $path  = isset($_POST['path']) ? $_POST['path'] : null;
    $table = Model::getDataTableName($model['modelename']);
    $description = isset($_POST['description']) ? $_POST['description'] : null;
    // 加载字段解析类
    import('system.field2tag');
    import('system.keywords');
    $key = new Keywords($model['modelename']);
    $tag = new Field2Tag($model);
    $val = $tag->getVal();
    if ($val->method()) {
        $data  = $tag->_POST();
        // 路径转换
        $maxid = $db->max('id',$table);
        $path  = Article::formatPath($maxid,$path,$data[$model['setkeyword']]);
        if ($val->isVal()) {
            $val->out();
        } else {
            $editor = $tag->getEditors();
            foreach ($editor as $k=>$e) {
                // 下载远程图片
                if ($e['snapimg']) {
                    $snapimg = isset($_POST[$k.'_attr']['snapimg']) ? $_POST[$k.'_attr']['snapimg'] : false;
                    if ($snapimg) {
                        $data[$k] = snapImg($data[$k]);
                    }
                }
                // 删除站外连接
                if ($e['dellink']) {
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
                $id   = $db->lastId();
                $text = L('article/pop/addok');
            } else {
                $row = array(
                    'path' => $path,
                    'description' => $description,
                );
                if (!empty($data)) {
                    $row = array_merge($row,$data);
                }
                $db->update($table,$row,DB::quoteInto('`id` = ?',$id));
                $text = L('article/pop/editok');
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
            // 输出执行结果
            echo_json(array(
                'text' => $text,
                'url'  => PHP_FILE,
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

    if ($sort) {
        $hl.= '<p><label>'.L('article/add/sort').'：</label><button type="button" onclick="$(this).toggleSorts();">请选择分类...</button></p>';
    }

    $hl.= $tag->fetch('<p><label>{label}：</label>{object}</p>',$data);
    $hl.= '</div></fieldset>';

    $hl.= '<fieldset><legend><a class="collapse" rel=".more-attr">'.L('common/attr').'</a></legend>';
    $hl.= '<div class="more-attr">';
    $hl.= '<p><label>'.L('article/add/path').'：</label><input tip="::300::'.ubbencode(L('model/add/path/@tip')).'<br/>'.h2encode(L('article/add/path/@tip')).'" class="in4" type="text" name="path" id="path" value="'.(empty($path)?$model['modelpath']:$path).'" /></p>';
    if (!empty($model['setkeyword'])) {
        $hl.= '<p><label>'.L('article/add/keywords').'：</label><input tip="'.L('article/add/keywords').'::250::'.L('article/add/keywords/@tip').'" class="in4" type="text" name="keywords" id="keywords" value="'.$keywords.'" />&nbsp;<button type="button" onclick="$(\'#keywords\').getKeywords(\'#'.$model['setkeyword'].'\')" tip="'.L('common/get/@tip','system').'">'.L('common/get','system').'</button></p>';
    }
    $hl.= '<p><label>'.L('article/add/description').'：</label><textarea tip="'.L('article/add/description').'::'.L('article/add/description/@tip').'" name="description" id="description" rows="5" class="in4">'.$description.'</textarea></p>';
    $hl.= '</div></fieldset>';
    $hl.= but('save').'<input name="id" type="hidden" value="'.$id.'" /></form>';

    $hl.= '<div id="toggleSorts" class="panel">';
    $hl.= '<div class="head"><strong>请选择分类...</strong><a href="javascript:;" onclick="$(this).toggleSorts();">×</a></div><div class="body">';
    $hl.= Article::sort($model['modelid'],$sortids);
    $hl.= '<p class="tr"><button type="button">'.L('article/add/submit').'</button>&nbsp;<button type="button" onclick="$(this).toggleSorts();">'.L('article/add/cancel').'</button></p>';
    $hl.= '</div></div>';
    print_x($title,$hl,$n);
}
