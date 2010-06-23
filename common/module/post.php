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

class LCPost {
    /**
     * 添加文章
     *
     * @param  $title
     * @param  $content
     * @param  $path
     * @param  $data
     * @return array
     */
    function addPost($title,$content,$path,$data=null) {
        $db = get_conn();
	    $postid = $db->insert('#@_post',array(
	       'title'   => $title,
	       'content' => $content,
	       'path'    => $path,
	    ));
	    return LCPost::editPost($postid,$data);
    }
    /**
     * 更新文章信息
     *
     * @param  $postid
     * @param  $data
     * @return array
     */
    function editPost($postid,$data) {
        $db = get_conn();
        $postid = intval($postid);
        $post_rows = $meta_rows = array();
        if ($post = LCPost::getPostById($postid)) {
            $data = is_array($data) ? $data : array();
            $categories = array();
            // 更新分类关系
            if ($data['category']) {
                LCTaxonomy::makeRelation('category',$postid,$data['category']);
                $categories = $data['category']; unset($data['category']);
            }
            $meta_rows = empty($data['meta']) ? array() : $data['meta']; unset($data['meta']);
            $post_rows = $data; $data['meta'] = $meta_rows; $data['category'] = $categories; 
            // 更新数据
            if (!empty($post_rows)) {
                $db->update('#@_post',$post_rows,array('postid' => $postid));
            }
            if (!empty($meta_rows)) {
                LCPost::editPostMeta($postid,$meta_rows);
            }
            // 清理缓存
            LCPost::clearPostCache($postid);
            return array_merge($post,$data);
        }
        return null;
    }
    /**
     * 查找指定的文章
     *
     * @param  $postid
     * @return array
     */
    function getPostById($postid) {
        $db = get_conn();
        $ckey  = sprintf('post.%s',$postid);
        $value = FCache::get($ckey);
        if (!empty($value)) return $value;
        
        $rs = $db->query("SELECT * FROM `#@_post` WHERE `postid`=%s LIMIT 0,1;",$postid);
		// 判断文章是否存在
		if ($post = $db->fetch($rs)) {
            // 取得分类关系
            $post['category'] = LCTaxonomy::getRelation('category',$postid);
		    if ($meta = LCPost::getPostMeta($post['postid'])) {
		    	$post['meta'] = $meta;
		    }
		    // 保存到缓存
            FCache::set($ckey,$post);
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
    function getPostMeta($postid) {
	    $db = get_conn(); $result = array(); $postid = intval($postid);
	    $rs = $db->query("SELECT * FROM `#@_post_meta` WHERE `postid`=%s;",$postid);
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
    function editPostMeta($postid,$data) {
        $db = get_conn(); $postid = intval($postid);
        if (!is_array($data)) return false;
        foreach ($data as $key=>$value) {
            // 获取变量类型
            $var_type = gettype($value);
            // 判断是否需要序列化
            $value = is_need_serialize($value) ? serialize($value) : $value;
            // 查询数据库里是否已经存在
            $length = (int) $db->result(vsprintf("SELECT COUNT(*) FROM `#@_post_meta` WHERE `postid`=%s AND `key`=%s;",array($postid,$db->escape($key))));
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
    function clearPostCache($postid) {
        return FCache::delete('post.'.$postid);
    }
    
}