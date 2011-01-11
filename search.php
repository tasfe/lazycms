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
include dirname(__FILE__).'/global.php';
// 加载模块
include_modules();
// 处理 PATH_INFO
if (!empty($_SERVER['PATH_INFO']))
    parse_path(ltrim($_SERVER['PATH_INFO'], '/'));
// 获取参数
$t = isset($_REQUEST['t']) ? trim($_REQUEST['t']) : null;
$q = isset($_REQUEST['q']) ? trim($_REQUEST['q']) : null;

switch ($t) {
    case 'tags':
        break;
    case 'search':
        break;
    case 'archives':
        break;
}

$db = get_conn();
$inner  = $b_guid = '';
// 载入模版
$html   = tpl_loadfile(ABS_PATH.'/'.system_themes_path().'/'.esc_html(C('TPL-Search')));
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
    $tid    = $db->result(sprintf("SELECT `taxonomyid` FROM `#@_term_taxonomy` WHERE `type`='post_tag' AND `termid`=%d", esc_sql($term['termid'])));
    $sql    = sprintf("SELECT DISTINCT(`p`.`postid`) FROM `#@_term_relation` AS `tr` LEFT JOIN `#@_post` AS `p` ON `p`.`postid`=`tr`.`objectid` WHERE `p`.`type`='post' AND `tr`.`taxonomyid`=%d ORDER BY `p`.`postid` %s", $tid, $order);
    $result = pages_query($sql);
    // 解析分页标签
    if (stripos($html,'{pagelist') !== false) {
        $html = preg_replace('/\{(pagelist)[^\}]*\/\}/isU',
            pages_list(PHP_FILE.'?t='.rawurlencode($t).'&q='.rawurlencode($tag).'&page=$'),
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
            $post['list'] = taxonomy_get($post['listid']);
            $post['path'] = post_get_path($post['listid'],$post['path']);
            // 文章内容
            if ($post['content'] && strpos($post['content'],'<!--pagebreak-->')!==false) {
                $contents = explode('<!--pagebreak-->', $post['content']);
                $content  = array_shift($contents);
            } else {
                $content  = $post['content'];
            }
            // 设置文章变量
            $vars = array(
                'zebra'    => ($i % ($zebra + 1)) ? '0' : '1',
                'postid'   => $post['postid'],
                'userid'   => $post['userid'],
                'author'   => $post['author'],
                'title'    => $post['title'],
                'views'    => '<script type="text/javascript" src="'.ROOT.'common/gateway.php?func=post_views&postid='.$post['postid'].'"></script>',
                'comment'  => '<script type="text/javascript" src="'.ROOT.'common/gateway.php?func=post_comment_count&postid='.$post['postid'].'"></script>',
                'digg'     => $post['digg'],
                'path'     => ROOT.$post['path'],
                'content'  => $content,
                'datetime' => $post['datetime'],
                'edittime' => $post['edittime'],
                'keywords' => $post['keywords'],
                'description' => $post['description'],
            );
            // 设置分类变量
            if (isset($post['list'])) {
                $vars['listid']   = $post['list']['taxonomyid'];
                $vars['listname'] = $post['list']['name'];
                $vars['listpath'] = ROOT.$post['list']['path'].'/';
                if (isset($post['list']['meta'])) {
                    foreach((array)$post['list']['meta'] as $k=>$v) {
                        $vars['list.'.$k] = $v;
                    }
                }
            }
            // 清理数据
            tpl_clean();
            tpl_set_var($vars);
            // 设置自定义字段
            if (isset($post['meta'])) {
                foreach((array)$post['meta'] as $k=>$v) {
                    tpl_set_var('post.'.$k, $v);
                }
            }
            // 解析标签
            $inner.= tpl_parse($block['inner'], $block); $i++;
        }
    } else {
        $inner = __('No record!');
    }
    // 生成标签块的唯一ID
    $b_guid = guid($block['tag']);
    // 把标签块替换成变量标签
    $html   = str_replace($block['tag'], '{$'.$b_guid.'}', $html);
    // 清理模版内部变量
    tpl_clean();
    tpl_set_var($b_guid,$inner);
    tpl_set_var(array(
        'guide'    => 'Tags &gt;&gt; '.$tag,
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