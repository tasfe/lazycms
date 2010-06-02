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

// 数字
define('VALIDATE_IS_NUMERIC',0x000000);
// 字母
define('VALIDATE_IS_LETTERS',0x000001);
//电子邮箱
define('VALIDATE_IS_EMAIL',0x000002);
// 超链接
define('VALIDATE_IS_URL',0x000003);
// 逗号分隔的字符串列表
define('VALIDATE_IS_LIST',0x000004);
// 字母、数字、下划线、杠
define('VALIDATE_IS_CNUS',0x000005);
// 字母、数字、下划线、杠、逗号、[、]
define('VALIDATE_IS_CNUSO',0x000006);

// 不能为空
define('VALIDATE_EMPTY',0x000007);
// 验证长度
define('VALIDATE_LENGTH',0x000008);
// 两个值是否相等
define('VALIDATE_EQUAL',0x000009);

/**
 * LazyCMS 系统验证类
 *
 */
class Validate {
    // private
    var $_error  = array();
    var $_isVal  = false;

    /**
     * 判断当前请求方法
     *
     * @return bool
     */
    function post(){
        return $_SERVER['REQUEST_METHOD']=='POST';
    }
    /**
     * 验证规则是否成立
     *
     * @return bool
     */
    function check(){
        $args = func_get_args();
        /**
         * 1.array('field',VALIDATE_TYPE,__('alert text'),params)
         * 2.array(
         *      array('field',VALIDATE_TYPE,__('alert text'),params),
         *      array('field',VALIDATE_TYPE,__('alert text'),params)
         *   )
         */
        if (is_array($args[0])) {
            // Use method 2 rule.
            if (is_array($args[0][0])) {
                foreach ($args[0] as $rule) {
                    if (!$this->check($rule)) break;
                }
            }
            // Use method 1 rule.
            else {
                return call_user_func_array(array(&$this,'check'),$args[0]);
            }
        } else {
            // Validate single rule.
            $error = false;
            $value = isset($_POST[$args[0]]) ? rawurldecode(trim($_POST[$args[0]])) : null; // POST值
            $type  = $args[1]; // 类型
            $text  = $args[2]; // 提示文字
            switch ($type) {
                case VALIDATE_EMPTY: case 'IS_EMPTY':
                    if (empty($value)) $error = $text;
                    break;
                case VALIDATE_LENGTH: case 'LENGTH_LIMIT':
                    if (mb_strlen($value,'UTF-8') < (int)$args[3]
                        || mb_strlen($value,'UTF-8') > (int)$args[4]) {
                            if ($args[3]) {
                                $error = sprintf($text,$args[3],$args[4]);
                            } else {
                                $error = sprintf($text,$args[4]);
                            }
                        }
                    break;
                case VALIDATE_EQUAL: case 'IS_EQUAL':
                    $value1 = isset($_POST[$args[3]]) ? trim($_POST[$args[3]]) : null;
                    if ($value != $value1) $error = $text;
                    break;
                case false: case 'false':
                    $error = $text;
                    break;
                default:
                    if (!Validate::is($value,$type)) $error = $text;
                    break;
            }
            // 没有错误信息
            if (!$error) return true;
            $this->_set_error($args[0],$error);
            return false;
        }
    }
    /**
     * 静态验证方法
     *
     * @param string $str   需要验证的字符串
     * @param mixed  $type  验证类型，常量或者正则表达式
     * @return bool
     */
    function is($str,$type){
        switch ($type) {
            case VALIDATE_IS_NUMERIC: case 'IS_NUMERIC':
                $pattern = '^\d+$';
                break;
            case VALIDATE_IS_LETTERS: case 'IS_LETTERS':
                $pattern = '^[A-Za-z]+$';
                break;
            case VALIDATE_IS_EMAIL: case 'IS_EMAIL':
                $pattern = '^\w+([\-\+\.]\w+)*@\w+([\-\.]\w+)*\.\w+([\-\.]\w+)*$';
                break;
            case VALIDATE_IS_URL: case 'IS_URL':
                $pattern = '^(http|https|ftp)\:(\/\/|\\\\)(([\w\/\\\+\-~`@\:%])+\.)+([\w\/\\\.\=\?\+\-~`@\'\:!%#]|(&amp;)|&)+';
                break;
            case VALIDATE_IS_LIST: case 'IS_LIST':
                $pattern = '^[\d\,\.]+$';
                break;
            case VALIDATE_IS_CNUS: case 'IS_CNUS':
                $pattern = '^[\w\-]+$';
                break;
            case VALIDATE_IS_CNUSO: case 'IS_CNUSO':
                $pattern = '^[\w\,\/\-\[\]]+$';
                break;
            default: // 自定义正则
                $pattern = $type;
                break;
        }
        return preg_match("/{$pattern}/i",$str);
    }
    /**
     * 验证结果有错误，输出错误
     *
     * @param bool $is_echo 是否输出错误
     * @return bool
     */
    function is_error($is_echo=true){
        if ($this->_error){
            if ($is_echo) {
                echo_json('Validate',$this->_error);
            }
            return true;
        }
        return false;
    }
    /**
     * 设置错误信息
     *
     * @param string $id
     * @param string $text
     */
    function _set_error($id,$text){
        static $i = 0;
        $this->_error[$i]['id']   = $id;
        $this->_error[$i]['text'] = $text;
        $i++;
    }
}
