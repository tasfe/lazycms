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
 * | Copyright (C) 2007-2008 LazyCMS.com All rights reserved.                  |
 * +---------------------------------------------------------------------------+
 * | LazyCMS is free software. This version use Apache License 2.0             |
 * | See LICENSE.txt for copyright notices and details.                        |
 * +---------------------------------------------------------------------------+
 */
// 加载公共文件
require dirname(__FILE__).'/admin.php';
// 查询管理员信息
$_ADMIN = user_current();
// 标题
admin_head('title',__('Publish Posts'));
admin_head('styles', array('css/publish'));
admin_head('scripts',array('js/publish'));
// 动作
$method  = isset($_REQUEST['method'])?$_REQUEST['method']:null;
// 所属
$parent_file = 'publish.php';
// 权限检查
current_user_can('publish');

switch ($method) {
    // 发布进程管理
    case 'list':
        admin_head('title',__('Process list'));
        
        include ADMIN_PATH.'/admin-header.php';
        echo '<div class="wrap">';
        echo   '<h2>'.admin_head('title').'<a class="button" href="'.PHP_FILE.'?method=new">'._x('Add New','publish').'</a></h2>';
        echo   '<form action="'.PHP_FILE.'?method=bulk" method="post" name="publish" id="publish">';
        table_nav();
        echo       '<table class="data-table" cellspacing="0">';
        echo           '<thead>';
        table_thead();
        echo           '</thead>';
        echo           '<tfoot>';
        table_thead();
        echo           '</tfoot>';
        echo           '<tbody>';
        echo           '<tr><td colspan="8" class="tc">'.__('No record!').'</td></tr>';
        echo           '</tbody>';
        echo       '</table>';
        table_nav();
        echo   '</form>';
        echo '</div>';
        include ADMIN_PATH.'/admin-footer.php';
        break;
    // 保存进程
    case 'save':
        $actions  = isset($_POST['action']) ? $_POST['action'] : null;
        $category = isset($_POST['category']) ? $_POST['category'] : null;
        if (empty($actions) && empty($category)) {
	    	admin_error(__('Did not select any item.'));
	    }
        // 添加生成所有页面进程
        if (instr('createpages',$actions)) {
            print_r($actions);
        }
        // 添加生成所有文章进程
        if (instr('createposts',$actions)) {
            print_r($actions);
        }
        // 添加生成所有文章和列表进程
        if (instr('createlists',$actions)) {
            print_r($actions);
        }
        break;
    // 发布页面
    default:
        $referer = referer(PHP_FILE);
        admin_head('loadevents','publish_init');
        include ADMIN_PATH.'/admin-header.php';
        echo '<div class="wrap">';
        echo   '<h2>'.admin_head('title').'<a class="button" href="'.PHP_FILE.'?method=list">'.__('Process list').'</a></h2>';
        echo   '<form action="'.PHP_FILE.'?method=save" method="post" name="publish" id="publish">';
        echo     '<fieldset>';
        echo       '<table class="form-table">';
        echo           '<tr>';
        echo                '<td><strong>'.__('Select you want to publish the items:').'</strong></td>';
        echo           '</tr>';
        echo           '<tr>';
        echo                '<td>';
        echo                    '<label for="createpages"><input type="checkbox" name="action[]" value="createpages" id="createpages">'.__('Create all Pages').'</label>';
        echo                    '<label for="createposts"><input type="checkbox" name="action[]" value="createposts" id="createposts">'.__('Create all Posts').'</label>';
        echo                    '<label for="createlists"><input type="checkbox" name="action[]" value="createlists" id="createlists">'.__('Create all Posts and Lists').'</label>';
        echo                '</td>';
        echo           '</tr>';
        echo           '<tr>';
        echo                '<td><strong>'.__('Select you want to publish the category:').'</strong></td>';
        echo           '</tr>';
        echo           '<tr>';
        echo                '<td>'.categories_tree().'</td>';
        echo           '</tr>';
        echo           '<tr>';
        echo                '<td>';
        echo                    '<label for="checkall"><input type="checkbox" name="select" value="all" id="checkall">'.__('Select / Deselect').'</label>';
        echo                '</td>';
        echo           '</tr>';
        echo       '</table>';
        echo     '</fieldset>';
        echo   '<p class="submit"><button type="submit">'._x('Add New','publish').'</button> <button type="button" onclick="LazyCMS.redirect(\''.$referer.'\')">'.__('Back').'</button></p>';
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
    echo         '<option value="stop">'.__('Stop').'</option>';
    echo         '<option value="start">'.__('Start').'</option>';
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
    echo     '<th>'._x('Type','publish').'</th>';
    echo     '<th>'._x('Total','publish').'</th>';
    echo     '<th>'._x('Complete','publish').'</th>';
    echo     '<th>'.__('Begin Time').'</th>';
    echo     '<th>'.__('Elapsed time').'</th>';
    echo     '<th>'.__('End Time').'</th>';
    echo     '<th>'._x('State','publish').'</th>';
    echo '</tr>';
}

/**
 * 分类列表
 *
 * @param  $trees
 * @return string
 */
function categories_tree($trees=null) {
    static $func = null; if (!$func) $func = __FUNCTION__;
    $hl = sprintf('<ul class="%s">',is_null($trees) ? 'categories' : 'children');
    if ($trees === null) $trees = taxonomy_get_trees();
    foreach ($trees as $i=>$tree) {
        $hl.= sprintf('<li><label class="selectit" for="category-%d">',$tree['taxonomyid']);
        $hl.= sprintf('<input type="checkbox" id="category-%d" name="category[]" value="%d" />%s</label>',$tree['taxonomyid'],$tree['taxonomyid'],$tree['name']);
    	if (isset($tree['subs'])) {
    		$hl.= $func($tree['subs']);
    	}
        $hl.= '</li>';
    }
    $hl.= '</ul>';
    return $hl;
}
