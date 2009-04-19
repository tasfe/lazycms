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
 * 
 * 模板标签直接扩展HTML标签实现
 */
class ParseTags{
    var $_HTML = null;
    var $_Value= array();
    /**
     * 载入html代码
     *
     * @param string $p1
     * @return object
     */
    function load($p1){
        $this->_HTML = $p1;
        // 处理 include 标签
        $tags = array(); $tp = LAZY_PATH.'/'.c('TEMPLATE').'/';
        if (preg_match_all('#\{include[^\}]*\/\}#isU',$this->_HTML,$r)) {
            $tags = $r[0];
        }
        foreach ($tags as $tag) {
            $file = sect($tag,'file="','"');
            $this->_HTML = str_replace($tag,read_file($tp.$file),$this->_HTML);
        }
        // 格式化图片、css、js路径
        $this->_HTML = preg_replace('/(<(((script|link|img|input|embed|object|base|area|map|table|td|th|tr).+?(src|href|background))|((param).+?(src|value)))=([^\/]+?))((images|scripts)\/.{0,}?\>)/i','${1}'.SITE_BASE.c('TEMPLATE').'/${10}',$this->_HTML);
        return $this;
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
     * 清空内部数组
     * 
     * 清空通过$this->value()设置的内部数组
     *
     */
    function clear(){
        $this->_Value = array();
    }
    /**
     * 设置对象内的数组变量
     *
     * @param string $p1    数组键名
     * @param mixed $p2     数组值
     */
    function value($p1,$p2=null){
        if (is_array($p1)) {
            $this->_Value = array_merge($this->_Value,$p1);
        } else {
            $this->_Value[$p1] = $p2;
        }
    }
    /**
     * 根据键名取得内部数组的值
     *
     * @param string $p1    键名
     * @return mixed
     */
    function getValue($p1){
        return isset($this->_Value[$p1])?$this->_Value[$p1]:null;
    }
    /**
     * 解析模板
     *
     * @return string   解析后的内容
     */
    function parse(){
        // 解析单行标签
        foreach ($this->getSingleTag() as $t) {
            $this->_HTML = str_replace($t,$this->parseTag($t,'s'),$this->_HTML);
        }
        // 解析块标签
        foreach ($this->getBlockTag() as $t) {
            $this->_HTML = str_replace($t,$this->parseTag($t,'b'),$this->_HTML);
        }
        // 解析系统变量
        foreach ($this->getVar() as $t) {
            $this->_HTML = str_replace($t,$this->parseTag($t,'v'),$this->_HTML);
        }
        return $this->_HTML;
    }
    /**
     * 解析标签
     *
     * @param string $p1    整个标签字符串
     * @param mixed  $p2    解析类型，s:单行标签, b:块标签, v：变量标签
     * @return string
     */
    function parseTag($p1,$p2){
        $R = $p1; $tag = $p1; $isParse = false;
        // 取得标签名称
        switch ($p2) {
            case 's': 
                $tagName = sect($tag,'(\{)','( |\/)');
                break;
            case 'b': 
                $tagName = sect($tag,'{',' ');
                break;
            case 'v': 
                $tagName = sect($tag,'(\{\$)','( |\})');
                break;
            default:
                $tagName = null;
                break;
        }
        // 取不到tagname直接退出
        if (empty($tagName)) { return ; }
        // 取得已安装的模块
        $modules = System::getModules();
        foreach ($modules as $module) {
            include_file(COM_PATH.'/modules/'.$module.'/tags.php');
            $className = ucfirst($module).'_Tags'; if (!class_exists($className)) { continue; }
            // 调用模块内的接口
            $result = call_user_func(array($className, 'ParseTags'),$this,$tagName,$tag);
            if (is_array($result) && $result['CODE']) {
                $R = $result['DATA']; $isParse = true; break;
            }
        }
        // 没有任何解析
        if (!$isParse) {
            $R = $this->getValue($tagName);
        }
        // 解析函数
        $func = sect($tag,'(func\=("|\'))','("|\')');
        if (strlen($func)>0) {
            if (strpos($func,'@me')!==false) { 
                $func = preg_replace("/'@me'|\"@me\"|@me/isU",'$R',$func);
            }
            $R = eval('return '.$func.';');
        }
        return $R;
    }
    /**
     * 解析标签属性
     *
     * @param string $tag       整个标签字符串
     * @param string $tagName   标签名称
     * @return string           解析后的内容
     */
    function parseAtt($tag,$tagName){
        $val = $this->getValue($tagName);
        // size
        $size = sect($tag,'(size\=("|\'))','("|\')');
        if (validate($size,2)) {
            if ((int)len($val) > (int)$size) {
                $R = cnsubstr($val,$size).'...';
            } else{
                $R = $val;
            }
        }
        // datemode
        if (is_numeric($val)) {
            $date = sect($tag,'(mode\=("|\'))','("|\')');
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
        $code = sect($tag,'(code\=("|\'))','("|\')');
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
        
        // 关键字加链接


        return $R;
    }
    /**
     * 取得HTML
     */
    function getHTML(){
        return $this->_HTML;
    }
    /**
     * 取得变量标签
     *
     * @return array
     */
    function getVar(){
        $R = array();
        if (preg_match_all('#\{\$[^\}]*\}#',$this->_HTML,$r)) {
            $R = $r[0];
        }
        return $R;
    }
    /**
     * 取得单行标签
     *
     * @return array
     */
    function getSingleTag(){
        $R = array();
        if (preg_match_all('#\{[^\}]*\/\}#isU',$this->_HTML,$r)) {
            $R = $r[0];
        }
        return $R;
    }
    /**
     * 取得块标签
     *
     * @return array
     */
    function getBlockTag($p1=null){
        if (empty($p1)) {
            $R = array();
            if (preg_match_all('#\{([\w+\:\-]+\b)[^\}]*\}(.+)\{\/\1\}#isU',$this->_HTML,$r)) {
                $R = $r[0];
            }
        } else {
            $R = null;
            if (preg_match_all('#\{('.preg_quote($p1,'/').'\b)[^\}]*\}(.+)\{\/\1\}#isU',$this->_HTML,$r)) {
                $R = array_pop($r[0]);
            }
        }
        return $R;
    }
    /**
     * 取得指定的属性
     *
     * @param bool   $p1   完整标签
     * @param string $p2   属性名称
     * @return string
     */
    function getTagAttr($p1,$p2){
        $R = sect($p1,'('.$p2.'\=("|\'))','("|\')');
        switch ((string)$p2) {
            case 'number':
                $R = validate($R,2)?$R:20;
                break;
            case 'zebra':
                $R = validate($R,2)?$R:1;
                break;
        }
        return $R;
    }
    /**
     * 返回解析结果，提供给各个模块的返回接口
     *
     * @param bool   $isParse   解析成功返回true
     * @param string $result    解析后的字符串
     * @return array
     */
    function R($isParse,$result){
        return array(
            'CODE' => $isParse,
            'DATA' => $result,
        );
    }
    // *** *** www.LazyCMS.net *** *** //
    function close(){
        $this->clear();
        $this->_HTML = null;
    }
}