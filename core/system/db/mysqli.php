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
 * mysqli 操作类
 *
 * @copyright   Copyright (c) 2007-2008 lazycms.net All rights reserved.
 * @author      Lukin <mylukin@gmail.com>
 * @date        2008-6-24
 */
class lazy_mysqli extends DB{
    
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
        if (function_exists('mysqli_connect')) {
            $this->_conn = @mysqli_connect($this->config('host'),$this->config('user'),$this->config('pwd'),$this->config('name'),$this->config('port'));
        } else {
            throwError(L('error/nodbext',array('name'=>$this->config('scheme'))));
        }
        // 验证连接是否正确
        if (!$this->_conn) {
            throwError(mysqli_connect_error());
        }
        return $this->_conn;
    }
    // select *** *** www.LazyCMS.net *** ***
    public function select($db=null){
        // 验证连接是否正确
        if (!$this->_conn) {
            $this->connect();
        }
        if (empty($db)) {
            $select = mysqli_select_db($this->_conn,$this->config('name'));
        } else {
            $select = mysqli_select_db($this->_conn,$db);
        }
        // 选择数据库
        if (!$select) {
            throwError(L('error/selectdb'));
        }
        mysqli_query($this->_conn,"SET character_set_connection=".$this->config('lang').", character_set_results=".$this->config('lang').", character_set_client=binary;");
        if (version_compare($this->version(), '4.1', '<' )) {
            throwError(L('error/nodbver'));
        }
        if(version_compare($this->version(), '5.0.1', '>' )) {
            mysqli_query($this->_conn,"SET sql_mode='';");
        }
        return $this->_conn;
    }
    // execute *** *** www.LazyCMS.net *** ***
    private function execute($sql,$func,$type=''){
        $this->connect();
        $sql = preg_replace('/\#\~(.+)\~\#/e','$this->config(\'\\1\')',$sql);
        $sql = preg_replace('/`(#@_)(\w+)`/i','`'.$this->config('prefix').'$2`',$sql);
        $this->_sql = $sql;
        
        if(!($I1= $func($this->_conn,$sql))){
            if(in_array($this->errno(),array(2006,2013)) && substr($type,0,5) != 'RETRY') {
                $this->close();$this->connect();
                $this->execute($sql,$func,'RETRY'.$type);
            } elseif($type != 'SILENT' && substr($type, 5) != 'SILENT') {
                throwError('MySQL Query Error:<br/>SQL:'.$sql."<br>".$this->error(),$this->errno());
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
    // query *** *** www.LazyCMS.net *** ***
    public function query($sql,$bind=null){
        if ((string)$bind!='') { $sql = $this->quoteInto($sql,$bind); }
        return $this->execute($sql,'mysqli_query');
    }
    // exec *** *** www.LazyCMS.net *** ***
    public function exec($sql,$bind=null){
        if ((string)$bind!='') { $sql = $this->quoteInto($sql,$bind); }
        return $this->execute($sql,'mysqli_real_query');
    }
    // fetch *** *** www.LazyCMS.net *** ***
    public function fetch($rs=null,$type=1){
        switch ($type) {
            case '0': $I1 = MYSQLI_NUM;break;
            case '1': $I1 = MYSQLI_ASSOC;break;
            case '2': $I1 = MYSQLI_BOTH;break;
        }
        return mysqli_fetch_array($rs,$I1);
    }
    // count *** *** www.LazyCMS.net *** ***
    public function count($rs=null){
        if (is_object($rs)) {
            $I1 = $rs;
        } else {
            $I1 = $this->query($rs);
        }
        return mysqli_num_rows($I1);
    }
    // affected_rows *** *** www.LazyCMS.net *** ***
    public function affected_rows(){
        return mysqli_affected_rows($this->_conn);
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
        return (($this->_conn) ? mysqli_error($this->_conn) : 'Unknown Error!');
    }
    // errno *** *** www.LazyCMS.net *** ***
    public function errno() {
        return intval(mysqli_errno($this->_conn));
    }
    // version *** *** www.LazyCMS.net *** ***
    public function version(){
        return mysqli_get_server_info($this->_conn);
    }
    // lastId *** *** www.LazyCMS.net *** ***
    public function lastInsertId() {
        return ($I1 = mysqli_insert_id($this->_conn)) >= 0 ? $I1 : $this->result("SELECT last_insert_id();");
    }
    // free *** *** www.LazyCMS.net *** ***
    public function free($l1){
        if (is_object($l1)) {
            return mysqli_free_result($l1);
        }
    }
    // close *** *** www.LazyCMS.net *** ***
    public function close(){
        if (is_object($this->_conn)) {
            return mysqli_close($this->_conn);
        }
    }
}