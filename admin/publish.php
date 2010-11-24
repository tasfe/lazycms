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
$_USER = user_current();
// 标题
system_head('title',__('Publish Posts'));
system_head('styles', array('css/publish'));
system_head('scripts',array('js/publish'));
// 动作
$method  = isset($_REQUEST['method'])?$_REQUEST['method']:null;
// 所属
$parent_file = 'publish.php';
// 权限检查
current_user_can('publish');

switch ($method) {
    // 发布进程管理
    case 'list':
        system_head('title',__('Process list'));
        system_head('loadevents','publish_list');
        $result = pages_query("SELECT * FROM `#@_publish` ORDER BY `pubid` DESC");
        include ADMIN_PATH.'/admin-header.php';
        echo '<div class="wrap">';
        echo   '<h2>'.system_head('title').'<a class="button" href="'.PHP_FILE.'">'._x('Add New','publish').'</a></h2>';
        echo   '<form action="'.PHP_FILE.'?method=bulk" method="post" name="publishlist" id="publishlist">';
        table_nav();
        echo       '<table class="data-table" cellspacing="0">';
        echo           '<thead>';
        table_thead();
        echo           '</thead>';
        echo           '<tfoot>';
        table_thead();
        echo           '</tfoot>';
        echo           '<tbody>';
        if ($result) {
            while ($data = pages_fetch($result)) {
                $pubid = $data['pubid'];
                if ($data['total']<=0 && $data['state']==2) {
                    $rate = 100;
                } else {
                    $rate = $data['total']<=0 ? 0 : floor($data['complete'] / $data['total'] * 100);
                }

                $actions = '<span class="delete"><a href="javascript:;" onclick="publish_delete('.$pubid.')">'.__('Delete').'</a></span>';
                echo       '<tr id="publish-'.$pubid.'">';
                echo           '<td class="check-column"><input type="checkbox" name="listids[]" value="'.$pubid.'" /></td>';
                echo           '<td><strong>'.$data['name'].'</strong><br/><div class="row-actions">'.$actions.'</div></td>';
                echo           '<td>'.number_format($data['total']).'</td>';
                echo           '<td>'.number_format($data['complete']).'</td>';
                echo           '<td class="w150"><div class="rate"><div class="inner" style="width:'.$rate.'px"></div><div class="text">'.$rate.'%</div></div></td>';
                echo           '<td>'.time_format('%H:%i:%s,%ms',$data['elapsetime']).'</td>';
                echo           '<td>'.get_icon('c'.($data['state']+1)).'</td>';
                echo       '</tr>';
            }
        } else {
            echo           '<tr><td colspan="8" class="tc">'.__('No record!').'</td></tr>';
        }
        echo           '</tbody>';
        echo       '</table>';
        table_nav();
        echo   '</form>';
        echo '</div>';
        include ADMIN_PATH.'/admin-footer.php';
        break;
    // 批量动作
	case 'bulk':
	    $action  = isset($_POST['action'])?$_POST['action']:null;
	    $listids = isset($_POST['listids'])?$_POST['listids']:null;
	    if (empty($listids)) {
	    	ajax_error(__('Did not select any item.'));
	    }
	    switch ($action) {
            case 'delete':
                publish_delete($listids);
	            ajax_success(__('Process deleted.'),"LazyCMS.redirect('".referer()."');");
	            break;
            default:
                ajax_alert(__('Parameter is invalid.'));
                break;
	    }
	    break;
    // 保存进程
    case 'save':
        $actions  = isset($_POST['action']) ? $_POST['action'] : null;
        $category = isset($_POST['category']) ? $_POST['category'] : null;
        $option   = isset($_POST['option']) ? $_POST['option'] : 'all';
        if (empty($actions) && empty($category)) {
	    	ajax_error(__('Did not select any item.'));
	    }
        // 添加列表生成
        if ($category) {
            $names = taxonomy_get_names($category);
            // 生成列表和文章
            if ($option == 'all') {
                publish_add(sprintf(__('Create Lists and Posts:%s'),$names),'publish_lists',array($category,true));
            }
            // 只生成列表
            elseif ($option == 'lists') {
                publish_add(sprintf(__('Create Lists:%s'),$names),'publish_lists',array($category,false));
            }
            // 只生成文章
            elseif ($option == 'posts') {
                publish_add(sprintf(__('Create Posts:%s'),$names),'publish_posts',array($category));
            }
        }
        // 添加生成所列表进程
        if (instr('createlists',$actions)) {
            publish_add(__('Create all Lists'),'publish_lists');
        }
        // 添加生成所有文章进程
        if (instr('createposts',$actions)) {
            publish_add(__('Create all Posts'),'publish_posts',array('posts'));
        }
        // 添加生成所有页面进程
        if (instr('createpages',$actions)) {
            publish_add(__('Create all Pages'),'publish_posts',array('pages'));
        }
        ajax_success(__('Publish process successfully created.'),"LazyCMS.redirect('".PHP_FILE."?method=list');");
        break;
    // 发布页面
    default:
        $referer    = referer(PHP_FILE);
        $categories = taxonomy_get_trees();
        system_head('loadevents','publish_init');
        include ADMIN_PATH.'/admin-header.php';
        echo '<div class="wrap">';
        echo   '<h2>'.system_head('title').'<a class="button" href="'.PHP_FILE.'?method=list">'.__('Process list').'</a></h2>';
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
        echo                    '<label for="createlists"><input type="checkbox" name="action[]" value="createlists" id="createlists">'.__('Create all Lists').'</label>';
        echo                '</td>';
        echo           '</tr>';
        if ($categories) {
            echo       '<tr>';
            echo            '<td><strong>'.__('Select you want to publish the category:').'</strong></td>';
            echo       '</tr>';
            echo       '<tr>';
            echo            '<td>';
            echo                display_ul_categories($categories);
            echo                '<div class="option">';
            echo                    '<label for="radio_all"><input type="radio" name="option" value="all" id="radio_all" checked="checked">'.__('Create Lists and Posts').'</label>';
            echo                    '<label for="radio_lists"><input type="radio" name="option" value="lists" id="radio_lists">'.__('Only Create lists').'</label>';
            echo                    '<label for="radio_posts"><input type="radio" name="option" value="posts" id="radio_posts">'.__('Only Create posts').'</label>';
            echo                '</div>';
            echo                '<div class="buttons">';
            echo                    '<button type="button" rel="select">'.__('Select / Deselect').'</button>';
            echo                '</div>';
            echo            '</td>';
            echo       '</tr>';
        }
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
    // 分页地址
    $page_url = PHP_FILE.'?'.http_build_query(array(
        'method' => 'list',
        'page'   => '$',
    ));
    echo '<div class="table-nav">';
    echo     '<select name="actions">';
    echo         '<option value="">'.__('Bulk Actions').'</option>';
    echo         '<option value="delete">'.__('Delete').'</option>';
    echo     '</select>';
    echo     '<button type="button">'.__('Apply').'</button>';
    echo     pages_list($page_url);
    echo '</div>';
}
/**
 * 表头
 *
 */
function table_thead() {
    echo '<tr>';
    echo     '<th class="check-column"><input type="checkbox" name="select" value="all" /></th>';
    echo     '<th>'._x('Name','publish').'</th>';
    echo     '<th>'._x('Total','publish').'</th>';
    echo     '<th>'._x('Complete','publish').'</th>';
    echo     '<th>'._x('Rate','publish').'</th>';
    echo     '<th>'.__('Elapsed time').'</th>';
    echo     '<th>'._x('State','publish').'</th>';
    echo '</tr>';
}

/**
 * 分类列表
 *
 * @param  $trees
 * @return string
 */
function display_ul_categories($trees=null) {
    static $func = null;
    $hl = sprintf('<ul class="%s">',is_null($func) ? 'categories' : 'children');
    if (!$func) $func = __FUNCTION__;
    if ($trees === null) $trees = taxonomy_get_trees();
    foreach ($trees as $i=>$tree) {
        $hl.= sprintf('<li><label class="selectit" for="category-%d">',$tree['taxonomyid']);
        $hl.= sprintf('<input type="checkbox" id="category-%1$d" name="category[]" value="%1$d" />%2$s<em>(%3$d)</em></label>',$tree['taxonomyid'],$tree['name'],$tree['count']);
    	if (isset($tree['subs'])) {
    		$hl.= $func($tree['subs']);
    	}
        $hl.= '</li>';
    }
    $hl.= '</ul>';
    return $hl;
}
