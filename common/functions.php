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
 * | Copyright (C) 2007-2010 LazyCMS.com All rights reserved.                  |
 * +---------------------------------------------------------------------------+
 * | LazyCMS is free software. See LICENSE for copyright notices and details.  |
 * +---------------------------------------------------------------------------+
 */
defined('COM_PATH') or die('Restricted access!');
/**
 * LazyCMS 公用函数库
 *
 * @author  Lukin <my@lukin.cn>
 * @version $Id$
 */


/**
 * 解析 PHP info
 *
 * @return array
 */
function parse_phpinfo() {
    ob_start(); phpinfo(INFO_MODULES); $s = ob_get_contents(); ob_end_clean();
    $s = strip_tags($s, '<h2><th><td>');
    $s = preg_replace('/<th[^>]*>([^<]+)<\/th>/', '<info>\1</info>', $s);
    $s = preg_replace('/<td[^>]*>([^<]+)<\/td>/', '<info>\1</info>', $s);
    $t = preg_split('/(<h2[^>]*>[^<]+<\/h2>)/', $s, -1, PREG_SPLIT_DELIM_CAPTURE);
    $r = array(); $count = count($t);
    $p1 = '<info>([^<]+)<\/info>';
    $p2 = '/'.$p1.'\s*'.$p1.'\s*'.$p1.'/';
    $p3 = '/'.$p1.'\s*'.$p1.'/';
    for ($i = 1; $i < $count; $i++) {
        if (preg_match('/<h2[^>]*>([^<]+)<\/h2>/', $t[$i], $matchs)) {
            $name = trim($matchs[1]);
            $vals = explode("\n", $t[$i + 1]);
            foreach ($vals AS $val) {
                if (preg_match($p2, $val, $matchs)) { // 3cols
                    $r[$name][trim($matchs[1])] = array(trim($matchs[2]), trim($matchs[3]));
                } elseif (preg_match($p3, $val, $matchs)) { // 2cols
                    $r[$name][trim($matchs[1])] = trim($matchs[2]);
                }
            }
        }
    }
    return $r;
}
/**
 * 解析路径 /tags/CMS
 *
 * @param string $path
 * @return array
 */
function parse_path($path) {
    $paths  = explode('/', $path);
    $length = count($paths);
    for($i=0; $i<$length; $i++){
        if (isset($paths[$i+1])) {
            $_GET[$paths[$i]] = strval($paths[++$i]);
        }
    }
    $_REQUEST = array_merge($_POST,$_GET);
    return $paths;
}
/**
 * 格式化路径
 *
 * @param string $path  %ID,%PY,%MD5 和 strftime() 支持的参数
 * @param array $data
 *          array(
 *              'ID'  => 1,
 *              'PY'  => '标题',
 *              'MD5' => '文章ID或者其他任何唯一的值',
 *          )
 * @return string
 */
function path_format($path,$data=null) {
    if (is_array($data)) {
        $py = $id = $md5 = null;
        foreach ($data as $k=>$v) {
            if (empty($v)) continue;
            if ($k=='PY') {
                $py = preg_replace('/[^\w\-\.\!\(\)~,#@$%^]/', '-', trim(clear_space(pinyin($v))));
            } elseif ($k=='ID') {
                $id = $v;
            } elseif ($k=='MD5') {
                $md5 = md5($path.$v);
            }
        }
        if ($py)  $path = str_replace(array('%PY','%py'),   $py,  $path);
        if ($id)  $path = str_replace(array('%ID','%id'),   $id,  $path);
        if ($md5) $path = str_replace(array('%MD5','%md5'), $md5, $path);
    }
    return strftime($path);
}
/**
 * W3c Datetime
 *
 * @param int $timestamp
 * @return string
 */
function W3cDate($timestamp=0) {
    if (!$timestamp) $timestamp = time();
    if (version_compare(PHP_VERSION,'5.1.0','>='))
        return date('c', $timestamp);
    
    $date    = date('Y-m-d\TH:i:s', $timestamp);
    $matches = array();
    if (preg_match('/^([\-+])(\d{2})(\d{2})$/', date('O', $timestamp), $matches)) {
        $date .= $matches[1] . $matches[2] . ':' . $matches[3];
    } else {
        $date .= 'Z';
    }
    return $date;

}
/**
 * 时间格式化
 *
 * @param  $format
 * @param  $time
 * @return mixed
 */
function time_format($format,$time) {
    $days   = $time>86400 ? floor($time/86400) : 0;
    $time   = $time - 86400 * $days;
    $hour   = $time>=3600 && $time<86400 ? floor($time/3600) : 0;
    $time   = $time - 86400 * $days - 3600 * $hour;
    $minute = $time>=60 && $time<3600 ? floor($time/60) : 0;
    $time   = $time - 86400 * $days - 3600 * $hour - 60 * $minute;
    $second = $time>0 && $time<60 ? $time : 0;
    $time   = floor(($time - intval($second)) * 100);
    $micro  = $time>0 ? $time : 0;
    $format = str_replace(array('%%ms','%%m','%%H','%%i','%%s'), array('$ms','$m','$H','$i','$s'),$format);
    $result = str_replace(array('%ms','%m','%H','%i','%s'), array(
        sprintf('%02d',$micro),
        sprintf('%02d',$days),
        sprintf('%02d',$hour),
        sprintf('%02d',$minute),
        sprintf('%02d',$second),
    ),$format);
    return str_replace(array('$ms','$m','$H','$i','$s'),array('%ms','%m','%H','%i','%s'),$result);
}
/**
 * 取得数据库连接对象
 */
function &get_conn(){
    global $db;
    if (is_null($db) || get_class($db)=='DBQuery_NOOP') {
        if (!class_exists('DBQuery'))
            include COM_PATH.'/system/dbquery.php';

        if (defined('DB_DSN') && defined('DB_USER') && defined('DB_PWD')) {
            $db = DBQuery::factory(DB_DSN, DB_USER, DB_PWD);
        } else {
            $db = new DBQuery_NOOP();
        }
    }
    return $db;
}
/**
 * 输出编辑器
 *
 * @param  $id
 * @param  $content
 * @param  $options see http://xheditor.com/manual/2#chapter2
 * @return string
 */
function editor($id,$content,$options=null) {
    $defaults = array(
        'width'         => '680',
        'height'        => '280',
        'toobar'        => 'full',
        'emotPath'      => ROOT.'common/images/emots/',
        'editorRoot'    => ROOT.'common/editor/',
        'loadCSS'       => ROOT.'common/css/xheditor.plugins.css',
    );
    
    $options = $options ? array_merge($defaults, $options) : $defaults;

    if (isset($options['tools'])) unset($options['toobar']);

    if (isset($options['toobar'])) {
        switch ($options['toobar']) {
            case 'full':
                $options['tools'] = 'Source,Preview,Pastetext,|,Blocktag,FontSize,Bold,Italic,Underline,Strikethrough,FontColor,'.
                                    'BackColor,Removeformat,|,Align,List,Outdent,Indent,|,Link,Unlink,Img,Flash,Flv,Emot,Table,GoogleMap,Pagebreak,Explorer,Removelink,LocalizedImages,|,'.
                                    'Fullscreen';
                break;
            case 'simple':
                $options['tools'] = 'simple';
                break;
            case 'mini':
                $options['tools'] = 'mini';
                break;
        }
        unset($options['toobar']);
    }

    $botbar = array();
    if (instr('Pagebreak', $options['tools'])) {
        $botbar[] = '<button type="button" onclick="xhe_'.$id.'.exec(\'Pagebreak\');">'.__('Insert Pagebreak').'</button>';
    }
    if (instr('Removelink', $options['tools'])) {
        $botbar[] = '<button type="button" onclick="xhe_'.$id.'.exec(\'Removelink\');">'.__('Remove external links').'</button>';
    }
    if (instr('Explorer', $options['tools'])) {
        $botbar[] = '<button type="button" onclick="xhe_'.$id.'.exec(\'Explorer\');">'.__('Explorer').'</button>';
    }
    if (instr('LocalizedImages', $options['tools'])) {
        $botbar[] = '<input cookie="true" type="checkbox" name="LocalizedImages['.$id.']" id="LocalizedImages_'.$id.'" value="1" /><label for="LocalizedImages_'.$id.'">'.__('Localized Images').'</label>';
    }
    $ht = '<textarea class="text" id="'.$id.'" name="'.$id.'">'.esc_html($content).'</textarea>';
    $ht.= '<script type="text/javascript">';
    $ht.= 'var xhe_'.$id.' = $(\'textarea[name='.$id.']\').xheditor(';
    $ht.= '$.extend('.json_encode($options).',{"onUpload":(typeof(onUpload)==\'function\' ? onUpload : null),';
    $ht.= '"upLinkUrl":LazyCMS.UpLinkUrl, "upLinkExt":LazyCMS.UpLinkExt, "upImgUrl":LazyCMS.UpImgUrl, "upImgExt":LazyCMS.UpImgExt, "upFlashUrl":LazyCMS.UpFlashUrl, "upVideoUrl":LazyCMS.UpVideoUrl, "upVideoExt":LazyCMS.UpVideoExt,';
    $ht.= '"plugins":xhePlugins, "beforeSetSource":xheFilter.SetSource, "beforeGetSource":xheFilter.GetSource';
    $ht.= '}));</script>';
    if (!empty($botbar)) $ht.= '<div class="xhe_botbar">'.implode('', $botbar).'</div>';
    return $ht;
}

if (!function_exists('error_page')) :
/**
 * 错误页面
 *
 * @param string $title
 * @param string $content
 * @param bool $is_full     是否输出完整页面
 * @return string
 */
function error_page($title,$content,$is_full=false) {
    // CSS
    $css = '<style type="text/css">';
    $css.= '#error-page { width:600px; min-height:250px; background:#fff url('.ROOT.'common/images/warning-large.png) no-repeat 15px 10px; margin-top:15px; padding-bottom:30px; border:1px solid #B5B5B5; }';
    $css.= '#error-page { -moz-border-radius:6px; -webkit-border-radius:6px; -khtml-border-radius:6px; border-radius:6px; }';
    $css.= '#error-title { width:500px; border-bottom:solid 1px #B5B5B5; margin:0 0 15px 80px; }';
    $css.= '#error-title h1{ font-size: 25px; margin:10px 0 5px 0; }';
    $css.= '#error-content,#error-buttons { margin:10px 0 10px 80px; }';
    if ($is_full) {
        $css.= 'body { margin:10px 20px; font-family: Verdana; color: #333333; background:#FAFAFA; font-size: 12px; line-height: 1.5; }';
        $css.= '#error-page { width:900px; margin:15px auto; }';
        $css.= '#error-title { width:800px;}';
    }
    $css.= '</style>';
    // Page
    $page = '<div id="error-page">';
    $page.= '<div id="error-title"><h1>'.$title.'</h1></div>';
    $page.= '<div id="error-content">'.$content.'</div>';
    $page.= '<div id="error-buttons"><button type="button" onclick="window.history.back();">'.__('Back').'</button></div>';
    $page.= '</div>';

    if ($is_full) {
        $hl = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
        $hl.= '<html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
        $hl.= '<title>'.$title.' &#8212; LazyCMS</title>';
        $hl.= $css.'</head><body>'.$page;
        $hl.= '</body></html>';
    } else {
    	$hl = $css.$page;
    }
    return $hl;
}
endif;

/**
 * 系统异常处理
 *
 * @param  $errno
 * @param  $errstr
 * @param  $errfile
 * @param  $errline
 * @return bool
 */
function handler_error($errno,$errstr,$errfile,$errline) {
    if (E_STRICT===$errno) return true;
    return throw_error($errstr,$errno,$errfile,$errline);
}
/**
 * 取得错误信息
 *
 * @return array
 */
function last_error($error=true) {
    global $LC_ERRNO, $LC_ERROR,$LC_ERRFILE,$LC_ERRLINE;
    // 清理错误
    if ($error === null)
        $LC_ERRNO = $LC_ERROR = $LC_ERRFILE = $LC_ERRLINE = null;
    // 没有错误
    if (!$LC_ERRNO) return null;
    // 有错误
    return array(
        'errno' => $LC_ERRNO,
        'error' => $LC_ERROR,
        'file'  => $LC_ERRFILE,
        'line'  => $LC_ERRLINE,
    );
}
/**
 * 异常处里函数
 *
 * @param  $errstr          错误消息
 * @param int $errno        异常类型
 * @return bool
 */
function throw_error($errstr,$errno=E_LAZY_NOTICE,$errfile=null,$errline=0){
    global $LC_ERRNO, $LC_ERROR,$LC_ERRFILE,$LC_ERRLINE;
    $string  = $file = null;
    $traces  = debug_backtrace();
    $error   = $traces[0]; unset($traces[0]);
    $errstr  = rel_root($errstr);
    $errfile = rel_root($errfile ? $errfile : $error['file']);
    $errline = rel_root($errline ? $errline : $error['line']);
    $LC_ERRNO = $errno; $LC_ERROR = $errstr; $LC_ERRFILE = $errfile; $LC_ERRLINE = $errline;
    if (error_reporting() === 0) return false;
    foreach($traces as $i=>$trace) {
        $file  = isset($trace['file']) ? rel_root($trace['file']) : $file;
        $line  = isset($trace['line']) ? $trace['line'] : null;
        $class = isset($trace['class']) ? $trace['class'] : null;
        $type  = isset($trace['type']) ? $trace['type'] : null;
        $args  = isset($trace['args']) ? $trace['args'] : null;
        $function  = isset($trace['function']) ? $trace['function'] : null;
        $string   .= "\t#".$i.' ['.date("y-m-d H:i:s").'] '.$file.($line?'('.$line.') ':' ');
        $string   .= $class.$type.$function.'(';
        if (is_array($args)) {
            $arrs = array();
            foreach ($args as $v) {
                if (is_object($v)) {
                    $arrs[] = implode(' ',get_object_vars($v));
                } else {
                    $error_level = error_reporting(0);
                    $vars = print_r($v,true);
                    error_reporting($error_level);
                    while (strpos($vars,chr(32).chr(32))!==false) {
                        $vars = str_replace(chr(32).chr(32),chr(32),$vars);
                    }
                    $arrs[] = $vars;
                }
            }
            $string.= str_replace("\n",'',implode(', ',$arrs));
        }
        $string.=")\r\n";
    }

    $log = "[Message]:\r\n\t{$errstr}\r\n";
    $log.= "[File]:\r\n\t{$errfile} ({$errline})\r\n";
    $log.= $string?"[Trace]:\r\n{$string}\r\n":'';
    // 记录日志
    error_log($log, 3, ABS_PATH.'/error.log');
    // 处里错误
    switch ($errno) {
        case E_LAZY_ERROR:
            // 命令行模式
            if (IS_CLI) $html = $log;
            else {
                // 格式化为HTML
                $html = str_replace("\t",str_repeat('&nbsp; ',2),nl2br(esc_html($log)));
                // 不是ajax请求，格式化成HTML完成页面
                $html = is_ajax() ? $html : error_page(__('System Error'),$html,true);
            }
            // 输出错误信息，并停止程序
            echo $html; exit();
            break;
        case E_LAZY_WARNING: case E_LAZY_NOTICE:
            // 命令行模式
            if (IS_CLI) $html = $log;
            else {
                // 格式化为HTML
                $html = str_replace("\t",str_repeat('&nbsp; ',2),nl2br(esc_html($log)));
                // 不是ajax请求，格式化成HTML完成页面
                $html = is_ajax() ? $html : error_page(__('System Error'),$html,true);
            }
            echo $html;
            break;
        default: break;
    }
    return false;
}
/**
 * jQuery
 * 
 * @param string $js
 * @return string
 */
function jQuery($js) {
    if (!headers_sent())
        header('Content-Type: application/javascript; charset=utf-8');
    return sprintf('jQuery && (function($) { %s })(jQuery);', $js);
}
/**
 * 输出ajax规范的json字符串
 *
 * @param string $code
 * @param mixed $data
 * @param string $eval
 * @return void
 */
function ajax_echo($code,$data,$eval=null){
    if ($code) header('X-LazyCMS-Code: '.$code);
    if ($eval) header('X-LazyCMS-Eval: '.$eval);
    // 申请JSON格式
    if (is_accept_json()) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
    }
    elseif (!is_scalar($data)) {
        echo json_encode($data);
    }
    else {
        echo $data;
    }
    exit();
}
/**
 * ajax confirm
 *
 * @param string $message   提示文字
 * @param string $submit    确定之后执行的代码
 * @param string $cancel    取消之后执行的代码
 * @return void
 */
function ajax_confirm($message,$submit,$cancel=null) {
    if ($submit) header('X-LazyCMS-Submit: '.$submit);
    if ($cancel) header('X-LazyCMS-Cancel: '.$cancel);
    return ajax_echo('Confirm',$message);
}
function ajax_alert($message,$eval=null){
    return ajax_echo('Alert',$message,$eval);
}
function ajax_success($message,$eval=null){
    return ajax_echo('Success',$message,$eval);
}
function ajax_error($message,$eval=null){
    return ajax_echo('Error',$message,$eval);
}
function ajax_return($data) {
    return ajax_echo('Return', $data);
}
/**
 * 检查状态
 *
 * @param bool $state
 * @return string
 */
function test_result($state) {
    return $state ? '<strong style="color:#009900;">&radic;</strong>' : '<strong style="color:#FF0000;">&times;</strong>';
}
/**
 * 取得用户的IP
 *
 * @return string
 */
function get_ip() {
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } elseif (isset($_SERVER['HTTP_X_REAL_IP'])) {
        $ip = $_SERVER['HTTP_X_REAL_IP'];
    } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}
/**
 * 防止浏览器缓存
 */
function no_cache(){
    header("Expires:".date("D,d M Y H:i:s",time()-60*10)." GMT");
    header("Last-Modified:".date("D,d M Y H:i:s")." GMT");
    header("Cache-Control:no-cache,must-revalidate");
    header("Pragma:no-cache");
}
/**
 * 清除空白
 *
 * @param  $content
 * @return mixed
 */
function clear_space($content){
    if (strlen($content)==0) return $content; $r = $content;
    $r = str_replace(array(chr(9),chr(10),chr(13)),'',$r);
    while (strpos($r,chr(32).chr(32))!==false || strpos($r,'&nbsp;')!==false) {
        $r = str_replace(chr(32).chr(32),chr(32),str_replace('&nbsp;',chr(32),$r));
    }
    return $r;
}
/**
 * 在数组或字符串中查找
 *
 * @param mixed  $needle   需要搜索的字符串
 * @param string|array $haystack 被搜索的数据，字符串用英文“逗号”分割或数组
 * @return bool
 */
function instr($needle,$haystack){
    if (empty($haystack)) { return false; }
    if (!is_array($haystack)) $haystack = explode(',',$haystack);
    return in_array($needle,$haystack);
}
/**
 * 页面跳转
 *
 * @param string $url
 * @param int $time
 * @param string $msg
 * @return void
 */
function redirect($url,$time=0,$msg='') {
	// 多行URL地址支持
    $url = str_replace(array("\n", "\r"), '', $url);
    if (empty($msg)) $msg = sprintf(__('<a href="%1$s">%2$d seconds after goto %1$s.</a>'),$url,$time);
    if (!headers_sent()) header("Content-Type:text/html; charset=utf-8");
    if (is_ajax()) {
        $data = array('Location' => $url);
        if ($time) $data = array_merge($data,array('Time' => $time));
        if ($time && $msg)  $data = array_merge($data,array('Message' => $msg));
        ajax_echo('Redirect',$data);
    } else {
    	if (!headers_sent()) {
    		if(0===intval($time)) {
    			header("Location: {$url}");
    		}
    	}
		$html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
		$html.= '<html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
		$html.= '<meta http-equiv="refresh" content="'.$time.';url='.$url.'" />';
		$html.= '<title>'.__('Redirecting...').'</title>';
		$html.= '<script type="text/javascript" charset="utf-8">';
        $html.= 'window.setTimeout(function(){location.replace("'.esc_js($url).'");}, '.($time*1000).');';
        $html.= '</script>';
		$html.= '</head><body>';
		$html.= 0===$time ? null : $msg;
		$html.= '</body></html>';
        exit($html);
    }
}
/**
 * 取得返回地址
 *
 * @param string $default
 * @param bool   $back_server_referer 是否返回来路
 * @return string
 */
function referer($default='',$back_server_referer=true){
    $default = $default?$default:ROOT;
    $referer = isset($_REQUEST['referer'])?$_REQUEST['referer']:null;
    if ($back_server_referer) {
        if(empty($referer) && isset($_SERVER['HTTP_REFERER'])) {
            $referer = $_SERVER['HTTP_REFERER'];
        } else {
            $referer = esc_html($referer);
        }
    } else {
        if(empty($referer)) {
            $referer = $default;
        } else {
            $referer = esc_html($referer);
        }

    }

    if(strpos($referer, 'login.php')!==false) $referer = $default;
    return $referer;
}
/**
 * 替换文件路径以网站根目录开始，防止暴露文件的真实地址
 *
 * @param   string  $path
 * @return  string  返回一个相对当前站点的文件路径
 */
function rel_root($path){
    $abs_path = str_replace(DIRECTORY_SEPARATOR,'/',ABS_PATH.DIRECTORY_SEPARATOR);
    $src_path = str_replace(DIRECTORY_SEPARATOR,'/',$path);
    return str_replace($abs_path, (IS_CLI ? '/' : ROOT), $src_path);
}
/**
 * 转义sql语句
 *
 * @param  $str
 * @return string
 */
function esc_sql($str) {
    return get_conn()->escape($str);
}
/**
 * 转换特殊字符为HTML实体
 *
 * @param   string $str
 * @return  string
 */
function esc_html($str){
    if(empty($str)) {
        return $str;
    } elseif (is_array($str)) {
		$str = array_map('esc_html', $str);
	} elseif (is_object($str)) {
		$vars = get_object_vars($str);
		foreach ($vars as $key=>$data) {
			$str->{$key} = esc_html($data);
		}
	} else {
        $str = htmlspecialchars($str);
    }
    return $str;
}
/**
 * Escapes strings to be included in javascript
 *
 * @param string $str
 * @return mixed
 */
function esc_js($str) {
    return preg_replace('/([^ :!#$%@()*+,-.\x30-\x5b\x5d-\x7e])/e',
        "'\\x'.(ord('\\1')<16? '0': '').dechex(ord('\\1'))", $str);
}
/**
 * 全概率计算
 *
 * @param array $input array('a'=>0.5,'b'=>0.2,'c'=>0.4)
 * @param int $pow 小数点位数
 * @return array key
 */
function random($input, $pow = 2) {
    $much = pow(10, $pow);
    $max  = array_sum($input) * $much;
    $rand = mt_rand(1, $max);
    $base = 0;
    foreach ($input as $k => $v) {
        $min = $base * $much + 1;
        $max = ($base + $v) * $much;
        if ($min <= $rand && $rand <= $max) {
            return $k;
        } else {
            $base += $v;
        }
    }
    return false;
}
/**
 * 随机字符串
 *
 * @param int $length
 * @param string $charlist
 * @return string
 */
function str_rand($length=6,$charlist='0123456789abcdefghijklmnopqrstopwxyz'){
    $charcount = strlen($charlist); $str = null;
    for ($i=0;$i<$length;$i++) {
        $str.= $charlist[mt_rand(0,$charcount-1)];
    }
    return $str;
}
/**
 * 格式化为XML
 *
 * @param string $content
 * @return mixed
 */
function xmlencode($content){
    if (strlen($content) == 0) return $content;
    return str_replace(
        array('&',"'",'"','>','<'),
        array('&amp;','&apos;','&quot;','&gt;','&lt;'),
        $content
    );
}
/**
 * XMLdecode
 *
 * @param string $content
 * @return mixed
 */
function xmldecode($content){
    if (strlen($content) == 0) return $content;
    return str_replace(
        array('&amp;','&apos;','&quot;','&gt;','&lt;'),
        array('&',"'",'"','>','<'),
        $content
    );
}
/**
 * 判断是否为UTF-8编码
 *
 * @param   string
 * @return  bool
 */
function is_utf8($str){
    return preg_match('%^(?:
        [\x09\x0A\x0D\x20-\x7E]             # ASCII
        | [\xC2-\xDF][\x80-\xBF]            # non-overlong 2-byte
        | \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
        | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2} # straight 3-byte
        | \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
        | \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
        | [\xF1-\xF3][\x80-\xBF]{3}         # planes 4-15
        | \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
        )*$%xs',$str);
}
/**
 * 判断是否为ajax提交
 *
 * @return bool
 */
function is_ajax(){
	return (isset($_SERVER['HTTP_X_REQUESTED_WITH'])?$_SERVER['HTTP_X_REQUESTED_WITH']:null)=='XMLHttpRequest';
}
/**
 * 检查数组类型
 *
 * @param array $array
 * @return bool
 */
function is_assoc($array) {
    return (is_array($array) && (0 !== count(array_diff_key($array, array_keys(array_keys($array)))) || count($array)==0));
}
/**
 * 检查值是否已经序列化
 *
 * @param mixed $data Value to check to see if was serialized.
 * @return bool
 */
function is_serialized($data) {
    // if it isn't a string, it isn't serialized
    if (!is_string($data))
        return false;
    $data = trim($data);
    if ('N;' == $data)
        return true;
    if (!preg_match('/^([adObis]):/', $data, $badions))
        return false;
    switch ($badions[1]) {
        case 'a' :
        case 'O' :
        case 's' :
            if (preg_match("/^{$badions[1]}:[0-9]+:.*[;}]\$/s", $data))
                return true;
            break;
        case 'b' :
        case 'i' :
        case 'd' :
            if (preg_match("/^{$badions[1]}:[0-9.E-]+;\$/", $data))
                return true;
            break;
    }
    return false;
}
/**
 * 判断是否请求JSON格式
 * 
 * @return bool
 */
function is_accept_json() {
    return strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/json')!==false;
}
/**
 * 验证是否json
 *
 * @param string $string
 * @return bool
 */
function is_json($string){
    return preg_match('/^("(\\.|[^"\n\r])*?"|[,:{}\[\]0-9.\-+Eaeflnr-u \n\r\t])+?$/',$string);
}
/**
 * stripslashes 扩展
 *
 * @param   array     $value     要处理的数组
 * @return  mixed
 */
function stripslashes_deep($value) {
    if (is_array($value)) {
		$value = array_map('stripslashes_deep', $value);
	} elseif (is_object($value)) {
		$vars = get_object_vars($value);
		foreach ($vars as $key=>$data) {
			$value->{$key} = stripslashes_deep($data);
		}
	} else {
		$value = stripslashes($value);
	}
	return $value;
}
/**
 * 执行压缩
 *
 * @param string $content		需要压缩的内容
 * @param int    $level			压缩等级，默认3，越大压缩级别越高
 * @param bool   $force_gzip	强制使用gzip，默认true
 */
function ob_compress($content,$level=3,$force_gzip=false){
    if (strlen($content)>2048
            && false == headers_sent()
            && false == ini_get('zlib.output_compression')
            && 'ob_gzhandler' != ini_get('output_handler')) {
        header('Vary: Accept-Encoding'); // Handle proxies
        if ( false !== strpos( strtolower($_SERVER['HTTP_ACCEPT_ENCODING']), 'deflate')
                && function_exists('gzdeflate')
                && ! $force_gzip ) {
            header('Content-Encoding: deflate');
            $content = gzdeflate( $content, $level );
        } elseif ( false !== strpos( strtolower($_SERVER['HTTP_ACCEPT_ENCODING']), 'gzip')
                && function_exists('gzencode') ) {
            header('Content-Encoding: gzip');
            $content = gzencode( $content, $level );
        }
        header("Content-Length: ".strlen($content));
    }
    return $content;
}
/**
 * 内容截取，支持正则
 *
 * $start,$end,$clear 支持正则表达式，“/”斜杠开头为正则模式
 * $clear 支持数组
 *
 * @param string $content           内容
 * @param string $start             开始代码
 * @param string $end               结束代码
 * @param string|array $clear      清除内容
 * @return string
 */
function mid($content,$start,$end=null,$clear=null){
    if (empty($content) || empty($start)) return null;
    if ( strncmp($start, '/', 1) === 0) {
        if (preg_match($start, $content, $args)) {
            $start = $args[0];
        }
    }
    if ( $end && strncmp($end, '/', 1) === 0 ) {
        if (preg_match($end, $content, $args)) {
            $end = $args[0];
        }
    }
    $start_len = strlen($start); $result = null;
    $start_pos = stripos($content,$start); if ($start_pos === false) return null;
    $length    = $end===null ? null : stripos(substr($content,-(strlen($content)-$start_pos-$start_len)),$end);
    if ($start_pos !== false) {
        if ($length === null) {
            $result = trim(substr($content, $start_pos + $start_len));
        } else {
            $result = trim(substr($content, $start_pos + $start_len, $length));
        }
    }
    if ($result && $clear) {
        if (is_array($clear)) {
            foreach ($clear as $v) {
                if ( strncmp($v, '/', 1) === 0 ) {
                    $result = preg_replace($v, '', $result);
                } else {
                    if (strpos($result, $v) !== false) {
                        $result = str_replace($v, '', $result);
                    }
                }
            }
        } else {
            if ( strncmp($clear, '/', 1) === 0 ) {
                $result = preg_replace($clear, '', $result);
            } else {
                if (strpos($result,$clear) !== false) {
                    $result = str_replace($clear, '', $result);
                }
            }
        }
    }
    return $result;
}
/**
 * 格式化URL地址
 *
 * 补全url地址，方便采集
 *
 * @param string $base  页面地址
 * @param string $html  html代码
 * @return string
 */
function format_url($base, $html) {
    if (preg_match_all('/<(img|script)[^>]+src=([^\s]+)[^>]*>|<(a|link)[^>]+href=([^\s]+)[^>]*>/iU', $html, $matchs)) {
        $pase_url  = parse_url($base);
        $base_host = sprintf('%s://%s',   $pase_url['scheme'], $pase_url['host']);
        if (($pos=strpos($pase_url['path'], '#')) !== false) {
            $base_path = rtrim(dirname(substr($pase_url['path'], 0, $pos)), '\\/');
        } else {
            $base_path = rtrim(dirname($pase_url['path']), '\\/');
        }
        $base_url = $base_host.$base_path;
        foreach($matchs[0] as $match) {
            if (preg_match('/^(.+(href|src)=)([^ >]+)(.+?)$/i', $match, $args)) {
                $url = trim(trim($args[3],'"'),"'");
                // http 开头，跳过
                if (preg_match('/^(http|https|ftp)\:\/\//i', $url)) continue;
                // 邮件地址和javascript
                if (strncasecmp($url, 'mailto:', 7)===0 || strncasecmp($url, 'javascript:', 11)===0) continue;
                // 绝对路径
                if (strncmp($url, '/', 1) === 0) {
                    $url = $base_host.$url;
                }
                // 相对路径
                elseif (strncmp($url, '../', 3) === 0) {
                    while (strncmp($url, '../', 3) === 0) {
                        $url = substr($url, -(strlen($url)-3));
                        if(strlen($base_path) > 0){
                            $base_path = dirname($base_path);
                        }
                        if ($url == '../') {
                            $url = ''; break;
                        }
                    }
                    $url = $base_host.$base_path.'/'.$url;
                }
                // 当前路径
                elseif (strncmp($url, './', 2) === 0) {
                    $url = $base_url.'/'.substr($url, 2);
                }
                // 其他
                else {
                    $url = $base_url.'/'.$url;
                }
                // 替换标签
                $html = str_replace($match, sprintf('%s"%s"%s', $args[1], $url, $args[4]), $html);
            }
        }
    }
    return $html;
}
/**
 * 格式化大小
 *
 * @param int $bytes
 * @return string
 */
function format_size($bytes){
    if ($bytes == 0) return '-';
    $units = array('Bytes', 'KB', 'MB', 'GB', 'TB', 'PB');
    $i = 0; while ($bytes >= 1024) { $bytes /= 1024; $i++; }
    $precision = $i == 0 ? 0 : 2;
    return number_format(round($bytes, $precision), $precision) . ' ' . $units[$i];
}
/**
 * 截取子字符串
 *
 * @param string $string
 * @param int $start
 * @param int $end
 * @return bool|string
 */
function substring($string, $start, $end=null) {
    if ($end === null) {
        return substr($string, $start);
    } elseif($end > $start) {
        return substr($string, $start, $end - $start);
    } else {
        return false;
    }
}
/**
 * IP地理位置解析
 *
 * @param string $ip
 * @return string
 */
function ip2addr($ip) {
    static $QQWry;
	if ( is_null($QQWry) ) {
        include_file(COM_PATH.'/system/qqwry.php');
        $QQWry = new QQWry(COM_PATH.'/QQWry.Dat');
    }
    return $QQWry->ip2addr($ip);
}
/**
 * 图片缩略图
 *
 * @param string $image
 * @param int $max_w
 * @param int $max_h
 * @param null $toname
 * @return bool|null
 */
function image_thumb($image, $max_w=100, $max_h=100, $toname=null) {
    if (!class_exists('Image')) {
        include_file(COM_PATH.'/system/image.php');
    }
    return Image::thumb($image, $max_w, $max_h, $toname);
}
/**
 * jsmin
 *
 * @param string $js
 * @return string
 */
function jsmin($js) {
    if (!class_exists('JSMin')) {
        include_file(COM_PATH.'/system/jsmin.php');
    }
    return JSMin::minify($js);
}
/**
 * 取得拼音
 *
 * @param string $str
 * @param bool $ucfirst 首字母大写
 * @return string
 */
function pinyin($str, $ucfirst=true) {
    if (!function_exists('_pinyin_get_object')) {
        include_file(COM_PATH.'/system/pinyin.php');
    }
    $pinyin = _pinyin_get_object();
    return $pinyin->encode($str, $ucfirst);
}
/**
 * 分页列表
 *
 * @param string $url   $ 代表当前页数
 * @param string $mode  首页丢弃模式
 * @param int $page     当前页数
 * @param int $total    总页数
 * @param int $length   当前页记录数
 * @return string
 */
function pages_list($url,$mode='$',$page=null,$total=null,$length=null) {
    if (!function_exists('_pages_get_object')) {
        include_file(COM_PATH.'/system/pages.php');
    }
    $pages = _pages_get_object();
    if ($page !== null)   $pages->page   = $page;
    if ($total !== null)  $pages->pages  = $total;
    if ($length !== null) $pages->length = $length;
    return $pages->page_list($url, $mode);
}
/**
 * 查询HTTP状态的描述
 *
 * @param int $code HTTP status code.
 * @return string Empty string if not found, or description if found.
 */
function http_status_desc($code) {
    if (!function_exists('_httplib_get_object')) {
        include_file(COM_PATH.'/system/httplib.php');
    }
    $http = _httplib_get_object();
    return $http->status_desc($code);
}
/**
 * 设置时区
 *
 * @param string $timezone
 * @return bool
 */
function time_zone_set($timezone) {
    if (!function_exists('_timezone_get_object')) {
        include_file(COM_PATH.'/system/timezone.php');
    }
    $zone = _timezone_get_object();
    return $zone->set_zone($timezone);
}
/**
 * 支持的时区分组
 *
 * @return array
 */
function time_zone_group() {
    if (!function_exists('_timezone_get_object')) {
        include_file(COM_PATH.'/system/timezone.php');
    }
    $zone = _timezone_get_object();
    return $zone->get_group();
}
/**
 * 添加回调函数
 *
 * @param string $func
 * @param mixed $args
 * @return bool
 */
function func_add_callback() {
    global $LC_func_callback;

    if (!is_array($LC_func_callback))
        $LC_func_callback = array();
    
    $args = func_get_args();
    $func = array_shift($args);
    
    $LC_func_callback[] = array(
        'func' => $func,
        'args' => $args,
    );
    return true;
}
/**
 * 头像
 *
 * @param string $email
 * @param int $size
 * @param string $default
 * @return string
 */
function get_avatar($email, $size=96, $default='') {
    if ( !empty($email) )
		$email_hash = md5( strtolower( $email ) );

    if ( !empty($email) )
        $host = sprintf( "http://%d.gravatar.com", ( hexdec( $email_hash{0} ) % 2 ) );
    else
        $host = 'http://0.gravatar.com';

    if ( 'mystery' == $default )
        $default = "{$host}/avatar/ad516503a11cd5ca435acc9bb6523536.gif?s={$size}"; // ad516503a11cd5ca435acc9bb6523536 == md5('unknown@gravatar.com')
    elseif ( 'blank' == $default )
		$default = HTTP_HOST.ROOT.'common/images/blank.gif';
    elseif ( empty($email) )
		$default = "{$host}/avatar/00000000000000000000000000000000.gif?d={$default}&amp;s={$size}";

    if ( !empty($email) ) {
        $result = "{$host}/avatar/{$email_hash}.gif?s={$size}&amp;d=".urlencode( $default )."&amp;r=g";
    } else {
        $result = $default;
    }
    return $result;
}
/**
 * 小图标
 *
 * @param string $name
 * @param string $alt
 * @return string
 */
function get_icon($name,$alt='') {
    switch ($name) {
        case 'passed':      $name = 'b8'; break;
        case 'draft':       $name = 'b9'; break;
        case 'enabled':     $name = 'c3'; break;
        case 'disabled':    $name = 'c4'; break;
    }
    return '<img src="'.ROOT.'common/images/blank.gif" class="os '.$name.'" alt="'.$alt.'" />';
}
/**
 * 将目录下的文件或文件夹读取成为数组
 *
 * @param string $path    路径
 * @param string $ext     读取类型
 * @return array
 */
function get_dir_array($path,$ext='*'){
    $path = str_replace(array('.','[',']'),array(DIRECTORY_SEPARATOR,'*','*'),$path);
    if (!strncasecmp($path,'@',1)) {
        $path = str_replace('@',COM_PATH,$path);
    } else {
        $path = ABS_PATH.DIRECTORY_SEPARATOR.$path;
    }
    $process_func = create_function('&$path,$ext','$path=substr($path,strrpos($path,"/")+1);');
    if (!substr_compare($path,'/',strlen($path)-1,1)===false) $path .= '/';
    $result = ($ext=='dir') ? glob("{$path}*",GLOB_ONLYDIR) : glob("{$path}*.{{$ext}}",GLOB_BRACE);
    array_walk($result,$process_func);
    return $result;
}
/**
 * 加载所有模块
 *
 * @return bool
 */
function include_modules() {
    static $loaded; if ($loaded) return true;
    // 加载模版处里类
    include_file(COM_PATH.'/system/template.php');
    // 加载分页类
    include_file(COM_PATH.'/system/pages.php');
    // 查询模块列表
    $modules = get_dir_array('@/module','php');
    foreach ($modules as $file) {
        include_file(COM_PATH.'/module/'.$file);
    }
    // 执行函数回调
    global $LC_func_callback;
    if ($LC_func_callback) {
        foreach ((array)$LC_func_callback as $call) {
            if (function_exists($call['func'])) call_user_func_array($call['func'], $call['args']);
        }
    }
    $loaded = true; return true;
}
/**
 * 格式化下拉框选项
 *
 * @param string $path      路径
 * @param string $ext       类型
 * @param string $html      html字符串 可以使用变量：#value#,#name#,#selected#
 * @param string $selected  selected
 */
function options($path,$ext,$html,$selected=null){
    $type = $ext=='lang' ? 'mo' : $ext;
    $dirs = get_dir_array($path,$type); $result = null;
    if (strpos($html,'%23')!==false) { $html = str_replace('%23','#',$html); }
    foreach ($dirs as $v) {
        if ($ext=='lang') {
            $v   = basename($v,'.mo');
            $val = code2lang($v);
        } else{
            $val = $v;
        }
        $opt = $html;
        if (strpos($opt,'#value#')!==false) { $opt = str_replace('#value#',$v,$opt); }
        if (strpos($opt,'#name#')!==false)  { $opt = str_replace('#name#',$val,$opt); }
        if ($selected==$v) {
            $opt = str_replace('#selected#',' selected="selected"',$opt);
        } else{
            $opt = str_replace('#selected#','',$opt);
        }
        $result.= $opt;
    }
    return $result;
}

/**
 * 自动转换字符集 支持数组转换
 *
 * @param string $from
 * @param string $to
 * @param mixed  $data
 * @return mixed
 */
function iconvs($from,$to,$data){
    $from = strtoupper($from)=='UTF8'? 'UTF-8':$from;
    $to   = strtoupper($to)=='UTF8'? 'UTF-8':$to;
    if ( strtoupper($from) === strtoupper($to) || empty($data) || (is_scalar($data) && !is_string($data)) ){
        //如果编码相同或者非字符串标量则不转换
        return $data;
    }
    if (is_string($data) ) {
        if(function_exists('iconv')) {
            $to = substr($to,-8)=='//IGNORE' ? $to : $to.'//IGNORE';
            return iconv($from,$to,$data);
        } elseif (function_exists('mb_convert_encoding')) {
            return mb_convert_encoding ($data, $to, $from);
        } else {
            return $data;
        }
    }
    elseif (is_array($data)){
        foreach ( $data as $key => $val ) {
            $_key        = iconvs($from,$to,$key);
            $data[$_key] = iconvs($from,$to,$val);
            if ($key != $_key ) {
                unset($data[$key]);
            }
        }
        return $data;
    }
    else {
        return $data;
    }
}
/**
 * 批量创建目录
 *
 * @param string $path   文件夹路径
 * @param int    $mode   权限
 * @return bool
 */
function mkdirs($path, $mode = 0777){
    if (!is_dir($path)) {
        mkdirs(dirname($path), $mode);
        $error_level = error_reporting(0);
        $result      = mkdir($path, $mode);
        error_reporting($error_level);
        return $result;
    }
    return true;
}
/**
 * 删除文件夹
 *
 * @param string $path		要删除的文件夹路径
 * @return bool
 */
function rmdirs($path){
    // 不允许删除根目录
    if ($path=='/' || realpath($path)==ABS_PATH)
        return false;
    $error_level = error_reporting(0);
    if ($dh = opendir($path)) {
        while (false !== ($file=readdir($dh))) {
            if ($file != '.' && $file != '..') {
                $file_path = $path.'/'.$file;
                is_dir($file_path) ? rmdirs($file_path) : unlink($file_path);
            }
        }
        closedir($dh);
    }
    $result = rmdir($path);
    error_reporting($error_level);
    return $result;
}
/**
 * 代替 require_once
 *
 * @param  $path
 * @return bool
 */
function include_file($path){
    static $paths = array();
    if (is_file($path)) {
        if (!isset($paths[$path])) {
            include $path;
            $paths[$path] = true;
            return true;
        }
        return false;
    }
    return false;
}
if (!function_exists('authcode')) :
/**
 * 给用户生成唯一CODE
 *
 * @param string $data
 * @return string
 */
function authcode($data=null){
    return guid(HTTP_HOST.$data.get_ip().$_SERVER['HTTP_USER_AGENT']);
}
endif;
/**
 * 生成guid
 *
 * @param  $randid  字符串
 * @return string   guid
 */
function guid($mix=null){
    if (is_null($mix)) {
        $randid = uniqid(mt_rand(),true);
    } else {
        if (is_object($mix) && function_exists('spl_object_hash')) {
            $randid = spl_object_hash($mix);
        } elseif (is_resource($mix)) {
            $randid = get_resource_type($mix).strval($mix);
        } else {
            $randid = serialize($mix);
        }
    }
    $randid = strtoupper(md5($randid));
    $hyphen = chr(45);
    $result = array();
    $result[] = substr($randid, 0, 8);
    $result[] = substr($randid, 8, 4);
    $result[] = substr($randid, 12, 4);
    $result[] = substr($randid, 16, 4);
    $result[] = substr($randid, 20, 12);
    return implode($hyphen,$result);
}
/**
 * 语言包列表
 *
 * @param string $code    语言包名称的缩写
 * @return string
 */
function code2lang($code){
    $lang = array(
        'af'	=> __('Afrikaans'),
        'sq'	=> __('Albanian'),
        'ar'	=> __('Arabic'),
        'be'	=> __('Belarusian'),
        'bg'	=> __('Bulgarian'),
        'ca'	=> __('Catalan'),
        'zh-CN'	=> __('Chinese (Simplified)'),
        'zh-TW'	=> __('Chinese (Traditional)'),
        'hr'	=> __('Croatian'),
        'cs'	=> __('Czech'),
        'da'	=> __('Danish'),
        'nl'	=> __('Dutch'),
        'en'	=> __('English'),
        'et'	=> __('Estonian'),
        'tl'	=> __('Filipino'),
        'fi'	=> __('Finnish'),
        'fr'	=> __('French'),
        'gl'	=> __('Galician'),
        'de'	=> __('German'),
        'el'	=> __('Greek'),
        'iw'	=> __('Hebrew'),
        'hi'	=> __('Hindi'),
        'hu'	=> __('Hungarian'),
        'is'	=> __('Icelandic'),
        'id'	=> __('Indonesian'),
        'ga'	=> __('Irish'),
        'it'	=> __('Italian'),
        'ja'	=> __('Japanese'),
        'ko'	=> __('Korean'),
        'lv'	=> __('Latvian'),
        'lt'	=> __('Lithuanian'),
        'mk'	=> __('Macedonian'),
        'ms'	=> __('Malay'),
        'mt'	=> __('Maltese'),
        'no'	=> __('Norwegian'),
        'fa'	=> __('Persian'),
        'pl'	=> __('Polish'),
        'pt'	=> __('Portuguese'),
        'ro'	=> __('Romanian'),
        'ru'	=> __('Russian'),
        'sr'	=> __('Serbian'),
        'sk'	=> __('Slovak'),
        'sl'	=> __('Slovenian'),
        'es'	=> __('Spanish'),
        'sw'	=> __('Swahili'),
        'sv'	=> __('Swedish'),
        'th'	=> __('Thai'),
        'tr'	=> __('Turkish'),
        'uk'	=> __('Ukrainian'),
        'vi'	=> __('Vietnamese'),
        'cy'	=> __('Welsh'),
        'yi'	=> __('Yiddish'),
    );
    return isset($lang[$code])?$lang[$code]:$code;
}
/**
 * 取得使用的语言
 *
 * @return string
 */
function language() {
    $ck_lang = cookie_get('language');
    $ck_lang = preg_replace( '/[^a-z0-9,_-]+/i', '', $ck_lang ); 

    if (empty($ck_lang) && isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
        $ck_lang = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
        if (($pos=strpos($ck_lang,',')) !== false) {
            $ck_lang = substr($ck_lang,0,$pos);
        }
        // 需要转换大小写
        if (strtolower($ck_lang) == 'zh-cn') {
            $ck_lang = 'zh-CN';
        } elseif (strtolower($ck_lang) == 'zh-tw') {
            $ck_lang = 'zh-TW';
        }
    } elseif(empty($ck_lang)) {
        $ck_lang = C('Language');
    }
    return $ck_lang;
}
/**
 * 查询配置
 *
 * @param string|array $key
 * @param mixed $value
 * @return mixed
 */
function C($key,$value=null){
    $ckey = 'cfg.'; $args = null;
    // 批量赋值
    if(is_array($key)) {
        foreach ($key as $k=>$v) {
        	C($k,$v);
        }
        return true;
    }
    // 分析key
    if (strpos($key,'.')!==false) {
    	$args   = explode('.',$key);
    	$module = array_shift($args);
    	$code   = array_shift($args);
    } else {
        $module = 'System';
    	$code   = $key;
    }
    $db  = @get_conn();
    $key = $module.'.'.$code;
    // 取值
    if($key && func_num_args()==1) {
        // 数据库链接有问题
        if ($db && !$db->ready) return null;
        // 先从缓存里取值
        $value = fcache_get($ckey.$key);
        if (fcache_is_null($value)) {
            if ($db->is_table('#@_option')) {
                $result = $db->query("SELECT `value` FROM `#@_option` WHERE `module`='%s' AND `code`='%s' LIMIT 1 OFFSET 0;",array($module,$code));
                if ($data = $db->fetch($result)) {
                    $value = is_serialized($data['value']) ? unserialize($data['value']) : $data['value'];
                    // 保存到缓存
                    fcache_set($ckey.$key,$value);
                }
            }
        }
        // 支持多维数组取值
        if (!empty($args) && is_array($value)) {
        	foreach ($args as $arg) {
        		$value = $value[$arg];
        	}
        }
        return $value;
    }
    // 参数赋值
    else {
        // 删除属性
        if (is_null($value)) {
            fcache_delete($key);
            $db->delete('#@_option',array(
                'module' => $module,
                'code'   => $code,
            ));
        } else {
            // 保存到缓存
            fcache_set($ckey.$key,$value);
            // 查询数据库里是否已经存在
            $length = (int) $db->result(vsprintf("SELECT COUNT(`id`) FROM `#@_option` WHERE `module`='%s' AND `code`='%s'",array(esc_sql($module),esc_sql($code))));
            // update
            if ($length > 0) {
                $db->update('#@_option',array(
                   'value' => $value,
                ),array(
                    'module' => $module,
                    'code'   => $code,
                ));
            }
            // insert
            else {
                // 保存到数据库里
                $db->insert('#@_option',array(
                    'module' => $module,
                    'code'   => $code,
                    'value'  => $value,
                ));
            }
        }
        return true;
    }
    return null;
}

if (!function_exists('json_encode')) {
    function json_encode($value){
        global $_json;
        if (!$_json) {
            include_file(COM_PATH.'/system/json.php');
            $_json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
        }
        return $_json->encode($value);
    }
}

if (!function_exists('json_decode')) {
    function json_decode($json){
        global $_json;
        if (!$_json) {
            include_file(COM_PATH.'/system/json.php');
            $_json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
        }
        return $_json->decode($json);
    }
}

if (!function_exists('mb_substr')) {
    function mb_substr( $str, $start, $length=null, $encoding='UTF-8' ) {
        if ( !instr( $encoding, 'utf8,utf-8,UTF8,UTF-8' ) ) {
            return is_null( $length )? substr( $str, $start ) : substr( $str, $start, $length);
        }
        if (function_exists('iconv_substr')){
            return iconv_substr($str,$start,$length,$encoding);
        }
        // use the regex unicode support to separate the UTF-8 characters into an array
        preg_match_all( '/./us', $str, $match );
        $chars = is_null( $length )? array_slice( $match[0], $start ) : array_slice( $match[0], $start, $length );
        return implode( '', $chars );
    }
}

if (!function_exists('mb_strlen')) {
    function mb_strlen( $str, $encoding='UTF-8' ) {
        if ( !instr( $encoding, 'utf8,utf-8,UTF8,UTF-8' ) ) {
            return strlen($str);
        }
        if (function_exists('iconv_strlen')){
            return iconv_strlen($str,$encoding);
        }
        // use the regex unicode support to separate the UTF-8 characters into an array
        preg_match_all( '/./us', $str, $match );
        return count($match);
    }
}

if (!function_exists('hash_hmac')){
    function hash_hmac($algo, $data, $key, $raw_output = false) {
        $packs = array('md5' => 'H32', 'sha1' => 'H40');

        if ( !isset($packs[$algo]) )
            return false;

        $pack = $packs[$algo];

        if (strlen($key) > 64)
            $key = pack($pack, $algo($key));
        else if (strlen($key) < 64)
            $key = str_pad($key, 64, chr(0));

        $ipad = (substr($key, 0, 64) ^ str_repeat(chr(0x36), 64));
        $opad = (substr($key, 0, 64) ^ str_repeat(chr(0x5C), 64));

        return $algo($opad . pack($pack, $algo($ipad . $data)));
    }
}

if (!function_exists('http_build_query')) {
    // from php.net (modified by Mark Jaquith to behave like the native PHP5 function)
	function http_build_query($data, $prefix=null, $sep=null, $key='', $urlencode=true) {
		$ret = array();

        foreach ( (array) $data as $k => $v ) {
            if ( $urlencode)
                $k = urlencode($k);
            if ( is_int($k) && $prefix != null )
                $k = $prefix.$k;
            if ( !empty($key) )
                $k = $key . '%5B' . $k . '%5D';
            if ( $v === NULL )
                continue;
            elseif ( $v === FALSE )
                $v = '0';

            if ( is_array($v) || is_object($v) )
                array_push($ret, http_build_query($v, '', $sep, $k, $urlencode));
            elseif ( $urlencode )
                array_push($ret, $k.'='.urlencode($v));
            else
                array_push($ret, $k.'='.$v);
        }

        if ( NULL === $sep )
            $sep = ini_get('arg_separator.output');

        return implode($sep, $ret);
	}
}

if (!function_exists('substr_compare')) {
   function substr_compare($main_str, $str, $offset, $length = NULL, $case_insensitivity = false) {
       $offset = (int) $offset;

       // Throw a warning because the offset is invalid
       if ($offset >= strlen($main_str)) {
           return throw_error(__('The start position cannot exceed initial string length.'), E_LAZY_WARNING);;
       }

       // We are comparing the first n-characters of each string, so let's use the PHP function to do it
       if ($offset == 0 && is_int($length) && $case_insensitivity === true) {
           return strncasecmp($main_str, $str, $length);
       }

       // Get the substring that we are comparing
       if (is_int($length)) {
           $main_substr = substr($main_str, $offset, $length);
           $str_substr  = substr($str, 0, $length);
       } else {
           $main_substr = substr($main_str, $offset);
           $str_substr  = $str;
       }

       // Return a case-insensitive comparison of the two strings
       if ($case_insensitivity === true) {
           return strcasecmp($main_substr, $str_substr);
       }

       // Return a case-sensitive comparison of the two strings
       return strcmp($main_substr, $str_substr);
   }
}

if (!function_exists('gzdecode')) {
    /**
	 * Opposite of gzencode. Decodes a gzip'ed file.
	 *
	 * @param 	string		compressed data
	 * @return	boolean	True if the creation was successfully
	 */
	function gzdecode($data) {
		$len = strlen($data);
		if ($len < 18 || strncmp($data,"\x1f\x8b",2)) {
			return false;  // Not GZIP format (See RFC 1952)
		}
		$method = ord(substr($data,2,1));  // Compression method
		$flags  = ord(substr($data,3,1));  // Flags
		if ($flags & 31 != $flags) {
			// Reserved bits are set -- NOT ALLOWED by RFC 1952
			return false;
		}
		// NOTE: $mtime may be negative (PHP integer limitations)
		$mtime = unpack("V", substr($data,4,4));
		$mtime = $mtime[1];
		$xfl   = substr($data,8,1);
		$os    = substr($data,8,1);
		$headerlen = 10;
		$extralen  = 0;
		$extra     = "";
		if ($flags & 4) {
			// 2-byte length prefixed EXTRA data in header
			if ($len - $headerlen - 2 < 8) {
				return false;    // Invalid format
			}
			$extralen = unpack("v",substr($data,8,2));
			$extralen = $extralen[1];
			if ($len - $headerlen - 2 - $extralen < 8) {
				return false;    // Invalid format
			}
			$extra = substr($data,10,$extralen);
			$headerlen += 2 + $extralen;
		}

		$filenamelen = 0;
		$filename = "";
		if ($flags & 8) {
			// C-style string file NAME data in header
			if ($len - $headerlen - 1 < 8) {
				return false;    // Invalid format
			}
			$filenamelen = strpos(substr($data,8+$extralen),chr(0));
			if ($filenamelen === false || $len - $headerlen - $filenamelen - 1 < 8) {
				return false;    // Invalid format
			}
			$filename  = substr($data,$headerlen,$filenamelen);
			$headerlen+= $filenamelen + 1;
		}

		$commentlen = 0;
		$comment = "";
		if ($flags & 16) {
			// C-style string COMMENT data in header
			if ($len - $headerlen - 1 < 8) {
				return false;    // Invalid format
			}
			$commentlen = strpos(substr($data,8+$extralen+$filenamelen),chr(0));
			if ($commentlen === false || $len - $headerlen - $commentlen - 1 < 8) {
				return false;    // Invalid header format
			}
			$comment   = substr($data,$headerlen,$commentlen);
			$headerlen+= $commentlen + 1;
		}

		$headercrc = "";
		if ($flags & 1) {
			// 2-bytes (lowest order) of CRC32 on header present
			if ($len - $headerlen - 2 < 8) {
				return false;    // Invalid format
			}
			$calccrc   = crc32(substr($data,0,$headerlen)) & 0xffff;
			$headercrc = unpack("v", substr($data,$headerlen,2));
			$headercrc = $headercrc[1];
			if ($headercrc != $calccrc) {
				return false;    // Bad header CRC
			}
			$headerlen += 2;
		}

		// GZIP FOOTER - These be negative due to PHP's limitations
		$datacrc = unpack("V",substr($data,-8,4));
		$datacrc = $datacrc[1];
		$isize   = unpack("V",substr($data,-4));
		$isize   = $isize[1];

		// Perform the decompression:
		$bodylen = $len-$headerlen-8;
		if ($bodylen < 1) {
			// This should never happen - IMPLEMENTATION BUG!
			return null;
		}
		$body = substr($data,$headerlen,$bodylen);
		$data = "";
		if ($bodylen > 0) {
			switch ($method) {
				case 8:
				// Currently the only supported compression method:
				$data = gzinflate($body);
				break;
				default:
				// Unknown compression method
				return false;
			}
		} else {
			// I'm not sure if zero-byte body content is allowed.
			// Allow it for now...  Do nothing...
		}

		// Verifiy decompressed size and CRC32:
		// NOTE: This may fail with large data sizes depending on how
		//      PHP's integer limitations affect strlen() since $isize
		//      may be negative for large sizes.
		if ($isize != strlen($data) || crc32($data) != $datacrc) {
			// Bad format!  Length or CRC doesn't match!
			return false;
		}
		return $data;
	}
}

if (!function_exists('mime_content_type')) {
    /**
     * Detect MIME Content-type for a file (deprecated)
     *
     * @param string $filename
     * @return string
     */
    function mime_content_type($filename) {
        if (function_exists('finfo_open')) {
            $finfo    = finfo_open(FILEINFO_MIME);
            $mimetype = finfo_file($finfo, $filename);
            finfo_close($finfo);
            return $mimetype;
        } else {
            switch (strtolower(pathinfo($filename, PATHINFO_EXTENSION))) {
                case 'txt':
                    return 'text/plain';
                case 'htm': case 'html': case 'php':
                    return 'text/html';
                case 'css':
                    return 'text/css';
                case 'js':
                    return 'application/javascript';
                case 'json':
                    return 'application/json';
                case 'xml':
                    return 'application/xml';
                case 'swf':
                    return 'application/x-shockwave-flash';
                case 'flv':
                    return 'video/x-flv';

                // images
                case 'png':
                    return 'image/png';
                case 'jpe': case 'jpg': case 'jpeg':
                    return 'image/jpeg';
                case 'gif':
                    return 'image/gif';
                case 'bmp':
                    return 'image/bmp';
                case 'ico':
                    return 'image/x-icon';
                case 'tiff': case 'tif':
                    return 'image/tiff';
                case 'svg': case 'svgz':
                    return 'image/svg+xml';

                // archives
                case 'zip':
                    return 'application/zip';
                case 'rar':
                    return 'application/rar';
                case 'exe': case 'com': case 'bat': case 'dll':
                    return 'application/x-msdos-program';
                case 'msi':
                    return 'application/x-msi';
                case 'cab':
                    return 'application/x-cab';
                case 'qtl':
                    return 'application/x-quicktimeplayer';

                // audio/video
                case 'mp3': case 'mpga': case 'mpega': case 'mp2': case 'm4a':
                    return 'audio/mpeg';
                case 'qt': case 'mov':
                    return 'video/quicktime';
                case 'mpeg': case 'mpg': case 'mpe':
                    return 'video/mpeg';
                case '3gp':
                    return 'video/3gpp';
                case 'mp4':
                    return 'video/mp4';

                // adobe
                case 'pdf':
                    return 'application/pdf';
                case 'psd':
                    return 'image/x-photoshop';
                case 'ai': case 'ps': case 'eps': case 'epsi': case 'epsf': case 'eps2': case 'eps3':
                    return 'application/postscript';
                case 'psd':
                    return 'image/x-photoshop';

                // ms office
                case 'doc': case 'dot':
                    return 'application/msword';
                case 'rtf':
                    return 'application/rtf';
                case 'xls': case 'xlb': case 'xlt':
                    return 'application/vnd.ms-excel';
                case 'ppt': case 'pps':
                    return 'application/vnd.ms-powerpoint';
                case 'xlsx':
                    return 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
                case 'xltx':
                    return 'application/vnd.openxmlformats-officedocument.spreadsheetml.template';
                case 'pptx':
                    return 'application/vnd.openxmlformats-officedocument.presentationml.presentation';
                case 'ppsx':
                    return 'application/vnd.openxmlformats-officedocument.presentationml.slideshow';
                case 'potx':
                    return 'application/vnd.openxmlformats-officedocument.presentationml.template';
                case 'docx':
                    return 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
                case 'dotx':
                    return 'application/vnd.openxmlformats-officedocument.wordprocessingml.template';

                // open office
                case 'odt':
                    return 'application/vnd.oasis.opendocument.text';
                case 'ods':
                    return 'application/vnd.oasis.opendocument.spreadsheet';
                case 'odp':
                    return 'application/vnd.oasis.opendocument.presentation';
                case 'odb':
                    return 'application/vnd.oasis.opendocument.database';
                case 'odg':
                    return 'application/vnd.oasis.opendocument.graphics';
                case 'odi':
                    return 'application/vnd.oasis.opendocument.image';

                default:
                    return 'application/octet-stream';
            }
        }
    }
}

/**
 * 返回当前 Unix 时间戳和微秒数
 *
 * @return float
 */
function micro_time($get_as_float=false){
    if ($get_as_float && version_compare(PHP_VERSION, '5.0.0', '<')) {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }
    return microtime($get_as_float);
}
/**
 * array_splice 保留key
 *
 * @param array &$input
 * @param int $start
 * @param int $length
 * @param mixed $replacement
 * @return array|bool
 */
function array_ksplice(&$input, $start, $length=0, $replacement=null) {
    if (!is_array($replacement)) {
        return array_splice($input, $start, $length, $replacement);
    }
    $keys        = array_keys($input);
    $values      = array_values($input);
    $replacement = (array) $replacement;
    $rkeys       = array_keys($replacement);
    $rvalues     = array_values($replacement);
    array_splice($keys, $start, $length, $rkeys);
    array_splice($values, $start, $length, $rvalues);
    $input = array_combine($keys, $values);
    return $input;
}
