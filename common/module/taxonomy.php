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
 * 取得关键词
 *
 * @param int $termid
 * @return array|null
 */
function term_get_byid($termid) {
    $db = get_conn(); $termid = intval($termid);
    $rs = $db->query("SELECT * FROM `#@_term` WHERE `termid`=%d LIMIT 1 OFFSET 0;",$termid);
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
    $rs = $db->query("SELECT * FROM `#@_term` WHERE `name`='%s' LIMIT 1 OFFSET 0;",$name);
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
    $termid = $db->result(sprintf("SELECT `termid` FROM `#@_term` WHERE `name`='%s' LIMIT 1 OFFSET 0;",esc_sql($name)));
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
 * @param string $title
 * @param string $content
 * @return array
 */
function term_gets($title=null, $content=null) {
    $ckey  = 'terms.dicts';
    $dicts = fcache_get($ckey);
    if (fcache_is_null($dicts)) {
        $db = get_conn(); $dicts = array();
        // 读取关键词列表
        $rs = $db->query("SELECT `name` FROM `#@_term`");
        while ($data = $db->fetch($rs)) {
            $dicts[] = $data['name'];
        }
        fcache_set($ckey,$dicts);
    }
    // 不输入文章，只返回词库
    if ($title===null && $content === null) return $dicts;
    // 开始分词
    $content = clear_space(strip_tags($content));
    if (trim($content) == '') return null;
    if (mb_strlen($content,'UTF-8') > 1024)
        $content = mb_substr($content,0,1024,'UTF-8');

    include_file(COM_PATH.'/system/keyword.php');
    $splitword = new keyword($dicts);
    $keywords  = $splitword->get($content);
    // 本地分词失败或分词较少
    if (C('Tags-Service') && (empty($keywords) || count($keywords)<=3)) {
        // 使用keyword.lazycms.com的网络分词
        include_file(COM_PATH.'/system/httplib.php');
        $r = @httplib_get(sprintf('http://keyword.lazycms.com/related_kw.php?title=%s&content=%s', rawurlencode($title), rawurlencode($content)),array(
            'timeout' => 3
        ));
        $code = httplib_retrieve_response_code($r);
        if ($code == '200') {
            $keys = array();
            $xml  = httplib_retrieve_body($r);
            // 取出关键词为数组
            if (preg_match_all('/\<kw\>\<\!\[CDATA\[(.+)\]\]\>\<\/kw\>/i',$xml,$args)) {
                $keys = $args[1];
            }
            // 合并分词结果
            if (!empty($keys)) {
                $newkeys  = array_unique(array_merge($keywords,$keys));
                $keywords = $newkeys;
                foreach ($keywords as $keyword) {
                    foreach($newkeys as $k=>$v) {
                        if ($keyword!=$v && stripos($keyword,$v) !== false) {
                            unset($keywords[$k]);
                        }
                    }
                }
                $keywords = array_values($keywords); unset($newkeys);
            }
        }
    }
    if (empty($keywords)) {
        return null;
    } else {
        return $keywords;
    }
}
/**
 * 取得分类名称列表
 *
 * @param  $category
 * @param int $num
 * @return string
 */
function taxonomy_get_names($category,$num=3) {
    $names = array();
    foreach($category as $i=>$taxonomyid) {
        $taxonomy = taxonomy_get($taxonomyid);
        if ($i >= $num) {
            $names[] = $taxonomy['name'].'...';
            break;
        } else {
            $names[] = $taxonomy['name'];
        }
    }
    return implode(',', $names);
}
/**
 * 统计数量
 *
 * @param string $type
 * @return int
 */
function taxonomy_count($type='category') {
    $db = get_conn(); return $db->result(sprintf("SELECT COUNT(`taxonomyid`) FROM `#@_term_taxonomy` WHERE `type`='%s';", $type));
}
/**
 * 取得分类列表
 *
 * @param string $type
 * @return array
 */
function taxonomy_get_list($type='category') {
    // TODO 需要缓存
    $db = get_conn(); $result = array();
    $rs = $db->query("SELECT `taxonomyid` FROM `#@_term_taxonomy` WHERE `type`='%s';", $type);
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
    if ($parentid) {
        $result = isset($result[$parentid])?$result[$parentid]:array();
    }
    foreach($un as $v) unset($result[$v]);
    return $result;
}
/**
 * 取得分类ids
 *
 * @param array $tree
 * @return array
 */
function taxonomy_get_subids($tree) {
    static $result = array(); $func = __FUNCTION__;
    if (!is_array($tree)) return ;
    foreach ($tree as $taxonomyid=>$taxonomy) {
        $result[] = $taxonomyid;
        if (isset($taxonomy['subs'])) {
            $func($taxonomy['subs']);
        }
    }
    return $result;
}
/**
 * 取得分类ids
 *
 * @param string $listid
 * @param string $listsub
 * @return array
 */
function taxonomy_get_ids($listid, $listsub = 'me') {
    $listids = array_unique(explode(',', $listid));
    if ($listsub == 'me') {
        foreach ($listids as $id) {
            $t = taxonomy_get_trees($id, 'category');
            if (isset($t['subs'])) {
                $ids = taxonomy_get_subids($t['subs']);
            } elseif(!isset($t['taxonomyid'])) {
                $ids = taxonomy_get_subids($t);
            }
            $listids = $ids ? array_merge($listids, $ids) : $listids;
        }
    } elseif($listsub) {
        $listsub = explode(',', $listsub);
        $listids = array_merge($listids, $listsub);
    }
    return array_unique($listids);
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
    if (fcache_not_null($taxonomy)) return $taxonomy;

    $rs = $db->query("SELECT * FROM `#@_term_taxonomy` WHERE `taxonomyid`=%d LIMIT 1 OFFSET 0;",$taxonomyid);
    if ($taxonomy = $db->fetch($rs)) {
        if ($term = term_get_byid($taxonomy['termid'])) {
            $taxonomy = array_merge($taxonomy,$term);
        }
        if ($meta = taxonomy_get_meta($taxonomy['taxonomyid'])) {
            foreach (array('path','page','list','model','description') as $field) {
                $taxonomy[$field] = $meta[$field]; unset($meta[$field]);
            }
            $taxonomy['meta'] = $meta;
        }
        $taxonomy['keywords'] = taxonomy_get_relation('sort_tag',$taxonomy['taxonomyid']);
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
        $result[$row['key']] = is_serialized($row['value']) ? unserialize($row['value']) : $row['value'];
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
    $db = get_conn(); $result = array(); $tt_ids = array();
    $rs = $db->query("SELECT `taxonomyid` FROM `#@_term_taxonomy` WHERE `type`='%s';",$type);
    while ($tt = $db->fetch($rs)) {
        $tt_ids[] = $tt['taxonomyid'];
    }
    $in_tt_ids = "'" . implode("', '", $tt_ids) . "'";
    $rs = $db->query("SELECT DISTINCT(`tr`.`taxonomyid`) AS `taxonomyid`,`tr`.`order` AS `order` FROM `#@_term_relation` AS `tr` LEFT JOIN `#@_term_taxonomy` AS `tt` ON `tt`.`taxonomyid`=`tr`.`taxonomyid` WHERE `tr`.`objectid`=%d AND `tt`.`taxonomyid` IN({$in_tt_ids});",$objectid);
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
    $rs = $db->query("SELECT DISTINCT(`tr`.`taxonomyid`) AS `taxonomyid` FROM `#@_term_relation` AS `tr` LEFT JOIN `#@_term_taxonomy` AS `tt` ON `tt`.`taxonomyid`=`tr`.`taxonomyid` WHERE `tr`.`objectid`=%d AND `tt`.`taxonomyid` IN({$in_tt_ids});",$objectid);
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
        taxonomy_update_count($taxonomyid);
    }
    return true;
}
/**
 * 更新文章数
 *
 * @param int $taxonomyid
 * @return void
 */
function taxonomy_update_count($taxonomyid) {
    $db = get_conn();
    if ($taxonomy = taxonomy_get($taxonomyid)) {
        $count = $db->result(sprintf("SELECT COUNT(`objectid`) FROM `#@_term_relation` WHERE `taxonomyid`=%d;",esc_sql($taxonomyid)));
        if ($taxonomy['count'] != $count) {
            $db->update('#@_term_taxonomy',array('count'=>$count),array('taxonomyid'=>$taxonomyid));
            taxonomy_clean_cache($taxonomyid);
        }
        return $count;
    }
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
 * @param string $name
 * @param string $type
 * @return array|null
 */
function taxonomy_add_tag($name, $type='post_tag') {
    $db = get_conn();
    $taxonomyid = $db->result(sprintf("SELECT `taxonomyid` FROM `#@_term` AS `t` LEFT JOIN `#@_term_taxonomy` AS `tt` ON `tt`.`termid`=`t`.`termid` WHERE `tt`.`type`='%s' AND `t`.`name`='%s' LIMIT 1 OFFSET 0;",esc_sql($type),esc_sql($name)));
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
            $taxonomies = array();
            // 移除重复的关键词
            $keywords = array_unique($keywords);
            // 去除关键词两边的空格，转义HTML
            array_walk($keywords,create_function('&$s','$s=esc_html(trim($s));'));
            // 强力插入关键词
            foreach($keywords as $key) {
                $taxonomies[] = taxonomy_add_tag($key, 'sort_tag');
            }
            // 组合关键词
            $data['keywords'] = implode(',', $keywords);
            // 创建关系
            taxonomy_make_relation('sort_tag',$taxonomyid,$taxonomies);
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
        // 清理字段
        unset($meta_rows['keywords']);
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
        // 查询数据库里是否已经存在
        $length = (int) $db->result(vsprintf("SELECT COUNT(*) FROM `#@_term_taxonomy_meta` WHERE `taxonomyid`=%d AND `key`='%s';",array($taxonomyid,esc_sql($key))));
        // update
        if ($length > 0) {
            $db->update('#@_term_taxonomy_meta',array(
                'value' => $value,
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
    if ($taxonomy = taxonomy_get($taxonomyid)) {
        // 删除分类关系
        $db->delete('#@_term_relation',array('taxonomyid' => $taxonomyid));
        // 删除分类扩展信息
        $db->delete('#@_term_taxonomy_meta',array('taxonomyid' => $taxonomyid));
        // 删除分类信息
        $db->delete('#@_term_taxonomy',array('taxonomyid' => $taxonomyid));
        // 清理缓存
        taxonomy_clean_cache($taxonomyid);
        // 删除文件
        return rmdirs(ABS_PATH.'/'.$taxonomy['path']);
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
        $tpl    = tpl_init('taxonomy');
        $page   = $page<1 ? 1 : intval($page);
        $inner  = $b_guid = ''; $i = 0;
        $suffix = C('HTMLFileSuffix');
        // 载入模版
        $html   = tpl_loadfile(ABS_PATH.'/'.system_themes_path().'/'.$taxonomy['list']);
        // 标签块信息
        $block  = tpl_get_block($html,'post,list','list');
        if ($block) {
            // 扩展字段过滤
            $meta    = tpl_get_attr($block['tag'],'meta');
            // 子分类ID
            $listsub = tpl_get_attr($block['tag'],'listsub');
            // 被排除的子分类ID
            $notsid  = tpl_get_attr($block['tag'],'listsub','!=');
            // 每页条数
            $number  = tpl_get_attr($block['tag'],'number');
            // 排序方式
            $order   = tpl_get_attr($block['tag'],'order');
            // 斑马线实现
            $zebra   = tpl_get_attr($block['tag'],'zebra');
            // 校验数据
            $listsub = $listsub == 'me' ? $listsub : validate_is($listsub, VALIDATE_IS_LIST) ? $listsub : null;
            $notsid  = validate_is($notsid,VALIDATE_IS_LIST) ? $notsid : null;
            $zebra   = validate_is($zebra,VALIDATE_IS_NUMERIC) ? $zebra : 0;
            $number  = validate_is($number,VALIDATE_IS_NUMERIC) ? $number : 10;
            $order   = instr(strtoupper($order),'ASC,DESC') ? $order : 'DESC';
            // 设置每页显示数
            pages_init($number, $page);
            $listids = array($taxonomyid);
            if ($listsub !== null) {
                // 查询IDs
                $listids = taxonomy_get_ids($taxonomyid, $listsub);
                // 排除IDs
                $notsids = $notsid ? explode(',', $notsid) : array();
                // 删掉排除的IDs
                foreach ($listids as $k=>$id) {
                    if (in_array($id,$notsids))
                        unset($listids[$k]);
                }
            }
            $length = count($listids);
            if ($length == 1) {
                $where = sprintf(" AND `tr`.`taxonomyid`=%d", array_pop($listids));
            } elseif ($length > 1) {
                $where = sprintf(" AND `tr`.`taxonomyid` IN(%s)", implode(',', $listids));
            }
            // 自定义字段
            if ($meta && (strpos($meta,':') !== false || strncasecmp($meta, 'find(', 5) === 0)) {
                // meta="find(value,field)"
                if (strncasecmp($meta, 'find(', 5) === 0) {
                    $index = strrpos($meta, ',');
                    $field = substring($meta, $index+1, strrpos($meta,')'));
                    $value = substring($meta, 5, $index);
                    $where.= sprintf(" AND FIND_IN_SET('%s', `pm`.`value`)", esc_sql($value));
                }
                // meta="field:value"
                elseif (($pos=strpos($meta,':')) !== false) {
                    $field = substr($meta, 0, $pos);
                    $value = substr($meta, $pos + 1);
                    $where.= sprintf(" AND `pm`.`value`='%s'", esc_sql($value));
                }
                $sql = sprintf("SELECT DISTINCT(`tr`.`objectid`) AS `postid` FROM `#@_term_relation` AS `tr` LEFT JOIN `#@_post_meta` AS `pm` ON `tr`.`objectid`=`pm`.`postid` WHERE `pm`.`key`='%2\$s' %3\$s ORDER BY `objectid` %1\$s", $order, esc_sql($field), $where);
            } else {
                $where = str_replace('`tr`.', '', $where);
                // 拼装sql
                $sql = sprintf("SELECT `objectid` AS `postid` FROM `#@_term_relation` WHERE 1 %s ORDER BY `objectid` %s", $where, esc_sql($order));
            }
            $result = pages_query($sql);
            // 解析分页标签
            if (stripos($html,'{pagelist') !== false) {
                $html = preg_replace('/\{(pagelist)[^\}]*\/\}/isU',
                    pages_list(ROOT.$taxonomy['path'].'/index$'.$suffix, '!$'),
                    $html
                );
            }
            // 数据存在
            if ($result) {
                // 取得标签块内容
                $block['inner'] = tpl_get_block_inner($block);
                while ($data = pages_fetch($result)) {
                    $post = post_get($data['postid']);
                    if (empty($post)) continue;
                    $post['list'] = taxonomy_get($post['listid']);
                    $post['path'] = post_get_path($post['listid'],$post['path']);
                    // 生成文章
                    if ($make_post) post_create($post['postid']);
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
                        'comment'  => '<script type="text/javascript" src="'.ROOT.'common/gateway.php?func=post_comment&postid='.$post['postid'].'"></script>',
                        'people'   => '<script type="text/javascript" src="'.ROOT.'common/gateway.php?func=post_comment_people&postid='.$post['postid'].'"></script>',
                        'digg'     => $post['digg'],
                        'path'     => ROOT.$post['path'],
                        'content'  => $content,
                        'date'     => $post['datetime'],
                        'edittime' => $post['edittime'],
                        'keywords' => post_get_taxonomy($post['keywords']),
                        'description' => $post['description'],
                    );
                    // 设置分类变量
                    if (isset($post['list'])) {
                        $vars['listid']     = $post['list']['taxonomyid'];
                        $vars['listname']   = $post['list']['name'];
                        $vars['listpath']   = ROOT.$post['list']['path'].'/';
                        $vars['listcount']  = '<script type="text/javascript" src="'.ROOT.'common/gateway.php?func=taxonomy_count&listid='.$post['list']['taxonomyid'].'"></script>';
                        if (isset($post['list']['meta'])) {
                            foreach((array)$post['list']['meta'] as $k=>$v) {
                                $vars['list.'.$k] = $v;
                            }
                        }
                    }
                    // 清理数据
                    tpl_clean($tpl);
                    tpl_set_var($vars, $tpl);
                    // 设置自定义字段
                    if (isset($post['meta'])) {
                        foreach((array)$post['meta'] as $k=>$v) {
                            tpl_set_var('post.'.$k, $v, $tpl);
                        }
                    }
                    // 解析变量
                    $inner.= tpl_parse($block['inner'], $block, $tpl); $i++;
                }
            } else {
                $inner = __('The page is in the making, please visit later ...');
            }
            // 生成标签块的唯一ID
            $b_guid = guid($block['tag']);
            // 把标签块替换成变量标签
            $html   = str_replace($block['tag'], '{$'.$b_guid.'}', $html);
        }

        // 所需要的标签和数据都不存在，不需要生成页面
        if ($i==0 && $page>1) return false;
        // 清理模版内部变量
        tpl_clean($tpl);
        tpl_set_var($b_guid, $inner, $tpl);
        tpl_set_var(array(
            'listid'        => $taxonomy['taxonomyid'],
            'listname'      => $taxonomy['name'],
            'listpath'      => ROOT.$taxonomy['path'].'/',
            'count'         => '<script type="text/javascript" src="'.ROOT.'common/gateway.php?func=taxonomy_count&listid='.$taxonomy['taxonomyid'].'"></script>',
            'guide'         => system_category_guide($taxonomy['taxonomyid']),
            'title'         => $taxonomy['name'],
            'keywords'      => post_get_taxonomy($taxonomy['keywords']),
            'description'   => $taxonomy['description'],
        ), $tpl);
        // 设置自定义字段
        if (isset($taxonomy['meta'])) {
            foreach((array)$taxonomy['meta'] as $k=>$v) {
                tpl_set_var('list.'.$k, $v, $tpl);
            }
        }
        $html = tpl_parse($html, $tpl);
        // 生成的文件路径
        $file = ABS_PATH.'/'.$taxonomy['path'].'/index' . ($page==1 ? '' : $page) . $suffix;
        // 创建目录
        mkdirs(dirname($file));
        // 保存文件
        return file_put_contents($file,$html);
    }
    return true;
}
/**
 * 文章数
 *
 * @return string
 */
function taxonomy_gateway_count() {
    $listid = isset($_GET['listid']) ? $_GET['listid'] : 0;
    if ($taxonomy = taxonomy_get($listid)) {
        $count = $taxonomy['count'];
    } else {
        $count = 0;
    }
    return 'document.write('.$count.');';
}