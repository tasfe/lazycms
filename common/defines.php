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
defined('COM_PATH') or die('Restricted access!');
/**
 * LazyCMS 系统定义文件
 */
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
define('IS_WIN',strstr(PHP_OS, 'WIN') ? 1 : 0 );
define('IS_CLI',PHP_SAPI=='cli'? 1   :   0);
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
define('WEB_ROOT',str_replace('\\','/',substr(dirname(PHP_FILE),0,-strlen(substr(realpath('.'),strlen(ABS_PATH)))+1)));
// Http scheme
define('HTTP_SCHEME',(($scheme=isset($_SERVER['HTTPS'])?$_SERVER['HTTPS']:null)=='off' || empty($scheme))?'http':'https');
// Http host
IS_CLI ? define('HTTP_HOST','') : define('HTTP_HOST',HTTP_SCHEME.'://'.$_SERVER['HTTP_HOST']);
// 非命令行模式
if (!IS_CLI) { ob_start(); }
// System version
define('LAZY_VERSION','2.0.1');