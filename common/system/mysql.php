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
// 定义返回类型
define('CLIENT_MULTI_RESULTS', 0x20000);
/**
 * mysql访问类
 *
 */
class Mysql {
	// public
    var $conn  = null;
    var $sql   = null;
    // private
    var $_host = null;
    var $_user = null;
    var $_pwd  = null;
    var $_name = null;
    var $_prefix = null;
    var $_scheme = 'mysql';
    var $_pconnect = false;
    var $_goneaway = 3; 
    /**
     * 初始化连接
     *
     * @param  $config          数据库设置
     * @param bool $pconnect    是否需要长连接
     * @return void
     */
    function __construct($config=null, $pconnect=false) {
        if (!extension_loaded('mysql')) {
            return throw_error(__('Your PHP installation appears to be missing the MySQL extension which is required by LazyCMS.'),E_LAZY_ERROR);
        }
        if (!empty($config)) {
            $this->config($config);
            $this->config('pconnect',$pconnect);
            if ($this->connect()) {
                $this->select_db();
            }
        }
    }

    function Mysql() {
        // 添加PHP4下的析构函数
        register_shutdown_function( array(&$this, '__destruct') );

        // 调用PHP的构造函数
        $args = func_get_args();
		call_user_func_array( array(&$this, '__construct'), $args );
    }

    function __destruct(){
    	$this->close();
    }
    /**
     * 连接Mysql
     *
     * @return bool|void
     */
    function connect(){
        // 检验数据库链接参数
        if (!$this->_host || !$this->_user)
            return throw_error(__('Database connect error, please check the database settings!'),E_LAZY_ERROR);
        // 连接数据库
        if (function_exists('mysql_pconnect') && $this->_pconnect) {
            $this->conn = mysql_pconnect($this->_host,$this->_user,$this->_pwd,CLIENT_MULTI_RESULTS);
        } elseif (function_exists('mysql_connect')) {
            $this->conn = mysql_connect($this->_host,$this->_user,$this->_pwd,false,CLIENT_MULTI_RESULTS);
        }
        
        // 验证连接是否正确
        if (!$this->conn) {
            return throw_error(__('Database connect error, please check the database settings!'),E_LAZY_ERROR);
        }
        return $this->conn;
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
            return throw_error(sprintf(__('%s database not found!'),$db),E_LAZY_ERROR);
        }
        // MYSQL数据库的设置
        if (version_compare($this->version(), '4.1', '>=')) {
        	mysql_query("SET NAMES utf8;",$this->conn);
        	if(version_compare($this->version(), '5.0.1', '>' )) {
	            mysql_query("SET sql_mode='';",$this->conn);
	        }
        } else {
            return throw_error(__('MySQL database version lower than 4.1, please upgrade MySQL!'),E_LAZY_ERROR);
        }
        
        return true;
    }
    /**
	 * Prepares a SQL query for safe execution. Uses sprintf()-like syntax.
	 *
	 * The following directives can be used in the query format string:
	 *   %d (decimal number)
	 *   %s (string)
	 *   %% (literal percentage sign - no argument needed)
	 *
	 * Both %d and %s are to be left unquoted in the query string and they need an argument passed for them.
	 * Literals (%) as parts of the query must be properly written as %%.
	 *
	 * This function only supports a small subset of the sprintf syntax; it only supports %d (decimal number), %s (string).
	 * Does not support sign, padding, alignment, width or precision specifiers.
	 * Does not support argument numbering/swapping.
	 *
	 * May be called like {@link http://php.net/sprintf sprintf()} or like {@link http://php.net/vsprintf vsprintf()}.
	 *
	 * Both %d and %s should be left unquoted in the query string.
	 *
	 *
	 * @param string $query Query statement with sprintf()-like placeholders
	 * @param array|mixed $args The array of variables to substitute into the query's placeholders if being called like
	 * 	{@link http://php.net/vsprintf vsprintf()}, or the first variable to substitute into the query's placeholders if
	 * 	being called like {@link http://php.net/sprintf sprintf()}.
	 * @param mixed $args,... further variables to substitute into the query's placeholders if being called like
	 * 	{@link http://php.net/sprintf sprintf()}.
	 * @return null|false|string Sanitized query string, null if there is no query, false if there is an error and string
	 * 	if there was something to prepare
	 */
    function prepare($query = null) { // ( $query, *$args )
        if ( is_null( $query ) ) return ;
        $args = func_get_args(); array_shift( $args );
        // If args were passed as an array (as in vsprintf), move them up
		if ( isset( $args[0] ) && is_array($args[0]) ) $args = $args[0];

        $query = str_replace( "'%s'", '%s', $query ); // in case someone mistakenly already singlequoted it
		$query = str_replace( '"%s"', '%s', $query ); // doublequote unquoting
        $query = preg_replace( '/(?<!%)%s/', "'%s'", $query ); // quote the strings, avoiding escaped strings like %%s
        // 处理表前缀
        if (preg_match_all("/'[^']+'/",$query,$r)) {
            foreach($r[0] as $i=>$v) {
                $query = preg_replace('/'.preg_quote($v,'/').'/',"'@{$i}@'",$query,1);
            }
        }
        $query = preg_replace('/(?<![^\s])(`*)#@_([\w`]+\s*)/iU','$1'.$this->_prefix.'$2',$query);
        if (isset($r[0]) && !empty($r[0])) {
            foreach($r[0] as $i=>$v) {
                $query = str_replace("'@{$i}@'", $v, $query);
            }
        }
        if ($args) {
            $query = vsprintf($query, $this->escape($args));
        }
        return $query;
    }
    /**
     * 指定函数执行SQL语句
     *
     * @param string $sql	sql语句
     * @param mixed  $bind	占位符 可以是字符串，也可以是数组
     * @param string $type	类型
     * @return resource
     */
    function query($sql){
        // 验证连接是否正确
        if (!$this->conn) {
            return throw_error(__('Supplied argument is not a valid MySQL-Link resource.'),E_LAZY_ERROR);
        }
        $args = func_get_args();
        
        $sql = call_user_func_array(array(&$this,'prepare'), $args);

        if ( preg_match("/^\\s*(insert|delete|update|replace|alter table|create) /i",$sql) ) {
        	$func = 'mysql_unbuffered_query';
        } else {
        	$func = 'mysql_query';
        }
        $this->sql = $sql;
        if (!($result = $func($sql,$this->conn))) {
            if (in_array($this->errno(),array(2006,2013)) && ($this->_goneaway-- > 0)) {
                $this->close(); $this->connect(); $this->select_db();
                $result = call_user_func_array(array(&$this,'query'), $args);
            } else {
                // 重置计数
                $this->_goneaway = 3;
                return throw_error(sprintf(__("MySQL Query Error:%s"),$sql."\r\n\t".$this->error()),E_LAZY_ERROR);
            }
        }
        // 查询正常
        if ($result) {
            // 重置计数
            $this->_goneaway = 3;
            // 返回结果
            if ($func == 'mysql_unbuffered_query') {
                if ( preg_match("/^\\s*(insert|replace) /i",$sql) ) {
                    $result = $this->insert_id();
                } else {
                    $result = $this->affected_rows();
                }
            }
        }
        return $result;
    }
    /**
     * 增量的变更表差异
     *
     * @param  $queries
     * @param bool $execute
     * @return array
     */
    function delta($queries, $execute = true) {
        // Separate individual queries into an array
        if ( !is_array($queries) ) {
            $queries = explode( ';', $queries );
            if ('' == $queries[count($queries) - 1]) array_pop($queries);
        }

        $cqueries = array(); // Creation Queries
        $iqueries = array(); // Insertion Queries
        $for_update = array();

        // Create a tablename index for an array ($cqueries) of queries
        foreach($queries as $qry) {
            $qry = trim($this->prepare($qry));
            if (preg_match("/CREATE TABLE ([^ ]*)/", $qry, $matches)) {
                $cqueries[trim( strtolower($matches[1]), '`' )] = $qry;
                $for_update[$matches[1]] = 'Created table ' . $matches[1];
            } else if (preg_match("/CREATE DATABASE ([^ ]*)/", $qry, $matches)) {
                array_unshift($cqueries, $qry);
            } else if (preg_match("/INSERT INTO ([^ ]*)/", $qry, $matches)) {
                $iqueries[] = $qry;
            } else if (preg_match("/UPDATE ([^ ]*)/", $qry, $matches)) {
                $iqueries[] = $qry;
            } else {
                // Unrecognized query type
            }
        }

        // Check to see which tables and fields exist
        $show_tables_res = $this->query("SHOW TABLES;");
	    while ($data = $this->fetch($show_tables_res,0)) {
            $table = $data[0];
            // If a table query exists for the database table...
            if (isset($cqueries[strtolower($table)])) {
                // Clear the field and index arrays
                $cfields = $indices = array();
                // Get all of the field names in the query from between the parens
                preg_match("/\((.*)\)/ms", $cqueries[strtolower($table)], $match2);
                $qryline = trim($match2[1]);

                // Separate field lines into an array
                $flds = explode("\n", $qryline);

                // For every field line specified in the query
				foreach ($flds as $fld) {
					// Extract the field name
					preg_match("/^([^ ]*)/", trim($fld), $fvals);
					$fieldname = isset($fvals[1]) ? $fvals[1]: '';//trim( $fvals[1], '`' );

					// Verify the found field name
					$validfield = true;
					switch (strtolower($fieldname)) {
					case '':
					case 'primary':
					case 'index':
					case 'fulltext':
					case 'unique':
					case 'key':
						$validfield = false;
						$indices[] = trim(trim($fld), ", \n");
						break;
					}
					$fld = trim($fld);

					// If it's a valid field, add it to the field array
					if ($validfield) {
                        $fieldname = strtolower(trim( $fieldname, '`' ));
						$cfields[$fieldname] = trim($fld, ", \n");
					}
				}

                // Fetch the table column structure from the database
                $describe_res = $this->query("DESCRIBE `{$table}`;");
                while ($tablefield = $this->fetch($describe_res)) {
                    // If the table field exists in the field array...
                    if (isset($cfields[strtolower($tablefield['Field'])])) {
						// Get the field type from the query
						preg_match("/`".$tablefield['Field']."` ([^ ]*( unsigned)?)/i", $cfields[strtolower($tablefield['Field'])], $matches);
						$fieldtype = isset($matches[1]) ? $matches[1] : '';

						// Is actual field type different from the field type in query?
						if ($tablefield['Type'] != $fieldtype) {
							// Add a query to change the column type
							$cqueries[] = "ALTER TABLE `{$table}` CHANGE COLUMN `".$tablefield['Field']."` " . $cfields[strtolower($tablefield['Field'])];
							$for_update[$table.'.'.$tablefield['Field']] = "Changed type of {$table}.".$tablefield['Field']." from ".$tablefield['Type']." to {$fieldtype}";
						}

						// Get the default value from the array
						if (preg_match("/ DEFAULT '(.*)'/i", $cfields[strtolower($tablefield['Field'])], $matches)) {
							$default_value = $matches[1];
							if ($tablefield['Default'] != $default_value) {
								// Add a query to change the column's default value
								$cqueries[] = "ALTER TABLE `{$table}` ALTER COLUMN `".$tablefield['Field']."` SET DEFAULT '{$default_value}'";
								$for_update[$table.'.'.$tablefield['Field']] = "Changed default value of {$table}.".$tablefield['Field']." from ".$tablefield['Default']." to {$default_value}";
							}
						}

						// Remove the field from the array (so it's not added)
						unset($cfields[strtolower($tablefield['Field'])]);
					} else {
						// This field exists in the table, but not in the creation queries?
					}
                }

                // For every remaining field specified for the table
				foreach ($cfields as $fieldname => $fielddef) {
					// Push a query line into $cqueries that adds the field to that table
					$cqueries[] = "ALTER TABLE `{$table}` ADD COLUMN {$fielddef}";
					$for_update[$table.'.'.$fieldname] = "Added column {$table}.{$fieldname}";
				}

                // Index stuff goes here
				// Fetch the table index structure from the database
                $tableindices   = array();
                $show_index_res = $this->query("SHOW INDEX FROM `{$table}`;");
                while ($index = $this->fetch($show_index_res)) {
                    $tableindices[] = $index;
                }

                if (!empty($tableindices)) {
					// Clear the index array
					unset($index_ary);

					// For every index in the table
					foreach ($tableindices as $tableindex) {
						// Add the index to the index data array
						$keyname = $tableindex['Key_name'];
						$index_ary[$keyname]['columns'][] = array('fieldname' => $tableindex['Column_name'], 'subpart' => $tableindex['Sub_part']);
						$index_ary[$keyname]['unique'] = ($tableindex['Non_unique'] == 0)?true:false;
					}

					// For each actual index in the index array
					foreach ($index_ary as $index_name => $index_data) {
						// Build a create string to compare to the query
						$index_string = '';
						if ($index_name == 'PRIMARY') {
							$index_string .= 'PRIMARY ';
						} else if($index_data['unique']) {
							$index_string .= 'UNIQUE ';
						}
						$index_string .= 'KEY ';
						if ($index_name != 'PRIMARY') {
							$index_string .= '`'.$index_name.'` ';
						}
						$index_columns = '';
						// For each column in the index
						foreach ($index_data['columns'] as $column_data) {
							if ($index_columns != '') $index_columns .= ',';
							// Add the field to the column list string
							$index_columns .= '`'.$column_data['fieldname'].'`';
							if ($column_data['subpart'] != '') {
								$index_columns .= '(`'.$column_data['subpart'].'`)';
							}
						}
						// Add the column list to the index create string
						$index_string .= '('.$index_columns.')';
						if (!(($aindex = array_search($index_string, $indices)) === false)) {
							unset($indices[$aindex]);
						}
					}
				}

                // For every remaining index specified for the table
				foreach ( (array) $indices as $index ) {
					// Push a query line into $cqueries that adds the index to that table
					$cqueries[] = "ALTER TABLE `{$table}` ADD {$index}";
					$for_update[$table.'.'.$fieldname] = "Added index {$table} {$index}";
				}

				// Remove the original table creation query from processing
				unset($cqueries[strtolower($table)]);
				unset($for_update[strtolower($table)]);
            } else {
				// This table exists in the database, but not in the creation queries?
			}
        }

        $allqueries = array_merge($cqueries, $iqueries);
        if ($execute) {
            foreach ($allqueries as $query) {
                $this->query($query);
            }
        }
        return $for_update;
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
             . "VALUES ('" . implode("', '", $vals) . "')";

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
            $set[] = $this->identifier($col)." = '".$this->escape($val)."'";
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
        $res = $this->query("SHOW COLUMNS FROM `{$table}`;");
        while ($rs = $this->fetch($res)) {
        	$result[] = $rs['Field'];
        }
        return $result;
    }
    /**
     * 检查是否存在数据库
     *
     * @param  $dbname
     * @return bool
     */
    function is_database($dbname){
        $res = $this->query("SHOW DATABASES;");
        while ($rs = $this->fetch($res,0)) {
        	if ($dbname == $rs[0]) return true;
        }
        return false;
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
        $res = $this->query("SHOW TABLES FROM `{$this->_name}`;");
        if (!strncasecmp($table,'#@_',3))
            $table = str_replace('#@_',$this->_prefix,$table);
        
        while ($rs = $this->fetch($res,0)) {
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
            return '';
        }
        if (is_string($data)) {
            return $data;
        }
        $cond = array();
        foreach ($data as $field => $value) {
            $cond[] = "(" . $this->identifier($field) ." = '". $this->escape($value) . "')";
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

        return $str;
	}
    /**
     * 设置数据库连接参数
     *
     * @param mixed $config
     * @return mixed
     */
    function config($config,$value=null) {
        // 批量赋值
    	if (is_array($config)) {
    		foreach ($config as $k=>$v) {
                $this->config($k,$v);
    		}
            return true;
        }
        // 取值
        if ($config && func_num_args()==1) {
            $property = "_{$config}";
            return isset($this->$property)?$this->$property:null;
        }
        // 单个赋值
        else {
            $property = "_{$config}";
            $this->$property = $value;
            return $value;
        }
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
