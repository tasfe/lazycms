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
 * SQLite 操作类
 *
 * @copyright   Copyright (c) 2007-2008 lazycms.net All rights reserved.
 * @author      Lukin <mylukin@gmail.com>
 * @date        2008-6-24
 */
class lazy_sqlite extends DB{
    // connect *** *** www.LazyCMS.net *** ***
    public function connect(){
        if ($this->_conn) { return $this->_conn; }
        if (file_exists($this->config('db'))) {
            // 连接数据库
            if (function_exists('sqlite_open')) {
                $this->_conn = sqlite_open($this->config('db'),0666);
            } else {
                trigger_error(L('error/db/nodbext',array('name'=>$this->config('scheme'))));
            }
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
        $sql = preg_replace(array('/\#\~(.+)\~\#/e','/`(#@_)(\w+)`/i','/`([^`]+)`/'),array('$this->config(\'\\1\')','`$2`','[$1]'),$sql);
        $this->_sql = $sql;
        
        if(!($I1= $func($this->_conn,$sql))){
            trigger_error('SQLite Query Error:<br/>SQL:'.$sql."<br>".$this->error(),$this->errno());
        }
        return $I1;
    }
    // batQuery *** *** www.LazyCMS.net *** ***
    public function batQuery($l1){ // $l1:sql
        if (empty($l1)) { return ; }
        $l1 = preg_replace('/\#\~(.+)\~\#/e','$this->config(\'\\1\')',$l1);
        $l1 = str_replace(chr(10).chr(10),chr(10),str_replace(chr(13),chr(10),$l1));
        $I2 = explode(chr(10),$l1);
        $I3 = create_function('&$l1,$l2','$l1=trim($l1);');array_walk($I2,$I3);
        $I4 = "";
        foreach ($I2 as $v) {
            if (preg_match('/;$/',$v)) {
                $I4 .= $v;
                // 执行sql
                $this->exec($I4);
                // 置空
                $I4 = '';
            } elseif (!preg_match('/^\-\-/',$v) && !preg_match('/^\/\//',$v) && !preg_match('/^\/\*/',$v) && !preg_match('/^#/',$v)) {
                $l2 = strrpos($v,'# ');
                if ($l2!==false) {
                    $l3 = trim(substr($v,0,$l2));
                    if (substr($l3,-1)==',') {
                        $v = $l3;
                    }
                }
                $I4.= $v."\n";
            }
        }
    }
    // query *** *** www.LazyCMS.net *** ***
    public function query($sql,$bind=null){
        if ((string)$bind!='') { $sql = $this->quoteInto($sql,$bind); }
        return $this->execute($sql,'sqlite_query');
    }
    // exec *** *** www.LazyCMS.net *** ***
    public function exec($sql,$bind=null){
        if ((string)$bind!='') { $sql = $this->quoteInto($sql,$bind); }
        return $this->execute($sql,'sqlite_exec');
    }
    // fetch *** *** www.LazyCMS.net *** ***
    public function fetch($rs=null,$type=1){
        switch ((int)$type) {
            case 0: $I2 = SQLITE_NUM;break;
            case 1: $I2 = SQLITE_ASSOC;break;
            case 2: $I2 = SQLITE_BOTH;break;
        }
        $I1 = sqlite_fetch_array($rs,$I2);
        if ((int)$type!==0) {
            foreach ((array)$I1 as $k=>$v) {
                if (($n = strpos($k,'.'))!==false){
                    $ck = substr($k,$n+1);
                    $I1[$ck] = $v;
                    unset($I1[$k]);
                }
            }
        }
        return $I1;
    }
    
    // count *** *** www.LazyCMS.net *** ***
    public function count($rs=null){
        if (is_object($rs)) {
            $I1 = $rs;
        } else {
            $I1 = $this->query($rs);
        }
        return sqlite_num_rows($I1);
    }

    // result *** *** www.LazyCMS.net *** ***
    public function result($l1,$l2=0) {
        if (is_object($l1)) {
            $I2 = $l1;
        } else {
            $I2 = $this->query($l1);
        }
        if ($data = $this->fetch($I2,0)) {
            return $data[$l2];
        }
    }

    // error *** *** www.LazyCMS.net *** ***
    public function error() {
        return (($this->_conn) ? sqlite_last_error($this->_conn) : 'Unknown Error!');
    }

    // version *** *** www.LazyCMS.net *** ***
    public function version(){
        return sqlite_libversion();
    }

    // lastId *** *** www.LazyCMS.net *** ***
    public function lastId() {
        return sqlite_last_insert_rowid($this->_conn);
    }

    // affected_rows *** *** www.LazyCMS.net *** ***
    public function affected_rows(){
        return sqlite_changes($this->_conn);
    }

    // close *** *** www.LazyCMS.net *** ***
    public function close(){
        if (is_object($this->_conn)) {
            return sqlite_close($this->_conn);
        }
    }
}