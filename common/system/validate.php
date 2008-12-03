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
 * 验证类
 */
class Validate{
    var $_error = array();
    var $_isVal = false;
    /**
     * 判断页面的请求方法
     *
     * @return bool
     */
    function method(){
        return $_SERVER['REQUEST_METHOD']=='POST' ? true : false;
    }
    /**
     * 验证方法
     *
     * @param string $p1
     * @return object
     */
    function check($p1){
        $R1 = explode(";",$p1);
        foreach ($R1 as $v) {
            if ($this->_check($v)) {
                break;
            }
        }
        return $this;
    }
    /**
     * 私有的验证过程
     *
     * @param string $p1 username|1|L('login/check/name')|2-30
     * @return bool
     */
    function _check($p1){
        $R = null; $R1 = explode("|",$p1);
        $p2 = rawurldecode(isset($_POST[$R1[0]]) ? $_POST[$R1[0]] : null); // POST值
        $p3 = isset($R1[1]) ? (string)$R1[1] : null; // 类型
        $p4 = isset($R1[2]) ? (string)$R1[2] : null; // 提示错误
        $p5 = isset($R1[3]) ? (string)$R1[3] : null;
        switch ((string)$p3) {
            case '0': // 值为空，返回错误
                if (empty($p2)) { $R = $p4; }
                break;
            case '1': // 验证长度
                $p6 = explode("-",$p5);
                if (len($p2) < (int)$p6[0] || len($p2) > (int)$p6[1]) { $R = $p4; }
                break;
            case '2' : // 验证两个值是否相等
                $p6 = isset($_POST[$p5]) ? $_POST[$p5] : null;
                if ($p2 != $p6) { $R = $p4; }
                break;
            case '3' : // 直接返回错误信息
                if ($p5=='false' || $p5==false) {
                    $R = $p4;
                }
                break;
            case '4' : // 数据库验证
                $db = get_conn();
                if (strpos($p5,'#pro#')!==false) {
                    $p6 = str_replace('#pro#',$p2,$p5);
                } else {
                    $p6 = $p5;
                }
                if ($db->result($p6) > 0) { $R = $p4; }
                unset($db);
                break;
            case '5' : // 验证是含有特殊字符
                $p6 = array("'","\\",":","*","?","<",">","|",";",",");
                if (instr("/,.",substr($p2,-1)) || instr("/,.",substr($p2,0,1))){
                    $R = $p4; break;
                }
                foreach ($p6 as $v) {
                    if (strpos($p2,$v)!==false) {
                        $R = $p4; break;
                    }
                }
                break;
            default :
                if (!validate($p2,$p5)) { $R = $p4; }
                break;
        }
        if (empty($R)) {
            return false;
        }
        $this->_setError($R1[0],$R);
        return true;
    }
    /**
     * 设置错误信息
     *
     * @param string $id
     * @param string $text
     */
    function _setError($id,$text){
        static $i = 0;
        $this->isVal(true);
        $this->_error[$i]['id']   = $id;
        $this->_error[$i]['text'] = $text;
        $i++;
    }
    /**
     * 取得验证结果
     *
     * @param string $type
     * @return string
     */
    function fetch(){
        return $this->_error;
    }
    /**
     * 输出验证信息
     *
     * @param string $type
     */
    function out(){
        echo_json('VALIDATE',$this->fetch());
    }
    /**
     * 验证是否正确
     *
     * @param string $val
     * @return bool
     */
    function isVal($val=''){
        if ($val!=='') {
            $this->_isVal = $val;
        } else {
            return $this->_isVal;
        }
    }
}