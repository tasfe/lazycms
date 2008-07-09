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
// System *** *** www.LazyCMS.net *** ***
class System{
    // __group *** *** www.LazyCMS.net *** ***
    static function __group($l1,$l2,$l3=null){
        // $l1:groupid, $l2:current groupid, $l3:selected
        $I1 = null; $db = get_conn();
        $res = $db->query("SELECT `groupid`,`groupname` FROM `#@_system_group` WHERE 1=1 ORDER BY `groupid` DESC;");
        while ($rs = $db->fetch($res,0)) {
            if ($l2 != $rs[0]) {
                $selected = ((int)$l3 == (int)$rs[0]) ? ' selected="selected"' : null;
                $I1.= '<option value="'.$rs[0].'"'.$selected.'>'.$rs[1].'</option>';
            }
        }
        return $I1;
    }
}
