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
 * 添加文章
 *
 * @param  $title
 * @param  $content
 * @param  $path
 * @param  $data
 * @return array
 */
function post_add($title,$content,$path,$data=null) {
    $db = get_conn();
    $postid = $db->insert('#@_post',array(
       'title'   => $title,
       'content' => $content,
       'path'    => $path,
    ));
    $data['path'] = $path;
    return post_edit($postid,$data);
}
/**
 * 更新文章信息
 *
 * @param  $postid
 * @param  $data
 * @return array
 */
function post_edit($postid,$data) {
    $db = get_conn();
    $postid = intval($postid);
    $post_rows = $meta_rows = array();
    if ($post = post_get($postid)) {
        $data = is_array($data) ? $data : array();
        // 格式化路径
        if (isset($data['path'])) {
            $data['path'] = format_path($data['path'],array(
                'ID'  => $postid,
                'PY'  => $post['title'],
                'MD5' => $postid,
            ));
            // 删除旧文件
            if ($data['path']!=$post['path'] && file_exists_case(ABS_PATH.'/'.$post['path'])) {
                unlink(ABS_PATH.'/'.$post['path']);
            }
        }
        // 更新分类关系
        $categories = array();
        if (isset($data['category'])) {
            $categories = $data['category'];
            taxonomy_make_relation('category',$postid,$data['category']);
        }
        // 分析关键词
        if (isset($data['keywords'])) {
            $taxonomies = array();
            if ($data['keywords']) {
                if (is_array($data['keywords'])) {
                    $keywords = $data['keywords'];
                } else {
                    // 替换掉全角逗号和全角空格
                    $data['keywords'] = str_replace(array('，','　'),array(',',' '),$data['keywords']);
                    // 先用,分隔关键词
                    $keywords = explode(',',$data['keywords']);
                    // 分隔失败，使用空格分隔关键词
                    if (count($keywords)==1) $keywords = explode(' ',$data['keywords']);
                }
                // 移除重复的关键词
                $keywords = array_unique($keywords);
                // 去除关键词两边的空格，转义HTML
                array_walk($keywords,create_function('&$s','$s=esc_html(trim($s));'));
                // 强力插入关键词
                foreach($keywords as $key) {
                    $taxonomy = taxonomy_add('post_tag',$key);
                    $taxonomies[] = $taxonomy['taxonomyid'];
                }
            }
            // 创建关系
            taxonomy_make_relation('post_tag',$postid,$taxonomies);

        }
        unset($data['category'],$data['keywords']);
        $meta_rows = empty($data['meta']) ? array() : $data['meta']; unset($data['meta']);
        $post_rows = $data; $data['meta'] = $meta_rows; $data['category'] = $categories;

        // 更新数据
        if (!empty($post_rows)) {
            $db->update('#@_post',$post_rows,array('postid' => $postid));
        }
        if (!empty($meta_rows)) {
            post_edit_meta($postid,$meta_rows);
        }
        // 清理缓存
        post_clean_cache($postid);
        return array_merge($post,$data);
    }
    return null;
}
/**
 * 取得post
 *
 * @param  $res
 * @param  $sql
 * @param int $page
 * @param int $size
 * @return array
 */
function post_gets($sql, $page=0, $size=10){
    $db = get_conn(); $posts = array();
    $count_sql = preg_replace('/SELECT (.+) FROM/iU','SELECT COUNT(*) FROM',$sql,1);
    $count_sql = preg_replace('/ORDER BY (.+) (ASC|DESC)/i','',$count_sql,1);
    $total = $db->result($count_sql);
    $pages = ceil($total/$size);
    $pages = ((int)$pages == 0) ? 1 : $pages;
    if ((int)$page < (int)$pages) {
        $length = $size;
    } elseif ((int)$page >= (int)$pages) {
        $length = $total - (($pages-1) * $size);
    }
    if ((int)$page > (int)$pages) $page = $pages;
    $sql.= sprintf(' LIMIT %d OFFSET %d;',$size,($page-1)*$size);
    $res = $db->query($sql);
    while ($post = $db->fetch($res)) {
        $post['model'] = model_get_bycode($post['model']);
        $post['category'] = taxonomy_get_relation('category',$post['postid'],$rel_ids);
        if ($post['sortid'] && !in_array($post['sortid'],$rel_ids)) {
            array_unshift($post['category'],taxonomy_get_byid($post['sortid']));
        }
        $posts[] = $post;
    }
    return array(
        'page'   => $page,
        'size'   => $size,
        'total'  => $total,
        'pages'  => $pages,
        'length' => $length,
        'posts'  => $posts,
    );
}
/**
 * 查找指定的文章
 *
 * @param  $postid
 * @return array
 */
function post_get($postid) {
    $db = get_conn();
    $ckey  = sprintf('post.%s',$postid);
    $value = fcache_get($ckey);
    if (!empty($value)) return $value;

    $rs = $db->query("SELECT * FROM `#@_post` WHERE `postid`=%d LIMIT 0,1;",$postid);
    // 判断文章是否存在
    if ($post = $db->fetch($rs)) {
        // 取得分类关系
        $post['category'] = taxonomy_get_relation('category',$postid);
        $post['keywords'] = taxonomy_get_relation('post_tag',$postid);
        if ($meta = post_get_meta($post['postid'])) {
            $post['meta'] = $meta;
        }
        // 保存到缓存
        fcache_set($ckey,$post);
        return $post;
    }
    return null;
}
/**
 * 获取文章的详细信息
 *
 * @param  $postid
 * @return array
 */
function post_get_meta($postid) {
    $db = get_conn(); $result = array(); $postid = intval($postid);
    $rs = $db->query("SELECT * FROM `#@_post_meta` WHERE `postid`=%d;",$postid);
    while ($row = $db->fetch($rs)) {
        if (is_need_unserialize($row['type'])) {
           $result[$row['key']] = unserialize($row['value']);
        } else {
           $result[$row['key']] = $row['value'];
        }
    }
    return $result;
}
/**
 * 填写文章的详细信息
 *
 * @param  $postid
 * @param  $data
 * @return bool
 */
function post_edit_meta($postid,$data) {
    $db = get_conn(); $postid = intval($postid);
    if (!is_array($data)) return false;
    foreach ($data as $key=>$value) {
        // 获取变量类型
        $var_type = gettype($value);
        // 判断是否需要序列化
        $value = is_need_serialize($value) ? serialize($value) : $value;
        // 查询数据库里是否已经存在
        $length = (int) $db->result(vsprintf("SELECT COUNT(*) FROM `#@_post_meta` WHERE `postid`=%d AND `key`='%s';",array($postid,esc_sql($key))));
        // update
        if ($length > 0) {
            $db->update('#@_post_meta',array(
                'value' => $value,
                'type'  => $var_type,
            ),array(
                'postid' => $postid,
                'key'    => $key,
            ));
        }
        // insert
        else {
            // 保存到数据库里
            $db->insert('#@_post_meta',array(
                'postid' => $postid,
                'key'    => $key,
                'value'  => $value,
                'type'   => $var_type,
            ));
        }
    }
    return true;
}
/**
 * 清理文章缓存
 *
 * @param  $postid
 * @return bool
 */
function post_clean_cache($postid) {
    return fcache_delete('post.'.$postid);
}
/**
 * 删除一片文章
 *
 * @param  $postid
 * @return bool
 */
function post_delete($postid) {
    $db = get_conn();
    $postid = intval($postid);
    if (!$postid) return false;
    if ($post = post_get($postid)) {
        // 删除文件
        if (file_exists_case(ABS_PATH.'/'.$post['path'])) {
            if (!unlink(ABS_PATH.'/'.$post['path'])) {
                return false;
            }
        }
        // 删除分类关系
        $relations = taxonomy_get_relation('category',$postid);
        foreach($relations as $r) {
            taxonomy_delete_relation($postid,$r['taxonomyid']);
        }
        // 删除关键词关系
        $relations = taxonomy_get_relation('post_tag',$postid);
        foreach($relations as $r) {
            taxonomy_delete_relation($postid,$r['taxonomyid']);
        }
        $db->delete('#@_post_meta',array('postid' => $postid));
        $db->delete('#@_post',array('postid' => $postid));
        // 清理缓存
        post_clean_cache($postid);
        return true;
    }
    return false;
}
/**
 * 生成页面
 *
 * @param  $postid
 * @return bool
 */
function post_create($postid) {
    $db = get_conn();
    $postid = intval($postid);
    if (!$postid) return false;
    if ($post = post_get($postid)) {
        $model = model_get_bycode($post['model']);
        // 使用模型设置
        if (empty($post['template'])) {
            $post['template'] = $model['page'];
        }
        // 处理关键词
        if (is_array($post['keywords'])) {
            $keywords = array();
            foreach($post['keywords'] as $v) {
                $keywords[] = $v['name'];
            }
            $post['keywords'] = implode(',', $keywords);
        }
        $html = tpl_loadfile(ABS_PATH.'/'.system_themes_path().'/'.esc_html($post['template']));
                tpl_clean();
                tpl_value(array(
                    'postid'   => $post['postid'],
                    'path'     => WEB_ROOT.$post['path'],
                    'title'    => $post['title'],
                    'content'  => $post['content'],
                    'datetime' => $post['datetime'],
                    'keywords' => $post['keywords'],
                    'description' => $post['description'],
                ));
                // 设置自定义字段
                if (isset($post['meta'])) {
                    foreach((array)$post['meta'] as $k=>$v) {
                        tpl_value('model.'.$k, $v);
                    }
                }
        
        $html = tpl_parse($html);
        // 生成的文件路径
        $file = ABS_PATH.'/'.$post['path'];
        // 创建目录
        mkdirs(dirname($file));
        // 保存文件
        file_put_contents($file,$html);
    }
    return true;
}
