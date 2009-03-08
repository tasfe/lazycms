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
 * 文章管理公共模块
 */
class Content_Article{
    /**
     * 格式化路径
     *
     * @param  int      $p1     MaxID
     * @param  string   $p2     用户输入的字符串（未格式化）
     * @param  string   $p3     文章标题，需要用这个来格式化成为标题路径
     * @param  integer  $p4     时间戳，用来格式化日期变量
     * @return string
     */
    function formatPath($p1,$p2,$p3,$p4=null){
        $p4 = empty($p4) ? now() : $p4; $p5 = null;
        if (strpos($p2,'%P')!==false) {
            $p5 = pinyin($p3);
            $p5 = empty($p5)?$p1:$p5;
        }
        $R = str_replace(array('%I','%M','%P'),array($p1,md5($p1.salt(10)),$p5),$p2);
        $R = strftime($R,$p4);
        return $R;
    }
    /**
     * 添加文章的分类HTML
     *
     * @param  integer  $p1     模型ID
     * @param  string   $p2     选择的分类IDs
     * @param  integer  $p3     上级分类ID
     * @return string
     */
    function getSortListByParentId($p1,$p2=null,$p3=0){
        $R = null; $db = get_conn();
        $oby = $p3==0?'ASC':'DESC';
        $res = $db->query("SELECT `sortid`,`sortname` FROM `#@_content_sort` WHERE `parentid`=? ORDER BY `sortid` {$oby};",$p3);
        while ($rs = $db->fetch($res,0)) {
            if ($db->count("SELECT * FROM `#@_content_sort_model` WHERE `modelid`=".DB::quote($p1)." AND `sortid`=".DB::quote($rs[0]).";") == 0) {
                $disabled = ' disabled="disabled"';
                $sortname = '<span>'.$rs[1].'</span>';
            } else {
                $disabled = null;
                $sortname = $rs[1];
            }
            $checked = instr($p2,$rs[0])?' checked="checked"':null;
            $R.= '<li><input type="checkbox" name="sortids[]" id="sortids['.$rs[0].']" value="'.$rs[0].'"'.$checked.$disabled.'><label for="sortids['.$rs[0].']">'.$sortname.'</label>';
            if ((int)$db->count("SELECT * FROM `#@_content_sort` WHERE `parentid`=".DB::quote($rs[0]).";") > 0) {
                $R.= Content_Article::getSortListByParentId($p1,$p2,$rs[0]);
            }
            $R.= '</li>';
            
        }
        return '<ul>'.$R.'</ul>';
    }
    /**
     * 添加分类的select->option
     *
     * @param  integer  $p1     上级分类ID
     * @param  integer  $p2     标识第几层分类
     * @param  integer  $p3     当前分类的ID
     * @param  integer  $p4     选中分类的ID
     * @return string
     */
    function getSortOptionByParentId($p1=0,$p2=0,$p3=0,$p4=null){
        $R = $nbsp = null; $db = get_conn(); 
        for ($i=0;$i<$p2;$i++) {
            $nbsp.= "&nbsp; &nbsp;";
        }
        $res = $db->query("SELECT `sortid`,`sortname` FROM `#@_content_sort` WHERE `parentid`=? ORDER BY `sortid` ASC;",$p1);
        while ($rs = $db->fetch($res,0)) {
            if ((int)$p3 != (int)$rs[0]) {
                $model = is_bool($p3)?null:' models="'.implode(',',Content_Article::getModelsBySortId($rs[0],'modelid')).'"';
                $selected = ((int)$p4 == (int)$rs[0]) ? ' selected="selected"' : null;
                $R.= '<option'.$model.' value="'.$rs[0].'"'.$selected.'>'.$nbsp.'├'.$rs[1].'</option>';
                if ((int)$db->count("SELECT * FROM `#@_content_sort` WHERE `parentid`=".DB::quote($rs[0]).";") > 0) {
                    $R.= Content_Article::getSortOptionByParentId($rs[0],$p2+1,$p3,$p4);
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
        $num = $db->count("SELECT * FROM `#@_content_sort` WHERE `parentid`=".DB::quote($p1).";");
        return ((int)$num>0)?'2':'1';
    }
    /**
     * 根据分类ID取得关联模型的数据
     *
     * @param  integer  $p1     分类ID
     * @param  string   $p2     数据库字段
     * @return array
     */
    function getModelsBySortId($p1,$p2='modelid'){
        $db = get_conn(); $R = array();
        $res = $db->query("SELECT * FROM `#@_content_sort_model` AS `csm` LEFT JOIN `#@_content_model` AS `cm` ON `csm`.`modelid`=`cm`.`modelid` WHERE `cm`.`modelstate`=1 AND `csm`.`sortid`=?;",$p1);
        while ($rs = $db->fetch($res)) {
            $R[] = $rs[$p2];
        }
        return $R;
    }
    /**
     * 写分类关系
     *
     * @param  string   $p1     关联表名称
     * @param  integer  $p2     文档ID
     * @param  integer  $p3     分类ID
     * @return bool
     */
    function joinSort($p1,$p2,$p3){
        $db = get_conn();
        $N  = $db->count("SELECT * FROM `{$p1}` WHERE `tid`=".DB::quote($p2)." AND `type`=1 AND `sid`=".DB::quote($p3).";");
        return ((int)$N>0) ? true : $db->insert($p1,array(
            'tid'  => $p2,
            'sid'  => $p3,
            'type' => 1,
        ));
    }
    /**
     * 取得指定文档的所述分类
     *
     * @param  string   $p1     关联表名称
     * @param  integer  $p2     文档ID
     * @return array
     */
    function getSortIdsByDocId($p1,$p2){
        $db = get_conn(); $R = array();
        $res = $db->query("SELECT * FROM `{$p1}` WHERE `tid`=? AND `type`=1;",$p2);
        while ($rs = $db->fetch($res)) {
            $R[] = $rs['sid'];
        }
        return $R;
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
                $R = array_merge($R,Content_Article::getSortIdsBySortIds($rs1[0]));
            }
        }
        return array_unique($R);
    }

    function getTemplateBySortId($p1){
        $db  = get_conn(); $R = array();
        $res = $db->query("SELECT `sortemplate`,`pagetemplate` FROM `#@_content_sort` WHERE `sortid`=?;",$p1);
        if ($rs = $db->fetch($res,0)) {
            $R['sort'] = $rs[0];
            $R['page'] = $rs[1];
            // 使用模型设置的模板
            if (empty($R['sort'])) {
                $R['sort'] = $db->result("SELECT `b`.`sortemplate` FROM `#@_content_sort_model` AS `a` LEFT JOIN `#@_content_model` AS `b` ON `a`.`modelid`=`b`.`modelid` WHERE `a`.`sortid`=".DB::quote($p1)." LIMIT 0,1;");
            }
            if (empty($R['page'])) {
                $R['page'] = $db->result("SELECT `b`.`pagetemplate` FROM `#@_content_sort_model` AS `a` LEFT JOIN `#@_content_model` AS `b` ON `a`.`modelid`=`b`.`modelid` WHERE `a`.`sortid`=".DB::quote($p1)." LIMIT 0,1;");
            }
        }
        return $R;
    }
    /**
     * 统计分类和指定模型下的文档数量
     *
     * @param  integer  $p1     分类ID
     * @param  string   $p2     模型标识，用英文逗号分隔
     * @return integer
     */
    function count($p1,$p2){
        $db = get_conn(); $R = 0;
        if (empty($p2)) { return $R; }
        $p3 = explode(',',$p2);
        foreach ($p3 as $v) {
            $table = Content_Model::getJoinTableName($v);
            $R = $R + $db->count("SELECT * FROM `{$table}` WHERE `sid`=".DB::quote($p1)." AND `type`=1;");
        }
        return $R;
    }
}