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
            $result = WEB_ROOT;
            break;
        case '$host': case '$domain':
            $result = HTTP_HOST;
            break;
        case '$ver': case '$version':
            $result = LAZY_VERSION;
            break;
        case '$theme': case '$templet': case '$template':
            $result = WEB_ROOT.system_themes_path();
            break;
        case '$lang': case '$language':
            $result = C('Language');
            break;
        case '$cms': case '$lazycms':
            $result = '<span id="lazycms">Powered by: <a href="http://lazycms.com/" style="font-weight:bold" target="_blank">LazyCMS</a> '.LAZY_VERSION.'</span>';
            break;
        case '$guide':
            $name  = tpl_get_attr($tag,'name');
            $guide = tpl_get_var('guide');
            if (!$name) $name = __('Home');
            if ($guide) {
                $result = '<a href="'.WEB_ROOT.'">'.$name.'</a> &gt;&gt; '.$guide;
            } else {
                $result = '<a href="'.WEB_ROOT.'">'.$name.'</a> &gt;&gt; '.tpl_get_var('title');
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
                $link = str_replace(array('[$inst]','[$webroot]'),WEB_ROOT,$link);
                $link = str_replace(array('[$host]','[$domain]'),HTTP_HOST,$link);
                $link = str_replace(array('[$theme]','[$templet]','[$template]'),WEB_ROOT.system_themes_path(),$link);
            } else {
                $link = WEB_ROOT.'tags.php?q=$';
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
                case 'new'  : $order = 'ORDER BY `p`.`postid` DESC';
                case 'hot'  : $order = 'ORDER BY `p`.`views` DESC, `p`.`postid` DESC';
                case 'chill': $order = 'ORDER BY `p`.`views` ASC, `p`.`postid` DESC';
            }
            $sql = sprintf("SELECT DISTINCT(`p`.`postid`) FROM `#@_post` AS `p` RIGHT JOIN `#@_term_relation` AS `tr` ON `p`.`postid`=`tr`.`objectid` WHERE `p`.`type`='post' %s %s LIMIT %d;",$where,$order,$number);
            break;
        case 'related':
            $_keywords = tpl_get_var('_keywords');
            if ($_keywords) {
                $postid = tpl_get_var('postid');
                $ids = implode(',', $_keywords);
                $sql = sprintf("SELECT DISTINCT(`objectid`) AS `postid` FROM `#@_term_relation` WHERE `taxonomyid` IN(%s) AND `objectid`<>%d LIMIT %d;",$ids,$postid,$number);
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
            // 设置文章变量
            $vars = array(
                'zebra'    => ($i % ($zebra + 1)) ? '0' : '1',
                'postid'   => $post['postid'],
                'sortid'   => $post['sortid'],
                'userid'   => $post['userid'],
                'author'   => $post['author'],
                'views'    => '<script type="text/javascript" src="'.WEB_ROOT.'common/gateway.php?func=post_views&postid='.$post['postid'].'"></script>',
                'digg'     => $post['digg'],
                'title'    => $post['title'],
                'path'     => WEB_ROOT.$post['path'],
                'datetime' => $post['datetime'],
                'edittime' => $post['edittime'],
                'keywords' => $post['keywords'],
                'description' => $post['description'],
            );
            // 设置分类变量
            if (isset($post['sort'])) {
                $vars['sortname'] = $post['sort']['name'];
                $vars['sortpath'] = WEB_ROOT.$post['sort']['path'].'/';
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
 * 生成导航
 *
 * @param int $sortid
 * @return string
 */
function system_category_guide($sortid) {
    if (empty($sortid)) return ; $result = '';
    if ($taxonomy = taxonomy_get($sortid)) {
        $result = '<a href="'.WEB_ROOT.$taxonomy['path'].'/">'.esc_html($taxonomy['name']).'</a>';
        if ($taxonomy['parent']) {
            $result = system_category_guide($taxonomy['parent'])." &gt;&gt; ".$result;
        }
    }
    return $result;
}
/**
 * 查询模版路径
 *
 * @return string
 */
function system_themes_path() {
    return TEMPLATE.'/'.C('Template');
}