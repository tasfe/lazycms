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
 * LazyCMS 公共函数库
 *
 * @copyright   Copyright (c) 2007-2008 lazycms.net All rights reserved.
 * @author      Lukin <mylukin@gmail.com>
 * @date        2008-6-17
 */

// now *** *** www.LazyCMS.net *** ***
function now(){
    return time() + (C('TIME_ZONE')*3600);
}

// stripslashes_deep *** *** www.LazyCMS.net *** ***
function stripslashes_deep($p1,$p2='stripslashes') {
    return is_array($p1) ? array_map('stripslashes_deep', $p1) : $p2($p1);
}

// object_deep *** *** www.LazyCMS.net *** ***
function object_deep($p1) {
    if (is_object($p1) || is_array($p1)) {
        $R = array();
        foreach ((array)$p1 as $k=>$v) {
            $R[$k] = object_deep($v);
        }
        return $R;
    } else {
        return $p1;
    }
}

// nocache *** *** www.LazyCMS.net *** ***
function nocache(){
    header("Expires:".date("D,d M Y H:i:s",now()-60*10)." GMT");
    header("Last-Modified:".date("D,d M Y H:i:s")." GMT");
    header("Cache-Control:no-cache,must-revalidate");
    header("Pragma:no-cache");
}

// salt *** *** www.LazyCMS.net *** ***
function salt($p1=6){
    $p2 = "0123456789abcdefghijklmnopqrstopwxyz";
    $p3 = strlen($p2); $p4 = null;
    for ($i=0;$i<$p1;$i++) {
        $p4.= $p2[mt_rand(0,strlen($p2)-1)];
    }
    return $p4;
}

// replace_root *** *** www.LazyCMS.net *** ***
function replace_root($p1){
    return str_replace(SEPARATOR,'/',str_replace(LAZY_PATH.SEPARATOR,SITE_BASE,$p1));
}

// t2js *** *** www.LazyCMS.net *** ***
function t2js($p1,$p2=false){
    $R = str_replace(array("\r", "\n"), array('', '\n'), addslashes($p1));
    return $p2 ? "document.writeln(\"$R\");" : $R;
}

// h2encode *** *** www.LazyCMS.net *** ***
function h2encode($p1){
    return htmlspecialchars($p1);
}

// h2decode *** *** www.LazyCMS.net *** ***
function h2decode($p1){
    return empty($p1) ? $p1 : htmlspecialchars_decode($p1);
}

// get_php_setting *** *** www.LazyCMS.net *** ***
function get_php_setting($p1){
    $R = (ini_get($p1) == '1' ? 1 : 0);return isok($R);
}

// instr *** *** www.LazyCMS.net *** ***
function instr($p1,$p2){
    if (strlen($p1)==0) { return false; }
    if (!is_array($p1)) { $p1 = explode(",",$p1); }
    return in_array($p2,$p1) ? true : false;
}

// isok *** *** www.LazyCMS.net *** ***
function isok($p1){
    return $p1 ? '<strong style="color:#009900;">'.L('common/on','system').'</strong>' :
                    '<strong style="color:#FF0000;">'.L('common/off','system').'</strong>';
}

// left *** *** www.LazyCMS.net *** ***
function left($p1,$p2){
    if ((int)len($p1)>(int)$p2) {
        return cnsubstr($p1,$p2).'...';
    } else{
        return $p1;
    }
}

// vsort *** *** www.LazyCMS.net *** ***
function vsort($p1){
    if (empty($p1) || !is_array($p1)) { return array(); }
    sort($p1); $R = $p2 = array();
    foreach ($p1 as $v){
        $p2[] = len($v);
    }
    do {
        $p3 = min($p2);
        $pk = array_search($p3,$p2);
        if ($pk === false) { break; }
        $R[] = $p1[$pk];
        unset($p2[$pk]);
    } while (count($p2)>0);
    return $R;
}

// get_conn *** *** www.LazyCMS.net *** ***
function get_conn(){
    static $db = null;
    if (is_object($db)) { return $db; }
    $db = DB::factory(C('DSN_CONFIG'));
    $db->select_db();
    return $db;
}

// array_search_value *** *** www.LazyCMS.net *** ***
function array_search_value($p1,$p2){
    while (list($k,$v)=each($p2)) {
        if (strpos($v,$p1)!==false) {
            return $k;
        }
    }
    return false;
}

// clear_cache *** *** www.LazyCMS.net *** ***
function clear_cache(){
    header("Expires:".date("D,d M Y H:i:s",now()-60*10)." GMT");
    header("Last-Modified:".date("D,d M Y H:i:s")." GMT");
    header("Cache-Control:no-cache,must-revalidate");
    header("Pragma:no-cache");
}

// language *** *** www.LazyCMS.net *** ***
function language(){
    $R = isset($_GET['language'])?$_GET['language']:null;
    if (!$R) { $R = Cookie::get('language'); }
    return $R ? $R : C('LANGUAGE');
}

// utf2ansi *** *** www.LazyCMS.net *** ***
function utf2ansi($p1,$p2='GB2312'){
    if (function_exists('iconv')) {
        return iconv('UTF-8',"{$charset}//IGNORE",$p1);
    } elseif (function_exists('mb_convert_encoding')){
        return mb_convert_encoding($p1,$p2,'UTF-8');
    } else {
        return $p1;
    }
}

// ansi2utf *** *** www.LazyCMS.net *** ***
function ansi2utf($p1){
    if (strlen($p1)==0) { return ;}
    if (is_utf8($p1)) { return $p1; }
    if (function_exists('iconv')) {
        return iconv('',"UTF-8//IGNORE",$p1);
    } elseif (function_exists('mb_convert_encoding')){
        return mb_convert_encoding($p1,'UTF-8','auto');
    } else {
        return $p1;
    }
}

// is_utf8 *** *** www.LazyCMS.net *** ***
function is_utf8($p1){
    return preg_match('%^(?:
            [\x09\x0A\x0D\x20-\x7E] # ASCII
            | [\xC2-\xDF][\x80-\xBF] # non-overlong 2-byte
            | \xE0[\xA0-\xBF][\x80-\xBF] # excluding overlongs
            | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2} # straight 3-byte
            | \xED[\x80-\x9F][\x80-\xBF] # excluding surrogates
            | \xF0[\x90-\xBF][\x80-\xBF]{2} # planes 1-3
            | [\xF1-\xF3][\x80-\xBF]{3} # planes 4-15
            | \xF4[\x80-\x8F][\x80-\xBF]{2} # plane 16
            )*$%xs',$p1);
}

// but *** *** www.LazyCMS.net *** ***
function but($p1){
    $R = '<p class="button"><button type="submit" class="submit" onclick="return $(this.form).save();">'.L("common/{$p1}",'system').'</button>';
    $R.= '<button type="button" class="apply" onclick="return $(this.form).apply();">'.L("common/apply",'system').'</button>';
    $R.= '<button type="reset" onclick="javascript:return confirm(\''.L('confirm/reset','system').'\')">'.L('common/reset','system').'</button>';
    $R.= '<button type="button" onclick="javascript:history.back();">'.L('common/back','system').'</button></p>';
    return $R;
}

// rmdirs *** *** www.LazyCMS.net *** ***
function rmdirs($p1,$p2=true){
    if ($R1=@opendir($p1)) {
        while (false !== ($R2=readdir($R1))) {
            if ($R2 != "." && $R2 != "..") {
                $p3 = $p1.'/'.$R2;
                is_dir($p3) ? rmdirs($p3,$p2) : ($p2 ? @unlink($p3) : null);
            }
        }
        closedir($R1);
    }
    return @rmdir($p1);
}

// mkdirs *** *** www.LazyCMS.net *** ***
function mkdirs($p1, $p2 = 0777){
    // $p1:dir $p2:mode
    if (!is_dir($p1)) {
        mkdirs(dirname($p1), $p2);
        return @mkdir($p1, $p2);
    }
    return true;
}

// save_file *** *** www.LazyCMS.net *** ***
function save_file($p1,$p2='',$p3=true){
    if (file_exists($p1)) {
        if (!is_writable($p1)) {
            // 设置ftp信息，则修改权限，反之则输出错误
            echo_json('没有可写权限<br/>文件：'.replace_root($p1),0);
        }
    }
    if (!$fp = fopen($p1,($p3?'wb':'ab'))) {
        trigger_error(L('error/createfile',array('file'=>$p1),'system'));
    }
    flock($fp,LOCK_EX + LOCK_NB);
    if (!fwrite($fp,$p2)) {
        trigger_error(L('error/writefile',array('file'=>$p1),'system'));
    }
    fclose($fp);
}

// print_x *** *** www.LazyCMS.net *** ***
function print_x($p1,$p2=null,$p3=null){
    // $p1:title
    // $p2:content
    // $p3:select tab   0 null 自动识别
    G('TITLE',$p1); $p3 = !empty($p3) ? $p3.'|' : null;
    print_v(menu($p3.G('TABS')).'<div id="box">'.$p2.'</div>');
}

// get_dir_array *** *** www.LazyCMS.net *** ***
function get_dir_array($p1,$p2){
    //$p1:路径 $p2:读取类型
    $p1 = str_replace(array('.','[',']'),array(SEPARATOR,'*','*'),$p1);
    if (substr($p1,0,1)=='@') {
        $p1 = str_replace('@',COM_PATH,$p1);
    } else {
        $p1 = LAZY_PATH.SEPARATOR.$p1;
    }
    $p3 = create_function('&$p1,$p2','$p1=substr($p1,strrpos($p1,"/")+1);');
    if (substr($p1,-1)!='/') { $p1 .= '/'; }
    $R = ($p2=='dir') ? glob("{$p1}*",GLOB_ONLYDIR) : glob("{$p1}*.{{$p2}}",GLOB_BRACE);
    array_walk($R,$p3);
    return $R;
}

// len *** *** www.LazyCMS.net *** ***
function len($p1){
    if (function_exists('mb_strlen')) {
        return mb_strlen($p1,'utf-8');
    } elseif (function_exists('iconv_strlen')){
        return iconv_strlen($p1,'utf-8');
    } else {
        preg_match_all(C('CN_PATTERN'),$p1,$R1);
        return count($R1[0]);
    }
}

// xmlencode *** *** www.LazyCMS.net *** ***
function xmlencode($p1){
    if (strlen($p1)==0) { return ; }
    return str_replace(array('&',"'",'"','>','<'),array('&amp;','&apos;','&quot;','&gt;','&lt;'),$p1);
}

// cnsubstr *** *** www.LazyCMS.net *** ***
function cnsubstr($p1,$p2){
    $p3 = 0;
    $p4 = $p2;
    if (func_num_args() >= 3) {
        $p3 = $p2;
        $p4 = func_get_arg(2);
    }
    if (function_exists('mb_substr')) {
        return mb_substr($p1,$p3,$p4,'utf-8');
    } elseif (function_exists('iconv_substr')){
        return iconv_substr($p1,$p3,$p4,'utf-8');
    } else {
        preg_match_all(C('CN_PATTERN'),$p1,$R1);
        if (count($R1[0]) - $p3 > $p4) {
            return implode('',array_slice($R1[0],$p3,$p4));
        }
        return implode('',array_slice($R1[0],$p3,$p4));
    }
}

// read_file *** *** www.LazyCMS.net *** ***
function read_file($p1){
    if (!is_file($p1)) { return ; }
    $fp   = fopen($p1,'rb');
    $size = filesize($p1);
    if ((int)$size==0) { return ; }
    $R = fread($fp,$size);
    fclose($fp);
    return $R;
}

// require_file *** *** www.LazyCMS.net *** ***
function require_file($p1){
    // $p1:filePath
    static $R = array();
    if (is_file($p1)) {
        if (!isset($R[$p1])) {
            require $p1;
            $R[$p1] = true;
            return true;
        }
        return false;
    }
    return false;
}

// include_file *** *** www.LazyCMS.net *** ***
function include_file($p1){
    // $p1:filePath
    static $R = array();
    if (is_file($p1)) {
        if (!isset($R[$p1])) {
            $R[$p1] = include $p1;
        }
        return $R[$p1];
    }
    return false;
}

// print_v *** *** www.LazyCMS.net *** ***
function print_v($p1=null){
    $title = G('TITLE');
    $hl = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
    $hl.= '<html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
    $hl.= $title ? '<title>'.G('TITLE').'</title>': null;
    $hl.= '<link href="../system/images/main.css" rel="stylesheet" type="text/css" />';
    $hl.= '<script type="text/javascript" src="../../common/js/jquery.js?ver=r1.2.6"></script>';
    $hl.= '<script type="text/javascript" src="../../common/js/jquery.lazycms.js?ver=1.0"></script>';
    $hl.= '<script type="text/javascript">parent.document.title = "'.G('TITLE').' - '.L('system/@title','system').'";'.G('SCRIPT');
    $hl.= '$(document).ready(function(){ ';
    // 批量去除连接虚线
    $hl.= '$("a").focus(function(){ this.blur(); });';
    // 执行函数
    $hl.= 'SemiMemory(); autoTitle();';
    $hl.= '$("#box").tips("tip","[@tip]");';
    $hl.= ' });</script>';
    $hl.= G('HEAD');
    $hl.= '</head><body>'.$p1.'</body></html>'; echo $hl;
}

// redirect *** *** www.LazyCMS.net *** ***
function redirect($p1){
    header("Content-Type:text/html; charset=utf-8");
    $p1 = str_replace(array("\n", "\r"), '', $p1);
    $js = '<script type="text/javascript" charset="utf-8">parent.location.href="'.$p1.'";</script>';
    exit('<meta http-equiv="refresh" content="0;url='.$p1.'" />'.$js);
}

// pinyin *** *** www.LazyCMS.net *** ***
function pinyin($p1){
    static $R2 = null; $R = null;
    preg_match_all(G('CN_PATTERN'),trim($p1),$R1);
    $p2 = $R1[0]; $p3 = count($p2);
    if (empty($R2)) {
        $R2 = include_file(COM_PATH.'/data/pinyin.php');
    }
    for ($i=0;$i<$p3;$i++) {
        if (validate($p2[$i],'^\w+$')){
            $R.= $p2[$i];
        } elseif (!array_search_value($p2[$i],$R2)) {
            $R.= '-';
        } else {
            $R.= ucfirst(array_search_value($p2[$i],$R2));
        }
    }
    return trim($R,'-');
}

// echo_json *** *** www.LazyCMS.net *** ***
function echo_json($p1,$p2=1){
    if (!is_array($p1)) { 
        $R['text'] = $p1;
    } else {
        $R = $p1;
    }
    switch ((int)$p2){
        case 0 : $R['status'] = 'error'; break;
        case 1 : $R['status'] = 'success'; break;
        case 2 : $R['status'] = 'tips'; break;
    }
    exit(json_encode($R));
}

// form_opts *** *** www.LazyCMS.net *** ***
function form_opts($p1,$p2,$p3,$p4=null){
    $R = null;
    $R1 = get_dir_array($p1,$p2);
    if ($p2=='xml') {
        foreach ($R1 as &$v) {
            if ($n = strpos($v,'.')) { $v = substr($v,$n+1); }
        }
        $R1 = array_unique($R1);
    }
    if (strpos($p3,'%23')!==false) { $p3 = str_replace('%23','#',$p3); }
    foreach ($R1 as $p5) {
        if ($p2=='xml') {
            $p5 = basename($p5,".xml");
            if ($n = strpos($p5,'.')) {
                $p5 = substr($p5,$n+1);
            }
            $p6 = langbox($p5);
        } else{
            $p6 = $p5;
        }
        $R2 = $p3;
        if (strpos($R2,'#value#')!==false) { $R2 = str_replace('#value#',$p5,$R2); }
        if (strpos($R2,'#name#')!==false)  { $R2 = str_replace('#name#',$p6,$R2); }
        if ($p4==$p5) {
            $R2 = str_replace('#selected#',' selected="selected"',$R2);
        } else{
            $R2 = str_replace('#selected#','',$R2);
        }
        $R.= $R2;
    }
    return $R;
}

// menu *** *** www.LazyCMS.net *** ***
function menu($p1){
    if (($p3 = strpos($p1,'|'))!==false) {
        $p2 = substr($p1,0,$p3);
        $p1 = substr($p1,$p3+1);
    }
    $p3 = basename(PHP_FILE);
    $p4 = isset($_REQUEST['action']) ? strtolower($_REQUEST['action']) : null;
    $p5 = ' class="active"'; $p6 = 'javascript:self.location.reload();';
    $R = '<ul id="tabs">';
    $R1 = explode(';',$p1);
    foreach ($R1 as $k=>$v) {
        if (strpos($v,':')!==false) {
            $R2 = explode(':',$v); $active = null;
            if (!empty($p2)) {
                if (($k+1)==$p2) {
                    $active = $p5; $R2[1] = $p6;
                }
            } elseif (!empty($p4)){
                if ((string)$R2[1]==(string)$p3.'?action='.$p4) {
                    $active = $p5; $R2[1] = $p6;
                }
            } else {
                if ((string)$R2[1]==(string)$p3) {
                    $active = $p5; $R2[1] = $p6;
                }
            }
            $R.= '<li'.$active.'><a href="'.$R2[1].'">'.$R2[0].'</a></li>';
        } else {
            $R.= '<li class="active"><a href="javascript:self.location.reload();">'.$v.'</a></li>';
        }
    }
    $R.= '</ul>';
    return $R;
}

// sect *** *** www.LazyCMS.net *** ***
function sect($p1,$p2,$p3,$p4=null){
    if (empty($p1) || empty($p2) || empty($p3)) { return ;}
    if (substr($p2,0,1)==chr(40) && substr($p2,-1)==chr(41) && substr($p3,0,1)==chr(40) && substr($p3,-1)==chr(41)) {
        if (preg_match("/{$p2}(.*){$p3}/isU",$p1,$R2)) {
            if (count($R2)>0) {
                $p5 = $R2[0];
            }
        }
        if (preg_match("/{$p2}/isU",$p5,$R2)) {
            if (count($R2)>0) {
                $p6 = $R2[0];
            }
        }
        if (preg_match("/{$p3}/isU",$p5,$R2)) {
            if (count($R2)>0) {
                $p7 = $R2[0];
            }
        }
    } else {
        $p5 = $p1; $p6 = $p2; $p7 = $p3;
    }
    $p8 = strpos(strtolower($p5),strtolower($p6)); if ($p8===false) { return ; }
    $p9 = strpos(strtolower(substr($p5,-(strlen($p5)-$p8-strlen($p6)))),strtolower($p7));
    $R3 = null;
    if ($p8!==false && $p9!==false) {
        $R3 = trim(substr($p5,$p8+strlen($p6),$p9));
    }
    if (strlen($R3)>0 && strlen($p4)>0) {
        $R3 = clsre($R3,$p4);
    }
    return $R3;
}

// clsre *** *** www.LazyCMS.net *** ***
function clsre($p1,$p2){
    $R1 = $p1;
    $R2 = explode("\n",$p2);
    foreach ($R2 as $p3) {
        $p4 = trim($p3);
        if (!empty($p4)) {
            if (trim(substr($p4,0,1))==chr(40) && trim(substr($p4,-1))==chr(41)) {
                $R1 = preg_replace("/{$p4}/isU",'',$R1);
            } else {
                if (strpos($R1,$p3)!==false) { $R1 = str_replace($p3,'',$R1); }
            }
        }
    }
    return $R1;
}

// cls *** *** www.LazyCMS.net *** ***
function cls($p1){
    if (strlen($p1)==0) { return ;} { $R = $p1; }
    $R = str_replace(array(chr(9),chr(10),chr(13)),'',$R);
    while (strpos($R,chr(32).chr(32))!==false || strpos($R,'&nbsp;')!==false) {
        $R = str_replace(chr(32).chr(32),chr(32),str_replace('&nbsp;',chr(32),$R));
    }
    return $R;
}

// snapImg *** *** www.LazyCMS.net *** ***
function snapImg($p1){
    $R = $p1;
    if (preg_match_all('/<img[^>]*src=("([^"]+)"|\'([^\']+)\')[^>]*>/isU',$R,$imgs)) {
        $imgs[1] = array_unique($imgs[1]);
        foreach ($imgs[1] as $img) {
            $img = trim($img,'"\'');
            if ($downImg = downImg($img)) {
                if (validate($img,5)) {
                    $R = str_replace($img,SITE_BASE.ltrim($downImg,'/'),$R);
                }
            }
        }
    }
    return $R;
}

// downImg *** *** www.LazyCMS.net *** ***
function downImg($p1,$p2=null){
    static $d = null;
    if (validate($p1,5)) {
        if (!is_object($d)) {
            import("system.downloader");
            $d = new DownLoader();
            $d->timeout = 100;
        }
        $d->connect($p1,'GET',$d->timeout)->send();
        if ($d->status() == 200) {
            if (empty($p2)) {
                $imgInfo = pathinfo($p1);
                $imgPath = C('UPLOAD_IMAGE_PATH').date('/Y/m/d/',now());
                $imgPath = str_replace('/',SEPARATOR,$imgPath);
                if (isset($imgInfo['extension']) && isset($imgInfo['filename'])) {
                    $fileName = $imgInfo['filename'].'.'.$imgInfo['extension'];
                    if (is_file(LAZY_PATH.SEPARATOR.$imgPath.$fileName)) {
                        $fileName = str_replace('.','',microtime(true)).'.'.$imgInfo['extension'];
                    }
                } else {
                    if(preg_match("/Content-Type\:(.+)\r\n/i",$d->header(),$imgInfo)){
                        $fileName = str_replace('.','',microtime(true)).'.'.substr($imgInfo[1],strrpos($imgInfo[1],'/')+1);
                    }
                }
            } else {
                $imgInfo  = pathinfo($p1);
                $imgPath  = $imgInfo['dirname'];
                $fileName = $imgInfo['basename'];
            }
            mkdirs(LAZY_PATH.SEPARATOR.$imgPath);
            save_file(LAZY_PATH.SEPARATOR.$imgPath.$fileName,$d->body());
            return str_replace(SEPARATOR,'/',$imgPath).$fileName;
        } else {
            return $p1;
        }
    }
    return $p1;
}

// validate *** *** www.LazyCMS.net *** ***
function validate($p1,$p2){
    // $p1:str, $p2:类型
    switch((string)$p2){
        case '0' : // 数字，字母，逗号，杠，下划线，[，]
            $p3 = '^[\w\,\/\-\[\]]+$';
            break;
        case '1' : // 字母
            $p3 = '^[A-Za-z]+$';
            break;
        case '2' : // 匹配数字
            $p3 = '^\d+$';
            break;
        case '3' : // 字母，数字，下划线，杠
            $p3 = '^[\w\-]+$';
            break;
        case '4' : // Email
            $p3 = '^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$';
            break;
        case '5' : // url
            $p3 = '^(http|https|ftp):(\/\/|\\\\)(([\w\/\\\+\-~`@:%])+\.)+([\w\/\\\.\=\?\+\-~`@\':!%#]|(&amp;)|&)+';
            break;
        case '6' : // 
            $p3 = '^[\d\,\.]+$';
            break;
        default  : // 自定义正则
            $p3 = $p2;
            break;
    }
    return preg_match("/{$p3}/i",$p1);
}

// pagelist *** *** www.LazyCMS.net *** ***
function pagelist($p1,$p2,$p3,$p4){
    //url,page,总页数,记录总数
    $R = null;
    if (strpos($p1,'%24')!==false) { $p1 = str_replace('%24','$',$p1); }
    if (strpos($p1,'$')==0 || $p4==0) { return ; }
    if ($p2 > 2) {
        $R.= '<a href="'.str_replace('$',$p2-1,$p1).'">&laquo;</a>';
    } elseif ($p2==2) {
        $R.= '<a href="'.str_replace('$',1,$p1).'">&laquo;</a>';
    }
    if ($p2 > 3) {
        $R.= '<a href="'.str_replace('$',1,$p1).'">1</a><span>&#8230;</span>';
    }
    $p5 = $p2-2;
    $p6 = $p2+7;
    for ($i=$p5; $i<=$p6; $i++) {
        if ($i>=1 && $i<=$p3) {
            if ((int)$i==(int)$p2) {
                $R.= '<span class="active">'.$i.'</span>';
            } else {
                if ($i==1) {
                    $R.= '<a href="'.str_replace('$',1,$p1).'">'.$i.'</a>';
                } else {
                    $R.= '<a href="'.str_replace('$',$i,$p1).'">'.$i.'</a>';
                }
            }
        }
    }
    if ($p2 < ($p3-7)) {
        $R.= '<span>&#8230;</span><a href="'.str_replace('$',$p3,$p1).'">'.$p3.'</a>';
    }
    if ($p2 < $p3) {
        $R.= '<a href="'.str_replace('$',$p2+1,$p1).'">&raquo;</a>';
    }
    return '<div class="pages">'.$R.'</div>';
}

// import *** *** www.LazyCMS.net *** ***
function import($p1,$p2='',$p3='.php',$p4=false){
    // $p1:path, $p2:baseUrl, $p3:ext, $p4:subDir
    static $_I1 = array();
    // 已经加载的文件不再加载
    if (isset($_I1[strtolower($p1.$p2)])) {
        return true;
    } else {
        $_I1[strtolower($p1.$p2)] = true;
    }
    if (empty($p2)) {
        // 默认方式调用应用类库
        $p2 = COM_PATH;
    } else {
        // 相对路径调用
        $p5 = true;
    }
    $R1 = explode('.',$p1);
    if ('*' == $R1[0] || isset($p5)) {
        /**
         * 多级目录加载支持
         * 用于子目录递归调用
         */
    } elseif('@' == $R1[0]) {
        // 加载根目录目录的文件
        $p1 = str_replace('@.','',$p1);
        $p2 = LAZY_PATH;
    }
    if (substr($p2, -1) != '/') { $p2 .= '/'; }
    $p6 = $p2.str_replace('.', '/', $p1).$p3;
    if (false !== strpos($p6,'*') || false !== strpos($p6,'?')) {
        // 导入匹配的文件
        $p7 = glob($p6);
        if ($p7) {
            foreach($p7 as $k=>$v) {
                if(is_dir($v)) {
                    if($p4) { import('*',$v.'/',$p3,$p4); }
                } else {
                    // 导入类库文件
                    $R = require_file($v);
                }
            }
            return $R;
        } else {
            return false;
        }
    } else {
        // 导入目录下的指定类库文件
        return require_file($p6);
    }
}
// editor *** *** www.LazyCMS.net *** ***
function editor($p1,$p2=array()){
    $A1 = array(); $p3 = null;
    if (is_array($p2) && !empty($p2)) {
        $A1 = $p2;
        $p2 = isset($A1['value']) ? $A1['value'] : null;
        if (isset($A1['editor'])) {
            $p3 = $A1['editor'];
        }
    }
    switch (strtolower((string)$p3)) {
        case 'fckeditor': 
            import('system.fckeditor');
            $A1['upimg'] = isset($A1['upimg']) ? $A1['upimg'] : false;
            $A1['upfile'] = isset($A1['upfile']) ? $A1['upfile'] : false;
            $A1['pagebreak'] = isset($A1['pagebreak']) ? $A1['pagebreak'] : false;
            $A1['snapimg'] = isset($A1['snapimg']) ? $A1['snapimg'] : array(0,0);
            $A1['dellink'] = isset($A1['dellink']) ? $A1['dellink'] : array(0,0);
            $A1['setimg'] = isset($A1['setimg']) ? $A1['setimg'] : array(0,0);
            $A1['resize'] = isset($A1['resize']) ? $A1['resize'] : false;
            $css = '<style type="text/css">';
            $css.= '.'.$p1.'_fckeditor_button{width:'.$A1['width'].'; margin-top:3px;}';
            $css.= '.'.$p1.'_fckeditor_button .fr a{margin-left:8px;}';
            $css.= '</style>';
            $div = $css; $but = $size = null;
            if ($A1['upimg']) { $but.= '<button type="button">'.L('fckeditor/upimg','system').'</button>'; }
            if ($A1['upfile']) { $but.= '<button type="button">'.L('fckeditor/upfile','system').'</button>'; }
            if ($A1['pagebreak']) { $but.= '<button type="button">'.L('fckeditor/pagebreak','system').'</button>'; }
            if ($A1['snapimg'][0]) {
                $but.= '<input type="checkbox" name="'.$p1.'_attr[snapimg]" id="'.$p1.'_attr[snapimg]" value="1"'.($A1['snapimg'][1]?' checked="checked"':null).' cookie="true" /><label for="'.$p1.'_attr[snapimg]">'.L('fckeditor/snapimg','system').'</label>&nbsp; ';
            }
            if ($A1['dellink'][0]) {
                $but.= '<input type="checkbox" name="'.$p1.'_attr[dellink]" id="'.$p1.'_attr[dellink]" value="1"'.($A1['dellink'][1]?' checked="checked"':null).' cookie="true" /><label for="'.$p1.'_attr[dellink]">'.L('fckeditor/dellink','system').'</label>&nbsp; ';
            }
            if ($A1['setimg'][0]) {
                $but.= '<input type="checkbox" name="'.$p1.'_attr[setimg]" id="'.$p1.'_attr[setimg]" value="1"'.($A1['setimg'][1]?' checked="checked"':null).' cookie="true" /><label for="'.$p1.'_attr[setimg]">'.L('fckeditor/setimg','system').'</label>';
            }
            
            // 是否显示调整编辑器高度按钮
            if ($A1['resize']) {
                $size.= '<div class="fr">';
                $size.= '<a href="javascript:;" onclick="$(\'#'.$p1.'\').editor().resize(\'+\',100);"><img src="'.SITE_BASE.'common/images/icon/add.png" /></a>';
                $size.= '<a href="javascript:;" onclick="$(\'#'.$p1.'\').editor().resize(\'-\',100);"><img src="'.SITE_BASE.'common/images/icon/reduce.png" /></a>';
                $size.= '</div>';    
            }
            if (!empty($but) || !empty($size)) {
                $div.= '<div class="'.$p1.'_fckeditor_button">';
                $div.= '<div class="fl">';
                $div.= $but;
                $div.= '</div>';
                $div.= $size;
                $div.= '</div>';
            }
            $FCK = new FCKeditor($p1);
            $FCK->BasePath = SITE_BASE.'common/editor/fckeditor/';
            if (isset($A1['toolbar'])) {
                $FCK->ToolbarSet = $A1['toolbar'];
            }
            if (isset($A1['width'])) {
                $FCK->Width = $A1['width'];
            }
            if (isset($A1['height'])) {
                $FCK->Height = $A1['height'];
            }
            if (isset($A1['config'])) {
                $FCK->Config = $A1['config'];
            }
            $FCK->Value = $p2;
            if (empty($A1['print'])) {
                return $FCK->CreateHtml().$div;
            } else {
                $FCK->Create();
                echo $but;
            }
            unset($FCK);
            break;
        default:
            break;
    }
    unset($A1);
}
// check_user *** *** www.LazyCMS.net *** ***
function check_user($p1=null,$p2=null){
    return get_user($p1,$p2);
}

// check_login *** *** www.LazyCMS.net *** ***
function check_login($p1=null,$p2='../logout.php'){
    $_USER = get_user($p1);
    if ($_USER === -2) {
        // 没有权限，进行错误提示
    } elseif ($_USER === false) {
        // 登录超时，跳转
        if ($_SERVER['REQUEST_METHOD']=='POST'){
            // ajax方式输出错误
            echo_json(array(
                'text'  => L('error/overtime','system'),
                'sleep' => 3,
                'url'   => $p2,
            ),0);
        } else {
            redirect($p2);
        }
    }
    return $_USER;
}

// get_user *** *** www.LazyCMS.net *** ***
function get_user($p1=null,$p2=null){
    /**
     * 验证登录或权限验证
     *
     * get_user(); // 取得已登录用户的资料
     * get_user('purview'); // 用户权限验证
     * get_user('username','userpass'); // 验证登录
     */
    $db = get_conn(); 
    $funcNum = func_num_args();
    // check_user('username','userpass')
    if ((int)$funcNum > 1 && !empty($p2)) {
        $username = $p1;
        $userpass = $p2;
        $purview  = null;
    } else {
        $username = Cookie::get('username');
        $userpass = Cookie::get('userpass');
        $purview  = ($p1=='system') ? 'system/'.$p1 : MODULE.'/'.$p1;
    }
    if (empty($username) || empty($userpass)) { return false; }
    // 开始验证
    $res = $db->query("SELECT `su`.*,`sg`.*
                        FROM `#@_system_users` AS `su`
                        LEFT JOIN `#@_system_group` AS `sg` ON `su`.`groupid` = `sg`.`groupid`
                        WHERE `su`.`username`=? AND `su`.`isdel`=0 AND `su`.`islock`=0
                        LIMIT 0,1;",$username);
    if ($rs = $db->fetch($res)) {
        if ((int)$funcNum > 1 && !empty($p2)) {
            $md5pass = md5($userpass.$rs['userkey']);
            if ($md5pass == $rs['userpass']) {
                $newkey  = substr($md5pass,0,6);
                $newpass = md5($userpass.$newkey);
                // 更新数据
                $db->update('#@_system_users',array(
                    'userpass' => $newpass,
                    'userkey'  => $newkey,
                ),DB::quoteInto('`username` = ?',$username));
                // 合并新密码和key
                $rs = array_merge($rs,array(
                    'userpass' => $newpass,
                    'userkey'  => $newkey,
                ));
                return $rs;
            } else {
                // 密码错误，返回-1
                return -1;
            }
        } else {
            if ((string)$userpass == (string)$rs['userpass']) {
                if (!empty($purview)) {
                    if (instr($rs['purview'],$purview)) {
                        return $rs;
                    } else {
                        // 没有权限返回 -1
                        return -2;
                    }
                } else {
                    return $rs;
                }
            }    
        }
    }
    return false;
}

// lazycms_error *** *** www.LazyCMS.net *** ***
function lazycms_error($errno, $errstr, $errfile, $errline){
    if (!in_array($errno,array(E_PARSE,E_USER_ERROR,E_USER_WARNING,E_USER_NOTICE,E_ALL))) { return ; }
    $errfile = replace_root($errfile); $errstr = replace_root($errstr);
    $trace = debug_backtrace(); unset($trace[0]);
    $error = array(); $traceInfo = null;
    $error['file']    = $errfile;
    $error['line']    = $errline;
    $error['message'] = $errstr;
    $time = date("y-m-d H:i:s");
    foreach($trace as $t) {
        $file  = isset($t['file']) ? replace_root($t['file']) : null;
        $line  = isset($t['line']) ? $t['line'] : null;
        $class = isset($t['class']) ? $t['class'] : null;
        $type  = isset($t['type']) ? $t['type'] : null;
        $args  = isset($t['args']) ? $t['args'] : null;
        $function  = isset($t['function']) ? $t['function'] : null;
        $traceInfo.= '['.$time.'] '.$file.' ('.$line.') ';
        $traceInfo.= $class.$type.$function.'(';
        if (is_array($args)) {
            $arrs = array();
            foreach ($args as $v) {
                if (is_object($v)) {
                    $arrs[] = implode(' ',get_object_vars($v));
                } else {
                    $arrs[] = h2encode($v);
                }
            }
            $traceInfo.= implode(', ',$arrs);
        }
        $traceInfo.=")\n";
    }
    $error['trace'] = replace_root($traceInfo);
    $hl = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
    $hl.= '<html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
    $hl.= '<title>'.L('error/title','system').'</title><style type="text/css">';
    $hl.= 'body{ font-family: Verdana; font-size:14px; }';
    $hl.= 'a{text-decoration:none;color:#174B73;}';
    $hl.= 'a:hover{ text-decoration:none;color:#FF6600;}';
    $hl.= '.red{ color:red; }';
    $hl.= '.notice{ padding:10px; margin:5px; color:#666; background:#FCFCFC; border:1px solid #E0E0E0;}';
    $hl.= '.notice h2{ border-bottom:1px solid #DDD; font-size:25px; margin-top:0; padding:8px 0;}';
    $hl.= '.title{ margin:4px 0; color:#F60; font-weight:bold;}';
    $hl.= '.message, .trace{ padding:1em; border:solid 1px #000; margin:10px 0; background:#FFD; line-height:150%;}';
    $hl.= '.message{ background:#FFD; color:#2E2E2E; border:1px solid #E0E0E0; }';
    $hl.= '.trace{ background:#E7F7FF; border:1px solid #E0E0E0; color:#535353;}';
    $hl.= '#footer{ color:#FF3300; margin:5pt auto; font-weight:bold; text-align:center;}';
    $hl.= '#footer sup{color:gray;font-size:9pt;}';
    $hl.= '#footer span{color:silver;}';
    $hl.= '</style></head><body>';
    $hl.= '<div class="notice"><h2>'.L('error/title','system').'</h2>';
    $hl.= '<div>You can choose to [ <a href="javascript:self.location.reload();">'.L('common/tryagain','system').'</a> ] [ <a href="javascript:history.back();">'.L('common/back','system').'</a> ] or [ <a href="'.SITE_BASE.'">'.L('common/backhome','system').'</a> ]</div>';
    $hl.= '<p><strong>'.L('error/position','system').':</strong>　FILE: <strong class="red">'.$error['file'].'</strong>　LINE: <strong class="red">'.$error['line'].'</strong></p>';
    $hl.= '<p class="title">[ '.L('error/errinfo','system').' ]</p>';
    $hl.= '<p class="message">'.$error['message'].'</p>';
    $hl.= '<p class="title">[ TRACE ]</p>';
    $hl.= '<p class="trace">'.nl2br($error['trace']).'</p></div>';
    $hl.= '<div id="footer">LazyCMS <sup>'.LAZY_VERSION.'</sup></div>';
    $hl.= '</body></html>';
    ob_end_clean();
    exit($hl);
}

// ubbencode *** *** www.LazyCMS.net *** ***
function ubbencode($p1){
    if (strlen($p1)==0) {return ;}
    $R = h2encode($p1);
    if (strpos($R,' ')!==false) { $R = str_replace(' ','&nbsp;',$R); }
    $R = preg_replace(array(
        '/\r\n|\n|\r/s',
        '/\[url\](.+?)\[\/url]/i',
        '/\[url\=([^\]]+)](.+?)\[\/url]/i',
        '/\[img\](.+?)\[\/img]/i',
        '/\[b\](.+?)\[\/b]/i',
        '/\[strong\](.+?)\[\/strong]/i',
        '/\[i\](.+?)\[\/i]/i',
        '/\[u\](.+?)\[\/u]/i',
        '/\[s\](.+?)\[\/s]/i',
        '/\[sub\](.+?)\[\/sub]/i',
        '/\[sup\](.+?)\[\/sup]/i',
        '/\[color\=([^\]]+)](.+?)\[\/color]/i',
        '/\[bgcolor\=([^\]]+)](.+?)\[\/bgcolor]/i',
        '/\[font\=([^\]]+)](.+?)\[\/font]/i',
        '/\[size\=([^\]]+)](.+?)\[\/size]/i',
        '/\[align\=([^\]]+)](.+?)\[\/align]/i',
        '/\[p\](.+?)\[\/p]/i',
        '/\[div\](.+?)\[\/div]/i',
        '/\[pre\](.+?)\[\/pre]/i',
        '/\[address\](.+?)\[\/address]/i',
    ),array(
        '<br/>',
        '<a href="$1">$1</a>',
        '<a href="$1">$2</a>',
        '<img src="$1" />',
        '<b>$1</b>',
        '<strong>$1</strong>',
        '<i>$1</i>',
        '<u>$1</u>',
        '<strike>$1</strike>',
        '<sub>$1</sub>',
        '<sup>$1</sup>',
        '<span style="color: $1">$2</span>',
        '<span style="background-color: $1">$2</span>',
        '<span style="font-family: $1">$2</span>',
        '<span style="font-size: $1">$2</span>',
        '<div style="text-align: $1">$2</div>',
        '<p>$1</p>',
        '<div>$1</div>',
        '<pre>$1</pre>',
        '<address>$1</address>',
    ),$R);
    for ($i=1; $i<7; $i++) {
        $R = preg_replace('/\[h'.$i.'\](.+?)\[\/h'.$i.']/i','<h'.$i.'>$1</h'.$i.'>',$R);
    }
    return $R;
}

// langbox *** *** www.LazyCMS.net *** ***
function langbox($p1){
    $lang = array(
        'ar' => 'Arabic',
        'bg' => 'Bulgarian',
        'bs' => 'Bosnian',
        'ca' => 'Catalan',
        'cs' => 'Czech',
        'da' => 'Danish',
        'de' => 'German',
        'el' => 'Greek',
        'en' => 'English',
        'en-au' => 'English (Australia)',
        'en-uk' => 'English (United Kingdom)',
        'eo' => 'Esperanto',
        'es' => 'Spanish',
        'et' => 'Estonian',
        'eu' => 'Basque',
        'fa' => 'Persian',
        'fi' => 'Finnish',
        'fr' => 'French',
        'gl' => 'Galician',
        'he' => 'Hebrew',
        'hr' => 'Croatian',
        'hu' => 'Hungarian',
        'it' => 'Italian',
        'ko' => 'Corea',
        'lt' => 'Lithuanian',
        'nl' => 'Dutch',
        'no' => 'Norwegian',
        'pl' => 'Polish',
        'pt' => 'Portuguese (Portugal)',
        'pt-br' => 'Portuguese (Brazil)',
        'ro' => 'Romanian',
        'ru' => 'Russian',
        'sk' => 'Slovak',
        'sl' => 'Slovenian',
        'sr' => 'Serbian (Cyrillic)',
        'sr-latn' => 'Serbian (Latin)',
        'sv' => 'Swedish',
        'th' => 'Thai',
        'tr' => 'Turkish',
        'uk' => 'Ukrainian',
        'zh' => '繁體中文',
        'zh-cn' => '简体中文',
        'ja' => 'Japanese',
    );
    return isset($lang[$p1]) ? $lang[$p1] : $p1;
}

// C *** *** www.LazyCMS.net *** ***
function C($p1=null,$p2=null) {
    // $p1:name, $p2:value
    static $R = array();
    if(!is_null($p2)) {
        $R[strtolower($p1)] = $p2;
        return ;
    }
    if(empty($p1)) {
        return $R;
    }
    if(is_array($p1)) {
        $R = array_merge($R,array_change_key_case($p1));
        return $R;
    }
    if(isset($R[strtolower($p1)])) {
        return $R[strtolower($p1)];
    }else{
        return false;
    }
}

// G *** *** www.LazyCMS.net *** ***
function G($p1=null,$p2=null){
    static $R = array();
    if(!is_null($p2)) {
        $R[strtolower($p1)] = $p2;
        return ;
    }
    if(empty($p1)) {
        return $R;
    }
    if(is_array($p1)) {
        $R = array_merge($R,array_change_key_case($p1));
        return $R;
    }
    if(isset($R[strtolower($p1)])) {
        return $R[strtolower($p1)];
    }else{
        return false;
    }
}

// L *** *** www.LazyCMS.net *** ***
function L($p1,$p2=null,$p3=null){
    static $R1 = array();
    if ($p1=='') { return $p1; }
    $R2 = G('MODULE');
    if (!is_array($p2) && !empty($p2)) { $p3 = $p2; }
    $p3 = empty($p3) ? ($R2 ? $R2 : MODULE) :$p3;
    $p4 = language();
    $R2 = isset($R1["{$p3}.{$p4}"]) ? $R1["{$p3}.{$p4}"] : null;
    if (!is_object($R2)) {
        $p5 = COM_PATH."/language/{$p3}."; $p6 = $p5.$p4.'.xml';
        if (!is_file($p6)) { $p6 = $p5.C('LANGUAGE').'.xml'; }
        if (!is_file($p6)) { return '['.$p1.']'; }
        $R2 = new DOMDocument;
        $R2->load($p6);
        $R1[$p4] = $R2;
    }
    $R3 = new DOMXPath($R2);
    $R4 = $R3->evaluate("/lazycms/$p1");
    if (false !== strpos($p1,'/@')) {
        $R = $R4->item(0)->value;
    } else {
        $R = $R4->item(0)->nodeValue;
    }
    if (!empty($p2) && is_array($p2)) {
        foreach ($p2 as $k=>$v) {
           $R = str_replace('{$'.$k.'}',$v,$R); 
        }
    }
    return $R ? $R : '['.$p1.']';
}

// property_exists *** *** www.LazyCMS.net *** ***
if (!function_exists('property_exists')) {
    function property_exists($p1, $p2) { // $p1:class, $p2:property
        if (is_object($p1)) { $p1 = get_class($p1); }
        return array_key_exists($p2,get_class_vars($p1));
    }
}

// json_encode *** *** www.LazyCMS.net *** ***
if (!function_exists('json_encode')) {
    function json_encode($p1){
        static $R = array();
        if (!isset($R[0])) {
            import('system.json');
            $R[0] = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
        }
        return $R[0]->encode($p1);
    }
}

// json_decode *** *** www.LazyCMS.net *** ***
if (!function_exists('json_decode')) {
    function json_decode($p1){
        static $R = array();
        if (!isset($R[0])) {
            import('system.json');
            $R[0] = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
        }
        return $R[0]->decode($p1);
    }
}

// 中文正则，请不要修改 *** *** www.LazyCMS.net *** ***
G('CN_PATTERN','/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/');