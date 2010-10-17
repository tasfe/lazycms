<?php
/**
* 全概率计算
*
* @param array $ps array('a'=>0.5,'b'=>0.2,'c'=>0.4)
* @return string 返回上面数组的key
* @author Lukin <my@lukin.cn>
*/
function random($ps){
    static $arr = array(); $key = md5(serialize($ps));
    if (!isset($arr[$key])) {
        $max = array_sum($ps);
        foreach ($ps as $k=>$v) {
            $v = $v / $max * 10000;
            for ($i=0; $i<$v; $i++) $arr[$key][] = $k;
        }
    }
    return $arr[$key][mt_rand(0,count($arr[$key])-1)];
}
