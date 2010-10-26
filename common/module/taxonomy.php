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
 * 取得关键词
 *
 * @param int $termid
 * @return array|null
 */
function term_get_byid($termid) {
    $db = get_conn(); $termid = intval($termid);
    $rs = $db->query("SELECT * FROM `#@_term` WHERE `termid`=%d LIMIT 0,1;",$termid);
    if ($term = $db->fetch($rs)) {
        return $term;
    }
    return null;
}
/**
 * 根据名称查找
 *
 * @param  $name
 * @return array|null
 */
function term_get_byname($name) {
    $db = get_conn();
    $rs = $db->query("SELECT * FROM `#@_term` WHERE `name`='%s' LIMIT 0,1;",$name);
    if ($term = $db->fetch($rs)) {
        return $term;
    }
    return null;
}
/**
 * 添加术语
 *
 * @param  $name
 * @return $termid
 */
function term_add($name) {
    $db = get_conn();
    $termid = $db->result(sprintf("SELECT `termid` FROM `#@_term` WHERE `name`='%s' LIMIT 0,1;",esc_sql($name)));
    if (!$termid) {
        $termid = $db->insert('#@_term',array(
            'name' => $name,
        ));
        // 清理缓存
        fcache_delete('terms.dicts');
    }
    return $termid;
}
/**
 * 根据词库取得措辞
 *
 * @param string $content
 * @param int $max_len
 * @param bool $save_other
 * @return array
 */
function term_gets($content,$max_len=8,$save_other=false) {
    $ckey  = 'terms.dicts';
    $dicts = fcache_get($ckey);
    if ($dicts === null) {
        $db = get_conn(); $dicts = array();
        // 读取关键词列表
        $rs = $db->query("SELECT `name` FROM `#@_term`");
        while ($data = $db->fetch($rs)) {
            $dicts[] = $data['name'];
        }
        fcache_set($ckey,$dicts);
    }
    require_file(COM_PATH.'/system/splitword.php');
    $splitword = new SplitWord($dicts);
    $keywords  = $splitword->get($content,$max_len,$save_other);
    if (empty($keywords)) {
        return null;
    } else {
        return $keywords;
    }
}
/**
 * 取得分类列表
 *
 * @param string $type
 * @return array
 */
function taxonomy_get_list($type='category') {
    $db = get_conn(); $result = array();
    $rs = $db->query("SELECT * FROM `#@_term_taxonomy` WHERE `type`='%s';",$type);
    while ($row = $db->fetch($rs)) {
        $result[] = $row['taxonomyid'];
    }
    return $result;
}

/**
 * 取得分类树
 *
 * @param int $parentid
 * @param string $type
 * @return array
 */
function taxonomy_get_trees($parentid=0,$type='category') {
    $result = array(); $un = array(); $parentid = intval($parentid);
    $taxonomy_list = taxonomy_get_list($type);
    foreach ($taxonomy_list as $taxonomyid) {
        $result[$taxonomyid] = taxonomy_get($taxonomyid);
    }
    // 将数组转变成树，因为使用了引用，所以不会占用太多的内存
    foreach ($result as $id => $item) {
        if ($item['parent']) {
            $result[$item['parent']]['subs'][$id] = &$result[$id];
            $un[] = $id;
        }
    }
    foreach($un as $v) unset($result[$v]);
    if ($parentid) {
        $result = isset($result[$parentid])?$result[$parentid]:array();
    }
    return $result;
}
/**
 * 检查分类目录是否存在
 *
 * @param  $taxonomyid
 * @param  $path        必须是format_path()格式化过的路径
 * @return bool
 */
function taxonomy_path_exists($taxonomyid,$path) {
    if (strpos($path,'%ID')!==false && strpos($path,'%MD5')!==false) return false;
    $db = get_conn();
    if ($taxonomyid) {
        $sql = sprintf("SELECT COUNT(`taxonomyid`) FROM `#@_term_taxonomy_meta` WHERE `key`='path' AND `value`='%s' AND `taxonomyid`<>'%d';", esc_sql($path), esc_sql($taxonomyid));
    } else {
        $sql = sprintf("SELECT COUNT(`taxonomyid`) FROM `#@_term_taxonomy_meta` WHERE `key`='path' AND `value`='%s';",esc_sql($path));
    }
    return !($db->result($sql) == 0);
}
/**
 * 取得分类信息
 *
 * @param int $taxonomyid
 * @return array|null
 */
function taxonomy_get($taxonomyid) {
    $db = get_conn(); $prefix = 'taxonomy.';
    $taxonomyid = intval($taxonomyid);
    $taxonomy   = fcache_get($prefix.$taxonomyid);
    if ($taxonomy !== null) return $taxonomy;

    $rs = $db->query("SELECT * FROM `#@_term_taxonomy` WHERE `taxonomyid`=%d LIMIT 0,1;",$taxonomyid);
    if ($taxonomy = $db->fetch($rs)) {
        if ($term = term_get_byid($taxonomy['termid'])) {
            $taxonomy = array_merge($taxonomy,$term);
        }
        if ($meta = taxonomy_get_meta($taxonomy['taxonomyid'])) {
            $taxonomy = array_merge($taxonomy,$meta);
        }
        // 保存到缓存
        fcache_set($prefix.$taxonomyid,$taxonomy);
        
        return $taxonomy;
    }
    return null;
}
/**
 * 获取分类扩展信息
 *
 * @param int $taxonomyid
 * @return array
 */
function taxonomy_get_meta($taxonomyid) {
    $db = get_conn(); $result = array(); $taxonomyid = intval($taxonomyid);
    $rs = $db->query("SELECT * FROM `#@_term_taxonomy_meta` WHERE `taxonomyid`=%d;",$taxonomyid);
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
 * 取得一个对象的分类
 *
 * @param string $type
 * @param int $objectid
 * @return array
 */
function taxonomy_get_relation($type, $objectid) {
    static $taxonomies = array(); $rel_ids = array();
    $db = get_conn(); $result = array();
    if (!isset($taxonomies[$type])) {
        $tt_ids = array();
        $rs = $db->query("SELECT `taxonomyid` FROM `#@_term_taxonomy` WHERE `type`='%s';",$type);
        while ($tt = $db->fetch($rs)) {
            $tt_ids[] = $tt['taxonomyid'];
        }
        $taxonomies[$type] = "'" . implode("', '", $tt_ids) . "'";
    }
    $in_tt_ids = $taxonomies[$type];
    $rs = $db->query("SELECT DISTINCT `tr`.`taxonomyid` AS `taxonomyid`,`tr`.`order` AS `order` FROM `#@_term_taxonomy` AS `tt` INNER JOIN `#@_term_relation` AS `tr` ON `tt`.`taxonomyid`=`tr`.`taxonomyid` WHERE `tr`.`objectid`=%d AND `tt`.`taxonomyid` IN({$in_tt_ids});",$objectid);
    while ($taxonomy = $db->fetch($rs)) {
        $result[$taxonomy['order']] = $taxonomy['taxonomyid'];
    }
    ksort($result);
    return $result;
}
/**
 * 建立分类关系
 *
 * @param  $type
 * @param  $objectid
 * @param  $taxonomies
 * @return bool
 */
function taxonomy_make_relation($type,$objectid,$taxonomies) {
    $db = get_conn(); $tt_ids = array(); $taxonomies = (array) $taxonomies;
    $rs = $db->query("SELECT `taxonomyid` FROM `#@_term_taxonomy` WHERE `type`='%s';",$type);
    while ($tt = $db->fetch($rs)) {
        $tt_ids[] = $tt['taxonomyid'];
    }
    // 取得分类差集,删除差集
    $tt_ids = array_diff($tt_ids,$taxonomies);
    $in_tt_ids = "'" . implode("', '", $tt_ids) . "'";
    // 先删除关系
    $rs = $db->query("SELECT DISTINCT `tr`.`taxonomyid` AS `taxonomyid` FROM `#@_term_taxonomy` AS `tt` INNER JOIN `#@_term_relation` AS `tr` ON `tt`.`taxonomyid`=`tr`.`taxonomyid` WHERE `tr`.`objectid`=%d AND `tt`.`taxonomyid` IN({$in_tt_ids});",$objectid);
    while ($taxonomy = $db->fetch($rs)) {
        taxonomy_delete_relation($objectid,$taxonomy['taxonomyid']);
    }
    // 然后添加分类关系
    foreach($taxonomies as $order=>$taxonomyid) {
        $is_exist = $db->result(sprintf("SELECT COUNT(*) FROM `#@_term_relation` WHERE `taxonomyid`=%d AND `objectid`=%d;",esc_sql($taxonomyid),esc_sql($objectid)));
        if (0 < $is_exist) {
            $db->update('#@_term_relation',array(
                'order' => $order,
            ),array(
                'taxonomyid' => $taxonomyid,
                'objectid'   => $objectid,
            ));
        } else {
            $db->insert('#@_term_relation',array(
                'taxonomyid' => $taxonomyid,
                'objectid'   => $objectid,
                'order'      => $order,
            ));
        }
        // 更新文章数
        $count = $db->result(sprintf("SELECT COUNT(`objectid`) FROM `#@_term_relation` WHERE `taxonomyid`=%d;",esc_sql($taxonomyid)));
        $db->update('#@_term_taxonomy',array('count'=>$count),array('taxonomyid'=>$taxonomyid));
        taxonomy_clean_cache($taxonomyid);
    }
    return true;
}
/**
 * 删除关系
 *
 * @param  $objectid
 * @param  $taxonomyid
 * @return bool
 */
function taxonomy_delete_relation($objectid,$taxonomyid) {
    $db = get_conn();
    return $db->delete('#@_term_relation',array(
        'taxonomyid' => $taxonomyid,
        'objectid'   => $objectid,
    ));
}
/**
 * 创建分类
 *
 * @param  $type
 * @param  $name
 * @param int $parentid
 * @param  $data
 * @return array|null
 */
function taxonomy_add($type,$name,$parentid=0,$data=null) {
    $db = get_conn(); $parentid = intval($parentid);
    $data = is_array($data) ? $data : array();
    $taxonomyid = $db->insert('#@_term_taxonomy',array(
       'type'   => $type,
       'parent' => $parentid,
    ));
    $data['name'] = $name;
    return taxonomy_edit($taxonomyid,$data);
}
/**
 * 添加Tag
 *
 * @param  $name
 * @return array|null
 */
function taxonomy_add_tag($name) {
    $db = get_conn(); $type = 'post_tag';
    $taxonomyid = $db->result(sprintf("SELECT `taxonomyid` FROM `#@_term_taxonomy` AS `tt` INNER JOIN `#@_term` AS `t` ON `tt`.`termid`=`t`.`termid` WHERE `tt`.`type`='%s' AND `t`.`name`='%s' LIMIT 0,1;",esc_sql($type),esc_sql($name)));
    if (!$taxonomyid) {
        $taxonomyid = $db->insert('#@_term_taxonomy',array(
           'type'   => $type,
        ));
        taxonomy_edit($taxonomyid,array(
            'name' => $name,
        ));
    }
    return $taxonomyid;
}
/**
 * 填写分类信息
 *
 * @param int $taxonomyid
 * @param array $data
 * @return array|null
 */
function taxonomy_edit($taxonomyid,$data) {
    $db = get_conn(); $taxonomy_rows = $term_rows = $meta_rows = array();
    $data = is_array($data) ? $data : array();
    if ($taxonomy = taxonomy_get($taxonomyid)) {
        // 格式化路径
        if (isset($data['path'])) {
            $data['path'] = path_format($data['path'],array(
                'ID'  => $taxonomyid,
                'PY'  => $data['name'],
                'MD5' => $taxonomyid,
            ));
            // 删除旧文件夹
            if (!empty($taxonomy['path'])) {
                if ($data['path']!=$taxonomy['path'] && is_dir(ABS_PATH.'/'.$taxonomy['path'])) {
                    rmdirs(ABS_PATH.'/'.$taxonomy['path']);
                }
            }
        }
        // 分析关键词
        if (isset($data['keywords']) && !empty($data['keywords'])) {
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
            // 组合关键词
            $data['keywords'] = implode(',', $keywords);
        }
        // 判断数据应该放在哪里
        foreach ($data as $field=>$value) {
            if ($db->is_field('#@_term_taxonomy',$field)) {
                $taxonomy_rows[$field] = $value;
            } elseif ($field=='name') {
                $term_rows[$field] = $value;
            } else {
                $meta_rows[$field] = $value;
            }
        }
        // 更新数据
        if (!empty($term_rows['name'])) $taxonomy_rows['termid'] = term_add($term_rows['name']);
        if ($taxonomy_rows) $db->update('#@_term_taxonomy',$taxonomy_rows,array('taxonomyid'=>$taxonomyid));
        if ($meta_rows) taxonomy_edit_meta($taxonomyid,$meta_rows);
        // 清理缓存
        taxonomy_clean_cache($taxonomyid);
        return array_merge($taxonomy,$data);
    }
    return null;
}
/**
 * 填写扩展信息
 *
 * @param int $taxonomyid
 * @param array $data
 * @return bool
 */
function taxonomy_edit_meta($taxonomyid,$data) {
    $db = get_conn(); $taxonomyid = intval($taxonomyid);
    $data = is_array($data) ? $data : array();
    foreach ($data as $key=>$value) {
        // 获取变量类型
        $var_type = gettype($value);
        // 判断是否需要序列化
        $value = is_need_serialize($value) ? serialize($value) : $value;
        // 查询数据库里是否已经存在
        $length = (int) $db->result(vsprintf("SELECT COUNT(*) FROM `#@_term_taxonomy_meta` WHERE `taxonomyid`=%d AND `key`='%s';",array($taxonomyid,esc_sql($key))));
        // update
        if ($length > 0) {
            $db->update('#@_term_taxonomy_meta',array(
                'value' => $value,
                'type'  => $var_type,
            ),array(
                'taxonomyid' => $taxonomyid,
                'key'    => $key,
            ));
        }
        // insert
        else {
            // 保存到数据库里
            $db->insert('#@_term_taxonomy_meta',array(
                'taxonomyid' => $taxonomyid,
                'key'    => $key,
                'value'  => $value,
                'type'   => $var_type,
            ));
        }
    }
    return true;
}
/**
 * 清理缓存
 *
 * @param int $taxonomyid
 * @return bool
 */
function taxonomy_clean_cache($taxonomyid) {
    $taxonomyid = intval($taxonomyid);
    return fcache_delete('taxonomy.'.$taxonomyid);
}
/**
 * 删除分类
 *
 * @param int $taxonomyid
 * @return bool
 */
function taxonomy_delete($taxonomyid) {
    $db = get_conn();
    $taxonomyid = intval($taxonomyid);
    if (!$taxonomyid) return false;
    if (taxonomy_get($taxonomyid)) {
        // 删除分类关系
        $db->delete('#@_term_relation',array('taxonomyid' => $taxonomyid));
        // 删除分类扩展信息
        $db->delete('#@_term_taxonomy_meta',array('taxonomyid' => $taxonomyid));
        // 删除分类信息
        $db->delete('#@_term_taxonomy',array('taxonomyid' => $taxonomyid));
        // 清理缓存
        taxonomy_clean_cache($taxonomyid);
        return true;
    }
    return false;
}

/**
 * 生成分类列表
 *
 * @param int $taxonomyid
 * @param int $page
 * @param bool $make_post   是否生成文章
 * @return bool
 */
function taxonomy_create($taxonomyid,$page=1,$make_post=false) {
    $taxonomyid = intval($taxonomyid);
    if (!$taxonomyid) return false;
    if ($taxonomy = taxonomy_get($taxonomyid)) {
        $page = $page<1 ? 1 : intval($page); $inner = '';
        $suffix = C('HTMLFileSuffix');
        // 载入模版
        $html   = tpl_loadfile(ABS_PATH.'/'.system_themes_path().'/'.$taxonomy['list']);
        // 标签块信息
        $block  = tpl_get_block($html,'post,list','list');
        if ($block) {
            // 每页条数
            $number = tpl_get_attr($block['tag'],'number');
            // 排序方式
            $order  = tpl_get_attr($block['tag'],'order');

            $sql = sprintf("SELECT `objectid` AS `postid` FROM `#@_term_relation` WHERE `taxonomyid`=%d ORDER BY `objectid` %s", esc_sql($taxonomyid), esc_sql($order));

            $result = post_gets($sql, $page, $number,'sortid,path',false);
            // 解析分页标签
            if (stripos($html,'{pagelist')!==false) {
                $html = preg_replace('/\{(pagelist)[^\}]*\/\}/isU',
                    page_list(WEB_ROOT.$taxonomy['path'].'/index$'.$suffix, $page, $result['pages'], $result['length'], true),
                    $html
                );
            }
            // 数据存在
            if ($result['length'] > 0) {
                foreach ($result['posts'] as $post) {
                    // 生成文章
                    if ($make_post) post_create($post['postid']);
                    // 清理模版内部变量
                    tpl_clean();
                    // 设置文章变量
                    $vars = array(
                        'postid'   => $post['postid'],
                        'title'    => $post['title'],
                        'content'  => $post['content'],
                        'path'     => WEB_ROOT.$post['path'],
                        'datetime' => $post['datetime'],
                        'keywords' => $post['keywords'],
                        'description' => $post['description'],
                    );
                    // 设置分类变量
                    $vars['sortid']   = $post['sortid'];
                    $vars['sortname'] = $post['sort']['name'];
                    $vars['sortpath'] = WEB_ROOT.$post['sort']['path'].'/index'.$suffix;
                    tpl_vars($vars);
                    // 设置自定义字段
                    if (isset($post['meta'])) {
                        foreach((array)$post['meta'] as $k=>$v) {
                            tpl_vars('model.'.$k, $v);
                        }
                    }
                    // 取得标签块内容
                    $block['inner'] = tpl_get_block_inner($block);
                    // 解析二级内嵌标签
                    foreach ((array)$block['sub'] as $sblock) {
                        $sblock['name'] = strtolower($sblock['name']);
                        switch($sblock['name']) {
                            // TODO 解析图片标签
                            case 'images':
                                $block['inner'] = str_replace($sblock['tag'],'',$block['inner']);
                                break;
                        }
                    }
                    // 解析变量
                    $inner.= tpl_parse($block['inner']);
                }
            }
            $html = str_replace($block['tag'],$inner,$html);
        }
        // 所需要的标签和数据都不存在，不需要生成页面
        if ($inner == '') return false;
        // 清理模版内部变量
        tpl_clean();
        tpl_vars(array(
            'sortid'   => $taxonomy['taxonomyid'],
            'sortname' => $taxonomy['name'],
            'sortpath' => WEB_ROOT.$taxonomy['path'].'/index'.$suffix,
            'termid'   => $taxonomy['termid'],
            'count'    => $taxonomy['count'],
            'title'    => $taxonomy['name'],
            'keywords' => $taxonomy['keywords'],
            'description' => $taxonomy['description'],
        ));
        $html = tpl_parse($html);
        // 生成的文件路径
        $file = ABS_PATH.'/'.$taxonomy['path'].'/index' . ($page==1 ? '' : $page) . $suffix;
        // 创建目录
        mkdirs(dirname($file));
        // 保存文件
        return file_put_contents($file,$html);
    }
    return true;
}