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
require dirname(__FILE__).'/admin.php';
// 查询管理员信息
$_USER = user_current();
// 设置标题
system_head('title',__('Comments'));
system_head('styles', array('css/comment'));
system_head('scripts',array('js/comment'));

// 方法
$method = isset($_REQUEST['method'])?$_REQUEST['method']:null;

switch ($method) {
    case 'get':
        current_user_can('comment-edit');
        $cmtid   = isset($_REQUEST['cmtid']) ? $_REQUEST['cmtid'] : 0;
        $comment = comment_get($cmtid);
        ajax_return(array(
            'author'  => $comment['author'],
            'mail'    => $comment['mail'],
            'url'     => $comment['url'],
            'content' => $comment['content'],
        ));
        break;
    case 'edit':
        current_user_can('comment-edit');
        $cmtid   = isset($_POST['cmtid']) ? $_POST['cmtid'] : 0;
        $content = isset($_POST['content']) ? trim($_POST['content'])  : '';
        comment_edit($cmtid, $content, null, array(
            'name' => isset($_POST['author']) ? $_POST['author'] : '',
            'mail' => isset($_POST['mail']) ? $_POST['mail'] : '',
            'url'  => isset($_POST['url']) ? $_POST['url'] : '',
        ));
        comment_create(comment_get_postid($cmtid));
        ajax_success(__('Comment updated.'), "LazyCMS.redirect('".referer()."');");
        break;
    case 'reply':
        current_user_can('comment-reply');
        $parent  = isset($_POST['parent'])  ? $_POST['parent']  : 0;
        $content = isset($_POST['content']) ? trim($_POST['content'])  : '';
        $postid  = comment_get_postid($parent);
        if (comment_add($postid,$content,$parent,$_USER)) {
            comment_create($postid);
            ajax_success(__('Comment on the success!'), "LazyCMS.redirect('".referer()."');");
        } else {
            ajax_error(__('Comment failed!'));
        }
        break;
    case 'bulk':
        $action  = isset($_POST['action'])?$_POST['action']:null;
	    $listids = isset($_POST['listids'])?$_POST['listids']:null;
	    if (empty($listids)) {
	    	ajax_error(__('Did not select any item.'));
	    }
	    switch ($action) {
	        case 'unapprove':
                current_user_can('comment-state');
	            foreach ($listids as $cmtid) {
	            	comment_edit($cmtid,null,'0');
	            }
                redirect(referer());
	            break;
            case 'approve':
                current_user_can('comment-state');
                $approved = count($listids);
	            foreach ($listids as $cmtid) {
	            	comment_edit($cmtid,null,'1');
	            }
                ajax_success(sprintf(_n('%s comment approved', '%s comments approved', $approved ), $approved),"LazyCMS.redirect('".referer()."');");
	            break;
            case 'delete':
                current_user_can('comment-delete');
                $deleted = count($listids);
	            foreach ($listids as $cmtid) {
	            	comment_delete($cmtid);
	            }
	            ajax_success(sprintf(_n('%s comment permanently deleted', '%s comments permanently deleted', $deleted ), $deleted),"LazyCMS.redirect('".referer()."');");
	            break;
            default:
                ajax_alert(__('Parameter is invalid.'));
                break;
	    }
        break;
    default:
        current_user_can('comment-list');
        system_head('jslang',array(
            'Reply comment' => __('Reply comment'),
            'Edit comment' => __('Edit comment'),
            'Author' => __('Author'),
            'Email' => __('Email'),
            'Url' => __('Url'),
        ));
        system_head('loadevents','comment_list_init');
        $where  = 'WHERE 1';
        $query  = array('page' => '$');
        $status = isset($_GET['status'])  ? $_GET['status']  : '';
        if ($status) {
            $query['status'] = $status;
            if ($status == 'moderated') {
                $approved = '0';
            } elseif ($status == 'approved') {
                $approved = '1';
            } else {
                $approved = $status;
            }
            $where.= sprintf(" AND `approved`='%s'", $approved);
        }
        $postid = isset($_GET['p'])  ? $_GET['p']  : null;
        if ($postid) {
            $query['p'] = $postid;
            $where.= ' AND `postid`='.esc_sql($postid);
        }
        $ipaddr = isset($_GET['ip']) ? $_GET['ip'] : null;
        if ($ipaddr) {
            $query['ip'] = $ipaddr;
            $where.= ' AND `ip`='.esc_sql(sprintf('%u', ip2long($ipaddr)));
        }
        
        $result = pages_query("SELECT * FROM `#@_comments` {$where} ORDER BY `cmtid` DESC");
        // 分页地址
        $page_url = PHP_FILE.'?'.http_build_query($query);
        $pend_len = comment_count(0, '0');
        // 加载头部
        include ADMIN_PATH.'/admin-header.php';
        echo '<div class="wrap">';
        echo   '<h2>'.system_head('title').'</h2>';
        echo   '<form action="'.PHP_FILE.'?method=bulk" method="post" name="comments" id="comments">';
        echo       '<div class="submenu">';
        echo           '<a'.($status==''?' class="current"':'').' href="'.PHP_FILE.'">'._x('All','comments').'</a> | ';
        echo           '<a'.($status=='moderated'?' class="current"':'').' href="'.PHP_FILE.'?status=moderated">'._x('Pending','comments').'</a> '.($pend_len?' <span>('.$pend_len.')</span> ':'').'| ';
        echo           '<a'.($status=='approved'?' class="current"':'').' href="'.PHP_FILE.'?status=approved">'.__('Approved').'</a>';
        echo       '</div>';
        table_nav($page_url);
        echo       '<table class="data-table fixed" cellspacing="0">';
        echo           '<thead>';
        table_thead();
        echo           '</thead>';
        echo           '<tfoot>';
        table_thead();
        echo           '</tfoot>';
        echo           '<tbody>';
        if ($result) {
            while ($data = pages_fetch($result)) {
                $data['avatar'] = get_avatar($data['mail'], 32, 'mystery');
                $data['author'] = $data['author'] ? $data['author'] : __('Anonymous');
                $data['count']  = comment_count($data['postid']);
                $data['ipaddr'] = long2ip($data['ip']);
                if ($data['parent']) {
                    $parent = comment_get($data['parent']);
                    $reply  = ' | '.sprintf(__('In reply to <a href="%s">%s</a>.'), 'javascript:;', $parent['author']);
                } else {
                    $reply  = '';
                }
                if ($data['approved']==0) {
                    $actions = '<span class="approve"><a href="javascript:;" onclick="comment_state(\'approve\','.$data['cmtid'].')">'.__('Approve').'</a> | </span>';
                } elseif ($data['approved']==1) {
                    $actions = '<span class="unapprove"><a href="javascript:;" onclick="comment_state(\'unapprove\','.$data['cmtid'].')">'.__('Unapprove').'</a> | </span>';
                }
                $actions.= '<span class="reply"><a href="javascript:;" onclick="comment_reply('.$data['cmtid'].')">'.__('Reply').'</a> | </span>';
                $actions.= '<span class="edit"><a href="javascript:;" onclick="comment_edit('.$data['cmtid'].')">'.__('Edit').'</a> | </span>';
                $actions.= '<span class="delete"><a href="javascript:;" onclick="comment_delete('.$data['cmtid'].')">'.__('Delete').'</a></span>';
                echo       '<tr id="cmt-'.$data['cmtid'].'">';
                echo           '<td class="check-column"><input type="checkbox" name="listids[]" value="'.$data['cmtid'].'" /></td>';
                echo           '<td>';
                echo                '<div class="avatar"><img src="'.$data['avatar'].'" width="32" height="32" alt="'.esc_html($data['author']).'" /></div>';
                echo                '<strong>'.$data['author'].'</strong>';
                if ($data['url'] && $data['url']!='http://') {
                    $url = !strncmp($data['url'],'http://',7) ? substr($data['url'],7) : $data['url'];
                    echo            '<br /><a href="'.$data['url'].'" target="_blank">'.$url.'</a>';
                }
                if ($data['mail']) {
                    echo            '<br /><a href="mailto:'.$data['mail'].'">'.$data['mail'].'</a>';
                }
                echo            '<br /><a href="'.PHP_FILE.'?ip='.$data['ipaddr'].'" title="'.esc_html(ip2addr($data['ip'])).'">'.$data['ipaddr'].'</a>';
                echo           '</td>';
                echo           '<td><div class="submitted-on">'.sprintf(__('Submitted on: <b>%s</b>'),date('Y-m-d H:i:s',$data['date'])).$reply.'</div>';
                echo               '<div class="content">'.nl2br($data['content']).'</div><div class="row-actions">'.$actions.'</div></td>';
                if ($post = post_get($data['postid'])) {
                    $path = post_get_path($post['sortid'],$post['path']);
                    echo       '<td><a href="'.ADMIN.'post.php?method=edit&postid='.$data['postid'].'">'.$post['title'].'</a><br />';
                    echo           '<a class="comment-count" href="'.PHP_FILE.'?p='.$data['postid'].'"><span>'.$data['count'].'</span></a>';
                    echo           '<a href="'.ROOT.$path.'#cmt_list" target="_blank">#</a>';
                    echo       '</td>';
                } else {
                    echo       '<td>'.__('The post has been deleted.').'<br />';
                    echo           '<a class="comment-count" href="'.PHP_FILE.'?p='.$data['postid'].'"><span>'.$data['count'].'</span></a>';                    
                    echo       '</td>';
                }
                echo       '</tr>';
            }
        } else {
            echo           '<tr><td colspan="4" class="tc">'.__('No record!').'</td></tr>';
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