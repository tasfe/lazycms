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
defined('COM_PATH') or die('Restricted access!');

/**
 * 添加发布进程
 *
 * @param string $name
 * @param string $func  callback function args is publish table row.
 * @param array $args
 * @return int
 */
function publish_add($name,$func,$args=array()) {
    return get_conn()->insert('#@_publish',array(
       'name'  => $name,
       'func'  => $func,
       'args'  => serialize($args),
       'state' => 0,
    ));
}
/**
 * 更新发布进程
 *
 * @param int $pubid
 * @param array $data
 * @return int
 */
function publish_edit($pubid,$data) {
    if (empty($data)) return false;
    return get_conn()->update('#@_publish', (array)$data, array('pubid' => $pubid));
}
/**
 * 检查是否有需要生成的进度
 *
 * @return int
 */
function publish_check_process() {
    return get_conn()->result("SELECT COUNT(`pubid`) FROM `#@_publish` WHERE (`state`=0 OR `state`=1);");
}
/**
 * 执行发布
 *
 * @return bool|mixed
 */
function publish_exec() {
    $db = get_conn();
    // 取出未执行进程，开始执行
    $rs = $db->query("SELECT * FROM `#@_publish` WHERE (`state`=0 OR `state`=1) ORDER BY `pubid` ASC LIMIT 1 OFFSET 0;");
    if ($data = $db->fetch($rs)) {
        if (!function_exists($data['func'])) {
            $sets = array('state' => 2);
            publish_edit($data['pubid'],$sets);
            return array_merge($data,$sets);
        }
        $args = unserialize($data['args']);
        unset($data['args']); array_unshift($args,$data);
        return call_user_func_array($data['func'], $args);
    }
    return false;
}
/**
 * 生成所有页面
 *
 * @param  $data
 * @return bool
 */
function publish_posts($data,$mode='all'){
    $db = get_conn(); $where = '';
    $sql = 'SELECT `postid` FROM `#@_post`';
    // 生成所有页面
    if ($mode == 'pages') {
        $where = "WHERE `type`='page'";
        $sql  .= $where;
    }
    // 生成所有文章
    elseif($mode == 'posts') {
        $where = "WHERE `type`='post'";
        $sql  .= $where;
    }
    // 指定分类文章生成
    elseif (is_array($mode) && !empty($mode)) {
        $sql = sprintf("SELECT DISTINCT(`p`.`postid`) FROM `#@_post` AS `p` LEFT JOIN `#@_term_relation` AS `tr` ON `p`.`postid`=`tr`.`objectid` WHERE `tr`.`taxonomyid` IN(%s)",implode(',',$mode));
    }

    // 总数小于等于0时，统计总数并保存
    if (isset($data['total']) && 0 >= $data['total'] && $data['state']==0) {
        if (is_array($mode) && !empty($mode)) {
            $count_sql = sprintf("SELECT COUNT(DISTINCT(`p`.`postid`)) FROM `#@_post` AS `p` LEFT JOIN `#@_term_relation` AS `tr` ON `p`.`postid`=`tr`.`objectid` WHERE `tr`.`taxonomyid` IN(%s)",implode(',',$mode));
        } else {
            $count_sql = "SELECT COUNT(`postid`) FROM `#@_post` {$where};";
        }
        $total = $db->result($count_sql);
        // 没有任何文章需要生成，直接结束
        if (0 >= $total) {
            $sets = array('state' => 2);
            publish_edit($data['pubid'],$sets);
            return array_merge($data,$sets);
        }
        // 更新需要生成的文档总数
        publish_edit($data['pubid'],array(
            'total'     => $total,
            'state'     => 1,
        ));
    }
    $length = 0;
    $rs = $db->query("{$sql} LIMIT 100 OFFSET %d;",$data['complete']);
    while ($row = $db->fetch($rs)) {
        post_create($row['postid']);
        $length++;
    }
    // 生成结束
    if ($length === 0) {
        $sets = array(
            'elapsetime' => $data['elapsetime'] + micro_time(true) - __BEGIN__,
            'state'      => 2,
        );
        publish_edit($data['pubid'],$sets);
    } else {
        // 更新进度
        $sets = array(
            'complete'   => $data['complete'] + $length,
            'elapsetime' => $data['elapsetime'] + micro_time(true) - __BEGIN__,
        );
        publish_edit($data['pubid'],$sets);
    }
    return array_merge($data,$sets);
}
/**
 * 生成列表
 *
 * @param array $data
 * @param array $listids
 * @param bool $make_post 是否生成文章
 * @param int $listid
 * @return array
 */
function publish_lists($data,$listids=null,$make_post=false,$listid=0) {
    if (isset($data['total']) && 0 >= $data['total'] && $data['state']==0) {
        $db = get_conn(); $lists = array();
        $taxonomy_list = $listids===null ? taxonomy_get_list('category') : $listids;
        // 计算要生成的总页数
        foreach($taxonomy_list as $taxonomyid) {
            if ($taxonomy = taxonomy_get($taxonomyid)) {
                // 载入模版
                $html = tpl_loadfile(ABS_PATH.'/'.system_themes_path().'/'.$taxonomy['list']);
                // 标签块信息
                $block  = tpl_get_block($html,'post,list','list');
                if ($block) {
                    // 每页条数
                    $number = tpl_get_attr($block['tag'],'number');
                    // 文章总数
                    $posts = $db->result(sprintf("SELECT COUNT(`objectid`) FROM `#@_term_relation` WHERE `taxonomyid`=%d;", esc_sql($taxonomyid)));
                    // 总页数
                    $pages = ceil($posts/$number); $pages = ((int)$pages == 0) ? 1 : $pages;
                    // 要生成的文档总数
                    $lists[$taxonomyid] = $pages;
                }
            }
        }
        $total = array_sum($lists);
        // 没有任何文章需要生成，直接结束
        if (0 >= $total) {
            $sets = array('state' => 2);
            publish_edit($data['pubid'],$sets);
            return array_merge($data,$sets);
        }
        $keys = array_keys($lists);
        // 更新需要生成的文档总数
        $sets = array(
            'total'      => $total,
            'state'      => 1,
            'args'       => array(
                'lists'  => $lists,
                'mpost'  => $make_post,
                'listid' => isset($keys[0]) ? $keys[0] : 0,
            )
        );
        $listids = $sets['args']['lists'];
        $listid  = $sets['args']['listid'];
        publish_edit($data['pubid'],$sets);
        $data = array_merge($data,$sets);
    }
    // 正在生成的分类ID
    $generated = 0;
    // 计算应该生成第几页
    foreach((array)$listids as $id=>$v) {
        if ($id == $listid) break;
        $generated+= $v;
    }
    $page = $data['complete'] - $generated + 1;
    // 生成成功
    if (taxonomy_create($listid,$page,$make_post)) {
        // 更新进度
        $sets = array(
            'complete'   => ++$data['complete'],
            'elapsetime' => $data['elapsetime'] + micro_time(true) - __BEGIN__,
        );
        publish_edit($data['pubid'],$sets);
    }
    // 当前分类生成结束
    else {
        // 发布google sitemaps
        publish_list_sitemaps($listid);
        // 切换到下一个分类
        $keys = array_keys($listids);
        $key  = array_search($listid, $keys) + 1;
        if (isset($keys[$key])) {
            $sets = array(
                'elapsetime' => $data['elapsetime'] + micro_time(true) - __BEGIN__,
                'args'       => array(
                    'lists'  => $listids,
                    'mpost'  => $make_post,
                    'listid' => $keys[$key],
                )
            );
            publish_edit($data['pubid'],$sets);
        }
        // 全部生成结束
        else {
            $sets = array(
                'elapsetime' => $data['elapsetime'] + micro_time(true) - __BEGIN__,
                'state'      => 2,
            );
            // 生成google sitemaps index
            publish_sitemap_index();
            publish_edit($data['pubid'],$sets);
        }

    }
    return array_merge($data,$sets);
}
/**
 * 生成 sitemap index
 *
 * @return int
 */
function publish_sitemap_index() {
    $urls = ''; publish_page_sitemaps();
    $urls.= sprintf('<sitemap><loc>%1$s</loc><lastmod>%2$s</lastmod></sitemap>', HTTP_HOST.ROOT.'sitemap-pages.xml', W3cDate());
    $lists = taxonomy_get_list('category');
    foreach ($lists as $taxonomyid) {
        $taxonomy = taxonomy_get($taxonomyid);
        $urls.= sprintf('<sitemap><loc>%1$s</loc><lastmod>%2$s</lastmod></sitemap>', HTTP_HOST.ROOT.xmlencode($taxonomy['path']).'/sitemap.xml', W3cDate());
    }
    $xml = system_sitemaps('sitemapindex', $urls);
    $map = ABS_PATH.'/sitemaps.xml';
    mkdirs(dirname($map));
    return file_put_contents($map, $xml);
}
/**
 * 发布单页面 googlemaps
 *
 * @return int
 */
function publish_page_sitemaps() {
    $db = get_conn(); $urls = '';
    $rs = $db->query("SELECT `postid` FROM `#@_post` WHERE `type`='page' ORDER BY `postid` DESC LIMIT 50000 OFFSET 0;");
    while ($data = $db->fetch($rs)) {
        $post = post_get($data['postid']);
        $post['path'] = post_get_path($post['listid'],$post['path']);
        $path = HTTP_HOST.ROOT.$post['path'];
        $urls.= sprintf('<url><loc>%1$s</loc><changefreq>daily</changefreq><lastmod>%2$s</lastmod><priority>0.9</priority></url>', xmlencode($path), W3cDate($post['edittime']?$post['edittime']:$post['datetime']));
    }
    $xml = system_sitemaps('urlset', $urls);
    $map = ABS_PATH.'/sitemap-pages.xml';
    mkdirs(dirname($map));
    return file_put_contents($map, $xml);
}
/**
 * 发布列表 google sitemaps
 *
 * @param int $listid
 * @return int
 */
function publish_list_sitemaps($listid) {
    $db  = get_conn(); $taxonomy = taxonomy_get($listid);
    // 载入模版
    $html = tpl_loadfile(ABS_PATH.'/'.system_themes_path().'/'.$taxonomy['list']);
    // 标签块信息
    $block = tpl_get_block($html,'post,list','list'); $pages = 1; $urls = '';
    if ($block) {
        // 每页条数
        $number = tpl_get_attr($block['tag'], 'number');
        // 文章总数
        $count  = $db->result(sprintf("SELECT COUNT(`objectid`) FROM `#@_term_relation` WHERE `taxonomyid`=%d;", esc_sql($listid)));
        // 总页数
        $pages = ceil($count/$number); $pages = ((int)$pages == 0) ? 1 : $pages;
    }
    // 分页
    for ($page=1; $page<=$pages; $page++) {
        $file = $page==1 ? '' : 'index'.$page.C('HTML-Ext');
        $path = HTTP_HOST.ROOT.$taxonomy['path'].'/'.$file;
        $urls.= sprintf('<url><loc>%1$s</loc><changefreq>daily</changefreq><lastmod>%2$s</lastmod><priority>0.5</priority></url>', xmlencode($path), W3cDate());
    }
    // 文章页
    $rs  = $db->query("SELECT `postid` FROM `#@_post` WHERE `listid`=%d AND `type`='post' ORDER BY `postid` DESC LIMIT %d OFFSET 0;", $listid, 50000-$pages);
    while ($data = $db->fetch($rs)) {
        $post = post_get($data['postid']);
        $post['path'] = post_get_path($post['listid'],$post['path']);
        // 文件不保存在本目录下的文件不加入索引
        if (strncmp($post['path'], $taxonomy['path'], strlen($taxonomy['path'])) !== 0) continue;
        $path = HTTP_HOST.ROOT.$post['path'];
        $urls.= sprintf('<url><loc>%1$s</loc><changefreq>weekly</changefreq><lastmod>%2$s</lastmod><priority>0.8</priority></url>', xmlencode($path), W3cDate($post['edittime']?$post['edittime']:$post['datetime']));
    }
    $xml = system_sitemaps('urlset', $urls);
    $map = ABS_PATH.'/'.$taxonomy['path'].'/sitemap.xml';
    mkdirs(dirname($map));
    return file_put_contents($map, $xml);
}
/**
 * 删除进程
 *
 * @param  $listids
 * @return bool
 */
function publish_delete($listids) {
    if (empty($listids)) return false;
    $listids = is_array($listids) ? implode(',', $listids) : $listids;
    return get_conn()->query("DELETE FROM `#@_publish` WHERE `pubid` IN({$listids})");
}
/**
 * 清空进程
 *
 * @return void
 */
function publish_empty() {
    return get_conn()->truncate('#@_publish');
}