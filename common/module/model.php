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
 * 通过模型ID查询模型信息
 *
 * @param int $modelid
 * @param string $field
 * @return array|null
 */
function model_get_byid($modelid,$field='*') {
    $modelid = intval($modelid);
    $model = model_get($modelid,0);
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
function model_get_bycode($code,$field='*') {
    $language = null;
    if (($pos=strpos($code,':'))!==false) {
        $language = mb_substr($code,0,$pos);
        $code     = mb_substr($code,$pos+1);
    }
    $model = model_get($code,1,$language);
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
 * @param string $language  语言
 * @return array|null
 */
function model_get($param,$type=0,$language=null) {
    $db = get_conn(); if ((int)$type>2) return null;
    $ckeys = array('model.modelid.','model.code.');
    if ($type==1) {
        $language = $language==null?language():$language;
        $ckey = sprintf('%s%s.',$ckeys[$type],$language);
    } else {
        $ckey = $ckeys[$type];
    }
    $model = fcache_get($ckey.$param);
    if ($model !== null) return $model;

    switch($type){
        case 0:
            $where = sprintf("WHERE `modelid`=%d",esc_sql($param));
            break;
        case 1:
            $where = sprintf("WHERE `language`='%s' AND `code`='%s'",esc_sql($language),esc_sql($param));
            break;
    }
    $rs = $db->query("SELECT * FROM `#@_model` {$where} LIMIT 1 OFFSET 0;");
    // 判断是否存在
    if ($model = $db->fetch($rs)) {
        $model['langcode'] = sprintf('%s:%s',$model['language'],$model['code']);
        $fields = unserialize($model['fields']);
        $model['fields'] = array();
        if (is_array($fields)) {
            foreach ($fields as $i=>$field_str) {
                parse_str($field_str,$field);
                $field['_n'] = '_'.$field['n'];
                $model['fields'][$i+1] = $field;
            }
        }
        // 保存到缓存
        fcache_set($ckey.$param,$model);
        return $model;
    }
    return null;
}
/**
 * 查询所有模型信息
 *
 * @return unknown
 */
function model_gets($state=null) {
    $db = get_conn(); $result = array(); $conditions = array();
    $where = is_null($state) ? null : sprintf("WHERE `state`='%s'",esc_sql($state));
    $rs = $db->query("SELECT * FROM `#@_model` {$where} ORDER BY `modelid` ASC;");
    while ($row = $db->fetch($rs)) {
        $result[] = model_get_byid($row['modelid']);
    }
    return $result;
}
/**
 * 创建一个模型
 *
 * @param array $data
 * @return array
 */
function model_add($data) {
    $db = get_conn();
    if (!is_array($data)) return false;
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
function model_edit($modelid,$data) {
    $db = get_conn(); $modelid = intval($modelid);
    $data = is_array($data) ? $data : array();
    if ($model = model_get_byid($modelid)) {
        // 更新数据
        if ($data) {
            $db->update('#@_model',$data,array('modelid'=>$modelid));
        }
        // 清理用户缓存
        model_clean_cache($modelid);
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
function model_clean_cache($modelid) {
    if ($model = model_get_byid($modelid)) {
        $ckey = 'model.';
        foreach (array('modelid','code') as $field) {
            if ($field=='modelid') {
                fcache_delete(sprintf('%s%s.%s',$ckey,$field,$model[$field]));
            } else {
                fcache_delete(sprintf('%s%s.%s.%s',$ckey,$field,$model['language'],$model[$field]));
            }
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
function model_delete($modelid) {
    $db = get_conn();
    $modelid = intval($modelid);
    if (!$modelid) return false;
    if (model_get_byid($modelid)) {
        model_clean_cache($modelid);
        $db->delete('#@_model',array('modelid'=>$modelid));
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
function model_get_types($type=null) {
    $types = array(
        'input'    => __('Input'),                   // 输入框
        'textarea' => __('Textarea'),                // 文本框
        'radio'    => __('Radio'),                   // 单选框
        'checkbox' => __('Check box'),               // 复选框
        'select'   => __('Drop-down menu'),          // 下拉菜单
        'basic'    => __('Basic editor'),            // 简易编辑器
        'editor'   => __('Adv editor'),              // 内容编辑器
        'date'     => __('Date Selector'),           // 日期选择器
        'upfile'   => __('File upload box'),         // 文件上传框
    );
    return empty($type) ? $types : $types[$type];
}