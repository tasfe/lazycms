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
 * ParseTags
 *
 * @copyright   Copyright (c) 2007-2008 lazycms.net All rights reserved.
 * @author      Lukin <mylukin@gmail.com>
 * @date        2008-9-2
 */
// ParseHTML *** *** www.LazyCMS.net *** ***
class ParseTags {
    private $tag  = array();
    private $HTML = null;
    // load *** *** www.LazyCMS.net *** ***
    public function load($p1){
        $this->HTML = $this->encode($p1);
        if (preg_match_all('#<[^>]*>#is',$this->HTML,$r)) {
            $this->tags = $r[0];
        }
        return $this;
    }
    // fetch *** *** www.LazyCMS.net *** ***
    public function fetch($p1){
        //print_r($this->tags);
        $pos = array(); $tag = array(); $name = array();
        foreach ($this->tags as $k=>$v) {
            if (preg_match('#<([\w\-\:]+) [^>]*'.$p1.'="([^"]*)"[^>]*>#i',$v,$r)) { $name[$r[1]] = $k; }
        }
        $len = count($this->tags);
        foreach ($name as $k=>$v) {
            $n = 0;
            for ($i=$v; $i<$len; $i++) {
                if (preg_match('#<'.$k.'[^>]*>|</'.$k.'[^>]*>#i',$this->tags[$i])) {
                    if (preg_match('#</'.$k.'[^>]*>#i',$this->tags[$i])) {
                        $n = $i; break;
                    }
                    $tag[$k][] = $this->tags[$i]; 
                }
            }
            $j = count($tag[$k]); $t = 1;
            for ($i=$n; $i<$len; $i++) {
                if (preg_match('#</'.$k.'[^>]*>#i',$this->tags[$i])) {
                    if ($t==$j) {
                        $pos['start'] = $v;
                        $pos['end']   = $i;
                    }
                    $t++;
                }
            }
        }
        $R = array_slice($this->tags,$pos['start'],($pos['end']-$pos['start'])+1);
        return $this->decode(implode('',$R));
    }
    // loadHTML *** *** www.LazyCMS.net *** ***
    public function loadHTML() {
        $args = func_get_args();
        return $this->load(call_user_func_array('read_file', $args));
    }
    // encode *** *** www.LazyCMS.net *** ***
    public function encode($p1){
        return preg_replace('#<(@[^>]*/)>#i','&lt;\1&gt;',str_replace('&','&amp;',$p1));
    }
    // decode *** *** www.LazyCMS.net *** ***
    public function decode($p1){
        return preg_replace('#&lt;(@(?!&gt;).*/)&gt;#i','<\1>',str_replace('&amp;','&',$p1));
    }
}