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

class ModuleModel {
    /**
     * 通过模型ID查询模型信息
     *
     * @param int $modelid
     * @param string $field
     * @return array|null
     */
    function get_model_by_id($modelid,$field='*') {
        $model = ModuleModel::get_model($modelid,0);
        if ($field!='*') {
        	return isset($model[$field])?$model[$field]:null;
        }
        return $model;
    }
    /**
     * 通过模型标识查询模型信息
     *
     * @param string $ename
     * @param string $field
     * @return array|null
     */
    function get_model_by_code($code,$field='*') {
        $model = ModuleModel::get_model($code,1);
        if ($field!='*') {
        	return isset($model[$field])?$model[$field]:null;
        }
        return $model;
    }
    /**
     * 取得模型信息
     *
     * @param string $param
     * @param int $type
     * @return array|null
     */
    function get_model($param,$type=0) {
        $db = get_conn(); if ((int)$type>2) return null;
	    $prefixs = array('model.modelid.','model.code.');
        $prefix  = $prefixs[$type];
        $value   = DataCache::get($prefix.$param);
        if (!empty($value)) return $value;
        
        switch($type){
            case 0:
                $where = sprintf("`modelid`=%s",$db->escape($param));
                break;
            case 1:
                $where = sprintf("`code`=%s",$db->escape($param));
                break;
        }
	    $rs = $db->query("SELECT * FROM `#@_model` WHERE {$where} LIMIT 0,1;");
		// 判断是否存在
		if ($model = $db->fetch($rs)) {
		    $fields = unserialize($model['fields']);
		    $model['fields'] = array();
		    if (is_array($fields)) {
    		    foreach ($fields as $i=>$field_str) {
    		    	parse_str($field_str,$field);
    		    	$model['fields'][$i+1] = $field;
    		    }	
		    }
		    // 保存到缓存
            DataCache::set($prefix.$param,$model);
			return $model;
		}
		return null;
    }
    /**
     * 查询所有模型信息
     *
     * @return unknown
     */
    function get_models($state=null) {
	    $db = get_conn(); $result = array();
	    $where = is_null($state)?null:"WHERE `state`='0'";
	    $rs = $db->query("SELECT * FROM `#@_model` {$where} ORDER BY `modelid` ASC;");
	    while ($row = $db->fetch($rs)) {
	        $result[] = ModuleModel::get_model_by_id($row['modelid']);
	    }
	    return $result;
	}
	/**
	 * 创建一个模型
	 *
	 * @param array $data
	 * @return array
	 */
	function create_model($data) {
	    $db = get_conn();
	    $modelid = $db->insert('#@_model',$data);
	    $model   = array_merge($data,array(
	       'modelid' => $modelid,
	    ));
	    return $model;
	}
	/**
	 * 更新模型信息
	 *
	 * @param int $modelid
	 * @param array $data
	 * @return array|null
	 */
	function fill_model($modelid,$data) {
	    $db = get_conn();
	    if ($model = ModuleModel::get_model_by_id($modelid)) {
	    	// 更新数据
            if ($data) {
                $db->update('#@_model',$data,array('modelid'=>$modelid));
            }
            // 清理用户缓存
            ModuleModel::clear_model_cache($modelid);
            return array_merge($model,$data);
	    }
        return null;
	}
	/**
     * 清理缓存
     *
     * @param int $modelid
     * @return bool
     */
    function clear_model_cache($modelid) {
        if ($model = ModuleModel::get_model_by_id($modelid)) {
            $prefix = 'model.';
            foreach (array('modelid','code') as $field) {
                DataCache::delete($prefix.$field.'.'.$model[$field]);
            }
        }
        return true;
    }
    /**
     * 删除
     *
     * @param int $userid
     * @return bool
     */
    function delete_model_by_id($modelid) {
        $db = get_conn(); if (!$modelid) return ;
        if (ModuleModel::get_model_by_id($modelid)) {
            ModuleModel::clear_model_cache($modelid);
            $db->delete('#@_model',array('modelid'=>$modelid));
            // TODO: 删除其他数据
            return true;
        }
        return false;
    }
    /**
     * 取得控件的数据库字段类型
     *
     * @param string $type
     * @return string|array
     */
    function get_type($type=null) {
        $types = array(
            'input'    => _('Input'),                   // 输入框
            'textarea' => _('Textarea'),                // 文本框
            'radio'    => _('Radio'),                   // 单选框
            'checkbox' => _('Checkbox'),                // 复选框
            'select'   => _('Select'),                  // 下拉菜单
            'basic'    => _('Basic editor'),            // 简易编辑器
            'editor'   => _('Adv editor'),              // 内容编辑器
            'date'     => _('Date'),                    // 日期选择器
            'upfile'   => _('Upload file'),             // 文件上传框
        );
        return empty($type) ? $types : $types[$type];
    }
}