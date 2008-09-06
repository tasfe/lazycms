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
 * @date        2008-8-11
 */
// Model *** *** www.LazyCMS.net *** ***
class Model{
    // getDataTableName *** *** www.LazyCMS.net *** ***
    static function getDataTableName($p1){
        $db = get_conn(); if (strlen($p1)==0) { return false; }
        return str_replace('#@_',$db->config('prefix'),"#@_content_data_{$p1}");
    }
    // getJoinTableName *** *** www.LazyCMS.net *** ***
    static function getJoinTableName($p1){
        $db = get_conn(); if (strlen($p1)==0) { return false; }
        return str_replace('#@_',$db->config('prefix'),"#@_content_join_{$p1}");
    }
    // getType *** *** www.LazyCMS.net *** ***
    static function getType($p1=null){
        $R = array(
            'input'    => 'varchar',        // 输入框
            'textarea' => 'text',           // 文本框
            'radio'    => 'varchar(255)',   // 单选框
            'checkbox' => 'varchar(255)',   // 复选框
            'select'   => 'varchar(255)',   // 下拉菜单
            'basic'    => 'text',           // 简易编辑器
            'editor'   => 'mediumtext',     // 内容编辑器
            'date'     => 'int(11)',        // 日期选择器
            'upfile'   => 'varchar(255)',   // 文件上传框
        );
        return empty($p1) ? $R : $R[$p1];
    }
    // getValidate *** *** www.LazyCMS.net *** ***
    static function getValidate(){
        return array(
            'empty'  => '%s|0|'.L('model/validate/empty/@err'),
            'limit'  => '%s|1|'.L('model/validate/limit/@err').'|1-100',
            'equal'  => '%s|2|'.L('model/validate/equal/@err').'|[field]',
            'email'  => '%s|validate|'.L('model/validate/email/@err').'|4',
            'letter' => '%s|validate|'.L('model/validate/letter/@err').'|1',
            'number' => '%s|validate|'.L('model/validate/number/@err').'|2',
            'url'    => '%s|validate|'.L('model/validate/url/@err').'|5',
            'custom' => '%s|validate|'.L('model/validate/custom/@err').'|'.L('model/validate/custom/@reg').'',
        );
    }
    // getModels *** *** www.LazyCMS.net *** ***
    static function getModels($p1=null){
        $db  = get_conn(); $R = array();
        $in  = empty($p1) ? null : DB::quoteInto('And `modelename`=?',$p1);
        $res = $db->query("SELECT * FROM `#@_content_model` WHERE `modelstate`=1 {$in} ORDER BY `modelid` ASC");
        while ($rs = $db->fetch($res)) {
            if (empty($p1)) {
                $R[] = $rs;
            } else {
                return $rs;
            }
        }
        return empty($R)?false:$R;
    }
}
