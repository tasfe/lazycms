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
 * 中文分词类
 *
 * 修改自ThinkPHP的中英文分词扩展
 */
class SplitWord{
    // 词典最大 7 中文字，这里的数值为字节数组的最大索引
    var $_maxLen = 13;
    // 最小 2 中文字，这里的数值为字节数组的最大索引
    var $_minLen = 3;
    // 存放结果的数组
    var $_result = array();
    var $_dicts  = array();
    // 高频词列表
    var $_highFreq = array();
    // 英文
    var $_enChar = array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z","A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z","1","2","3","4","5","6","7","8","9","0","ａ","ｂ","ｃ","ｄ","ｅ","ｆ","ｇ","ｈ","ｉ","ｊ","ｋ","ｌ","ｍ","ｎ","ｏ","ｐ","ｑ","ｒ","ｓ","ｔ","ｕ","ｖ","ｗ","ｘ","ｙ","ｚ","Ａ","Ｂ","Ｃ","Ｄ","Ｅ","Ｆ","Ｇ","Ｈ","Ｉ","Ｊ","Ｋ","Ｌ","Ｍ","Ｎ","Ｏ","Ｐ","Ｑ","Ｒ","Ｓ","Ｔ","Ｕ","Ｖ","Ｗ","Ｘ","Ｙ","Ｚ","０","１","２","３","４","５","６","７","８","９");
    // 标点符号列表
    var $_sign = array('\r','\n','\t','`','~','!','@','#','$','%','^','&','*','(',')','-','_','+','=','|','\\','\'','"',';',':','/','?','.','>',',','<','[','{',']','}','·','～','！','＠','＃','￥','％','……','＆','×','（','）','－','——','＝','＋','＼','｜','【','｛','】','｝','‘','“','”','；','：','、','？','。','》','，','《',' ','　');
    /**
     * 兼容PHP5模式
     *
     */
    function __construct(){
        @set_time_limit(0);
        // 预先加载词库
        $files = glob(COM_PATH.'/dicts/*',GLOB_BRACE); asort($files,SORT_REGULAR);
        foreach ($files as $file){
            $isHFW = (substr($file,-4)=='.hfw')?true:false;
            $fp = fopen($file,'r');
            while($ws = trim(fgets($fp))){
                if (empty($ws)){ continue; }
                if ($isHFW) {
                    // 高频词表
                    $this->_highFreq[] = $ws;
                } else {
                    $this->_dicts[strlen($ws)][$ws] = 1;
                }
            }
            fclose($fp);
        }
    }
    /**
     * 初始化类
     *
     * @return SplitWord
     */
    function SplitWord(){
        $this->__construct();
    }
    /**
     * 判断是否在词典内
     *
     * @param string $string
     * @return bool
     */
    function _isKeyword($string){
        $len = strlen($string);
        if ($len > $this->_maxLen) {
            return false;
        } else {
            return isset($this->_dicts[$len][$string]);
        }
    }
    /**
     * 初步中文分词
     *
     * @param string $string
     * @return array
     */
    function _cnSplit($string){
        $len = len($string);
        $substring = array();
        $cnTmpStr = "";
        $enTmpStr = "";

        for($i=0;$i<$len;$i++) {
            $char = cnsubstr($string,$i,1);
            $cnTmpStr = trim($cnTmpStr);
            $enTmpStr = trim($enTmpStr);
            if (isset($this->_sign[$char])) {
                // 一连串的中文放入待分词的词组
                if ($cnTmpStr != "") {
                    // 遇到标点了,根据设置的标点断句最短的词组长度判断是否直接分词
                    if (len($cnTmpStr) <= $this->_minLen) {
                        $substring[] = array($cnTmpStr,'1');
                    } else {
                        $substring[] = array($cnTmpStr,'0');
                    }
                    $cnTmpStr = "";
                }
                // 一连串的英语字母或数字可以直接返回分词结果
                if ($enTmpStr != "") {
                    $substring[] = array($enTmpStr,'1');
                    $enTmpStr = "";
                }
            } else if(isset($this->_enChar[$char])) {
                // 遇到英文或数字了,可以给中文句子断句了
                if ($cnTmpStr != "") {
                    // 遇到标点了,根据设置的标点断句最短的词组长度判断是否直接分词
                    if(len($cnTmpStr) <= $this->_minLen) {
                        $substring[] = array($cnTmpStr,'1');
                    } else {
                        $substring[] = array($cnTmpStr,'0');
                    }
                    $cnTmpStr = "";
                }
                $enTmpStr.= $char;
            } else {
                // 遇到中文了,可以给英文句子或数字断句了
                if ($enTmpStr != "") {
                    $substring[] = array($enTmpStr,'1');
                    $enTmpStr = "";
                }
                $cnTmpStr.= $char;
            }
        }
        $cnTmpStr = trim($cnTmpStr); $enTmpStr = trim($enTmpStr);
        // 追加没有添加到子句中的中英文句子
        if ($cnTmpStr != "") {
            // 要判断一下后面没有英文词组,这样句子是在没有标点符号的情况下结束了
            if ($enTmpStr == "" && len($cnTmpStr) <= $this->_minLen) {
                $substring[] = array($cnTmpStr,'1');
            } else {
                $substring[] = array($cnTmpStr,'0');
            }
        }
        if ($enTmpStr != "") { $substring[] = array($enTmpStr,'1'); }
        return $substring;
    }
    /**
     * 具体分词
     *
     * @param string $string
     * @param int    $maxLen
     * @param bool   $saveSingle
     * @param bool   $saveOther
     * @return array
     */
    function getWord($string, $maxLen=8, $saveSingle=false, $saveOther=false){
        $this->_result = array();
        // 使用标点将长句分成短句
        $subSens = $this->_cnSplit($string);

        foreach($subSens as $item) {
            if($item[1] == '1') {
                $this->_result[] = trim($item[0]);
                continue;
            } else {
                $subSen = $item[0];
            }

            $bFind = false;
            // i,j是扫描的指针.N是本次扫描的子串字数上界
            $i = $j = $N = 0;
            // 每次取子串最长字数,默认为8个字.M越大分词越慢,但是越准确
            $M = $maxLen;
            // 用来记录没有匹配的字,多个连续的未匹配的字认为组合成一个词.
            $tmpStr = '';
            // 每次取的子串
            $sub_str = '';
            // 字符串长度
            $senLen = len($subSen);

            while($i < $senLen) {
                $N = ($i+$M) < $senLen ? $M : $senLen-$i;
                // N是本次扫描的子串字数上界
                $bFind = false;
                // 取子串到字典中匹配
                for($j = $N; $j > 0; $j--) {
                    // 从$i指的地方开始,取$j的长度
                    $sub_str = cnsubstr($subSen,$i,$j);
                    // 字典中有该词
                    if ($this->_isKeyword($sub_str)) {
                        // 临时字符串中只有一个字或没有词
                        if (len($tmpStr) < 2 && !$saveSingle) {
                            $tmpStr = ""; // 清空它
                        } elseif ($tmpStr != "") {
                            // 多个连续的没有匹配的字认为他组成一个生词
                            $this->_result[] = $tmpStr;
                            $tmpStr = "";
                        }

                        $this->_result[] = $sub_str;

                        $bFind = true;
                        $i += $j; //指针后移
                        break;
                    }
                }

                if(!$bFind) {
                    // 当前单个字无法匹配,而且它是高频词
                    if (isset($this->_highFreq[$sub_str])) {
                        // 临时字符串中只有一个字,遇到高频词可以进行断句,所以要判断一下临时队列
                        if (len($tmpStr) ==1 && !$saveSingle) {
                            $tmpStr = ""; //清空它
                        } else if($tmpStr != "") {
                            // 多个连续的没有匹配的字认为他组成一个生词
                            $this->_result[] = $tmpStr;
                            $tmpStr = "";
                        }
                        // 如果要保留单个的高频字,将它保留下来,否则剔除
                        if ($saveSingle) {
                            $this->_result[] = $sub_str;
                        }
                    } else {
                        // 不是标点,是一个没有匹配的单个的字
                        if ($saveOther) {
                            $tmpStr.= $sub_str;
                        }
                    }
                    $i++;
                }
            }
            // 扫描结束,临时队列还有词,那应该是最后面无法进行分词的一些字
            if ($tmpStr !="" && $saveOther) { $this->_result[] = $tmpStr; }
        }
        return $this->_result;
    }
}
