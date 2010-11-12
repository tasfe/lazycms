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
 * 分词类
 *
 * @author  Lukin <my@lukin.cn>
 * @date    2010/1/21 20:42
 */
class SplitWord {
    // 编码
    var $_encoding = 'UTF-8';
    // 词典最大 7 中文字
    var $_maxLen = 7;
    // 最小 2 中文字
    var $_minLen = 2;
    // 词库
    var $_dicts      = array();
    // 高频词列表
    var $_hf_dicts   = array();
    // 英文
    var $_en_chars   = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','1','2','3','4','5','6','7','8','9','0','ａ','ｂ','ｃ','ｄ','ｅ','ｆ','ｇ','ｈ','ｉ','ｊ','ｋ','ｌ','ｍ','ｎ','ｏ','ｐ','ｑ','ｒ','ｓ','ｔ','ｕ','ｖ','ｗ','ｘ','ｙ','ｚ','Ａ','Ｂ','Ｃ','Ｄ','Ｅ','Ｆ','Ｇ','Ｈ','Ｉ','Ｊ','Ｋ','Ｌ','Ｍ','Ｎ','Ｏ','Ｐ','Ｑ','Ｒ','Ｓ','Ｔ','Ｕ','Ｖ','Ｗ','Ｘ','Ｙ','Ｚ','０','１','２','３','４','５','６','７','８','９');
    // 标点符号列表
    var $_punct_marks = array("\r","\n","\t",'`','~','!','@','#','$','%','^','&','*','(',')','-','_','+','=','|','\\','\'','"',';',':','/','?','.','>',',','<','[','{',']','}','·','～','！','＠','＃','￥','％','……','＆','×','（','）','－','——','＝','＋','＼','｜','【','｛','】','｝','‘','“','”','；','：','、','？','。','》','，','《',' ','　');

    function SplitWord() {
        $args = func_get_args();
		call_user_func_array( array(&$this, '__construct'), $args );
	}

    function __construct($dicts=null){
        $this->_hf_dicts = explode(',', __('I,is,for,you,him,she,of,and,at,the,in'));
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
        if ($strlen > $this->_maxLen) {
            return false;
        } else {
            return isset($this->_dicts[$strlen][$dict]);
        }
    }
    /**
     * 初步分词
     *
     * @param  $content
     * @return array
     */
    function _init($content) {
        $strlen     = mb_strlen($content,$this->_encoding);
        $substring  = array();
        $cn_tmp_str = '';
        $en_tmp_str = '';
        
        for($i=0;$i<$strlen;$i++) {
            $char = mb_substr($content,$i,1,$this->_encoding);
            if (in_array($char,$this->_punct_marks)) {
                // 一连串的中文放入待分词的词组
                if ($cn_tmp_str != '') {
                    // 遇到标点了,根据设置的标点断句最短的词组长度判断是否直接分词
                    $substring[] = array($cn_tmp_str, (mb_strlen($cn_tmp_str,$this->_encoding) <= $this->_minLen ? 1 : 0));
                    $cn_tmp_str  = '';
                }
                // 一连串的英语字母或数字可以直接返回分词结果
                if ($en_tmp_str != '') {
                    $substring[] = array($en_tmp_str, 1);
                    $en_tmp_str  = '';
                }
            } else if(in_array($char,$this->_en_chars)) {
                // 遇到英文或数字了,可以给中文句子断句了
                if ($cn_tmp_str != '') {
                    // 遇到标点了,根据设置的标点断句最短的词组长度判断是否直接分词
                    $substring[] = array($cn_tmp_str, (mb_strlen($cn_tmp_str,$this->_encoding) <= $this->_minLen ? 1 : 0));
                    $cn_tmp_str = '';
                }
                $en_tmp_str.= $char;
            } else {
                // 遇到中文了,可以给英文句子或数字断句了
                if ($en_tmp_str != '') {
                    $substring[] = array($en_tmp_str, 1);
                    $en_tmp_str  = '';
                }
                $cn_tmp_str.= $char;
            }
        }
        $cn_tmp_str = trim($cn_tmp_str); $en_tmp_str = trim($en_tmp_str);
        // 追加没有添加到子句中的中英文句子
        if ($cn_tmp_str != '') {
            // 要判断一下后面没有英文词组,这样句子是在没有标点符号的情况下结束了
            $substring[] = array($cn_tmp_str, ($en_tmp_str == '' && (mb_strlen($cn_tmp_str,$this->_encoding) <= $this->_minLen) ? 1 : 0));
        }
        if ($en_tmp_str != '') $substring[] = array($en_tmp_str, 1);
        return $substring;
    }
    /**
     * 取得分词
     *
     * @param  $content             内容
     * @param int $max_len          词组最长字数
     * @param bool $save_other      是否保存其他字符
     * @return array
     */
    function get($content,$max_len=8,$save_other=false) {
        $result = array();
        // 使用标点将长句分成短句
        $subsens = $this->_init($content);
        
        foreach ($subsens as $item) {
            if ($item[1]==1) {
                $result[] = trim($item[0]);
                continue;
            } else {
                $sentence = $item[0];
            }
            
            // i,j是扫描的指针.n是本次扫描的子串字数上界
            $i = $j = $n = 0;
            // 每次取子串最长字数,默认为8个字.m越大分词越慢,但是越准确
            $m = $max_len;
            // 用来记录没有匹配的字,多个连续的未匹配的字认为组合成一个词.
            $tmp_str = '';
            // 每次取的子串
            $sub_str = '';
            // 字符串长度
            $sen_len = mb_strlen($sentence,$this->_encoding);

            while($i < $sen_len) {
                // n是本次扫描的子串字数上界
                $n = ($i+$m) < $sen_len ? $m : $sen_len-$i;

                $find = false;
                // 取子串到字典中匹配
                for($j = $n; $j > 0; $j--) {
                    // 从$i指的地方开始,取$j的长度
                    $sub_str = mb_substr($sentence,$i,$j,$this->_encoding);
                    // 字典中有该词
                    if ($this->is($sub_str)) {
                        // 临时字符串中只有一个字或没有词
                        if (mb_strlen($tmp_str,$this->_encoding) < 2) {
                            $tmp_str = ''; // 清空它
                        } elseif ($tmp_str != "") {
                            // 多个连续的没有匹配的字认为他组成一个生词
                            $result[] = $tmp_str;
                            $tmp_str  = '';
                        }

                        $result[] = $sub_str;

                        $find     = true;
                        $i += $j; //指针后移
                        break;
                    }
                }

                if(!$find) {
                    // 当前单个字无法匹配,而且它是高频词
                    if (in_array($sub_str,$this->_hf_dicts)) {
                        // 临时字符串中只有一个字,遇到高频词可以进行断句,所以要判断一下临时队列
                        if (mb_strlen($tmp_str,$this->_encoding) == 1) {
                            $tmp_str = ''; //清空它
                        } else if($tmp_str != '') {
                            // 多个连续的没有匹配的字认为他组成一个生词
                            $result[] = $tmp_str;
                            $tmp_str  = '';
                        }
                    } else {
                        // 不是标点,是一个没有匹配的单个的字
                        if ($save_other) {
                            $tmp_str.= $sub_str;
                        }
                    }
                    $i++;
                }
            }
            // 扫描结束,临时队列还有词,那应该是最后面无法进行分词的一些字
            if ($tmp_str != '' && $save_other) $result[] = $tmp_str;
        }
        return array_values(array_unique($result));
    }
}
