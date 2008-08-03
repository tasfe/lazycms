<?php
/**
 * +---------------------------------------------------------------------------+
 * | LL                             LLLL   LL     L   LLLL                 LL  |
 * | LL                            LL   L  LLL   LL  LL   L               LLL  |
 * | LL      LLLL   LLLLL  LL  LL  LL      LLLL LLL  LL          LL  LL    LL  |
 * | LL         LL     LL  LL  LL  LL      L LLL LL  LLLLL       LL  LL    LL  |
 * | LL      LLLLL    LL    LLLL   LL      L  L  LL   LLLLL      LL  LL    LL  |
 * | LL     LL  LL   LL     LLLL   LL      L     LL      LL       LLLL     LL  |
 * | LL     LL  LL  LL       LL    LL   L  L     LL  L   LL       LLLL     LL  |
 * | LLLLLL  LLLLL  LLLLL    LL     LLLL   L     LL   LLLL         LL     LLLL |
 * |                        LL                                                 |
 * |                        LL                                                 |
 * +---------------------------------------------------------------------------+
 * | Copyright (c) 2007-2008 LazyCMS.net All rights reserved.                  |
 * +---------------------------------------------------------------------------+
 * | 许可协议，请查看源代码中附带的 LICENSE.txt 文件，                         |
 * | 或者访问 http://www.lazycms.net/ 获得详细信息。                           |
 * +---------------------------------------------------------------------------+
 */
defined('COM_PATH') or die('Restricted access!');
// 加载 LazySQL 支持类
// import('class.lazysql');
/**
 * 数据操作抽象类
 *
 * @copyright   Copyright (c) 2007-2008 lazycms.net All rights reserved.
 * @author      Lukin <mylukin@gmail.com>
 * @date        2008-6-24
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
abstract class DB {
    
    // Default config
    protected $_config = array();

    // Query sql
    protected $_sql  = null;
    
    // Database connection
    protected $_conn = null;
    
    // Defautl port
    protected $_port = null;

    // connect *** *** www.LazyCMS.net *** ***
    abstract public function connect();

    // select_db *** *** www.LazyCMS.net *** ***
    abstract public function select_db();

    // query *** *** www.LazyCMS.net *** ***
    abstract public function query($sql);

    // exec *** *** www.LazyCMS.net *** ***
    abstract public function exec($sql);

    // fetch *** *** www.LazyCMS.net *** ***
    abstract public function fetch();

    // count *** *** www.LazyCMS.net *** ***
    abstract public function count();
    
    // affected_rows *** *** www.LazyCMS.net *** ***
    abstract public function affected_rows();

    // close *** *** www.LazyCMS.net *** ***
    abstract public function close();

    // config *** *** www.LazyCMS.net *** ***
    public function config($config){
        if (is_array($config)) {
            $this->_config = array_merge($this->_config,$config);
        } else {
            return isset($this->_config[$config]) ? $this->_config[$config] : null;
        }
    }

    // factory *** *** www.LazyCMS.net *** ***
    static function factory($DSN){
        if (is_array($DSN)) {
            $config = array_change_key_case($DSN);
        } else {
            $config = self::parseDSN($DSN);
        }
        // 载入对应的数据库封装类
        import('class.db.'.$config['scheme']);
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

    // parseDSN *** *** www.LazyCMS.net *** ***
    static function parseDSN($DSN){
        $scheme = strtolower(substr($DSN,0,strpos($DSN,':')));
        switch ($scheme) {
            // DSN format: lazysql://path=LazyCMS#DataBase
            case 'lazysql':
            // DSN format: sqlite://path=LazyCMS.db
            case 'sqlite': 
                if (preg_match('/^(\w+):\/\/path\=(.+)$/i',trim($DSN),$info)){
                    $info[2] = str_replace(array('\\','/'),SEPARATOR,trim($info[2]));
                    if (strncmp($info[2],SEPARATOR,1)!==0 && strpos($info[2],':/')===false && strpos($info[2],':\\')===false){
                        if ($pos = strrpos($info[2],SEPARATOR)) {
                            $dbpath = substr($info[2],0,$pos);
                            $dbfile = substr($info[2],$pos+1);
                            $folder = LAZY_PATH.SEPARATOR.$dbpath;
                            mkdirs($folder); save_file($folder.'/index.html',' ');
                        } else {
                            $dbfile = $info[2];
                            $folder = LAZY_PATH;
                        }
                        $info[2] = $folder.SEPARATOR.$dbfile;
                    }
                    return array(
                        'scheme'=> $info[1],
                        'db'    => $info[2],
                    );
                } else {
                    trigger_error(L('error/db/config'),'system');
                }
                break;
            // DSN format: mysql://root:123456@localhost:3306/lazy/lazycms
            default: 
                if (preg_match('/^(\w+):\/\/([^\/:]+)(:([^@]+)?)?@(\w+)(:(\d+))?(\/(\w+)\/(\w+)|\/(\w+))$/i',trim($DSN),$info)) {
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
                    trigger_error(L('error/db/config'),'system');
                }
                break;
        }
    }

    // getConn *** *** www.LazyCMS.net *** ***
    public function getConn(){
        return $this->_conn;
    }

    // getName *** *** www.LazyCMS.net *** ***
    public function getName(){
        return $this->config('db');
    }

    // max *** *** www.LazyCMS.net *** ***
    public function max($p1,$p2){
        // $p1:field, $p2:table
        return $this->result(sprintf("SELECT max( `%s` ) FROM `%s` WHERE 1=1;",$p1,$p2)) + 1;
    }
    // __destruct *** *** www.LazyCMS.net *** ***
    public function __destruct(){
        $this->close();
    }
    
    /* Copy */
    
    // copy *** *** www.LazyCMS.net *** ***
    public function copy($p1,$p2){
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

    /* Insert */
    
    // insert *** *** www.LazyCMS.net *** ***
    public function insert($p1,$p2){
        // $p1:table, $p2:array
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

    /* Update */

    // update *** *** www.LazyCMS.net *** ***
    public function update($p1,$p2,$p3=null){
        // $p1:table, $p2:set, $p3:where
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
    
    /* Delete */

    // delete *** *** www.LazyCMS.net *** ***
    public function delete($p1,$p2=null){
        // $p1:table, $p2:where
        $p2 = $this->whereExpr($p2);
        // build the statement
        $sql = "DELETE FROM "
             . $this->quoteIdentifier($p1)
             . (($p2) ? " WHERE {$p2}" : '');
        $this->exec($sql);
        return $this->affected_rows();
    }

    /* Format SQL */

    // whereExpr *** *** www.LazyCMS.net *** ***
    public function whereExpr($p1) {
        if (empty($p1)) {
            return $p1;
        }
        if (!is_array($p1)) {
            $p1 = array($p1);
        }
        foreach ($p1 as &$term) {
            $term = '(' . $term . ')';
        }
        $p1 = implode(' AND ', $p1);
        return $p1;
    }
    
    // quote *** *** www.LazyCMS.net *** ***
    static function quote($p1){
        if (is_array($p1)) {
            foreach ($p1 as &$val) {
                $val = self::quote($val);
            }
            return implode(', ', $p1);
        }
        if (is_int($p1) || is_float($p1)) {
            return $p1;
        }
        return "'".addcslashes($p1, "\000\n\r\\'\"\032")."'";
    }
    
    // quoteInto *** *** www.LazyCMS.net *** ***
    static function quoteInto($sql, $bind) {
        // 替换单一占位符
        if (!is_array($bind) && strpos($sql,'?')!==false) {
            return preg_replace('/\?/',self::quote($bind),$sql,1);
        }
        // 替换占位符
        if (is_array($bind)) {
            foreach ($bind as $k=>$v) {
                $sql = str_replace("[{$k}]",self::quote($v),$sql);
            }
        }
        return $sql;
    }
    
    // quoteIdentifier *** *** www.LazyCMS.net *** ***
    static function quoteIdentifier($p1){
        $R = null;
        $p2 = $p1;
        if (is_array($p2)) {
            $p2 = implode(',',$p2);
        }
        // 检测是否是多个字段
        if (strpos($p2,',') !== false) {
            // 多个字段，递归执行
            $R1 = explode(',',$p2);
            foreach ($R1 as $k=>$v) {
                if (empty($R)) {
                    $R = self::quoteIdentifier($v);
                } else {
                    $R .= ','.self::quoteIdentifier($v);
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