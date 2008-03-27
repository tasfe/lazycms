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
 * | Author: Lukin <mylukin@gmail.com>                                         |
 * +---------------------------------------------------------------------------+
 */
defined('CORE_PATH') or die('Restricted access!');
/**
 * 数据库简单封装类
 *
 * @copyright   Copyright (c) 2007-2008 lazycms.net All rights reserved.
 * @author      Lukin <mylukin@gmail.com>
 */
// DB *** *** www.LazyCMS.net *** ***
abstract class DB extends Lazy{
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

    // getConnect *** *** www.LazyCMS.net *** ***
    abstract public function getConnect();
    
    // getSQL *** *** www.LazyCMS.net *** ***
    abstract public function getSQL();

    // query *** *** www.LazyCMS.net *** ***
    abstract public function query($sql,$bind=null);

    // exec *** *** www.LazyCMS.net *** ***
    abstract public function exec($sql,$bind=null);

    // fetch *** *** www.LazyCMS.net *** ***
    abstract public function fetch($l1,$l2=1); // $l1:resource, $l2:type

    // count *** *** www.LazyCMS.net *** ***
    abstract public function count($l1); // $l1:sql or resource

    // factory *** *** www.LazyCMS.net *** ***
    public function factory($config){
        // mysql://root:123456@localhost:3306/lazycms
        if (is_array($config)) {
            $config   = array_change_key_case($config);
        } else {
            $config = self::parseDSN($config);
        }
        // 载入对应的数据库封装类
        $db = O($config['scheme'],'system.db');
        // 设置默认端口
        if (empty($config['port']) || !isset($config['port'])) { $config['port'] = $db->_port; }
        // 设置表前缀
        if (empty($config['prefix']) || !isset($config['prefix'])) { $config['prefix'] = C('DSN_PREFIX'); }
        // 数据库设置
        $db->config($config);
        // 初始化连接
        $db->connect();
        
        return $db;
    }
    // parseDSN *** *** www.LazyCMS.net *** ***
    public function parseDSN($config){
        $info = parse_url($config);
        if ($info['scheme']) {
            $config = array(
                'host'  => !empty($info['host']) ? $info['host'] : '',
                'port'  => !empty($info['port']) ? $info['port'] : '',
                'user'  => !empty($info['user']) ? $info['user'] : '',
                'pwd'   => !empty($info['pass']) ? $info['pass'] : '',
                'name'  => !empty($info['path']) ? substr($info['path'],1) : '',
                'scheme'=> !empty($info['scheme']) ? $info['scheme'] : '',
            );
        } else {
            if (preg_match('/^(.+):\/\/(.[^:]+)(:(.[^@]+)?)?@([a-z0-9\-\.]+)(:(\d+))?\/(\w+)/i',trim($config),$info)) {
                $config = array(
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
        return $config;
    }
    // config *** *** www.LazyCMS.net *** ***
    public function config($config){
        if (is_array($config)) {
            $this->_config = array_merge($this->_config,$config);
        } else {
            return $this->_config[$config];
        }
    }
    

    /* Insert */
    
    // insert *** *** www.LazyCMS.net *** ***
    public function insert($l1,array $l2){ // $l1:table, $l2:array
        $cols = array();
        $vals = array();
        foreach ($l2 as $col => $val) {
            $cols[] = $this->quoteIdentifier($col);
            $vals[] = $this->quote($val);
        }

        $sql = "INSERT INTO "
             . $this->quoteIdentifier($this->_config['prefix'].$l1)
             . ' (' . implode(', ', $cols) . ') '
             . 'VALUES (' . implode(', ', $vals) . ')';

        $this->exec($sql);
        return mysql_affected_rows($this->_conn);
    }

    /* Update */

    // update *** *** www.LazyCMS.net *** ***
    public function update($l1,$l2,$l3=null){ // $l1:table, $l2:set, $l3:where
        // extract and quote col names from the array keys
        $set = array();
        foreach ($l2 as $col => $val) {
            $set[] = $this->quoteIdentifier($col).' = '.$this->quote($val);
        }
        $l3 = $this->whereExpr($l3);
        // build the statement
        $sql = "UPDATE "
             . $this->quoteIdentifier($this->_config['prefix'].$l1)
             . ' SET ' . implode(', ', $set)
             . (($l3) ? " WHERE {$l3}" : '');
        $this->exec($sql);
        return mysql_affected_rows($this->_conn);
    }

    /* Delete */

    // delete *** *** www.LazyCMS.net *** ***
    public function delete($l1,$l2=null){ // $l1:table, $l2:where
        $l2 = $this->whereExpr($l2);
        // build the statement
        $sql = "DELETE FROM "
             . $this->quoteIdentifier($this->_config['prefix'].$l1)
             . (($l2) ? " WHERE {$l2}" : '');
        $this->exec($sql);
        return mysql_affected_rows($this->_conn);
    }

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
    public function quote($l1){
        if (is_array($l1)) {
            foreach ($l1 as &$val) {
                $val = self::quote($val);
            }
            return implode(', ', $l1);
        }
        if (is_int($l1) || is_float($l1)) {
            return $l1;
        }
        return "'".addcslashes($l1,"\000\n\r\\'\"\032")."'";
    }
    // quoteInto *** *** www.LazyCMS.net *** ***
    public function quoteInto($sql, $bind) {
        // 替换单一占位符
        if (!is_array($bind) && strpos($sql,'?')!==false) {
            return str_replace('?',self::quote($bind),$sql);
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
    public function quoteIdentifier($l1){
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
                    $l4 = "`{$l6}`{$l5}`{$l7}`";
                }
                return "`{$l3}`.{$l4}";
            } else {
                return "`{$l2}`";
            }
        }
    }
    
}

?>