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
 * LazyCMS 系统定义文件
 */
// 系统信息
define('PHP_SAPI_NAME',php_sapi_name());
define('IS_APACHE',strstr($_SERVER['SERVER_SOFTWARE'], 'Apache') || strstr($_SERVER['SERVER_SOFTWARE'], 'LiteSpeed'));
define('IS_CGI',strncmp(PHP_SAPI_NAME,'cgi',3)==0 ? 1 : 0 );
define('IS_IIS',PHP_SAPI_NAME =='isapi' ? 1 : 0);
define('IIS_VER',IS_IIS ? substr($_SERVER["SERVER_SOFTWARE"],strrpos($_SERVER["SERVER_SOFTWARE"],'/')+1) :0);
// 当前文件名
if (!defined('PHP_FILE')) {
    // CGI or FASTCGI 模式
    if (IS_CGI) {
        $R = explode('.php',$_SERVER['PHP_SELF']);
        define('PHP_FILE',rtrim(str_replace($_SERVER['HTTP_HOST'],'',$R[0].'.php'),'/'));
    } else {
        define('PHP_FILE',rtrim($_SERVER['SCRIPT_NAME'],'/'));
    } unset($R);
}
// 路径分隔符
define('SEPARATOR',DIRECTORY_SEPARATOR);
// 网站根目录
define('SITE_BASE',str_replace(str_replace(str_replace(SEPARATOR,'/',LAZY_PATH.SEPARATOR),'/',str_replace(SEPARATOR,'/',$_SERVER["SCRIPT_FILENAME"])),'/',PHP_FILE));
// Http scheme
define('HTTP_SCHEME',(($scheme=isset($_SERVER['HTTPS'])?$_SERVER['HTTPS']:null)=='off' || empty($scheme))?'http':'https');
// Http host
define('HTTP_HOST',HTTP_SCHEME.'://'.$_SERVER['HTTP_HOST']);
// System version
define('LAZY_VERSION','2.0.522');