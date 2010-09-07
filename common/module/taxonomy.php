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
    $rs = $db->query("SELECT * FROM `#@_term` WHERE `termid`=%s LIMIT 0,1;",$termid);
    // 判断用户是否存在
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
    $rs = $db->query("SELECT * FROM `#@_term` WHERE `name`=%s LIMIT 0,1;",$name);
    // 判断用户是否存在
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
    $termid = $db->result(sprintf("SELECT `termid` FROM `#@_term` WHERE `name`=%s LIMIT 0,1;",esc_sql($name)));
    if (!$termid) {
        $termid = $db->insert('#@_term',array(
            'name' => $name,
        ));
        // 清理缓存
        FCache::delete('terms.dicts');
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
    $dicts = FCache::get($ckey);
    if (empty($dicts)) {
        $db = get_conn(); $dicts = array();
        // 读取关键词列表
        $rs = $db->query("SELECT `name` FROM `#@_term`");
        while ($data = $db->fetch($rs)) {
            $dicts[] = $data['name'];
        }
        FCache::set($ckey,$dicts);
    }
    require_once COM_PATH.'/system/splitword.php';
    $splitword = new SplitWord($dicts);
    $keywords  = $splitword->get($content,$max_len,$save_other);
    if (empty($keywords)) {
        return null;
    } else {
        return $keywords;
    }
}

/**
 * 取得分类树
 *
 * @param int $parentid
 * @param string $type
 * @return array
 */
function taxonomy_get_trees($parentid=0,$type='category') {
    $db = get_conn(); $result = array(); $un = array(); $parentid = intval($parentid);
    $rs = $db->query("SELECT * FROM `#@_term_taxonomy` WHERE `type`=%s;",$type);
    while ($row = $db->fetch($rs)) {
        $result[$row['taxonomyid']] = taxonomy_get_byid($row['taxonomyid']);
    }
    // 将数组转变成树，因为使用了引用，所以不会占用太多的内存
    foreach ($result as $id => $item) {
        if ($item['parent']) {
            $result[$item['parent']]['subs'][$item['taxonomyid']] = &$result[$id];
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
 * 取得分类信息
 *
 * @param int $taxonomyid
 * @return array|null
 */
function taxonomy_get_byid($taxonomyid) {
    $db = get_conn(); $prefix = 'taxonomy.';
    $taxonomyid = intval($taxonomyid);
    $value = FCache::get($prefix.$taxonomyid);
    if (!empty($value)) return $value;
    $rs = $db->query("SELECT * FROM `#@_term_taxonomy` WHERE `taxonomyid`=%s LIMIT 0,1;",$taxonomyid);
    // 判断用户是否存在
    if ($taxonomy = $db->fetch($rs)) {
        if ($term = term_get_byid($taxonomy['termid'])) {
            $taxonomy = array_merge($taxonomy,$term);
        }
        if ($meta = taxonomy_get_meta($taxonomy['taxonomyid'])) {
            $taxonomy = array_merge($taxonomy,$meta);
        }
        // 保存到缓存
        FCache::set($prefix.$taxonomyid,$taxonomy);
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
    $rs = $db->query("SELECT * FROM `#@_term_taxonomy_meta` WHERE `taxonomyid`=%s;",$taxonomyid);
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
 * @param  $type
 * @param  $objectid
 * @return array
 */
function taxonomy_get_relation($type,$objectid) {
    static $taxonomies = array();
    $db = get_conn(); $result = array();
    if (!isset($taxonomies[$type])) {
        $tt_ids = array();
        $rs = $db->query("SELECT `taxonomyid` FROM `#@_term_taxonomy` WHERE `type`=%s;",$type);
        while ($tt = $db->fetch($rs)) {
            $tt_ids[] = $tt['taxonomyid'];
        }
        $taxonomies[$type] = "'" . implode("', '", $tt_ids) . "'";
    }
    $in_tt_ids = $taxonomies[$type];
    $rs = $db->query("SELECT DISTINCT `tr`.`taxonomyid` AS `taxonomyid`,`tr`.`order` AS `order` FROM `#@_term_taxonomy` AS `tt` INNER JOIN `#@_term_relation` AS `tr` ON `tt`.`taxonomyid`=`tr`.`taxonomyid` WHERE `tr`.`objectid`=%s AND `tt`.`taxonomyid` IN({$in_tt_ids});",$objectid);
    while ($taxonomy = $db->fetch($rs)) {
        $result[$taxonomy['order']] = taxonomy_get_byid($taxonomy['taxonomyid']);
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
    $rs = $db->query("SELECT `taxonomyid` FROM `#@_term_taxonomy` WHERE `type`=%s;",$type);
    while ($tt = $db->fetch($rs)) {
        $tt_ids[] = $tt['taxonomyid'];
    }
    // 取得分类差集,删除差集
    $tt_ids = array_diff($tt_ids,$taxonomies);
    $in_tt_ids = "'" . implode("', '", $tt_ids) . "'";
    // 先删除关系
    $rs = $db->query("SELECT DISTINCT `tr`.`taxonomyid` AS `taxonomyid` FROM `#@_term_taxonomy` AS `tt` INNER JOIN `#@_term_relation` AS `tr` ON `tt`.`taxonomyid`=`tr`.`taxonomyid` WHERE `tr`.`objectid`=%s AND `tt`.`taxonomyid` IN({$in_tt_ids});",$objectid);
    while ($taxonomy = $db->fetch($rs)) {
        taxonomy_delete_relation($objectid,$taxonomy['taxonomyid']);
    }
    // 然后添加分类关系
    foreach($taxonomies as $order=>$taxonomyid) {
        $is_exist = $db->result(sprintf("SELECT COUNT(*) FROM `#@_term_relation` WHERE `taxonomyid`=%s AND `objectid`=%s;",esc_sql($taxonomyid),esc_sql($objectid)));
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
    $taxonomyid = $db->result(sprintf("SELECT `taxonomyid` FROM `#@_term_taxonomy` AS `tt` INNER JOIN `#@_term` AS `t` ON `tt`.`termid`=`t`.`termid` WHERE `tt`.`type`=%s AND `t`.`name`=%s LIMIT 0,1;",esc_sql($type),esc_sql($name)));
    if (!$taxonomyid) {
        $taxonomyid = $db->insert('#@_term_taxonomy',array(
           'type'   => $type,
           'parent' => $parentid,
        ));
    }
    $data['name'] = $name;
    return taxonomy_edit($taxonomyid,$data);;
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
    if ($taxonomy = taxonomy_get_byid($taxonomyid)) {
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
        taxonomy_clear_cache($taxonomyid);
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
        $length = (int) $db->result(vsprintf("SELECT COUNT(*) FROM `#@_term_taxonomy_meta` WHERE `taxonomyid`=%s AND `key`=%s;",array($taxonomyid,esc_sql($key))));
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
function taxonomy_clear_cache($taxonomyid) {
    $taxonomyid = intval($taxonomyid);
    return FCache::delete('taxonomy.'.$taxonomyid);
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
    if (taxonomy_get_byid($taxonomyid)) {
        // 删除分类关系
        $db->delete('#@_term_relation',array('taxonomyid' => $taxonomyid));
        // 删除分类扩展信息
        $db->delete('#@_term_taxonomy_meta',array('taxonomyid' => $taxonomyid));
        // 删除分类信息
        $db->delete('#@_term_taxonomy',array('taxonomyid' => $taxonomyid));
        // 清理缓存
        taxonomy_clear_cache($taxonomyid);
        return true;
    }
    return false;
}