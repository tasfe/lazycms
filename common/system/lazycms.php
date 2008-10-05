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
defined('COM_PATH') or die('Restricted access!');
/**
 * LazyCMS 基础类
 *
 * @copyright   Copyright (c) 2007-2008 lazycms.net All rights reserved.
 * @author      Lukin <mylukin@gmail.com>
 * @date        2008-6-18
 */
// LazyCMS *** *** www.LazyCMS.net *** ***
abstract class LazyCMS{
    // init *** *** www.LazyCMS.net *** ***
    private function init(){
        static $R = true;
        if (!$R) { return ; } $R = false;
        if (!defined('E_STRICT')) { define('E_STRICT', 2048); }
        // 加载惯例配置文件
        C(include_file(COM_PATH.'/config.php'));
        // 设置错误级别
        ini_set('display_errors',true);
        error_reporting(E_ALL & ~E_NOTICE);
        // 解析魔术引号
        set_magic_quotes_runtime(0);
        if (get_magic_quotes_gpc()) {
            $R1 = array(& $_GET, & $_POST, & $_COOKIE, & $_REQUEST);
            while (list($k,$v) = each($R1)) {
                $R1[$k] = stripslashes_deep($R1[$k]);
            }
            unset($R1,$k,$v);
        }
        unset($_ENV,$HTTP_ENV_VARS,$HTTP_POST_VARS,$HTTP_GET_VARS,$HTTP_POST_FILES,$HTTP_COOKIE_VARS);
        // 设置系统时区 PHP5支持
        if(function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
        // 是否开启gzip压缩
        if (C('COMPRESS_MODE')) { ob_start('ob_zip'); } else { ob_start(); }
    }
    // exec *** *** www.LazyCMS.net *** ***
    private function exec(){
        // 定义处理错误的函数
        set_error_handler('lazycms_error'); $PHP_DIR = dirname(PHP_FILE);
        // 设置当前模块的常量
        define('MODULE',substr($PHP_DIR,strrpos($PHP_DIR,'/')+1));
        // 获取当前动作
        $action = isset($_REQUEST['action']) ? strtolower($_REQUEST['action']) : null;
        // 导入权限列表
        $config = array();
        $modules= glob(COM_PATH.'/modules/*',GLOB_ONLYDIR);
        foreach ($modules as $v) {
            $module = substr($v,strrpos($v,'/')+1);
            $config[$module] = include_file($v.'/config.php');
        }
        G('CONFIG',$config); unset($config,$modules);
        // 导入模块配置
        import('modules.*.static.*');
        // 在动作之前执行的函数
        if (function_exists('lazy_before')){ lazy_before(); }
        // 执行动作调度
        if (!empty($action)) {
            // 组合出需要执行的函数
            $function = 'lazy_'.$action;
            // 判断函数是否存在
            if (function_exists($function)) {
                $function();
            } else {
                // 输出错误信息
                trigger_error(L('error/function',array('fun'=>$function,'file'=>PHP_FILE),'system'));
            }
        } else {
            if (function_exists('lazy_default')) {
                lazy_default();
            } else {
                // 输出错误信息，提示用户定义lazy_def()函数
                trigger_error(L('error/lazydefault',array('file'=>PHP_FILE),'system'));
            }
        }
        // 在动作之后执行的函数
        if (function_exists('lazy_after')){ lazy_after(); } ob_end_flush();
    }
    // run *** *** www.LazyCMS.net *** ***
    final public function run(){
        LazyCMS::init();
        LazyCMS::exec();
    }
}
