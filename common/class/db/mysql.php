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
 * mysql操作类
 *
 * @copyright   Copyright (c) 2007-2008 lazycms.net All rights reserved.
 * @author      Lukin <mylukin@gmail.com>
 * @date        2008-6-24
 */
class lazy_mysql extends DB{
    
    // Defautl port
    protected $_port = 3306;
    
    // __construct *** *** www.LazyCMS.net *** ***
    public function __construct(){
        // 添加额外的参数，不能添加 host,port,user,pwd,name,prefix
        $config = array(
            'lang'  => 'utf8',
        );
        $this->config($config);
    }
    // connect *** *** www.LazyCMS.net *** ***
    public function connect(){
        if ($this->_conn) { return $this->_conn; }
        // 连接数据库
        if (function_exists('mysql_connect')) {
            $this->_conn = @mysql_connect($this->config('host').':'.$this->config('port'),$this->config('user'),$this->config('pwd'),true);
        } else {
            trigger_error(L('error/db/nodbext',array('name'=>$this->config('scheme'))));
        }
        // 验证连接是否正确
        if (!$this->_conn) {
            trigger_error(L('error/db/nolink'));
        }
        return $this->_conn;
    }
    
    // select *** *** www.LazyCMS.net *** ***
    public function select_db($db=null){
        // 验证连接是否正确
        if (!$this->_conn) {
            $this->connect();
        }
        if (empty($db)) {
            $select = mysql_select_db($this->config('name'),$this->_conn);
        } else {
            $select = mysql_select_db($db,$this->_conn);
        }
        // 选择数据库
        if (!$select) {
            trigger_error(L('error/db/noselect'));
        }
        mysql_query("SET character_set_connection=".$this->config('lang').", character_set_results=".$this->config('lang').", character_set_client=binary;");
        if (version_compare($this->version(), '4.1', '<' )) {
            trigger_error(L('error/db/nodbver'));
        }
        if(version_compare($this->version(), '5.0.1', '>' )) {
            mysql_query("SET sql_mode='';");
        }
        return $this->_conn;
    }
    // execute *** *** www.LazyCMS.net *** ***
    private function execute($sql,$func,$type=''){
        $this->connect();
        $sql = preg_replace('/\#\~(.+)\~\#/e','$this->config(\'\\1\')',$sql);
        $sql = preg_replace('/`(#@_)(\w+)`/i','`'.$this->config('prefix').'$2`',$sql);
        $this->_sql = $sql;
        
        if(!($I1= $func($sql,$this->_conn))){
            if(in_array($this->errno(),array(2006,2013)) && substr($type,0,5) != 'RETRY') {
                $this->close();$this->connect();
                $this->execute($sql,$func,'RETRY'.$type);
            } elseif($type != 'SILENT' && substr($type, 5) != 'SILENT') {
                trigger_error('MySQL Query Error:<br/>SQL:'.$sql."<br>".$this->error(),$this->errno());
            }
        }
        return $I1;
    }
    // isTable *** *** www.LazyCMS.net *** ***
    public function isTable($l1){
        $l1 = str_replace('#@_',$this->config('prefix'),$l1);
        $res = $this->query("SHOW TABLES");
        while ($data = $this->fetch($res,0)) {
            if (strtolower($l1)==strtolower($data[0])) {
                $this->free($res);
                return true;
            }
        }
        $this->free($res);
        return false;
    }
    // batQuery *** *** www.LazyCMS.net *** ***
    public function batQuery($l1){ // $l1:sql
        if (empty($l1)) { return ; }
        $l1 = preg_replace('/\#\~(.+)\~\#/e','$this->config(\'\\1\')',$l1);
        $l1 = preg_replace('/`(#@_)(\w+)`/i','`'.$this->config('prefix').'$2`',$l1);
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
    // affected_rows *** *** www.LazyCMS.net *** ***
    public function affected_rows(){
        return mysql_affected_rows($this->_conn);
    }
    // query *** *** www.LazyCMS.net *** ***
    public function query($sql,$bind=''){
        if ((string)$bind!='') { $sql = $this->quoteInto($sql,$bind); }
        return $this->execute($sql,'mysql_query');
    }
    // exec *** *** www.LazyCMS.net *** ***
    public function exec($sql,$bind=''){
        if ((string)$bind!='') { $sql = $this->quoteInto($sql,$bind); }
        return $this->execute($sql,'mysql_unbuffered_query');
    }
    // fetch *** *** www.LazyCMS.net *** ***
    public function fetch($rs=null,$type=1){
        switch ($type) {
            case '0': $I1 = MYSQL_NUM;break;
            case '1': $I1 = MYSQL_ASSOC;break;
            case '2': $I1 = MYSQL_BOTH;break;
        }
        return mysql_fetch_array($rs,$I1);
    }
    // count *** *** www.LazyCMS.net *** ***
    public function count($rs=null){
        if (is_resource($rs)) {
            $I1 = $rs;
        } else {
            $I1 = $this->query($rs);
        }
        return mysql_num_rows($I1);
    }
    // result *** *** www.LazyCMS.net *** ***
    public function result($l1,$l2=0) {
        if (is_resource($l1)) {
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
        return (($this->_conn) ? mysql_error($this->_conn) : mysql_error());
    }
    // errno *** *** www.LazyCMS.net *** ***
    public function errno() {
        return intval(($this->_conn) ? mysql_errno($this->_conn) : mysql_errno());
    }
    // version *** *** www.LazyCMS.net *** ***
    public function version(){
        return mysql_get_server_info($this->_conn);
    }
    // lastId *** *** www.LazyCMS.net *** ***
    public function lastId() {
        return ($I1 = mysql_insert_id($this->_conn)) >= 0 ? $I1 : $this->result("SELECT last_insert_id();");
    }
    // free *** *** www.LazyCMS.net *** ***
    public function free($l1){
        if (is_resource($l1)) {
            return mysql_free_result($l1);
        }
    }
    // close *** *** www.LazyCMS.net *** ***
    public function close(){
        if (is_resource($this->_conn)) {
            return mysql_close($this->_conn);
        }
    }
}