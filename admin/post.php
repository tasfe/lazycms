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
// 加载公共文件
require dirname(__FILE__).'/admin.php';
// 取得管理员信息
$_ADMIN = LCUser::current();
// 标题
admin_head('title',  __('Posts'));
admin_head('styles', array('css/post'));
admin_head('scripts',array('js/post'));
// 动作
$action  = isset($_REQUEST['action'])?$_REQUEST['action']:null;

switch ($action) {
    // 添加
    case 'new':
        //fb(LCPost::getPostById(1));
	    current_user_can('post-new');
	    // 重置标题
	    admin_head('title',__('Add New Post'));
	    admin_head('loadevents','post_manage_init');
	    include ADMIN_PATH.'/admin-header.php';
	    post_manage_page('add');	    
	    include ADMIN_PATH.'/admin-footer.php';
	    break;
	// 编辑
	case 'edit':
	    current_user_can('post-edit');
	    // 重置标题
	    admin_head('title',__('Edit Post'));
	    admin_head('loadevents','post_manage_init');
	    include ADMIN_PATH.'/admin-header.php';
	    post_manage_page('edit');	    
	    include ADMIN_PATH.'/admin-footer.php';
	    break;
	// 保存
	case 'save':
	    
	    $validate = new Validate();
        if ($validate->post()) {
            $mcode    = isset($_POST['model'])?$_POST['model']:null;
            $model    = LCModel::getModelByCode($mcode,'*','_');
            $postid   = isset($_POST['postid'])?$_POST['postid']:0;
            $category = isset($_POST['category'])?$_POST['category']:array();
            $title    = isset($_POST['title'])?$_POST['title']:null;
            $autokeys = isset($_POST['autokeys'])?$_POST['autokeys']:null;
            $path     = isset($_POST['path'])?$_POST['path']:null;
            $content  = isset($_POST['content'])?$_POST['content']:null;
            $page     = isset($_POST['page'])?$_POST['page']:null;
            $keywords = isset($_POST['keywords'])?$_POST['keywords']:null;
            $description = isset($_POST['description'])?$_POST['description']:null;
            
            $validate->check(array(
                array('title',VALIDATE_EMPTY,_x('The title field is empty.','post')),
                array('title',VALIDATE_LENGTH,_x('The title field length must be %d-%d characters.','post'),1,255),
            ));

            $validate->check(array(
                array('path',VALIDATE_EMPTY,_x('The path field is empty.','post')),
            ));

            // 验证自定义的字段
            if ($model['fields']) {
                foreach($model['fields'] as $field) {
                    if (empty($field['v'])) continue;
                    $last_rules = array();
                    $rules = explode("\n",$field['v']);
                    foreach($rules as $rule) {
                        if (strpos($rule,'|')===false) continue;
                        $VRS = explode('|',rtrim($rule,';')); array_unshift($VRS,$field['n']);
                        $last_rules[] = $VRS;
                    }
                    $validate->check($last_rules);
                }
            }

            // 验证通过
            if (!$validate->is_error()) {
                // TODO 判断是否开启自动获取关键词
                
                // 获取数据
                $data = array(
                    'category' => $category,
                    'model'    => esc_html($mcode),
                );
                // 获取模型字段值
                if ($model['fields']) {
                    foreach($model['fields'] as $field) {
                        $data['meta'][trim($field['n'],'_')] = isset($_POST[$field['n']])?$_POST[$field['n']]:null;
                    }
                }
                
                // 插入
                if (empty($postid)) {
                    $path = esc_html($path);
                    $data['author'] = $_ADMIN['userid'];
                    $data['passed'] = 0;
                    if ($post = LCPost::addPost($title,$content,$path,$data)) {
                        $postid = $post['postid'];
                    }
                }
                // 更新
                else {
                    $data['path']    = esc_html($path);
                    $data['title']   = $title;
                    $data['content'] = $content;
                    
                }
            }
        }
	    break;
	// 获取扩展字段
	case 'extend-attr':
	    $mcode  = isset($_GET['model'])?$_GET['model']:null;
	    $model  = LCModel::getModelByCode($mcode,'*','_');
        $attrs = null;

	    if ($model) {
	    	foreach ($model['fields'] as $field) {
	    		$attrs.= '<tr>';
                $attrs.=    '<th><label for="'.$field['n'].'">'.$field['l'];
                if (!empty($field['h'])) {
                    $attrs.=    '<span class="description">'.$field['h'].'</span>';
                }
                $attrs.=        '</label>';
                $attrs.=    '</th>';
                $attrs.=    '<td>';
                switch ($field['t']) {
                    case 'input':
                        $attrs.= '<input id="'.$field['n'].'" name="'.$field['n'].'" type="text" style="width:'.$field['w'].'" maxlength="'.$field['c'].'" value="'.$field['d'].'" />';
                        break;
                    case 'textarea':
                        $attrs.= '<textarea name="'.$field['n'].'" id="'.$field['n'].'" style="width:'.$field['w'].'" rows="8">'.$field['d'].'</textarea>';
                        break;
                    case 'select':
                        $values = explode("\n",$field['s']);
                        $attrs.= '<select name="'.$field['n'].'" id="'.$field['n'].'" edit="true" style="width:'.$field['w'].'">';
                        foreach ($values as $k=>$v) {
                            $v = trim($v);
                            if ($v!='') {
                                $vs = explode(':',$v);
                                $vs = array_map('esc_html',$vs); $vs[1] = isset($vs[1])?$vs[1]:$vs[0];
                                $selected = !empty($field['d']) ? (strval($vs[0])==strval($field['d']) ? ' selected="selected"' : null) : null;
                                $attrs.= '<option value="'.$vs[0].'"'.$selected.'>'.$vs[1].'</option>';
                            }
                        }
                        $attrs.= '</select>';
                        break;
                    case 'radio': case 'checkbox':
                        $values = explode("\n",$field['s']);
                        $attrs.= '<div id="'.$field['n'].'" style="width:'.$field['w'].'">';
                        foreach ($values as $k=>$v) {
                            $v = trim($v);
                            if ($v!='') {
                                $vs = explode(':',$v);
                                $vs = array_map('esc_html',$vs); $vs[1] = isset($vs[1])?$vs[1]:$vs[0];
                                $checked = !empty($field['d']) ? (instr($vs[0],$field['d']) ? ' checked="checked"' : null) : null;
                                $attrs.= '<label><input name="'.$field['n'].($field['t']=='checkbox'?'['.$k.']':null).'" type="'.$field['t'].'" value="'.$vs[0].'"'.$checked.' />'.$vs[1].'</label>';
                            }
                        }
                        $attrs.= '</div>';
                        break;
                    case 'upfile':
                        $attrs.= '<input id="'.$field['n'].'" name="'.$field['n'].'" type="text" style="width:'.$field['w'].'" />&nbsp;<button type="button">'.__('Browse...').'</button>';
                        break;
                }
                
                $attrs.=     '</td>';
                $attrs.= '</tr>';
	    	}
	    }
        echo_json('Return',$attrs);
	    break;
    default:
	    current_user_can('post-list');
	    admin_head('loadevents','post_list_init');
	    $posts = array();
        include ADMIN_PATH.'/admin-header.php';
        echo '<div class="wrap">';
        echo   '<h2>'.admin_head('title').'<a class="btn" href="'.PHP_FILE.'?action=new">'._x('Add New','post').'</a></h2>';
        echo   '<form action="'.PHP_FILE.'?action=bulk" method="post" name="postlist" id="postlist">';
        actions();
        echo       '<table class="data-table" cellspacing="0">';
        echo           '<thead>';
        thead();
        echo           '</thead>';
        echo           '<tfoot>';
        thead();
        echo           '</tfoot>';
        echo           '<tbody>';
        if ($posts) {
            
        } else {
            echo           '<tr><td colspan="6" class="tc">'.__('No record!').'</td></tr>';
        }
        echo           '</tbody>';
        echo       '</table>';
        actions();
        echo   '</form>';
        echo '</div>';
        include ADMIN_PATH.'/admin-footer.php';
        break;
}

/**
 * 批量操作
 *
 */
function actions() {
    echo '<div class="actions">';
    echo     '<select name="actions">';
    echo         '<option value="">'.__('Bulk Actions').'</option>';
    echo         '<option value="create">'.__('Create').'</option>';
    echo         '<option value="delete">'.__('Delete').'</option>';
    echo     '</select>';
    echo     '<button type="button">'.__('Apply').'</button>';
    echo '</div>';
}
/**
 * 表头
 *
 */
function thead() {
    echo '<tr>';
    echo     '<th class="check-column"><input type="checkbox" name="select" value="all" /></th>';
    echo     '<th>'._x('Title','post').'</th>';
    echo     '<th>'._x('Author','post').'</th>';
    echo     '<th>'._x('Categories','post').'</th>';
    echo     '<th>'._x('Tags','post').'</th>';
    echo     '<th>'._x('Date','post').'</th>';
    echo '</tr>';
}

/**
 * 管理页面
 *
 * @param string $action
 */
function post_manage_page($action) {
    $referer = referer(PHP_FILE);
    $postid  = isset($_GET['postid'])?$_GET['postid']:0;
    $models  = LCModel::getModels(1);
    if ($action!='add') {
    	$_DATA = array();
    }
    $ext   = C('CreateFileExt');
    $mcode = isset($_GET['model'])?$_GET['model']:null;
    if ($mcode) {
    	$model = LCModel::getModelByCode($mcode);
    } else {
        $model   = array_pop(array_slice($models,0,1));
    }
    $title   = isset($_DATA['title'])?$_DATA['title']:null;
    $content = isset($_DATA['content'])?$_DATA['content']:null;
    $path    = isset($_DATA['path'])?$_DATA['path']:$model['path'];
    $page    = isset($_DATA['page'])?$_DATA['page']:null;
    
    echo '<div class="wrap">';
    echo   '<h2>'.admin_head('title').'</h2>';
    echo   '<form action="'.PHP_FILE.'?action=save" method="post" name="postmanage" id="postmanage">';
    echo   '<fieldset>';
    echo       '<table class="form-table">';
    echo           '<tbody class="fixed-attr">';
    if ($models) {
        echo           '<tr>';
        echo               '<th><label for="model">'._x('Model','post').'</label></th>';
        echo               '<td><select name="model" id="model">';
        foreach ($models as $m) {
        	echo               '<option value="'.$m['langcode'].'">'.$m['name'].'</option>';
        }
        echo               '</select></td>';
        echo           '</tr>';
    }
    echo               '<tr class="taxonomyid">';
    echo                   '<th><label for="taxonomyid">'._x('Categories','post').'</label></th>';
    echo                   '<td>';
    echo                       categories_tree();
    echo                   '</td>';
    echo               '</tr>';
    echo               '<tr>';
    echo                   '<th><label for="title">'._x('Title','post').' <span class="description">'.__('(required)').'</span></label></th>';
    echo                   '<td>';
    echo                       '<input id="title" name="title" type="text" size="70" value="'.$title.'" />';
    echo                       '&nbsp;<label><input type="checkbox" value="1" name="autokeys">'.__('Auto get keywords').'</label>';
    echo                   '</td>';
    echo               '</tr>';
    echo               '<tr>';
    echo                   '<th><label for="content">'._x('Content','post').'</label></th>';
    echo                   '<td><textarea cols="120" rows="15" id="content" name="content">'.$content.'</textarea></td>';
    echo               '</tr>';
    echo               '<tr>';
    echo                   '<th><label for="path">'._x('Path','model').' <span class="description">'.__('(required)').'</span></label></th>';
    echo                   '<td><input id="path" name="path" type="text" size="70" value="'.$path.'" /> <span class="rules">';
    echo                       '<a href="#%ID'.$ext.'">['.__('Post ID').']</a>';
    echo                       '<a href="#%MD5'.$ext.'">['.__('MD5 Value').']</a>';
    echo                       '<a href="#%PY'.$ext.'">['.__('Pinyin').']</a>';
    echo                   '</span></td>';
    echo               '</tr>';
    echo           '</tbody>';
    echo           '<tbody class="extend-attr"></tbody>';
    echo       '</table>';
    echo   '</fieldset>';
    echo   '<fieldset>';
    echo       '<a href="javascript:;" class="toggle" title="'.__('Click to toggle').'"><br/></a>';
    echo       '<h3>'.__('More attribute').'</h3>';
    echo       '<table class="form-table">';
    echo           '<tbody class="more-attr">';
    echo               '<tr>';
    echo                   '<th><label for="pagetemplate">'._x('Page Template','post').'</label></th>';
    echo                   '<td>';
    echo                       '<select id="pagetemplate" name="page">';
    echo                           $models?'<option value="">'.__('Use the model set').'</option>':null;
    echo                           options(C('Template'),C('TemplateExts'),'<option value="#value#"#selected#>#name#</option>',$page);
    echo                       '</select>';
    echo                   '</td>';
    echo               '</tr>';
    echo               '<tr>';
    echo                   '<th><label for="keywords">'._x('Keywords','post').'</label></th>';
    echo                   '<td><input type="text" size="70" name="keywords" id="keywords" value="" />&nbsp;<button type="button">'.__('Get').'</button></td>';
    echo               '</tr>';
    echo               '<tr>';
    echo                   '<th><label for="description">'._x('Description','post').'</label></th>';
    echo                   '<td><textarea cols="70" rows="5" id="description" name="description"></textarea></td>';
    echo               '</tr>';    
    echo           '</tbody>';
    echo       '</table>';
    echo '</fieldset>';
    if ($action=='add') {
        echo   '<p class="submit"><button type="submit">'.__('Add Post').'</button> <button type="button" onclick="self.location.replace(\''.$referer.'\')">'.__('Back').'</button></p>';
    } else {
        echo   '<input type="hidden" name="postid" value="'.$postid.'" />';
        echo   '<p class="submit"><button type="submit">'.__('Update Post').'</button> <button type="button" onclick="self.location.replace(\''.$referer.'\')">'.__('Back').'</button></p>';
    }
    echo   '</form>';
    echo '</div>';
}

/**
 * 显示分类树
 *
 * @param int $selected
 * @param  $trees
 * @return string
 */
function categories_tree($selected=0,$trees=null) {
    static $func = null; if (!$func) $func = __FUNCTION__;
    $hl = sprintf('<ul class="%s">',is_null($trees) ? 'categories' : 'children');
    if ($trees===null) $trees = LCTaxonomy::getTaxonomysTree();
    foreach ($trees as $i=>$tree) {
        $hl.= sprintf('<li><label class="selectit" for="category-%d">',$tree['taxonomyid']);
        $hl.= sprintf('<input type="checkbox" id="category-%d" name="category[]" value="%d">%s</label>',$tree['taxonomyid'],$tree['taxonomyid'],$tree['name']);
    	if (isset($tree['subs'])) {
    		$hl.= $func($selected,$tree['subs']);
    	}
        $hl.= '</li>';
    }
    $hl.= '</ul>';
    return $hl;
}