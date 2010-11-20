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
defined('COM_PATH') or die('Restricted access!');

/**
 * 添加评论
 *
 * @param int $postid
 * @param string $content
 * @param int $parent
 * @param array $user
 * @return int
 */
function comment_add($postid,$content,$parent=0,$user=null) {
    $db = get_conn();
    $userid = isset($user['userid']) ? $user['userid'] : 0;
    $author = isset($user['name']) ? esc_html($user['name']) : '';
    $email  = isset($user['email']) ? esc_html($user['email']) : '';
    $url    = isset($user['url']) ? esc_html($user['url']) : '';
    return $db->insert('#@_comments', array(
        'postid'  => $postid,
        'author'  => $author,
        'email'   => $email,
        'url'     => $url,
        'ip'      => sprintf('%u',ip2long($_SERVER['REMOTE_ADDR'])),
        'agent'   => esc_html($_SERVER['HTTP_USER_AGENT']),
        'date'    => time(),
        'content' => $content,
        'parent'  => $parent,
        'userid'  => $userid,
        'approved'=> 1,
    ));
}

function comment_create($postid) {
    $postid = intval($postid);
    if (!$postid) return false;
    if ($post = post_get($postid)) {
        // 加载模版
        $html  = tpl_loadfile(ABS_PATH.'/'.system_themes_path().'/'.esc_html(C('Template-Comments')));
        // 标签块信息
        $block  = tpl_get_block($html,'comments','list');
        if ($block) {
            // 每页条数
            $number = tpl_get_attr($block['tag'],'number');
            // 排序方式
            $order  = tpl_get_attr($block['tag'],'order');
            // 斑马线实现
            $zebra  = tpl_get_attr($block['tag'],'zebra');
            // 校验数据
            $zebra  = validate_is($zebra,VALIDATE_IS_NUMERIC) ? $zebra : 0;
            $number = validate_is($number,VALIDATE_IS_NUMERIC) ? $number : 10;
            $order  = instr(strtoupper($order),'ASC,DESC') ? $order : 'DESC';
            // sql
            $sql = sprintf("SELECT * FROM `#@_comments` WHERE `postid`=%d ORDER BY `date` DESC");
        }
    }
}