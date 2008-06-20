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
    // getTopGroupId *** *** www.LazyCMS.net *** ***
    static function getTopGroupId(){
        $db  = getConn();
        $res = $db->query("SELECT `groupid` FROM `#@_passport_group` WHERE 1 ORDER BY `groupid` DESC;");
        if ($data = $db->fetch($res,0)) {
            return $data[0];
        } else {
            return 0;
        }
    }
    // __group *** *** www.LazyCMS.net *** ***
    static function __group($l1,$l2,$l3=null){
        // $l1:groupid, $l2:current groupid, $l3:selected
        $I1 = null;
        $db  = getConn();
        $res = $db->query("SELECT `groupid`,`groupname` FROM `#@_passport_group` WHERE 1 ORDER BY `groupid` DESC;");
        while ($data = $db->fetch($res,0)) {
            if ($l2 != $data[0]) {
                $selected = ((int)$l3 == (int)$data[0]) ? ' selected="selected"' : null;
                $I1 .= '<option value="'.$data[0].'"'.$selected.'>'.$data[1].'</option>';
            }
        }
        return $I1;
    }
    // getModel *** *** www.LazyCMS.net *** ***
    static function getModel($l1){
        $db  = getConn();
        $res = $db->query("SELECT * FROM `#@_passport_group` WHERE `groupid` = ?;",$l1);
        if ($data = $db->fetch($res)) {
            return $data;
        } else {
            return false;
        }
    }
    // getData *** *** www.LazyCMS.net *** ***
    static function getData($l1,$l2){
        $db  = getConn(); $I1 = array();
        $res = $db->query("SELECT * FROM `{$l2}` WHERE `userid` = ?;",$l1);
        if (!$data = $db->fetch($res)) {
            return false;
        }
        return $data;
    }
    // navLogout *** *** www.LazyCMS.net *** ***
    static function navigation($l1=null){
        $db = getConn(); $field = empty($l1) ? 'navlogin' : $l1;
        $I1 = $db->result("SELECT `{$field}` FROM `#@_passport_config` WHERE `systemname` = 'LazyCMS';");
        return self::formatLink($I1);
    }
    // formatLink *** *** www.LazyCMS.net *** ***
    static function formatLink($l1){
        $I1 = ""; $result = $l1;
        if (strlen($result)) {
            $I2 = sect($result,"(\{language\=".language().")","(\})","");
            $I2 = str_replace(chr(10).chr(10),chr(10),str_replace(chr(13),chr(10),$I2));
            if (strpos($I2,'(lazy:')!==false) {
                $tag = O('Tags');
                $I2  = $tag->createhtm($I2);
            }
            $I3 = explode(chr(10),$I2);
            foreach ($I3 as $I4) {
                if (strlen(trim($I4))>0) {
                    if (strpos($I4,'|')===false) { continue; }
                    $I5 = explode('|',$I4); if (count($I5)<2) { continue; }
                    $I5[0] = trim($I5[0]); $I5[1] = trim($I5[1]);
                    if (instr('http:/,ftp://,https:',substr($I5[1],0,6))) {
                        $I1.= '<a href="'.$I5[1].'" target="_blank">'.htmlencode($I5[0]).'</a>';
                    } elseif (strtolower(substr($I5[1],6))=='logout') {
                        $I1.= '<a href="'.$I5[1].'" onclick="javascript:return confirm(\''.t2js(L("confirm/logout")).'\');">'.htmlencode($I5[0]).'</a>';
                    } else {
                        $I1.= '<a href="'.$I5[1].'">'.htmlencode($I5[0]).'</a>';
                    }
                }
            }
        }
        return $I1;
    }
    // checker *** *** www.LazyCMS.net *** ***
    static function checker(){
        $db = getConn(); $module = getObject();
        $username = Cookie::get('username');
        $userpass = Cookie::get('userpass');
        if (!empty($username) && !empty($userpass)) {
            $res = $db->query("SELECT * FROM `#@_passport` WHERE `username` = ?;",$username);
            if ($data = $db->fetch($res)) {
                if ($userpass==$data['userpass']) {
                    $module->passport = $data;
                    $groupid = $data['groupid'];
                    $res = $db->query("SELECT * FROM `#@_passport_group` WHERE `groupid` = ?;",$groupid);
                    if ($data = $db->fetch($res)) {
                        $module->passport = array_merge($module->passport,$data);
                        $grouptable = $data['grouptable'];
                        $res = $db->query("SELECT * FROM `{$grouptable}` WHERE `userid` = ?;",$module->passport['userid']);
                        if ($data = $db->fetch($res)) {
                            $module->passport = array_merge($module->passport,$data);
                        }
                    }
                    return true;
                }
            }
        }
        return false;
    }
    // tags *** *** www.LazyCMS.net *** ***
    static function tags($tags,$inValue){
        $db = getConn(); $tag = O('Tags');
        $jsType = strtolower($tag->getLabel($tags,'type'));
        switch (strtolower($jsType)) {
            case 'usernav':
                $I1 = '<script type="text/javascript" src="'.url('Passport','UserNav').'"></script>';
                break;
        }
        return $I1;
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
        $salt   = salt(4);
        if (!$isDeleteTable) {
            if ($db->isTable($data[2])) {
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
    // showTypes *** *** www.LazyCMS.net *** ***
    static function showTypes($l1=null){
        $I1 = null; $module = getObject();
        $l2 = array(
            'input'    => 'varchar',   // 输入框
            'textarea' => 'text',      // 文本框
            'radio'    => 'varchar',   // 单选框
            'checkbox' => 'varchar',   // 复选框
            'select'   => 'varchar',   // 下拉菜单
            'basic'    => 'text',      // 简易编辑器
            'editor'   => 'mediumtext',// 内容编辑器
            'date'     => 'datetime',  // 日期选择器
            'upfile'   => 'varchar',   // 文件上传框
        );
        foreach ($l2 as $k => $v){
            $selected = ((string)$l1 == (string)$k) ? ' selected="selected"' : null;
            $I1 .= '<option value="'.$k.'" type="'.$v.'"'.$selected.'>'.$module->L('list/field/type/'.$k).'</option>';
        }
        return $I1;
    }
    // uninstSQL *** *** www.LazyCMS.net *** ***
    static function uninstSQL(){
        return <<<SQL
            DROP TABLE IF EXISTS `#@_passport`;
            DROP TABLE IF EXISTS `#@_passport_group`;
            DROP TABLE IF EXISTS `#@_passport_fields`;
            DROP TABLE IF EXISTS `#@_passport_config`;
SQL;
    }
    // instsql *** *** www.LazyCMS.net *** ***
    static function instSQL(){
        return <<<SQL
            // 用户表
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
            CREATE TABLE IF NOT EXISTS `#@_passport_group` (
              `groupid` int(11) NOT NULL auto_increment,    # 编号
              `groupname` varchar(30) NOT NULL,             # 用户组名称
              `groupename` varchar(50) NOT NULL,            # 用户组标识
              `grouptable` varchar(50) NOT NULL,            # 附加表
              `template` varchar(255),                      # 模板
              `purview` text,                               # 组权限
              `groupstate` int(11) default '0',             # 状态 0:启用 1:禁用
              PRIMARY KEY  (`groupid`),
              KEY `groupname` (`groupname`)
            ) ENGINE=MyISAM DEFAULT CHARSET=#~lang~#;
            // 用户信息模型字段
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
            // 会员系统设置
            CREATE TABLE IF NOT EXISTS `#@_passport_config` (
              `systemname` varchar(20),                     # 系统名称
              `navlogout` text,                             # 用户导航 未登录
              `navlogin` text,                              # 用户导航 已登录
              `navuser` text,                               # 登入后的用户菜单
              `reservename` text,                           # 限制注册的会员名
              PRIMARY KEY  (`systemname`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=#~lang~#;
            // 添加默认设置数据
            INSERT INTO `#@_passport_config`(
              `systemname`,`navlogout`,`navlogin`,`navuser`,`reservename`
            )VALUES(
              'LazyCMS',
              '{language=zh-cn\n  用户中心|(lazy:url module="Passport" action="Main"/)\n  退出|(lazy:url module="Passport" action="Logout"/)\n}',
              '{language=zh-cn\n  注册|(lazy:url module="Passport" action="Register"/)\n  登录|(lazy:url module="Passport" action="Login"/)\n}',
              '{language=zh-cn\n  用户中心|(lazy:url module="Passport" action="Main"/)\n  更新密码|(lazy:url module="Passport" action="UpdatePass"/)\n  更新资料|(lazy:url module="Passport" action="UserConfig"/)\n  退出|(lazy:url module="Passport" action="Logout"/)\n}',
              'fuck,江泽民,系统,管理员,法轮,admin'
            );
            // 短消息
            CREATE TABLE IF NOT EXISTS `#@_passport_message` (
              `msgid` int(11) NOT NULL auto_increment,
              `isview` int(11) default '0',                 # 是否阅读 1:已读
              `letuser` varchar(30),                        # 发送人
              `username` varchar(30),                       # 接收人
              `msgtitle` varchar(100),                      # 短信标题
              `msgcontent` text,                            # 短信内容
              `msgdate` int(11) default '0',                # 发送时间
              PRIMARY KEY  (`msgid`),
              KEY `letuser` (`letuser`),
              KEY `username` (`username`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=#~lang~#;
SQL;
    }
}