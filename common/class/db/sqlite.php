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
                trigger_error(L('error/db/nodbext',array('name'=>$this->config('scheme')),'system'));
            }
        }
        return $this->_conn;
    }
    // select_db *** *** www.LazyCMS.net *** ***
    public function select_db(){
        // 验证连接是否正确
        if (!file_exists($this->config('db'))) {
            trigger_error(L('error/db/nolink'),'system');
        } else {
            return true;
        }
    }

    // execute *** *** www.LazyCMS.net *** ***
    private function execute($sql,$func,$type=''){
        $this->connect();
        $sql = preg_replace(array('/\#\~(.+)\~\#/e','/`(#@_)(\w+)`/i','/`([^`]+)`/'),array('$this->config(\'\\1\')','`$2`','[$1]'),$sql);
        $this->_sql = $sql;
        
        if(!($R= $func($this->_conn,$sql))){
            trigger_error('SQLite Query Error:<br/>SQL:'.$sql."<br>".$this->error(),$this->errno());
        }
        return $R;
    }
    // batQuery *** *** www.LazyCMS.net *** ***
    public function batQuery($p1){ // $p1:sql
        if (empty($p1)) { return ; }
        $p1 = preg_replace('/\#\~(.+)\~\#/e','$this->config(\'\\1\')',$p1);
        $p1 = str_replace(chr(10).chr(10),chr(10),str_replace(chr(13),chr(10),$p1));
        $R1 = explode(chr(10),$p1);
        $R2 = create_function('&$p1,$p2','$p1=trim($p1);');array_walk($R1,$R2);
        $R3 = "";
        foreach ($R1 as $v) {
            if (preg_match('/;$/',$v)) {
                $R3 .= $v;
                // 执行sql
                $this->exec($R3);
                // 置空
                $R3 = '';
            } elseif (!preg_match('/^\-\-/',$v) && !preg_match('/^\/\//',$v) && !preg_match('/^\/\*/',$v) && !preg_match('/^#/',$v)) {
                $p2 = strrpos($v,'# ');
                if ($p2!==false) {
                    $p3 = trim(substr($v,0,$p2));
                    if (substr($p3,-1)==',') {
                        $v = $p3;
                    }
                }
                $R3.= $v."\n";
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
            case 0: $R1 = SQLITE_NUM;break;
            case 1: $R1 = SQLITE_ASSOC;break;
            case 2: $R1 = SQLITE_BOTH;break;
        }
        $R = sqlite_fetch_array($rs,$R1);
        if ((int)$type!==0) {
            foreach ((array)$R as $k=>$v) {
                if (($n = strpos($k,'.'))!==false){
                    $ck = substr($k,$n+1);
                    $R[$ck] = $v;
                    unset($R[$k]);
                }
            }
        }
        return $R;
    }
    
    // count *** *** www.LazyCMS.net *** ***
    public function count($rs=null){
        if (is_object($rs)) {
            $R = $rs;
        } else {
            $R = $this->query($rs);
        }
        return sqlite_num_rows($R);
    }

    // result *** *** www.LazyCMS.net *** ***
    public function result($p1,$p2=0) {
        if (is_object($p1)) {
            $R1 = $p1;
        } else {
            $R1 = $this->query($p1);
        }
        if ($data = $this->fetch($R1,0)) {
            return $data[$p2];
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