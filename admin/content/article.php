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
    check_login('article');
    // 设置公共菜单
    $menus = array(); $model = array();
    foreach (Model::getModel() as $v) {
        $model[] = $v['modelename'];
        $menus[] = L('common/add').$v['modelname'].':article.php?action=edit&model='.$v['modelename'];
    }
    G('MODEL',$model);
    G('TABS',L('article/@title').':article.php;'.implode(';',$menus));
}
// lazy_default *** *** www.LazyCMS.net *** ***
function lazy_default(){ 
    print_x(L('article/@title'),'显示一个搜索过滤器，直接进行搜索');
}

function formatPath($p1,$p2,$p3,$p4=null){
    $p4 = empty($p4) ? now() : $p4;
    $R = str_replace(array('%I','%M','%P'),array($p1,md5($p1.salt(10)),pinyin($p3)),$p2);
    $R = strftime($p2,$p4);
    return $R;
}

// lazy_edit *** *** www.LazyCMS.net *** ***
function lazy_edit(){
    $db = get_conn(); $data = array();
    $m  = isset($_REQUEST['model']) ? strtolower($_REQUEST['model']) : null;
    $id = isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : null;
    $n  = array_search($m,G('MODEL'))+2;
    $model = Model::getModel($m); if (!$model) { trigger_error(L('error/invalid','system')); }
    $title = L('common/add').$model['modelname'];
    $path  = isset($_POST['path']) ? $_POST['path'] : null;
    $description = isset($_POST['description']) ? $_POST['description'] : null;

    import('system.field2tag');
    $tag = new Field2Tag($model);
    $val = $tag->getVal();
    if ($val->method()) {
        $table = Model::getDBName($model['modelename']);
        $data  = $tag->_POST();
        // 路径转换
        $maxid = $db->max('id',$table);
        $path  = formatPath($maxid,$path,$data[$model['setkeyword']]);
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
                import('system.keywords');
                $key = new Keywords();
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
        if (!empty($oneid)) {
        
        }
    }
    
    $hl = '<form id="form1" name="form1" method="post" action="">';
    $hl.= '<fieldset><legend rel="tab"><a class="collapsed" rel=".more-attr" cookie="false">'.$title.'</a></legend>';
    $hl.= '<div class="more-attr">';
    $hl.= $tag->fetch('<p><label>{label}：</label>{object}</p>');
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
    print_x($title,$hl,$n);
}