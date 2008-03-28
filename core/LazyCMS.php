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
 * | Copyright (c) 2007-2008 LazyCMS.net All rights reserved.                  |
 * +---------------------------------------------------------------------------+
 * | 许可协议，请查看源代码中附带的 LICENSE.txt 文件，                         |
 * | 或者访问 http://www.lazycms.net/ 获得详细信息。                           |
 * +---------------------------------------------------------------------------+
 * | Author: Lukin <mylukin@gmail.com>                                         |
 * +---------------------------------------------------------------------------+
 */
defined('CORE_PATH') or die('Restricted access!');
/**
 * LazyCMS 核心文件
 *
 * @copyright   Copyright (c) 2007-2008 lazycms.net All rights reserved.
 * @author      Lukin <mylukin@gmail.com>
 */

// 记录开始运行时间
$GLOBALS['_beginTime'] = microtime(true);

// 判断PHP版本不能低于5.0
!version_compare(PHP_VERSION, '5', '<' ) or die('PHP version must not be lower than 5.0, please upgrade your PHP version!<br/>&lt;<a href="http://www.php.net/downloads.php" target="_blank">http://www.php.net/downloads.php</a>&gt;');

// LazyCMS系统目录定义
if (!defined('RUNTIME_PATH')) { define('RUNTIME_PATH',CORE_PATH); }

if (is_file(RUNTIME_PATH.'/~runtime.php')) {
    // 加载核心缓存文件
    // 如果有修改核心文件请删除该缓存
    require RUNTIME_PATH.'/~runtime.php';
} else {
    // 加载系统定义文件
    require CORE_PATH."/common/defines.php";

    // 加载系统函数库
    require CORE_PATH.'/common/funs.php';

    // 加载基础抽象类
    import('system.lazy');
    import('system.lazycms');
    
    // 加载数据库处理类
    import('system.db');
    // 加载异常处理类
    import('system.error');
    // 加载cookie支持类
    import('system.cookie');
    // 加载调度器
    import("system.dispatcher");
    // 是否生成核心缓存
    $cache = ( !defined('CACHE_RUNTIME') || CACHE_RUNTIME == true );
    if ($cache) {
        // 生成核心文件的缓存 去掉文件空白以减少大小
        $_I1  = php_strip_whitespace(CORE_PATH.'/common/defines.php');
        $_I1 .= php_strip_whitespace(CORE_PATH.'/common/funs.php');
        $_I1 .= php_strip_whitespace(CORE_PATH.'/system/lazy.php');
        $_I1 .= php_strip_whitespace(CORE_PATH.'/system/lazycms.php');
        $_I1 .= php_strip_whitespace(CORE_PATH.'/system/db.php');
        $_I1 .= php_strip_whitespace(CORE_PATH.'/system/error.php');
        $_I1 .= php_strip_whitespace(CORE_PATH.'/system/cookie.php');
        $_I1 .= php_strip_whitespace(CORE_PATH.'/system/dispatcher.php');
    }
    
    // 加载兼容函数
    if (version_compare(PHP_VERSION,'5.2.0','<') ) {
        require CORE_PATH.'/common/compat.php';
        if ($cache) {
            $_I1 .= php_strip_whitespace(CORE_PATH.'/common/compat.php');
        }
    }

    // 生成编译文件
    if ($cache) {
        saveFile(RUNTIME_PATH.'/~runtime.php',$_I1);
        unset($_I1);
    }
}