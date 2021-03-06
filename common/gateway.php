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
 * 对外接口
 *
 * @author  Lukin <my@lukin.cn>
 * @version $Id$
 */
// 加载公共文件
include dirname(__FILE__).'/../global.php';
// 加载模块
include_modules();
// 获取参数
$func      = isset($_GET['func']) ? trim($_GET['func']) : null;
$callback  = isset($_GET['callback']) ? $_GET['callback'] : null;
// 组装gateway专用函数
$position  = strpos($func,'_');
$prefix    = substr($func,0,$position);
$name      = substr($func,$position + 1);
$func_name = sprintf('%s_gateway_%s',$prefix,$name);
// 函数存在，可以执行
if (function_exists($func_name)) {
    $result = call_user_func($func_name);
    // ajax 请求
    if (is_ajax() && $result) {
        // jsonp
        if ($callback) {
            printf('%s(%s);', $callback, json_encode($result));
        }
        // 直接输出
        elseif (is_scalar($result)) {
            echo $result;
        }
        // 需要json_encode
        else {
            ajax_echo('Return', $result);
        }
    }
    // HTML输出
    elseif($result) {
        if (is_scalar($result)) {
            echo $result;
        } else {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($result);
        }
    }
} else {
    die(__('Restricted access!'));
}
