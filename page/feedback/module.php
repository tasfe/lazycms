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
 * | Author: Lukin <mylukin@gmail.com>                                         |
 * +---------------------------------------------------------------------------+
 */
defined('CORE_PATH') or die('Restricted access!');
/**
 * Module 层
 *
 * @copyright   Copyright (c) 2007-2008 lazycms.net All rights reserved.
 * @author      Lukin <mylukin@gmail.com>
 */
class FeedBack{
    public static $addTable = '#@_feedback_custom';
    // tags *** *** www.LazyCMS.net *** ***
    static function tags($tags,$inValue){
        return true;
    }
    // uninstSQL *** *** www.LazyCMS.net *** ***
    static function uninstSQL(){
        $addTable = self::$addTable;
        return <<<SQL
            DROP TABLE IF EXISTS `#@_feedback`;
            DROP TABLE IF EXISTS `#@_feedback_fields`;
            DROP TABLE IF EXISTS `{$addTable}`;
SQL;
    }
    // instsql *** *** www.LazyCMS.net *** ***
    static function instSQL(){
        return <<<SQL
            // 留言反馈
            CREATE TABLE IF NOT EXISTS `#@_feedback` (
              `fbid` int(11) NOT NULL auto_increment,
              `isview` int(11) default '0',             # 是否已读
              `istag` int(11) default '0',              # 是否加星
              `fbtitle` varchar(100),                   # 标题
              `fbcontent` text,                         # 内容
              `fbip` varchar(20),                       # IP地址
              `fbdate` int(11) default '0',             # 添加日期
              PRIMARY KEY  (`fbid`)
            ) ENGINE=MyISAM DEFAULT CHARSET=#~lang~#;
            // 留言自定义字段
            CREATE TABLE IF NOT EXISTS `#@_feedback_fields` (
              `fieldid` int(11) NOT NULL auto_increment,
              `fieldorder` int(11),                         # 字段排序
              `fieldname` varchar(50),                      # 表单文字
              `fieldename` varchar(50),                     # 字段名
              `fieldtype` varchar(20),                      # 类型
              `fieldlength` varchar(255),                   # 长度
              `fieldefault` varchar(255),                   # 默认值
              `inputtype` varchar(20),                      # 输入框类型
              `fieldvalue` varchar(255),                    # radio,checkbox,select 值
              PRIMARY KEY  (`fieldid`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=#~lang~#;
SQL;
    }
}