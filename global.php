<?php
/**
 * +---------------------------------------------------------------------------+
 * | LL                             LLLL   LL     L   LLLL                 LL  |
 * | LL                            LL   L  LLL   LL  LL   L               LLL  |
 * | LL      LLLL   LLLLL  LL  LL  LL      LLLL LLL  LL          LL  LL    LL  |
 * | LL         LL     LL  LL  LL  LL      L LLL LL  LLLLL       LL  LL    LL  |
 * | LL      LLLLL    LL    LLLL   LL      L  L  LL   LLLLL      LL  LL    LL  |
 * | LL     LL  LL   LL     LLLL   LL      L     LL      LL       LLLL     LL  |
 * | LL     LL  LL  LL       LL    LL   L  L     LL  L   LL       LLLL     LL  |
 * | LLLLLL  LLLLL  LLLLL    LL     LLLL   L     LL   LLLL         LL     LLLL |
 * |                        LL                                                 |
 * |                        LL                                                 |
 * +---------------------------------------------------------------------------+
 * | Copyright (C) 2007-2008 LazyCMS.net All rights reserved.                  |
 * +---------------------------------------------------------------------------+
 * | LazyCMS is free software. This version use Apache License 2.0             |
 * | See LICENSE.txt for copyright notices and details.                        |
 * +---------------------------------------------------------------------------+
 */
/**
 * LazyCMS 公用文件
 *
 * @copyright   Copyright (c) 2007-2008 lazycms.net All rights reserved.
 * @author      Lukin <mylukin@gmail.com>
 * @date        2008-6-17
 */

// 记录开始运行时间
$GLOBALS['_beginTime'] = microtime(true);

// 判断PHP版本不能低于5.0
!version_compare(PHP_VERSION, '5.0', '<') or die('PHP version lower than 5.0, upgrade PHP!<br/>&lt;<a href="http://php.net/downloads.php" target="_blank">http://php.net/downloads.php</a>&gt;');

if (str_replace('\\','/',__FILE__) === $_SERVER["SCRIPT_FILENAME"]) { die('Restricted access!'); }

// 定义网站根目录真实路径
define('LAZY_PATH',dirname(__FILE__));

// 定义内核路径
define('COM_PATH',LAZY_PATH.'/common');

// 开启IE7兼容模式
header('X-UA-Compatible: IE=EmulateIE7');

// 加载系统定义文件
require COM_PATH."/defines.php";

// 加载系统函数库
require COM_PATH.'/functions.php';

// 加载基础类
import('system.lazycms');
import('system.db');
import('system.cookie');
import('system.validate');
import('system.recordset');

// 执行程序
LazyCMS::run();
