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
        $this->formatTags();
        return $this;
    }
    // loadHTML *** *** www.LazyCMS.net *** ***
    public function loadHTML() {
        $args = func_get_args();
        return $this->load(call_user_func_array('read_file', $args));
    }
    // fetch *** *** www.LazyCMS.net *** ***
    public function fetch($p1){
        $i = 0; $R = array();
        foreach ($this->tags as $k=>$v) {
            if (preg_match('#<([\w\-\:]+) [^>]*'.$p1.'="([^"]*)"[^>]*>#i',$v,$r)) { 
                $R[$i][$p1] = $r[2];
                $R[$i]['tag'] = $this->getTag($r[1],$i,$k);
                $i++;
            }
        }
        return $R;
    }
    // getTag *** *** www.LazyCMS.net *** ***
    private function getTag($tagName,$k,$p){
        $len = count($this->tags);
        $pos = array(); $n = 0; $R = array();
        for ($i=$p; $i<$len; $i++) {
            if (preg_match('#<'.$tagName.'[^>]*>|</'.$tagName.'[^>]*>#i',$this->tags[$i])) {
                if (preg_match('#</'.$tagName.'[^>]*>#i',$this->tags[$i])) {
                    $n = $i; break;
                }
                $tag[$tagName][] = $this->tags[$i]; 
            }
        }
        $j = count($tag[$tagName]); $t = 1;
        for ($i=$n; $i<$len; $i++) {
            if (preg_match('#</'.$tagName.'[^>]*>#i',$this->tags[$i])) {
                if ($t==$j) {
                    $pos['start'] = $p;
                    $pos['end']   = $i;
                }
                $t++;
            }
        }
        $R['all'] = $this->decode(implode('',array_slice($this->tags,$pos['start'],($pos['end']-$pos['start'])+1)));
        $R['cut'] = $this->decode(implode('',array_slice($this->tags,$pos['start']+1,($pos['end']-$pos['start'])-1)));
        return $R;
    }
    // formatTags *** *** www.LazyCMS.net *** ***
    private function formatTags(){
        if (preg_match_all('#<[^>]*>#is',$this->HTML,$r)) {
            $tags = $r[0];
        }
        $len = count($tags);
        for ($i=0;$i<$len;$i++) {
            $next = isset($tags[$i+1]) ? $tags[$i+1] : null;
            $mid  = $this->mid($tags[$i],$next);
            $this->tags[] = $tags[$i]; if (empty($next)) { break; }
            if (empty($mid)) { continue; }; $this->tags[] = $mid;
        }
    }
    // mid *** *** www.LazyCMS.net *** ***
    private function mid($p1,$p2){
        if (empty($p1) || empty($p2) || empty($this->HTML)) { return ;}
        static $R1 = null; static $R2 = 0; $R = null;
        if (empty($R1)) { $R1 = $this->HTML; }
        $p4 = strpos(strtolower($R1),strtolower($p1)); if ($p4===false) { return ; }
        $p5 = strpos(strtolower(substr($R1,-(strlen($R1)-$p4-strlen($p1)))),strtolower($p2));
        if ($p4!==false && $p5!==false) {
            $R  = substr($R1,$p4+strlen($p1),$p5);
            $R1 = substr($R1,$p4+$p5+strlen($p1));
            $R2 = strlen($p2);
        }
        return $R;
    }
    // encode *** *** www.LazyCMS.net *** ***
    public function encode($p1){
        return preg_replace('#<(@[^>]*/)>#iU','&lt;\1&gt;',str_replace('&','&amp;',$p1));
    }
    // decode *** *** www.LazyCMS.net *** ***
    public function decode($p1){
        return preg_replace('#&lt;(@.*/)&gt;#iU','<\1>',str_replace('&amp;','&',$p1));
    }
}