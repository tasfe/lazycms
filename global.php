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
// 检查环境是否适合做爱做的事
!version_compare(PHP_VERSION, '4.3.3', '<') or die('PHP version lower than 4.3.3, upgrade PHP!<br/>&lt;<a href="http://php.net/downloads.php" target="_blank">http://php.net/downloads.php</a>&gt;');

if (str_replace('\\','/',__FILE__) === $_SERVER["SCRIPT_FILENAME"]) { die('Restricted access!'); }

// 屏蔽无聊人的闲言碎语
error_reporting() < E_ALL & ~E_NOTICE or error_reporting(E_ALL & ~E_NOTICE);

// 确定插入的位置，防止插到菊花
define('ABS_PATH',dirname(__FILE__));

// 确定菊花位置
define('COM_PATH',ABS_PATH.'/common');

// 确定用什么姿势来做爱做的事
require COM_PATH.'/config.php';

// 加点料，更容易插入
require COM_PATH."/defines.php";

// debug模式
if (DEBUG_MODE) {
    // 加载FirePHP类
    require COM_PATH.'/system/firephp.php';
}

// 加载配套设施，更容易达到高潮
require COM_PATH.'/functions.php';
// 加载用来确定是几P的功能类
require COM_PATH.'/system/cookie.php';
// 加载防止意外受孕功能类
require COM_PATH.'/system/validate.php';
// 加载高潮缓冲区功能类
require COM_PATH.'/system/fcache.php';

// 设置意外受孕后的处理方法
set_error_handler('handler_error');

// 处理那些不老实的爪子，更深层次的防止意外出现
if (get_magic_quotes_gpc()) {
    $args = array(& $_GET, & $_POST, & $_COOKIE, & $_FILES, & $_REQUEST);
    while (list($k,$v) = each($args)) {
        $args[$k] = stripslashes_deep($args[$k]);
    }
    unset($args,$k,$v);
}

// 将闲杂人等踢出房间
unset($_ENV,$HTTP_ENV_VARS,$HTTP_SERVER_VARS,$HTTP_SESSION_VARS,$HTTP_POST_VARS,$HTTP_GET_VARS,$HTTP_POST_FILES,$HTTP_COOKIE_VARS);