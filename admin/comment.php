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
// 设置标题
admin_head('title',__('Comments'));
admin_head('scripts',array('js/comment'));

// 方法
$method = isset($_REQUEST['method'])?$_REQUEST['method']:null;

switch ($method) {
    default:
        admin_head('loadevents','comment_list_init');
        $query  = array('page' => '$');
        $result = pages_query("SELECT * FROM `#@_comments` ORDER BY `commentid` DESC");
        // 分页地址
        $page_url = PHP_FILE.'?'.http_build_query($query);
        // 加载头部
        include ADMIN_PATH.'/admin-header.php';
        echo '<div class="wrap">';
        echo   '<h2>'.admin_head('title').'<a class="button" href="'.PHP_FILE.'?method=new">'._x('Add New','comment').'</a></h2>';
        echo   '<form action="'.PHP_FILE.'?method=bulk" method="post" name="comments" id="comments">';
        table_nav($page_url);
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
                $data['author']  = $data['author'] ? $data['author'] : __('Anonymous');
                $post = post_get($data['postid']);
                $actions = '<span class="unapprove"><a href="javascript:;" onclick="">'.__('Unapprove').'</a> | </span>';
                $actions.= '<span class="reply"><a href="javascript:;">'.__('Reply').'</a> | </span>';
                $actions.= '<span class="edit"><a href="javascript:;">'.__('Edit').'</a> | </span>';
                $actions.= '<span class="delete"><a href="javascript:;" onclick="post_delete('.$post['postid'].')">'.__('Delete').'</a></span>';
                echo       '<tr>';
                echo           '<td class="check-column"><input type="checkbox" name="listids[]" value="'.$data['commentid'].'" /></td>';
                echo           '<td><strong>'.$data['author'].'</strong></td>';
                echo           '<td>'.sprintf(__('Submitted on: <b>%s</b>'),date('Y-m-d H:i:s',$data['date'])).'<br />'.$data['content'].'<br/><div class="row-actions">'.$actions.'</div></td>';
                echo           '<td><a href="'.ADMIN.'post.php?method=edit&postid='.$data['postid'].'">'.$post['title'].'</a></td>';
                echo       '</tr>';
            }
        } else {
            echo           '<tr><td colspan="7" class="tc">'.__('No record!').'</td></tr>';
        }
        echo           '</tbody>';
        echo       '</table>';
        table_nav($page_url);
        echo   '</form>';
        echo '</div>';
        // 加载尾部
        include ADMIN_PATH.'/admin-footer.php';
        break;
}

/**
 * 批量操作
 *
 * @param string $url
 * @return void
 */
function table_nav($url) {
    echo '<div class="table-nav">';
    echo     '<select name="actions">';
    echo         '<option value="">'.__('Bulk Actions').'</option>';
    echo         '<option value="unapprove">'.__('Unapprove').'</option>';
    echo         '<option value="approve">'.__('Approve').'</option>';
    echo         '<option value="delete">'.__('Delete').'</option>';
    echo     '</select>';
    echo     '<button type="button">'.__('Apply').'</button>';
    echo     pages_list($url);
    echo '</div>';
}
/**
 * 表头
 *
 */
function table_thead() {
    echo '<tr>';
    echo     '<th class="check-column"><input type="checkbox" name="select" value="all" /></th>';
    echo     '<th class="wp20">'.__('Author').'</th>';
    echo     '<th>'._x('Comment','comment').'</th>';
    echo     '<th class="wp20">'.__('In Response To').'</th>';
    echo '</tr>';
}