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
 * | Copyright (C) 2007-2010 LazyCMS.com All rights reserved.                  |
 * +---------------------------------------------------------------------------+
 * | LazyCMS is free software. See LICENSE for copyright notices and details.  |
 * +---------------------------------------------------------------------------+
 */
defined('COM_PATH') or die('Restricted access!');

/**
 * 检测系统是否正确安装
 *
 * @return bool
 */
function installed() {
    $result = false;
    // 能取到安装日期
    if (C('Installed')) {
        $db = @get_conn();
        // 数据库链接不正确
        if (!$db) return $result;
        $tables = array(
            'option','model','user','user_meta',
            'publish','post','post_meta','comments',
            'term','term_relation','term_taxonomy','term_taxonomy_meta',
        );
        $table_ok = true;
        // 检查数据表是否正确
        foreach($tables as $table) {
            if (false === $db->is_table('#@_'.$table)) {
                $table_ok = false;
            }
        }
        $result = $table_ok;
    }
    return $result;
}
/**
 * wrapper
 *
 * @param string $html
 * @return void
 */
function install_wrapper($html) {
    system_head('jslang',array(
        'Rock it!' => __('Rock it!'),
    ));
    echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
    echo '<html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
    echo '<title>'.__('LazyCMS Setup').'</title>'; loader_css('css/install'); loader_js('js/common'); loader_js('js/install');
    echo '<script type="text/javascript">'.system_jslang().'$(document).ready(install_init);</script></head><body>';
    echo '<h1 id="logo">'.__('LazyCMS Setup').'</h1>';
    echo $html.'</body></html>';
}
/**
 * 安装默认设置
 *
 * @return bool
 */
function install_defaults($initial) {

    // 默认设置
    $options = array(
        // 2.0
        'Installed'         => W3cDate(),
        'SiteTitle'         => __('My Site'),
        'Language'          => 'zh-CN',
        'Compress'          => 0,
        'Tags-Service'      => 1,
        'Timezone'          => 'Asia/Shanghai',
        'Template'          => 'lazycms',
        'Template-404'      => '~404.html',
        'Template-Tags'     => '~tags.html',
        'Template-Comments' => '~comments.html',
        'TemplateSuffixs'   => 'htm,html',
        'HTMLFileSuffix'    => '.html',
        'Comments-Path'     => 'comments/',
    );
    // 覆盖或升级设置
    foreach($options as $k=>$v) {
        if (C($k)===null) {
            C($k,$v);
        }
    }
    // 安装初始化数据
    if ($initial) {
        $db = get_conn();
        // 创建首页
        $db->query("INSERT INTO `#@_post` VALUES (1, 0, 1, 'page', '', 'Lukin', 'index.html', '".__('Home')."', '', 0, 0, 'passed', 'No', 0, 0, 'home.html', '');");
    }
    return true;
}
/**
 * 表结构
 *
 * @return string
 */
function install_schema() {
    return <<<SQL
CREATE TABLE IF NOT EXISTS `#@_option` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `module` char(20) NOT NULL,
  `code` char(50) NOT NULL,
  `value` longtext,
  PRIMARY KEY (`id`),
  UNIQUE KEY `opt_idx` (`code`,`module`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#@_user` (
  `userid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(20) NOT NULL,
  `pass` char(32) NOT NULL,
  `mail` char(150) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `registered` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `authcode` char(36) NOT NULL,
  PRIMARY KEY (`userid`),
  UNIQUE KEY `name` (`name`),
  KEY `authcode` (`authcode`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#@_user_meta` (
  `metaid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(10) unsigned NOT NULL DEFAULT '0',
  `key` char(50) NOT NULL,
  `value` longtext NOT NULL,
  PRIMARY KEY (`metaid`),
  KEY `userid` (`userid`),
  KEY `key` (`key`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#@_model` (
  `modelid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` char(20) NOT NULL,
  `language` char(10) NOT NULL DEFAULT 'en',
  `code` char(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `path` char(255) NOT NULL,
  `list` varchar(50) NOT NULL,
  `page` varchar(50) NOT NULL,
  `fields` longtext NOT NULL,
  `state` enum('enabled','disabled') NOT NULL DEFAULT 'enabled',
  PRIMARY KEY (`modelid`),
  UNIQUE KEY `code` (`language`,`code`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#@_post` (
  `postid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `listid` int(10) unsigned NOT NULL DEFAULT '0',
  `userid` int(10) unsigned NOT NULL DEFAULT '0',
  `type` char(20) NOT NULL,
  `model` char(75) NOT NULL,
  `author` varchar(255) NOT NULL,
  `path` char(255) NOT NULL,
  `title` char(255) NOT NULL,
  `content` longtext NOT NULL,
  `views` int(10) unsigned NOT NULL DEFAULT '0',
  `digg` int(10) unsigned NOT NULL DEFAULT '0',
  `approved` tinytext NOT NULL,
  `comments` enum('Yes','No') NOT NULL DEFAULT 'No',
  `datetime` int(10) unsigned NOT NULL DEFAULT '0',
  `edittime` int(10) unsigned NOT NULL DEFAULT '0',
  `template` varchar(50) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`postid`),
  UNIQUE KEY `path` (`path`),
  KEY `model` (`listid`,`model`),
  KEY `title` (`title`),
  KEY `author` (`author`),
  KEY `userid` (`userid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#@_post_meta` (
  `metaid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `postid` bigint(20) unsigned NOT NULL,
  `key` char(50) NOT NULL,
  `value` longtext,
  PRIMARY KEY (`metaid`),
  KEY `postid` (`postid`),
  KEY `key` (`key`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#@_comments` (
  `cmtid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `postid` bigint(20) unsigned NOT NULL,
  `author` varchar(255) NOT NULL,
  `mail` varchar(100) NOT NULL,
  `url` varchar(200) NOT NULL,
  `ip` bigint(20) unsigned NOT NULL,
  `date` int(10) unsigned NOT NULL,
  `content` text NOT NULL,
  `approved` tinytext NOT NULL,
  `agent` varchar(255) NOT NULL,
  `parent` bigint(20) unsigned NOT NULL,
  `userid` int(10) unsigned NOT NULL,
  PRIMARY KEY (`cmtid`),
  KEY `postid` (`postid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#@_publish` (
  `pubid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `total` int(10) unsigned NOT NULL DEFAULT '0',
  `complete` int(10) unsigned NOT NULL DEFAULT '0',
  `func` varchar(50) NOT NULL,
  `args` longtext NOT NULL,
  `elapsetime` decimal(15,4) unsigned NOT NULL DEFAULT '0.0000',
  `state` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`pubid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#@_term` (
  `termid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(35) NOT NULL,
  PRIMARY KEY (`termid`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#@_term_relation` (
  `objectid` bigint(20) unsigned NOT NULL,
  `taxonomyid` int(10) unsigned NOT NULL,
  `order` smallint(5) unsigned NOT NULL DEFAULT '0',
  KEY `taxonomyid` (`taxonomyid`),
  KEY `objectid` (`objectid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#@_term_taxonomy` (
  `taxonomyid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `termid` bigint(20) unsigned NOT NULL,
  `type` char(20) NOT NULL DEFAULT 'category',
  `parent` int(10) unsigned NOT NULL DEFAULT '0',
  `count` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`taxonomyid`),
  KEY `type` (`type`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#@_term_taxonomy_meta` (
  `metaid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `taxonomyid` int(10) unsigned NOT NULL,
  `key` char(50) NOT NULL,
  `value` longtext,
  PRIMARY KEY (`metaid`),
  KEY `taxonomyid` (`taxonomyid`),
  KEY `key` (`key`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
SQL;
}






