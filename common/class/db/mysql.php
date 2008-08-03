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
            trigger_error(L('error/db/nodbext',array('name'=>$this->config('scheme')),'system'));
        }
        // 验证连接是否正确
        if (!$this->_conn) {
            trigger_error(L('error/db/nolink','system'));
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
            trigger_error(L('error/db/noselect','system'));
        }
        mysql_query("SET character_set_connection=".$this->config('lang').", character_set_results=".$this->config('lang').", character_set_client=binary;");
        if (version_compare($this->version(), '4.1', '<' )) {
            trigger_error(L('error/db/nodbver','system'));
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
        if(!($R= $func($sql,$this->_conn))){
            if(in_array($this->errno(),array(2006,2013)) && substr($type,0,5) != 'RETRY') {
                $this->close();$this->connect();
                $this->execute($sql,$func,'RETRY'.$type);
            } elseif($type != 'SILENT' && substr($type, 5) != 'SILENT') {
                trigger_error('MySQL Query Error:<br/>SQL:'.$sql."<br>".$this->error(),$this->errno());
            }
        }
        return $R;
    }
    // isTable *** *** www.LazyCMS.net *** ***
    public function isTable($p1){
        $p1 = str_replace('#@_',$this->config('prefix'),$p1);
        $res = $this->query("SHOW TABLES");
        while ($data = $this->fetch($res,0)) {
            if (strtolower($p1)==strtolower($data[0])) {
                $this->free($res);
                return true;
            }
        }
        $this->free($res);
        return false;
    }
    // batQuery *** *** www.LazyCMS.net *** ***
    public function batQuery($p1){ // $p1:sql
        if (empty($p1)) { return ; }
        $p1 = preg_replace('/\#\~(.+)\~\#/e','$this->config(\'\\1\')',$p1);
        $p1 = preg_replace('/`(#@_)(\w+)`/i','`'.$this->config('prefix').'$2`',$p1);
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
            case '0': $R = MYSQL_NUM;break;
            case '1': $R = MYSQL_ASSOC;break;
            case '2': $R = MYSQL_BOTH;break;
        }
        return mysql_fetch_array($rs,$R);
    }
    // count *** *** www.LazyCMS.net *** ***
    public function count($rs=null){
        if (is_resource($rs)) {
            $R = $rs;
        } else {
            $R = $this->query($rs);
        }
        return mysql_num_rows($R);
    }
    // result *** *** www.LazyCMS.net *** ***
    public function result($p1,$p2=0) {
        if (is_resource($p1)) {
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
        return ($R = mysql_insert_id($this->_conn)) >= 0 ? $R : $this->result("SELECT last_insert_id();");
    }
    // free *** *** www.LazyCMS.net *** ***
    public function free($p1){
        if (is_resource($p1)) {
            return mysql_free_result($p1);
        }
    }
    // close *** *** www.LazyCMS.net *** ***
    public function close(){
        if (is_resource($this->_conn)) {
            return mysql_close($this->_conn);
        }
    }
}