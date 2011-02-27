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
 * LazyCMS 系统定义文件
 *
 * @author  Lukin <my@lukin.cn>
 * @version $Id$
 */
// System version
define('LAZY_VERSION','2.0');
// 严重错误，停止程序
define('E_LAZY_ERROR',10);
// 警告错误，不停止程序
define('E_LAZY_WARNING',20);
// 提示错误，不停止程序
define('E_LAZY_NOTICE',40);
// 系统信息
if(version_compare(PHP_VERSION,'6.0.0','<') ) {
    @set_magic_quotes_runtime(0);
}
defined('E_STRICT') or define('E_STRICT',2048);
define('IS_CGI',!strncasecmp(PHP_SAPI,'cgi',3) ? 1 : 0 );
define('IS_WIN',DIRECTORY_SEPARATOR == '\\' );
define('IS_CLI',PHP_SAPI=='cli' ?  1 : 0);
// 当前文件名
if(!defined('PHP_FILE')) {
    if (IS_CLI) {
        define('PHP_FILE',$argv[0]);
    } elseif(IS_CGI) {
        //CGI/FASTCGI模式下
        $_temp  = explode('.php',$_SERVER["PHP_SELF"]);
        define('PHP_FILE', rtrim(str_replace($_SERVER["HTTP_HOST"],'',$_temp[0].'.php'),'/'));
    } else {
        define('PHP_FILE', rtrim($_SERVER["SCRIPT_NAME"],'/'));
    }
}
// Web root
defined('ROOT') or define('ROOT', str_replace('\\', '/', substr((($p = dirname(PHP_FILE)) == '/' ? $p : $p . '/'), 0, ($i = strlen(substr(realpath('.'), strlen(ABS_PATH)))) > 0 ? -$i : strlen(dirname(PHP_FILE)) + 1)));
// Http scheme
define('HTTP_SCHEME',(($scheme=isset($_SERVER['HTTPS'])?$_SERVER['HTTPS']:null)=='off' || empty($scheme))?'http':'https');
// 非命令行模式
if (!IS_CLI) {
    // Delete or modify this line may cause the system does not work
    header(sprintf(base64_decode(strtr('LazyCMS/2.0-lukin9BGYXpiyOzTlx0fIahGSLtpC9-=','uaGfik-09.SOx2lM/CyznL','UCMz5JkVimd0ycL3lbQ1OW')),LAZY_VERSION));
    // Http host
    define('HTTP_HOST',HTTP_SCHEME.'://'.$_SERVER['HTTP_HOST']);
} else {
    define('HTTP_HOST','');
}
// 模版目录
defined('TEMPLATE') or define('TEMPLATE','themes');
// 上传目录
defined('MEDIA_PATH') or define('MEDIA_PATH','medias');
