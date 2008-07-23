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
 * LazySQL
 *
 * 部分代码和思想来源于 PHPSQL、SQLite、CTB文本论坛
 *
 * @copyright   Copyright (c) 2007-2008 lazycms.net All rights reserved.
 * @author      Lukin <mylukin@gmail.com>
 * @date        2008-7-22
 * TODO:
 *      SELECT,DELETE,ADD INDEX
 */
class LazySQL{
    private $null = "<?php die('LazySQL');?>";
    private $path;
    // __construct *** *** www.LazyCMS.net *** ***
    function __construct($path=null){
        $this->path  = (substr($path,-1)!==SEPARATOR) ? $path.SEPARATOR : $path;
    }
    // unescape *** *** www.LazyCMS.net *** ***
    static function unescape($l1){
        return str_replace(array("&#92;","&#47;","&#32;","&#44;"),array("\\","/"," ",","),stripslashes($l1));
    }
    // escape *** *** www.LazyCMS.net *** ***
    static function escape($l1){
        return str_replace(array(
            "|","<",">","&nbsp;"," ","(",")","`",'"',",","$","\\","/"
        ), array(
            "&#124;","&#60;","&#62;","&#32;","&#32;","&#40;","&#41;","&#96;","&#34;","&#44;","&#36;","&#92;","&#47;",
        ), stripslashes($l1));
    }
    // replace *** *** www.LazyCMS.net *** ***
    static function replace($l1){
        return str_replace("'", "&#39;", stripslashes($l1));
    }
    // open *** *** www.LazyCMS.net *** ***
    static function open($path,&$error=null){
        $__CLASS__ = __CLASS__;
        if (!file_exists($path)) {
            if (mkdirs($path)) {
                save_file($path.'/index.html',' ');
            } else {
                $error = "Create a database failure.";
                return false;
            }
        }
        return new $__CLASS__($path);
    }
    // query *** *** www.LazyCMS.net *** ***
    public function query($query){
        $query  = strtolower(trim($query));
        $query  = str_replace(chr(10).chr(10),chr(10),str_replace(chr(13),chr(10),$query));
        $eQuery = explode(chr(10),$query); array_walk($eQuery,create_function('&$l1,$l2','$l1=trim($l1);'));
        $query  = join('',$eQuery);
        $eQuery = explode("'", $query);
        $length = count($eQuery);
        for ($i=1;$i<$length;$i+=2) {
            $query = str_replace("'".$eQuery[$i]."'",$this->escape("'".$eQuery[$i]."'"),$query);
        }
        $sQuery = explode(' ',$query);
        $eLimit = explode('limit ',$query);
        $limit  = isset($eLimit[1]) ? $eLimit[1] : null;
        $eOrder = explode('order by ',$eLimit[0]);
        $order  = isset($eOrder[1]) ? $eOrder[1] : null;
        $eWhere = explode('where ',$eOrder[0]);
        $where  = isset($eWhere[1]) ? $eWhere[1] : null;
        $eSet   = explode('set ',$eWhere[0]);
        $will   = isset($eSet[1]) ? $eSet[1] : null;
        switch($first = $sQuery[0]) {
            case 'alter':
                if (strpos($query,'change')!==false) {
                    // 不支持多个 change 需要改进
                    $eChange = explode(' change ',$query);
                    $table   = preg_replace('/alter table `(.+?)`/is','$1',$eChange[0]);
                    $default = $field = $nField = null;
                    if (isset($eChange[1])) {
                        $eDef  = explode(' default ',$eChange[1]);
                        $e9696 = explode(' ', $eDef[0]);
                        $field = trim($e9696[0],'`');
                        if (isset($e9696[1])) {
                            $nField = trim($e9696[1],'`');
                            if (isset($eDef[1])) {
                                $default = trim(rtrim($eDef[1],";"),"'");
                            }
                        }
                    }
                    // 无法修改数据前的字段名，需要修改
                    return $this->alter_table($table,$field,$nField,$default);
                } elseif (strpos($query,'add index')!==false) {
                    $table = preg_replace('/alter table `(.+?)` add index `(.+?)`\;*/is','$1',$query);
                    $index = preg_replace('/alter table `(.+?)` add index `(.+?)`\;*/is','$2',$query);
                    return $this->add_index($table,$index);
                } elseif (strpos($query,'add')!==false) {
                    $eAdd  = explode(' add ',$query);
                    $table = preg_replace('/alter table `(.+?)`/is','$1',$eAdd[0]);
                    $field = $default = $add = null;
                    if (isset($eAdd[1])) {
                        $eDef  = explode(' default ',$eAdd[1]);
                        $field = trim($eDef[0],'`');
                        if (isset($eDef[1])) {
                            $eSub    = explode(' ', $eDef[1]);
                            $default = trim(rtrim($eSub[0],";"),"'");
                            if (isset($eSub[1])) {
                                $add = rtrim($eSub[1],";");
                            }
                        }
                    }
                    return $this->add_field($table,$field,$default,$add);
                } elseif (strpos($query,'drop index')!==false) {
                    $table = preg_replace('/alter table `(.+?)` drop index `(.+?)`\;*/is','$1',$query);
                    $index = preg_replace('/alter table `(.+?)` drop index `(.+?)`\;*/is','$2',$query);
                    return $this->drop_index($table,$index);
                } elseif (strpos($query,'drop')!==false) {
                    $table = preg_replace('/alter table `(.+?)` drop `(.+?)`\;*/is','$1',$query);
                    $field = preg_replace('/alter table `(.+?)` drop `(.+?)`\;*/is','$2',$query);
                    return $this->drop_field($table,$field);
                }
                break;
            case 'drop':
                $table = preg_replace('/drop table `(.+?)`\;*/is',"\\1",$query);
                return $this->drop_table($table);
                break;
            case 'create':
                $exists = preg_replace('/create table (if not exists )*`(.+?)` *\((.+?)\)\;*/is','$1',$query);
                $table  = preg_replace('/create table (if not exists )*`(.+?)` *\((.+?)\)\;*/is','$2',$query);
                $fields = preg_replace('/create table (if not exists )*`(.+?)` *\((.+?)\)\;*/is','$3',$query);
                $exists = $exists ? true : false;
                return $this->create_table($table,$fields,$exists);
                break;
            case 'truncate':
                $table = preg_replace('/truncate table `(.+?)`\;*/is',"\\1",$query);
                return $this->truncate($table);
                break;
            case 'insert':
                $table  = preg_replace('/insert into (.+?) *\((.+?)\) *values *\((.+?)\);*/ie',"trim('\$1','`')",$query);
                $fields = preg_replace('/insert into (.+?) *\((.+?)\) *values *\((.+?)\);*/is','$2',$query);
                $values = preg_replace('/insert into (.+?) *\((.+?)\) *values *\((.+?)\);*/is','$3',$query);
                return $this->insert($table,$fields,$values);
                break;
            case 'select': case 'delete':
                $eFrom = explode('from ',$query);
                $star  = preg_replace('/'.$first.'(.+?)/is',"\\1",$eFrom[0]);
                $eSub  = explode(' ',$eFrom[1]);
                $table = $eSub[0];
                if ($first == 'select') {
                    return $this->select($star,$table,$where,$order,$limit);
                } elseif ($first == 'delete') {
                    return $this->delete($table,$this->select($star,$table,$where,$order,$limit));
                }
                break;
            case 'update':
                $eSet  = explode(' set ',$query);
                $eSub  = explode(' ', $eSet[0]);
                $table = $eSub[1];
                return $this->update($table,$will,$this->select($star,$table,$where,$order,$limit));
                break;
            default:
                die($query);
                break;
        }
    }
    // exec *** *** www.LazyCMS.net *** ***
    public function exec($query){
        $this->query($query); return true;
    }
    // create_table *** *** www.LazyCMS.net *** ***
    private function create_table($table,$fields,$exists=false){
        $tFloder = $this->path.$table.SEPARATOR;
        $eFields = explode(',', $fields);
        $length  = count($eFields);
        for($i=0,$j=0; $i<$length; $i++) {
            $eSub = explode(' ', $eFields[$i]);
            $eSub[2] = isset($eSub[2]) ? $eSub[2] : null;
            if($eSub[2] == 'primary') {
                $j++;
                if ($eSub[1] && !is_numeric(trim($eSub[1],"'"))) {
                    $j ++;
                }
            }
        }
        
        if ($j != 1) { return false; }
        // 检查数据表是否存在
        if ($exists){
            // 数据表存在就不创建
            if (file_exists($tFloder)) {
                return false;
            }
        } else {
            // 不检查，直接删除已存在的表
            file_exists($tFloder) ? rmdirs($tFloder) : null;
        }
        // 创建数据表文件夹
        if (mkdirs($tFloder)) {
            // 创建数据文件夹
            mkdirs($tFloder.'data');
            // 创建索引文件夹
            mkdirs($tFloder.'index');
            $sI1 = null;
            for($i=0;$i<$length;$i++) {
                $eSub = explode(' ',$eFields[$i]);
                $eSub[2] = isset($eSub[2]) ? $eSub[2] : null;
                $sI1 .= $eSub[0].",".$eSub[1].",(".$eSub[2].")\n";
                if ($eSub[2] == 'index') {
                    $this->write($tFloder.'index'.SEPARATOR.trim($eSub[0],'`').'.php');
                }
            }
            $this->write($tFloder.'structure.php',$sI1);
            $this->write($tFloder.'primary.php');
            $this->write($tFloder.'rows_count.php','0');
            return true;
        }
    }
    // drop_table *** *** www.LazyCMS.net *** ***
    private function drop_table($table){
        return rmdirs($this->path.$table);
    }
    // truncate *** *** www.LazyCMS.net *** ***
    private function truncate($table){
        $tFloder = $this->path.$table.SEPARATOR;
        if (!file_exists($tFloder)) { return false; }
        $index   = $this->get_index($table);
        foreach ($index as $v) {
            $this->write($tFloder.'index'.SEPARATOR.$v['field'].'.php','',true);
        }
        // 先删除，再创建，达到清空数据表的目的
        $dFloder = $tFloder.'data'; rmdirs($dFloder); mkdirs($dFloder);
        $this->write($tFloder.'primary.php','',true);
        $this->write($tFloder.'rows_count.php','0',true);
        return true;
    }
    // get_index *** *** www.LazyCMS.net *** ***
    private function get_index($table,$field=null,$value=null) {
        $cont  = $this->read($this->path.$table.SEPARATOR.'structure.php');
        $eCont = explode("\n",rtrim($cont));
        $count = count($eCont); $I1 = array();
        for ($i=0,$j=0;$i<$count;$i++) {
            $eSub = explode(',', $eCont[$i]);
            $eSub[2] = isset($eSub[2]) ? $eSub[2] : null;
            if($eSub[2] == '(index)') {
                $I1[$j]['field'] = trim($eSub[0],'`');
                if (!empty($field) && !empty($value)) {
                    foreach ($field as $k=>$v) {
                        if ($v == $I1[$j]['field']) {
                            $I1[$j]['value'] = $value[$k]; break;
                        }
                    }
                }
                if (!isset($I1[$j]['value'])) {
                    $s = $this->get_structure($table,$I1[$j]['field']);
                    $I1[$j]['value'] = $s['default'];
                }
                $j++;
            }
        }
        return $I1;
    }
    // get_structure *** *** www.LazyCMS.net *** ***
    private function get_structure($table,$field){
        $cont = $this->read($this->path.$table.SEPARATOR.'structure.php');
        if (preg_match("/`(".preg_quote($field,"/").")`,'(.*)',\((.*)\)/i",$cont,$v)) {
            return array(
                'field'  => $v[1],
                'default'=> $v[2],
                'attr'   => $v[3],
            );
        }
    }
    // insert *** *** www.LazyCMS.net *** ***
    private function insert($table,$fields,$values){
        $tFloder = $this->path.$table.SEPARATOR;
        $table   = trim($table,'`');
        $eFields = explode(',',$fields);
        $eValues = explode(',',$values);
        $fcount  = count($eFields); 
        $vcount  = count($eValues); 
        if ($vcount != $fcount) {
            trigger_error('Fields and Value can\'t match.');
        }
        // 更新maxID
        $ID = (int)$this->read($tFloder.'rows_count.php'); $ID++;
        $this->write($tFloder.'rows_count.php',$ID,true);
        // 插入主键值
        $this->write($tFloder.'primary.php',$ID."\n");

        $aFields = $this->get_fields($fields);
        $aValues = $this->get_values($values);
        $index   = $this->get_index($table,$aFields,$aValues);
        $content = $this->get_content($table,$aFields,$aValues,$ID);
        $this->write_index($table,$index,$ID);
        $dFloder = $tFloder.'data'.SEPARATOR.$this->get_serial($ID);
        mkdirs($dFloder); $TG = $dFloder.$ID.'.php';
        return $this->write($TG,$content);
    }
    // alter_table *** *** www.LazyCMS.net *** ***
    private function alter_table($table,$field,$nField,$default){
        $tFloder = $this->path.$table.SEPARATOR;
        $file = $tFloder.'structure.php';
        $cont = $this->read($file);
        $cont = preg_replace("/`".preg_quote($field,"/")."`,'(.*)',(.+)/i","`{$nField}`,'{$default}',\\2",$cont);
        if ($this->write($file,$cont,true)) {
            $iFile = $tFloder.'index'.SEPARATOR.$field.'.php';
            return is_file($iFile) ? rename($iFile,$tFloder.'index'.SEPARATOR.$nField.'.php') : true;
        }
        return false;
    }
    // add_field *** *** www.LazyCMS.net *** ***
    private function add_field($table,$field,$default,$add){
        $file = $this->path.$table.SEPARATOR.'structure.php';
        if ($this->write($file,"`{$field}`,'{$default}',({$add})\n")) {
            if ($add=='index') {
                $this->add_index($table,$field);
            }
            return true;
        }
        return false;
    }
    // drop_index *** *** www.LazyCMS.net *** ***
    private function drop_index($table,$index){
        $tFloder = $this->path.$table.SEPARATOR;
        $file = $tFloder.'structure.php';
        $cont = $this->read($file);
        $cont = preg_replace("/(`".preg_quote($index,"/")."`),'(.*)',\(index\)/i","\\1,'\\2',()",$cont);
        if ($this->write($file,$cont,true)) {
            $iFile = $tFloder.'index'.SEPARATOR.$index.'.php';
            return is_file($iFile) ? unlink($iFile) : true;
        }
        return false;
    }
    // drop_field *** *** www.LazyCMS.net *** ***
    private function drop_field($table,$field){
        $tFloder = $this->path.$table.SEPARATOR;
        $file = $tFloder.'structure.php';
        $cont = $this->read($file);
        $cont = preg_replace("/(`".preg_quote($field,"/")."`),'(.*)',(.+)\n/i","",$cont);
        if ($this->write($file,$cont,true)) {
            $iFile = $tFloder.'index'.SEPARATOR.$field.'.php';
            return is_file($iFile) ? unlink($iFile) : true;
        }
        return false;
    }
    // get_fields *** *** www.LazyCMS.net *** ***
    private function get_fields($fields){
        $I1 = array();
        $eFields = explode(',',$fields);
        $length  = count($eFields);
        if (!$length) { return $I1; }
        foreach ($eFields as $field) {
            $I1[] = trim($field,'`');
        }
        return $I1;
    }
    // get_values *** *** www.LazyCMS.net *** ***
    private function get_values($values){
        $I1 = array();
        $eValues = explode(',',$values);
        $length  = count($eValues);
        if (!$length) { return $I1; }
        foreach ($eValues as $value) {
            $I1[] = trim($value,"'");
        }
        return $I1;
    }
    // get_content *** *** www.LazyCMS.net *** ***
    private function get_content($table,$fields,$values,$ID=0){
        $I1 = null;
        foreach ($fields as $i=>$field) {
            if (strtolower($values[$i])=='null') {
                $values[$i] = $ID;
            } elseif (strlen($values[$i])==0) {
                $s = $this->get_structure($table,$field);
                $values[$i] = $s['default'];
            }
            $I1.= "`".$field."`'".$values[$i]."'\n";
        }
        return rtrim($I1);
    }
    // write_index *** *** www.LazyCMS.net *** ***
    private function write_index($table,$index,$ID){
        $tFloder = $this->path.$table.SEPARATOR;
        foreach ((array)$index as $v) {
            $TG = $tFloder.'index'.SEPARATOR.$v['field'].'.php';
            $this->write($TG,"'".$v['value']."','".$ID."'\n");
        }
        return true;
    }
    // write *** *** www.LazyCMS.net *** ***
    private function write($path,$content='',$mode=false){
        if (file_exists($path)) {
            if (!is_writable($path)) {
                // 数据库没有可写权限
                trigger_error('Can not access the database to write.<br/><strong>DataBase FILE</strong>:'.replace_root($this->path));
            }
            if ($mode) {
                $content = $this->null."\n".$content;
            }
        } else {
            if (!$mode) {
                $content = $this->null."\n".$content;
            }
        }
        $fp = fopen($path,($mode?'wb':'ab'));
        flock($fp,LOCK_EX + LOCK_NB);
        fwrite($fp,$content);
        fclose($fp);
        return true;
    }
    // read *** *** www.LazyCMS.net *** ***
    private function read($path){
        if (!is_file($path)) { return ; }
        $fp = fopen($path,'rb');
        $I1 = null; $i = 1;
        while (!feof($fp)) {
            if ($i!==1) {
                $I1.= fgets($fp,4096);
            } else {
                fgets($fp,4096);
            }
            $i++;
        }
        fclose($fp);
        return $I1;
    }
    // get_serial *** *** www.LazyCMS.net *** ***
    private function get_serial($ID=0){
        $I1 = null;
        for ($i=1; $i<4; $i++) {
            $n = substr($ID,0,$i);
            if (strlen($n)==$i) {
                $I1.= $n.SEPARATOR;
            }
        }
        return $I1;
    }
}

// lazysql_open *** *** www.LazyCMS.net *** ***
function lazysql_open($path,&$error=null){
    return LazySQL::open($path,$error);
}
// lazysql_query *** *** www.LazyCMS.net *** ***
function lazysql_query($handle,$query){
    return $handle->query($query);
}
// lazysql_exec *** *** www.LazyCMS.net *** ***
function lazysql_exec($handle,$query){
    return $handle->exec($query);
}