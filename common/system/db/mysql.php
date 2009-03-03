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
 * MySQL 操作类
 */
class lazy_mysql extends DB {
    // Defautl port
    var $_port = 3306;
    /**
     * 兼容PHP5模式
     */
    function __construct(){
        // 添加PHP4下的析构函数
        register_shutdown_function(array(&$this, '__destruct'));
        // 添加额外的参数，不能添加 host,port,user,pwd,name,prefix
        $config = array(
            'lang'  => 'utf8',
        );
        $this->config($config);
    }
    /**
     * 初始化类
     */
    function lazy_mysql(){
        $this->__construct();
    }
    /**
     * 连接MySQL
     *
     * @return resource
     */
    function connect(){
        if ($this->_conn) { return $this->_conn; }
        // 连接数据库
        if (function_exists('mysql_connect')) {
            $this->_conn = @mysql_connect($this->config('host').':'.$this->config('port'),$this->config('user'),$this->config('pwd'));
        } else {
            trigger_error(t('system::database/noextension',array($this->config('scheme'))));
        }
        // 验证连接是否正确
        if (!$this->_conn) {
            trigger_error(t('system::database/nolink'));
        }
        return $this->_conn;
    }
    /**
     * 选择数据库
     *
     * @param string $db (optional)
     * @return resource
     */
    function select_db($db=null){
        // 验证连接是否正确
        if (!$this->_conn) {
            $this->connect();
        }
        if (empty($db)) {
            $select = mysql_select_db($this->config('name'),$this->_conn);
        } else {
            $select = mysql_select_db($db,$this->_conn);
        }
        // 选择数据库
        if (!$select) {
            trigger_error(t('system::database/noselectdb',array($this->config('name'))));
        }
        mysql_query("SET character_set_connection=".$this->config('lang').", character_set_results=".$this->config('lang').", character_set_client=binary;");
        if (version_compare($this->version(), '4.1', '<' )) {
            trigger_error(t('system::database/versionlower',array($this->config('scheme'),'4.1')));
        }
        if(version_compare($this->version(), '5.0.1', '>' )) {
            mysql_query("SET sql_mode='';");
        }
        return $this->_conn;
    }
    /**
     * 指定函数执行SQL语句
     *
     * @param string $sql
     * @param string $func
     * @param string $type
     * @return resource
     */
    function _execute($sql,$func,$type=''){
        $this->connect();
        $sql = preg_replace('/\#\~(.+)\~\#/e','$this->config(\'\\1\')',$sql);
        $sql = preg_replace('/`(#@_)(\w+)`/i','`'.$this->config('prefix').'$2`',$sql);
        $this->_sql = $sql;
        if(!($R= $func($sql,$this->_conn))){
            if(in_array($this->errno(),array(2006,2013)) && substr($type,0,5) != 'RETRY') {
                $this->close();$this->connect();
                $this->_execute($sql,$func,'RETRY'.$type);
            } elseif($type != 'SILENT' && substr($type, 5) != 'SILENT') {
                trigger_error('MySQL Query Error:<br/>SQL:'.$sql."<br>".$this->error());
            }
        }
        return $R;
    }
    /**
     * 判断数据表是否存在
     *
     * @param string $p1    table
     * @return bool
     */
    function isTable($p1){
        $tbl = str_replace('#@_',$this->config('prefix'),$p1);
        $res = mysql_list_tables($this->config('name'), $this->_conn);
        while ($rs = mysql_fetch_row($res)) {
            if (strtolower($tbl)==strtolower($rs[0])) {
                $this->free($res);
                return true;
            }
        }
        $this->free($res);
        return false;
    }
    /**
     * 列出表里的所有字段
     *
     * @param string $p1    表名
     */
    function listFields($p1){
        $result  = array();
        $table   = str_replace('#@_',$this->config('prefix'),$p1);
        $fields  = mysql_list_fields($this->config('name'), $table, $this->_conn);
        $columns = mysql_num_fields($fields);
        for ($i=0; $i<$columns; $i++) {
            $result[] = mysql_field_name($fields, $i);
        }
        $this->free($fields);
        return $result;
    }
    /**
     * 判断列名是否存在
     *
     * @param string $p1    table
     * @param string $p2    field
     * @return bool
     */
    function isField($p1,$p2){
        return in_array($p2,$this->listFields($p1));
    }
    /**
     * 批量执行SQL
     * 
     * @param string $p1 SQL
     */
    function batQuery($p1){
        if (empty($p1)) { return ; }
        $p1 = preg_replace('/\#\~(.+)\~\#/e','$this->config(\'\\1\')',$p1);
        $p1 = preg_replace('/`(#@_)(\w+)`/i','`'.$this->config('prefix').'$2`',$p1,1);
        $p1 = str_replace(chr(10).chr(10),chr(10),str_replace(chr(13),chr(10),$p1));
        $R1 = explode(chr(10),$p1);
        $R2 = create_function('&$p1,$p2','$p1=trim($p1);');array_walk($R1,$R2);
        $R3 = "";
        foreach ($R1 as $v) {
            if (preg_match('/;$/',$v)) {
                $R3 .= $v;
                // 执行sql
                $this->exec($R3);
                // 置空
                $R3 = '';
            } elseif (!preg_match('/^\-\-/',$v) && !preg_match('/^\/\//',$v) && !preg_match('/^\/\*/',$v) && !preg_match('/^#/',$v)) {
                $p2 = strrpos($v,'# ');
                if ($p2!==false) {
                    $p3 = trim(substr($v,0,$p2));
                    if (substr($p3,-1)==',') {
                        $v = $p3;
                    }
                }
                $R3.= $v."\n";
            }
        }
    }
    /**
     * 等同于 mysql_query
     *
     * @param string $sql
     * @param array  $bind
     * @return resource
     */
    function query($sql,$bind=''){
        if ((string)$bind!='') { $sql = $this->quoteInto($sql,$bind); }
        return $this->_execute($sql,'mysql_query');
    }
    /**
     * 等同于 mysql_unbuffered_query
     *
     * @param string $sql
     * @param array  $bind
     * @return bool
     */
    function exec($sql,$bind=''){
        if ((string)$bind!='') { $sql = $this->quoteInto($sql,$bind); }
        return $this->_execute($sql,'mysql_unbuffered_query');
    }
    /**
     * 等同于 mysql_result
     *
     * @param mixed $p1 可以是MYSQL资源句柄，也可以使用MYSQL语句
     * @param int   $p2 偏移量
     * @return string
     */
    function result($p1,$p2=0) {
        if (is_resource($p1)) {
            $R1 = $p1;
        } else {
            $R1 = $this->query($p1);
        }
        if ($data = $this->fetch($R1,0)) {
            return $data[$p2];
        }
    }
    /**
     * 取得数据集的单条记录
     *
     * @param resource  $rs
     * @param int       $type
     * @return array
     */
    function fetch($rs=null,$type=1){
        switch (intval($type)) {
            case 0: $R = MYSQL_NUM;break;
            case 1: $R = MYSQL_ASSOC;break;
            case 2: $R = MYSQL_BOTH;break;
        }
        return mysql_fetch_array($rs,$R);
    }
    /**
     * 统计记录数
     *
     * @param resource $rs
     * @return int
     */
    function count($rs=null){
        if (is_resource($rs)) {
            $R = $rs;
        } else {
            $R = $this->query($rs);
        }
        return mysql_num_rows($R);
    }
    /**
     * 取得前一次 MySQL 操作所影响的记录行数
     *
     * @return int
     */
    function affected_rows(){
        return mysql_affected_rows($this->_conn);
    }
    /**
     * 取得上一步 INSERT 操作产生的 ID 
     *
     * @return string
     */
    function lastId() {
        return ($R = mysql_insert_id($this->_conn)) >= 0 ? $R : $this->result("SELECT last_insert_id();");
    }
    /**
     * 返回上一个 MySQL 操作产生的文本错误信息 
     *
     * @return string
     */
    function error() {
        return (($this->_conn) ? mysql_error($this->_conn) : mysql_error());
    }
    /**
     * 返回上一个 MySQL 操作中的错误信息的数字编码
     *
     * @return int
     */
    function errno() {
        return intval(($this->_conn) ? mysql_errno($this->_conn) : mysql_errno());
    }
    /**
     * 取得 MySQL 服务器信息
     *
     * @return string
     */
    function version(){
        return mysql_get_server_info($this->_conn);
    }
    /**
     * 释放结果内存
     *
     * @param resource $p1
     * @return bool
     */
    function free($p1){
        if (is_resource($p1)) {
            return mysql_free_result($p1);
        }
    }
    /**
     * 关闭 MySQL 连接
     *
     * @return bool
     */
    function close(){
        if (is_resource($this->_conn)) {
            return mysql_close($this->_conn);
        }
    }
}