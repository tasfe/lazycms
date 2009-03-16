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
	/**
     * 取得模板文件
     *
     * 根据分类id取得模板文件
     * 
     * @param int $p1
     * @return array
     */
    function getTemplateBySortId($p1){
        $db  = get_conn(); $R = array();
        $res = $db->query("SELECT `sortemplate`,`pagetemplate` FROM `#@_content_sort` WHERE `sortid`=?;",$p1);
        if ($rs = $db->fetch($res,0)) {
            $R['sort'] = $rs[0];
            $R['page'] = $rs[1];
            // 使用模型设置的模板
            if (empty($R['sort'])) {
                $R['sort'] = $db->result("SELECT `b`.`sortemplate` FROM `#@_content_sort_join` AS `a` LEFT JOIN `#@_content_model` AS `b` ON `a`.`modelid`=`b`.`modelid` WHERE `a`.`sortid`=".DB::quote($p1)." LIMIT 0,1;");
            }
            if (empty($R['page'])) {
                $R['page'] = $db->result("SELECT `b`.`pagetemplate` FROM `#@_content_sort_join` AS `a` LEFT JOIN `#@_content_model` AS `b` ON `a`.`modelid`=`b`.`modelid` WHERE `a`.`sortid`=".DB::quote($p1)." LIMIT 0,1;");
            }
        }
        return $R;
    }
    /**
     * 生成文章
     *
     * @param string $modelename
     * @param string $ids
     * @return bool
     */
    function create($modelename,$ids){
        import('system.parsetags');
        $tag    = new ParseTags(); $db = get_conn();
        $table  = Content_Model::getDataTableName($modelename);
        $model  = Content_Model::getModelByEname($modelename);
        $fields = json_decode($model['modelfields']);
        $result = $db->query("SELECT * FROM `{$table}` WHERE `id` IN({$ids});");
        while ($rs = $db->fetch($result)) {
            // 取得模板地址
            $template = Content_Article::getTemplateBySortId($rs['sortid']);print_r($template);
            $tmplpath = LAZY_PATH.'/'.c('TEMPLATE').'/'.$template['page'];
            $tag->loadHTML($tmplpath);
            // 替换模板中的标签
            $tag->clear();
            // 替换自定义字段标签
            foreach ($fields as $field) {
                $tag->value($field->ename,$rs[$field->ename]);
            }
            // 设置标签值
            $tag->value(array(
                'id'        => $rs['id'],
                'date'      => $rs['date'],
                'hits'      => $rs['hits'],
                'digg'      => $rs['digg'],
                'path'      => SITE_BASE.$rs['path'],
                'userid'        => $rs['userid'],
                'description'   => $rs['description'],
            ));
            // 解析模板
            $outHTML = $tag->parse();
            $outFile = LAZY_PATH.'/'.$rs['path'];
            mkdirs(dirname($outFile)); save_file($outFile,$outHTML);
        }
        return true;
    }
}