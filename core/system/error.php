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
 * LazyCMS 异常处理类
 *
 * @copyright   Copyright (c) 2007-2008 lazycms.net All rights reserved.
 * @author      Lukin <mylukin@gmail.com>
 */
class Error extends Exception{
    // 重定义构造器使 message 变为必须被指定的属性
    public function __construct($message,$code=0,$extra=false) {
        // 确保所有变量都被正确赋值
        parent::__construct($message,$code);
    }

    // 自定义字符串输出的样式
    public function __toString() {
        return __CLASS__;
    }

    // getError *** *** www.LazyCMS.net *** ***
    public function getError(){
        $trace = $this->getTrace();
        $this->file = isset($trace[0]['file']) ? $trace[0]['file'] :null;
        $this->line = isset($trace[0]['line']) ? $trace[0]['line'] :null;
        $traceInfo  = '';
        $time       = date("y-m-d H:i:m");
        foreach($trace as $t) {
            $_file  = isset($t['file']) ? $t['file'] : null;
            $_line  = isset($t['line']) ? $t['line'] : null;
            $_class = isset($t['class']) ? $t['class'] : null;
            $_type  = isset($t['type']) ? $t['type'] : null;
            $_args  = isset($t['args']) ? $t['args'] : null;
            $_function = isset($t['function']) ? $t['function'] : null;
            $traceInfo .= '['.$time.'] '.$_file.' ('.$_line.') ';
            $traceInfo .= $_class.$_type.$_function.'(';
			if (!empty($_args)) {
				$traceInfo .= implode(', ', $_args);
			}
            $traceInfo .=")\n";
        }
        $error['message']   = $this->message;
        $error['file']      = $this->file;
        $error['line']      = $this->line;
        $error['trace']     = $traceInfo;

        return $error ;
    }
}
?>