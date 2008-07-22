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
/**
 * LazySQL 操作类
 *
 * @copyright   Copyright (c) 2007-2008 lazycms.net All rights reserved.
 * @author      Lukin <mylukin@gmail.com>
 * @date        2008-7-22
 */
class lazy_LazySQL extends DB{
    // connect *** *** www.LazyCMS.net *** ***
    public function connect(){
        if ($this->_conn) { return $this->_conn; }
        if (file_exists($this->config('db'))) {
            // 连接数据库
            $this->_conn = lazysql_open($this->config('db'),$error);
        }
        return $this->_conn;
    }
    // select_db *** *** www.LazyCMS.net *** ***
    public function select_db(){
        // 验证连接是否正确
        if (!file_exists($this->config('db'))) {
            trigger_error(L('error/db/nolink'));
        } else {
            return true;
        }
    }
    // execute *** *** www.LazyCMS.net *** ***
    private function execute($sql,$func,$type=''){
        $this->connect();
        $sql = preg_replace(array('/\#\~(.+)\~\#/e','/`(#@_)(\w+)`/i'),array('$this->config(\'\\1\')','`$2`'),$sql);
        $this->_sql = $sql;
        
        if(!($I1= $func($this->_conn,$sql))){
            trigger_error('LazySQL Query Error:<br/>SQL:'.$sql);
        }
        return $I1;
    }
    // query *** *** www.LazyCMS.net *** ***
    public function query($sql,$bind=null){
        if ((string)$bind!='') { $sql = $this->quoteInto($sql,$bind); }
        return $this->execute($sql,'lazysql_query');
    }
    // exec *** *** www.LazyCMS.net *** ***
    public function exec($sql,$bind=null){
        if ((string)$bind!='') { $sql = $this->quoteInto($sql,$bind); }
        return $this->execute($sql,'lazysql_exec');
    }
    // fetch *** *** www.LazyCMS.net *** ***
    public function fetch($rs=null,$type=1){}
    // count *** *** www.LazyCMS.net *** ***
    public function count($rs=null){}
    // affected_rows *** *** www.LazyCMS.net *** ***
    public function affected_rows(){}
    // close *** *** www.LazyCMS.net *** ***
    public function close(){}
}