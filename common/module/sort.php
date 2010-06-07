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
 * | Copyright (C) 2007-2008 LazyCMS.net All rights reserved.                  |
 * +---------------------------------------------------------------------------+
 * | LazyCMS is free software. This version use Apache License 2.0             |
 * | See LICENSE.txt for copyright notices and details.                        |
 * +---------------------------------------------------------------------------+
 */
defined('COM_PATH') or die('Restricted access!');

class ModuleSort {
    /**
     * 取得分类树
     *
     * @param int $parentid
     * @param string $type
     * @return array
     */
    function get_sorts_tree($parentid=0,$type='category') {
        $db = get_conn(); $result = array(); $un = array();
	    $rs = $db->query("SELECT * FROM `#@_sort` WHERE `type`=%s;",$type);
	    while ($row = $db->fetch($rs)) {
	        $result[$row['sortid']] = ModuleSort::get_sort_by_id($row['sortid']);
	    }
	    // 将数组转变成树，因为使用了引用，所以不会占用太多的内存
        foreach ($result as $id => $item) {
            if ($item['parent']) {
                $result[$item['parent']]['subs'][$item['sortid']] = &$result[$id];
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
     * @param int $sortid
     * @return array|null
     */
    function get_sort_by_id($sortid) {
        $db = get_conn(); $prefix = 'sort.';
        $value = DataCache::get($prefix.$sortid);
        if (!empty($value)) return $value;
	    $rs = $db->query("SELECT * FROM `#@_sort` WHERE `sortid`=%s LIMIT 0,1;",$sortid);
		// 判断用户是否存在
		if ($sort = $db->fetch($rs)) {
		    if ($meta = ModuleSort::get_sort_meta($sort['sortid'])) {
		    	$sort = array_merge($sort,$meta);
		    }
		    if ($term = ModuleSort::get_term_by_id($sort['termid'])) {
		    	$sort = array_merge($sort,$term);
		    }
		    // 查询数量
		    $sort['count'] = $db->result(sprintf("SELECT COUNT(*) FROM `#@_term_relation` WHERE `termid`=%s;",$sort['termid']));
		    // 保存到缓存
            DataCache::set($prefix.$sortid,$sort);
			return $sort;
		}
		return null;
    }
    /**
     * 获取分类扩展信息
     *
     * @param int $sortid
     * @return array
     */
    function get_sort_meta($sortid) {
        $db = get_conn(); $result = array();
	    $rs = $db->query("SELECT * FROM `#@_sort_meta` WHERE `sortid`=%s;",$sortid);
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
     * 取得关键词
     *
     * @param int $termid
     * @return array|null
     */
    function get_term_by_id($termid) {
        $db = get_conn();
	    $rs = $db->query("SELECT * FROM `#@_term` WHERE `termid`=%s LIMIT 0,1;",$termid);
		// 判断用户是否存在
		if ($term = $db->fetch($rs)) {
			return $term;
		}
		return null;
    }
    /**
     * 创建分类
     *
     * @param string $path
     * @param string $parent
     * @param string $type
     * @param array $data
     * @return array|null
     */
    function create_sort($path,$parent,$type='category',$data=null) {
        $db = get_conn();
	    $sortid = $db->insert('#@_sort',array(
	       'path' => $path,
	       'type' => $type,
	       'parent' => $parent,
	    ));
	    return ModuleSort::fill_sort_info($sortid,$data);
    }
    /**
     * 填写分类信息
     *
     * @param int $sortid
     * @param array $data
     * @return array|null
     */
    function fill_sort_info($sortid,$data) {
        $db = get_conn(); $sort_rows = $term_rows = $meta_rows = array();
        if ($sort = ModuleSort::get_sort_by_id($sortid)) {
            foreach ($data as $field=>$value) {
                if ($db->is_field('#@_sort',$field)) {
                    $sort_rows[$field] = $value;
                } elseif ($db->is_field('#@_term',$field)) {
                    $term_rows[$field] = $value;
                } else {
                    $meta_rows[$field] = $value;
                }
            }
            // 更新数据
            if ($term_rows) {
            	if ($sort['termid']) {
            		$db->update('#@_term',$term_rows,array('termid'=>$sort['termid']));
            	} else {
            	    $termid = $db->result(sprintf("SELECT `termid` FROM `#@_term` WHERE `name`=%s LIMIT 0,1;",$db->escape($term_rows['name'])));
            	    if ($termid) {
            	    	$sort['termid'] = $termid;
            	    } else {
                		$sort['termid'] = $db->insert('#@_term',$term_rows);
            	    }
                    $sort_rows['termid'] = $sort['termid'];
            	}
            }
            if ($sort_rows) {
                $db->update('#@_sort',$sort_rows,array('sortid'=>$sortid));
            }
            if ($meta_rows) {
                ModuleSort::fill_sort_meta($sortid,$meta_rows);
            }
            // 清理缓存
            ModuleSort::clear_sort_cache($sortid);
            return array_merge($sort,$data);
        }
        return null;
    }
    /**
     * 填写扩展信息
     *
     * @param int $sortid
     * @param array $data
     * @return bool
     */
    function fill_sort_meta($sortid,$data) {
        $db = get_conn();
        if (!is_array($data)) return false;
        foreach ($data as $key=>$value) {
            // 获取变量类型
            $var_type = gettype($value);
            // 判断是否需要序列化
            $value = is_need_serialize($value) ? serialize($value) : $value;
            // 查询数据库里是否已经存在
            $length = (int) $db->result(vsprintf("SELECT COUNT(*) FROM `#@_sort_meta` WHERE `sortid`=%s AND `key`=%s;",array($sortid,$db->escape($key))));
            // update
            if ($length > 0) {
                $db->update('#@_sort_meta',array(
                    'value' => $value,
                    'type'  => $var_type,
                ),array(
                    'sortid' => $sortid,
                    'key'    => $key,
                ));
            }
            // insert
            else {
                // 保存到数据库里
                $db->insert('#@_sort_meta',array(
                    'sortid' => $sortid,
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
     * @param int $sortid
     * @return bool
     */
    function clear_sort_cache($sortid) {
        DataCache::delete('sort.'.$sortid);
        return true;
    }
    /**
     * 删除分类
     *
     * @param int $sortid
     * @return bool
     */
    function delete_sort_by_id($sortid) {
        $db = get_conn(); if (!$sortid) return ;
        if (ModuleSort::get_sort_by_id($sortid)) {
            ModuleSort::clear_sort_cache($sortid);
            $db->delete('#@_sort',array('sortid' => $sortid));
            $db->delete('#@_sort_meta',array('sortid' => $sortid));
            // TODO: 删除其他数据
            return true;
        }
        return false;
    }
}