-- phpMyAdmin SQL Dump
-- version 2.10.3-rc1
-- http://www.phpmyadmin.net
-- 
-- 主机: localhost
-- 生成日期: 2008 年 07 月 25 日 16:14
-- 服务器版本: 5.0.45
-- PHP 版本: 5.2.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- 
-- 数据库: `lazy2`
-- 

-- --------------------------------------------------------

-- 
-- 表的结构 `lazy_article_index`
-- 

CREATE TABLE IF NOT EXISTS `lazy_article_index` (
  `artid` int(11) NOT NULL auto_increment COMMENT '编号',
  `sortid` int(11) default NULL COMMENT '分类编号',
  `modelid` int(11) default NULL COMMENT '模型编号',
  `artorder` int(11) default '0' COMMENT '排序编号',
  `artpath` varchar(255) NOT NULL COMMENT '路径',
  `artdate` int(11) default NULL COMMENT '文章添加时间',
  `arthits` int(11) default '0' COMMENT '浏览量',
  `artdigg` int(11) default '0' COMMENT 'digg',
  `description` varchar(250) default NULL COMMENT '描述',
  `isdel` tinyint(1) default '0' COMMENT '是否删除',
  PRIMARY KEY  (`artid`),
  UNIQUE KEY `artpath` (`artpath`),
  KEY `isdel` (`isdel`),
  KEY `sortid` (`sortid`),
  KEY `modelid` (`modelid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- 导出表中的数据 `lazy_article_index`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `lazy_article_sort`
-- 

CREATE TABLE IF NOT EXISTS `lazy_article_sort` (
  `sortid` int(11) NOT NULL auto_increment COMMENT '编号',
  `modelid` int(11) default NULL COMMENT '模块id',
  `sortid1` int(11) default '0' COMMENT '所属id',
  `sortorder` int(11) default '0' COMMENT '排序编号',
  `sortname` varchar(50) NOT NULL COMMENT '分类名称',
  `sortpath` varchar(255) NOT NULL COMMENT '路径',
  `description` varchar(250) default NULL COMMENT '描述',
  `sortopen` tinyint(1) default '0' COMMENT '是否展开',
  `sorttemplate` varchar(255) NOT NULL COMMENT '列表模板',
  `pagetemplate` varchar(255) NOT NULL COMMENT '内页模板',
  `subdomain` varchar(255) default NULL COMMENT '绑定域名',
  PRIMARY KEY  (`sortid`),
  KEY `modelid` (`modelid`),
  KEY `sortid1` (`sortid1`),
  KEY `sortname` (`sortname`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- 导出表中的数据 `lazy_article_sort`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `lazy_keywords`
-- 

CREATE TABLE IF NOT EXISTS `lazy_keywords` (
  `keyid` int(11) NOT NULL auto_increment COMMENT '编号',
  `keyword` varchar(50) NOT NULL COMMENT '关键词',
  `keysum` int(11) default '0' COMMENT '使用次数',
  PRIMARY KEY  (`keyid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- 导出表中的数据 `lazy_keywords`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `lazy_keyword_join`
-- 

CREATE TABLE IF NOT EXISTS `lazy_keyword_join` (
  `kjid` int(11) NOT NULL auto_increment COMMENT '编号',
  `module` varchar(50) NOT NULL COMMENT '分类使用模块名称',
  `targetid` int(11) NOT NULL COMMENT '文档编号',
  `keyid` int(11) NOT NULL,
  PRIMARY KEY  (`kjid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- 导出表中的数据 `lazy_keyword_join`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `lazy_onepage`
-- 

CREATE TABLE IF NOT EXISTS `lazy_onepage` (
  `oneid` int(11) NOT NULL auto_increment COMMENT '编号',
  `oneid1` int(11) default '0' COMMENT '所属id',
  `oneorder` int(11) default '0' COMMENT '排序',
  `onetitle` varchar(100) NOT NULL COMMENT '标题',
  `onepath` varchar(255) NOT NULL COMMENT '路径',
  `onename` varchar(50) NOT NULL COMMENT '名称',
  `onecontent` text COMMENT '内容',
  `onetemplate` varchar(255) NOT NULL COMMENT '模板',
  `description` varchar(250) default NULL COMMENT '描述',
  PRIMARY KEY  (`oneid`),
  KEY `oneid1` (`oneid1`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- 导出表中的数据 `lazy_onepage`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `lazy_system_fields`
-- 

CREATE TABLE IF NOT EXISTS `lazy_system_fields` (
  `fieldid` int(11) NOT NULL auto_increment,
  `module` varchar(50) NOT NULL COMMENT '所属模块，如果是模型，就存放模型的ename',
  `fieldorder` int(11) default '0' COMMENT '字段排序',
  `fieldname` varchar(100) NOT NULL COMMENT '表单文字',
  `fieldename` varchar(50) NOT NULL COMMENT '字段名',
  `fieldtype` varchar(20) NOT NULL COMMENT '类型',
  `fieldlength` varchar(255) default '50' COMMENT '长度',
  `fieldefault` varchar(255) default NULL COMMENT '默认值',
  `fieldvalue` text COMMENT 'radio,checkbox,select 值',
  `widgetype` varchar(20) NOT NULL COMMENT '控件类型',
  PRIMARY KEY  (`fieldid`),
  KEY `fieldename` (`fieldename`),
  KEY `modulename` (`module`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- 导出表中的数据 `lazy_system_fields`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `lazy_system_group`
-- 

CREATE TABLE IF NOT EXISTS `lazy_system_group` (
  `groupid` int(11) NOT NULL auto_increment COMMENT '编号',
  `groupename` varchar(50) NOT NULL COMMENT '组英文标识',
  `groupname` varchar(50) NOT NULL COMMENT '组名称',
  `purview` text COMMENT '组权限',
  `system` tinyint(1) default '0' COMMENT '1:系统组，不可删除',
  PRIMARY KEY  (`groupid`),
  UNIQUE KEY `groupename` (`groupename`),
  KEY `system` (`system`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- 
-- 导出表中的数据 `lazy_system_group`
-- 

INSERT INTO `lazy_system_group` (`groupid`, `groupename`, `groupname`, `purview`, `system`) VALUES 
(1, 'super', '超级管理员', 'system/system,system/users,system/webftp,system/module,system/settings,article/onepage', 1),
(2, 'admin', '普通管理员', 'system/system,system/users,system/webftp,system/module,system/settings,article/onepage', 1),
(3, 'user', '注册用户', '', 1);

-- --------------------------------------------------------

-- 
-- 表的结构 `lazy_system_model`
-- 

CREATE TABLE IF NOT EXISTS `lazy_system_model` (
  `modelid` int(11) NOT NULL auto_increment,
  `modelname` varchar(50) NOT NULL COMMENT '模型名称',
  `modelename` varchar(50) NOT NULL COMMENT '模型英文标识',
  `maintable` varchar(50) NOT NULL COMMENT '主索引表',
  `addtable` varchar(50) NOT NULL COMMENT '附属表',
  `modelstate` tinyint(1) default '1' COMMENT '1:禁用',
  PRIMARY KEY  (`modelid`),
  KEY `modelename` (`modelename`),
  KEY `modelstate` (`modelstate`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- 导出表中的数据 `lazy_system_model`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `lazy_system_users`
-- 

CREATE TABLE IF NOT EXISTS `lazy_system_users` (
  `userid` int(11) NOT NULL auto_increment COMMENT '编号',
  `groupid` int(11) NOT NULL COMMENT '所属组',
  `username` varchar(30) NOT NULL COMMENT '用户名',
  `userpass` varchar(32) NOT NULL COMMENT '密码',
  `userkey` varchar(6) default NULL COMMENT '随机密码key',
  `usermail` varchar(100) NOT NULL COMMENT '邮箱',
  `question` varchar(50) default NULL COMMENT '问题',
  `answer` varchar(50) default NULL COMMENT '回答',
  `language` varchar(30) default NULL COMMENT '界面语言',
  `editor` varchar(30) default NULL COMMENT '编辑器',
  `regdate` int(11) default '0' COMMENT '添加时间',
  `isdel` tinyint(1) default '0' COMMENT '是否被删除，删除自动屏蔽',
  `islock` tinyint(1) default '0' COMMENT '锁定',
  PRIMARY KEY  (`userid`),
  UNIQUE KEY `username` (`username`),
  KEY `adminname` (`username`),
  KEY `groupid` (`groupid`),
  KEY `usermail` (`usermail`),
  KEY `isdel` (`isdel`),
  KEY `islock` (`islock`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- 
-- 导出表中的数据 `lazy_system_users`
-- 

INSERT INTO `lazy_system_users` (`userid`, `groupid`, `username`, `userpass`, `userkey`, `usermail`, `question`, `answer`, `language`, `editor`, `regdate`, `isdel`, `islock`) VALUES 
(1, 1, 'admin', 'd1b148e3ec54d5ff4f7a5e21baffb3af', '73526e', 'mylukin@gmail.com', '888', '8888', 'zh-cn', 'fckeditor', 1215399176, 0, 0);
