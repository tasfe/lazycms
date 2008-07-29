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
 * 系统模块的公共函数
 *
 * @copyright   Copyright (c) 2007-2008 lazycms.net All rights reserved.
 * @author      Lukin <mylukin@gmail.com>
 * @date        2008-7-6
 */
// Onepage *** *** www.LazyCMS.net *** ***
class Onepage{
    // __sort *** *** www.LazyCMS.net *** ***
    static function __sort($p1=0,$p2=0,$p3=0,$p4=null){
        // $p1:father oneid, $p2:number, $p3:current oneid, $p4:selected
        $R = $nbsp = null; $db = get_conn(); 
        for ($i=0;$i<$p2;$i++) {
            $nbsp.= "&nbsp; &nbsp;";
        }
        $res = $db->query("SELECT `oneid`,`onename` FROM `#@_onepage` WHERE `oneid1`=? ORDER BY `oneid` ASC;",$p1);
        while ($rs = $db->fetch($res,0)) {
            if ((int)$p3 != (int)$rs[0]) {
                $selected = ((int)$p4 == (int)$rs[0]) ? ' selected="selected"' : null;
                $R.= '<option value="'.$rs[0].'"'.$selected.'>'.$nbsp.'├'.$rs[1].'</option>';
                if ((int)$db->count("SELECT * FROM `#@_onepage` WHERE `oneid1`='".$rs[0]."';") > 0) {
                    $R.= self::__sort($rs[0],$p2+1,$p3,$p4);
                }
            }
        }
        return $R;
    }
}
