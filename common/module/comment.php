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
    $email  = isset($user['mail']) ? esc_html($user['mail']) : '';
    $url    = isset($user['url']) ? esc_html($user['url']) : '';
    return $db->insert('#@_comments', array(
        'postid'   => $postid,
        'author'   => $author,
        'mail'     => $email,
        'url'      => $url,
        'ip'       => sprintf('%u',ip2long($_SERVER['REMOTE_ADDR'])),
        'agent'    => esc_html($_SERVER['HTTP_USER_AGENT']),
        'date'     => time(),
        'content'  => $content,
        'parent'   => $parent,
        'userid'   => $userid,
        'approved' => 1,
    ));
}
/**
 * 编辑评论
 *
 * @param int $cmtid
 * @param string $content
 * @param string $status
 * @param array $user
 * @return int
 */
function comment_edit($cmtid, $content, $status=null, $user=null) {
    $db = get_conn(); $sets = array();
    if ($content !== null) $sets['content'] = $content;
    if ($status !== null) $sets['approved'] = $status;
    if (isset($user['name'])) $sets['author'] = esc_html($user['name']);
    if (isset($user['mail'])) $sets['mail'] = esc_html($user['mail']);
    if (isset($user['url'])) $sets['url'] = esc_html($user['url']);
    if (isset($user['userid'])) $sets['userid'] = esc_html($user['userid']);
    $result = $db->update('#@_comments', $sets, array('cmtid' => $cmtid));
    // 重新生成评论
    comment_create(comment_get_postid($cmtid));
    return $result;
}
/**
 * 查询postid
 *
 * @param int $cmtid
 * @return int
 */
function comment_get_postid($cmtid) {
    $db = get_conn(); return $db->result(sprintf("SELECT `postid` FROM `#@_comments` WHERE `cmtid`=%d;", $cmtid));
}
/**
 * 取得一条评论
 *
 * @param int $cmtid
 * @return array
 */
function comment_get($cmtid) {
    static $comments = array();
    if (isset($comments[$cmtid]))
        return $comments[$cmtid];
    
    $db = get_conn();
    $rs = $db->query("SELECT * FROM `#@_comments` WHERE `cmtid`=%d;", $cmtid);
    if ($data = $db->fetch($rs)) {
        $comments[$cmtid] = $data;
    }
    return $comments[$cmtid];
}
/**
 * 删除评论
 *
 * @param int $cmtid
 * @return int
 */
function comment_delete($cmtid) {
    $db = get_conn();
    $postid = comment_get_postid($cmtid);
    $result = $db->delete('#@_comments', array('cmtid' => $cmtid));
    // 重新生成评论
    comment_create($postid);
    return $result;
}
/**
 * 评论数
 *
 * @param int $postid
 * @param string $status
 * @return int
 */
function comment_count($postid,$status='all') {
    $db = get_conn(); $where = 'WHERE 1';
    if ($postid) {
        $where.= sprintf(" AND `postid`='%d'", $postid);
    }
    if ($status != 'all') {
        $where.= sprintf(" AND `approved`='%s'", strval($status));
    }
    return $db->result("SELECT COUNT(`cmtid`) FROM `#@_comments` {$where};");
}
/**
 * 评论人数
 *
 * @param int $postid
 * @return int
 */
function comment_people($postid) {
    $db = get_conn(); return $db->result(sprintf("SELECT COUNT(DISTINCT(`author`)) FROM `#@_comments` WHERE `postid`=%d;", $postid));
}
/**
 * 生成评论
 *
 * @param int $postid
 * @return bool|int
 */
function comment_create($postid) {
    $postid = intval($postid);
    if (!$postid) return false;
    if ($post = post_get($postid)) {
        // 评论地址
        $post['cmt_path'] = post_get_path($post['sortid'],$post['path'], C('Comments-Path'));
        $guide = system_category_guide($post['sortid']);
        $title = sprintf(__('Comment: %s'), $post['title']);
        // 加载模版
        $html = tpl_loadfile(ABS_PATH.'/'.system_themes_path().'/'.esc_html(C('Template-Comments')));
        // 解析分页标签
        if (stripos($html,'{pagelist') !== false) {
            $html = preg_replace('/\{(pagelist)[^\}]*\/\}/isU','{$pagelist}', $html);
        }
        // 分析路径
        if (($pos=strrpos($post['cmt_path'],'.')) !== false) {
            $basename = substr($post['cmt_path'],0,$pos);
            $suffix   = substr($post['cmt_path'],$pos);
        } else {
            $basename = $post['cmt_path'];
            $suffix   = '';
        }
        // 标签块信息
        if ($block = tpl_get_block($html,'comments','list')) {
            $inner = $b_guid = '';
            // 生成标签块的唯一ID
            $b_guid = guid($block['tag']);
            // 把标签块替换成变量标签
            $html   = str_replace($block['tag'], '{$'.$b_guid.'}', $html);
            // 没有评论
            if (comment_count($post['postid'],'1') == 0) {
                tpl_clean();
                tpl_set_var($b_guid, __('No comment!'));
                tpl_set_var(array(
                    'guide'    => $guide ? $guide.' &gt;&gt; '.$title : $title,
                    'title'    => $title,
                    'keywords' => post_get_keywords($post['keywords']),
                    'description' => $post['description'],
                ));

                $html = tpl_parse($html);
                // 生成的文件路径
                $file = ABS_PATH.'/'.$post['cmt_path'];
                // 创建目录
                mkdirs(dirname($file));
                // 保存文件
                return file_put_contents($file, $html);
            }
            // 有评论
            else {
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

                $db = get_conn(); $i = $length = 0; $page = 1;
                $rs = $db->query("SELECT * FROM `#@_comments` WHERE `postid`=%d AND `approved`='1' ORDER BY `cmtid` {$order};", $post['postid']);
                $total = $db->result("SELECT FOUND_ROWS();");
                $pages = ceil($total / $number);
                $pages = ((int)$pages == 0) ? 1 : $pages;

                while ($data = $db->fetch($rs)) {
                    $block['inner'] = tpl_get_block_inner($block);
                    // 清理数据
                    tpl_clean();
                    tpl_set_var(array(
                        'zebra'   => ($i % ($zebra + 1)) ? '0' : '1',
                        'cmtid'   => $data['cmtid'],
                        'avatar'  => get_avatar($data['mail'], 16, 'mystery'),
                        'author'  => $data['author'] ? $data['author'] : __('Anonymous'),
                        'email'   => $data['mail'],
                        'url'     => !strncmp($data['url'],'http://',7) ? $data['url'] : 'http://'.$data['url'],
                        'ip'      => long2ip($data['ip']),
                        'address' => ip2addr(long2ip($data['ip'])),
                        'content' => nl2br($data['content']),
                        'agent'   => $data['agent'],
                        'date'    => $data['date'],
                    ));
                    // 解析变量
                    $inner.= tpl_parse($block['inner']); $i++; $length++;
                    // 分页
                    if (($i%$number)==0 || $i==$total) {
                        // 所需要的标签和数据都不存在，不需要生成页面
                        if ($inner=='' && $page>1) return false;
                        tpl_clean();
                        tpl_set_var($b_guid, $inner);
                        tpl_set_var(array(
                            'guide'    => $guide ? $guide.' &gt;&gt; '.$title : $title,
                            'title'    => $title,
                            'pagelist' => pages_list(ROOT.$basename.'_$'.$suffix, '!_$', $page, $pages, $length),
                            'keywords' => post_get_keywords($post['keywords']),
                            'description' => $post['description'],
                        ));

                        $out  = tpl_parse($html);
                        // 生成的文件路径
                        $file = ABS_PATH.'/'.$basename . ($page==1 ? '' : '_'.$page) . $suffix;
                        // 创建目录
                        mkdirs(dirname($file));
                        // 保存文件
                        file_put_contents($file, $out);

                        $page++; $inner = ''; $length = 0;
                    }
                }
                return true;
            }
        }
        // 没有标签
        else {
            tpl_clean();
            tpl_set_var(array(
                'guide'    => $guide ? $guide.' &gt;&gt; '.$title : $title,
                'title'    => $title,
                'keywords' => post_get_keywords($post['keywords']),
                'description' => $post['description'],
            ));

            $html = tpl_parse($html);
            // 生成的文件路径
            $file = ABS_PATH.'/'.$post['cmt_path'];
            // 创建目录
            mkdirs(dirname($file));
            // 保存文件
            return file_put_contents($file, $html);
        }
    }
}