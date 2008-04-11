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
SQL;
    }
}