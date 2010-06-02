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
$_ADMIN = ModuleUser::current(); 
// 标题
admin_head('title',  _('Posts'));
admin_head('styles', array('css/post'));
admin_head('scripts',array('js/post'));
// 动作
$action  = isset($_REQUEST['action'])?$_REQUEST['action']:null;

switch ($action) {
    // 添加
    case 'new':
	    current_user_can('post-new');
	    // 重置标题
	    admin_head('title',_('Add New Post'));
	    admin_head('loadevents','post_manage_init');
	    include ADMIN_PATH.'/admin-header.php';
	    post_manage_page('add');	    
	    include ADMIN_PATH.'/admin-footer.php';
	    break;
	// 编辑
	case 'edit':
	    current_user_can('post-edit');
	    // 重置标题
	    admin_head('title',_('Edit Post'));
	    admin_head('loadevents','post_manage_init');
	    include ADMIN_PATH.'/admin-header.php';
	    post_manage_page('edit');	    
	    include ADMIN_PATH.'/admin-footer.php';
	    break;
	// 保存
	case 'save':
	    
	    break;
	// 获取扩展字段
	case 'extend-attr':
	    $mcode  = isset($_GET['model'])?$_GET['model']:null;
	    $model  = ModuleModel::get_model_by_code($mcode);
        $result = $attrs = $sorts = null;

        $sorts.= '<tr class="sortid">';
        $sorts.=    '<th><label for="sortid">'.__('Parent','post').'</label></th>';
        $sorts.=    '<td><select name="sortid" id="sortid">';
        $sorts.=      '<option value="0" path="" model="">--- '._('None').' ---</option>';
        $sorts.=      display_option_tree($mcode,0);
        $sorts.=    '</select></td>';
        $sorts.= '</tr>';

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
                        $attrs.= '<input id="'.$field['n'].'" name="'.$field['n'].'" type="text" style="width:'.$field['w'].'" />&nbsp;<button type="button">'._('Browse...').'</button>';
                        break;
                }
                
                $attrs.=     '</td>';
                $attrs.= '</tr>';
	    	}
	    }
        $result = array(
            'sort' => $sorts,
            'attr' => $attrs
        );
        echo_json('Return',$result);
	    break;
    default:
	    current_user_can('post-list');
	    admin_head('loadevents','post_list_init');
	    $posts = array();
        include ADMIN_PATH.'/admin-header.php';
        echo '<div class="wrap">';
        echo   '<h2>'.admin_head('title').'<a class="btn" href="'.PHP_FILE.'?action=new">'.__('Add New','post').'</a></h2>';
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
            echo           '<tr><td colspan="6" class="tc">'._('Empty').'</td></tr>';
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
    echo         '<option value="">'._('Bulk Actions').'</option>';
    echo         '<option value="Create">'._('Create').'</option>';
    echo         '<option value="delete">'._('Delete').'</option>';
    echo     '</select>';
    echo     '<button type="button">'._('Apply').'</button>';
    echo '</div>';
}
/**
 * 表头
 *
 */
function thead() {
    echo '<tr>';
    echo     '<th class="check-column"><input type="checkbox" name="select" value="all" /></th>';
    echo     '<th>'.__('Title','post').'</th>';
    echo     '<th>'.__('Author','post').'</th>';
    echo     '<th>'.__('Categories','post').'</th>';
    echo     '<th>'.__('Tags','post').'</th>';
    echo     '<th>'.__('Date','post').'</th>';
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
    $models  = ModuleModel::get_models(1);
    if ($action!='add') {
    	$_DATA = array();
    }
    $ext   = C('CreateFileExt');
    $mcode = isset($_GET['model'])?$_GET['model']:null;
    if ($mcode) {
    	$model = ModuleModel::get_model_by_code($mcode);
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
        echo               '<th><label for="model">'.__('Model','post').'</label></th>';
        echo               '<td><select name="model" id="model">';
        foreach ($models as $m) {
        	echo               '<option value="'.$m['code'].'">'.$m['name'].'</option>';
        }
        echo               '</select></td>';
        echo           '</tr>';
    }
    echo               '<tr class="sortid">';
    echo                   '<th><label for="sortid">'.__('Parent','post').'</label></th>';
    echo                   '<td><select name="sortid" id="sortid">';
    echo                       '<option value="0" path="" model="">--- '._('None').' ---</option>';
    echo                       display_option_tree($model['code'],0);
    echo                   '</select></td>';
    echo               '</tr>';
    echo               '<tr>';
    echo                   '<th><label for="title">'.__('Title','post').' <span class="description">'._('(required)').'</span></label></th>';
    echo                   '<td>';
    echo                       '<input id="title" name="title" type="text" size="70" value="'.$title.'" />';
    echo                       '&nbsp;<label><input type="checkbox" value="1" name="autokeys">'._('Auto get keywords').'</label>';
    echo                   '</td>';
    echo               '</tr>';
    echo               '<tr>';
    echo                   '<th><label for="content">'.__('Content','post').'</label></th>';
    echo                   '<td><textarea cols="120" rows="15" id="content" name="content">'.$content.'</textarea></td>';
    echo               '</tr>';
    echo               '<tr>';
    echo                   '<th><label for="path">'.__('Path','model').' <span class="description">'._('(required)').'</span></label></th>';
    echo                   '<td><input id="path" name="path" type="text" size="70" value="'.$path.'" /> <span class="rules">';
    echo                       '<a href="#%ID'.$ext.'">['._('Post ID').']</a>';
    echo                       '<a href="#%MD5'.$ext.'">['._('MD5 Value').']</a>';
    echo                       '<a href="#%PY'.$ext.'">['._('Pinyin').']</a>';
    echo                   '</span></td>';
    echo               '</tr>';
    echo           '</tbody>';
    echo           '<tbody class="extend-attr"></tbody>';
    echo       '</table>';
    echo   '</fieldset>';
    echo   '<fieldset>';
    echo       '<a href="javascript:;" class="toggle" title="'._('Click to toggle').'"><br/></a>';
    echo       '<h3>'._('More attribute').'</h3>';
    echo       '<table class="form-table">';
    echo           '<tbody class="more-attr">';
    echo               '<tr>';
    echo                   '<th><label for="pagetemplate">'.__('Page Template','post').'</label></th>';
    echo                   '<td>';
    echo                       '<select id="pagetemplate" name="page">';
    echo                           $models?'<option value="">'._('Use model setting').'</option>':null;
    echo                           options(C('Template'),C('TemplateExts'),'<option value="#value#"#selected#>#name#</option>',$page);
    echo                       '</select>';
    echo                   '</td>';
    echo               '</tr>';
    echo               '<tr>';
    echo                   '<th><label for="keywords">'.__('Keywords','post').'</label></th>';
    echo                   '<td><input type="text" size="70" name="keywords" id="keywords" value="" />&nbsp;<button type="button">'._('Get').'</button></td>';
    echo               '</tr>';
    echo               '<tr>';
    echo                   '<th><label for="description">'.__('Description','post').'</label></th>';
    echo                   '<td><textarea cols="70" rows="5" id="description" name="description"></textarea></td>';
    echo               '</tr>';    
    echo           '</tbody>';
    echo       '</table>';
    echo '</fieldset>';
    if ($action=='add') {
        echo   '<p class="submit"><button type="submit">'._('Add Post').'</button> <button type="button" onclick="self.location.replace(\''.$referer.'\')">'._('Back').'</button></p>';
    } else {
        echo   '<input type="hidden" name="postid" value="'.$postid.'" />';
        echo   '<p class="submit"><button type="submit">'._('Update Post').'</button> <button type="button" onclick="self.location.replace(\''.$referer.'\')">'._('Back').'</button></p>';
    }
    echo   '</form>';
    echo '</div>';
}

/**
 * 显示分类树
 *
 * @param int $sortid
 * @param int $selected
 * @param int $n
 * @param array $trees
 * @return string
 */
function display_option_tree($model,$selected=0,$n=0,$trees=null) {
    static $func = null; if (!$func) $func = __FUNCTION__;
    if ($trees===null) $trees = ModuleSort::get_sorts_tree();
    $hl = ''; $space = str_repeat('&nbsp; &nbsp; ',$n);
    foreach ($trees as $i=>$tree) {
        $sel  = $selected==$tree['sortid']?' selected="selected"':null;
        $path = ModuleSystem::format_path($tree['path'],array(
            'ID'  => $tree['sortid'],
            'PY'  => $tree['name'],
            'MD5' => $tree['sortid'],
        ));
        if (in_array($model,$tree['model'])) {
            $hl.= '<option value="'.$tree['sortid'].'"'.$sel.' path="'.$path.'" model="'.implode(',',$tree['model']).'">'.$space.'├ '.$tree['name'].'</option>';
        } else {
    	    $hl.= '<optgroup label="'.$space.'├ '.$tree['name'].'"></optgroup>';
        }
    	if (isset($tree['subs'])) {
    		$hl.= $func($model,$selected,$n+1,$tree['subs']);
    	}
    }
    return $hl;
}