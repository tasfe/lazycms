<?php
/**
 * +---------------------------------------------------------------------------+
 * | LL                             LLLL   LL     L   LLLL               LLLL  |
 * | LL                            LL   L  LLL   LL  LL   L             LL  LL |
 * | LL      LLLL   LLLLL  LL  LL  LL      LLLL LLL  LL         LL  LL      LL |
 * | LL         LL     LL  LL  LL  LL      L LLL LL  LLLLL      LL  LL     LL  |
 * | LL      LLLLL    LL    LLLL   LL      L  L  LL   LLLLL     LL  LL    LL   |
 * | LL     LL  LL   LL     LLLL   LL      L     LL      LL      LLLL    LL    |
 * | LL     LL  LL  LL       LL    LL   L  L     LL  L   LL      LLLL   LL     |
 * | LLLLLL  LLLLL  LLLLL    LL     LLLL   L     LL   LLLL        LL    LLLLLL |
 * |                        LL                                                 |
 * |                        LL                                                 |
 * +---------------------------------------------------------------------------+
 * | Copyright (C) 2007-2008 LazyCMS.net All rights reserved.                  |
 * +---------------------------------------------------------------------------+
 * | LazyCMS is free software. This version use Apache License 2.0             |
 * | See LICENSE.txt for copyright notices and details.                        |
 * +---------------------------------------------------------------------------+
 */
defined('COM_PATH') or die('Restricted access!');
/**
 * LazyCMS 公用函数库
 */
/**
 * 初始化函数
 *
 */
function lazycms_run() {
    static $R = true;
    if (!$R) { return ; } $R = false;
    if (!defined('E_STRICT')) { define('E_STRICT', 2048); }
    // 加载配置
    c(include_file(COM_PATH.'/config.php'));
    // 设置错误级别
    ini_set('display_errors',true);
    error_reporting(E_ALL &~ E_NOTICE);
    // 解析魔术引号
    set_magic_quotes_runtime(0);
    if (get_magic_quotes_gpc()) {
        $R = array(& $_GET, & $_POST, & $_COOKIE, & $_REQUEST);
        while (list($k,$v) = each($R)) {
            $R[$k] = recursive_deep($R[$k],'stripslashes');
        }
        unset($R,$k,$v);
    }
    unset($_ENV,$HTTP_ENV_VARS,$HTTP_POST_VARS,$HTTP_GET_VARS,$HTTP_POST_FILES,$HTTP_COOKIE_VARS);
    // 设置系统时区 PHP5支持
    if(function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); } ob_start();
    // 定义处理错误的函数
    set_error_handler('lazycms_error'); $PHP_DIR = dirname(PHP_FILE);
    // 获取当前动作
    $action = isset($_REQUEST['action']) ? strtolower($_REQUEST['action']) : null;
    // 设置当前模块常量
    define('MODULE',substr($PHP_DIR,strrpos($PHP_DIR,'/')+1));
    // 设置当前动作常量
    define('ACTION',$action);
    // 导入模块配置
    import('modules.*.static.*');
    // 在动作之前执行的函数
    if (function_exists('lazy_before')){ lazy_before(); }
    // 执行动作调度
    if (!empty($action)) {
        // 组合出需要执行的函数
        $func = 'lazy_'.$action;
        // 判断函数是否存在
        if (function_exists($func)) {
            $func();
        } else {
            // 输出错误信息
            trigger_error(t('system::error/nofunc',array($func,PHP_FILE,$func)));
        }
    } else {
        if (function_exists('lazy_main')) {
            lazy_main();
        } else {
            // 输出错误信息，提示用户定义lazy_def()函数
            trigger_error(t('system::error/nomain'));
        }
    }
    // 在动作之后执行的函数
    if (function_exists('lazy_after')){ lazy_after(); }
}
/**
 * 异常监听函数
 *
 * @param int    $errno
 * @param string $errstr
 * @param string $errfile
 * @param int    $errline
 */
function lazycms_error($errno, $errstr, $errfile, $errline){
    if (!in_array($errno,array(E_PARSE,E_USER_ERROR,E_USER_WARNING,E_USER_NOTICE,E_ALL))) { return ; }
    $errfile = replace_root($errfile); $errstr = replace_root($errstr);
    $trace = debug_backtrace(); unset($trace[0]);
    $error = array(); $traceInfo = null;
    $error['file']    = $errfile;
    $error['line']    = $errline;
    $error['message'] = $errstr;
    foreach($trace as $t) {
        $file  = isset($t['file']) ? replace_root($t['file']) : null;
        $line  = isset($t['line']) ? $t['line'] : null;
        $class = isset($t['class']) ? $t['class'] : null;
        $type  = isset($t['type']) ? $t['type'] : null;
        $args  = isset($t['args']) ? $t['args'] : null;
        $function  = isset($t['function']) ? $t['function'] : null;
        $traceInfo.= '['.date("y-m-d H:i:s").'] '.$file.' ('.$line.') ';
        $traceInfo.= $class.$type.$function.'(';
        if (is_array($args)) {
            $arrs = array();
            foreach ($args as $v) {
                if (is_object($v)) {
                    $arrs[] = implode(' ',get_object_vars($v));
                } else {
                    $arrs[] = h2c(var_export($v,true));
                }
            }
            $traceInfo.= implode(', ',$arrs);
        }
        $traceInfo.=")\n";
    }
    $error['trace'] = replace_root($traceInfo); ob_end_clean();
    // ajax 提交模式
    if ($_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest') {
        $hl = '<style type="text/css">';
        $hl.= '.ErrorMessage .red{ color:red; }';
        $hl.= '.ErrorMessage{ padding:10px; margin:5px; color:#666; background:#FCFCFC; border:1px solid #E0E0E0;}';
        $hl.= '.ErrorMessage h2{ border-bottom:1px solid #DDD; font-size:25px; margin-top:0; padding:8px 0;}';
        $hl.= '.ErrorMessage .title{ margin:4px 0; color:#F60; font-weight:bold;}';
        $hl.= '.ErrorMessage .message, .ErrorMessage .trace{ padding:1em; border:solid 1px #000; margin:10px 0; background:#FFD; line-height:150%;}';
        $hl.= '.ErrorMessage .message{ background:#FFD; color:#2E2E2E; border:1px solid #E0E0E0; }';
        $hl.= '.ErrorMessage .trace{ background:#E7F7FF; border:1px solid #E0E0E0; color:#535353;}';
        $hl.= '</style>';
        $hl.= '<div class="ErrorMessage"><h2>'.t('system::error').'</h2>';
        $hl.= '<p><strong>'.t('system::error/position').':</strong>　FILE: <strong class="red">'.$error['file'].'</strong>　LINE: <strong class="red">'.$error['line'].'</strong></p>';
        $hl.= '<p class="title">[ '.t('system::error/message').' ]</p>';
        $hl.= '<p class="message">'.$error['message'].'</p>';
        $hl.= '<p class="title">[ TRACE ]</p>';
        $hl.= '<p class="trace">'.nl2br($error['trace']).'</p></div>';
    } else {
        $RE = isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:PHP_FILE;
        $hl = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
        $hl.= '<html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
        $hl.= '<title>'.t('system::error').'</title><style type="text/css">';
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
        $hl.= '<div class="notice"><h2>'.t('system::error').'</h2>';
        $hl.= '<div>You can choose to [ <a href="javascript:self.location.reload();">'.t('system::try').'</a> ] [ <a href="'.$RE.'">'.t('system::back').'</a> ] or [ <a href="'.SITE_BASE.'">'.t('system::home').'</a> ]</div>';
        $hl.= '<p><strong>'.t('system::error/position').':</strong>　FILE: <strong class="red">'.$error['file'].'</strong>　LINE: <strong class="red">'.$error['line'].'</strong></p>';
        $hl.= '<p class="title">[ '.t('system::error/message').' ]</p>';
        $hl.= '<p class="message">'.$error['message'].'</p>';
        $hl.= '<p class="title">[ TRACE ]</p>';
        $hl.= '<p class="trace">'.nl2br($error['trace']).'</p></div>';
        $hl.= '<div id="footer">LazyCMS <sup>'.LAZY_VERSION.'</sup></div>';
        $hl.= '</body></html>';
    }
    exit($hl);
}
/**
 * 批量获取指定的post or get 数组
 *
 * @return array
 */
function POST(){
    $R = array();
    $F = func_get_args();
    foreach ($F as $v) {
        $R[] = $_POST[$v];
    }
    return $R;
}
/**
 * 按钮
 *
 * @param string $p1
 * @return string
 */
function but($p1){
    $R = '<p class="button"><button type="submit">'.t($p1).'</button>';
    $R.= '<button type="reset" onclick="return $.confirm(\''.t('system::confirm/reset').'\',function(r){ r?$(\'button[type=reset]\').parents(\'form\').get(0).reset():false; })">'.t('system::reset').'</button>';
    $R.= '<button type="button" onclick="self.history.back();">'.t('system::back').'</button></p>';
    return $R;
}
/**
 * 返回当前 Unix 时间戳和微秒数
 *
 * @return float
 */
function microtime_float(){
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}
/**
 * 判断是否超出最大执行时间
 *
 * @param  float    $p1   最大执行时间
 * @return bool
 */
function isOverMaxTime($execTime){
    if (floatval(microtime_float()-$GLOBALS['_beginTime']) >= floatval($execTime)) {
        return true;
    }
}
/**
 * 获取当前时间戳
 *
 * @return integer
 */
function now(){
    return time() + (c('TIME_ZONE')*3600);
}
/**
 * 取得PHP设置
 *
 * @param  string    $p1   配置选项的名称
 * @return string
 */
function get_php_setting($p1){
    $R = (ini_get($p1) == '1' ? 1 : 0);return isok($R);
}
/**
 * 返回正对符号
 *
 * @param  bool    $p1   true or false
 * @return string
 */
function isok($p1){
    return $p1 ? '<strong style="color:#009900;">'.t('system::ON').'</strong>' :
                    '<strong style="color:#FF0000;">'.t('system::OFF').'</strong>';
}
/**
 * 替换文件路径以网站根目录开始，防止暴露文件的真实地址
 *
 * @param   string $p1
 * @return  string
 */
function replace_root($p1){
    return str_replace(SEPARATOR,'/',str_replace(LAZY_PATH.SEPARATOR,SITE_BASE,$p1));
}
/**
 * 转换特殊字符为HTML实体
 *
 * @param   string $p1
 * @return  string
 */
function h2c($p1){
    return empty($p1)?null:htmlspecialchars($p1);
}
/**
 * 深度递归处理
 *
 * @param   array     $p1     要处理的数组
 * @param   string    $p2     函数名：可以使用其他函数进行递归转义
 * @return  array
 */
function recursive_deep($p1,$p2='stripslashes') {
    return is_array($p1) ? array_map('recursive_deep', $p1) : $p2($p1);
}
/**
 * 转换多维数组所有的key为小写
 *
 * @param array $p1
 * @return array
 */
function array_change_key_case_deep($p1){
    return is_array($p1) ? array_change_key_case(array_map('array_change_key_case_deep', $p1)) : $p1;
}
/**
 * 转换字符串以js语法输出
 *
 * @param  string    $p1   字符串
 * @param  bool      $p2   是否输出
 *      true    转换后的代码 加上 document.writeln 输出方法
 * @return string
 */
function t2js($p1,$p2=false){
    $R = str_replace(array("\r", "\n"), array('', '\n'), addslashes($p1));
    return $p2 ? "document.writeln(\"$R\");" : $R;
}
/**
 * 防止浏览器缓存
 */
function no_cache(){
    header("Expires:".date("D,d M Y H:i:s",now()-60*10)." GMT");
    header("Last-Modified:".date("D,d M Y H:i:s")." GMT");
    header("Cache-Control:no-cache,must-revalidate");
    header("Pragma:no-cache");
}
/**
 * 取得网站的语言设置
 *
 * @return string
 */
function language() {
    $R = isset($_GET['lang'])?$_GET['lang']:null;
    if (!$R) { $R = Cookie::get('language'); }
    return $R ? $R : c('LANGUAGE');
}
/**
 * 批量创建目录
 *
 * @param string $p1    文件夹路径
 * @param int    $p2    权限
 * @return bool
 */
function mkdirs($p1, $p2 = 0777){
    if (!is_dir($p1)) {
        mkdirs(dirname($p1), $p2);
        return @mkdir($p1, $p2);
    }
    return true;
}
/**
 * 在数组或字符串中查找
 *
 * @param mixed  $p1 被搜索的数据，字符串用英文“逗号”分割
 * @param string $p2 需要搜索的字符串
 * @return bool
 */
function instr($p1,$p2){
    if (empty($p1)) { return false; }
    if (!is_array($p1)) { $p1 = explode(",",$p1); }
    return in_array($p2,$p1) ? true : false;
}
/**
 * 返回文件大小，自动设置适合于大小的单位
 *
 * @param int $p1   文件的大小，单位：字节
 * @return string
 */
function file_size($p1) {
	$R = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
	$n = 0;
	while ($p1 >= 1024) {
		$p1 /= 1024;
		$n++;
	}
	return round($p1,2).' '.$R[$n];
}
/**
 * 取得数据库连接对象
 *
 * @return object
 */
function get_conn(){
    static $db = null;
    if (is_object($db)) { return $db; }
    $db = DB::factory(c('DSN_CONFIG'));
    $db->select_db();
    return $db;
}
/**
 * 读取文件
 *
 * @param string $p1    filename
 * @return string
 */
function read_file($p1){
    if (!is_file($p1)) { return ; }
    $fp   = fopen($p1,'rb');
    $size = filesize($p1);
    if ((int)$size==0) { return ; }
    $R = fread($fp,$size);
    fclose($fp);
    return $R;
}
/**
 * 将文本保存成文件
 *
 * @param string $p1    filename
 * @param string $p2    content
 * @param bool   $p3    写入模式 false:追加
 */
function save_file($p1,$p2='',$p3=true){
    if (file_exists($p1)) {
        if (!is_writable($p1)) {
            // TODO: 只记录错误日志，不输出错误信息
        }
    }
    if (!$fp = fopen($p1,($p3?'wb':'ab'))) {
        // TODO: 记录错误日志，无法打开文件
    }
    flock($fp,LOCK_EX + LOCK_NB);
    if (!fwrite($fp,$p2)) {
        // TODO: 记录错误日志，无法写入文件
    }
    fclose($fp);
}
/**
 * 页面跳转
 *
 * @param string $p1
 */
function redirect($p1) {
    $p1 = str_replace(array("\n", "\r"), '', $p1);
    if (!headers_sent()) { header("Content-Type:text/html; charset=utf-8"); }
    if ($_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest') {
        echo_json('REDIRECT',array('URL'=>$p1));
    } else {
        $js = '<script type="text/javascript" charset="utf-8">self.location.href="'.$p1.'";</script>';
        exit('<meta http-equiv="refresh" content="0;url='.$p1.'" />'.$js);
    }
}
/**
 * 在数组的value里面搜索
 *
 * @param string $p1
 * @param array  $p2
 * @return bool
 */
function array_search_value($p1,$p2){
    while (list($k,$v)=each($p2)) {
        if (strpos($v,$p1)!==false) {
            return $k;
        }
    }
    return false;
}
/**
 * 随机字符串
 *
 * @param  int    $p1    返回字符串的位数，默认为6位
 * @return string
 */
function salt($p1=6){
    $p2 = "0123456789abcdefghijklmnopqrstopwxyz";
    $p3 = strlen($p2); $p4 = null;
    for ($i=0;$i<$p1;$i++) {
        $p4.= $p2[mt_rand(0,$p3-1)];
    }
    return $p4;
}
/**
 * 输出JSON格式
 *
 * @param int   $p1   消息代码
 * @param mixed $p2   消息内容
 */
function echo_json($p1,$p2){
    exit(json_encode(array(
        'CODE' => $p1,
        'DATA' => $p2,
    )));
}

function ajax_process($p1,$p2){
    echo_json('PROCESS',array(
        'PERCENT' => $p1,
        'DATA'    => $p2,
    ));
}

/**
 * alert 警告
 *
 * @param string $p1    Message
 * @param string $p2    Url:可选
 *          默认参数：不进行转向
 *          0：当前url，不带 $_SERVER["QUERY_STRING"]
 *          1:来路，$_SERVER["HTTP_REFERER"]
 */
function ajax_alert($p1,$p2=''){
    switch ((string)$p2) {
        case '0':
            $p2 = PHP_FILE;
            break;
        case '1':
            $p2 = $_SERVER["HTTP_REFERER"];
            break;
    }
    echo_json('ALERT',array(
        'MESSAGE' => $p1,
        'URL'     => $p2,
    ));
}
/**
 * 返回结果
 *
 * @param string $p1
 */
function ajax_result($p1=array()) {
    echo_json('RESULT',$p1);
}
/**
 * success 提示
 *
 * @param string $p1    Message
 * @param string $p2    Url:可选
 *          默认参数：不进行转向
 *          0：当前url，不带 $_SERVER["QUERY_STRING"]
 *          1:来路，$_SERVER["HTTP_REFERER"]
 */
function ajax_success($p1,$p2=''){
    switch ((string)$p2) {
        case '0':
            $p2 = PHP_FILE;
            break;
        case '1':
            $p2 = $_SERVER["HTTP_REFERER"];
            break;
    }
    echo_json('SUCCESS',array(
        'MESSAGE' => $p1,
        'URL'     => $p2,
    ));
}
/**
 * error 提示
 *
 * @param string $p1    Message
 * @param string $p2    Url:可选
 *          默认参数：不进行转向
 *          0：当前url，不带 $_SERVER["QUERY_STRING"]
 *          1:来路，$_SERVER["HTTP_REFERER"]
 */
function ajax_error($p1,$p2=''){
    switch ((string)$p2) {
        case '0':
            $p2 = PHP_FILE;
            break;
        case '1':
            $p2 = $_SERVER["HTTP_REFERER"];
            break;
    }
    echo_json('ERROR',array(
        'MESSAGE' => $p1,
        'URL'     => $p2,
    ));
}
/**
 * 执行gzip压缩并输出
 *
 * @param  string   $p1     字符串
 * @return gzip
 */
function ob_zip($p1){
    if (!headers_sent() && extension_loaded("zlib") && strstr($_SERVER["HTTP_ACCEPT_ENCODING"],"gzip")) {
        $p1 = gzencode($p1,9);
        header("Content-Encoding: gzip");
        header("Content-Length: ".strlen($p1));
    }
    return $p1;
}
/**
 * 抓取图片
 *
 * @param string $p1
 * @return string
 */
function snap_img($p1){
    $R = $p1;
    if (preg_match_all('/<img[^>]*src=("([^"]+)"|\'([^\']+)\')[^>]*>/isU',$R,$imgs)) {
        $imgs[1] = array_unique($imgs[1]);
        foreach ($imgs[1] as $img) {
            $img = trim($img,'"\'');
            if ($downImg = down_img($img)) {
                if (validate($img,5) && $img!=$downImg) {
                    $R = str_replace($img,SITE_BASE.ltrim($downImg,'/'),$R);
                }
            }
        }
    }
    return $R;
}
/**
 * 下载图片
 *
 * @param string $p1    要下载的文件路径
 * @param string $p2    指定保存地址
 * @return string
 */
function down_img($p1,$p2=null){
    static $http = null;
    if (validate($p1,5)) {
        if (!is_object($d)) {
            import("system.httplib");
            $http = new Httplib();
        }
        $http->connect($p1,'GET',100)->send();
        if ($http->status() == 200) {
            if (empty($p2)) {
                $imgInfo = pathinfo($p1);
                $imgPath = c('UPLOAD_IMAGE_PATH').date('/Y/m/d/',now());
                $imgPath = str_replace('/',SEPARATOR,$imgPath);
                if (isset($imgInfo['extension']) && isset($imgInfo['filename'])) {
                    $fileName = $imgInfo['filename'].'.'.$imgInfo['extension'];
                    if (is_file(LAZY_PATH.SEPARATOR.$imgPath.$fileName)) {
                        $fileName = str_replace('.','',microtime(true)).'.'.$imgInfo['extension'];
                    }
                } else {
                    if(preg_match("/Content-Type\:(.+)\r\n/i",$http->header(),$imgInfo)){
                        $fileName = str_replace('.','',microtime(true)).'.'.substr($imgInfo[1],strrpos($imgInfo[1],'/')+1);
                    }
                }
            } else {
                $imgInfo = pathinfo($p2);
                $imgPath  = $imgInfo['dirname'];
                $fileName = $imgInfo['basename'];
            }
            mkdirs(LAZY_PATH.SEPARATOR.$imgPath);
            save_file(LAZY_PATH.SEPARATOR.$imgPath.$fileName,$http->response());
            return str_replace(SEPARATOR,'/',$imgPath).$fileName;
        } else {
            return $p1;
        }
    }
    return $p1;
}
/**
 * UTF-8转换成其他任何编码
 * 
 * @param  string   $p1    要转换的内容
 * @param  string   $p2    转换的编码
 * @return string
 */
function utf2ansi($p1,$p2='GB2312'){
    if (function_exists('iconv')) {
        return iconv('UTF-8',"{$charset}//IGNORE",$p1);
    } elseif (function_exists('mb_convert_encoding')){
        return mb_convert_encoding($p1,$p2,'UTF-8');
    } else {
        return $p1;
    }
}
/**
 * 判断是否为UTF-8编码
 * 
 * @return bool
 */
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
/**
 * ANSI转换为UTF－8
 * 
 * @param  string   $p1    要转换的内容
 * @return string
 */
function ansi2utf($p1){
    if (strlen($p1)==0) { return ;}
    if (is_utf8($p1)) { return $p1; }
    if (function_exists('iconv')) {
        return iconv('',"UTF-8//IGNORE",$p1);
    } elseif (function_exists('mb_convert_encoding')){
        return mb_convert_encoding($p1,'UTF-8','GBK,ASCII');
    } else {
        return $p1;
    }
}
/**
 * 等同于 include_once
 *
 * @param string    $p1    被包含文件的路径
 * @return mixed
 */
function include_file($p1) {
    static $R = array();
    if (is_file($p1)) {
        if (!isset($R[$p1])) { $R[$p1] = include $p1; }
        return $R[$p1];
    }
    return false;
}
/**
 * 等同于 require_once
 *
 * @param string    $p1    被包含文件的路径
 * @return bool
 */
function require_file($p1){
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
/**
 * 清除空格
 *
 * @param string $p1
 * @return string
 */
function clear_blank($p1){
    if (strlen($p1)==0) { return ;} { $R = $p1; }
    $R = str_replace(array(chr(9),chr(10),chr(13)),'',$R);
    while (strpos($R,chr(32).chr(32))!==false || strpos($R,'&nbsp;')!==false) {
        $R = str_replace(chr(32).chr(32),chr(32),str_replace('&nbsp;',chr(32),$R));
    }
    return $R;
}
/**
 * 统计字符串的长度，支持中文
 *
 * @param string $p1
 * @return int
 */
function len($p1){
    if (function_exists('mb_strlen')) {
        return mb_strlen($p1,'utf-8');
    } elseif (function_exists('iconv_strlen')){
        return iconv_strlen($p1,'utf-8');
    } else {
        preg_match_all(g('CN_PATTERN'),$p1,$R1);
        return count($R1[0]);
    }
}
/**
 * 从左边截取，支持中文
 *
 * @param  string   $p1    要截取的字符串
 * @param  integer  $p2    截取多少个字
 * @return string
 */
function left($p1,$p2){
    if ((int)len($p1)>(int)$p2) {
        return cnsubstr($p1,$p2).'...';
    } else{
        return $p1;
    }
}
/**
 * 根据数组值的长度排序
 *
 * @param  array   $p1    排序数组
 * @return array
 */
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
/**
 * 转换成拼音或者英文路径
 *
 * @param string $p1
 * @return string
 */
function pinyin($p1){
    static $R2 = null; $R = null;
    preg_match_all(g('CN_PATTERN'),trim($p1),$R1);
    $p2 = $R1[0]; $p3 = count($p2);
    if (empty($R2)) {
        $R2 = include_file(COM_PATH.'/modules/system/config/pinyin.php');
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
/**
 * 中文截取，用法与substr相同
 *
 * @param string $p1
 * @param int    $p2
 * @return string
 */
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
        preg_match_all(g('CN_PATTERN'),$p1,$R1);
        if (count($R1[0]) - $p3 > $p4) {
            return implode('',array_slice($R1[0],$p3,$p4));
        }
        return implode('',array_slice($R1[0],$p3,$p4));
    }
}
/**
 * 根据文件扩展名返回文件的图标
 *
 * @param string $p1    文件名
 * @return string
 */
function icon($p1){
    $ext = strtolower(pathinfo($p1,PATHINFO_EXTENSION));
    switch ($ext) {
        case 'doc': case 'docx':
            $R = 'a9'; break;
		case 'htm': case 'html':
		    $R = 'b9'; break;
		case 'js':
		    $R = 'c4'; break;
		case 'mid': case 'wma': case 'mp3':
		    $R = 'c5'; break;
		case 'pdf':
		    $R = 'c6'; break;
		case 'php':
		    $R = 'c7'; break;
		case 'jpg': case 'gif': case 'bmp': case 'png':
		    $R = 'c8'; break;
		case 'swf':
		    $R = 'c9'; break;
		case 'txt': case 'sql':
		    $R = 'd4'; break;
		case 'xls':
		    $R = 'd5'; break;
        case 'ppt': case 'pptx':
		    $R = 'd6'; break;
		case 'zip': case 'rar':
		    $R = 'd7'; break;
        case 'db': case 'mdb':
		    $R = 'd9'; break;
        case 'css':
            $R = 'e6'; break;
        case 'xml':
		    $R = 'e1'; break;
		default:
		    $R = 'e2'; break;
    }
    return '<img class="'.$R.' os" src="'.SITE_BASE.'common/images/white.gif" />';
}
/**
 * 输出tabs菜单
 *
 * @param string $p1
 * @return string
 */
function menu($p1){
    if (($p3 = strpos($p1,'|'))!==false) {
        $p2 = substr($p1,0,$p3);
        $p1 = substr($p1,$p3+1);
    }
    $p3 = basename(PHP_FILE);
    $p4 = isset($_REQUEST['action']) ? strtolower($_REQUEST['action']) : null;
    $p5 = ' class="active"';
    $R = '<ul id="tabs">';
    $R1 = explode(';',$p1);
    foreach ($R1 as $k=>$v) {
        if (strlen($v)==0) { continue; }
        if (strpos($v,':')!==false) {
            $R2 = explode(':',$v); $active = null;
            if (!empty($p2)) {
                if (($k+1)==$p2) {
                    $active = $p5;
                }
            } elseif (!empty($p4)){
                if ((string)$R2[1]==(string)$p3.'?action='.$p4) {
                    $active = $p5;
                }
            } else {
                if ((string)$R2[1]==(string)$p3) {
                    $active = $p5;
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
/**
 * 将目录下的文件或文件夹读取成为数组
 *
 * @param string $p1    路径
 * @param string $p2    读取类型
 * @return array
 */
function get_dir_array($p1,$p2='*'){
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
/**
 * 格式化下拉框选项
 *
 * @param string $p1    路径
 * @param string $p2    类型
 * @param string $p3    html字符串 可以使用变量：#value#,#name#,#selected#
 * @param string $p4    selected
 */
function form_opts($p1,$p2,$p3,$p4=null){
    $R = null;
    $p5 = $p2=='lang' ? 'dir' : $p2;
    $R1 = get_dir_array($p1,$p5);
    if (strpos($p3,'%23')!==false) { $p3 = str_replace('%23','#',$p3); }
    foreach ($R1 as $v) {
        if ($p2=='lang') {
            $p6 = langbox($v);
        } else{
            $p6 = $v;
        }
        $R2 = $p3;
        if (strpos($R2,'#value#')!==false) { $R2 = str_replace('#value#',$v,$R2); }
        if (strpos($R2,'#name#')!==false)  { $R2 = str_replace('#name#',$p6,$R2); }
        if ($p4==$v) {
            $R2 = str_replace('#selected#',' selected="selected"',$R2);
        } else{
            $R2 = str_replace('#selected#','',$R2);
        }
        $R.= $R2;
    }
    return $R;
}
/**
 * 内容截取，支持正则
 *
 * @param string $p1    内容
 * @param string $p2    开始代码
 * @param string $p3    结束代码
 * @param string $p4    清除内容
 * @return string
 */
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
/**
 * 清除内容，支持正则
 *
 * @param string $p1
 * @param string $p2
 * @return string
 */
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
/**
 * 验证函数
 *
 * @param string $p1    需要验证的字符串
 * @param int    $p2    验证类型
 * @return bool
 */
function validate($p1,$p2){
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
/**
 * 格式化为XML
 *
 * @param string $p1
 * @return string
 */
function xmlencode($p1){
    if (strlen($p1)==0) { return ; } $R = $p1;
    if (strpos($R,'&')!==false) { $R = str_replace('&','&amp;',$R); }
    if (strpos($R,"'")!==false) { $R = str_replace("'",'&apos;',$R); }
    if (strpos($R,'"')!==false) { $R = str_replace('"','&quot;',$R); }
    if (strpos($R,'>')!==false) { $R = str_replace('>','&gt;',$R); }
    if (strpos($R,'<')!==false) { $R = str_replace('<','&lt;',$R); }
    return $R;
}
/**
 * UBB代码转换
 *
 * @param string $p1
 * @return string
 */
function ubbencode($p1){
    if (strlen($p1)==0) {return ;}
    $T = h2c($p1);
    if (!strncasecmp($R,"\n",1)) {
        $T = substr($R,1);
    }
    $T = preg_replace(array(
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
    ),$T);
    for ($i=1; $i<7; $i++) {
        $T = preg_replace('/\[h'.$i.'\](.+?)\[\/h'.$i.']/i','<h'.$i.'>$1</h'.$i.'>',$T);
    }
    if (strpos($T,'<br/>')!==false && strpos($T,'  |')!==false) {
        $R = array();
        foreach (explode('<br/>',$T) as $v) {
            if (($n=strpos($v,'  |'))!==false) {
                $R[] = substr($v,$n+3);
            }
        }
        $R = implode('<br/>',$R);
    } else {
        $R = $T;
    }

    if (strpos($R,' ')!==false) {
        $R = str_replace(' ','&nbsp;',$R); 
    }
    return $R;
}
// 编辑器
function editor($p1,$p2=array()){
    static $isLoadJs = false;
    $A1 = array(); $p3 = null; $R = null;
    if (is_array($p2) && !empty($p2)) {
        $A1 = $p2;
        $p2 = isset($A1['value']) ? $A1['value'] : null;
    }
    $A1['upimg'] = isset($A1['upimg']) ? $A1['upimg'] : false;
    $A1['upfile'] = isset($A1['upfile']) ? $A1['upfile'] : false;
    $A1['pagebreak'] = isset($A1['pagebreak']) ? $A1['pagebreak'] : false;
    $A1['snapimg'] = isset($A1['snapimg']) ? $A1['snapimg'] : array(0,0);
    $A1['dellink'] = isset($A1['dellink']) ? $A1['dellink'] : array(0,0);
    $A1['resize'] = isset($A1['resize']) ? 'true' : 'false';
    $css = '<style type="text/css">';
    $css.= '.'.$p1.'_editor_button{ width:'.$A1['width'].'; display:table; zoom:100%; margin:3px 0 5px 0;}';
    $css.= '.'.$p1.'_editor_button .fr a{margin-left:8px;}';
    $css.= '</style>';
    $div = $css; $but = $size = null;
    if ($A1['upimg']) { $but.= '<button type="button" onclick="$(\'#'.$p1.'\').Explorer(\'/'.c('UPLOAD_IMAGE_PATH').'\',\'UPLOAD_IMAGE_EXT\');">'.t('system::editor/upimg').'</button>'; }
    if ($A1['upfile']) { $but.= '<button type="button" onclick="$(\'#'.$p1.'\').Explorer(\'/'.c('UPLOAD_FILE_PATH').'\',\'UPLOAD_ALLOW_EXT\');">'.t('system::editor/upfile').'</button>'; }
    if ($A1['pagebreak']) { $but.= '<button type="button" onclick="tinyMCE.get(\''.$p1.'\').execCommand(\'mcePageBreak\',false);">'.t('system::editor/break').'</button>'; }
    if ($A1['snapimg'][0]) {
        $but.= '<input type="checkbox" name="'.$p1.'_attr[snapimg]" id="'.$p1.'_attr[snapimg]" value="1"'.($A1['snapimg'][1]?' checked="checked"':null).' cookie="true" /><label for="'.$p1.'_attr[snapimg]">'.t('system::editor/snapimg').'</label>&nbsp; ';
    }
    if ($A1['dellink'][0]) {
        $but.= '<input type="checkbox" name="'.$p1.'_attr[dellink]" id="'.$p1.'_attr[dellink]" value="1"'.($A1['dellink'][1]?' checked="checked"':null).' cookie="true" /><label for="'.$p1.'_attr[dellink]">'.t('system::editor/dellink').'</label>&nbsp; ';
    }
    if (!empty($but)) {
        $div.= '<div class="'.$p1.'_editor_button">'.$but.'</div>';
    }
    $hl = '<!-- tinyMCE include script -->';
    if (!$isLoadJs) {
        $isLoadJs = true;
        $hl.= '<script src="'.SITE_BASE.'common/editor/tiny_mce/tiny_mce.js" type="text/javascript"></script>';
    }
    $hl.= '<script type="text/javascript">';
    $hl.= 'tinyMCE.init({';
    $hl.= 'mode:"exact",elements:"'.$p1.'",theme:"advanced",relative_urls:false,tab_focus:":prev,:next",language:"'.language().'",';
    if ($A1['toolbar']=='Basic') {
        $hl.= 'plugins:"safari,pagebreak,inlinepopups,noneditable,emotions",';
        $hl.= 'theme_advanced_buttons1:"bold,italic,underline,strikethrough,|,numlist,bullist,|,outdent,indent,blockquote,|,fontselect,|,forecolor,backcolor,|,link,unlink,image,emotions,|,help",theme_advanced_buttons2:"",';
    } else {
        $hl.= 'plugins:"safari,pagebreak,table,inlinepopups,searchreplace,media,fullscreen,noneditable,emotions",';
    }
    $hl.= 'theme_advanced_toolbar_location:"top",theme_advanced_toolbar_align:"left",theme_advanced_statusbar_location:"bottom",theme_advanced_resizing:'.$A1['resize'].',';
    $hl.= 'setup : function(ed) {ed.onInit.add(function(ed) {});}';
    $hl.= '});</script>';
    $hl.= '<textarea style="width:'.$A1['width'].';height:'.$A1['height'].';" id="'.$p1.'" name="'.$p1.'">'.h2c($p2).'</textarea>';
    $R = $hl.$div;
    unset($A1); return $R;
}
/**
 * 分页函数
 *
 * @param string $p1    url中必须包含$特殊字符，用来代替页数
 * @param int    $p2    当前页数
 * @param int    $p3    总页数
 * @param int    $p4    记录总数
 * @return string
 */
function pagelist($p1,$p2,$p3,$p4){
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
/**
 * 多功能导入函数
 *
 * @param string    $p1
 * @param string    $p2
 * @param string    $p3
 * @param bool      $p4
 * @return mixed
 */
function import($p1,$p2='',$p3='.php',$p4=false){
    static $S = array();
    // 已经加载的文件不再加载
    if (isset($S[strtolower($p1.$p2)])) {
        return true;
    } else {
        $S[strtolower($p1.$p2)] = true;
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
/**
 * 语言包列表
 *
 * @param string $p1    语言包名称的缩写
 * @return string
 */
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
/**
 * 多功能配置函数
 *
 * @param string    $p1     参数名
 * @param mixed     $p2     参数值
 * @return mixed
 */
function c($p1=null,$p2=null) {
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
/**
 * 多功能全局变量设置函数
 *
 * @param string    $p1     参数名
 * @param mixed     $p2     参数值
 * @return mixed
 */
function g($p1=null,$p2=null){
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

/**
 * 多语言调用
 *
 * @param string $p1    module::key.key
 * @param array  $p2    需要被替换的变量参数，以数组形式传入
 * @return string
 */
function t($p1,$p2=array()){
    static $T = array(); 
    $p3 = strtolower($p1);
    // 取得模块名
    if (($i = strpos($p3,'::'))!==false) {
        $p4 = substr($p3,0,$i);
        $p3 = substr($p3,($i+2));
    } else {
        $p4 = MODULE;
    }
    if (!isset($T[$p4])) {
        $p5 = sprintf('%s/language/%s',COM_PATH,language());
        if (!file_exists($p5)) {
            $p5 = sprintf('%s/language/%s',COM_PATH,c('LANGUAGE'));
        }
        // 加载系统模块语言包
        if ($M = include_file($p5.'/'.strtolower($p4).'.php')) {
            $T[$p4] = array_merge(array(),$M);
        }
        // key 全部转换成小写
        $T[$p4] = array_change_key_case_deep($T[$p4]);
    }
    
    if (strpos($p3,'/')!==false) {
        $p5 = explode('/',$p3);
        $R  = array();
        foreach ($p5 as $f) {
            if (empty($R)) {
                if (isset($T[$p4][$f])) {
                    $R = $T[$p4][$f];
                } else {
                    $R = $p1;
                    break;
                }
            } else {
                if (is_array($R) && !empty($R)) {
                    if (isset($R[$f])) {
                        $R = $R[$f];
                    } else {
                        $R = $p1;
                        break;
                    }
                } else {
                    $R = $p1;
                }
            }
        }
        
    } else {
        $R = isset($T[$p4][$p3])?$T[$p4][$p3]:$p1;
    }
    
    if (is_array($R)) {
        $R = isset($R['title'])?$R['title']:$R;
    }
    
    if (!empty($p2) && is_array($p2)) {
        $R = call_user_func_array('sprintf',array_merge(array((string)$R),$p2));
    }
    return $R;
}

// property_exists *** *** www.LazyCMS.net *** ***
if (!function_exists('property_exists')) {
    /**
     * 检查对象或类是否具有该属性
     *
     * @param mixed  $p1
     * @param string $p2
     * @return bool
     */
    function property_exists($p1, $p2) {
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
g('CN_PATTERN','/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/');