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
 * 系统后台 Module 层
 *
 * @copyright   Copyright (c) 2007-2008 lazycms.net All rights reserved.
 * @author      Lukin <mylukin@gmail.com>
 */
class System{
    // header *** *** www.LazyCMS.net *** ***
    static function header(){
        $tpl = O('Template');
        $tpl->path = LAZY_PATH.C('PAGES_PATH').'/system/template';
        $tpl->display('header.php');
    }
    // header *** *** www.LazyCMS.net *** ***
    static function footer(){
        $tpl = O('Template');
        $tpl->path = LAZY_PATH.C('PAGES_PATH').'/system/template';
        $tpl->display('footer.php');
    }
    // showTables *** *** www.LazyCMS.net *** ***
    static function showTables($l1=null){
        $db  = getConn(); $I1 = null;
        $res = $db->query("SHOW TABLES LIKE '%archives%'");
        while ($data = $db->fetch($res,0)) {
            $selected = ((string)$l1==(string)$data[0]) ? ' selected="selected"' : null;
            $I1 .= '<option value="'.$data[0].'"'.$selected.'>'.$data[0].'</option>';
        }
        return $I1;
    }
    // showTypes *** *** www.LazyCMS.net *** ***
    static function showTypes($l1=null){
        $I1 = null;
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
            $I1 .= '<option value="'.$k.'" type="'.$v.'"'.$selected.'>'.L('models/field/type/'.$k).'</option>';
        }
        return $I1;
    }
    // filesPath *** *** www.LazyCMS.net *** ***
    static function filesPath($l1,$l2){
        $I1 = "<a href=\"javascript:Root();\" onclick=\"\$('#{$l2}').browseFiles('".url('System','browseFiles')."','/');return false;\">Root://</a>";
        if (empty($l1)) { return $I1; }
        $paths = explode('/',$l1);
        $count = count($paths);
        $I2 = null;
        foreach ($paths as $k=>$path) {
            if ($k!=0) {
                $I1.= "/";
            }
            if (empty($I2)) {
                $I2 = $path;
            } else {
                $I2.= '/'.$path;
            }
            if ((int)$k==(int)$count-1) {
                $I1.= "<strong>{$path}</strong>";
            } else {
                $I1.= "<a href=\"javascript:Address('{$I2}');\" onclick=\"\$('#{$l2}').browseFiles('".url('System','browseFiles')."','{$I2}');return false;\">{$path}</a>";
            }
        }
        return $I1;
    }
    // installModel *** *** www.LazyCMS.net *** ***
    static function installModel($modelCode,$isDeleteTable=false){
        $db       = getConn();
        $modelCode= base64_decode($modelCode);
        $modelDom = DOMDocument::loadXML($modelCode);
        $XPath    = new DOMXPath($modelDom);
        // Model Value
        $data[0] = $XPath->evaluate("//lazycms/model/modelname")->item(0)->nodeValue;
        $data[1] = $XPath->evaluate("//lazycms/model/modelename")->item(0)->nodeValue;
        $data[2] = $XPath->evaluate("//lazycms/model/maintable")->item(0)->nodeValue;
        $data[3] = $XPath->evaluate("//lazycms/model/addtable")->item(0)->nodeValue;
        $data[4] = $XPath->evaluate("//lazycms/model/modelstate")->item(0)->nodeValue;
		if (!$isDeleteTable) {
			$res = $db->query("SHOW TABLES LIKE '".$data[3]."'");
			if ($db->fetch($res,0)) {
				$data[1].= '_'.salt(4);
				$data[3].= '_'.salt(4);
			}
		}
        // Insert model
        $row = array(
            'modelname'  => $data[0],
            'modelename' => $data[1],
            'maintable'  => $data[2],
            'addtable'   => $data[3],
            'modelstate' => $data[4],
        );
        $db->insert('model',$row);

        // Insert fields
        $inSQL      = null;
        $indexSQL   = null;
        $modelid    = $db->lastInsertId();
        $objFields  = $modelDom->getElementsByTagName('fields')->item(0)->childNodes;
        $fieldCount = $objFields->length;
        for ($i=0; $i<$fieldCount; $i++) {
            $row       = array();
            $objItem   = $objFields->item($i)->childNodes;
            $itemCount = $objItem->length;
            for ($j=0; $j<$itemCount; $j++) {
                $row[$objItem->item($j)->nodeName] = $objItem->item($j)->nodeValue;
            }
            $row = array_merge($row,array(
                'modelid'    => $modelid,
                'fieldorder' => $db->max('fieldid','fields'),
                'fieldindex' => (string)$row['fieldindex'],
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
            $db->insert('fields',$row);
        }
        $db->exec("DROP TABLE IF EXISTS `".$data[3]."`;");
        // 创建新表
        $db->exec("CREATE TABLE IF NOT EXISTS `".$data[3]."` (
                    `aid` int(11) NOT NULL,
                    {$inSQL}{$indexSQL}
                    PRIMARY KEY (`aid`)
                   ) ENGINE=MyISAM DEFAULT CHARSET=#~lang~#;");
    }
    // instsql *** *** www.LazyCMS.net *** ***
    static function instSQL(){
        return <<<SQL
            // 系统表
            DROP TABLE IF EXISTS `#@_system`;
            CREATE TABLE IF NOT EXISTS `#@_system` (
              `systemname` varchar(10) default 'LazyCMS',   # 系统名称
              `systemver` varchar(10) NOT NULL,             # 系统版本
              `dbversion` varchar(10) NOT NULL,             # 数据库版本
              `sitename` varchar(50) NOT NULL,              # 网站名称
              `sitemail` varchar(100) NOT NULL,             # 管理员邮箱
              `sitekeywords` text,                          # 系统关键词
              `lockip` text,                                # 锁定的IP
              `modules` text,                               # 已经安装的模块列表
              `instdate` int(11) default '0',               # 系统安装日期
              PRIMARY KEY  (`systemname`)
            ) ENGINE=MyISAM DEFAULT CHARSET=#~lang~#;
            // 管理员表
            DROP TABLE IF EXISTS `#@_admin`;
            CREATE TABLE IF NOT EXISTS `#@_admin` (
              `adminid` int(11) NOT NULL auto_increment,    # 编号
              `adminname` varchar(30) NOT NULL,             # 管理员名称
              `adminpass` varchar(32) NOT NULL,             # 管理员密码
              `adminkey` varchar(6),                        # 随机密码key
              `adminlevel` text,                            # 管理员级别
              `adminlanguage` varchar(30) NOT NULL,         # 语言包
              `admineditor` varchar(30) NOT NULL,           # 编辑器
              `admindate` int(11) default '0',              # 添加时间
              `diymenu` text,                               # 自定义菜单
              PRIMARY KEY  (`adminid`),
              KEY `adminname` (`adminname`)
            ) ENGINE=MyISAM DEFAULT CHARSET=#~lang~#;
            // 日志
            DROP TABLE IF EXISTS `#@_log`;
            CREATE TABLE IF NOT EXISTS `#@_log` (
              `logid` int(11) NOT NULL auto_increment,      # 编号
              `adminname` varchar(30) NOT NULL,             # 管理员名称
              `ip` varchar(20) NOT NULL,                    # 登录IP
              `lognum` int(11) default '0',                 # 错误级别
              `logdate` int(11) default '0',                # 登录日期
              PRIMARY KEY  (`logid`)
            ) ENGINE=MyISAM DEFAULT CHARSET=#~lang~#;
            // 自定义菜单
            DROP TABLE IF EXISTS `#@_diymenu`;
            CREATE TABLE IF NOT EXISTS `#@_diymenu` (
              `diymenuid` int(11) NOT NULL auto_increment,  # 编号
              `diymenulang` varchar(10) NOT NULL,           # 所属语言
              `diymenu` text,                               # 自定义菜单内容
              PRIMARY KEY  (`diymenuid`),
              KEY `diymenulang` (`diymenulang`)
            ) ENGINE=MyISAM DEFAULT CHARSET=#~lang~#;
            // 公共存档
            DROP TABLE IF EXISTS `#@_archives`;
            CREATE TABLE IF NOT EXISTS `#@_archives` (
              `id` int(11) NOT NULL auto_increment,
              `sortid` int(11) default '0',                 # 分类编号
              `title` varchar(255) NOT NULL,                # 标题
              `show` tinyint(1) default '0',                # 显示
              `commend` tinyint(1) default '0',             # 推荐
              `top` tinyint(1) default '0',                 # 置顶
              `img` varchar(255),                           # 图片
              `path` varchar(255) NOT NULL,                 # 路径
              `date` int(11) NOT NULL,                      # 发布时间
              PRIMARY KEY  (`id`),
              UNIQUE KEY `path` (`path`),
              KEY `sortid` (`sortid`),
              KEY `show` (`show`),
              KEY `commend` (`commend`),
              KEY `top` (`top`)
            ) ENGINE=MyISAM DEFAULT CHARSET=#~lang~#;
            // 自定义模型
            DROP TABLE IF EXISTS `#@_model`;
            CREATE TABLE IF NOT EXISTS `#@_model` (
              `modelid` int(11) NOT NULL auto_increment,
              `modelname` varchar(50) NOT NULL,             # 模块名称
              `modelename` varchar(50) NOT NULL,            # 模块E名称
              `maintable` varchar(50) NOT NULL,             # 主索引表
              `addtable` varchar(50) NOT NULL,              # 附加表
              `modelstate` enum('0','1') default '0',       # 状态 0:启用 1:禁用
              PRIMARY KEY  (`modelid`)
            ) ENGINE=MyISAM DEFAULT CHARSET=#~lang~#;
            // 模型字段
            DROP TABLE IF EXISTS `#@_fields`;
            CREATE TABLE IF NOT EXISTS `#@_fields` (
              `fieldid` int(11) NOT NULL auto_increment,
              `modelid` int(11) NOT NULL,                   # 所属模型
              `fieldorder` int(11),                         # 所属模型
              `fieldname` varchar(50),                      # 表单文字
              `fieldename` varchar(50),                     # 字段名
              `fieldtype` varchar(20),                      # 类型
              `fieldlength` varchar(255),                   # 长度
              `fieldefault` varchar(255),                   # 默认值
              `fieldindex` enum('0','1') default '0',       # 是否索引 0:不索引 1:索引
              `inputtype` varchar(20),                      # 输入框类型
              `fieldvalue` varchar(255),                    # radio,checkbox,select 值
              PRIMARY KEY  (`fieldid`),
              KEY `modelid` (`modelid`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=#~lang~#;
            // 分类
            DROP TABLE IF EXISTS `#@_sort`;
            CREATE TABLE IF NOT EXISTS `#@_sort` (
              `sortid` int(11) NOT NULL auto_increment,
              `sortid1` int(11) default '0',                # 所属分类
              `modelid` int(11) default '0',                # 模型编号
              `sortorder` int(11) NOT NULL,                 # 排序
              `sortname` varchar(50) NOT NULL,              # 分类名称
              `sortpath` varchar(255) NOT NULL,             # 路径
              `keywords` varchar(255),                      # meta 关键词
              `description` varchar(255),                   # meta 简述
              `sortopen` enum('0','1') default '0',         # 是否展开 0:关闭 1:展开
              `sorttemplate1` varchar(255),                  # 分类页外模板
              `sorttemplate2` varchar(255),                  # 分类页内模板
              `pagetemplate1` varchar(255),                  # 内容页外模板
              `pagetemplate2` varchar(255),                  # 内容页内模板
              PRIMARY KEY  (`sortid`),
              UNIQUE KEY `sortpath` (`sortpath`),
              KEY `sortid1` (`sortid1`),
              KEY `sortname` (`sortname`),
              KEY `modelid` (`modelid`)
            ) ENGINE=MyISAM DEFAULT CHARSET=#~lang~#;
SQL;
    }
}