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
/**
 * 模型函数
 */
class Content_Model{
    /**
     * 根据模型标识，取得数据表名
     *
     * @param string $p1    模型标识
     * @return string
     */
    function getDataTableName($p1){
        $db = get_conn(); if (strlen($p1)==0) { return false; }
        return str_replace('#@_',$db->config('prefix'),"#@_content_data_{$p1}");
    }
    /**
     * 根据模型标识，取得关联表名
     *
     * @param string $p1    模型标识
     * @return string
     */
    function getJoinTableName($p1){
        $db = get_conn(); if (strlen($p1)==0) { return false; }
        return str_replace('#@_',$db->config('prefix'),"#@_content_join_{$p1}");
    }
    /**
     * 取得控件的数据库字段类型
     *
     * @param string $p1
     * @return string
     */
    function getType($p1=null){
        $R = array(
            'input'    => 'varchar',        // 输入框
            'textarea' => 'text',           // 文本框
            'radio'    => 'varchar',   // 单选框
            'checkbox' => 'varchar',   // 复选框
            'select'   => 'varchar',   // 下拉菜单
            'basic'    => 'text',           // 简易编辑器
            'editor'   => 'mediumtext',     // 内容编辑器
            'date'     => 'int(11)',        // 日期选择器
            'upfile'   => 'varchar',   // 文件上传框
        );
        return empty($p1) ? $R : $R[$p1];
    }
    /**
     * 取得验证规则
     *
     * @return string
     */
    function getValidate(){
        return array(
            'empty'  => '%s|0|'.t('validate/error/empty'),
            'limit'  => '%s|1|'.t('validate/error/limit').'|1-100',
            'equal'  => '%s|2|'.t('validate/error/equal').'|[field]',
            'email'  => '%s|validate|'.t('validate/error/email').'|4',
            'letter' => '%s|validate|'.t('validate/error/letter').'|1',
            'number' => '%s|validate|'.t('validate/error/number').'|2',
            'url'    => '%s|validate|'.t('validate/error/url').'|5',
            'custom' => '%s|validate|'.t('validate/error/custom').'|'.t('validate/error/regular'),
        );
    }
    /**
     * 取得需要删除的列名
     *
     * @param array $p1     当前数据库的列数组
     * @param array $p2     字段数组
     * @return array
     */
    function diffFields($p1,$p2){
        $fields = array();
        foreach ($p2 as $field) {
            $fields[] = $field->ename;
        }
        $fields = array_merge($fields,array('id','sortid','order','date','hits','path','digg','passed','userid','keywords','description'));
        return array_diff($p1,$fields);
    }
    /**
     * 取得单个模型的所有信息
     *
     * @param string $p1    模型标识
     * @return array
     */
    function getModelByEname($p1){
        $db = get_conn();
        $res = $db->query("SELECT * FROM `#@_content_model` WHERE `modelstate`=1 AND `modelename`=?;",$p1);
        if ($rs = $db->fetch($res)) {
            return $rs;
        }
        return array();
    }
    /**
     * 取得单个模型的所有信息
     *
     * @param string $p1    模型ID
     * @return array
     */
    function getModelById($p1){
        $db = get_conn();
        $res = $db->query("SELECT * FROM `#@_content_model` WHERE `modelstate`=1 AND `modelid`=?;",$p1);
        if ($rs = $db->fetch($res)) {
            return $rs;
        }
        return array();
    }
	/**
     * 根据分类ID取得关联模型的数据
     *
     * @param  integer  $p1     分类ID
     * @param  string   $p2     数据库字段
     * @return array
     */
    function getModelsBySortId($p1,$p2=array('modelid')){
        $db = get_conn(); $R = array(); $l = count($p2); $p2 = $l>1?$p2:array_pop($p2);
        $res = $db->query("SELECT * FROM `#@_content_sort_join` AS `csm` LEFT JOIN `#@_content_model` AS `cm` ON `csm`.`modelid`=`cm`.`modelid` WHERE `cm`.`modelstate`=1 AND `csm`.`sortid`=?;",$p1);
        while ($rs = $db->fetch($res)) {
            if ($l > 1) {
                foreach ($p2 as $f){
                    $R[$f][] = $rs[$f];
                }
            } else {
                $R[] = $rs[$p2];
            }
        }
        return $R;
    }
    /**
     * 根据模型类型，取得多个模型的数据
     *
     * @param array $p1 (optional)  为空则取得所有模型信息
     * @return array
     */
    function getModelsByType($p1=null){
        $db = get_conn(); $R = array();
        $in = empty($p1) ? null : DB::quoteInto('AND `modeltype` IN(?)',(is_array($p1)?implode("','",$p1):$p1));
        $res = $db->query("SELECT * FROM `#@_content_model` WHERE `modelstate`=1 {$in} ORDER BY `modelid` ASC");
        while ($rs = $db->fetch($res)) {
            $R[] = $rs;
        }
        return empty($R)?false:$R;
    }
    /**
     * 添加一个模型
     *
     * @param array $model  模型基本数据
     * @return int          最后插入模型的id
     */
    function addModel($model){
        $db = get_conn();
        // 数据表名
        $table  = Content_Model::getDataTableName($model['modelename']);
        // 关联表名
        $jtable = Content_Model::getJoinTableName($model['modelename']);
        // 解析字段
        $fields = json_decode($model['modelfields']);
        $sute   = null;
        foreach ($fields as $v) {
            $data = (array) $v;
            $len  = instr('input,radio,checkbox,select,upfile',$data['intype'])?'('.$data['length'].')':null;
            $type = Content_Model::getType($data['intype']);
            $type = strpos($type,')')===false ? $type.$len : $type;
            $def  = empty($data['default'])?null:" DEFAULT '".$data['default']."'";
            $sute.= ",\n`".$data['ename']."` {$type} {$def}";
        }
        // 先删除表
        $db->exec("DROP TABLE IF EXISTS `{$table}`;");
        // 创建表
        $SQL = "CREATE TABLE IF NOT EXISTS `{$table}` (";
        $SQL.= "    `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,";
        $SQL.= "    `sortid` INT(11) DEFAULT '0',";
        $SQL.= "    `order` INT(11) DEFAULT '0',";
        $SQL.= "    `date` INT(11) DEFAULT '0',";
        $SQL.= "    `hits` INT(11) DEFAULT '0',";
        $SQL.= "    `digg` INT(11) DEFAULT '0',";
        $SQL.= "    `passed` TINYINT(1) DEFAULT '0',";
        $SQL.= "    `userid` INT(11) DEFAULT '0',";
        $SQL.= "    `path` VARCHAR(255),";
        $SQL.= "    `description` VARCHAR(255),";
        $SQL.= "    KEY `sortid` (`sortid`),";
        $SQL.= "    UNIQUE (`path`) {$sute}";
        $SQL.= ") ENGINE=MyISAM DEFAULT CHARSET=#~lang~#;";
        $db->exec($SQL);
        // 创建关联表
        $db->exec("
        CREATE TABLE IF NOT EXISTS `{$jtable}` (
            `jid` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `tid` INT(11) NOT NULL,
            `sid` INT(11) NOT NULL,
            `type` INT(11) NOT NULL,
            KEY `tid` (`tid`),
            KEY `sid` (`sid`)
        ) ENGINE=MyISAM DEFAULT CHARSET=#~lang~#;");
        // 执行数据插入
        $db->insert('#@_content_model',$model);
        return $db->lastId();
    }
}