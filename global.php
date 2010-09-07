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
// 检查环境是否适合做爱做的事
!version_compare(PHP_VERSION, '4.3.3', '<') or die('PHP version lower than 4.3.3, upgrade PHP!<br/>&lt;<a href="http://php.net/downloads.php" target="_blank">http://php.net/downloads.php</a>&gt;');

if (str_replace('\\','/',__FILE__) === $_SERVER["SCRIPT_FILENAME"]) { die('Restricted access!'); }

// 设置正确的错误级别
error_reporting() < E_ALL & ~E_NOTICE or error_reporting(E_ALL & ~E_NOTICE);

// 定义项目物理跟路径
define('ABS_PATH',dirname(__FILE__));

// 定义项目物理公共目录
define('COM_PATH',ABS_PATH.'/common');

// 加载项目配置
require COM_PATH.'/config.php';

// 定义系统常量
require COM_PATH."/defines.php";

// 加载FirePHP类
require COM_PATH.'/system/firephp.php';

// 加载公共函数库
require COM_PATH.'/functions.php';
// 非命令行模式
if (!IS_CLI) {
    // 加载cookie库
    require COM_PATH.'/system/cookie.php';    
}
// 加载验证类
require COM_PATH.'/system/validate.php';
// 加载文件缓存类
require COM_PATH.'/system/fcache.php';

// 处理系统变量
if (get_magic_quotes_gpc()) {
    $args = array(& $_GET, & $_POST, & $_COOKIE, & $_FILES, & $_REQUEST);
    while (list($k,$v) = each($args)) {
        $args[$k] = stripslashes_deep($args[$k]);
    }
    unset($args,$k,$v);
}

// 删除没用的系统变量
unset($_ENV,$HTTP_ENV_VARS,$HTTP_SERVER_VARS,$HTTP_SESSION_VARS,$HTTP_POST_VARS,$HTTP_GET_VARS,$HTTP_POST_FILES,$HTTP_COOKIE_VARS);

