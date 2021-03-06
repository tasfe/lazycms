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
 * pdo_sqlite 访问类
 *
 * @author  Lukin <my@lukin.cn>
 * @version $Id$
 */
class DB_PDO_SQLite extends DBQuery {
    /**
     * 初始化链接
     *
     * @param array $config
     * @return bool
     */
    function __construct($config) {
        if (!extension_loaded('pdo_sqlite')) {
            return throw_error(sprintf(__('Your PHP installation appears to be missing the %s extension which is required by LazyCMS.'), 'PDO_SQLite'),E_LAZY_ERROR);
        }
        if (!empty($config)) {
            $this->name = isset($config['name']) ? $config['name'] : $this->name;
            if (strncmp($this->name, '/', 1)!==0 || strpos($this->name, ':')===false) {
                $this->name = ABS_PATH.'/'.$this->name;
            }
            $this->scheme = isset($config['scheme']) ? substr($config['scheme'], 4) : $this->scheme;
            $this->open($this->name);
            if ($this->conn && $this->errno()==0) {
                $this->apply_plugins();
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
    function open($dbname) {
        if (error_reporting()==0 || $this->is_database($dbname)) {
            $this->conn = new PDO(sprintf('%s:%s', $this->scheme, $dbname), null, null);
        } else {
            return throw_error(__('Database is not found!'), E_LAZY_ERROR);
        }
        
        // 验证连接是否正确
        if ($this->errno() != 0) {
            return throw_error(sprintf(__('Database connect error:%s'), $this->error()), E_LAZY_ERROR);
        }
        // 设置字段模式
        $this->conn->exec("PRAGMA short_column_names=ON;");
        return $this->conn;
    }
    /**
     * 执行自定义函数
     *
     * @return void
     */
    function apply_plugins() {
        $this->conn->sqliteCreateFunction('UCASE', 'strtoupper', 1);
        $this->conn->sqliteCreateFunction('FROM_UNIXTIME', array('DBQuery', 'strftime'), 2);
        $this->conn->sqliteCreateFunction('FIND_IN_SET', 'instr', 2);
    }
    /**
     * 执行查询
     *
     * @param string $sql
     * @return bool
     */
    function query($sql){
        // 验证连接是否正确
        if (!is_object($this->conn)) {
            return throw_error(__('Supplied argument is not a valid SQLite object.'),E_LAZY_ERROR);
        }
        $args = func_get_args();

        $sql = call_user_func_array(array(&$this,'prepare'), $args);
        $sql = $this->process($sql, $befores, $afters);

        if ( preg_match("/^\\s*(insert|delete|update|replace|alter table|create) /i",$sql) ) {
        	$func = 'exec';
        } else {
        	$func = 'query';
        }
        // 执行前置sql
        if ($befores) {
            foreach ($befores as $v) $this->query($v);
        }
        $this->sql = $sql;
        $result = $this->conn->$func($sql);
        if ($this->errno() != 0) {
            return throw_error(sprintf(__("SQLite Query Error:%s"),$sql."\r\n\t".$this->error()),E_LAZY_ERROR);
        }
        // 查询正常
        else {
            // 执行后置 SQL
            if ($afters) {
                foreach ($afters as $v) $this->query($v);
            }
            // 返回结果
            if ($func == 'exec') {
                if ( preg_match("/^\\s*(insert|replace) /i", $sql) ) {
                    $result = ($insert_id = $this->conn->lastInsertId()) >= 0 ? $insert_id : $this->result("SELECT LAST_INSERT_ROWID();");
                } else {
                    $result = $this->result("SELECT CHANGES();");
                }
            }
        }
        return $result;
    }
    /**
     * 取得数据集的单条记录
     *
     * @param PDOStatement $result
     * @param int $mode
     * @return array
     */
    function fetch($result, $mode=1){
        switch (intval($mode)) {
            case 0: $mode = PDO::FETCH_NUM;break;
            case 1: $mode = PDO::FETCH_ASSOC;break;
            case 2: $mode = PDO::FETCH_BOTH;break;
        }
        return $result->fetch($mode);
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
        return $this->conn->getAttribute(PDO::ATTR_CLIENT_VERSION);
    }
    /**
     * 错误码
     *
     * @return int
     */
    function errno() {
        $errno = $this->conn->errorCode();
        return empty($errno) ||$errno=='00000' ? 0 : $errno;
    }
    /**
     * 错误信息
     *
     * @return string
     */
    function error() {
        $error = $this->conn->errorInfo();
        return $error[2];
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

        return str_replace("'", "''", $value);
    }
    /**
     * 类构造
     *
     * @return void
     */
    function DB_PDO_SQLite() {
        // 调用PHP的构造函数
        $args = func_get_args();
		call_user_func_array( array(&$this, '__construct'), $args );
    }
}
