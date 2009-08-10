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
 * 分类管理公共模块
 */
class Content_Sort{
    /**
     * 添加文章的分类select->option
     *
     * @param  integer  $p1     模型ID
     * @param  string   $p2     选择的分类IDs
     * @param  integer  $p3     上级分类ID
     * @param  integer  $p4     循环的层级
     * @return string
     */
    function getSortListByParentId($p1,$p2,$p3=0,$p4=0){
        $R = $nbsp = null; $db = get_conn();
        for ($i=0;$i<$p4;$i++) {
            $nbsp.= "&nbsp; ";
        }
        $res = $db->query("SELECT `sortid`,`sortname` FROM `#@_content_sort` WHERE `parentid`=? ORDER BY `sortid` ASC;",$p3);
        while ($rs = $db->fetch($res,0)) {
            if ($db->result("SELECT COUNT(*) FROM `#@_content_sort_join` WHERE `modelid`=".DB::quote($p1)." AND `sortid`=".DB::quote($rs[0]).";") == 0) {
                $class = ' class="disabled"';
            } elseif ((int)$p2==(int)$rs[0]) {
                $class = ' class="selected"';
            } else {
                $class = null;
            }
            $R.= '<a href="javascript:;" onclick="$(this).setSortId();" value="'.$rs[0].'" label="'.h2c($rs[1]).'"'.$class.'>'.$nbsp.'├'.$rs[1].'</a>';
            if ((int)$db->result("SELECT COUNT(*) FROM `#@_content_sort` WHERE `parentid`=".DB::quote($rs[0]).";") > 0) {
                $R.= Content_Sort::getSortListByParentId($p1,$p2,$rs[0],($p4+1));
            }
        }
        return $R;
    }
    /**
     * 添加分类的select->option
     *
     * @param  integer  $p1     上级分类ID
     * @param  integer  $p2     循环的层级
     * @param  integer  $p3     当前分类的ID
     * @param  integer  $p4     选中分类的ID
     * @return string
     */
    function getSortOptionByParentId($p1=0,$p2=0,$p3=0,$p4=null){
        $R = $nbsp = null; $db = get_conn();
        for ($i=0;$i<$p2;$i++) {
            $nbsp.= "&nbsp; ";
        }
        $res = $db->query("SELECT `sortid`,`sortname` FROM `#@_content_sort` WHERE `parentid`=? ORDER BY `sortid` ASC;",$p1);
        while ($rs = $db->fetch($res,0)) {
            if ((int)$p3 != (int)$rs[0]) {
                $model = is_bool($p3)?null:' models="'.implode(',',Content_Model::getModelsBySortId($rs[0])).'"';
                $selected = ((int)$p4 == (int)$rs[0]) ? ' selected="selected"' : null;
                $R.= '<option'.$model.' value="'.$rs[0].'"'.$selected.'>'.$nbsp.'├'.$rs[1].'</option>';
                if ((int)$db->result("SELECT COUNT(*) FROM `#@_content_sort` WHERE `parentid`=".DB::quote($rs[0]).";") > 0) {
                    $R.= Content_Sort::getSortOptionByParentId($rs[0],$p2+1,$p3,$p4);
                }
            }
        }
        return $R;
    }
    /**
     * 判断是否有子分类
     *
     * @param  integer  $p1     分类ID
     * @return string
     */
    function isSubSort($p1){
        $db  = get_conn();
        $num = $db->result("SELECT COUNT(*) FROM `#@_content_sort` WHERE `parentid`=".DB::quote($p1).";");
        return ((int)$num>0)?'2':'1';
    }
    /**
     * 取得指定分类下的所有小类ID
     *
     * @param int $p1
     */
    function getSortIdsBySortIds($p1){
        $db = get_conn(); $R = array();
        $res = $db->query("SELECT `sortid` FROM `#@_content_sort` WHERE `sortid` IN({$p1})");
        while ($rs = $db->fetch($res,0)) {
            $R[] = $rs[0];
            $res1 = $db->query("SELECT `sortid` FROM `#@_content_sort` WHERE `parentid`=?",$rs[0]);
            while ($rs1 = $db->fetch($res1,0)) {
                $R = array_merge($R,Content_Sort::getSortIdsBySortIds($rs1[0]));
            }
        }
        return array_unique($R);
    }
}
