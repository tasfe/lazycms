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
class Passport{
    // tags *** *** www.LazyCMS.net *** ***
    static function tags($tags,$inValue){
        return true;
    }
    // installModel *** *** www.LazyCMS.net *** ***
    static function installModel($groupCode,$isDeleteTable=false){
        $db       = getConn();
        $groupDom = DOMDocument::loadXML($groupCode);
        $XPath    = new DOMXPath($groupDom);
        // Model Value
        $data[] = $XPath->evaluate("//lazycms/group/groupname")->item(0)->nodeValue;
        $data[] = $XPath->evaluate("//lazycms/group/groupename")->item(0)->nodeValue;
        $data[] = '#@_'.$XPath->evaluate("//lazycms/group/grouptable")->item(0)->nodeValue;
        $data[] = $XPath->evaluate("//lazycms/group/purview")->item(0)->nodeValue;
        $data[] = $XPath->evaluate("//lazycms/group/groupstate")->item(0)->nodeValue;
        if (!$isDeleteTable) {
            if ($db->isTable($data[2])) {
                $salt = salt(4);
                $data[1].= '_'.$salt;
                $data[2].= '_'.$salt;
            }
        }
        // Insert model
        $row = array(
            'groupname'  => $data[0],
            'groupename' => $data[1],
            'grouptable' => $data[2],
            'purview'    => $data[3],
            'groupstate' => $data[4],
        );
        $db->insert('#@_passport_group',$row);

        // Insert fields
        $inSQL      = null;
        $indexSQL   = null;
        $groupid    = $db->lastInsertId();
        $objFields  = $groupDom->getElementsByTagName('fields')->item(0)->childNodes;
        $fieldCount = $objFields->length;
        for ($i=0; $i<$fieldCount; $i++) {
            $row       = array();
            $objItem   = $objFields->item($i)->childNodes;
            $itemCount = $objItem->length;
            for ($j=0; $j<$itemCount; $j++) {
                $row[$objItem->item($j)->nodeName] = $objItem->item($j)->nodeValue;
            }
            $row = array_merge($row,array(
                'groupid'    => $groupid,
                'fieldorder' => $db->max('fieldid','#@_passport_fields'),
                'fieldindex' => $row['fieldindex'],
            ));
            if (instr('text,mediumtext,datetime',$row['fieldtype'])) {
                $row['fieldlength'] = null;
            } else {
                $row['fieldlength'] = !empty($row['fieldlength']) ? $row['fieldlength'] : 255;
            }
            $length  = !empty($row['fieldlength']) ? "( ".$row['fieldlength']." ) " : null;
            if ((string)$row['fieldtype']!='datetime') {
                $default = (string)$row['fieldefault'] ? " default '".t2js($row['fieldefault'])."' " : null;
            } else {
                $default = null;
            }
            $inSQL.= "`".$row['fieldename']."` ".$row['fieldtype'].$length.$default.",";
            if (!empty($row['fieldindex'])){ 
                $indexSQL.= "KEY `".$row['fieldename']."` (`".$row['fieldename']."`),";
            }
            $db->insert('#@_passport_fields',$row);
        }
        $db->exec("DROP TABLE IF EXISTS `".$data[2]."`;");
        // 创建新表
        $db->exec("CREATE TABLE IF NOT EXISTS `".$data[2]."` (
                    `userid` int(11) NOT NULL,
                    {$inSQL}{$indexSQL}
                    PRIMARY KEY (`userid`)
                   ) ENGINE=MyISAM DEFAULT CHARSET=#~lang~#;");
    }
    // instsql *** *** www.LazyCMS.net *** ***
    static function instSQL(){
        return <<<SQL
            // 用户表
            DROP TABLE IF EXISTS `#@_passport`;
            CREATE TABLE IF NOT EXISTS `#@_passport` (
              `userid` int(11) NOT NULL auto_increment,     # 编号
              `groupid` int(11) default '0',                # 用户所属组
              `username` varchar(30) NOT NULL,              # 用户名称
              `userpass` varchar(32) NOT NULL,              # 用户密码
              `userkey` varchar(6),                         # 随机密码key
              `userdate` int(11) default '0',               # 添加时间
              `usermail` varchar(100),                      # Email
              `mailis` int(11) default '0',                 # 是否显示 Mail
              `question` varchar(50),                       # 找回密码的提示问题
              `answer` varchar(50),                         # 找回密码的回答
              `language` varchar(30) NOT NULL,              # 语言包
              `editor` varchar(30) NOT NULL,                # 编辑器
              `isdel` int(11) default '0',                  # 是否删除 0:正常 1:删除（删除之后就禁用此用户名）
              `islock` int(11) default '0',                 # 锁定用户 0:启用 1:禁用
              PRIMARY KEY  (`userid`),
              KEY `username` (`username`),
              KEY `usermail` (`usermail`),
              KEY `isdel` (`isdel`),
              KEY `islock` (`islock`)
            ) ENGINE=MyISAM DEFAULT CHARSET=#~lang~#;
            // 用户组表
            DROP TABLE IF EXISTS `#@_passport_group`;
            CREATE TABLE IF NOT EXISTS `#@_passport_group` (
              `groupid` int(11) NOT NULL auto_increment,    # 编号
              `groupname` varchar(30) NOT NULL,             # 用户组名称
              `groupename` varchar(50) NOT NULL,            # 用户组标识
              `grouptable` varchar(50) NOT NULL,            # 附加表
              `purview` text,                               # 组权限
              `groupstate` int(11) default '0',             # 状态 0:启用 1:禁用
              PRIMARY KEY  (`groupid`),
              KEY `groupname` (`groupname`)
            ) ENGINE=MyISAM DEFAULT CHARSET=#~lang~#;
            // 用户信息模型字段
            DROP TABLE IF EXISTS `#@_passport_fields`;
            CREATE TABLE IF NOT EXISTS `#@_passport_fields` (
              `fieldid` int(11) NOT NULL auto_increment,
              `groupid` int(11) NOT NULL,                   # 所属模型
              `fieldorder` int(11),                         # 字段排序
              `fieldname` varchar(50),                      # 表单文字
              `fieldename` varchar(50),                     # 字段名
              `fieldtype` varchar(20),                      # 类型
              `fieldlength` varchar(255),                   # 长度
              `fieldefault` varchar(255),                   # 默认值
              `fieldindex` int(11) default '0',             # 是否索引 0:不索引 1:索引
              `inputtype` varchar(20),                      # 输入框类型
              `fieldvalue` varchar(255),                    # radio,checkbox,select 值
              PRIMARY KEY  (`fieldid`),
              KEY `groupid` (`groupid`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=#~lang~#;
SQL;
    }
}