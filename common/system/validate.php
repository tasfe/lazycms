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
    public function check($p1){
        $R1 = explode(";",$p1);
        foreach ($R1 as $v) {
            if ($this->__check($v)) {
                break;
            }
        }
        return $this;
    }
    // __check *** *** www.LazyCMS.net *** ***
    private function __check($p1){
        // username|1|L('login/check/name')|2-30
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
        $this->setError($R1[0],$R);
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
                $R = json_encode($this->error);
                break;
            default :
                $R = var_export($this->erro,true);
                break;
        }
        return $R;
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