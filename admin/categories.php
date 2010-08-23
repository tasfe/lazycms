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
// 接客了！。。。
require dirname(__FILE__).'/admin.php';
// 得到客人信息
$_ADMIN = LCUser::current();
// 标题
admin_head('title',  __('Categories'));
admin_head('styles', array('css/categories'));
admin_head('scripts',array('js/categories'));
// 姿势
$action  = isset($_REQUEST['action'])?$_REQUEST['action']:null;
// 所属
$parent_file = 'categories.php';
// 权限检查
current_user_can('categories');

switch ($action) {
    // 强力插入
    case 'new':
	    // 重置标题
	    admin_head('title',__('Add New Category'));
	    // 添加JS事件
	    admin_head('loadevents','sort_manage_init');
	    include ADMIN_PATH.'/admin-header.php';
        // 显示页面
	    category_manage_page('add');
        include ADMIN_PATH.'/admin-footer.php';
        break;
    // 活塞式运动，你懂得。。。
	case 'edit':
	    // 重置标题
	    admin_head('title',__('Edit Category'));
	    // 添加JS事件
	    admin_head('loadevents','sort_manage_init');
	    include ADMIN_PATH.'/admin-header.php';
	    category_manage_page('edit');
        include ADMIN_PATH.'/admin-footer.php';
	    break;
	// 删除
    case 'delete':
        $taxonomyid = isset($_GET['taxonomyid'])?$_GET['taxonomyid']:null;
        if (LCTaxonomy::deleteTaxonomyById($taxonomyid)) {
        	admin_success(__('Category deleted.'),"LazyCMS.redirect('".PHP_FILE."');");
        } else {
            admin_error(__('Category delete fail.'));
        }
        break;
    // 保存
	case 'save':
        $taxonomyid  = isset($_POST['taxonomyid'])?$_POST['taxonomyid']:0;
	    $validate = new Validate();
        if ($validate->post()) {
            $parent = isset($_POST['parent'])?$_POST['parent']:'0';
            $name   = isset($_POST['name'])?$_POST['name']:null;
            $path   = isset($_POST['path'])?$_POST['path']:null;
            $list   = isset($_POST['list'])?$_POST['list']:null;

            $validate->check(array(
                array('name',VALIDATE_EMPTY,_x('The name field is empty.','sort')),
                array('name',VALIDATE_LENGTH,_x('The name field length must be %d-%d characters.','sort'),1,30),
            ));
            
            $validate->check(array(
                array('path',VALIDATE_EMPTY,_x('The path field is empty.','sort')),
            ));

            // 安全有保证，做爱做的事吧！
            if (!$validate->is_error()) {
                // 编辑
                if ($taxonomyid) {
                    $info = array(
                        'parent' => esc_html($parent),
                        'name'   => esc_html($name),
                        'path'   => esc_html($path),
                        'list'   => esc_html($list),
                    );
                    LCTaxonomy::editTaxonomy($taxonomyid,$info);
                    admin_success(__('Category updated.'),"LazyCMS.redirect('".PHP_FILE."');");
                } 
                // 强力插入了
                else {
                    $path   = esc_html($path);
                    $parent = esc_html($parent);
                    $name   = esc_html($name);
                    LCTaxonomy::addTaxonomy('category',$name,$parent,array(
                        'path'  => esc_html($path),
                        'list'  => esc_html($list),
                    ));
                    admin_success(__('Category created.'),"LazyCMS.redirect('".PHP_FILE."');");
                }
            }
        }
	    break;
	// 批量动作
	case 'bulk':
	    $actions = isset($_POST['actions'])?$_POST['actions']:null;
	    $listids = isset($_POST['listids'])?$_POST['listids']:null;
	    if (empty($listids)) {
	    	admin_error(__('Did not select any item.'));
	    }
	    switch ($actions) {
	        // 删除
	        case 'delete':
	            foreach ($listids as $taxonomyid) {
	            	LCTaxonomy::deleteTaxonomyById($taxonomyid);
	            }
	            admin_success(__('Categories deleted.'),"LazyCMS.redirect('".PHP_FILE."');");
	            break;
            default:
                admin_alert(__('Parameter is invalid.'));
                break;
	    }
	    break;
    default:
	    admin_head('loadevents','sort_list_init');
	    $sorts = LCTaxonomy::getTaxonomysTree();
        include ADMIN_PATH.'/admin-header.php';
        echo '<div class="wrap">';
        echo   '<h2>'.admin_head('title').'<a class="btn" href="'.PHP_FILE.'?action=new">'._x('Add New','sort').'</a></h2>';
        echo   '<form action="'.PHP_FILE.'?action=bulk" method="post" name="sortlist" id="sortlist">';
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
            echo            display_tr_tree($sorts);
        } else {
            echo           '<tr><td colspan="5" class="tc">'.__('No record!').'</td></tr>';
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
    echo         '<option value="create">'.__('Create').'</option>';
    echo         '<option value="recreate">'.__('Recreate').'</option>';
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
function display_tr_tree($sorts,$n=0) {
    static $func = null; if (!$func) $func = __FUNCTION__; 
    $hl = ''; $space = str_repeat('&mdash; ',$n);
    foreach ($sorts as $sort) {
        $path = WEB_ROOT.format_path($sort['path'],array(
            'ID'  => $sort['taxonomyid'],
            'PY'  => $sort['name'],
            'MD5' => $sort['taxonomyid'],
        ));
        $href    = PHP_FILE.'?action=edit&taxonomyid='.$sort['taxonomyid'];
        $actions = '<span class="create"><a href="javascript:;">'.__('Create').'</a> | </span>';
        $actions.= '<span class="edit"><a href="'.$href.'">'.__('Edit').'</a> | </span>';
        $actions.= '<span class="delete"><a href="'.PHP_FILE.'?action=delete&taxonomyid='.$sort['taxonomyid'].'">'.__('Delete').'</a></span>';
        $hl.= '<tr>';
        $hl.=   '<td class="check-column"><input type="checkbox" name="listids[]" value="'.$sort['taxonomyid'].'" /></td>';
        $hl.=   '<td><span class="space">'.$space.'</span><strong><a href="'.$href.'">'.$sort['name'].'</a></strong><br/><div class="row-actions">'.$actions.'</div></td>';
        $hl.=   '<td><a href="'.$path.'" target="_blank">'.$path.'</a></td>';
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
    $referer = referer(PHP_FILE);
    $taxonomyid  = isset($_GET['taxonomyid'])?$_GET['taxonomyid']:0;
    if ($action!='add') {
    	$_SORT  = LCTaxonomy::getTaxonomyById($taxonomyid);
    }
    $parent  = isset($_SORT['parent'])?$_SORT['parent']:null;
    $name    = isset($_SORT['name'])?$_SORT['name']:null;
    $path    = isset($_SORT['path'])?$_SORT['path']:null;
    $list    = isset($_SORT['list'])?$_SORT['list']:null;
    $keywords = isset($_SORT['keywords'])?$_SORT['keywords']:null;
    $description = isset($_SORT['description'])?$_SORT['description']:null;
    $modules = LCModel::getModels(1);
    echo '<div class="wrap">';
    echo   '<h2>'.admin_head('title').'</h2>';
    echo   '<form action="'.PHP_FILE.'?action=save" method="post" name="sortmanage" id="sortmanage">';
    echo     '<fieldset>';
    echo       '<table class="form-table">';
    echo           '<tr>';
    echo               '<th><label for="parent">'._x('Parent','sort').'</label></th>';
    echo               '<td><select name="parent" id="parent">';
    echo                   '<option value="0" path="" model="">--- '.__('None').' ---</option>';
    echo                    options_tree($taxonomyid,$parent);
    echo               '</select></td>';
    echo           '</tr>';
    echo           '<tr>';
    echo               '<th><label for="name">'._x('Name','sort').'<span class="description">'.__('(required)').'</span></label></th>';
    echo               '<td><input id="name" name="name" type="text" size="30" value="'.$name.'" /></td>';
    echo           '</tr>';
    echo           '<tr>';
    echo               '<th><label for="path">'._x('Path','sort').'<span class="description">'.__('(required)').'</span></label></th>';
    echo               '<td><input id="path" name="path" type="text" size="70" value="'.$path.'" /><div class="rules">';
    echo                   '<a href="#%ID">['.__('Category ID').']</a>';
    echo                   '<a href="#%MD5">['.__('MD5 Value').']</a>';
    echo                   '<a href="#%PY">['.__('Pinyin').']</a>';
    echo                   '<a href="#%Y">['.strftime('%Y').']</a>';
    echo                   '<a href="#%m">['.strftime('%m').']</a>';
    echo                   '<a href="#%d">['.strftime('%d').']</a>';
    echo                   '<a href="#%a">['.strftime('%a').']</a>';
    echo               '</div></td>';
    echo           '</tr>';
    echo           '<tr>';
    echo               '<th><label for="listtemplate">'.__('List Template').'</label></th>';
    echo               '<td>';
    echo                   '<select id="listtemplate" name="list">';
    echo                       $modules?'<option value="">'.__('Use the model set').'</option>':null;
    echo                       options(C('Template'),C('TemplateSuffixs'),'<option value="#value#"#selected#>#name#</option>',$list);
    echo                   '</select>';
    echo               '</td>';
    echo           '</tr>';
    echo           '<tr>';
    echo               '<th><label for="keywords">'._x('Keywords','sort').'</label></th>';
    echo               '<td><input type="text" size="70" name="keywords" id="keywords" value="'.$keywords.'" /></td>';
    echo           '</tr>';
    echo           '<tr>';
    echo               '<th><label for="description">'._x('Description','sort').'</label></th>';
    echo               '<td><textarea cols="70" rows="5" id="description" name="description">'.$description.'</textarea></td>';
    echo           '</tr>'; 
    echo       '</table>';
    echo     '</fieldset>';
    if ($action=='add') {
        echo   '<p class="submit"><button type="submit">'.__('Add Category').'</button> <button type="button" onclick="self.location.replace(\''.$referer.'\')">'.__('Back').'</button></p>';
    } else {
        echo   '<input type="hidden" name="taxonomyid" value="'.$taxonomyid.'" />';
        echo   '<p class="submit"><button type="submit">'.__('Update Category').'</button> <button type="button" onclick="self.location.replace(\''.$referer.'\')">'.__('Back').'</button></p>';
    }
    echo   '</form>';
    echo '</div>';
}
/**
 * 显示分类树
 *
 * @param int $taxonomyid   当前分类ID
 * @param int $selected 被选择的分类ID
 * @param int $n
 * @param array $trees
 * @return string
 */
function options_tree($taxonomyid,$selected=0,$n=0,$trees=null) {
    static $func = null; if (!$func) $func = __FUNCTION__;
    if ($trees===null) $trees = LCTaxonomy::getTaxonomysTree();
    $hl = ''; $space = str_repeat('&nbsp; &nbsp; ',$n);
    foreach ($trees as $tree) {
        $sel  = $selected==$tree['taxonomyid']?' selected="selected"':null;
        $path = format_path($tree['path'],array(
            'ID'  => $tree['taxonomyid'],
            'PY'  => $tree['name'],
            'MD5' => $tree['taxonomyid'],
        ));
        if ($taxonomyid==$tree['taxonomyid']) {
            $hl.= '<optgroup label="'.$space.'├ '.$tree['name'].'"></optgroup>';
        } else {
            $hl.= '<option value="'.$tree['taxonomyid'].'"'.$sel.' path="'.$path.'">'.$space.'├ '.$tree['name'].'</option>';
        }
    	if (isset($tree['subs'])) {
    		$hl.= $func($taxonomyid,$selected,$n+1,$tree['subs']);
    	}
    }
    return $hl;
}