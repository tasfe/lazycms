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
/**
 * 禁用错误报告
 *
 * Set this to error_reporting( E_ALL ) or error_reporting( E_ALL | E_STRICT ) for debugging
 */
// 定义管理后台路径
defined('ADMIN_PATH') or define('ADMIN_PATH',dirname(__FILE__));
// 禁止跳转
define('NO_REDIRECT', true);
// 加载公共文件
include ADMIN_PATH.'/admin.php'; error_reporting(0);
// 获取相关变量
$type = isset($_GET['type']) ? $_GET['type'] : null;
$ver  = isset($_GET['ver']) ? $_GET['ver'] : 0;
$load = isset($_GET['load']) ? $_GET['load'] : null;
$load = preg_replace('/[^a-z0-9,_\-\.]+/i', '', $load);
$ckey = sprintf('loader.%s.%s.%s', $type, $load, $ver);
$load = explode(',', $load);
if (empty($load)) exit;

// 缓存日期，默认365天
$expire = 31536000;

// 输出缓存header
header('Expires: ' . gmdate( "D, d M Y H:i:s", time() + $expire ) . ' GMT');
header("Cache-Control: public, max-age={$expire}");

// 从缓存读取
/*$out = fcache_get($ckey);
if (fcache_not_null($out)) {
    if ($type == 'css') {
        header('Content-Type: text/css; charset=utf-8');
    } elseif ($type == 'js') {
        header('Content-Type: application/x-javascript; charset=utf-8');
    }
    echo $out; exit();
}*/

// 判断类型
$out  = '';
switch ($type) {
	case 'css':
		header('Content-Type: text/css; charset=utf-8');
        $loads = loader_get_files($type, $load);
		foreach ($load as $css) {
        	if (isset($loads[$css])) {
        		foreach ($loads[$css] as $src) {
        		    $content = file_get_contents($src) . "\n";
        		    if (!strncasecmp($src,COM_PATH,strlen(COM_PATH))) {
        		    	$content = str_replace('../images/',ROOT.'common/images/',$content);
                        $content = str_replace('../editor/',ROOT.'common/editor/',$content);
        		    } else {
        		    	$content = str_replace('../images/',ADMIN.'images/',$content);
        		    }
        		    $out.= $content;
        		}
        	}
        }
        // 清除注释和回车
        $out = preg_replace('@(\/\*(.+)\*\/)|(\r\n|\n|\t)@sU', '', $out);
		break;
    case 'js':
		header('Content-Type: application/x-javascript; charset=utf-8');
        $loads = loader_get_files($type, $load);
		foreach ($load as $js) {
            if (isset($loads[$js])) {
        		foreach ($loads[$js] as $src) {
        		    $index   = strrpos($src, '.');
        		    $src_min = substr($src, 0, $index).'.min'.substr($src, $index);
                    // min文件是否存在
                    $is_file = is_file($src_min);
        		    // 读取文件
        		    $content = file_get_contents($is_file ? $src_min : $src);
                    // 替换系统常量
        		    if (substr($src,-10) == 'lazycms.js') {
                        $content = preg_replace('/^(\\s*ADMIN).+$/m',"\$1: '".ADMIN."',",$content);
                        $content = preg_replace('/^(\\s*ROOT).+$/m',"\$1: '".ROOT."',",$content);
        		    }
                    // 替换文件上传的路径
                    elseif (substr($src,-9) == 'common.js') {
                        $content = preg_replace('/^(\\s*LazyCMS\.UpLinkExt\\s*).+$/m', "\$1= '".C('UPFILE-Exts')."';", $content);
                        $content = preg_replace('/^(\\s*LazyCMS\.UpImgExt\\s*).+$/m', "\$1= '".C('UPIMG-Exts')."';", $content);
                    }
                    // jsmin
                    if (!$is_file && filesize($src) <= 50*1024) $content = jsmin($content);
                    $out.= "/* file:".rel_root($src)." */\n". $content . "\n\n";
        		}
        	}
        }
		break;
}
// 保存数据到缓存
fcache_set($ckey, $out, $expire);
// 输出内容
echo $out;
