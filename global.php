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
 *
 * @author  Lukin <my@lukin.cn>
 * @version $Id$
 */
// 检查环境是否适合做爱做的事
!version_compare(PHP_VERSION, '4.3.3', '<') or die('PHP version lower than 4.3.3, upgrade PHP!<br/>&lt;<a href="http://php.net/downloads.php" target="_blank">http://php.net/downloads.php</a>&gt;');

// 定义项目物理跟路径
define('ABS_PATH',dirname(__FILE__));

// 定义项目物理公共目录
define('COM_PATH',ABS_PATH.'/common');

// 加载项目配置
if (is_file(COM_PATH.'/config.php'))
    require COM_PATH.'/config.php';

// 定义系统常量
require COM_PATH."/defines.php";
// 加载公共函数库
require COM_PATH.'/functions.php';
// 加载数据库访问类
require COM_PATH.'/system/mysql.php';
// 加载验证类
require COM_PATH.'/system/validate.php';
// 加载cookie库
require COM_PATH.'/system/cookie.php';
// 加载文件缓存类
require COM_PATH.'/system/fcache.php';
// 加载本地化语言类库
require COM_PATH.'/system/l10n.php';
// 设置系统时区
time_zone_set(C('Timezone'));
// 开始时间
define('BEGIN_TIME',micro_time(true));
// 处理错误
set_error_handler('handler_error');
// 处理系统变量
if (get_magic_quotes_gpc()) {
    $args = array(& $_GET, & $_POST, & $_COOKIE, & $_FILES, & $_REQUEST);
    while (list($k,$v) = each($args)) {
        $args[$k] = stripslashes_deep($args[$k]);
    }
    unset($args,$k,$v);
}
// 加载默认语言包
load_textdomain(); if (!IS_CLI) C('Compress') ? ob_start('ob_compress') : ob_start();
// 删除没用的系统变量
unset($_ENV,$HTTP_ENV_VARS,$HTTP_SERVER_VARS,$HTTP_SESSION_VARS,$HTTP_POST_VARS,$HTTP_GET_VARS,$HTTP_POST_FILES,$HTTP_COOKIE_VARS);
// 禁止直接访问此文件
str_replace('\\','/',__FILE__) != $_SERVER["SCRIPT_FILENAME"] or die(__('Restricted access!'));