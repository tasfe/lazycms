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
 * | Copyright (C) 2007-2008 LazyCMS.com All rights reserved.                  |
 * +---------------------------------------------------------------------------+
 * | LazyCMS is free software. This version use Apache License 2.0             |
 * | See LICENSE.txt for copyright notices and details.                        |
 * +---------------------------------------------------------------------------+
 */
defined('COM_PATH') or die('Restricted access!');
/**
 * 简单取词类
 *
 * @author  Lukin <my@lukin.cn>
 * @date    2010/1/21 20:42
 */
class Keyword {
    // 编码
    var $_encoding = 'UTF-8';
    // 词典
    var $_dicts  = array();
    // 词典最大 7 中文字
    var $_maxLen = 7;
    // 最小 2 中文字
    var $_minLen = 2;
    // 标点符号列表
    var $_punct_marks = array("\r","\n","\t",'`','~','!','@','#','$','%','^','&','*','(',')','-','_','+','=','|','\\','\'','"',';',':','/','?','.','>',',','<','[','{',']','}','·','～','！','＠','＃','￥','％','……','＆','×','（','）','－','——','＝','＋','＼','｜','【','｛','】','｝','‘','“','”','；','：','、','？','。','》','，','《',' ','　');

    function Keyword() {
        $args = func_get_args();
		call_user_func_array( array(&$this, '__construct'), $args );
	}

    function __construct($dicts=null){
		if (!is_array($dicts)) {
            $dicts = explode("\n",str_replace("\r\n","\n",$dicts));
        }
        foreach ($dicts as $dict) {
            if (empty($dict)) continue;
            $this->_dicts[mb_strlen($dict,$this->_encoding)][$dict] = 1;
        }
        return $this->_dicts;
	}
    /**
     * 加载词库
     *
     * @param  $file
     * @return array|bool|int
     */
    function load($file) {
        if (!is_file($file)) return false;
        $content = file_get_contents($file);
        return $this->__construct($content);
    }
    /**
     * 判断是否是词组
     *
     * @param  $dict
     * @return bool
     */
    function is($dict) {
        $strlen = mb_strlen($dict,$this->_encoding);
        if ($this->_minLen > $strlen || $strlen > $this->_maxLen) {
            return false;
        } else {
            return isset($this->_dicts[$strlen][$dict]);
        }
    }
    /**
     * 预先断句
     *
     * @param string $content
     * @return array
     */
    function _prepare($content) {
        $strlen     = mb_strlen($content,$this->_encoding);
        $substring  = array();
        $tmp_str    = '';
        for($i=0; $i<$strlen; $i++) {
            $char = mb_substr($content,$i,1,$this->_encoding);
            if (in_array($char,$this->_punct_marks)) {
                // 一连串的中文放入待分词的词组
                if ($tmp_str != '') {
                    // 遇到标点了,根据设置的标点断句最短的词组长度判断是否直接分词
                    $substring[] = trim($tmp_str);
                    $tmp_str  = '';
                }
            } else {
                $tmp_str.= $char;
            }
        }
        $tmp_str = trim($tmp_str);
        // 追加没有添加到子句中的中英文句子
        if ($tmp_str != '') $substring[] = $tmp_str;
        return $substring;
    }
    /**
     * 取词
     *
     * @param string $content
     * @return array
     */
    function get($content) {
        $result = array();
        // 使用标点将长句分成短句
        $subsens = $this->_prepare($content);

        foreach ($subsens as $sentence) {
            // i,j是扫描的指针.n是本次扫描的子串字数上界
            $i = $j = $n = 0;
            // 字符串长度
            $sen_len = mb_strlen($sentence,$this->_encoding);
            // 开始扫描
            while($i < $sen_len) {
                // n是本次扫描的子串字数上界
                $n = $sen_len - $i;
                // 取子串到字典中匹配
                for($j = $n; $j > 0; $j--) {
                    // 从$i指的地方开始,取$j的长度
                    $sub_str = mb_substr($sentence,$i,$j,$this->_encoding);
                    // 字典中有该词
                    if ($this->is($sub_str)) {
                        $result[] = $sub_str;
                        $i += $j; //指针后移
                        break;
                    }
                }
                $i++;
            }
        }
        return array_values(array_unique($result));
    }
}
