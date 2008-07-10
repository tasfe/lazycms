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
    $time = isset($_SERVER['REQUEST_TIME'])?$_SERVER['REQUEST_TIME']:time();
    return $time+(C('TIME_ZONE')*3600);
}

// stripslashes_deep *** *** www.LazyCMS.net *** ***
function stripslashes_deep($l1) {
    return is_array($l1) ? array_map('stripslashes_deep', $l1) : stripslashes($l1);
}

// replace_root *** *** www.LazyCMS.net *** ***
function replace_root($l1){
    return str_replace('\\','/',str_replace(LAZY_PATH.'\\',C('SITE_BASE'),$l1));
}

// t2js *** *** www.LazyCMS.net *** ***
function t2js($l1,$l2=false){
    $I1 = str_replace(array("\r", "\n"), array('', '\n'), addslashes($l1));
    return $l2 ? "document.writeln(\"$I1\");" : $I1;
}

// h2encode *** *** www.LazyCMS.net *** ***
function h2encode($l1){
    return htmlspecialchars($l1);
}

// h2decode *** *** www.LazyCMS.net *** ***
function h2decode($l1){
    return empty($l1) ? $l1 : htmlspecialchars_decode($l1);
}

// get_php_setting *** *** www.LazyCMS.net *** ***
function get_php_setting($val){
    $r = (ini_get($val) == '1' ? 1 : 0);return isok($r);
}

// fieldset *** *** www.LazyCMS.net *** ***
function fieldset($l1,$l2){
    return '<fieldset><legend>'.$l1.'</legend>'.$l2.'</fieldset>';
}

// instr *** *** www.LazyCMS.net *** ***
function instr($l1,$l2){
    if (strlen($l1)==0) { return false; }
    if (!is_array($l1)) { $l1 = explode(",",$l1); }
    return in_array($l2,$l1) ? true : false;
}

// isok *** *** www.LazyCMS.net *** ***
function isok($r){
    return $r ? '<strong style="color:#009900;">'.L('common/on').'</strong>' :
                    '<strong style="color:#FF0000;">'.L('common/off').'</strong>';
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
function array_search_value($l1,$l2){
    while (list($k,$v)=each($l2)) {
        if (strpos($v,$l1)!==false) {
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
    $I1 = isset($_GET['language'])?$_GET['language']:null;
    if (!$I1) { $I1 = Cookie::get('language'); }
    return $I1 ? $I1 : C('LANGUAGE');
}

// utf2ansi *** *** www.LazyCMS.net *** ***
function utf2ansi($str,$charset='GB2312'){
    if (function_exists('iconv')) {
        return iconv('UTF-8',"{$charset}//IGNORE",$str);
    } elseif (function_exists('mb_convert_encoding')){
        return mb_convert_encoding($str,$charset,'UTF-8');
    } else {
        return $str;
    }
}

// ansi2utf *** *** www.LazyCMS.net *** ***
function ansi2utf($str){
    if (is_utf8($str)) { return $str; }
    if (function_exists('iconv')) {
        return iconv('',"UTF-8//IGNORE",$str);
    } elseif (function_exists('mb_convert_encoding')){
        return mb_convert_encoding($str,'UTF-8','auto');
    } else {
        return $str;
    }
}

// is_utf8 *** *** www.LazyCMS.net *** ***
function is_utf8($l1){
    return preg_match('%^(?:
            [\x09\x0A\x0D\x20-\x7E] # ASCII
            | [\xC2-\xDF][\x80-\xBF] # non-overlong 2-byte
            | \xE0[\xA0-\xBF][\x80-\xBF] # excluding overlongs
            | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2} # straight 3-byte
            | \xED[\x80-\x9F][\x80-\xBF] # excluding surrogates
            | \xF0[\x90-\xBF][\x80-\xBF]{2} # planes 1-3
            | [\xF1-\xF3][\x80-\xBF]{3} # planes 4-15
            | \xF4[\x80-\x8F][\x80-\xBF]{2} # plane 16
            )*$%xs',$l1);
}

// but *** *** www.LazyCMS.net *** ***
function but($l1){
    $I1 = '<p class="button"><button type="submit" class="submit" onclick="return $(this.form).save();">'.L("common/{$l1}").'</button>';
    $I1.= '<button type="button" class="apply" onclick="return $(this.form).apply();">'.L("common/apply").'</button>';
    $I1.= '<button type="reset" onclick="javascript:return confirm(\''.L('confirm/reset').'\')">'.L('common/reset').'</button>';
    $I1.= '<button type="button" onclick="javascript:history.back();">'.L('common/back').'</button></p>';
    return $I1;
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

// save_file *** *** www.LazyCMS.net *** ***
function save_file($l1,$l2=''){
    if (file_exists($l1)) {
        if (!is_writable($l1)) {
            chmod($l1,0777);
        }
    }
    if (!$fp = fopen($l1,'wb')) {
        trigger_error(L('error/createfile',array('file'=>$l1)));
    }
    if (!fwrite($fp,$l2)) {
        trigger_error(L('error/writefile',array('file'=>$l1)));
    };
    fclose($fp);
}

// print_x *** *** www.LazyCMS.net *** ***
function print_x($l1,$l2=null,$l3=null){
    // $l1:title
    // $l2:content
    // $l3:select tab   0 null 自动识别
    G('TITLE',$l1); $l3 = !empty($l3) ? $l3.'|' : null;
    print_v(menu($l3.G('TABS')).'<div id="box">'.$l2.'</div>');
}

// get_dir_array *** *** www.LazyCMS.net *** ***
function get_dir_array($l1,$l2){
    //$l1:路径 $l2:读取类型
    if (strpos($l1,'.')!==false) { $l1 = str_replace('.','/',$l1); }
    if (strpos($l1,'@')!==false) { $l1 = str_replace('@',COM_PATH,$l1); }
    if (strpos($l1,'[')!==false) { $l1 = str_replace('[','*',$l1); }
    if (strpos($l1,']')!==false) { $l1 = str_replace('[','*',$l1); }
    $l3 = create_function('&$l1,$l2','$I2=strrpos($l1,"/"); $I1=substr($l1,$I2+1); $l1=$I1;');
    if (substr($l1,-1)!='/') { $l1 .= '/'; }
    $I1 = ($l2=='dir') ? glob("{$l1}*",GLOB_ONLYDIR) : glob("{$l1}*.{$l2}",GLOB_BRACE);
    array_walk($I1,$l3);
    return $I1;
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

// load_file *** *** www.LazyCMS.net *** ***
function load_file($l1){
    if (!is_file($l1)) { return ; }
    $fp   = fopen($l1,'rb');
    $size = filesize($l1);
    if ((int)$size==0) { return ; }
    $I1 = fread($fp,$size);
    fclose($fp);
    return $I1;
}

// require_file *** *** www.LazyCMS.net *** ***
function require_file($l1){
    // $l1:filePath
    static $I1 = array();
    if (is_file($l1)) {
        if (!isset($I1[$l1])) {
            require $l1;
            $I1[$l1] = true;
            return true;
        }
        return false;
    }
    return false;
}

// include_file *** *** www.LazyCMS.net *** ***
function include_file($l1){
    // $l1:filePath
    static $I1 = array();
    if (is_file($l1)) {
        if (!isset($I1[$l1])) {
            $I1[$l1] = include $l1;
        }
        return $I1[$l1];
    }
    return false;
}

// print_v *** *** www.LazyCMS.net *** ***
function print_v($l1=null){
    $title = G('TITLE');
    $hl = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
    $hl.= $title ? '<title>'.G('TITLE').'</title>': null;
    $hl.= '<html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
    $hl.= '<link href="../system/images/main.css" rel="stylesheet" type="text/css" />';
    $hl.= '<script type="text/javascript" src="../../common/js/jquery.js?ver=1.2.6"></script>';
    $hl.= '<script type="text/javascript" src="../../common/js/jquery.lazycms.js?ver=1.0"></script>';
    $hl.= '<script type="text/javascript">parent.document.title = "'.G('TITLE').' - '.L('manage/@title').'";';
    $hl.= '$(document).ready(function(){ ';
    $hl.= 'autoTitle();';
    // 用户打开操作提示
    $hl.= '$("#box").tips("tip","[@tip]");';
    $hl.= ' });'.G('SCRIPT').'</script>';
    $hl.= G('HEAD');
    $hl.= '</head><body>'.$l1.'</body></html>'; echo $hl;
}

// redirect *** *** www.LazyCMS.net *** ***
function redirect($l1){
    header("Content-Type:text/html; charset=utf-8");
    $l1 = str_replace(array("\n", "\r"), '', $l1);
    $js = '<script type="text/javascript" charset="utf-8">parent.location.href="'.$l1.'";</script>';
    exit('<meta http-equiv="refresh" content="0;url='.$l1.'" />'.$js);
}

// pinyin *** *** www.LazyCMS.net *** ***
function pinyin($l1){
    static $I3 = null; $I1 = null;
    preg_match_all(G('CN_PATTERN'),trim($l1),$I2);
    $l2 = $I2[0]; $l3 = count($l2);
    if (empty($I3)) {
        $I3 = include_file(COM_PATH.'/data/pinyin.php');
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
    return trim($I1,'-');
}

// echo_json *** *** www.LazyCMS.net *** ***
function echo_json($l1,$l2=1){
    if (!is_array($l1)) { 
        $I1['text'] = $l1;
    } else {
        $I1 = $l1;
    }
    switch ((int)$l2){
        case 0 : $I1['status'] = 'error'; break;
        case 1 : $I1['status'] = 'success'; break;
        case 2 : $I1['status'] = 'tips'; break;
    }
    exit(json_encode($I1));
}

// form_opts *** *** www.LazyCMS.net *** ***
function form_opts($l1,$l2,$l3,$l4=null){
    $I1 = null;
    $I2 = get_dir_array($l1,$l2);
    if ($l2=='xml') {
        foreach ($I2 as &$v) {
            if ($n = strpos($v,'.')) { $v = substr($v,$n+1); }
        }
        $I2 = array_unique($I2);
    }
    if (strpos($l3,'%23')!==false) { $l3 = str_replace('%23','#',$l3); }
    foreach ($I2 as $l5) {
        if ($l2=='xml') {
            $l5 = basename($l5,".xml");
            if ($n = strpos($l5,'.')) {
                $l5 = substr($l5,$n+1);
            }
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
        $I1.= $I3;
    }
    return $I1;
}

// menu *** *** www.LazyCMS.net *** ***
function menu($l1){
    if (($l2 = strpos($l1,'|'))!==false) {
        $l2 = substr($l1,0,$l2);
        $l1 = substr($l1,$l2+1);
    }
    $l3 = basename(PHP_FILE);
    $l4 = isset($_REQUEST['action']) ? strtolower($_REQUEST['action']) : null;
    $l5 = ' class="active"'; $l6 = 'javascript:self.location.reload();';
    $I1 = '<ul id="tabs">';
    $I2 = explode(';',$l1);
    foreach ($I2 as $k=>$v) {
        if (strpos($v,':')!==false) {
            $I3 = explode(':',$v); $active = null;
            if (!empty($l2)) {
                if (($k+1)==$l2) {
                    $active = $l5; $I3[1] = $l6;
                }
            } elseif (!empty($l4)){
                if ((string)$I3[1]==(string)$l3.'?action='.$l4) {
                    $active = $l5; $I3[1] = $l6;
                }
            } else {
                if ((string)$I3[1]==(string)$l3) {
                    $active = $l5; $I3[1] = $l6;
                }
            }
            $I1.= '<li'.$active.'><a href="'.$I3[1].'">'.$I3[0].'</a></li>';
        } else {
            $I1.= '<li class="active"><a href="javascript:self.location.reload();">'.$v.'</a></li>';
        }
    }
    $I1.= '</ul>';
    return $I1;
}

// validate *** *** www.LazyCMS.net *** ***
function validate($l1,$l2){
    // $l1:str, $l2:类型
    switch((string)$l2){
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
            $l3 = '^(http|https|ftp):(\/\/|\\\\)(([\w\/\\\+\-~`@:%])+\.)+([\w\/\\\.\=\?\+\-~`@\':!%#]|(&amp;)|&)+\.('.$l4.')$';
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

// pagelist *** *** www.LazyCMS.net *** ***
function pagelist($l1,$l2,$l3,$l4){
    //url,page,总页数,记录总数
    $I1 = null;
    if (strpos($l1,'%24')!==false) { $l1 = str_replace('%24','$',$l1); }
    if (strpos($l1,'$')==0 || $l4==0) { return ; }
    if ($l2 > 2) {
        $I1.= '<a href="'.str_replace('$',$l2-1,$l1).'">&laquo;</a>';
    } elseif ($l2==2) {
        $I1.= '<a href="'.str_replace('$',1,$l1).'">&laquo;</a>';
    }
    if ($l2 > 3) {
        $I1.= '<a href="'.str_replace('$',1,$l1).'">1</a><span>&#8230;</span>';
    }
    $l5 = $l2-2;
    $l6 = $l2+7;
    for ($i=$l5; $i<=$l6; $i++) {
        if ($i>=1 && $i<=$l3) {
            if ((int)$i==(int)$l2) {
                $I1.= '<span class="active">'.$i.'</span>';
            } else {
                if ($i==1) {
                    $I1.= '<a href="'.str_replace('$',1,$l1).'">'.$i.'</a>';
                } else {
                    $I1.= '<a href="'.str_replace('$',$i,$l1).'">'.$i.'</a>';
                }
            }
        }
    }
    if ($l2 < ($l3-7)) {
        $I1.= '<span>&#8230;</span><a href="'.str_replace('$',$l3,$l1).'">'.$l3.'</a>';
    }
    if ($l2 < $l3) {
        $I1.= '<a href="'.str_replace('$',$l2+1,$l1).'">&raquo;</a>';
    }
    return '<div class="pages">'.$I1.'</div>';
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
        $l2 = COM_PATH;
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
        $l1 = str_replace('@.','',$l1);
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
                    $I1 = require_file($v);
                }
            }
            return $I1;
        } else {
            return false;
        }
    } else {
        // 导入目录下的指定类库文件
        return require_file($l6);
    }
}

// check_user *** *** www.LazyCMS.net *** ***
function check_user($l1=null,$l2=null){
    return get_user($l1,$l2);
}

// check_login *** *** www.LazyCMS.net *** ***
function check_login($l1=null,$l2='../logout.php'){
    if ($_USER = get_user($l1)) { 
        return $_USER;
    } else {
        if ($_SERVER['REQUEST_METHOD']=='POST'){
            // ajax方式输出错误
            echo_json(array(
                'text'  => L('error/overtime'),
                'sleep' => 3,
                'url'   => $l2,
            ),0);
        } else {
            redirect($l2);
        }
    }
}

// get_user *** *** www.LazyCMS.net *** ***
function get_user($l1=null,$l2=null){
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
    if ((int)$funcNum > 1 && !empty($l2)) {
        $username = $l1;
        $userpass = $l2;
        $purview  = null;
    } else {
        $username = Cookie::get('username');
        $userpass = Cookie::get('userpass');
        $purview  = ($l1=='manage') ? 'system/'.$l1 : MODULE.'/'.$l1;
    }
    if (empty($username) || empty($userpass)) { return false; }
    // 开始验证
    $res = $db->query("SELECT `su`.*,`sg`.*
                        FROM `#@_system_users` AS `su`
                        LEFT JOIN `#@_system_group` AS `sg` ON `su`.`groupid` = `sg`.`groupid`
                        WHERE `su`.`username`=? AND `su`.`isdel`=0 AND `su`.`islock`=0
                        LIMIT 0,1;",$username);
    if ($rs = $db->fetch($res)) {
        if ((int)$funcNum > 1 && !empty($l2)) {
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
                // 需要验证
                if (!empty($purview)) {
                    if (instr($rs['purview'],$purview)) {
                        return $rs;
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
        if (!empty($args)) {
            $traceInfo.= implode(', ', $args);
        }
        $traceInfo.=")\n";
    }
    $error['trace'] = replace_root($traceInfo);
    $hl = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
    $hl.= '<html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
    $hl.= '<title>'.L('error/title').'</title><style type="text/css">';
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
    $hl.= '<div class="notice"><h2>'.L('error/title').'</h2>';
    $hl.= '<div>You can choose to [ <a href="javascript:self.location.reload();">'.L('common/tryagain').'</a> ] [ <a href="javascript:history.back();">'.L('common/back').'</a> ] or [ <a href="'.C('SITE_BASE').'">'.L('common/backhome').'</a> ]</div>';
    $hl.= '<p><strong>'.L('error/position').':</strong>　FILE: <strong class="red">'.$error['file'].'</strong>　LINE: <strong class="red">'.$error['line'].'</strong></p>';
    $hl.= '<p class="title">[ '.L('error/errinfo').' ]</p>';
    $hl.= '<p class="message">'.$error['message'].'</p>';
    $hl.= '<p class="title">[ TRACE ]</p>';
    $hl.= '<p class="trace">'.nl2br($error['trace']).'</p></div>';
    $hl.= '<div id="footer">LazyCMS <sup>'.LAZY_VERSION.'</sup></div>';
    $hl.= '</body></html>';
    ob_end_clean();
    exit($hl);
}

// ubbencode *** *** www.LazyCMS.net *** ***
function ubbencode($l1){
    if (strlen($l1)==0) {return ;}
    $I1 = h2encode($l1);
    if (strpos($I1,' ')!==false) { $I1 = str_replace(' ','&nbsp;',$I1); }
    $I1 = preg_replace('/\r\n|\n|\r/','<br/>',$I1);
    $I1 = preg_replace('/\[url\](.+?)\[\/url]/i','<a href="$1">$1</a>',$I1);
    $I1 = preg_replace('/\[url\=([^\]]+)](.+?)\[\/url]/i','<a href="$1">$2</a>',$I1);
    $I1 = preg_replace('/\[img\](.+?)\[\/img]/i','<img src="$1" />',$I1);
    $I1 = preg_replace('/\[b\](.+?)\[\/b]/i','<b>$1</b>',$I1);
    $I1 = preg_replace('/\[strong\](.+?)\[\/strong]/i','<strong>$1</strong>',$I1);
    $I1 = preg_replace('/\[i\](.+?)\[\/i]/i','<i>$1</i>',$I1);
    $I1 = preg_replace('/\[u\](.+?)\[\/u]/i','<u>$1</u>',$I1);
    $I1 = preg_replace('/\[s\](.+?)\[\/s]/i','<strike>$1</strike>',$I1);
    $I1 = preg_replace('/\[sub\](.+?)\[\/sub]/i','<sub>$1</sub>',$I1);
    $I1 = preg_replace('/\[sup\](.+?)\[\/sup]/i','<sup>$1</sup>',$I1);
    $I1 = preg_replace('/\[color\=([^\]]+)](.+?)\[\/color]/i','<span style="color: $1">$2</span>',$I1);
    $I1 = preg_replace('/\[bgcolor\=([^\]]+)](.+?)\[\/bgcolor]/i','<span style="background-color: $1">$2</span>',$I1);
    $I1 = preg_replace('/\[font\=([^\]]+)](.+?)\[\/font]/i','<span style="font-family: $1">$2</span>',$I1);
    $I1 = preg_replace('/\[size\=([^\]]+)](.+?)\[\/size]/i','<span style="font-size: $1">$2</span>',$I1);
    $I1 = preg_replace('/\[align\=([^\]]+)](.+?)\[\/align]/i','<div style="text-align: $1">$2</div>',$I1);
    $I1 = preg_replace('/\[p\](.+?)\[\/p]/i','<p>$1</p>',$I1);
    $I1 = preg_replace('/\[div\](.+?)\[\/div]/i','<div>$1</div>',$I1);
    $I1 = preg_replace('/\[pre\](.+?)\[\/pre]/i','<pre>$1</pre>',$I1);
    $I1 = preg_replace('/\[address\](.+?)\[\/address]/i','<address>$1</address>',$I1);
    for ($i=1; $i<7; $i++) {
        $I1 = preg_replace('/\[h'.$i.'\](.+?)\[\/h'.$i.']/i','<h'.$i.'>$1</h'.$i.'>',$I1);
    }
    return $I1;
}

// langbox *** *** www.LazyCMS.net *** ***
function langbox($l1){
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
    return isset($lang[$l1]) ? $lang[$l1] : $l1;
}

// C *** *** www.LazyCMS.net *** ***
function C($l1=null,$l2=null) {
    // $l1:name, $l2:value
    static $I1 = array();
    if(!is_null($l2)) {
        $I1[strtolower($l1)] = $l2;
        return ;
    }
    if(empty($l1)) {
        return $I1;
    }
    if(is_array($l1)) {
        $I1 = array_merge($I1,array_change_key_case($l1));
        return $I1;
    }
    if(isset($I1[strtolower($l1)])) {
        return $I1[strtolower($l1)];
    }else{
        return false;
    }
}

// G *** *** www.LazyCMS.net *** ***
function G($l1=null,$l2=null){
    static $I1 = array();
    if(!is_null($l2)) {
        $I1[strtolower($l1)] = $l2;
        return ;
    }
    if(empty($l1)) {
        return $I1;
    }
    if(is_array($l1)) {
        $I1 = array_merge($I1,array_change_key_case($l1));
        return $I1;
    }
    if(isset($I1[strtolower($l1)])) {
        return $I1[strtolower($l1)];
    }else{
        return false;
    }
}

// L *** *** www.LazyCMS.net *** ***
function L($l1,$l2=null,$l3='system'){
    static $I2 = array();
    if ($l1=='') { return $l1; }
    $l4 = language();
    $I3 = isset($I2["{$l3}.{$l4}"]) ? $I2["{$l3}.{$l4}"] : null;
    if (!is_object($I3)) {
        $l5 = COM_PATH."/language/{$l3}."; $l6 = $l5.$l4.'.xml';
        if (!is_file($l6)) { $l6 = $l5.C('LANGUAGE').'.xml'; }
        if (!is_file($l6)) { return '['.$l1.']'; }
        $I3 = new DOMDocument;
        $I3->load($l6);
        $I2[$l4] = $I3;
    }
    $I4 = new DOMXPath($I3);
    $I5 = $I4->evaluate("//lazycms/$l1");
    if (false !== strpos($l1,'/@')) {
        $I1 = $I5->item(0)->value;
    } else {
        $I1 = $I5->item(0)->nodeValue;
    }
    if (!empty($l2) && is_array($l2)) {
        foreach ($l2 as $k=>$v) {
           $I1 = str_replace('{$'.$k.'}',$v,$I1); 
        }
    }
    return $I1 ? $I1 : '['.$l1.']';
}

// property_exists *** *** www.LazyCMS.net *** ***
if (!function_exists('property_exists')) {
    function property_exists($l1, $l2) { // $l1:class, $l2:property
        if (is_object($l1)) { $l1 = get_class($l1); }
        return array_key_exists($l2,get_class_vars($l1));
    }
}

// json_encode *** *** www.LazyCMS.net *** ***
if (!function_exists('json_encode')) {
    function json_encode($l1){
        static $I1 = array();
        if (!isset($I1[0])) {
            import('class.json');
            $I1[0] = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
        }
        return $I1[0]->encode($l1);
    }
}

// json_decode *** *** www.LazyCMS.net *** ***
if (!function_exists('json_decode')) {
    function json_decode($l1){
        static $I1 = array();
        if (!isset($I1[0])) {
            import('class.json');
            $I1[0] = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
        }
        return $I1[0]->decode($l1);
    }
}

// 中文正则，请不要修改 *** *** www.LazyCMS.net *** ***
C('CN_PATTERN',"/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/");
?>