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
admin_head('title',  _('Categories'));
admin_head('styles', array('css/categories'));
admin_head('scripts',array('js/categories'));
// 动作
$action  = isset($_REQUEST['action'])?$_REQUEST['action']:null;
// 所属
$parent_file = 'categories.php';
// 权限检查
current_user_can('categories');

switch ($action) {
    // 添加
    case 'new':
	    // 重置标题
	    admin_head('title',_('Add New Category'));
	    // 添加JS事件
	    admin_head('loadevents','sort_manage_init');
	    include ADMIN_PATH.'/admin-header.php';
        // 显示页面
	    category_manage_page('add');
        include ADMIN_PATH.'/admin-footer.php';
        break;
    // 编辑
	case 'edit':
	    // 重置标题
	    admin_head('title',_('Edit Category'));
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
        	admin_success(_('Category deleted.'),"LazyCMS.redirect('".PHP_FILE."');");
        } else {
            admin_error(_('Category delete fail.'));
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
            $type   = isset($_POST['type'])?$_POST['type']:array();
            $list   = isset($_POST['list'])?$_POST['list']:null;
            $page   = isset($_POST['page'])?$_POST['page']:null;

            $validate->check(array(
                array('name',VALIDATE_EMPTY,__('The name field is empty.','sort')),
                array('name',VALIDATE_LENGTH,__('The name field length must be %d-%d characters.','sort'),1,30),
            ));
            
            $validate->check(array(
                array('path',VALIDATE_EMPTY,__('The path field is empty.','sort')),
            ));

            // 验证通过
            if (!$validate->is_error()) {
                // 编辑
                if ($taxonomyid) {
                    $info = array(
                        'parent' => esc_html($parent),
                        'name'   => esc_html($name),
                        'path'   => esc_html($path),
                        'model'  => esc_html($type),
                        'list'   => esc_html($list),
                        'page'   => esc_html($page),
                    );
                    LCTaxonomy::editTaxonomy($taxonomyid,$info);
                    // 保存用户信息
                    admin_success(_('Category updated.'),"LazyCMS.redirect('".PHP_FILE."');");
                } 
                // 添加
                else {
                    $path   = esc_html($path);
                    $parent = esc_html($parent);
                    LCTaxonomy::addTaxonomy($parent,'category',array(
                        'name'  => esc_html($name),
                        'path'  => esc_html($path),
                        'model' => esc_html($type),
                        'list'  => esc_html($list),
                        'page'  => esc_html($page),
                    ));
                    // 保存用户信息
                    admin_success(_('Category created.'),"LazyCMS.redirect('".PHP_FILE."');");
                }
            }
        }
	    break;
	// 批量动作
	case 'bulk':
	    $actions = isset($_POST['actions'])?$_POST['actions']:null;
	    $listids = isset($_POST['listids'])?$_POST['listids']:null;
	    if (empty($listids)) {
	    	admin_error(_('Did not select any item.'));
	    }
	    switch ($actions) {
	        // 删除
	        case 'delete':
	            foreach ($listids as $taxonomyid) {
	            	LCTaxonomy::deleteTaxonomyById($taxonomyid);
	            }
	            admin_success(_('Categories deleted.'),"LazyCMS.redirect('".PHP_FILE."');"); 
	            break;
	    }
	    break;
    default:
	    admin_head('loadevents','sort_list_init');
	    $sorts = LCTaxonomy::getTaxonomysTree();
        include ADMIN_PATH.'/admin-header.php';
        echo '<div class="wrap">';
        echo   '<h2>'.admin_head('title').'<a class="btn" href="'.PHP_FILE.'?action=new">'.__('Add New','sort').'</a></h2>';
        echo   '<form action="'.PHP_FILE.'?action=bulk" method="post" name="sortlist" id="sortlist">';
        actions();
        echo       '<table class="data-table" cellspacing="0">';
        echo           '<thead>';
        thead();
        echo           '</thead>';
        echo           '<tfoot>';
        thead();
        echo           '</tfoot>';
        echo           '<tbody>';
        if ($sorts) {
            echo            display_tr_tree($sorts);
        } else {
            echo           '<tr><td colspan="5" class="tc">'._('No record!').'</td></tr>';
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
    echo         '<option value="create">'._('Create').'</option>';
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
    echo     '<th>'.__('Name','sort').'</th>';
    echo     '<th>'.__('Path','sort').'</th>';
    echo     '<th class="w150">'.__('Type','sort').'</th>';
    echo     '<th class="w50">'.__('Posts','sort').'</th>';
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
        $actions = '<span class="create"><a href="javascript:;">'._('Create').'</a> | </span>';
        $actions.= '<span class="edit"><a href="'.$href.'">'._('Edit').'</a> | </span>';
        $actions.= '<span class="delete"><a href="'.PHP_FILE.'?action=delete&taxonomyid='.$sort['taxonomyid'].'">'._('Delete').'</a></span>';
        $models  = array(); foreach ($sort['model'] as $code) $models[] = LCModel::getModelByCode($code,'name');
        $hl.= '<tr>';
        $hl.=   '<td class="check-column"><input type="checkbox" name="listids[]" value="'.$sort['taxonomyid'].'" /></td>';
        $hl.=   '<td><span class="space">'.$space.'</span><strong><a href="'.$href.'">'.$sort['name'].'</a></strong><br/><div class="row-actions">'.$actions.'</div></td>';
        $hl.=   '<td><a href="'.$path.'" target="_blank">'.$path.'</a></td>';
        $hl.=   '<td>'.implode(',',$models).'</td>';
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
    $parent = isset($_SORT['parent'])?$_SORT['parent']:null;
    $name   = isset($_SORT['name'])?$_SORT['name']:null;
    $path   = isset($_SORT['path'])?$_SORT['path']:null;
    $model  = isset($_SORT['model'])?$_SORT['model']:array();
    $list   = isset($_SORT['list'])?$_SORT['list']:null;
    $page   = isset($_SORT['page'])?$_SORT['page']:null;
    $modules = LCModel::getModels(1);
    echo '<div class="wrap">';
    echo   '<h2>'.admin_head('title').'</h2>';
    echo   '<form action="'.PHP_FILE.'?action=save" method="post" name="sortmanage" id="sortmanage">';
    echo     '<fieldset>';
    echo       '<table class="form-table">';
    echo           '<tr>';
    echo               '<th><label for="parent">'.__('Parent','sort').'</label></th>';
    echo               '<td><select name="parent" id="parent">';
    echo                   '<option value="0" path="" model="">--- '._('None').' ---</option>';
    echo                    display_option_tree($taxonomyid,$parent);
    echo               '</select></td>';
    echo           '</tr>';
    echo           '<tr>';
    echo               '<th><label for="name">'.__('Name','sort').' <span class="description">'._('(required)').'</span></label></th>';
    echo               '<td><input id="name" name="name" type="text" size="30" value="'.$name.'" /></td>';
    echo           '</tr>';
    echo           '<tr>';
    echo               '<th><label for="path">'.__('Path','sort').' <span class="description">'._('(required)').'</span></label></th>';
    echo               '<td><input id="path" name="path" type="text" size="70" value="'.$path.'" /><div class="rules">';
    echo                   '<a href="#%ID">['._('Category ID').']</a>';
    echo                   '<a href="#%MD5">['._('MD5 Value').']</a>';
    echo                   '<a href="#%PY">['._('Pinyin').']</a>';
    echo                   '<a href="#%Y">['.strftime('%Y').']</a>';
    echo                   '<a href="#%m">['.strftime('%m').']</a>';
    echo                   '<a href="#%d">['.strftime('%d').']</a>';
    echo                   '<a href="#%a">['.strftime('%a').']</a>';
    echo               '</div></td>';
    echo           '</tr>';
    if ($modules) {
        echo       '<tr>';
        echo           '<th><label>'.__('Type','sort').'</label></th>';
        echo           '<td>';
        foreach ($modules as $module) {
            $checked = in_array($module['code'],$model)?' checked="checked"':null;
        	echo           '<label><input type="checkbox" name="type[]" value="'.$module['code'].'"'.$checked.' /> '.$module['name'].'</label>';
        }
        echo           '</td>';
        echo       '</tr>';
    }
    echo           '<tr>';
    echo               '<th><label for="listtemplate">'._('List Template').'</label></th>';
    echo               '<td>';
    echo                   '<select id="listtemplate" name="list">';
    echo                       $modules?'<option value="">'._('Use model setting').'</option>':null;
    echo                       options(C('Template'),C('TemplateExts'),'<option value="#value#"#selected#>#name#</option>',$list);
    echo                   '</select>';
    echo               '</td>';
    echo           '</tr>';
    echo           '<tr>';
    echo               '<th><label for="pagetemplate">'._('Page Template').'</label></th>';
    echo               '<td>';
    echo                   '<select id="pagetemplate" name="page">';
    echo                       $modules?'<option value="">'._('Use model setting').'</option>':null;
    echo                       options(C('Template'),C('TemplateExts'),'<option value="#value#"#selected#>#name#</option>',$page);
    echo                   '</select>';
    echo               '</td>';
    echo           '</tr>';
    echo       '</table>';
    echo     '</fieldset>';
    if ($action=='add') {
        echo   '<p class="submit"><button type="submit">'._('Add Category').'</button> <button type="button" onclick="self.location.replace(\''.$referer.'\')">'._('Back').'</button></p>';
    } else {
        echo   '<input type="hidden" name="taxonomyid" value="'.$taxonomyid.'" />';
        echo   '<p class="submit"><button type="submit">'._('Update Category').'</button> <button type="button" onclick="self.location.replace(\''.$referer.'\')">'._('Back').'</button></p>';
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
function display_option_tree($taxonomyid,$selected=0,$n=0,$trees=null) {
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
            $hl.= '<option value="'.$tree['taxonomyid'].'"'.$sel.' path="'.$path.'" model="'.implode(',',$tree['model']).'">'.$space.'├ '.$tree['name'].'</option>';
        }
    	if (isset($tree['subs'])) {
    		$hl.= $func($taxonomyid,$selected,$n+1,$tree['subs']);
    	}
    }
    return $hl;
}