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
 * 公共函数
 *
 * @copyright   Copyright (c) 2007-2008 lazycms.net All rights reserved.
 * @author      Lukin <mylukin@gmail.com>
 * @date        2008-8-26
 */
// Article *** *** www.LazyCMS.net *** ***
class Article{
    // formatPath *** *** www.LazyCMS.net *** ***
    static function formatPath($p1,$p2,$p3,$p4=null){
        $p4 = empty($p4) ? now() : $p4;
        $R = str_replace(array('%I','%M','%P'),array($p1,md5($p1.salt(10)),pinyin($p3)),$p2);
        $R = strftime($R,$p4);
        return $R;
    }
    // sort *** *** www.LazyCMS.net *** ***
    function sort($p1,$p2=null,$p3=0,$p4=0){
        // $p1:modelid, $p2:selected, $p3:father id, $p4:number
        $R = $nbsp = null; $db = get_conn();
        for ($i=0;$i<$p4;$i++) {
            $nbsp.= "&nbsp; &nbsp;";
        }
        $res = $db->query("SELECT `cs`.`sortid`,`cs`.`sortname` FROM `#@_content_sort_model` AS `csm` LEFT JOIN `#@_content_sort` AS `cs` ON `csm`.`sortid`=`cs`.`sortid` WHERE `csm`.`modelid`=[m] AND `cs`.`parentid`=[p] ORDER BY `cs`.`sortid` ASC;",array('m'=>$p1,'p'=>$p3));
        while ($rs = $db->fetch($res,0)) {
            $selected = ((int)$p2 == (int)$rs[0]) ? ' selected="selected"' : null;
            $R.= '<option value="'.$rs[0].'"'.$selected.'>'.$nbsp.'├'.$rs[1].'</option>';
            if ((int)$db->count("SELECT * FROM `#@_content_sort_model` AS `csm` LEFT JOIN `#@_content_sort` AS `cs` ON `csm`.`sortid`=`cs`.`sortid` WHERE `csm`.`modelid`=".DB::quote($p1)." AND `cs`.`parentid`=".DB::quote($rs[0]).";") > 0) {
                $R.= self::sort($p1,$p2,$rs[0],$p4+1);
            }
        }
        return $R;
    }
    // __sort *** *** www.LazyCMS.net *** ***
    static function __sort($p1=0,$p2=0,$p3=0,$p4=null){
        // $p1:father sortid, $p2:number, $p3:current sortid, $p4:selected
        $R = $nbsp = null; $db = get_conn(); 
        for ($i=0;$i<$p2;$i++) {
            $nbsp.= "&nbsp; &nbsp;";
        }
        $res = $db->query("SELECT `sortid`,`sortname` FROM `#@_content_sort` WHERE `parentid`=? ORDER BY `sortid` ASC;",$p1);
        while ($rs = $db->fetch($res,0)) {
            if ((int)$p3 != (int)$rs[0]) {
                $selected = ((int)$p4 == (int)$rs[0]) ? ' selected="selected"' : null;
                $R.= '<option value="'.$rs[0].'"'.$selected.'>'.$nbsp.'├'.$rs[1].'</option>';
                if ((int)$db->count("SELECT * FROM `#@_content_sort` WHERE `parentid`=".DB::quote($rs[0]).";") > 0) {
                    $R.= self::__sort($rs[0],$p2+1,$p3,$p4);
                }
            }
        }
        return $R;
    }
    // __sub *** *** www.LazyCMS.net *** ***
    static function __sub($p1){
        $db  = get_conn();
        $num = $db->count("SELECT * FROM `#@_content_sort` WHERE `parentid`=".DB::quote($p1).";");
        return ((int)$num>0)?'1':'0';
    }
    // getModels *** *** www.LazyCMS.net *** ***
    static function getModels($p1,$p2='modelid'){
        $db = get_conn(); $R = array();
        $res = $db->query("SELECT * FROM `#@_content_sort_model` AS `csm` LEFT JOIN `#@_content_model` AS `cm` ON `csm`.`modelid`=`cm`.`modelid` WHERE `cm`.`modelstate`=1 AND `csm`.`sortid`=?;",$p1);
        while ($rs = $db->fetch($res)) {
            $R[] = $rs[$p2];
        }
        return $R;
    }
}
