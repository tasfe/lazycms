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
 * 模板解析类
 */
class ParseTags{
    var $HTML = null;
    var $vars = array();
    /**
     * 兼容PHP5模式
     */
    function __construct(){
        // 添加PHP4下的析构函数
        register_shutdown_function(array(&$this, '__destruct'));
    }
    /**
     * 初始化类
     */
    function ParseTags(){
        $this->__construct();
    }
    /**
     * 载入html代码
     *
     * @param string $p1
     * @return object
     */
    function load($p1){
        $html = $p1;
        // 处理 include 标签
        $tags = array(); $tp = LAZY_PATH.'/'.c('TEMPLATE').'/';
        if (preg_match_all('/\{include[^\}]*file=([^\}]*)\}/isU',$html,$r)) {
            $tags = $r[1];
            foreach ($tags as $i=>$tag) {
                $file = trim($tag,'"\' ');
                $html = str_replace($r[0][$i],read_file($tp.$file),$html);
            }
        }
        // 追加jQuery库
        $html = preg_replace('/<\/title[^>]*>/is',"\${0}\n<meta name=\"generator\" content=\"LazyCMS ".LAZY_VERSION."\"/>\n<script type=\"text/javascript\" src=\"".SITE_BASE."common/js/jquery.js?ver=".LAZY_VERSION."\"></script>\n<script type=\"text/javascript\" src=\"".SITE_BASE."common/js/lazycms.library.js?ver=".LAZY_VERSION."\"></script>",$html);
        // 格式化图片、css、js路径
        $html = preg_replace('/(<(((script|link|img|input|embed|object|base|area|map|table|td|th|tr).+?(src|href|background))|((param).+?(src|value)))=([^\/]+?))((images|scripts)\/.{0,}?\>)/i','${1}'.SITE_BASE.c('TEMPLATE').'/${10}',$html);        
        $this->HTML = $html;
        return $html;
    }
    /**
     * 加载html文件
     *
     * @return string
     */
    function loadHTML() {
        $args = func_get_args();
        return $this->load(call_user_func_array('read_file', $args));
    }
    /**
     * 解析标签
     */
    function parse(){
        // TODO:解析列表标签
        $this->HTML = $this->ParseBlocks($this->HTML);
        // TODO:解析变量标签
        $this->HTML = $this->ParseVars($this->HTML);
        return $this->HTML;
    }
    /**
     * 解析块标签
     *
     * @param string $html
     * @return string
     */
    function ParseBlocks($html){
        if (preg_match_all('/\{([\w+\:\-]+\b)[^\}]*\}(.+)\{\/\1\}/isU',$html,$r)) {
            $matches = array_shift($r);
            foreach ($matches as $t) {
                $html = str_replace($t,$this->ParseBlock($t),$html);
            }
        }
        return $html;
    }
    /**
     * 解析单个块标签
     *
     * @param string $tag
     * @return string
     */
    function ParseBlock($tag){
        static $objects = array();
        static $modules = null; $result = null;
        if (preg_match('/\{([\w+\:\-]+\b)[^\}]*\}(.+)\{\/\1\}/isU',$tag,$matches)) {
            $tagHtml  = $matches[0];
            $tagName  = strtolower($matches[1]);
            // 取得已安装的模块
            $modules = $modules?$modules:System::getModules();
            if (in_array($tagName,$modules)) {
               // 加载标签处理类
    	       include_file(COM_PATH."/modules/{$tagName}/tags.php");
    	       // 组合类名
    	       $className = ucfirst($tagName).'_Tags';
    	       // 判断类是否已定义
    	       if (class_exists($className)) {
    	           // 判断run方法是否存在
    	           if (method_exists($className,'run')) {
    	               if (!isset($objects[$tagName])) {
        	               $objects[$tagName] = new $className();
        	               $objects[$tagName]->vars = &$this->vars;
        	               $objects[$tagName]->HTML = &$this->HTML;
    	               }
    	               // 调用run方法处理标签
    	               $result = $objects[$tagName]->run($tagName,$tagHtml);
    	               // 在返回结果上应用自定义函数
    	               $result = $this->applyFunc($tagHtml,$result);
    	           }
    	       }
            }
        }
        unset($objects); return $result;
    }
    /**
     * 解析变量标签
     *
     * @param string $html
     * @return string
     */
    function ParseVars($html){
        if (preg_match_all('/\{\$[^\}]+\}/',$html,$r)) {
            foreach ($r[0] as $t) {
                $html = str_replace($t,$this->ParseVar($t),$html);
            }
        }
        return $html;
    }
    /**
     * 解析单个变量标签
     *
     * @param string $tag
     * @return string
     */
    function ParseVar($tag){
        static $objects = array();
        static $modules = null; $result = null;
        if (preg_match('/\{\$([^\} ]+)[^\}]*\}/',$tag,$matches)) {
            $tagHtml  = $matches[0];
            $tagName  = strtolower($matches[1]);
            // 取得已安装的模块
            $modules = $modules?$modules:System::getModules();
            foreach ($modules as $module) {
            	// 加载标签处理类
                include_file(COM_PATH."/modules/{$module}/tags.php");
                // 组合类名
                $className = ucfirst($module).'_Tags';
                // 判断类是否已定义
                if (class_exists($className)) { 
                    // 判断run方法是否存在
                    if (method_exists($className,'vars')) {
                        if (!isset($objects[$module])) {
                            $objects[$module] = new $className();
                            $objects[$module]->vars = &$this->vars;
                            $objects[$module]->HTML = &$this->HTML;	
                        }
                        // 调用run方法处理标签
                        $result = $objects[$module]->vars($tagName,$tagHtml);
                        if ($result) { break; }
                    }
                }
            }
            // 没有模块处理此变量，则从系统变量里面找出变量值
            if (!$result) {
            	$result = $this->parseAtt($tagHtml,$tagName);
            }
            // 在返回结果上应用自定义函数
            $result = $this->applyFunc($tagHtml,$result);
        }
        unset($objects); return $result;
    }
    /**
     * 应用函数
     *
     * @param string $tag
     * @param string $value
     * @return string
     */
    function applyFunc($tag,$result){
        $result = empty($result)?'':$result;
        if (stripos($tag,'func=')!==false) {
            $func = sect($tag,'func="','"');
            if (strlen($func)>0) {
                if (strpos($func,'@me')!==false) { 
                    $func = preg_replace("/'@me'|\"@me\"|@me/isU",'$result',$func);
                }
                $result = eval('return '.$func.';');
            }
        }
        return $result;
    }
    /**
     * 解析标签属性
     *
     * @param string $tag       整个标签字符串
     * @param string $tagName   标签名称
     * @return string           解析后的内容
     */
    function parseAtt($tag,$tagName){
        $val = $R = $this->V($tagName);
        // size
        if (stripos($tag,'size=')!==false) {
            $size = sect($tag,'size="','"');
            if (validate($size,2)) {
                if ((int)len($val) > (int)$size) {
                    $R = cnsubstr($val,$size).'...';
                } else{
                    $R = $val;
                }
            }
        }
        // datemode
        if (is_numeric($val) && (stripos($tag,'mode=')!==false)) {
            $date = sect($tag,'mode="','"');var_dump($date);
            if (strlen($date) > 0) {
                switch ((string)$date) {
                    case '0':
                        $R = date('Y-n-j G:i:s',$val);
                        break;
                    case '1':
                        $R = date('Y-m-d H:i:s',$val);
                        break;
                    default:
                        $R = date($date,$val);
                        break;
                }
            }
        }
        // code
        if (stripos($tag,'code=')!==false) {
            $code = sect($tag,'code="','"');
            if (strlen($R) > 0) {
                switch (strtolower($code)) {
                    case 'javascript': case 'js':
                        $R = t2js($R);
                        break;
                    case 'xmlencode': case 'xml':
                        $R = xmlencode($R);
                        break;
                    case 'urlencode': case 'url':
                        $R = rawurlencode($R);
                        break;
                    case 'htmlencode':
                        $R = h2c($R);
                        break;
                }
            }
        }
        
        // 关键字加链接


        return $R;
    }
    /**
     * 清空内部数组
     * 
     * 清空通过$this->value()设置的内部数组
     *
     */
    function clear(){
        $this->vars = array();
    }
    /**
     * 设置对象内的数组变量
     *
     * @param string $p1    数组键名
     * @param mixed $p2     数组值
     */
    function V($p1=null,$p2=null){
        // 返回对象内数组
        if (empty($p1) && empty($p2)) {
        	return $this->vars;
        }
        // 根据变量key返回变量值
        if (is_string($p1) && empty($p2)) {
        	return isset($this->vars[$p1])?$this->vars[$p1]:null;
        }
        // 内部变量赋值
        if (is_array($p1)) {
            $this->vars = array_merge($this->vars,$p1);
        } else {
            $this->vars[$p1] = $p2;
        }
        return $this->vars;
    }
    /**
     * 类析构函数
     */
    function __destruct(){
        
    }
}