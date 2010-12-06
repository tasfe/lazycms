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
 * sqlite2 访问类
 *
 * @author  Lukin <my@lukin.cn>
 * @version $Id$
 */
class DB_Sqlite2 extends DBQuery {
    // 持久链接
    var $pconnect = false;
    /**
     * 初始化链接
     *
     * @param array $config
     * @return bool
     */
    function __construct($config) {
        if (!function_exists('sqlite_query')) {
            return throw_error(sprintf(__('Your PHP installation appears to be missing the %s extension which is required by LazyCMS.'), 'SQLite2'),E_LAZY_ERROR);
        }
        if (!empty($config)) {
            $this->name     = isset($config['name']) ? $config['name'] : $this->name;
            $this->pconnect = isset($config['pconnect']) ? $config['pconnect'] : $this->pconnect;
            if (strncmp($this->name, '/', 1)!==0 || strpos($this->name, ':')===false) {
                $this->name = ABS_PATH.'/'.$this->name;
            }
            $this->open($this->name);
            if ($this->conn && sqlite_last_error($this->conn)==0) {
                $this->ready = true;
            }
        }
    }
    /**
     * 打开数据库
     *
     * @param string $dbname
     * @param int $flags
     * @return bool
     */
    function open($dbname, $mode=0666) {
        // 连接数据库
        if (function_exists('sqlite_popen') && $this->pconnect) {
            $this->conn = sqlite_popen($dbname, $mode, $error);
        } elseif (function_exists('sqlite_open')) {
            $this->conn = sqlite_open($dbname, $mode, $error);
        }
        // 验证连接是否正确
        if (!$this->conn) {
            return throw_error(sprintf(__('Database connect error:%s'), $error), E_LAZY_ERROR);
        }
        return $this->conn;
    }
    /**
     * 执行查询
     *
     * @param string $sql
     * @return bool
     */
    function query($sql){
        // 验证连接是否正确
        if (!$this->conn) {
            return throw_error(__('Supplied argument is not a valid SQLite-Link resource.'),E_LAZY_ERROR);
        }
        $args = func_get_args(); $afters = array();

        $sql = call_user_func_array(array(&$this,'prepare'), $args);
        $sql = $this->process($sql, $afters);

        if ( preg_match("/^\\s*(insert|delete|update|replace|alter table|create) /i",$sql) ) {
        	$func = 'sqlite_exec';
        } else {
        	$func = 'sqlite_query';
        }
        $this->sql = $sql;
        if (!($result = $func($this->conn, $sql))) {
            return throw_error(sprintf(__("SQLite Query Error:%s"),$sql."\r\n\t".sqlite_error_string(sqlite_last_error($this->conn))),E_LAZY_ERROR);
        }
        // 查询正常
        else {
            // 执行后置 SQL
            if ($afters) {
                foreach ($afters as $v) $this->query($v);
            }
            // 返回结果
            if ($func == 'sqlite_exec') {
                if ( preg_match("/^\\s*(insert|replace) /i", $sql) ) {
                    $result = ($insert_id = sqlite_last_insert_rowid($this->conn)) >= 0 ? $insert_id : $this->result("SELECT LAST_INSERT_ROWID();");
                } else {
                    $result = sqlite_changes($this->conn);
                }
            }
        }
        return $result;
    }
    /**
     * 取得数据集的单条记录
     *
     * @param resource $result
     * @param int $mode
     * @return array
     */
    function fetch($result,$mode=1){
        switch (intval($mode)) {
            case 0: $mode = SQLITE_NUM;break;
            case 1: $mode = SQLITE_ASSOC;break;
            case 2: $mode = SQLITE_BOTH;break;
        }
        return sqlite_fetch_array($result, $mode);
    }
    /**
     * 检查是否存在数据库
     *
     * @param string $dbname
     * @return bool
     */
    function is_database($dbname) {
        return is_file($dbname);
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
        $res = $this->query("SELECT `name` FROM `sqlite_master` WHERE `type`='table';");
        if (!strncasecmp($table,'#@_',3))
            $table = str_replace('#@_', $this->prefix, $table);

        while ($rs = $this->fetch($res,0)) {
        	if ($table == $rs[0]) return true;
        }
        return false;
    }
    /**
     * 列出表里的所有字段
     *
     * @param string $table    表名
     */
    function list_fields($table){
        $result = array();
        $res    = $this->query(sprintf("PRAGMA table_info(%s);", $table));
        while ($row = $this->fetch($res)) {
            $result[] = $row['name'];
        }
        return $result;
    }
    /**
     * SQLite 版本
     *
     * @return string
     */
    function version(){
        return sqlite_libversion();
    }
    /**
     * 关闭链接
     *
     * @return bool
     */
    function close(){
        if (is_resource($this->conn)) {
            return sqlite_close($this->conn);
        }
    }
    /**
     * 转义SQL语句
     *
     * @param mixed $value
     * @return string
     */
    function escape($value){
        // 空
        if ($value === null) return '';
        // 转义变量
        $value = $this->envalue($value);

        return sqlite_escape_string($value);
    }
    /**
     * 类构造
     *
     * @return void
     */
    function DB_Sqlite2() {
        // 添加PHP4下的析构函数
        register_shutdown_function( array(&$this, '__destruct') );

        // 调用PHP的构造函数
        $args = func_get_args();
		call_user_func_array( array(&$this, '__construct'), $args );
    }
    /**
     * 类析构
     *
     * @return void
     */
    function __destruct(){
    	$this->close();
    }
}
