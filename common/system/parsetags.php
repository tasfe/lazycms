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
        $this->_HTML = $this->_encode($p1);
        $this->_tags = $this->_formatTags();
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
     * 获取标签
     *
     * @param string $p1
     * @return array
     */
    function fetch($p1){
        $i = 0; $R = array();
        foreach ($this->_tags as $k=>$v) {
            if (preg_match('#<([\w\-\:]+) [^>]*'.$p1.'="([^"]*)"[^>]*>#i',$v,$r)) { 
                $R[$i]['rules'] = $r[2];
                $R[$i]['tag']   = $this->_getTag($r[1],$k);
                $i++;
            }
        }
        return $R;
    }
    /**
     * 取得标签
     *
     * @param string $tagName
     * @param string $p
     * @return string
     */
    function _getTag($tagName,$p){
        $len = count($this->_tags); $n = 0;
        $pos = array();  $R = array(); $tag = array();
        for ($i=$p; $i<$len; $i++) {
            if (preg_match('#<'.$tagName.'[^>]*>|</'.$tagName.'[^>]*>#i',$this->_tags[$i])) {
                if (preg_match('#</'.$tagName.'[^>]*>#i',$this->_tags[$i])) {
                    $n = $i; break;
                }
                $tag[$tagName][] = $this->_tags[$i]; 
            }
        }
        $j = count($tag[$tagName]); $t = 1;
        for ($i=$n; $i<$len; $i++) {
            if (preg_match('#</'.$tagName.'[^>]*>#i',$this->_tags[$i])) {
                if ($t==$j) {
                    $pos['start'] = $p;
                    $pos['end']   = $i;
                }
                $t++;
            }
        }
        $R['all'] = $this->_decode(implode('',array_slice($this->_tags,$pos['start'],($pos['end']-$pos['start'])+1)));
        $R['cut'] = $this->_decode(implode('',array_slice($this->_tags,$pos['start']+1,($pos['end']-$pos['start'])-1)));
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
        if (preg_match_all('#<[^>]*>#is',$this->_HTML,$r)) {
            $tags = $r[0];
        }
        $len = count($tags);
        for ($i=0;$i<$len;$i++) {
            $next = isset($tags[$i+1]) ? $tags[$i+1] : null;
            $mid  = $this->_mid($tags[$i],$next);
            $R[]  = $tags[$i]; if (empty($next)) { break; }
            if (empty($mid)) { continue; }; $R[] = $mid;
        }
        return $R;
    }
    /**
     * 截取
     *
     * @param string $p1
     * @param string $p2
     * @return string
     */
    function _mid($p1,$p2){
        if (empty($p1) || empty($p2) || empty($this->_HTML)) { return ;}
        static $R1 = null; static $R2 = 0; $R = null;
        if (empty($R1)) { $R1 = $this->_HTML; }
        $p4 = strpos(strtolower($R1),strtolower($p1)); if ($p4===false) { return ; }
        $p5 = strpos(strtolower(substr($R1,-(strlen($R1)-$p4-strlen($p1)))),strtolower($p2));
        if ($p4!==false && $p5!==false) {
            $R  = substr($R1,$p4+strlen($p1),$p5);
            $R1 = substr($R1,$p4+$p5+strlen($p1));
            $R2 = strlen($p2);
        }
        return $R;
    }
    /**
     * 编码 
     */
    function _encode($p1){
        return preg_replace('#<(@[^>]*/)>#iU','&lt;\1&gt;',str_replace('&','&amp;',$p1));
    }
    /**
     * 解码 
     */
    function _decode($p1){
        return preg_replace('#&lt;(@.*/)&gt;#iU','<\1>',str_replace('&amp;','&',$p1));
    }
}