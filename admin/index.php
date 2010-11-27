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
// 动作
$method = isset($_REQUEST['method'])?$_REQUEST['method']:null;

switch ($method) {
    // 进度
    case 'publish':
        $data = publish_exec();
        if ($data) {
            $data['pubid'] = $data['pubid'];
            if ($data['total']<=0 && $data['state']==2) {
                $data['rate']   = 100;
            } else {
                $data['rate']   = $data['total']<=0 ? 0 : floor($data['complete'] / $data['total'] * 100);
            }
            $data['total']      = number_format($data['total']);
            $data['complete']   = number_format($data['complete']);
            $data['elapsetime'] = time_format('%H:%i:%s,%ms',$data['elapsetime']);
            $data['state']      = get_icon('c'.($data['state']+1));
            // 删除不需要显示的数据
            unset($data['name'],$data['func'],$data['args']);
        }
        ajax_return($data);
        break;
    // 取得关键词
    case 'terms':
        $content = isset($_REQUEST['content'])?$_REQUEST['content']:null;
        if ($content) {
            $terms = term_gets($content);
        } else {
            $terms = array();
        }
        ajax_return(empty($terms) ? '' : $terms);
        break;
    // 默认页面
    default:
        // HTTPLIB
        require COM_PATH.'/system/httplib.php';
        $db = get_conn(); $http_test = httplib_test();
        // 设置标题
        system_head('title',__('Control Panel'));
        system_head('styles', array('css/cpanel','css/comment'));
        system_head('scripts', array('js/cpanel','js/comment'));
        system_head('loadevents','cpanel_init');
        system_head('jslang',array(
            'Reply comment' => __('Reply comment'),
            'Edit comment' => __('Edit comment'),
            'Author' => __('Author'),
            'Email' => __('Email'),
            'Url' => __('Url'),
        ));
        // 加载头部
        include ADMIN_PATH.'/admin-header.php';

        echo '<div class="wrap">';
        echo    '<h2>'.system_head('title').'</h2>';
        echo    '<div class="container">';
        echo        '<fieldset cookie="true">';
        echo            '<a href="javascript:;" class="toggle" title="'.__('Click to toggle').'"><br/></a>';
        echo            '<h3>'.__('Server Environment').'</h3>';
        echo            '<div class="inside server-env">';
        echo                '<p><label>'.__('Server OS:').'</label>'.PHP_OS.'</p>';
        echo                '<p><label>'.__('Server Software:').'</label>'.$_SERVER['SERVER_SOFTWARE'].'</p>';
        echo                '<p><label>'.__('Server API :').'</label>'.PHP_SAPI.'</p>';
        echo                '<p><label>'.__('PHP Version:').'</label>'.PHP_VERSION.'&nbsp; '.echo_state(version_compare(PHP_VERSION,'4.3.3','>')).'</p>';
        echo                '<p><label>'.__('MySQL Version:').'</label>'.$db->version().'&nbsp; '.echo_state(version_compare($db->version(),'4.1.0','>')).'</p>';
        echo                '<p><label>'.__('LazyCMS Version:').'</label>'.LAZY_VERSION.'</p>';
        echo                '<p><label>'.__('GD library:').'</label>'.(function_exists('gd_info') ? GD_VERSION : __('Not Supported')).'&nbsp; '.echo_state(function_exists('gd_info')).'</p>';
        echo                '<p><label>'.__('ICONV:').'</label>'.(function_exists('iconv') ? ICONV_VERSION : __('Not Supported')).'&nbsp; '.echo_state(function_exists('iconv')).'</p>';
        echo                '<p><label>'.__('Multibyte String:').'</label>'.(extension_loaded('mbstring') ? 'mb_***' : __('Not Supported')).'&nbsp; '.echo_state(extension_loaded('mbstring')).'</p>';
        echo                '<p><label>'.__('Allow URL Open:').'</label>'.($http_test ? array_shift($http_test) : __('Not Supported')).'&nbsp; '.echo_state($http_test).'</p>';
        echo            '</div>';
        echo        '</fieldset>';
        echo        '<div class="clear"><br/></div>';
        echo    '</div>';
        echo    '<div class="container">';
        echo        '<fieldset cookie="true">';
        echo            '<a href="javascript:;" class="toggle" title="'.__('Click to toggle').'"><br/></a>';
        echo            '<h3>'.__('Right Now').'</h3>';
        echo            '<div class="inside right-now">';
        $post_count = post_count('post'); $page_count = post_count('page'); $category_count = taxonomy_count('category'); $comment_count = comment_count(0,'all');
        echo                '<div class="content">';
        echo                '<h4>'.__('Content').'</h4>';
        echo                '<table cellspacing="0">';
        echo                    '<tr><td class="number"><a href="'.ADMIN.'post.php">'.number_format($post_count).'</a></td><td><a href="'.ADMIN.'post.php">'._n('Post','Posts', $post_count).'</a></td></tr>';
        echo                    '<tr><td class="number"><a href="'.ADMIN.'page.php">'.number_format($page_count).'</a></td><td><a href="'.ADMIN.'page.php">'._n('Page','Pages', $page_count).'</a></td></tr>';
        echo                    '<tr><td class="number"><a href="'.ADMIN.'categories.php">'.number_format($category_count).'</a></td><td><a href="'.ADMIN.'categories.php">'._n('Category','Categories', $category_count).'</a></td></tr>';
        echo                '</table>';
        echo                '</div>';
        echo                '<div class="discussion">';
        echo                '<h4>'.__('Discussion').'</h4>';
        echo                '<table cellspacing="0">';
        echo                    '<tr><td class="number"><a href="'.ADMIN.'comment.php">'.number_format($comment_count).'</a></td><td><a href="'.ADMIN.'comment.php">'._n('Comment','Comments', $comment_count).'</a></td></tr>';
        echo                    '<tr><td class="number"><a href="'.ADMIN.'comment.php?status=approved">'.number_format(comment_count(0,'1')).'</a></td><td><a href="'.ADMIN.'comment.php?status=approved">'.__('Approved').'</a></td></tr>';
        echo                    '<tr><td class="number"><a href="'.ADMIN.'comment.php?status=moderated">'.number_format(comment_count(0,'0')).'</a></td><td><a href="'.ADMIN.'comment.php?status=moderated">'.__('Pending').'</a></td></tr>';
        echo                '</table>';
        echo                '</div>';
        echo            '</div>';
        echo        '</fieldset>';
        echo        '<fieldset cookie="true">';
        echo            '<a href="javascript:;" class="toggle" title="'.__('Click to toggle').'"><br/></a>';
        echo            '<h3>'.__('Recent Comments').'</h3>';
        echo            '<div class="inside comments">';
        $i  = 0;
        $rs = $db->query("SELECT * FROM `#@_comments` ORDER BY `cmtid` DESC LIMIT 5;");
        while ($data = $db->fetch($rs)) {
            $data['avatar'] = get_avatar($data['mail'], 50, 'mystery');
            $data['author'] = $data['author'] ? $data['author'] : __('Anonymous');
            if ($data['approved']==0) {
                $actions = '<span class="approve"><a href="javascript:;" onclick="comment_state(\'approve\','.$data['cmtid'].')">'.__('Approve').'</a> | </span>';
            } elseif ($data['approved']==1) {
                $actions = '<span class="unapprove"><a href="javascript:;" onclick="comment_state(\'unapprove\','.$data['cmtid'].')">'.__('Unapprove').'</a> | </span>';
            }
            $actions.= '<span class="reply"><a href="javascript:;" onclick="comment_reply('.$data['cmtid'].')">'.__('Reply').'</a> | </span>';
            $actions.= '<span class="edit"><a href="javascript:;" onclick="comment_edit('.$data['cmtid'].')">'.__('Edit').'</a> | </span>';
            $actions.= '<span class="delete"><a href="javascript:;" onclick="comment_delete('.$data['cmtid'].')">'.__('Delete').'</a></span>';

            echo            '<div id="cmt-'.$data['cmtid'].'" class="comment">';
            echo                '<img src="'.$data['avatar'].'" width="50" height="50" alt="'.esc_html($data['author']).'" />';
            echo                '<div class="comment-wrap">';
            if ($post = post_get($data['postid'])) {
                $path = post_get_path($post['sortid'],$post['path']);
                echo                '<h4><a href="'.ROOT.$path.'#cmt_list" target="_blank">'.$post['title'].'</a></h4>';
            } else {
                echo                '<h4>'.__('The post has been deleted.').'</h4>';
            }
            if ($data['approved']==0) {
                echo                '<em>['.__('Pending').']</em>';
            }
            echo                    '<span class="author">'.sprintf(__('From %s'), '<cite>'.$data['author'].'</cite>').'</span>';
            echo                    '<div class="content">'.$data['content'].'</div>';
            echo                    '<div class="row-actions">'.$actions.'</div>';
            echo                '</div>';
            echo                '<div class="clear"><br/></div>';
            echo            '</div>';
            $i++;
        }
        if ($i==0) {
            echo        '<div class="empty">'.__('No comments yet.').'</div>';
        } else {
            echo        '<div class="buttons"><a href="'.ADMIN.'comment.php" class="button">'.__('View all').'</a></div>';
        }
        echo            '</div>';
        echo        '</fieldset>';
        echo        '<div class="clear"><br/></div>';
        echo    '</div>';
        echo '</div>';
        // 加载尾部
        include ADMIN_PATH.'/admin-footer.php';
        break;
}