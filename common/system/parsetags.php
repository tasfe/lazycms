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
    var $_tags = array();
    var $_HTML = null;
    /**
     * 载入html代码
     *
     * @param string $p1
     * @return object
     */
    function load($p1){
        $this->_HTML = $p1;
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
     * 解析HTML模板
     */
    function parseHTML(){
        $this->_include();
    }
    /**
     * 取得HTML
     */
    function getHTML(){
        return $this->_HTML;
    }
    /**
     * 处理 include 标签
     */
    function _include(){
        $tags = array(); $tp = LAZY_PATH.'/'.c('TEMPLATE').'/';
        if (preg_match_all('#\{include[^\}]*\/\}#isU',$this->_HTML,$r)) {
            $tags = $r[0];
        }
        foreach ($tags as $tag) {
            $file = sect($tag,'file="','"');
            $this->_HTML = str_replace($tag,read_file($tp.$file),$this->_HTML);
        }
    }
    /**
     * 取得变量标签
     *
     * @param string $p1
     * @return array
     */
    function getVar($p1){
        $R = array();
        if (preg_match_all('#\{\$[^\}]*\}#',$p1,$r)) {
            $R = $r[0];
        }
        return $R;
    }
    /**
     * 格式化标签
     *
     * @return array
     */
    function _formatTags(){
        // 需要缓存此结果
        $R = array(); $tags = array();
        // 取得块外变量
        if (preg_match_all('#\{\$[^\}]*\}#',$this->_HTML,$r)) {
            $tags['v'] = $r[0];
        }
        // 取得单行标签
        if (preg_match_all('#\{[^\}]*\/\}#isU',$this->_HTML,$r)) {
            $tags['s'] = $r[0];
        }
        // 取得多行标签
        if (preg_match_all('#\{(\$this\.|[\w]+\:[\w]+|\/)[^\}]*[^\/]\}#isU',$this->_HTML,$r)) {
            $tags['m'] = $r[0];
        }
        print_r($tags);
        return $R;
    }
}