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
 * 数据操作基础封装类
 *
 * 统一处理引号
 * DB::quote(mixed value)
 *
 * 统一处理数据库字段
 * DB::quoteInto(mixed value)
 * @example:
 *   // 单一占位符
 *   quoteInto('field = ?','value');
 *   // 多个占位符
 *   quoteInto('field = [field] and field1 = [field1]',array('field'=>'value','field1'=>'value1'));
 *
 * 统一处理字段冲突
 * DB::quoteIdentifier(mixed value)
 * @example:
 *   // 单一字段
 *   quoteIdentifier('field');
 *   // 逗号分割字段
 *   quoteIdentifier('field,field1,field2');
 *   // 数组
 *   quoteIdentifier(array('field','field1','field2'));
 *   // 前缀表 和 AS 字段
 *   quoteIdentifier('table.field AS field1');
 */
class DB {
    // Default config
    var $_config = array();
    // Query sql
    var $_sql  = null;
    // Database connection
    var $_conn = null;
    // Defautl port
    var $_port = null;
    /**
     * 更改参数配置
     *
     * @param string $config
     * @return string
     */
    function config($config){
        if (is_array($config)) {
            $this->_config = array_merge($this->_config,$config);
        } else {
            return isset($this->_config[$config]) ? $this->_config[$config] : null;
        }
    }
    /**
     * 取得数据库连接对象
     *
     * @param string $DSN
     * @return object
     */
    function factory($DSN){
        if (is_array($DSN)) {
            $config = array_change_key_case($DSN);
        } else {
            $config = DB::parseDSN($DSN);
        }
        // 载入对应的数据库封装类
        import('system.db.'.$config['scheme']);
        $className = 'lazy_'.$config['scheme'];
        $db = new $className();
        // 设置默认端口
        if (array_key_exists('port',$config)) {
            if (empty($config['port']) || !isset($config['port'])) {
                $config['port'] = $db->_port;
            }
        }
        // 数据库设置
        $db->config($config);
        // 初始化连接
        $db->connect(); return $db;
    }
    /**
     * Enter description here...
     *
     * @param string $DSN
     * @return unknown
     */
    function parseDSN($DSN){
        $scheme = strtolower(substr($DSN,0,strpos($DSN,':')));
        switch ($scheme) {
            // DSN format: lazysql://path=LazyCMS#DataBase
            case 'lazysql':
                if (preg_match('/^(\w+):\/\/path\=(.+)$/i',trim($DSN),$info)){
                    $info[2] = str_replace(array('\\','/'),SEPARATOR,trim($info[2]));
                    if (strncmp($info[2],SEPARATOR,1)!==0 && strpos($info[2],':/')===false && strpos($info[2],':\\')===false){
                        if ($pos = strrpos($info[2],SEPARATOR)) {
                            $dbpath = substr($info[2],0,$pos);
                            $dbfile = substr($info[2],$pos+1);
                            $folder = ABS_PATH.SEPARATOR.$dbpath;
                            mkdirs($folder); save_file($folder.'/index.html',' ');
                        } else {
                            $dbfile = $info[2];
                            $folder = ABS_PATH;
                        }
                        $info[2] = $folder.SEPARATOR.$dbfile;
                    }
                    return array(
                        'scheme'=> $info[1],
                        'db'    => $info[2],
                    );
                } else {
                    trigger_error(t('system::database/parseDSN'));
                }
                break;
            // DSN format: mysql://root:123456@localhost:3306/lazy/lazycms
            default:
                if (preg_match('/^(\w+):\/\/([^\/:]+)(:([^@]+)?)?@([\w\-\.]+)(:(\d+))?(\/([\w\-]+)\/([\w\-]+)|\/([\w\-]+))$/i',trim($DSN),$info)) {
                    return array(
                        'host'  => $info[5],
                        'port'  => $info[7],
                        'user'  => $info[2],
                        'pwd'   => $info[4],
                        'name'  => isset($info[11]) ? $info[11] : $info[10],
                        'scheme'=> $info[1],
                        'prefix'=> (!empty($info[9]) ? $info[9].'_' : null),
                    );
                } else {
                    trigger_error(t('system::database/parseDSN'));
                }
                break;
        }
    }
    /**
     * 取得数据库连接资源句柄
     *
     * @return resource
     */
    function getConn(){
        return $this->_conn;
    }
    /**
     * 取得数据库名称
     *
     * @return string
     */
    function getName(){
        return $this->config('db');
    }
    /**
     * 取得最后查询SQL字符串
     *
     * @return string
     */
    function getSQL(){
        return $this->_sql;
    }
    /**
     * 取得指定表、指定字段的最大值
     *
     * @param string $p1    filed
     * @param string $p2    table
     * @return int
     */
    function max($p1,$p2){
        return $this->result(sprintf("SELECT max( `%s` ) FROM `%s` WHERE 1=1;",$p1,$p2)) + 1;
    }
    /**
     * 类析构函数
     */
    function __destruct(){
        $this->close();
    }
    /**
     * 复制表结构
     *
     * @param string $p1    旧表名
     * @param string $p2    新表名
     * @return bool
     */
    function copy($p1,$p2){
        $res = $this->query("SHOW CREATE TABLE `{$p1}`");
        if ($data = $this->fetch($res,0)) {
            $sql = $data[1];
            $sql = preg_replace('/^(CREATE TABLE) `'.preg_quote($data[0],'/').'` (\()/i', '$1 IF NOT EXISTS `'.$p2.'` $2', $sql);
            $this->exec($sql);
            return true;
        } else {
            return false;
        }
    }
    /**
     * 插入数据
     *
     * @param string $p1    table
     * @param array  $p2    插入数据的数组，key对应列名，value对应值
     * @return int
     */
    function insert($p1,$p2){
        $cols = array();
        $vals = array();
        foreach ($p2 as $col => $val) {
            $cols[] = $this->quoteIdentifier($col);
            $vals[] = $this->quote($val);
        }

        $sql = "INSERT INTO "
             . $this->quoteIdentifier($p1)
             . ' (' . implode(', ', $cols) . ') '
             . 'VALUES (' . implode(', ', $vals) . ')';
        $this->exec($sql);
        return $this->affected_rows();
    }
    /**
     * 更新数据
     *
     * @param string $p1    table
     * @param array  $p2    set 数组
     * @param mixed  $p3    where语句，支持数组，数组默认使用 AND 连接
     * @return int
     */
    function update($p1,$p2,$p3=null){
        // extract and quote col names from the array keys
        $set = array();
        foreach ($p2 as $col => $val) {
            $set[] = $this->quoteIdentifier($col).' = '.$this->quote($val);
        }
        $p3 = $this->whereExpr($p3);
        // build the statement
        $sql = "UPDATE "
             . $this->quoteIdentifier($p1)
             . ' SET ' . implode(', ', $set)
             . (($p3) ? " WHERE {$p3}" : '');
        $this->exec($sql);
        return $this->affected_rows();
    }
    /**
     * 删除数据
     *
     * @param string $p1    table
     * @param mixed  $p2    where 同上
     * @return int
     */
    function delete($p1,$p2=null){
        // $p1:table, $p2:where
        $p2 = $this->whereExpr($p2);
        // build the statement
        $sql = "DELETE FROM "
             . $this->quoteIdentifier($p1)
             . (($p2) ? " WHERE {$p2}" : '');
        $this->exec($sql);
        return $this->affected_rows();
    }
    /**
     * where语句组合
     *
     * @param mixed $p1 where语句，支持数组，数组默认使用 AND 连接
     * @return string
     */
    function whereExpr($p1) {
        if (empty($p1)) {
            return $p1;
        }
        if (!is_array($p1)) {
            $p1 = array($p1);
        }
        $p2 = array();
        foreach ($p1 as $term) {
            $p2[] = '(' . $term . ')';
        }
        $p1 = implode(' AND ', $p2);
        return $p1;
    }
    /**
     * 转义SQL语句
     *
     * @param mixed $p1
     * @return mixed
     */
    function quote($p1){
        if (is_array($p1)) {
            $p2 = array();
            foreach ($p1 as $val) {
                $p2[] = DB::quote($val);
            }
            return implode(', ', $p2);
        }
        if (is_int($p1) || is_float($p1)) {
            return $p1;
        }
        return "'".addcslashes($p1, "\000\n\r\\'\"\032")."'";
    }
    /**
     * 替换占位符
     *
     * @param string $sql
     * @param array  $bind
     * @return string
     */
    function quoteInto($sql, $bind) {
        // 替换单一占位符
        if (!is_array($bind) && strpos($sql,'?')!==false) {
            return preg_replace('/\?/',DB::quote($bind),$sql,1);
        }
        // 替换占位符
        if (is_array($bind)) {
            foreach ($bind as $k=>$v) {
                $sql = str_replace("[{$k}]",DB::quote($v),$sql);
            }
        }
        return $sql;
    }
    /**
     * 转义SQL关键字
     *
     * @param string $p1
     * @return string
     */
    function quoteIdentifier($p1){
        $R = null;
        $p2 = $p1;
        // 检测是否是多个字段
        if (strpos($p2,',') !== false) {
            // 多个字段，递归执行
            $R1 = explode(',',$p2);
            foreach ($R1 as $k=>$v) {
                if (empty($R)) {
                    $R = DB::quoteIdentifier($v);
                } else {
                    $R .= ','.DB::quoteIdentifier($v);
                }
            }
            return $R;
        } else {
            // 解析各个字段
            if (strpos($p2,'.') !== false) {
                $R1 = explode('.',$p2);
                $p3 = trim($R1[0]);
                $p4 = trim($R1[1]);
                $p5 = chr(32).'AS'.chr(32);
                if (stripos($p4,$p5) !== false) {
                    $p6 = trim(substr($p4,0,stripos($p4,$p5)));
                    $p7 = trim(substr($p4,stripos($p4,$p5)+4));
                    $p4 = sprintf("`%s`%s`%s`",$p6,$p5,$p7);
                }
                return sprintf("`%s`.%s",$p3,$p4);
            } else {
                return sprintf("`%s`",$p2);
            }
        }
    }
}
