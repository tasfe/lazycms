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
 * 完成URL解析、路由和调度
 *
 * @copyright   Copyright (c) 2007-2008 lazycms.net All rights reserved.
 * @author      Lukin <mylukin@gmail.com>
 */
// Dispatcher *** *** www.LazyCMS.net *** ***
class Dispatcher extends Lazy{

    // dispatch *** *** www.LazyCMS.net *** ***
    static function dispatch(){
        $url = getURL();
        if (!empty($_SERVER['PATH_INFO']) && $_SERVER["SCRIPT_NAME"]!=$_SERVER['PATH_INFO']) {
            // 执行url解析
            $I2 = substr($url,strlen(getUriBase()));
            self::parseUrl($I2);
        } else{
            $I2 = $_SERVER["QUERY_STRING"];
            parse_str($I2,$args);
            if (count($args) > 1) {
                // index.php?module=index&action=index&id=1
                if (C('ROUTER_ON')) {
                    self::routerCheck($I2);
                }
                // 保证$_REQUEST正常取值
                $_REQUEST = array_merge($_POST,$_GET);
            } else{
                // 执行url解析
                self::parseUrl($I2);
                // 销毁没用的数组
                $I2 = parent::urldecode($I2);
                if (get_magic_quotes_gpc()) { $I2 = str_replace('%20','_',addslashes($I2)); }
                if (isset($_REQUEST[$I2])) { unset($_REQUEST[$I2]); }
                if (isset($_GET[$I2])) { unset($_GET[$I2]); }
            }
        }
    }

    // getModule *** *** www.LazyCMS.net *** ***
    static function getModule(){
        $module = isset($_POST[C('VAR_MODULE')]) ? $_POST[C('VAR_MODULE')] : (isset($_GET[C('VAR_MODULE')])? $_GET[C('VAR_MODULE')]:null);
        // 如果 $module 为空，则赋予默认值
        if (empty($module)) { $module = C('DEFAULT_MODULE'); }
        return $module;
    }

    // getAction *** *** www.LazyCMS.net *** ***
    static function getAction(){
        $action = isset($_POST[C('VAR_ACTION')]) ? $_POST[C('VAR_ACTION')] : (isset($_GET[C('VAR_ACTION')])? $_GET[C('VAR_ACTION')]:null);
        // 如果 $action 为空，则赋予默认值
        if (empty($action)) { $action = C('DEFAULT_ACTION'); }
        return $action;
    }

    // parseUrl *** *** www.LazyCMS.net *** ***
    private function parseUrl($l1){
        $l1 = ltrim($l1,'/');
        $route = false;
        // 检测路由规则
        if (C('ROUTER_ON')) {
            $route = self::routerCheck($l1);
        }
        if (!$route) {
            $l2 = C('PATH_DEPR');
            $I1 = explode($l2,$l1);
            $_module = array_shift($I1);
            $_action = array_shift($I1);
            $_GET[C('VAR_MODULE')] = empty($_module) ? C('DEFAULT_MODULE') : $_module;
            $_GET[C('VAR_ACTION')] = empty($_action) ? C('DEFAULT_ACTION') : $_action;
            for($i=0,$cnt=count($I1); $i<$cnt; $i++){
                if (isset($I1[$i+1])) {
                    $_GET[$I1[$i]] = (string)$I1[++$i];
                } elseif($i==0) {
                    $_GET[$_GET[C('VAR_ACTION')]] = (string)$I1[$i];
                }
            }
        }
        // 保证$_REQUEST正常取值
        $_REQUEST = array_merge($_POST,$_GET);
    }

    // routerCheck *** *** www.LazyCMS.net *** ***
    private function routerCheck($l1){
        $I1 = false;
        // 搜索路由映射 把路由名称解析为对应的模块和操作
        if(is_file(CORE_PATH.'/custom/routes.php')) {
            $l1     = strtolower($l1);
            $routes = include CORE_PATH.'/custom/routes.php';
            if (is_array($routes)) {
                $routes = array_change_key_case($routes);
            } else {
                $routes = null;
            }
            if(isset($_GET[C('VAR_ROUTER')])) {
                // 存在路由变量
                $routeName = strtolower($_GET[C('VAR_ROUTER')]);
            }else{
                $paths = explode(C('PATH_DEPR'),$l1);
                // 获取路由名称
                $routeName = strtolower(array_shift($paths));
            }
            if(isset($routes[$routeName])) {
                // 读取当前路由名称的路由规则
                // 路由定义格式 routeName=>array(‘模块名称’,’操作名称’,’参数定义’,’额外参数’)
                $route = $routes[$routeName];
                $_GET[C('VAR_MODULE')] = $route[0];
                $_GET[C('VAR_ACTION')] = $route[1];
                
                // 获取当前路由参数对应的变量
                if (isset($paths)) {
                    $vars   = explode(',',$route[2]);
                    $length = count($vars);
                    for($i=0;$i<$length;$i++) {
                        $_GET[$vars[$i]] = array_shift($paths);
                    }
                    // 解析剩余的URL参数
                    $res = preg_replace('@(\w+)\/([^,\/]+)@e', '$_GET[\'\\1\']="\\2";', implode('/',$paths));
                }
                if(isset($route[3])) {
                    // 路由里面本身包含固定参数 形式为 a=111&b=222
                    parse_str($route[3],$params);
                    $_GET = array_merge($_GET,$params);
                }
                $I1 = true;
            } elseif (isset($routes[$routeName.'@'])) {
                // 存在泛路由
                // 路由定义格式 routeName@=>array(
                // array('路由正则1',‘模块名称’,’操作名称’,’参数定义’,’额外参数’),
                // array('路由正则2',‘模块名称’,’操作名称’,’参数定义’,’额外参数’),
                // ...)
                $routeItem = $routes[$routeName.'@'];
                $regx = str_replace($routeName,'',$l1);
                foreach ($routeItem as $route){
                    $rule = $route[0]; // 路由正则
                    if(preg_match($rule,$regx,$matches)) {
                        // 匹配路由定义
                        $_GET[C('VAR_MODULE')] = $route[1];
                        $_GET[C('VAR_ACTION')] = $route[2];
                        // 获取当前路由参数对应的变量
                        $vars   = explode(',',$route[3]);
                        $length = count($vars);
                        for($i=0;$i<$length;$i++) {
                            $_GET[$vars[$i]] = $matches[$i+1];
                        }
                        // 解析剩余的URL参数
                        $res = preg_replace('@(\w+)\/([^,\/]+)@e', '$_GET[\'\\1\']="\\2";', str_replace($matches[0],'',$regx));
                        if(isset($route[4])) {
                            // 路由里面本身包含固定参数 形式为 a=111&b=222
                            parse_str($route[4],$params);
                            $_GET = array_merge($_GET,$params);
                        }
                        $I1 = true;
                        break;
                    }
                }
            }
        }
        return $I1;
    }
}
?>