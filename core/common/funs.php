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
 * LazyCMS 公共函数库
 *
 * @copyright   Copyright (c) 2007-2008 lazycms.net All rights reserved.
 * @author      Lukin <mylukin@gmail.com>
 */

/* *** *** *** *** *** *** *** *** *** *** *** *** 小写函数名 *** *** *** *** *** *** *** *** *** *** *** *** */

// now *** *** www.LazyCMS.net *** ***
function now(){
    return isset($_SERVER['REQUEST_TIME'])?$_SERVER['REQUEST_TIME']:time();
}

// using *** *** www.LazyCMS.net *** ***
function using($l1,$l2='',$l3='.php',$l4=false){
    return import($l1,$l2,$l3,$l4);
}

// replace *** *** www.LazyCMS.net *** ***
function replace($l1,$l2,$l3,$l4=-1){
    //$l1:pattern, $l2:replacement, $l3:subject, $l4:limit
    return preg_replace($l1,$l2,$l3,$l4);
}

// stripslashes_deep *** *** www.LazyCMS.net *** ***
function stripslashes_deep($l1) {
    // $l1:array
    return is_array($l1) ? array_map('stripslashes_deep', $l1) : stripslashes($l1);
}

// array_unset_empty *** *** www.LazyCMS.net *** ***
function array_unset_empty($l1){
    $I1 = array();
    if (!is_array($l1)) { return $I1; }
    foreach ($l1 as $k=>$v) {
        if (!empty($v)) {
            $I1[$k] = $v;
        }
    }
    return $I1;
}

// isfile *** *** www.LazyCMS.net *** ***
function isfile($l1){
    $paths = explode('/',$l1); $count = count($paths);
    if (strpos($paths[$count-1],'.')!==false){
        return true;
    } else {
        return false;
    }
}

// defmenu *** *** www.LazyCMS.net *** ***
function defmenu(){
    return L("diymenu/@title")."|url('System','DiyMenu')\r\n"
          .L("parameters/support")."|javascript:;\r\n    "
          .L("parameters/osite")."|http://www.lazycms.net/\r\n    "
          .L("parameters/forums")."|http://forums.lazycms.net/";
}

// t2js *** *** www.LazyCMS.net *** ***
function t2js($l1,$l2=false){
    $I1 = str_replace(array("\r", "\n"), array('', '\n'), addslashes($l1));
    return $l2 ? "document.writeln(\"$I1\");" : $I1;
}

// htmlencode *** *** www.LazyCMS.net *** ***
function htmlencode($l1){
    $l1 = htmlspecialchars($l1);
    if (strpos($l1,'&amp;')!==false) {
        $l1 = str_replace('&amp;','&',$l1);
    }; return $l1;
}

// htmldecode *** *** www.LazyCMS.net *** ***
function htmldecode($l1){
    return empty($l1) ? $l1 : htmlspecialchars_decode($l1);
}

// language *** *** www.LazyCMS.net *** ***
function language(){
    $I1 = isset($_GET['language'])?$_GET['language']:null;
    if (!$I1) { 
        $I1 = Cookie::get('language');
    }
    return $I1 ? $I1 : C('LANGUAGE');
}

// instr *** *** www.LazyCMS.net *** ***
function instr($l1,$l2){
    if (!is_array($l1)) {
        $l1 = explode(",",$l1);
    }
    return in_array($l2,$l1) ? true : false;
}

// langbox *** *** www.LazyCMS.net *** ***
function langbox($l1){
    $XML = loadFile(CORE_PATH.'/common/language.xml');
    if (preg_match("/<$l1 l=\"(.+)\"\/>/",$XML,$I1)) {
        return $I1[1];
    } else {
        return $l1;
    }
}

// bbimg *** *** www.LazyCMS.net *** ***
function bbimg($l1,$l2){
    return replace('/(<img.+?src=".+?")(.+?)((\/|)>)/i','$1 onmousemove="this.style.cursor=\'pointer\'" onclick="window.open(this.src);" onerror="if(this.width>'.$l2.'){this.width='.$l2.'}"; onload="if(this.width>'.$l2.'){this.width='.$l2.'}" $3',$l1);
}

// salt *** *** www.LazyCMS.net *** ***
function salt($l1=6){
    $l2 = "0123456789abcdefghijklmnopqrstopwxyz";
    $l3 = strlen($l2);$l4 = "";
    for ($i=0;$i<$l1;$i++) {
        $l4 .= $l2[mt_rand(0,strlen($l2)-1)];
    }
    return $l4;
}

// xmlcode *** *** www.LazyCMS.net *** ***
function xmlcode($l1,$l2='utf-8',$l3="lazycms") {
    // $l1:data, $l2:encoding, $l3:root
    $I1 = '<?xml version="1.0" encoding="'.$l2.'"?>';
    $I1.= '<'.$l3.'>';
    $I1.= data2xml($l1);   
    $I1.= '</'.$l3.'>'; 
    return $I1;
}

// pinyin *** *** www.LazyCMS.net *** ***
function pinyin($l1){
    static $I3 = null; $I1 = null;
    preg_match_all(C('CN_PATTERN'),trim($l1),$I2);
    $l2 = $I2[0]; $l3 = count($l2);
    if (empty($I3)) {
        $I3 = include CORE_PATH."/common/pinyin.php";
    }
    for ($i=0;$i<$l3;$i++) {
        if (validate($l2[$i],'^\w+$')){
            $I1.= $l2[$i];
        } elseif (!array_search_value($l2[$i],$I3)) {
            $I1.= '-';
        } else {
            $I1.= ucfirst(array_search_value($l2[$i],$I3));
        }
    }
    return $I1;
}

// array_search_value *** *** www.LazyCMS.net *** ***
function array_search_value($l1,$l2){
    while (list($k,$v)=each($l2)) {
        if (strpos($v,$l1)!==false) {
            return $k;
        }
    }
    return false;
}

// data2xml *** *** www.LazyCMS.net *** ***
function data2xml($l1) {
    // $l1:object or array
    if(is_object($l1)) {
        $l1 = get_object_vars($l1);
    }
    $I1 = null;
    foreach($l1 as $k=>$v) {
        is_numeric($k) && $k="item id=\"{$k}\"";
        $I1.= "<{$k}>";
        $I1.= (is_array($v)||is_object($v)) ? data2xml($v) : ((strlen($v)>0 && is($v)) ? "<![CDATA[{$v}]]>" : $v);
        list($k,) = explode(' ',$k);
        $I1.= "</{$k}>";
    }
    return $I1;
}

// lefte *** *** www.LazyCMS.net *** ***
function lefte($l1,$l2){
    if ((int)len($l1)>(int)$l2) {
        $I1 = cnsubstr($l1,$l2).'...';
    } else{
        $I1 = $l1;
    }
    return $I1;
}

// is *** *** www.LazyCMS.net *** ***
function is($l1,$l2="&,',\",>,<,\n"){
    $I2 = explode(',',$l2);
    foreach ($I2 as $v){
        if (strpos($l1,$v)!==false) {
            return true;
        }
    }
    return false;
}

// len *** *** www.LazyCMS.net *** ***
function len($l1){
    if (function_exists('mb_strlen')) {
        return mb_strlen($l1,'utf-8');
    } elseif (function_exists('iconv_strlen')){
        return iconv_strlen($l1,'utf-8');
    } else {
        preg_match_all(C('CN_PATTERN'),$l1,$I2);
        return count($I2[0]);
    }
}

// encode *** *** www.LazyCMS.net *** ***
function encode($l1){
    if (strlen($l1)==0) { return ; } $I1 = $l1;
    if (strpos($I1,chr(123))!==false) { $I1 = str_replace(chr(123),'&#123;',$I1); }
    if (strpos($I1,chr(125))!==false) { $I1 = str_replace(chr(125),'&#125;',$I1); }
    if (strpos($I1,chr(58))!==false) { $I1 = str_replace(chr(58),chr(3).'#lazy58#'.chr(2),$I1); }
    if (strpos($I1,chr(59))!==false) { $I1 = str_replace(chr(59),chr(3).'#lazy59#'.chr(2),$I1); }
    if (strpos($I1,chr(124))!==false) { $I1 = str_replace(chr(124),chr(3).'#lazy124#'.chr(2),$I1); }
    return $I1;
}

// decode *** *** www.LazyCMS.net *** ***
function decode($l1){
    if (strlen($l1)==0) { return ; } $I1 = $l1;
    if (strpos($I1,chr(3).'#lazy58#'.chr(2))!==false) { $I1 = str_replace(chr(3).'#lazy58#'.chr(2),chr(58),$I1); }
    if (strpos($I1,chr(3).'#lazy59#'.chr(2))!==false) { $I1 = str_replace(chr(3).'#lazy59#'.chr(2),chr(59),$I1); }
    if (strpos($I1,chr(3).'#lazy124#'.chr(2))!==false) { $I1 = str_replace(chr(3).'#lazy124#'.chr(2),chr(124),$I1); }
    if (strpos($I1,'&#123;')!==false) { $I1 = str_replace('&#123;',chr(123),$I1); }
    if (strpos($I1,'&#125;')!==false) { $I1 = str_replace('&#125;',chr(125),$I1); }
    return $I1;
}

// xmlencode *** *** www.LazyCMS.net *** ***
function xmlencode($l1){
    if (strlen($l1)==0) { return ; } $I1 = $l1;
    if (strpos($I1,'&')!==false) { $I1 = str_replace('&','&amp;',$I1); }
    if (strpos($I1,"'")!==false) { $I1 = str_replace("'",'&apos;',$I1); }
    if (strpos($I1,'"')!==false) { $I1 = str_replace('"','&quot;',$I1); }
    if (strpos($I1,'>')!==false) { $I1 = str_replace('>','&gt;',$I1); }
    if (strpos($I1,'<')!==false) { $I1 = str_replace('<','&lt;',$I1); }
    return $I1;
}

// loading *** *** www.LazyCMS.net *** ***
function loading($l1,$l2,$l3){
    $HTML = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
    $HTML.= '<html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
    $HTML.= '<title>loading...</title><style type="text/css"><!--'.chr(10).'body { margin:0px; }'.chr(10);
    $HTML.= '.loading{ width:102px; height:15px; background:#F9F9F9; display:block; float:left; overflow:hidden;}'.chr(10);
    $HTML.= '.loading div{ width:100px; position:absolute; color:#FF0000; text-align:center; font-size:12px; line-height:14px;}'.chr(10);
    $HTML.= '.loading span{ height:13px; border:solid 1px #FFFFFF; background:#0000CC; display:block; overflow:hidden;}'.chr(10).'--></style>';
    $HTML.= '<script type="text/javascript" src="'.C('SITE_BASE').C('PAGES_PATH').'/system/js/jquery.js"></script>';
    $HTML.= '<script language="javascript" type="text/javascript">';
    if ((int)$l2==100) {
        $HTML.= "window.setTimeout(\"\$('#{$l1}',window.parent.\$('#toolbar')).fadeOut('slow',function(){\$('#{$l1}',window.parent.\$('#toolbar')).remove()});\",3000);";
    } else {
        $HTML.= '$(function () { self.location.href=\''.$l3.'\'; });';
    }
    $HTML.= '</script>';
    $HTML.= '<noscript><meta http-equiv="refresh" content="0;url='.$l3.'" /></noscript>';
    $HTML.= '</head><body><div class="loading"><div>'.$l2.'%</div><span style="width:'.$l2.'px;">&nbsp;</span></div></body></html>';
    return $HTML;
}
// exeloading *** *** www.LazyCMS.net *** ***
function exeloading($l1,$l2){
    $js = '<script type="text/javascript" src="'.C('SITE_BASE').C('PAGES_PATH').'/system/js/jquery.js"></script><script type="text/javascript" src="'.C('SITE_BASE').C('PAGES_PATH').'/system/js/jquery.lazycms.js"></script>';
	$js.= "<script type=\"text/javascript\">loading('{$l1}','{$l2}');</script>";
    echo $js;
}
// ubbencode *** *** www.LazyCMS.net *** ***
function ubbencode($l1){
    if (strlen($l1)==0) {return ;}
    $I1 = htmlencode($l1);
    if (strpos($I1,' ')!==false) { $I1 = str_replace(' ','&nbsp;',$I1); }
    $I1 = replace('/\r\n|\n|\r/','<br/>',$I1);
    $I1 = replace('/\[url\](.+?)\[\/url]/i','<a href="$1">$1</a>',$I1);
    $I1 = replace('/\[url\=([^\]]+)](.+?)\[\/url]/i','<a href="$1">$2</a>',$I1);
    $I1 = replace('/\[img\](.+?)\[\/img]/i','<img src="$1" />',$I1);
    $I1 = replace('/\[b\](.+?)\[\/b]/i','<b>$1</b>',$I1);
    $I1 = replace('/\[strong\](.+?)\[\/strong]/i','<strong>$1</strong>',$I1);
    $I1 = replace('/\[i\](.+?)\[\/i]/i','<i>$1</i>',$I1);
    $I1 = replace('/\[u\](.+?)\[\/u]/i','<u>$1</u>',$I1);
    $I1 = replace('/\[s\](.+?)\[\/s]/i','<strike>$1</strike>',$I1);
    $I1 = replace('/\[sub\](.+?)\[\/sub]/i','<sub>$1</sub>',$I1);
    $I1 = replace('/\[sup\](.+?)\[\/sup]/i','<sup>$1</sup>',$I1);
    $I1 = replace('/\[color\=([^\]]+)](.+?)\[\/color]/i','<span style="color: $1">$2</span>',$I1);
    $I1 = replace('/\[bgcolor\=([^\]]+)](.+?)\[\/bgcolor]/i','<span style="background-color: $1">$2</span>',$I1);
    $I1 = replace('/\[font\=([^\]]+)](.+?)\[\/font]/i','<span style="font-family: $1">$2</span>',$I1);
    $I1 = replace('/\[size\=([^\]]+)](.+?)\[\/size]/i','<span style="font-size: $1">$2</span>',$I1);
    $I1 = replace('/\[align\=([^\]]+)](.+?)\[\/align]/i','<div style="text-align: $1">$2</div>',$I1);
    $I1 = replace('/\[p\](.+?)\[\/p]/i','<p>$1</p>',$I1);
    $I1 = replace('/\[div\](.+?)\[\/div]/i','<div>$1</div>',$I1);
    $I1 = replace('/\[pre\](.+?)\[\/pre]/i','<pre>$1</pre>',$I1);
    $I1 = replace('/\[address\](.+?)\[\/address]/i','<address>$1</address>',$I1);
    for ($i=1; $i<7; $i++) {
        $I1 = replace('/\[h'.$i.'\](.+?)\[\/h'.$i.']/i','<h'.$i.'>$1</h'.$i.'>',$I1);
    }
    return $I1;
}

// cls *** *** www.LazyCMS.net *** ***
function cls($l1){
    if (strlen($l1)==0) { return ;} $I1 = $l1;
    if (strpos($I1,chr(9))!==false) { $I1 = str_replace(chr(9),'',$I1); }
    if (strpos($I1,chr(10))!==false) { $I1 = str_replace(chr(10),'',$I1); }
    if (strpos($I1,chr(13))!==false) { $I1 = str_replace(chr(13),'',$I1); }
    while (strpos($I1,chr(32).chr(32))!==false || strpos($I1,'&nbsp;&nbsp;')!==false) {
        $I1 = str_replace(chr(32).chr(32),chr(32),str_replace('&nbsp;&nbsp;',chr(32),$I1));
    }
    return $I1;
}

// ip *** *** www.LazyCMS.net *** ***
function ip(){
    if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
        $I1 = $_SERVER["HTTP_CLIENT_IP"];
    } elseif (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])){
        $I1 = $_SERVER["HTTP_X_FORWARDED_FOR"];
    } elseif (!empty($_SERVER["REMOTE_ADDR"])){
        $I1 = $_SERVER["REMOTE_ADDR"];
    } else {
       $I1 = "Unknown";
    }
    return $I1;
}

// fileICON *** *** www.LazyCMS.net *** ***
function fileICON($l1){
    $I2 = pathinfo($l1);
    $l3 = isset($I2['extension']) ? $I2['extension'] : 'icon';
    $l3 = strtolower($l3);
    switch ($l3) {
        case 'html' :
            $ext = 'htm';
            break;
        case 'jpeg': case 'jpe': case 'jif': case 'jfif':
            $ext = 'jpg';
            break;
        default :
            $ext = $l3;
            break;
    }
    if (!is_file(LAZY_PATH.C('PAGES_PATH')."/system/images/os/file/{$ext}.gif")) {
        $ext = 'icon';
    }
    return $ext;
}

// rmdirs *** *** www.LazyCMS.net *** ***
function rmdirs($l1,$l2=true){
    if ($I2=@opendir($l1)) {
        while (false !== ($I3=readdir($I2))) {
            if ($I3 != "." && $I3 != "..") {
                $l3 = $l1.'/'.$I3;
                is_dir($l3) ? rmdirs($l3,$l2) : ($l2 ? @unlink($l3) : null);
            }
        }
        closedir($I2);
    }
    return @rmdir($l1);
}

// mkdirs *** *** www.LazyCMS.net *** ***
function mkdirs($l1, $l2 = 0777){
    // $l1:dir $l2:mode
    if (!is_dir($l1)) {
        mkdirs(dirname($l1), $l2);
        return @mkdir($l1, $l2);
    }
    return true;
}

// menu *** *** www.LazyCMS.net *** ***
function menu($l1){
    // $l1:title|url('System','Main')|selected;
    $I1 = '<ul class="menu">';
    $I2 = explode(';',$l1);
    foreach ($I2 as $v) {
        if (strpos($v,'|')!==false) {
            $I3 = explode('|',$v);
            if (isset($I3[2])) {
                $selected = ' class="selected"';
                $I3[1]    = 'javascript:void(0);';
            } else {
                $selected = '';
            }
            $I1.= '<li'.$selected.'><a href="'.$I3[1].'">'.$I3[0].'</a></li>';
        } else {
            $I1.= '<li class="selected"><a href="javascript:void(0);">'.$v.'</a></li>';
        }
    }
    $I1.= '</ul>';
    return $I1;
}

// cnsubstr *** *** www.LazyCMS.net *** ***
function cnsubstr($l1,$l2){
    $l3 = 0;
    $l4 = $l2;
    if (func_num_args() >= 3) {
        $l3 = $l2;
        $l4 = func_get_arg(2);
    }
    if (function_exists('mb_substr')) {
        return mb_substr($l1,$l3,$l4,'utf-8');
    } elseif (function_exists('iconv_substr')){
        return iconv_substr($l1,$l3,$l4,'utf-8');
    } else {
        preg_match_all(C('CN_PATTERN'),$l1,$I2);
        if (count($I2[0]) - $l3 > $l4) {
            return implode('',array_slice($I2[0],$l3,$l4));
        }
        return implode('',array_slice($I2[0],$l3,$l4));
    }
}

// redirect *** *** www.LazyCMS.net *** ***
function redirect($l1,$l2=0,$l3=''){
    // $l1:url ,$l2:time ,$l3:msg
    //多行URL地址支持
    $l1 = str_replace(array("\n", "\r"), '', $l1);
    if(empty($l3)) {
        $l3 = L('common/redirect',array('time'=>$l2,'url'=>$l1));
    }
    $js = '<script language="javascript" type="text/javascript">window.setTimeout(function () { document.location.href=\''.$l1.'\'; }, '.($l2*1000).');</script>';
    if (!headers_sent()) {
        // redirect
        header("Content-Type:text/html; charset=utf-8");
        if (0===$l2) {
            header("Location: ".$l1); 
        } else {
            header("refresh:{$l2};url={$l1}");
            echo($l3.$js);
        }
        exit();
    } else {
        $I1 = '<meta http-equiv="refresh" content="'.$l2.';url='.$l1.'" />';
        if ($l2!=0) {
            $I1 .= $l3.$js;
        }
        exit($I1);
    }
}

// validate *** *** www.LazyCMS.net *** ***
function validate($l1,$l2){
    // $l1:str, $l2:类型
    switch($l2){
        case '0' : // 数字，字母，逗号，杠，下划线，[，]
            $l3 = '^[a-zA-Z0-9\,\/\-\_\[\]]+$';
            break;
        case '1' : // 字母
            $l3 = '^[A-Za-z]+$';
            break;
        case '2' : // 匹配数字
            $l3 = '^\d+$';
            break;
        case '3' : // 字母，数字，下划线，杠
            $l3 = '^[A-Za-z0-9\_\-]+$';
            break;
        case '4' : // Email
            $l3 = '^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$';
            break;
        case '5' : // url
            $l3 = '^(http|https|ftp):(\/\/|\\\\)(([\w\/\\\+\-~`@:%])+\.)+([\w\/\\\.\=\?\+\-~`@\':!%#]|(&amp;)|&)+';
            break;
        case '6' : // 
            $l3 = '^[0-9\,\.]+$';
            break;
        case '7' : // 图片连接 http://www.example.com/xxx.jpg
            $l4 = str_replace(',','|',C('UPFILE_SUFFIX'));
            $l3 = '^(http|https|ftp):(\/\/|\\\\)(([\w\/\\\+\-~`@:%])+\.)+([\w\/\\\.\=\?\+\-~`@\':!%#]|(&amp;)|&)+\.({$l4})$';
            break;
        case '8' : // 日期格式
            $l3 = '^((((1[6-9]|[2-9]\d)\d{2})-(0?[13578]|1[02])-(0?[1-9]|[12]\d|3[01]))|(((1[6-9]|[2-9]\d)\d{2})-(0?[13456789]|1[012])-(0?[1-9]|[12]\d|30))|(((1[6-9]|[2-9]\d)\d{2})-0?2-(0?[1-9]|1\d|2[0-8]))|(((1[6-9]|[2-9]\d)(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00))-0?2-29-))(| (20|21|22|23|[0-1]?\d):[0-5]?\d:[0-5]?\d)$';
            break;
        default  : // 自定义正则
            $l3 = $l2;
            break;
    }
    return preg_match("/{$l3}/i",$l1);
}

// snapImg *** *** www.LazyCMS.net *** ***
function snapImg($l1){
    $I1 = $l1;
    if (preg_match_all('/<img.[^>]*src="(.[^>]+?)".[^>]*\/>/i',$l1,$imgs)) {
        foreach ($imgs[1] as $img) {
            if ($downImg = downPic($img)) {
				if (validate($img,7)) {
					$I1 = str_replace($img,C('SITE_BASE').ltrim($downImg,'/'),$I1);
				}
            }
        }
    }
    return $I1;
}

// downPic *** *** www.LazyCMS.net *** ***
function downPic($img,$path=null){
    static $d = null;
    if (validate($img,7)) {
        if (!is_object($d)) {
            import("system.downloader");
            $d = new DownLoader();
            $d->timeout = 100;
        }
        $d->connect($img,'GET',$d->timeout)->send();
        if ($d->status() == 200) {
            if (empty($path)) {
                $imgInfo = pathinfo($img);
                $imgPath = C('UPFILE_PATH').date('/'.C('UPFILE_PATH_STYLE').'/',now());
                if (isset($imgInfo['extension']) && isset($imgInfo['filename'])) {
                    $fileName = $imgInfo['filename'].'.'.$imgInfo['extension'];
                    if (is_file(LAZY_PATH.$imgPath.$fileName)) {
                        $fileName = salt(16).'.'.$imgInfo['extension'];
                    }
                } else {
                    if(preg_match("/Content-Type\:(.+)\r\n/i",$d->header(),$imgInfo)){
                        $fileName = salt(16).'.'.substr($imgInfo[1],strrpos($imgInfo[1],'/')+1,strlen($imgInfo[1]));
                    }
                }
            } else {
                $imgInfo = pathinfo($path);
                $imgPath = $imgInfo['dirname'];
                $fileName = $imgInfo['basename'];
            }
            mkdirs(LAZY_PATH.$imgPath);
            saveFile(LAZY_PATH.$imgPath.$fileName,$d->body());
            return $imgPath.$fileName;
        } else {
            return false;
        }
    }
    return $img;
}

// url *** *** www.LazyCMS.net *** ***
function url($l1=null,$l2=null,$l3=null){
    //$l1:module $l2:action $l3:params
    $I2 = C('URL_MODEL');$I3 = C('PATH_DEPR');
    if (!$l1) { $l1 = C('DEFAULT_MODULE'); }
    if (!$l2) { $l2 = C('DEFAULT_ACTION'); }
    $l5 = C('SITE_BASE');
    // 根据网站启用模式，取得 bootstrap
    if (!C('SITE_MODE') && $I2!=URL_REWRITE) { // 静态模式
        // 默认静态模式，静态模式 动态url从 page/index.php开始
        // 设置基础路径 C('PAGES_PATH')
        $l5 .= C('PAGES_PATH').'/';
    }
    // URL_REWRITE 模式不增加 index.php
    if ($I2==URL_PATHINFO || $I2==URL_COMMON) {
        $l5 .= C('VAR_INDEX');
    }
    // URL 模式下的路径组合
    if ($I2==URL_COMMON) {
        $I1 = $l5.'?';
    } elseif ($I2==URL_PATHINFO) {
        if (!IS_APACHE && IS_IIS) {
            $I1 = $l5.'?'.$I3;
        } else {
            $I1 = $l5.$I3;
        }
    } elseif ($I2==URL_REWRITE) {
        $I1 = $l5;
    }
    // 判断网站启用URL模式
    if ($I2==URL_PATHINFO || $I2==URL_REWRITE) { //rewrite模式
        $I1 .= $l1.$I3.$l2;
        if (is_array($l3)) {
            $I1 .= $I3.argsURL($l3,$I3);
        } else {
            if (!empty($l3)) {
                parse_str($l3,$l6);
                $I1 .= $I3.argsURL($l6,$I3);    
            }
        }
    } else { // 普通模式
        $l6  = '&';
        $I1 .= C('VAR_MODULE').'='.$l1.$l6;
        $I1 .= C('VAR_ACTION').'='.$l2;
        if (is_array($l3)) {
            $I1 .= $l6.http_build_query($l3);
        } else {
            if (!empty($l3)) {
                $I1 .= $l6.ltrim($l3,$l6);
            }
        }
    }
    // 静态模式，URL_PATHINFO and URL_REWRITE，增加伪静态后缀
    if (($I2==URL_PATHINFO || $I2==URL_REWRITE) && C('HTML_URL_SUFFIX')) {
        $I1 .= C('HTML_URL_SUFFIX');
    }
    // 不转换变量
    $l7 = func_num_args()>3 ? func_get_arg(3) : null;
    if (!empty($l7)) {
        if (strpos($I1,'$')!==false) {
            return str_replace('$',$l7,$I1);
        }
        if (is_array($l7)) {
            foreach ($l7 as $k=>$v) {
                $I1 = str_replace(":{$k}",$v,$I1);
            }
        }
    }
    return $I1;
}

// vendor *** *** www.LazyCMS.net *** ***
function vendor($l1,$l2='',$l3='.php',$l4=false){
    if(empty($l2)) {
        $l2 = VENDOR_PATH;
    }
    return import($l1,$l2,$l3,$l4);
}

// import *** *** www.LazyCMS.net *** ***
function import($l1,$l2='',$l3='.php',$l4=false){
    // $l1:path, $l2:baseUrl, $l3:ext, $l4:subDir
    static $_I1 = array();
    // 已经加载的文件不再加载
    if (isset($_I1[strtolower($l1.$l2)])) {
        return true;
    } else {
        $_I1[strtolower($l1.$l2)] = true;
    }
    if (empty($l2)) {
        // 默认方式调用应用类库
        $l2 = CORE_PATH;
    } else {
        // 相对路径调用
        $l5 = true;
    }
    $I2 = explode('.',$l1);
    if ('*' == $I2[0] || isset($l5)) {
        /**
         * 多级目录加载支持
         * 用于子目录递归调用
         */
    } elseif('@' == $I2[0]) {
        // 加载page目录的文件
        $l1 = str_replace('@',C('PAGES_PATH'),$l1);
        $l2 = LAZY_PATH;
    }
    if (substr($l2, -1) != '/') { $l2 .= '/'; }
    $l6 = $l2.str_replace('.', '/', $l1).$l3;
    if (false !== strpos($l6,'*') || false !== strpos($l6,'?')) {
        // 导入匹配的文件
        $l7 = glob($l6);
        if ($l7) {
            foreach($l7 as $k=>$v) {
                if(is_dir($v)) {
                    if($l4) { import('*',$v.'/',$l3,$l4); }
                } else {
                    // 导入类库文件
                    $I1 = requireFile($v);
                }
            }
            return $I1;
        } else {
            return false;
        }
    } else {
        // 导入目录下的指定类库文件
        return requireFile($l6);
    }
}

//pagelist *** *** www.LazyCMS.net *** ***
function pagelist($l1,$l2,$l3,$l4){
    //url,page,总页数,记录总数
    $I1 = null;
    if (strpos($l1,'%24')!==false) { $l1 = str_replace('%24','$',$l1); }
    if (strpos($l1,'$')==0 || $l4==0) { return ; }
	$l7 = C('SITE_MODE') ? 1 : null;
    if ($l2 > 3) {
        $I1 = '<a href="'.str_replace('$',$l7,$l1).'">1 ...</a>';
    }
    if ($l2 > 2) {
        $I1 .= '<a href="'.str_replace('$',$l2-1,$l1).'">&lsaquo;&lsaquo;</a>';
    } elseif ($l2==2) {
        $I1 .= '<a href="'.str_replace('$',$l7,$l1).'">&lsaquo;&lsaquo;</a>';
    }
    $l5 = $l2-2;
    $l6 = $l2+7;
    for ($i=$l5; $i<=$l6; $i++) {
        if ($i>=1 && $i<=$l3) {
            if ((int)$i==(int)$l2) {
                $I1 .= "<strong>$i</strong>";
            } else {
                if ($i==1) {
                    $I1 .= '<a href="'.str_replace('$',$l7,$l1).'">'.$i.'</a>';
                } else {
                    $I1 .= '<a href="'.str_replace('$',$i,$l1).'">'.$i.'</a>';
                }
            }
        }
    }
    if ($l2 < $l3) {
        $I1 .= '<a href="'.str_replace('$',$l2+1,$l1).'">&rsaquo;&rsaquo;</a>';
    }
    if ($l2 < ($l3-7)) {
        $I1 .= '<a href="'.str_replace('$',$l3,$l1).'">... '.$l3.'</a>';
    }
    $I2 = explode('$',$l1);
    $I1.= '<kbd><input type="text" name="page" size="2" onkeydown="if(event.keyCode==13) {window.location=\''.t2js($I2[0]).'\'+this.value+\''.t2js($I2[1]).'\'; return false;}" /></kbd>';
    return '<div class="pagelist"><em>'.$l4.'</em>'.$I1.'</div>';
}

// sect *** *** www.LazyCMS.net *** ***
function sect($l1,$l2,$l3,$l4=null){
    $I1 = null;
    if (empty($l1) || empty($l2) || empty($l3)) { return ;}
    if (substr($l2,0,1)==chr(40) && substr($l2,-1)==chr(41) && substr($l3,0,1)==chr(40) && substr($l3,-1)==chr(41)) {
        if (preg_match('/'.$l2.'((.|\n)+?)'.$l3.'/i',$l1,$I2)) {
            if (count($I2)>0) {
                $l5 = $I2[0];
            }
        }
        if (preg_match('/'.$l2.'/i',$l5,$I2)) {
            if (count($I2)>0) {
                $l6 = $I2[0];
            }
        }
        if (preg_match('/'.$l3.'/i',$l5,$I2)) {
            if (count($I2)>0) {
                $l7 = $I2[0];
            }
        }
    } else {
        $l5 = $l1; $l6 = $l2; $l7 = $l3;
    }
    $l8 = strpos(strtolower($l5),strtolower($l6));
    if ($l8===false) {return ;}
    $l9 = strpos(strtolower(substr($l5,-(strlen($l5)-$l8-strlen($l6)))),strtolower($l7));
    if ($l8!==false && $l9!==false) {
        $I1 = trim(substr($l5,$l8 + strlen($l6),$l9));
    }
    if (strlen($I1)>0 && strlen($l4)>0) {
        $I1 = clsre($I1,$l4);
    }
    return $I1;
}

// clsre *** *** www.LazyCMS.net *** ***
function clsre($l1,$l2){
    $I1 = $l1;
    $I2 = explode("\n",$l2);
    foreach ($I2 as $l3) {
        $l3 = trim($l3);
        if (!empty($l3)) {
            if (trim(substr($l3,0,1))==chr(40) && trim(substr($l3,-1))==chr(41)) {
                $I1 = replace('/'.$l3.'/i','',$I1);
            } else {
                if (strpos($I1,$l3)!==false) { $I1 = str_replace($l3,'',$I1); }
            }
        }
    }
    return $I1;
}

// check *** *** www.LazyCMS.net *** ***
function check($l1){
    // username|1|L('login/check/name')|2-30
    $I1 = null;
    $I2 = explode("|",$l1);
    $l2 = urldecode(isset($_POST[$I2[0]]) ? $_POST[$I2[0]] : null); // POST值
    $l3 = isset($I2[1]) ? (string)$I2[1] : null; // 类型
    $l4 = isset($I2[2]) ? (string)$I2[2] : null; // 提示错误
    switch ($l3) {
        case '0' : // 值为空，返回错误
            if ($l2 == '') { $I1 = $l4; }
            break;
        case '1' : // 验证长度
            $I3 = explode("-",$I2[3]);
            if (len($l2) < (int)$I3[0] || len($l2) > (int)$I3[1]) { $I1 = $l4; }
            break;
        case '2' : // 验证两个值是否相等
            $l5 = isset($_POST[$I2[3]]) ? $_POST[$I2[3]] : null;
            if ($l2 != $l5) { $I1 = $l4; }
            break;
        case '3' :
            $db = getConn();
            if (strpos($I2[3],'#pro#')!==false) {
                $l5 = str_replace('#pro#',$l2,$I2[3]);
            } else {
                $l5 = $I2[3];
            }
            if ($db->result($l5) > 0) { $I1 = $l4; }
            unset($db);
            break;
        case '4' :
            $l5 = array("'","\\",":","*","?","<",">","|",";",",");
            if (instr("/,.",substr($l2,-1)) || instr("/,.",substr($l2,0,1))){
                $I1 = $l4; break;
            }
            foreach ($l5 as $v) {
                if (strpos($l2,$v)!==false) {
                    $I1 = $l4; break;
                }
            }
            break;
        case '5' :
            $l5 = isset($I2[3]) ? $I2[3] : null;
            if ($I2[3]=='false') {
                $I1 = $l4; break;
            }
            break;
        default :
            $l5 = isset($I2[3]) ? $I2[3] : null;
            if (!validate($l2,$l5)) { $I1 = $l4; }
            break;
    }
    return $I1;
}

/* *** *** *** *** *** *** *** *** *** *** *** *** 大小写函数名 *** *** *** *** *** *** *** *** *** *** *** *** */

// throwError *** *** www.LazyCMS.net *** ***
function throwError($l1,$l2=0,$l3=false){
    // $l1:message, $l2:code, $l3:extra
    throw new Error($l1,$l2,$l3);
}

// isOK *** *** www.LazyCMS.net *** ***
function isOK($l1){
    return $l1 ? '<span style="color:#009900;">√</span>' : '<span style="color:#FF0000;">×</span>';
}

// formatTemplet *** *** www.LazyCMS.net *** ***
function formatTemplet($l1){
    return replace('/(<(script|link|img|input|embed|param|object|base|area|map|table|param).+?(src|href|background|value)\=.+?)(\.\.\/\.\.\/|\.\.\/)(([\w\-\.]+)\/(images|js)\/.{0,}?>)/i','${1}'.C('SITE_BASE').C('PAGES_PATH').'/${5}',$l1);
}

// clearCache *** *** www.LazyCMS.net *** ***
function clearCache(){
    header("Expires:".date("D,d M Y H:i:s",now()-60*10)." GMT");
    header("Last-Modified:".date("D,d M Y H:i:s")." GMT");
    header("Cache-Control:no-cache,must-revalidate");
    header("Pragma:no-cache");
}

// clearHTML *** *** www.LazyCMS.net *** ***
function clearHTML($l1){
    if (strlen($l1)==0) { return ; } $l1 = replace('/\r\n|\n/',' ',$l1);
    return replace('/<script(.|\n)+?<\/script>|<style(.|\n)+?<\/style>|<[^>]*>/i','',$l1);
}

// getObject *** *** www.LazyCMS.net *** ***
function getObject(){
    static $object = null;
    if (is_object($object)) { return $object; }
    $object = C('PRIVATE_OBJECT');
    return $object;
}

// likey *** *** www.LazyCMS.net *** ***
function likey($l1,$l2){
    //$l1:字段名  $l2:值
    $I1 = null; $I2 = explode(',',$l2);
    foreach ($I2 as $l3) {
        if (empty($I1)) {
            $I1 = "binary ucase({$l1}) LIKE ucase('%{$l3}%')";
        } else {
            $I1 .= " OR binary ucase({$l1}) LIKE ucase('%{$l3}%')";
        }
    }
    return $I1;
}
// getConn *** *** www.LazyCMS.net *** ***
function getConn(){
    static $db = null;
    if (is_object($db)) { return $db; }
    $db = DB::factory(C('DSN_CONFIG'));
    $db->select();
    return $db;
}

// getTpl *** *** www.LazyCMS.net *** ***
function getTpl(LazyCMS $this){
    // $this: control对象
    static $tpl = null;
    if (is_object($tpl)) { return $tpl; }
    $tpl = O('Template');
    $tpl->assign('module',$this);
    return $tpl;
}

// loadFile *** *** www.LazyCMS.net *** ***
function loadFile($l1){
    $fp = fopen($l1,'rb');
    $I1 = fread($fp,filesize($l1));
    fclose($fp);
    return $I1;
}

// saveFile *** *** www.LazyCMS.net *** ***
function saveFile($l1,$l2=''){
    $fp = fopen($l1,'wb');
    fwrite($fp,$l2);
    fclose($fp);
}

// leftHTML *** *** www.LazyCMS.net *** ***
function leftHTML($l1,$l2){
    $I1 = null;
    $I2 = explode("\n",$l1);
    foreach ($I2 as $l3) {
        $I1 .= $l3."\n";
        $j  = $j + len($l3);
        if ((int)$j>(int)$l2) {
            break;
        }
    }
    return $I1;
}

// emptyDir *** *** www.LazyCMS.net *** ***
function emptyDir($l1){
    $I2 = @opendir($l1);
    while (($l2= readdir($I2)) !== false) {
        if ($l2 != "." && $l2 != "..") {
            closedir($I2);
            return false;
        }
    }
    closedir($I2);
    return true;
}

// formatDate *** *** www.LazyCMS.net *** ***
function formatDate($l1,$l2){
    if (strlen($l1)==0) { return ; }
    switch ($l2) {
        case '0':
            $I1 = date('Y-n-j G:i:s',$l1);
            break;
        case '1':
            $I1 = date('Y-m-d H:i:s',$l1);
            break;
        default:
            $I1 = date($l2,$l1);
            break;
    }
    return $I1;
}

// includeFile *** *** www.LazyCMS.net *** ***
function includeFile($l1){
    // $l1:filePath
    static $_I1 = array();
    if (is_file($l1)) {
        if (!isset($_I1[$l1])) {
            include $l1;
            $_I1[$l1] = true;
            return true;
        }
        return false;
    }
    return false;
}

// requireFile *** *** www.LazyCMS.net *** ***
function requireFile($l1){
    // $l1:filePath
    static $_I1 = array();
    if (is_file($l1)) {
        if (!isset($_I1[$l1])) {
            require $l1;
            $_I1[$l1] = true;
            return true;
        }
        return false;
    }
    return false;
}

// argsURL *** *** www.LazyCMS.net *** ***
function argsURL($l1,$l2='/'){
    // $l1:array
    if (empty($l1) || !is_array($l1)) { return $l1; }
    $I1 = '';
    foreach ($l1 as $k=>$v) {
        if ($I1=='') {
            $I1 .=  $k.$l2.$v;
        } else {
            $I1 .=  $l2.$k.$l2.$v;
        }
    }
    return $I1;
}

// getURL *** *** www.LazyCMS.net *** ***
function getURL(){
    static $I1 = null;
    if (!empty($I1)) { return $I1; }
    $I1 = getUriBase();
    // 取得请求的URL
    if (!empty($_SERVER['PATH_INFO'])) {
        $I1 .= $_SERVER['PATH_INFO'];
    } elseif (!empty($_SERVER['REQUEST_URI'])) {
        $I1 = $_SERVER['REQUEST_URI'];
    } else {
        if (empty($_SERVER["QUERY_STRING"])) {
            $I1 .= '?'.$_SERVER["QUERY_STRING"];
        } else {
            $I1 = $_SERVER['PHP_SELF'];
        }
    }
    return $I1;
}

// getArrDir *** *** www.LazyCMS.net *** ***
function getArrDir($l1,$l2){
    //$l1:路径 $l2:读取类型
    if (strpos($l1,'@')!==false) { $l1 = str_replace('@',C('PAGES_PATH'),$l1); }
    $l1 = LAZY_PATH.str_replace('.', '/', $l1);
    if (strpos($l1,'[')!==false) { $l1 = str_replace('[','*',$l1); }
    if (strpos($l1,']')!==false) { $l1 = str_replace('[','*',$l1); }
    $l3 = create_function('&$l1,$l2','$I2=strrpos($l1,"/"); $I1=substr($l1,$I2+1); $l1=$I1;');
    if (substr($l1,-1)!='/') { $l1 .= '/'; }
    $I1 = ($l2=='dir') ? glob("{$l1}*",GLOB_ONLYDIR) : glob("{$l1}*.{$l2}",GLOB_BRACE);
    array_walk($I1,$l3);
    return $I1;
}

// formOpts *** *** www.LazyCMS.net *** ***
function formOpts($l1,$l2,$l3,$l4=null){
    $I1 = null;
    $I2 = getArrDir($l1,$l2);
    if (strpos($l3,'%23')!==false) { $l3 = str_replace('%23','#',$l3); }
    foreach ($I2 as $l5) {
        if ($l2=='xml') {
            $l5 = basename($l5,".xml");
            $l6 = langbox($l5);
        } else{
            $l6 = $l5;
        }
        $I3 = $l3;
        if (strpos($I3,'#value#')!==false) { $I3 = str_replace('#value#',$l5,$I3); }
        if (strpos($I3,'#name#')!==false)  { $I3 = str_replace('#name#',$l6,$I3); }
        if ($l4==$l5) {
            $I3 = str_replace('#selected#',' selected="selected"',$I3);
        } else{
            $I3 = str_replace('#selected#','',$I3);
        }
        $I1 .= $I3;
    }
    return $I1;
}

// formatMenu *** *** www.LazyCMS.net *** ***
function formatMenu($l1){
    $I1 = null; $l2 = $l1;
    if (strpos($l2,chr(10).chr(13))!==false) { $l2 = str_replace(chr(10).chr(13),chr(10),$l2); }
    if (strpos($l2,chr(13))!==false) { $l2 = str_replace(chr(13),chr(10),$l2); }
    if (strpos($l2,chr(10).chr(10))!==false) { $l2 = str_replace(chr(10).chr(10),chr(10),$l2); }
    $I2 = explode(chr(10),$l2);
    $count = count($I2);
    for ($i=0; $i<$count; $i++) {
        $l3 = $I2[$i];
        if (strpos($l2,'|')===false) { continue; }
        $I3 = explode('|',$l3);
        if (count($I3)==2) {
            if (instr('http:/,ftp://,https:',substr($I3[1],0,6))) {
                $I1 .='<li><a href="'.$I3[1].'" target="_blank">'.htmlencode(trim($I3[0])).'</a>';
            } else {
                if (preg_match('/url\(.+\)/',$I3[1])) {
                    $I3[1] = eval('return '.$I3[1].';');
                }
                $I1 .='<li><a href="'.$I3[1].'">'.htmlencode(trim($I3[0])).'</a>';
            }
            $l4 = isset($I2[$i+1]) ? $I2[$i+1] : null;
            if ($l4!==false) {
                if ((strncmp($l3,chr(32),1)===0 && strncmp($l4,chr(32),1)===0) || (strncmp($l3,chr(32),1)!==0 && strncmp($l4,chr(32),1)!==0)) {
                    $I1 .='</li>';
                } else {
                    if (strncmp($l3,chr(32),1)!==0 && strncmp($l4,chr(32),1)===0) {
                        $I1 .='<ul>';
                    }
                    if (strncmp($l3,chr(32),1)===0 && strncmp($l4,chr(32),1)!==0) {
                        $I1 .='</li></ul></li>';
                    }
                }
            }
            
        }
    }
    return $I1;
}

// getUriBase *** *** www.LazyCMS.net *** ***
function getUriBase(){
    static $I1 = null;
    if (!empty($I1)) { return $I1; }
    // 取得请求的文件名
    if (empty($_SERVER['SCRIPT_NAME'])) {
        if (!empty($_SERVER['PATH_INFO'])) {
            if (!empty($_SERVER['REQUEST_URI'])) {
                $_SERVER['SCRIPT_NAME'] = $_SERVER['REQUEST_URI'];
            } else {
                $_SERVER['SCRIPT_NAME'] = $_SERVER['PHP_SELF'];
            }
            $_SERVER['SCRIPT_NAME'] = substr($_SERVER['SCRIPT_NAME'],0,- strlen($_SERVER['PATH_INFO']));
        } elseif (!empty($_SERVER['PHP_SELF'])) {
            $_SERVER['SCRIPT_NAME'] = $_SERVER['PHP_SELF'];
        } elseif (!empty($_SERVER["REQUEST_URI"]) && !empty($_SERVER["QUERY_STRING"])) {
            $_SERVER['SCRIPT_NAME'] = substr($_SERVER['REQUEST_URI'],0,- strlen($_SERVER['QUERY_STRING']));
        }
    }
    $I1 = $_SERVER['SCRIPT_NAME'];
    return $I1;
}

/* *** *** *** *** *** *** *** *** *** *** *** *** 单个大写字母函数名 *** *** *** *** *** *** *** *** *** *** *** *** */

// C *** *** www.LazyCMS.net *** ***
function C($l1=null,$l2=null) {
    // $l1:name, $l2:value
    static $_I1 = array();
    if(!is_null($l2)) {
        $_I1[strtolower($l1)] = $l2;
        return ;
    }
    if(empty($l1)) {
        return $_I1;
    }
    // 合并用户配置到默认配置
    if(is_array($l1)) {
        $_I1 = array_merge($_I1,array_change_key_case($l1));
        return $_I1;
    }
    if(isset($_I1[strtolower($l1)])) {
        return $_I1[strtolower($l1)];
    }else{
        return false;
    }
}

// M *** *** www.LazyCMS.net *** ***
function M($l1=null,$l2=null,$l3=null) {
    // $l1:module name, $l2:name, $l3:value
    $l1 = strtolower($l1);
    $l2 = strtolower($l2);
    static $_I1 = array();
    if (is_file(LAZY_PATH.C('PAGES_PATH').'/'.$l1.'/config.php') && !isset($_I1[$l1])) {
        $_I1[$l1] = array_change_key_case(include LAZY_PATH.C('PAGES_PATH').'/'.$l1.'/config.php');
    }
    if(!is_null($l3)) {
        $_I1[$l1][$l2] = $l3;
        return ;
    }
    if(empty($l2)) {
        return $_I1[$l1];
    }
    // 合并用户配置到默认配置
    if(is_array($l2)) {
        $_I1 = array_merge($_I1,array_change_key_case($l2));
        return $_I1;
    }
    if(isset($_I1[$l1][$l2])) {
        return $_I1[$l1][$l2];
    }else{
        return false;
    }
}

// O *** *** www.LazyCMS.net *** ***
function O($l1,$l2='system'){
    // $l1:className, $l2:path
    static $_I1 = array();
    $l1 = strtolower($l1);
    if(isset($_I1[$l1])) { return $_I1[$l1]; }
    // 导入类文件
    import("{$l2}.{$l1}");
    // 创建对象，并返回
    if(class_exists(ucfirst($l1))) {
        $I1 = new $l1();
        $_I1[$l1] = $I1;
        return $I1;     
    }else {
        return false;
    }
}
// L *** *** www.LazyCMS.net *** ***
function L($l1,$l2=null,$l3='system'){
    // $l1:xpath, $l2:array, $l3:module
    // $l2:array,xml变量标签替换成指定的值
    static $_I1 = array();
    if ($l1=='') { return $l1; }
    $l3 = strtolower($l3);
    $l4 = language();
    $l7 = isset($_I1["{$l3}_{$l4}"]) ? $_I1["{$l3}_{$l4}"] : null;
    if (is_object($l7)) {
        $I2 = $l7;
    } else {
        $l5 = LAZY_PATH.C('PAGES_PATH')."/{$l3}/language/";
        $l6 = $l5.$l4.'.xml';
        if (!is_file($l6)) { $l6 = $l5.C('LANGUAGE').'.xml'; }
        if (!is_file($l6)) { return '['.$l1.']';}
        $I2 = DOMDocument::load($l6);
        $_I1["{$l3}_{$l4}"] = $I2;
    }
    $I3 = new DOMXPath($I2);
    $I4 = $I3->evaluate("//lazycms/$l1");
    if (false !== strpos($l1,'/@')) {
        $I1 = $I4->item(0)->value;
    } else {
        $I1 = $I4->item(0)->nodeValue;
    }
    if (is_array($l2)) {
        foreach ($l2 as $k=>$v) {
           $I1 = str_replace('{$'.$k.'}',$v,$I1); 
        }
    }
    return $I1 ? $I1 : '['.$l1.']';
}

// createNote *** *** www.LazyCMS.net *** ***
function createNote($l1){
return <<<NOTE
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
 * {$l1}
 */
NOTE;
}
?>