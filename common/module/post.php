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
    // 删除添加失败的文章
    $db->delete('#@_post',array('datetime' => 0)); 
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
 * @param int $postid
 * @param array $data
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
            $data['path'] = path_format($data['path'],array(
                'ID'  => $postid,
                'PY'  => $post['title'],
                'MD5' => $postid,
            ));
            // 删除旧文件
            if ($data['path'] != $post['path']) {
                post_process($post,'path');
                if (is_file(ABS_PATH.'/'.$post['path']))
                    unlink(ABS_PATH.'/'.$post['path']);
            }
        }
        $category = isset($data['category']) ? $data['category'] : null;
        $keywords = isset($data['keywords']) ? $data['keywords'] : null;
        unset($data['category'],$data['keywords']);
        $meta_rows = empty($data['meta']) ? array() : $data['meta']; unset($data['meta']);
        $post_rows = $data; $data['meta'] = $meta_rows; $data['category'] = $category;

        // 更新数据
        if (!empty($post_rows)) {
            $db->update('#@_post',$post_rows,array('postid' => $postid));
        }
        if (!empty($meta_rows)) {
            post_edit_meta($postid,$meta_rows);
        }
        // 更新分类关系
        if ($data['category']) {
            taxonomy_make_relation('category',$postid,$data['category']);
        }
        // 分析关键词
        if ($keywords) {
            $taxonomies = array();
            if (!is_array($keywords)) {
                // 替换掉全角逗号和全角空格
                $keywords = str_replace(array('，','　'),array(',',' '),$keywords);
                // 先用,分隔关键词
                $keywords = explode(',',$keywords);
                // 分隔失败，使用空格分隔关键词
                if (count($keywords)==1) $keywords = explode(' ',$keywords[0]);
            }
            // 移除重复的关键词
            $keywords = array_unique($keywords);
            // 去除关键词两边的空格，转义HTML
            array_walk($keywords,create_function('&$s','$s=esc_html(trim($s));'));
            // 强力插入关键词
            foreach($keywords as $key) {
                $taxonomies[] = taxonomy_add_tag($key);
            }
            $data['keywords'] = implode(',',$keywords);
            // 创建关系
            taxonomy_make_relation('post_tag',$postid,$taxonomies);
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
 * @param string $sql       sql语句
 * @param int $page         当前页
 * @param int $size         每页大小
 * @param string $option    文章变量处理选项 see function post_process()
 * @return array
 */
function post_gets($sql, $page=1, $size=10,$option='model,path,category',$is_return=true){
    $db = get_conn(); $posts = array();
    $page = $page<1 ? 1  : $page;
    $size = $size<1 ? 10 : $size;
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
    if ($is_return && (int)$page>(int)$pages) $page = $pages;
    $sql.= sprintf(' LIMIT %d OFFSET %d;',$size,($page-1)*$size);
    $res = $db->query($sql);
    while ($post = $db->fetch($res)) {
        $post = post_get($post['postid']);
        post_process($post,$option);
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
 * 判断路径是否存在
 *
 * @param  $postid
 * @param  $path    必须是format_path()格式化过的路径
 * @return bool
 */
function post_path_exists($postid,$path) {
    if (strpos($path,'%ID')!==false && strpos($path,'%MD5')!==false) return false;
    $db = get_conn(); $db->delete('#@_post',array('datetime' => 0)); // 删除添加失败的文章
    if ($postid) {
        $sql = sprintf("SELECT COUNT(`postid`) FROM `#@_post` WHERE `path`='%s' AND `postid`<>'%d';", esc_sql($path), esc_sql($postid));
    } else {
        $sql = sprintf("SELECT COUNT(`postid`) FROM `#@_post` WHERE `path`='%s';",esc_sql($path));
    }
    return !($db->result($sql) == 0);
}
/**
 * 查找指定的文章
 *
 * @param int $postid
 * @param bool $is_add 是否添加时调用
 * @return array
 */
function post_get($postid) {
    $db   = get_conn();
    $ckey = sprintf('post.%d',$postid);
    $post = fcache_get($ckey);
    if ($post !== null) return $post;

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
 * 处理文章
 *
 * @param array &$post
 * @param string $option model,path,category,template,keywords,sortid
 * @return void
 */
function post_process(&$post,$option=null) {
    // 解析分类
    if (instr('sortid',$option) && $post['sortid']>0) {
        $post['sort'] = taxonomy_get($post['sortid']);
    }
    // 解析模型数据
    if (instr('model',$option) && !empty($post['model'])) {
        $post['model'] = model_get_bycode($post['model']);
    }
    // 查询模版，文章设置->分类设置->模型设置
    if (instr('template',$option) && empty($post['template'])) {
        // 使用分类设置
        if ($post['sortid'] > 0) {
            $taxonomy = taxonomy_get($post['sortid']);
            $post['template'] = $taxonomy['page'];
        }
        // 使用模型设置
        if (empty($post['template'])) {
            $model = model_get_bycode($post['model']);
            $post['template'] = $model['page'];
        }
    }
    // 处理分类数据
    if (instr('category',$option) && is_array($post['category'])) {
        $categories = array();
        foreach($post['category'] as $taxonomyid) {
            $categories[$taxonomyid] = taxonomy_get($taxonomyid);
        }
        $post['category'] = $categories;
    }
    // 处理关键词
    if (instr('keywords',$option) && is_array($post['keywords'])) {
        $keywords = array();
        foreach($post['keywords'] as $taxonomyid) {
            $taxonomy = taxonomy_get($taxonomyid);
            $keywords[] = $taxonomy['name'];
        }
        $post['keywords'] = implode(',', $keywords);
    }
    // 处理生成路径
    if (instr('path',$option)) {
        if (strncmp($post['path'],'/',1) === 0) {
            $post['path'] = ltrim($post['path'], '/');
        } elseif ($post['sortid'] > 0) {
            $taxonomy = taxonomy_get($post['sortid']);
            if (isset($taxonomy['path'])) {
                $post['path'] = $taxonomy['path'].'/'.$post['path'];
            }

        }
    }
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
        post_process($post,'path');
        // 删除文件
        if (is_file(ABS_PATH.'/'.$post['path'])) {
            if (!unlink(ABS_PATH.'/'.$post['path'])) {
                return false;
            }
        }
        // 删除分类关系
        foreach($post['category'] as $taxonomyid) {
            taxonomy_delete_relation($postid,$taxonomyid);
        }
        // 删除关键词关系
        foreach($post['keywords'] as $taxonomyid) {
            taxonomy_delete_relation($postid,$taxonomyid);
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
    $postid = intval($postid);
    if (!$postid) return false;
    if ($post = post_get($postid)) {
        // 处理文章
        post_process($post,'path,keywords,template');
        // 加载模版
        $html = tpl_loadfile(ABS_PATH.'/'.system_themes_path().'/'.esc_html($post['template']));
        tpl_clean();
        tpl_vars(array(
            'postid'   => $post['postid'],
            'datetime' => $post['datetime'],
            'keywords' => $post['keywords'],
            'description' => $post['description'],
        ));
        // 设置自定义字段
        if (isset($post['meta'])) {
            foreach((array)$post['meta'] as $k=>$v) {
                tpl_vars('model.'.$k, $v);
            }
        }
        // 文章分页
        if ($post['content'] && strpos($post['content'],'<!--pagebeak-->')!==false) {
            $contents = explode('<!--pagebeak-->',$post['content']);
            // 总页数
            $pages = count($contents);
            if (($pos=strrpos($post['path'],'.')) !== false) {
                $basename = substr($post['path'],0,$pos);
                $suffix   = substr($post['path'],$pos);
            } else {
                $basename = $post['path'];
                $suffix   = '';
            }
            foreach($contents as $i=>$content) {
                $page = $i + 1;
                if ($page == 1) {
                    $path  = $basename.$suffix;
                    $title = $post['title'];
                } else {
                    $path  = $basename.$page.$suffix;
                    $title = $post['title'].' ('.$page.')';
                }

                tpl_vars(array(
                    'title'   => $title,
                    'content' => $content,
                    'path'    => WEB_ROOT.$path,
                ));
                $pagehtml = tpl_parse($html);
                // 解析分页标签
                if (stripos($pagehtml,'{pagelist') !== false) {
                    $pagehtml = preg_replace('/\{(pagelist)[^\}]*\/\}/isU',
                        page_list(WEB_ROOT.$basename.'$'.$suffix, $page, $pages, 1, true),
                        $pagehtml
                    );
                }
                // 生成的文件路径
                $file = ABS_PATH.'/'.$path;
                // 创建目录
                mkdirs(dirname($file));
                // 保存文件
                file_put_contents($file,$pagehtml);
            }
        }
        // 没有分页
        else {
            tpl_vars(array(
                'title'   => $post['title'],
                'content' => $post['content'],
                'path'    => WEB_ROOT.$post['path'],
            ));
            // 解析分页标签
            if (stripos($html,'{pagelist') !== false) {
                $html = preg_replace('/\{(pagelist)[^\}]*\/\}/isU','',$html);
            }
            $html = tpl_parse($html);
            // 生成的文件路径
            $file = ABS_PATH.'/'.$post['path'];
            // 创建目录
            mkdirs(dirname($file));
            // 保存文件
            return file_put_contents($file,$html);
        }
    }
    return true;
}
