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
 */
defined('COM_PATH') or die('Restricted access!');
/**
 * LazyCMS 验证类
 *
 * @copyright   Copyright (c) 2007-2008 lazycms.net All rights reserved.
 * @author      Lukin <mylukin@gmail.com>
 * @date        2008-6-18
 */
// Validate *** *** www.LazyCMS.net *** ***
class Validate {
    private $error = array();
    private $isVal = false;
    // method *** *** www.LazyCMS.net *** ***
    public function method(){
        return $_SERVER['REQUEST_METHOD']=='POST' ? true : false;
    }
    // check *** *** www.LazyCMS.net *** ***
    public function check($l1){
        $I2 = explode(";",$l1);
        foreach ($I2 as $v) {
            if ($this->__check($v)) {
                break;
            }
        }
        return $this;
    }
    // __check *** *** www.LazyCMS.net *** ***
    private function __check($l1){
        // username|1|L('login/check/name')|2-30
        $I1 = null; $I2 = explode("|",$l1);
        $l2 = rawurldecode(isset($_POST[$I2[0]]) ? $_POST[$I2[0]] : null); // POST值
        $l3 = isset($I2[1]) ? (string)$I2[1] : null; // 类型
        $l4 = isset($I2[2]) ? (string)$I2[2] : null; // 提示错误
        $l5 = isset($I2[3]) ? (string)$I2[3] : null;
        switch ((string)$l3) {
            case '0': // 值为空，返回错误
                if (empty($l2)) { $I1 = $l4; }
                break;
            case '1': // 验证长度
                $l6 = explode("-",$l5);
                if (len($l2) < (int)$l6[0] || len($l2) > (int)$l6[1]) { $I1 = $l4; }
                break;
            case '2' : // 验证两个值是否相等
                $l6 = isset($_POST[$l5]) ? $_POST[$l5] : null;
                if ($l2 != $l6) { $I1 = $l4; }
                break;
            case '3' :
                if ($l5=='false' || $l5==false) {
                    $I1 = $l4;
                }
                break;
            case '4' :
                $db = get_conn();
                if (strpos($l5,'#pro#')!==false) {
                    $l6 = str_replace('#pro#',$l2,$l5);
                } else {
                    $l6 = $l5;
                }
                if ($db->result($l6) > 0) { $I1 = $l4; }
                unset($db);
                break;
            case '5' :
                $l6 = array("'","\\",":","*","?","<",">","|",";",",");
                if (instr("/,.",substr($l2,-1)) || instr("/,.",substr($l2,0,1))){
                    $I1 = $l4; break;
                }
                foreach ($l6 as $v) {
                    if (strpos($l2,$v)!==false) {
                        $I1 = $l4; break;
                    }
                }
                break;
            default :
                if (!validate($l2,$l5)) { $I1 = $l4; }
                break;
        }
        if (empty($I1)) {
            return false;
        }
        $this->setError($I2[0],$I1);
        return true;
    }
    // setError *** *** www.LazyCMS.net *** ***
    public function setError($id,$text){
        static $i = 0;
        $this->isVal(true);
        $this->error[$i]['id']   = $id;
        $this->error[$i]['text'] = $text;
        $i++;
    }
    // fetch *** *** www.LazyCMS.net *** ***
    public function fetch($type='json'){
        $type = strtolower($type);
        switch ($type) {
            case 'json': 
                $I1 = json_encode($this->error);
                break;
            default :
                $I1 = var_export($this->erro,true);
                break;
        }
        return $I1;
    }
    // out *** *** www.LazyCMS.net *** ***
    public function out($type='json'){
        exit($this->fetch($type));
    }
    // isVal *** *** www.LazyCMS.net *** ***
    public function isVal($val=''){
        if ($val!=='') {
            $this->isVal = $val;
        } else {
            return $this->isVal;
        }
    }
}