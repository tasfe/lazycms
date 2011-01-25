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
 */
defined('COM_PATH') or die('Restricted access!');
/**
 * 中文转拼类
 *
 * @author  Lukin <my@lukin.cn>
 * @version $Id$
 */
class PinYin {
    var $fp  = null;
    // 码表
    var $dat = 'pinyin.dat';

    function __construct(){
        $this->dat = dirname(__FILE__).'/'.$this->dat;
        if (is_file($this->dat)) {
            $this->fp = fopen($this->dat, 'rb');
        }
    }

    function PinYin() {
        register_shutdown_function( array(&$this, '__destruct') );
        
        $args = func_get_args();
        call_user_func_array( array(&$this, '__construct'), $args );
    }
    /**
     * 转拼音
     *
     * @param string $str   汉字
     * @param bool $ucfirst 首字母大写
     * @param bool $polyphony 忽略多音节
     * @return string
     */
    function encode($str, $ucfirst=true, $polyphony=true) {
        $ret = ''; $len = mb_strlen($str, 'UTF-8');
        for ($i = 0; $i < $len; $i++) {
            $py = $this->pinyin(mb_substr($str, $i, 1, 'UTF-8'));
            if ($ucfirst && strpos($py, ',') !== false) {
                $pys = explode(',', $py);
                $ret.= implode(',', array_map('ucfirst', ($polyphony ? array_slice($pys, 0, 1) : $pys)));
            } else {
                $ret.= $ucfirst ? ucfirst($py) : $py;
            }
        }
        return $ret;
    }
    /**
     * 汉字转十进制
     *
     * @param string $word
     * @return number
     */
    function char2dec($word) {
        $bins  = '';
        $chars = str_split($word);
        foreach($chars as $char) $bins.= decbin(ord($char));
        $bins = preg_replace('/^.{4}(.{4}).{2}(.{6}).{2}(.{6})$/', '$1$2$3', $bins);
        return bindec($bins);
    }
    /**
     * 单个字转拼音
     *
     * @param string $char  汉字
     * @return string
     */
    function _pinyin($char){
        if (strlen($char) == 3 && is_resource($this->fp)) {
            $offset = $this->char2dec($char);
            // 判断 off 值
            if ($offset >= 0) {
                fseek($this->fp, ($offset - 19968) << 4, SEEK_SET);
                return trim(fread($this->fp, 16));
            }
        }
        return $char;
    }
    
    function __destruct() {
        if (is_resource($this->fp)) {
            fclose($this->fp);
        }
    }
}
/**
 * 取得实例
 *
 * @return $pinyin
 */
function &_pinyin_get_object() {
    static $pinyin;
	if ( is_null($pinyin) )
		$pinyin = new PinYin();
	return $pinyin;
}

if (!function_exists('pinyin')) :
/**
 * 取得拼音
 *
 * @param string $str
 * @param bool $ucfirst 首字母大写
 * @return string
 */
function pinyin($str, $ucfirst=true) {
    $py = _pinyin_get_object();
    return $py->encode($str, $ucfirst);
}
endif;
