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
 * 解析DSN
 *
 * @param string $DSN
 *          DSN format: mysql://root:123456@localhost:3306/prefix/dbname
 * @return array|null
 */
function parse_dsn($DSN){
    if (preg_match('/^(\w+):\/\/([^\/:]+)(:([^@]+)?)?@([\w\-\.]+)(:(\d+))?(\/([\w\-]+)\/([\w\-]+)|\/([\w\-]+))$/i',trim($DSN),$info)) {
        return array(
            'host'  => isset($info[5])?$info[5]:null,
            'port'  => isset($info[7])?$info[7]:null,
            'user'  => isset($info[2])?$info[2]:null,
            'pwd'   => isset($info[4])?$info[4]:null,
            'name'  => isset($info[11])?$info[11]:$info[10],
            'scheme'=> isset($info[1])?$info[1]:null,
            'prefix'=> !empty($info[9])?$info[9].'_' : null,
        );
    }
    return null;
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
function format_path($path,$data=null) {
    $py = $id = $md5 = null;
    if (is_array($data)) {
        foreach ($data as $k=>$v) {
            if (empty($v)) continue;
            if ($k=='PY') {
                $py = pinyin($v);
            } elseif ($k=='ID') {
                $id = $v;
            } elseif ($k=='MD5') {
                $md5 = strtoupper(md5($path.$v));
            }
        }
        $path = str_replace(array('%PY','%ID','%MD5'),array($py,$id,$md5),$path);
    }
    return strftime($path);
}
/**
 * 取得数据库连接对象
 *
 * @param string $DSN	    DSN format: mysql://root:123456@localhost:3306/lazy/lazycms
 * @param bool   $pconnect  是否使用长连接，默认不使用
 * @return object
 */
function get_conn($DSN=null,$pconnect=false){
    static $_db = array();
    if (is_null($DSN)) { $DSN = DSN_CONFIG; } $dsn = md5($DSN);
    if (isset($_db[$dsn]) && is_object($_db[$dsn])) return $_db[$dsn];
    if ($config = parse_dsn($DSN)) {
        $config['mode'] = $pconnect;
        require_file(COM_PATH.'/system/mysql.php');
    	$_db[$dsn] = new Mysql();
    	$_db[$dsn]->config($config);
        $_db[$dsn]->connect();
        $_db[$dsn]->select_db();
        if ($_db[$dsn]->ready) {
        	return $_db[$dsn];
        }
    }
    return false;
}
/**
 * 分页函数
 *
 * @param string $url        url中必须包含$特殊字符，用来代替页数
 * @param int    $page      当前页数
 * @param int    $total     总页数
 * @param int    $length    记录总数
 * @return string
 */
function page_list($url,$page,$total,$length){
    $pages = null;
    if (strpos($url,'%24')!==false) { $url = str_replace('%24','$',$url); }
    if (strpos($url,'$')==0 || $length==0) { return ; }
    if ($page > 2) {
        $pages.= '<a href="'.str_replace('$',$page-1,$url).'">&laquo;</a>';
    } elseif ($page==2) {
        $pages.= '<a href="'.str_replace('$',1,$url).'">&laquo;</a>';
    }
    if ($page > 3) {
        $pages.= '<a href="'.str_replace('$',1,$url).'">1</a><span>&#8230;</span>';
    }
    $before = $page-2;
    $after  = $page+7;
    for ($i=$before; $i<=$after; $i++) {
        if ($i>=1 && $i<=$total) {
            if ((int)$i==(int)$page) {
                $pages.= '<span class="active">'.$i.'</span>';
            } else {
                if ($i==1) {
                    $pages.= '<a href="'.str_replace('$',1,$url).'">'.$i.'</a>';
                } else {
                    $pages.= '<a href="'.str_replace('$',$i,$url).'">'.$i.'</a>';
                }
            }
        }
    }
    if ($page < ($total-7)) {
        $pages.= '<span>&#8230;</span><a href="'.str_replace('$',$total,$url).'">'.$total.'</a>';
    }
    if ($page < $total) {
        $pages.= '<a href="'.str_replace('$',$page+1,$url).'">&raquo;</a>';
    }
    return '<div class="pages">'.$pages.'</div>';
}
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
    $css.= '#error-page { width:600px; min-height:250px; background:#fff url('.WEB_ROOT.'common/images/warning-large.png) no-repeat 15px 10px; margin-top:15px; padding-bottom:30px; border:1px solid #B5B5B5; }';
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
/**
 * 系统错误处理函数
 *
 * @param int 	 $errno		错误类型
 * @param string $errstr	错误消息
 * @param string $errfile	错误文件
 * @param int	 $errline	错误行号
 */
function handler_error($errno, $errstr, $errfile, $errline){//E_NOTICE
    if (error_reporting() == 0 || in_array($errno,array(E_STRICT))) return false;
    $errfile = replace_root($errfile); $errstr = replace_root($errstr);
    $string = $file = null; $traces = debug_backtrace();
    foreach($traces as $i=>$trace) {
        $file  = isset($trace['file']) ? replace_root($trace['file']) : $file;
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
                    $vars = print_r($v,true);
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

    $message = "[Message]:\r\n\t{$errstr}\r\n";
    $message.= "[File]:\r\n\t{$errfile} ({$errline})\r\n";
    $message.= "[Trace]:\r\n{$string}\r\n";

    $output  = nl2br(esc_html($message));
    $output  = str_replace("\t",str_repeat('&nbsp; ',2),$output);

    // 记录日志
    $file = ABS_PATH.'/error.log'; error_log($message,3,$file);

    // 输出日志
    ob_end_clean();

    if (!is_ajax()) {
    	$output = error_page(__('System Error'),$output,true);
    }
    exit($output);
}
/**
 * 输出ajax规范的json字符串
 *
 * @param string $code
 * @param mixed  $data
 */
function echo_json($code,$data,$args=null){
    $result = array('CODE'=>$code,'DATA'=>$data);
    $result = is_array($args)?array_merge($result,$args):$result;
    header('Content-Type: application/json; charset=utf-8');
    ob_end_flush(); exit(json_encode($result));
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
 * 在数组或字符串中查找
 *
 * @param mixed  $needle   需要搜索的字符串
 * @param string $haystack 被搜索的数据，字符串用英文“逗号”分割
 * @return bool
 */
function instr($needle,$haystack){
    if (empty($haystack)) { return false; }
    if (!is_array($haystack)) $haystack = explode(',',$haystack);
    return in_array($needle,$haystack) ? true : false;
}
/**
 * 页面跳转
 *
 * @param string $url
 */
function redirect($url,$time=0,$msg='') {
	// 多行URL地址支持
    $url = str_replace(array("\n", "\r"), '', $url);
    if (empty($msg)) $msg = sprintf(__('<a href="%s">%d seconds after goto %s.</a>'),$url,$time,$url);
    if (!headers_sent()) header("Content-Type:text/html; charset=utf-8");
    if (is_ajax()) {
        $data = array('Location' => $url);
        if ($time) $data = array_merge($data,array('Time' => $time));
        if ($time && $msg)  $data = array_merge($data,array('Message' => $msg));
        echo_json('Redirect',$data);
    } else {
    	if (!headers_sent()) {
    		if(0===$time) {
    			header("Location: {$url}");
    		} else {
    			header("Refresh: {$time};url={$url}");
    		}
    	}
		$html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
		$html.= '<html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
		$html.= '<meta http-equiv="refresh" content="0;url='.$url.'" />';
		$html.= '<title>'.__('Redirecting...').'</title>';
		$html.= '<script type="text/javascript" charset="utf-8">self.location.href="'.$url.'";</script>';
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
    $default = $default?$default:WEB_ROOT;
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
function replace_root($path){
    $abs_path = str_replace(DIRECTORY_SEPARATOR,'/',ABS_PATH.DIRECTORY_SEPARATOR);
    $src_path = str_replace(DIRECTORY_SEPARATOR,'/',$path);
    return str_replace($abs_path,WEB_ROOT,$src_path);
}
/**
 * 转义sql语句
 *
 * @param  $str
 * @return string
 */
function esc_sql($str) {
    $db = get_conn();
    return $db->escape($str);
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
    	return array_map('esc_html', $str);
    } else {
        return htmlspecialchars($str);
    }
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
    if (preg_match('/^("(\\.|[^"\n\r])*?"|[,:{}\[\]0-9.\-+Eaeflnr-u \n\r\t])+?$/',$string)) {
    	return true;
    }
    return false;
}
/**
 * 是否需要序列化
 *
 * @param mixed $value
 * @return bool
 */
function is_need_serialize($value){
    $type = strtolower(gettype($value));
    return !instr($type,'integer,double,string,null');
}
/**
 * 是否需要反序列化
 *
 * @param string $type
 * @return bool
 */
function is_need_unserialize($type){
    $type = strtolower($type);
    return !instr($type,'integer,double,string,null');
}
/**
 * stripslashes 扩展
 *
 * @param   array     $params     要处理的数组
 * @return  mixed
 */
function stripslashes_deep($params) {
    return is_array($params) ? array_map('stripslashes_deep', $params) : stripslashes($params);
}
/**
 * 执行压缩
 *
 * @param string $content		需要压缩的内容
 * @param int    $level			压缩等级，默认3，越大压缩级别越高
 * @param bool   $force_gzip	强制使用gzip，默认true
 */
function ob_compress($content,$level=3,$force_gzip=true){
    if (!headers_sent() && !ini_get('zlib.output_compression') && 'ob_gzhandler' != ini_get('output_handler')) {
        header('Vary: Accept-Encoding'); // Handle proxies
        if ( false !== strpos( strtolower($_SERVER['HTTP_ACCEPT_ENCODING']), 'deflate') && function_exists('gzdeflate') && ! $force_gzip ) {
            header('Content-Encoding: deflate');
            $content = gzdeflate( $content, $level );
        } elseif ( false !== strpos( strtolower($_SERVER['HTTP_ACCEPT_ENCODING']), 'gzip') && function_exists('gzencode') ) {
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
 * @param string $content    内容
 * @param string $start      开始代码
 * @param string $end        结束代码
 * @param string $clear      清除内容
 * @return string
 */
function mid($content,$start,$end,$clear=null){
    if (empty($content) || empty($start) || empty($end)) { return ; }
    if ((!strncmp($start,'(',1)) && substr($start,-1)==')') {
        if (preg_match("/{$start}/isU",$content,$args)) {
            $start = $args[0];
        }
    }
    if ((!strncmp($end,'(',1)) && substr($end,-1)==')') {
        if (preg_match("/{$end}/isU",$content,$args)) {
            $end = $args[0];
        }
    }
    $start_len = strlen($start); $result = null;
    $start_pos = strpos(strtolower($content),strtolower($start)); if ($start_pos===false) { return ; }
    $end_pos   = strpos(strtolower(substr($content,-(strlen($content)-$start_pos-$start_len))),strtolower($end));
    if ($start_pos!==false && $end_pos!==false) {
        $result = trim(substr($content,$start_pos+$start_len,$end_pos));
    }
    if (strlen($result)>0 && strlen($clear)>0) {
        if ((!strncmp($clear,'(',1)) && substr($clear,-1)==')') {
            $result = preg_replace("/{$clear}/isU",'',$result);
        } else {
            if (strpos($result,$clear)!==false) {
                $result = str_replace($clear,'',$result);
            }
        }
    }
    return $result;
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
    if (substr($path,-1)!='/') { $path .= '/'; }
    $result = ($ext=='dir') ? glob("{$path}*",GLOB_ONLYDIR) : glob("{$path}*.{{$ext}}",GLOB_BRACE);
    array_walk($result,$process_func);
    return $result;
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
            $val = __(code2lang($v));
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
 * @param mixed  $data
 * @param string $from
 * @param string $to
 * @return mixed
 */
function auto_charset($data,$from,$to){
    $from = strtoupper($from)=='UTF8'? 'UTF-8':$from;
    $to   = strtoupper($to)=='UTF8'? 'UTF-8':$to;
    if ( strtoupper($from) === strtoupper($to) || empty($data) || (is_scalar($data) && !is_string($data)) ){
        //如果编码相同或者非字符串标量则不转换
        return $data;
    }
    if (is_string($data) ) {
        if (function_exists('mb_convert_encoding')) {
            return mb_convert_encoding ($data, $to, $from);
        } elseif(function_exists('iconv')) {
            return iconv($from,$to,$data);
        } else {
            return $data;
        }
    }
    elseif (is_array($data)){
        foreach ( $data as $key => $val ) {
            $_key        = auto_charset($key,$from,$to);
            $data[$_key] = auto_charset($val,$from,$to);
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
        return @mkdir($path, $mode);
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
    if ($dh=@opendir($path)) {
        while (false !== ($file=readdir($dh))) {
            if ($file != "." && $file != "..") {
                $file_path = $path.'/'.$file;
                is_dir($file_path) ? rmdirs($file_path) : @unlink($file_path);
            }
        }
        closedir($dh);
    }
    return @rmdir($path);
}
/**
 * 区分大小写的文件存在判断
 *
 * @param string $filename
 * @return bool
 */
function file_exists_case($filename) {
    if (is_file($filename)) {
        if (IS_WIN) {
            if (basename(realpath($filename)) != basename($filename)) {
                return false;
            }
        }
        return true;
    }
    return false;
}
/**
 * 等同于 include_once
 *
 * @param string    $path    被包含文件的路径
 * @return mixed
 */
function include_file($path) {
    static $_files = array();
    if (file_exists_case($path)) {
        if (!isset($_files[$path]))
            $_files[$path] = include $path;
        return $_files[$path];
    }
    return false;
}
/**
 * 等同于 require_once
 *
 * @param string    $path    被包含文件的路径
 * @return bool
 */
function require_file($path){
    static $_files = array();
    if (file_exists_case($path)) {
        if (!isset($_files[$path])) {
            require $path;
            $_files[$path] = true;
            return true;
        }
        return false;
    }
    return false;
}
/**
 * 给用户生成唯一CODE
 *
 * @param string $data
 * @return string
 */
function authcode($data=null){
    $ipaddr = $_SERVER['REMOTE_ADDR'];
    $randid = strtoupper(md5($data.$ipaddr.$_SERVER['HTTP_USER_AGENT']));
    return guid($randid);
}
/**
 * 生成guid
 *
 * @param  $randid  字符串
 * @return string   guid
 */
function guid($randid=null){
    $randid = is_null($randid)?strtoupper(md5(uniqid(mt_rand(), true))):$randid;
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
 * 取得拼音
 *
 * @param string $string
 * @return string
 */
function pinyin($string) {
    $db = get_conn(); $result = null;
    if (mb_strlen($string,'UTF-8')==1) {
        if (class_exists('FCache')) {
            $prefix = 'pinyin.';
            $en_str = md5($string);
            $result = FCache::get($prefix . $en_str);
        }

        if (empty($result)) {
            $result = $db->result("SELECT `key` FROM `#@_pinyin` WHERE FIND_IN_SET(".esc_sql($string).",`value`) LIMIT 0,1;");
            if ($result) {
                $result = ucfirst($result);
            } else {
                $result = $string;
            }
            
            if (class_exists('FCache')) {
                FCache::set($prefix . $en_str, $result);
            }
        }
    	return $result;
    } else {
        if (preg_match_all('/./u',$string,$args)) {
        	foreach ($args[0] as $arg) {
        	    if (preg_match('/[\x80-\xff]./',$arg)) {
        		    $result .= pinyin($arg);
        	    } else {
        	        $result .= $arg;
        	    }
        	}
        }
        return $result;
    }
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
    $ck_lang = Cookie::get('language');
    $ck_lang = preg_replace( '/[^a-z0-9,_-]+/i', '', $ck_lang );
	return $ck_lang && $ck_lang!='default' ? $ck_lang : LANGUAGE;
}
/**
 * 查询配置
 *
 * @param string|array $key
 * @param mixed $value
 * @return mixed
 */
function C($key,$value=null){
    $db = get_conn(); $ckey = 'cfg.'; $args = null;
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
    // 参数赋值
    if($key && !is_null($value)) {
        $key = $module.'.'.$code;
        // 保存到缓存
        FCache::set($ckey.$key,$value);
        // 获取变量类型
        $var_type = gettype($value);
        // 判断是否需要序列化
        $value = is_need_serialize($value) ? serialize($value) : $value;
        // 查询数据库里是否已经存在
        $length = (int) $db->result(vsprintf("SELECT COUNT(*) FROM `#@_option` WHERE `module`=%s AND `code`=%s",array(esc_sql($module),esc_sql($code))));
        // update
        if ($length > 0) {
        	$db->update('#@_option',array(
        	   'value' => $value,
        	   'type'  => $var_type,
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
                'type'   => $var_type,
            ));
        }
        return true;
    }
    // 取值
    elseif ($key && is_null($value)) {
        $key   = $module.'.'.$code;
        // 先从缓存里取值
        $value = FCache::get($ckey.$key);
        if (empty($value)) {
            $result = $db->query("SELECT * FROM `#@_option` WHERE `module`=%s AND `code`=%s LIMIT 0,1;",array($module,$code));
            if ($data = $db->fetch($result)) {
                $value = is_need_unserialize($data['type']) ? unserialize($data['value']) : $data['value'];
                // 保存到缓存
                FCache::set($ckey.$key,$value);
            }
        }
        // 支持多维数组取值
        if (!empty($args)) {
        	foreach ($args as $arg) {
        		$value = $value[$arg];
        	}
        }
        return $value;
    }
    return null;
}
/**
 * 本地化翻译函数
 *
 * @param string $str
 * @return string
 */
function _x($str,$context=null) {
	static $_l10n;
	if (!is_object($_l10n)) {
		$language = language();
		$mo_file  = COM_PATH."/locale/{$language}.mo";
		if (!file_exists_case($mo_file)) return $str;
		require_file(COM_PATH.'/system/l10n.php');
		$_l10n = new L10n();
	    $ckey = 'L10n.'.$language.'.entries';
	    // 取出缓存
	    $tables = FCache::get($ckey);
	    // 判断文件过期
	    $cache_file = FCache::file($ckey);
	    if (file_exists_case($cache_file)) {
	       defined('DATACACHE_EXPIRE') or define('DATACACHE_EXPIRE',0);
	       // mo文件的修改时间大于缓存文件的过期时间
	       if (filemtime($mo_file)+DATACACHE_EXPIRE > filemtime($cache_file)) {
	           $tables = null;
	       }
	    }
	    // 缓存结果不存在
		if (empty($tables)) {
		    $_l10n->load_file($mo_file);
		    FCache::set($ckey,$_l10n->entries);
		} else {
		    $_l10n->entries = $tables;
		}
	}
	return $_l10n->translate($str,$context);
}
/**
 *  简写的翻译函数
 *
 * @param  $string
 * @return string
 */
function __($string){
    return _x($string);
}

/**
 * 输出字符串
 *
 * @param string $str
 */
function _e($str){
    echo _x($str);
}

if (!function_exists('json_encode')) {
    function json_encode($value){
        static $_json = null;
        if (!$_json) {
            require_file(COM_PATH.'/system/json.php');
            $_json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
        }
        return $_json->encode($value);
    }
}

if (!function_exists('json_decode')) {
    function json_decode($json){
        static $_json = null;
        if (!$_json) {
            require_file(COM_PATH.'/system/json.php');
            $_json = new Services_JSON();
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
                array_push($ret,http_build_query($v, '', $sep, $k, $urlencode));
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

// 兼容 FirePHP 输出函数
if (!function_exists('fb')) {
    function fb(){ }
}