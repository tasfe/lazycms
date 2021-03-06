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
 * | Copyright (C) 2007-2010 LazyCMS.com All rights reserved.                  |
 * +---------------------------------------------------------------------------+
 * | LazyCMS is free software. See LICENSE for copyright notices and details.  |
 * +---------------------------------------------------------------------------+
 */
// 加载公共文件
include dirname(__FILE__).'/admin.php';
// 查询管理员信息
$_USER = user_current();
// 标题
system_head('title',  __('Categories'));
system_head('scripts',array('js/categories'));
// 动作
$method  = isset($_REQUEST['method'])?$_REQUEST['method']:null;
// 所属
$parent_file = 'categories.php';
// 权限检查
current_user_can('categories');

switch ($method) {
    // 强力插入
    case 'new':
        // 重置标题
        system_head('title', __('Add New Category'));
        system_head('styles', array('css/xheditor','css/datePicker'));
        system_head('scripts', array('js/xheditor','js/datePicker'));
        system_head('jslang', system_editor_lang());
        // 添加JS事件
        system_head('loadevents', 'sort_manage_init');
        include ADMIN_PATH . '/admin-header.php';
        // 显示页面
        category_manage_page('add');
        include ADMIN_PATH . '/admin-footer.php';
        break;
    // 活塞式运动，你懂得。。。
    case 'edit':
        // 重置标题
        system_head('title', __('Edit Category'));
        system_head('styles', array('css/xheditor','css/datePicker'));
        system_head('scripts', array('js/xheditor','js/datePicker'));
        system_head('jslang', system_editor_lang());
        // 添加JS事件
        system_head('loadevents', 'sort_manage_init');
        include ADMIN_PATH . '/admin-header.php';
        category_manage_page('edit');
        include ADMIN_PATH . '/admin-footer.php';
        break;
    // 保存
	case 'save':
        $taxonomyid  = isset($_POST['taxonomyid'])?$_POST['taxonomyid']:0;
        if (validate_is_post()) {
            // 路径两边不允许出现 /
            if (isset($_POST['path']))
                $_POST['path'] = trim($_POST['path'], '/');
            $mcode    = isset($_POST['model']) ? $_POST['model'] : null;
            $model    = model_get_bycode($mcode);
            $parent   = isset($_POST['parent']) ? $_POST['parent'] : '0';
            $name     = isset($_POST['name']) ? $_POST['name'] : null;
            $path     = isset($_POST['path']) ? $_POST['path'] : null;
            $list     = isset($_POST['list']) ? $_POST['list'] : null;
            $page     = isset($_POST['page']) ? $_POST['page'] : null;
            $keywords = isset($_POST['keywords']) ? $_POST['keywords'] : null;
            $description = isset($_POST['description']) ? $_POST['description'] : null;

            validate_check(array(
                array('name', VALIDATE_EMPTY, _x('The name field is empty.', 'sort')),
                array('name', VALIDATE_LENGTH, _x('The name field length must be %d-%d characters.', 'sort'), 1, 30),
            ));
            // 验证路径
            $path_exists = taxonomy_path_exists($taxonomyid, path_format($path, array('PY' => $name)));
            validate_check(array(
                array('path', VALIDATE_EMPTY, _x('The path field is empty.', 'sort')),
                array('path', VALIDATE_IS_PATH, sprintf(_x('The path can not contain any of the following characters %s', 'sort'), esc_html('* : < > | \\'))),
                array('path', (!$path_exists), _x('The path already exists.', 'sort')),
            ));

            // 验证自定义的字段
            if ($model['fields']) {
                foreach($model['fields'] as $field) {
                    if (empty($field['v'])) continue;
                    $last_rules = array();
                    $rules = explode("\n",$field['v']);
                    foreach($rules as $rule) {
                        if (strpos($rule,'|')===false) continue;
                        $VRS = explode('|',rtrim($rule,';')); array_unshift($VRS,$field['_n']);
                        $last_rules[] = $VRS;
                    }
                    validate_check($last_rules);
                }
            }

            if ($description) {
                validate_check(array(
                    array('description', VALIDATE_LENGTH, __('Description the field up to 255 characters.'), 0, 255),
                ));
            }

            // 安全有保证，做爱做的事吧！
            if (validate_is_ok()) {
                $data = array(
                    'model'     => esc_html($mcode),
                    'path'      => esc_html($path),
                    'list'      => esc_html($list),
                    'page'      => esc_html($page),
                    'keywords'  => esc_html($keywords),
                    'description' => esc_html($description),
                );
                // 获取模型字段值
                if ($model['fields']) {
                    foreach($model['fields'] as $field) {
                        if (isset($_POST[$field['_n']]) && $_POST[$field['_n']]) {
                            $data[$field['n']] = instr($field['t'],'basic,editor') ? $_POST[$field['_n']] : esc_html($_POST[$field['_n']]);
                        }

                    }
                }
                // 编辑
                if ($taxonomyid) {
                    $data['parent'] = esc_html($parent);
                    $data['name']   = esc_html($name);
                    taxonomy_edit($taxonomyid, $data);
                    $result = __('Category updated.');
                }
                // 强力插入了
                else {
                    $path     = esc_html($path);
                    $parent   = esc_html($parent);
                    $name     = esc_html($name);
                    $taxonomy = taxonomy_add('category', $name, $parent, $data);
                    $taxonomyid = $taxonomy['taxonomyid'];
                    $result = __('Category created.');
                }
                // 生成列表页
                if (taxonomy_create($taxonomyid)) {
                    $result = sprintf('<p>%s</p><p>%s</p>', $result, _x('[Submit] to Add New<br />[Cancel] to Back list', 'sort'));
                    ajax_confirm($result, "LazyCMS.redirect('" . PHP_FILE . "?method=new');", "LazyCMS.redirect('" . PHP_FILE . "');");
                } else {
                    ajax_alert($result . __('File create failed.'), "LazyCMS.redirect('" . PHP_FILE . "');");
                }
            }
        }
	    break;
	// 批量动作
	case 'bulk':
	    $action  = isset($_POST['action'])?$_POST['action']:null;
	    $listids = isset($_POST['listids'])?$_POST['listids']:null;
	    if (empty($listids)) {
	    	ajax_error(__('Did not select any item.'));
	    }
	    switch ($action) {
            // 生成
	        case 'createposts': case 'createlists': case 'createall':
                $names = taxonomy_get_names($listids);
                // 生成列表和文章
                if ($action == 'createall') {
                    publish_add(sprintf(__('Create Lists and Posts:%s'),$names),'publish_lists',array($listids,true));
                }
                // 只生成列表
                elseif ($action == 'createlists') {
                    publish_add(sprintf(__('Create Lists:%s'),$names),'publish_lists',array($listids,false));
                }
                // 只生成文章
                elseif ($action == 'createposts') {
                    publish_add(sprintf(__('Create Posts:%s'),$names),'publish_posts',array($listids));
                }
                ajax_success(__('Publish process successfully created.'),"LazyCMS.redirect('".ADMIN."publish.php?method=list');");
                break;
	        // 删除
	        case 'delete':
	            foreach ($listids as $taxonomyid) {
	            	taxonomy_delete($taxonomyid);
	            }
	            ajax_success(__('Categories deleted.'),"LazyCMS.redirect('".PHP_FILE."');");
	            break;
            default:
                ajax_alert(__('Parameter is invalid.'));
                break;
	    }
	    break;
    // 获取扩展字段
	case 'extend-attr':
        $model  = null; $hl = '';
	    $mcode  = isset($_REQUEST['model'])?$_REQUEST['model']:null;
	    $listid = isset($_REQUEST['listid'])?$_REQUEST['listid']:0;
        $suffix = C('HTML-Ext');
        if ($listid) {
            $taxonomy = taxonomy_get($listid);
        }
        if ($mcode) {
            $model = model_get_bycode($mcode);
            $path  = isset($taxonomy['list'])?$taxonomy['list']:$model['list'];
        } else {
            $path  = isset($taxonomy['list'])?$taxonomy['list']:'list'.$suffix;
        }
        header('X-LazyCMS-List: '.$path);
	    if ($model) {
	    	foreach ($model['fields'] as $field) {
                if (isset($taxonomy['meta'][$field['n']])) {
                    $field['d'] = $taxonomy['meta'][$field['n']];
                }
	    		$hl.= '<tr>';
                $hl.=    '<th><label for="'.$field['_n'].'">'.$field['l'];
                if (!empty($field['h'])) {
                    $hl.=    '<span class="resume">'.$field['h'].'</span>';
                }
                $hl.=        '</label>';
                $hl.=    '</th>';
                $hl.=    '<td>'.model_field2html($field).'</td>';
                $hl.= '</tr>';
	    	}
	    }
        ajax_return($hl);
	    break;
    default:
	    system_head('loadevents','sort_list_init');
	    $sorts = taxonomy_get_trees();
        include ADMIN_PATH.'/admin-header.php';
        echo '<div class="wrap">';
        echo   '<h2>'.system_head('title').'<a class="button" href="'.PHP_FILE.'?method=new">'._x('Add New','sort').'</a></h2>';
        echo   '<form action="'.PHP_FILE.'?method=bulk" method="post" name="sortlist" id="sortlist">';
        table_nav();
        echo       '<table class="data-table" cellspacing="0">';
        echo           '<thead>';
        table_thead();
        echo           '</thead>';
        echo           '<tfoot>';
        table_thead();
        echo           '</tfoot>';
        echo           '<tbody>';
        if ($sorts) {
            echo            display_tr_categories($sorts);
        } else {
            echo           '<tr><td colspan="4" class="tc">'.__('No record!').'</td></tr>';
        }
        echo           '</tbody>';
        echo       '</table>';
        table_nav();
        echo   '</form>';
        echo '</div>';
        include ADMIN_PATH.'/admin-footer.php';
        break;
}

/**
 * 批量操作
 *
 */
function table_nav() {
    echo '<div class="table-nav">';
    echo     '<select name="actions">';
    echo         '<option value="">'.__('Bulk Actions').'</option>';
    echo         '<option value="createposts">'.__('Create Posts').'</option>';
    echo         '<option value="createlists">'.__('Create Lists').'</option>';
    echo         '<option value="createall">'.__('Create Posts and Lists').'</option>';
    echo         '<option value="delete">'.__('Delete').'</option>';
    echo     '</select>';
    echo     '<button type="button">'.__('Apply').'</button>';
    echo '</div>';
}
/**
 * 表头
 *
 */
function table_thead() {
    echo '<tr>';
    echo     '<th class="check-column"><input type="checkbox" name="select" value="all" /></th>';
    echo     '<th>'._x('Name','sort').'</th>';
    echo     '<th>'._x('Path','sort').'</th>';
    echo     '<th class="w50">'._x('Posts','sort').'</th>';
    echo '</tr>';
}
/**
 * 显示分类表格树
 *
 * @param array $sorts
 * @param int $n
 * @return string
 */
function display_tr_categories($sorts,$n=0) {
    $func  = __FUNCTION__; $hl = '';
    $space = str_repeat('&mdash; ',$n);
    foreach ($sorts as $sort) {
        $path    = ROOT . $sort['path'] . '/';
        $href    = PHP_FILE.'?method=edit&taxonomyid='.$sort['taxonomyid'];
        $actions = '<span class="create"><a href="javascript:;" onclick="sort_create('.$sort['taxonomyid'].')">'.__('Create List').'</a> | </span>';
        $actions.= '<span class="add_post"><a href="'.ADMIN.'post.php?method=new&category='.$sort['taxonomyid'].'">'._x('Add New','post').'</a> | </span>';
        $actions.= '<span class="edit"><a href="'.$href.'">'.__('Edit').'</a> | </span>';
        $actions.= '<span class="delete"><a href="javascript:;" onclick="sort_delete('.$sort['taxonomyid'].')">'.__('Delete').'</a></span>';
        $hl.= '<tr>';
        $hl.=   '<td class="check-column"><input type="checkbox" name="listids[]" value="'.$sort['taxonomyid'].'" /></td>';
        $hl.=   '<td><span class="space">'.$space.'</span><strong><a href="'.ADMIN.'post.php?category='.$sort['taxonomyid'].'">'.$sort['name'].'</a></strong><br/><div class="row-actions">'.$actions.'</div></td>';
        // 检测目录是否已生成
        if (is_dir(ABS_PATH.'/'.$sort['path'])) {
            $hl.= '<td>'.get_icon('b6').'<a href="'.$path.'" target="_blank">'.$path.'</a></td>';
        } else {
            $hl.= '<td>'.get_icon('b7').'<a href="javascript:;" onclick="sort_create('.$sort['taxonomyid'].')">'.$path.'</a></td>';
        }
        $hl.=   '<td>'.$sort['count'].'</td>';
        $hl.= '</tr>';
        if (isset($sort['subs'])) {
    		$hl.= $func($sort['subs'],$n+1);
    	}
    }
    return $hl;
}

/**
 * 管理页面
 *
 * @param string $action
 */
function category_manage_page($action) {
    $taxonomyid = isset($_GET['taxonomyid']) ? $_GET['taxonomyid'] : 0;
    if ($action != 'add') {
        $_SORT = taxonomy_get($taxonomyid);
    }
    $suffix = C('HTML-Ext');
    $pmodel = model_gets('Post', 'enabled');
    $parent = isset($_SORT['parent']) ? $_SORT['parent'] : null;
    $name   = isset($_SORT['name']) ? $_SORT['name'] : null;
    $mcode  = isset($_SORT['model']) ? $_SORT['model'] : null;
    $model  = $mcode ? model_get_bycode($mcode) : array('langcode'=>'');
    $path   = isset($_SORT['path']) ? $_SORT['path'] : null;
    $list   = isset($_SORT['list']) ? $_SORT['list'] : 'list'.$suffix;
    $page   = isset($_SORT['page']) ? $_SORT['page'] : ($pmodel ? null : 'default' . $suffix);
    $models = model_gets('Category', 'enabled');
    $keywords = isset($_SORT['keywords']) ? post_get_taxonomy($_SORT['keywords'], true) : null;
    $description = isset($_SORT['description']) ? $_SORT['description'] : null;
    echo '<div class="wrap">';
    echo    '<h2>' . system_head('title') . '</h2>';
    echo    '<form action="' . PHP_FILE . '?method=save" method="post" name="sortmanage" id="sortmanage">';
    echo    '<fieldset>';
    echo        '<table class="form-table">';
    echo            '<tbody>';
    echo                '<tr>';
    echo                    '<th><label for="parent">' . _x('Parent', 'sort') . '</label></th>';
    echo                    '<td><select name="parent" id="parent">';
    echo                        '<option value="0" path="">' . __('&mdash; No Parent &mdash;') . '</option>';
    echo                        dropdown_categories($taxonomyid, $parent);
    echo                    '</select></td>';
    echo                '</tr>';
    if ($models) {
        echo            '<tr>';
        echo                '<th><label for="model">' . _x('Model', 'sort') . '</label></th>';
        echo                '<td><select name="model" id="model"' . ($action == 'add' ? ' cookie="true"' : '') . '>';
        echo                    '<option value="">' . __('&mdash; No Model &mdash;') . '</option>';
        foreach ($models as $m) {
            $selected = isset($model['langcode']) && $m['langcode']==$model['langcode']?' selected="selected"':'';
            echo                '<option value="' . $m['langcode'] . '"'.$selected.'>' . $m['name'] . '</option>';
        }
        echo                '</select></td>';
        echo            '</tr>';
    }
    echo                '<tr>';
    echo                    '<th><label for="name">' . _x('Name', 'sort') . '<span class="resume">' . __('(required)') . '</span></label></th>';
    echo                    '<td><input class="text" id="name" name="name" type="text" size="30" value="' . $name . '" /></td>';
    echo                '</tr>';
    echo                '<tr>';
    echo                    '<th><label for="path">' . _x('Path', 'sort') . '<span class="resume">' . __('(required)') . '</span></label></th>';
    echo                    '<td><input class="text" id="path" name="path" type="text" size="70" value="' . $path . '" /><div class="rules">';
    echo                        '<a href="#%ID" title="%ID">[' . __('Category ID') . ']</a>';
    echo                        '<a href="#%MD5" title="%MD5">[' . __('MD5 Value') . ']</a>';
    echo                        '<a href="#%PY" title="%PY">[' . __('Pinyin') . ']</a>';
    echo                    '</div></td>';
    echo                '</tr>';
    echo            '</tbody>';
    echo            '<tbody class="extend-attr"></tbody>';
    echo            '<tbody>';
    echo                '<tr>';
    echo                    '<th><label for="listtemplate">' . __('List Template') . '</label></th>';
    echo                    '<td>';
    echo                        '<select id="listtemplate" name="list">';
    echo                        options(system_themes_path(), C('TPL-Exts'), '<option value="#value#"#selected#>#name#</option>', $list);
    echo                        '</select>';
    echo                    '</td>';
    echo                '</tr>';
    echo                '<tr>';
    echo                    '<th><label for="pagetemplate">' . __('Page Template') . '</label></th>';
    echo                    '<td>';
    echo                        '<select id="pagetemplate" name="page">';
    echo                        $pmodel ? '<option value="">' . __('Use the model set') . '</option>' : null;
    echo                        options(system_themes_path(), C('TPL-Exts'), '<option value="#value#"#selected#>#name#</option>', $page);
    echo                        '</select>';
    echo                    '</td>';
    echo                '</tr>';
    echo            '</tbody>';
    echo        '</table>';
    echo    '</fieldset>';
    echo    '<fieldset cookie="true">';
    echo        '<a href="javascript:;" class="toggle" title="' . __('Click to toggle') . '"><br/></a>';
    echo        '<h3>' . __('More attribute') . '</h3>';
    echo        '<table class="form-table">';
    echo            '<tbody>';
    echo            '<tr>';
    echo                '<th><label for="keywords">' . _x('Keywords', 'sort') . '</label></th>';
    echo                '<td><input class="text" type="text" size="70" name="keywords" id="keywords" value="' . $keywords . '" /></td>';
    echo            '</tr>';
    echo            '<tr>';
    echo                '<th><label for="description">' . _x('Description', 'sort') . '<br /><span class="resume">' . __('(Maximum of 250)') . '</span></label></th>';
    echo                '<td><textarea class="text" cols="70" rows="5" id="description" name="description">' . $description . '</textarea></td>';
    echo            '</tr>';
    echo        '</table>';
    echo    '</fieldset>';

    echo    '<p class="submit">';
    if ($action == 'add') {
        echo    '<button type="submit">' . __('Add Category') . '</button>';
    } else {
        echo    '<button type="submit">' . __('Update Category') . '</button><input type="hidden" name="taxonomyid" value="' . $taxonomyid . '" />';
    }
    echo        '<button type="button" onclick="LazyCMS.redirect(\'' . PHP_FILE . '\')">' . __('Back') . '</button>';
    echo    '</p>';
    echo    '</form>';
    echo '</div>';
}
/**
 * 显示分类树
 *
 * @param int $taxonomyid   当前分类ID
 * @param int $selected 被选择的分类ID
 * @param array $trees
 * @return string
 */
function dropdown_categories($taxonomyid,$selected=0,$trees=null) {
    static $n = 0; $func = __FUNCTION__;
    if ($trees===null) $trees = taxonomy_get_trees();
    $hl = ''; $space = str_repeat('&nbsp; &nbsp; ',$n); $n++;
    foreach ($trees as $tree) {
        $sel  = $selected==$tree['taxonomyid']?' selected="selected"':null;
        if ($taxonomyid==$tree['taxonomyid']) {
            $hl.= '<optgroup label="'.$space.'├ '.$tree['name'].'"></optgroup>';
        } else {
            $hl.= '<option value="'.$tree['taxonomyid'].'"'.$sel.' path="'.$tree['path'].'/">'.$space.'├ '.$tree['name'].'</option>';
        }
    	if (isset($tree['subs'])) {
    		$hl.= $func($taxonomyid,$selected,$tree['subs']);
    	}
    }
    return $hl;
}