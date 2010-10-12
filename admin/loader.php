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
 * | Copyright (C) 2007-2008 LazyCMS.com All rights reserved.                  |
 * +---------------------------------------------------------------------------+
 * | LazyCMS is free software. This version use Apache License 2.0             |
 * | See LICENSE.txt for copyright notices and details.                        |
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
define('NO_REDIRECT',true);
// 加载公共文件
require ADMIN_PATH.'/admin.php'; error_reporting(0);
// 加载Loader
require COM_PATH.'/system/loader.php';
// 获取相关变量
$type = isset($_GET['type'])?$_GET['type']:null;
$lang = isset($_GET['lang'])?$_GET['lang']:null;
$lang = preg_replace( '/[^a-z0-9,_-]+/i', '', $lang );
$load = isset($_GET['load'])?$_GET['load']:null;
$load = preg_replace( '/[^a-z0-9,_-]+/i', '', $load );
$load = explode(',', $load);
if ( empty($load) ) exit; $load[] = $lang;

// 缓存日期，默认365天
$expire = 31536000;

// 输出缓存header
header('Expires: ' . gmdate( "D, d M Y H:i:s", time() + $expire ) . ' GMT');
header("Cache-Control: public, max-age={$expire}");

// 判断类型
$out  = '';
switch ($type) {
	case 'css':
		header('Content-Type: text/css');
		// 实例化loader类
        $loader = new StylesLoader($lang);
        $loads  = $loader->loads($load);
        foreach ($load as $css) {
        	if (isset($loads[$css])) {
        		foreach ($loads[$css] as $src) {
        		    $content = get_file($src) . "\n";
        		    if (!strncasecmp($src,COM_PATH,strlen(COM_PATH))) {
        		    	$content = str_replace('../images/',WEB_ROOT.'common/images/',$content);
        		    } else {
        		    	$content = str_replace('../images/',ADMIN_ROOT.'images/',$content);
        		    }
        		    // 清除注释和回车
        			$out.= preg_replace('@(\/\*(.+)\*\/)|(\r\n|\n)@sU','',$content);
        		}
        	}
        }
		break;
    case 'js':
		header('Content-Type: application/x-javascript; charset=utf-8');
		// 实例化loader类
        $loader = new ScriptsLoader($lang);
        $loads  = $loader->loads($load);
        foreach ($load as $js) {
            if (isset($loads[$js])) {
        		foreach ($loads[$js] as $src) {
                    $out.= "/* file:".replace_root($src)." */\n".get_file($src) . "\n\n";
        		}
        	}
        }
        // 添加语言翻译
        $jsL10n = $loads['LazyCMS.L10N'];
        $out = preg_replace('/^(\\s*L10n) *(\:) *(.+)/m','\1: $.extend(\3'.json_encode($jsL10n).'),',$out);
        // 替换系统常量
        $out = preg_replace('/^(\\s*ADMIN_ROOT).+/m',"$1: '".ADMIN_ROOT."',",$out);
        $out = preg_replace('/^(\\s*WEB_ROOT).+/m',"$1: '".WEB_ROOT."',",$out);
		break;
}
// 输出内容
echo $out;

/**
 * 读取文件内容
 *
 * @param string $path
 * @return string
 */
function get_file($path) {

	if ( function_exists('realpath') )
		$path = realpath($path);

	if ( ! $path || ! @is_file($path) )
		return '';

	return @file_get_contents($path);
}
