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
// 定义返回类型
define('CLIENT_MULTI_RESULTS', 0x20000);
/**
 * mysql访问类
 *
 */
class Mysql{
	// public
    var $conn  = null;
    var $ready = true;
    // private
    var $_host = 'localhost';
    var $_port = 3306;
    var $_user = 'root';
    var $_pwd  = '';
    var $_name = 'test';
    var $_prefix = null;
    var $_scheme = 'mysql';

    function Mysql() {
		// 添加PHP4下的析构函数
        register_shutdown_function( array(&$this, '__destruct') );
    }

    function __destruct(){
    	$this->close();
    }
    /**
     * 设置数据库连接参数
     *
     * @param mixed $config
     * @return string
     */
    function config($config) {
    	if (is_array($config)) {
    		foreach ($config as $k=>$v) {
    			if (!empty($v)) {
    				$property = "_{$k}";
    				$this->$property = $v;
    			}
    		}
        } else {
        	$property = "_{$config}";
            return isset($this->$property)?$this->$property:null;
        }
    }
    /**
     * 连接MySQL
     *
     */
    function connect($pconnect=false){
        // 连接数据库
        if (function_exists('mysql_pconnect') && $pconnect) {
            $this->conn = @mysql_pconnect($this->_host.':'.$this->_port,$this->_user,$this->_pwd,CLIENT_MULTI_RESULTS);
        } elseif (function_exists('mysql_connect')) {
            $this->conn = @mysql_connect($this->_host.':'.$this->_port,$this->_user,$this->_pwd,false,CLIENT_MULTI_RESULTS);
        } else {
            $this->ready = false; trigger_error(_('-_-!! Please open the mysql extension!'),E_USER_ERROR);
        }
        // 验证连接是否正确
        if (!$this->conn) {
            $this->ready = false; trigger_error(_('Database connect error, please check the database settings!'),E_USER_ERROR);
        }
    }
    /**
     * 选择数据库
     *
     * @param string $db (optional)
     */
    function select_db($db=null){
        // 验证连接是否正确
        if (!$this->conn) $this->connect();
        if (empty($db)) $db = $this->_name;
        // 选择数据库
        if (!mysql_select_db($db,$this->conn)) {
            $this->ready = false; trigger_error(sprintf(_('%s database not found!'),$db),E_USER_ERROR);
        }
        // MYSQL数据库的设置
        if (version_compare($this->version(), '4.1', '>=')) {
        	mysql_query("SET NAMES utf8;",$this->conn);
        	if(version_compare($this->version(), '5.0.1', '>' )) {
	            mysql_query("SET sql_mode='';",$this->conn);
	        }
        } else {
        	$this->ready = false; trigger_error(_('mysql database version lower than 4.1, please upgrade mysql!'),E_USER_WARNING);
        }
    }
    /**
     * 指定函数执行SQL语句
     *
     * @param string $sql	sql语句
     * @param mixed  $bind	占位符 可以是字符串，也可以是数组
     * @param string $type	类型
     * @return resource
     */
    function query($sql,$bind='`*([BIND_NULL])*`',$type=''){
    	// 替换占位符
    	if (is_array($bind)) {
    		$sql = vsprintf($sql,$this->escape($bind));
    	} elseif ($bind!=='`*([BIND_NULL])*`') {
    		$sql = sprintf($sql,$this->escape($bind));
    	} 
        $sql = preg_replace('/`(#@_)(\w+)`/i','`'.$this->_prefix.'$2`',$sql);
        if ( preg_match("/^\\s*(insert|delete|update|replace|alter) /i",$sql) ) {
        	$func = 'mysql_unbuffered_query';
        } else {
        	$func = 'mysql_query';
        }
        $this->last_query = $sql;
        if (!($result = $func($sql,$this->conn))) {
            if (in_array($this->errno(),array(2006,2013)) && substr($type,0,5) != 'RETRY') {
                $this->close();$this->connect(); $this->select_db();
                $this->query($sql,$bind,'RETRY'.$type);
            } elseif($type != 'SILENT' && substr($type, 5) != 'SILENT') {
                trigger_error(sprintf(_('MySQL Query Error:<br/>SQL:%s<br/>%s'),$sql,$this->error()),E_USER_WARNING);
            }
        }
        if ($func=='mysql_unbuffered_query') {
            if ( preg_match("/^\\s*(insert|replace) /i",$sql) ) {
                $result = $this->insert_id();
            } else {
                $result = $this->affected_rows();
            }
        }
        return $result;
    }
    /**
     * 等同于 mysql_result
     *
     * @param mixed $result 可以是MYSQL资源句柄，也可以使用MYSQL语句
     * @param int   $result 偏移量
     * @return string
     */
    function result($result,$row=0) {
    	if (!is_resource($result)) {
    		$result = $this->query($result);
    	}
        if ($rs = $this->fetch($result,0)) {
            return $rs[$row];
        }
    }
    /**
     * 取得数据集的单条记录
     *
     * @param resource  $result
     * @param int       $type
     * @return array
     */
    function fetch($result,$type=1){
        switch (intval($type)) {
            case 0: $type = MYSQL_NUM;break;
            case 1: $type = MYSQL_ASSOC;break;
            case 2: $type = MYSQL_BOTH;break;
        }
        return mysql_fetch_array($result,$type);
    }
	/**
     * 插入数据
     *
     * @param string $table    table
     * @param array  $data     插入数据的数组，key对应列名，value对应值
     * @return int
     */
    function insert($table,$data){
        $cols = array();
        $vals = array();
        foreach ($data as $col => $val) {
            $cols[] = $this->identifier($col);
            $vals[] = $this->escape($val);
        }

        $sql = "INSERT INTO "
             . $this->identifier($table)
             . ' (' . implode(', ', $cols) . ') '
             . 'VALUES (' . implode(', ', $vals) . ')';

             return $this->query($sql);
    }
    /**
     * 更新数据
     *
     * @param string $table    table
     * @param array  $sets     set 数组
     * @param mixed  $where    where语句，支持数组，数组默认使用 AND 连接
     * @return int
     */
    function update($table,$sets,$where=null){
        // extract and quote col names from the array keys
        $set = array();
        foreach ($sets as $col => $val) {
            $set[] = $this->identifier($col).' = '.$this->escape($val);
        }
        $where = $this->where($where);
        // build the statement
        $sql = "UPDATE "
             . $this->identifier($table)
             . ' SET ' . implode(', ', $set)
             . (($where) ? " WHERE {$where}" : '');

        return $this->query($sql);
    }
    /**
     * 删除数据
     *
     * @param string $table
     * @param string $where
     * @return int
     */
    function delete($table,$where=null){
        $where = $this->where($where);
        // build the statement
        $sql = "DELETE FROM "
             . $this->identifier($table)
             . (($where) ? " WHERE {$where}" : '');

        return $this->query($sql);
    }
    /**
     * 列出表里的所有字段
     *
     * @param string $table    表名
     */
    function list_fields($table){
        $result = array();
        $res = $this->query("SHOW COLUMNS FROM {$table};");
        while ($rs = $this->fetch($res)) {
        	$result[] = $rs['Field'];
        }
        return $result;
    }
    /**
     * 判断数据表是否存在
     *
     * 注意表名的大小写，是有区别的
     *
     * @param string $table    table
     * @return bool
     */
    function is_table($table){
        $res = $this->query("SHOW TABLES FROM {$this->_name};");
        while ($rs = $this->fetch($res)) {
        	if ($table == $rs[0]) return true;
        }
        return false;
    }
    /**
     * 判断列名是否存在
     *
     * @param string $p1    table
     * @param string $p2    field
     * @return bool
     */
    function is_field($table,$field){
        return in_array($field,$this->list_fields($table));
    }
    /**
     * 取得前一次 MySQL 操作所影响的记录行数
     *
     * @return int
     */
    function affected_rows(){
        return mysql_affected_rows($this->conn);
    }
    /**
     * 取得上一步 INSERT 操作产生的 ID
     *
     * @return string
     */
    function insert_id() {
        return ($insert_id = mysql_insert_id($this->conn)) >= 0 ? $insert_id : $this->result("SELECT last_insert_id();");
    }
    /**
     * 返回上一个 MySQL 操作产生的文本错误信息
     *
     * @return string
     */
    function error() {
        return (($this->conn) ? mysql_error($this->conn) : mysql_error());
    }
    /**
     * 返回上一个 MySQL 操作中的错误信息的数字编码
     *
     * @return int
     */
    function errno() {
        return intval(($this->conn) ? mysql_errno($this->conn) : mysql_errno());
    }
    /**
     * 取得 MySQL 服务器信息
     *
     * @return string
     */
    function version(){
        return mysql_get_server_info($this->conn);
    }
    /**
     * 释放结果内存
     *
     * @param resource $conn
     * @return bool
     */
    function free($conn){
        if (is_resource($conn)) {
            return mysql_free_result($conn);
        }
    }
    /**
     * 关闭 MySQL 连接
     *
     * @return bool
     */
    function close(){
        if (is_resource($this->conn)) {
            return mysql_close($this->conn);
        }
    }
    /**
     * where语句组合
     *
     * @param mixed $data where语句，支持数组，数组默认使用 AND 连接
     * @return string
     */
    function where($data) {
        if (empty($data)) {
            return ;
        }
        if (is_string($data)) {
            return $data;
        }
        $cond = array();
        foreach ($data as $field => $value) {
            $cond[] = '(' . $this->identifier($field) .'='. $this->escape($value) . ')';
        }
        $sql = implode(' AND ', $cond);
        return $sql;
    }
	/**
     * 转义SQL语句
     *
     * @param mixed $data
     * @return string
     */
    function escape($data){
        if ( is_array($data) ) {
			foreach ( (array) $data as $k => $v ) {
				if ( is_array($v) )
					$data[$k] = $this->escape( $v );
				else
					$data[$k] = $this->_real_escape( $v );
			}
		} else {
			$data = $this->_real_escape( $data );
		}
		return $data;
    }
    function _real_escape($str) {
		if ( $this->conn )
			$str = mysql_real_escape_string( $str, $this->conn );
		else
			$str = addslashes( $str );

		if (is_int($str) || is_float($str)) {
            return $str;
        }

        return "'{$str}'";
	}
	/**
     * 转义SQL关键字
     *
     * @param string $filed
     * @return string
     */
    function identifier($filed){
        $result = null;
        // 检测是否是多个字段
        if (strpos($filed,',') !== false) {
            // 多个字段，递归执行
            $fileds = explode(',',$filed);
            foreach ($fileds as $k=>$v) {
                if (empty($result)) {
                    $result = $this->identifier($v);
                } else {
                    $result.= ','.$this->identifier($v);
                }
            }
            return $result;
        } else {
            // 解析各个字段
            if (strpos($filed,'.') !== false) {
                $fileds = explode('.',$filed);
                $_table = trim($fileds[0]);
                $_filed = trim($fileds[1]);
                $_as    = chr(32).'AS'.chr(32);
                if (stripos($_filed,$_as) !== false) {
                    $_filed = sprintf("`%s`%s`%s`",trim(substr($_filed,0,stripos($_filed,$_as))),$_as,trim(substr($_filed,stripos($_filed,$_as)+4)));
                }
                return sprintf("`%s`.%s",$_table,$_filed);
            } else {
                return sprintf("`%s`",$filed);
            }
        }
    }
}
