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

// 注册模版变量处理函数
tpl_add_plugin('system_tpl_plugin');
tpl_add_plugin('system_tpl_list_plugin');

/**
 * 处理模版变量
 *
 * @param  $tag_name
 * @param  $tag
 * @return mixed    null 说明没有解析成功，会继续
 */
function system_tpl_plugin($tag_name,$tag) {
    switch ($tag_name) {
        case '$sitename':
            $result = C('SiteTitle');
            break;
        case '$inst': case '$webroot':
            $result = ROOT;
            break;
        case '$host': case '$domain':
            $result = HTTP_HOST;
            break;
        case '$ver': case '$version':
            $result = LAZY_VERSION;
            break;
        case '$theme': case '$templet': case '$template':
            $result = ROOT.system_themes_path();
            break;
        case '$lang': case '$language':
            $result = C('Language');
            break;
        case '$cms': case '$lazycms':
            $result = '<span id="lazycms">Powered by: <a href="http://lazycms.com/" style="font-weight:bold" target="_blank" title="LazyCMS">LazyCMS</a> '.LAZY_VERSION.'</span>';
            break;
        case '$guide':
            $name  = tpl_get_attr($tag,'name');
            $guide = tpl_get_var('guide');
            if (!$name) $name = __('Home');
            $name = esc_html($name);
            if ($guide) {
                $result = '<a href="'.ROOT.'" title="'.$name.'">'.$name.'</a> &gt;&gt; '.$guide;
            } else {
                $result = '<a href="'.ROOT.'" title="'.$name.'">'.$name.'</a> &gt;&gt; '.tpl_get_var('title');
            }
            break;
        case '$jquery':
            $version = tpl_get_attr($tag,'ver');
            $version = $version ? $version : '1.4.2';
            $result  = 'http://ajax.googleapis.com/ajax/libs/jquery/'.$version.'/jquery.min.js';
            break;
        case '$keywords': case '$keyword':
            $result = tpl_get_var('keywords');
            break;
        case '$description':
            $result = tpl_get_var('description');
            if (!$result) $result = tpl_get_var('title');
            break;
        case '$content':
            // 关键词链接地址
            $link   = tpl_get_attr($tag,'link');
            if ($link) {
                $link = str_replace(array('[$inst]','[$webroot]'),ROOT,$link);
                $link = str_replace(array('[$host]','[$domain]'),HTTP_HOST,$link);
                $link = str_replace(array('[$theme]','[$templet]','[$template]'),ROOT.system_themes_path(),$link);
            } else {
                $link = ROOT.'tags.php?q=$';
            }
            // 关键词匹配最大数
            $number = tpl_get_attr($tag,'tags');
            if (strpos($number,'-') !== false) {
                $range = explode('-', trim($number,'-')); sort($range);
                $tag_min = $range[0]; $tag_max = $range[1];
            } else {
                $tag_min = $tag_max = $number;
            }
            $tag_min = validate_is($tag_min,VALIDATE_IS_NUMERIC) ? $tag_min : 10;
            $tag_max = validate_is($tag_max,VALIDATE_IS_NUMERIC) ? $tag_max : 10;
            // 文章内容
            $content = tpl_get_var('content');
            if ($content) {
                $dicts = term_gets();
                if (!empty($dicts)) {
                    require_file(COM_PATH.'/system/keyword.php');
                    $splitword = new keyword($dicts);
                    $content   = $splitword->tags($content, $link, mt_rand($tag_min,$tag_max));
                }
            }
            $result = $content;
            break;
        default:
            $result = null;
            break;
    }
    return $result;
}
/**
 * 处理列表
 *
 * @param  $tag_name
 * @param  $tag
 * @return
 */
function system_tpl_list_plugin($tag_name,$tag,$block) {
    if (!instr($tag_name,'post,list')) return null;
    // 实例化模版对象
    $tpl = new Template();
    // 列表类型
    $type = $tpl->get_attr($tag,'type');
    // 类型为必填
    if (!$type) return null;
    // 分类ID
    $sortid = $tpl->get_attr($tag,'sortid');
    // 显示条数
    $number = $tpl->get_attr($tag,'number');
    // 斑马线实现
    $zebra  = $tpl->get_attr($tag,'zebra');
    // 校验数据
    $sortid = validate_is($sortid,VALIDATE_IS_LIST) ? $sortid : null;
    $number = validate_is($number,VALIDATE_IS_NUMERIC) ? $number : 10;
    $zebra  = validate_is($zebra,VALIDATE_IS_NUMERIC) ? $zebra : 0;
    // 处理
    switch ($type) {
        case 'new': case 'hot': case 'chill':
            $sortid = $sortid===null ? tpl_get_var('sortid') : $sortid;
            $where  = $sortid ? " AND `tr`.`taxonomyid` IN({$sortid})" : '';
            switch ($type) {
                case 'new'  : $order = 'ORDER BY `p`.`postid` DESC'; break;
                case 'hot'  : $order = 'ORDER BY `p`.`views` DESC, `p`.`postid` DESC'; break;
                case 'chill': $order = 'ORDER BY `p`.`views` ASC, `p`.`postid` DESC'; break;
            }
            $sql = sprintf("SELECT DISTINCT(`p`.`postid`) FROM `#@_post` AS `p` RIGHT JOIN `#@_term_relation` AS `tr` ON `p`.`postid`=`tr`.`objectid` WHERE `p`.`type`='post' %s %s LIMIT %d;",$where,$order,$number);
            break;
        case 'related':
            $_keywords = tpl_get_var('_keywords');
            if ($_keywords) {
                $postid = tpl_get_var('postid');
                $ids = implode(',', $_keywords);
                $sql = sprintf("SELECT DISTINCT(`p`.`postid`) FROM `#@_post` AS `p` RIGHT JOIN `#@_term_relation` AS `tr` ON `p`.`postid`=`tr`.`objectid` WHERE `p`.`type`='post' AND `tr`.`taxonomyid` IN(%s) AND `p`.`postid`<>%d LIMIT %d;",$ids,$postid,$number);
            } else {
                $sql = null;
            }
            break;
        default:
            $sql = null;
    }
    $result = null;
    if ($sql) {
        $db = get_conn(); $i = 0;
        $rs = $db->query($sql);
        $inner = $tpl->get_block_inner($block);
        while ($data = $db->fetch($rs)) {
            $post = post_get($data['postid']);
            $post['sort'] = taxonomy_get($post['sortid']);
            $post['path'] = post_get_path($post['sortid'],$post['path']);
            $post['keywords'] = post_get_keywords($post['keywords']);
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
                'sortid'   => $post['sortid'],
                'userid'   => $post['userid'],
                'author'   => $post['author'],
                'views'    => '<script type="text/javascript" src="'.ROOT.'common/gateway.php?func=post_views&postid='.$post['postid'].'"></script>',
                'digg'     => $post['digg'],
                'title'    => $post['title'],
                'path'     => ROOT.$post['path'],
                'content'  => $content,
                'date'     => $post['datetime'],
                'edittime' => $post['edittime'],
                'keywords' => $post['keywords'],
                'description' => $post['description'],
            );
            // 设置分类变量
            if (isset($post['sort'])) {
                $vars['sortname'] = $post['sort']['name'];
                $vars['sortpath'] = ROOT.$post['sort']['path'].'/';
            }
            $tpl->clean();
            $tpl->set_var($vars);
            // 设置自定义字段
            if (isset($post['meta'])) {
                foreach((array)$post['meta'] as $k=>$v) {
                    $tpl->set_var('model.'.$k, $v);
                }
            }
            // 解析二级内嵌标签
            if (isset($block['sub'])) {
                foreach ($block['sub'] as $sblock) {
                    $sblock['name'] = strtolower($sblock['name']);
                    switch($sblock['name']) {
                        // TODO 解析图片标签
                        case 'images':
                            $inner = str_replace($sblock['tag'],'',$inner);
                            break;
                    }
                }
            }
            $result.= $tpl->parse($inner);
            $i++;
        }
    }
    return $result;
}
/**
 * 标签
 *
 * @param string $tag
 * @return mixed
 */
function system_tags($tag) {
    $db = get_conn();
    $inner  = $b_guid = '';
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
                    'sortid'   => $post['sortid'],
                    'userid'   => $post['userid'],
                    'author'   => $post['author'],
                    'title'    => $post['title'],
                    'views'    => '<script type="text/javascript" src="'.ROOT.'common/gateway.php?func=post_views&postid='.$post['postid'].'"></script>',
                    'digg'     => $post['digg'],
                    'path'     => ROOT.$post['path'],
                    'content'  => $content,
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
    return tpl_parse($html);    
}

if (!function_exists('system_category_guide')) :
/**
 * 生成导航
 *
 * @param int $sortid
 * @return string
 */
function system_category_guide($sortid) {
    if (empty($sortid)) return ; $result = '';
    if ($taxonomy = taxonomy_get($sortid)) {
        $result = '<a href="'.ROOT.$taxonomy['path'].'/" title="'.esc_html($taxonomy['name']).'">'.esc_html($taxonomy['name']).'</a>';
        if ($taxonomy['parent']) {
            $result = system_category_guide($taxonomy['parent'])." &gt;&gt; ".$result;
        }
    }
    return $result;
}
endif;
/**
 * 查询模版路径
 *
 * @return string
 */
function system_themes_path() {
    return TEMPLATE.'/'.C('Template');
}
/**
 * rewrite
 *
 * @return mixed|string
 */
function system_gateway_rewrite() {
    // 获取参数
    $path  = isset($_GET['path']) ? substr('/'.rtrim(trim($_GET['path']),'/'), strlen(ROOT)) : null;
    $paths = parse_path($path);
    $type  = isset($paths[0]) ? strtolower($paths[0]) : null;
    $result = '';
    switch($type) {
        case 'tags': case 'tags.php':
            $result = system_tags($_GET[$type]);
            break;
        default:
            $result = __('Restricted access!');
            break;
    }

    return $result;
}