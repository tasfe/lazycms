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
require dirname(__FILE__).'/global.php';
// 加载模块
include_modules(); $db = get_conn();

$inner  = $block_guid = '';
// 获取参数
$tag    = isset($_REQUEST['q']) ? trim($_REQUEST['q']) : null;
// 载入模版
$html   = tpl_loadfile(ABS_PATH.'/'.system_themes_path().'/'.esc_html(C('Template-Tags')));
// 标签块信息
$block  = tpl_get_block($html,'post,list','list');
if ($tag && $block) {
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
    // 设置每页显示数
    pages_init($number);
    // 显示Tags相关的文章列表
    $term   = term_get_byname($tag);
    $tid    = $db->result(sprintf("SELECT `taxonomyid` FROM `#@_term_taxonomy` WHERE `termid`=%d", esc_sql($term['termid'])));
    $sql    = sprintf("SELECT DISTINCT(`p`.`postid`) FROM `#@_post` AS `p` RIGHT JOIN `#@_term_relation` AS `tr` ON `p`.`postid`=`tr`.`objectid` WHERE `p`.`type`='post' AND `tr`.`taxonomyid`=%d ORDER BY `p`.`postid` %s", $tid, $order);
    $result = pages_query($sql);
    // 解析分页标签
    if (stripos($html,'{pagelist') !== false) {
        $html = preg_replace('/\{(pagelist)[^\}]*\/\}/isU',
            pages_list(PHP_FILE.'?q='.rawurlencode($tag).'&page=$'),
            $html
        );
    }
    // 数据存在
    if ($result) {
        $i = 0;
        // 取得标签块内容
        $block['inner'] = tpl_get_block_inner($block);
        while ($data = pages_fetch($result)) {
            $post = post_get($data['postid']);
            if (empty($post)) continue;
            $post['sort'] = taxonomy_get($post['sortid']);
            $post['path'] = post_get_path($post['sortid'],$post['path']);
            // 设置文章变量
            $vars = array(
                'zebra'    => ($i % ($zebra + 1)) ? '0' : '1',
                'postid'   => $post['postid'],
                'sortid'   => $post['sortid'],
                'userid'   => $post['userid'],
                'author'   => $post['author'],
                'title'    => $post['title'],
                'views'    => '<script type="text/javascript" src="'.ROOT.'common/gateway.php?func=post_views&postid='.$post['postid'].'"></script>',
                'digg'     => $post['digg'],
                'path'     => ROOT.$post['path'],
                'datetime' => $post['datetime'],
                'edittime' => $post['edittime'],
                'keywords' => $post['keywords'],
                'description' => $post['description'],
            );
            // 设置分类变量
            if (isset($post['sort'])) {
                $vars['sortname'] = $post['sort']['name'];
                $vars['sortpath'] = ROOT.$post['sort']['path'].'/';
            }
            // 清理数据
            tpl_clean();
            tpl_set_var($vars);
            // 设置自定义字段
            if (isset($post['meta'])) {
                foreach((array)$post['meta'] as $k=>$v) {
                    tpl_set_var('model.'.$k, $v);
                }
            }
            // 解析二级内嵌标签
            if (isset($block['sub'])) {
                foreach ($block['sub'] as $sblock) {
                    $sblock['name'] = strtolower($sblock['name']);
                    switch($sblock['name']) {
                        // TODO 解析图片标签
                        case 'images':
                            $block['inner'] = str_replace($sblock['tag'],'',$block['inner']);
                            break;
                    }
                }
            }

            // 解析变量
            $inner.= tpl_parse($block['inner']); $i++;
        }
    }
    // 生成标签块的唯一ID
    $block_guid = guid($block['tag']);
    // 把标签块替换成变量标签
    $html = str_replace($block['tag'], '{$'.$block_guid.'}', $html);
    // 清理模版内部变量
    tpl_clean();
    tpl_set_var($block_guid,$inner);
    tpl_set_var(array(
        'guide'    => '<a href="'.PHP_FILE.'">Tags</a> &gt;&gt; '.$tag,
        'title'    => $tag,
        'keywords' => $tag,
    ));
} else {
    if (stripos($html,'{pagelist') !== false) {
        $html = preg_replace('/\{(pagelist)[^\}]*\/\}/isU', '', $html);
    }
    tpl_clean();
    tpl_set_var(array(
        'title'    => 'Tags',
        'keywords' => 'Tags',
    ));
}
echo tpl_parse($html);
