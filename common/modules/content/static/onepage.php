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
 * 单页管理公共模块
 */
class Content_Onepage{
    /**
     * 生成单页面
     *
     * @param string $modelname
     * @param string $ids
     * @return bool
     */
    function createPage($modelename,$ids){
        import('system.keywords');  $key = new Keywords($modelename);
        import('system.parsetags'); $tag = new ParseTags();
        $db     = get_conn(); $template = c('TEMPLATE_DEFAULT');
        $table  = Content_Model::getDataTableName($modelename);
        $model  = Content_Model::getModelByEname($modelename);
        $fields = json_decode($model['modelfields']);
        $result = $db->query("SELECT * FROM `{$table}` WHERE `id` IN({$ids});");
        while ($rs = $db->fetch($result)) {
            // 取得模板地址
			$res = $db->query("SELECT `pagetemplate` FROM `#@_content_model` WHERE `modelename`=?;",$modelename);
			if ($rs1 = $db->fetch($res,0)) { $template = $rs1[0]; }
			// 加载模板
            $tag->loadHTML(LAZY_PATH.'/'.c('TEMPLATE').'/'.$template);
            // 清除标签值
            $tag->clear();
            // 定义内部变量
            $tag->V(array(
                'id'        => $rs['id'],
                'sortid'    => $rs['sortid'],
                'date'      => $rs['date'],
                'hits'      => $rs['hits'],
                'digg'      => $rs['digg'],
                'path'      => SITE_BASE.$rs['path'],
                'keywords'    => $key->get($rs['id']),
                'description' => $rs['description'],
            ));
            // 添加自定义字段变量
            foreach ($fields as $field) {
                $tag->V($field->ename,$rs[$field->ename]);
            }
            // 解析模板变量
            $outHTML = $tag->parse();
            $outFile = LAZY_PATH.'/'.$rs['path'];
            mkdirs(dirname($outFile)); save_file($outFile,$outHTML);
        }
        return true;
    }
}