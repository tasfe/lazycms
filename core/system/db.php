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
defined('CORE_PATH') or die('Restricted access!');
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
 *   quoteInto('field = :field and field1 = :field1',array('field'=>'value','field1'=>'value1'));
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

    // select *** *** www.LazyCMS.net *** ***
    abstract public function select();

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
            return $this->_config[$config];
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
        import('system.db.'.$config['scheme']);
        $className = 'lazy_'.$config['scheme'];
        $db = new $className();
        // 设置默认端口
        if (array_key_exists('port',$config)) {
            if (empty($config['port']) || !isset($config['port'])) {
                $config['port'] = $db->_port;
            }
        }
        // 设置表前缀
        if (empty($config['prefix']) || !isset($config['prefix'])) { $config['prefix'] = C('DSN_PREFIX'); }
        // 数据库设置
        $db->config($config);
        // 初始化连接
        $db->connect(); return $db;
    }

    // parseDSN *** *** www.LazyCMS.net *** ***
    static function parseDSN($DSN){
        $scheme = strtolower(substr($DSN,0,strpos($DSN,':')));
        if ($scheme=='sqlite') {
            // DSN format: sqlite://db=LazyCMS.db
            if (preg_match('/^(.+):\/\/path\=(.+)/i',trim($DSN),$info)){
                $info[2] = trim($info[2]);
                if (strncmp($info[2],'/',1)!==0 && substr($info[2],1,2)!==':/'){
                    if ($pos = strrpos($info[2],'/')) {
                        $dbpath = substr($info[2],0,$pos);
                        $dbfile = substr($info[2],$pos+1);
                        $folder = LAZY_PATH.'/'.$dbpath;
                        mkdirs($folder); save_file($folder.'/index.html',' ');
                    } else {
                        $dbfile = $info[2];
                        $folder = LAZY_PATH;
                    }
                    $info[2] = $folder.'/'.$dbfile;
                }
                return array(
                    'scheme'=> $info[1],
                    'db'    => $info[2],
                );
            } else {
                throwError(L('error/dbconfig'));
            }
        } else {
            // DSN format: mysql://root:123456@localhost:3306/lazy/lazycms
            if (preg_match('/^(.+):\/\/(.[^:]+)(:(.[^@]+)?)?@([a-z0-9\-\.]+)(:(\d+))?\/(\w+)/i',trim($DSN),$info)) {
                return array(
                    'host'  => $info[5],
                    'port'  => $info[7],
                    'user'  => $info[2],
                    'pwd'   => $info[4],
                    'name'  => $info[8],
                    'scheme'=> $info[1],
                );
            } else {
                throwError(L('error/dbconfig'));
            }
        }
    }
    
    // getDataBase *** *** www.LazyCMS.net *** ***
    public function getDataBase(){
        return $this->config('name');
    }

    // getSQL *** *** www.LazyCMS.net *** ***
    public function getSQL(){
        return $this->_sql;
    }

    // getConnect *** *** www.LazyCMS.net *** ***
    public function getConnect(){
        return $this->_conn;
    }

    // max *** *** www.LazyCMS.net *** ***
    public function max($l1,$l2){
        // $l1:field, $l2:table
        return $this->result(sprintf("SELECT max( `%s` ) FROM `%s` WHERE 1=1;",$l1,$l2)) + 1;
    }
    // __destruct *** *** www.LazyCMS.net *** ***
    public function __destruct(){
        $this->close();
    }
    
    /* Copy */
    
    // copy *** *** www.LazyCMS.net *** ***
    public function copy($l1,$l2){
        $res = $this->query("SHOW CREATE TABLE `{$l1}`");
        if ($data = $this->fetch($res,0)) {
            $sql = $data[1];
            $sql = preg_replace('/^(CREATE TABLE) `'.preg_quote($data[0],'/').'` (\()/i', '$1 IF NOT EXISTS `'.$l2.'` $2', $sql);
            $this->exec($sql);
            return true;
        } else {
            return false;
        }
    }

    /* Insert */
    
    // insert *** *** www.LazyCMS.net *** ***
    public function insert($l1,$l2){
        // $l1:table, $l2:array
        $cols = array();
        $vals = array();
        foreach ($l2 as $col => $val) {
            $cols[] = $this->quoteIdentifier($col);
            $vals[] = $this->quote($val);
        }

        $sql = "INSERT INTO "
             . $this->quoteIdentifier($l1)
             . ' (' . implode(', ', $cols) . ') '
             . 'VALUES (' . implode(', ', $vals) . ')';

        $this->exec($sql);
        return $this->affected_rows();
    }

    /* Update */

    // update *** *** www.LazyCMS.net *** ***
    public function update($l1,$l2,$l3=null){
        // $l1:table, $l2:set, $l3:where
        // extract and quote col names from the array keys
        $set = array();
        foreach ($l2 as $col => $val) {
            $set[] = $this->quoteIdentifier($col).' = '.$this->quote($val);
        }
        $l3 = $this->whereExpr($l3);
        // build the statement
        $sql = "UPDATE "
             . $this->quoteIdentifier($l1)
             . ' SET ' . implode(', ', $set)
             . (($l3) ? " WHERE {$l3}" : '');
        $this->exec($sql);
        return $this->affected_rows();
    }
    
    /* Delete */

    // delete *** *** www.LazyCMS.net *** ***
    public function delete($l1,$l2=null){
        // $l1:table, $l2:where
        $l2 = $this->whereExpr($l2);
        // build the statement
        $sql = "DELETE FROM "
             . $this->quoteIdentifier($l1)
             . (($l2) ? " WHERE {$l2}" : '');
        $this->exec($sql);
        return $this->affected_rows();
    }

    /* Format SQL */

    // whereExpr *** *** www.LazyCMS.net *** ***
    public function whereExpr($l1) {
        if (empty($l1)) {
            return $l1;
        }
        if (!is_array($l1)) {
            $l1 = array($l1);
        }
        foreach ($l1 as &$term) {
            $term = '(' . $term . ')';
        }
        $l1 = implode(' AND ', $l1);
        return $l1;
    }
    
    // quote *** *** www.LazyCMS.net *** ***
    static function quote($l1){
        if (is_array($l1)) {
            foreach ($l1 as &$val) {
                $val = self::quote($val);
            }
            return implode(', ', $l1);
        }
        if (is_int($l1) || is_float($l1)) {
            return $l1;
        }
        return "'".addcslashes($l1, "\000\n\r\\'\"\032")."'";
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
                $sql = str_replace(":{$k}",self::quote($v),$sql);
            }
        }
        return $sql;
    }
    
    // quoteIdentifier *** *** www.LazyCMS.net *** ***
    static function quoteIdentifier($l1){
        $I1 = null;
        $l2 = $l1;
        if (is_array($l2)) {
            $l2 = implode(',',$l2);
        }
        // 检测是否是多个字段
        if (strpos($l2,',') !== false) {
            // 多个字段，递归执行
            $I2 = explode(',',$l2);
            foreach ($I2 as $k=>$v) {
                if (empty($I1)) {
                    $I1 = self::quoteIdentifier($v);
                } else {
                    $I1 .= ','.self::quoteIdentifier($v);
                }
            }
            return $I1;
        } else {
            // 解析各个字段
            if (strpos($l2,'.') !== false) {
                $I2 = explode('.',$l2);
                $l3 = trim($I2[0]);
                $l4 = trim($I2[1]);
                $l5 = chr(32).'AS'.chr(32);
                if (stripos($l4,$l5) !== false) {
                    $l6 = trim(substr($l4,0,stripos($l4,$l5)));
                    $l7 = trim(substr($l4,stripos($l4,$l5)+4));
                    $l4 = sprintf("`%s`%s`%s`",$l6,$l5,$l7);
                }
                return sprintf("`%s`.%s",$l3,$l4);
            } else {
                return sprintf("`%s`",$l2);
            }
        }
    }
}?>