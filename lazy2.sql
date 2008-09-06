-- phpMyAdmin SQL Dump
-- version 2.10.3-rc1
-- http://www.phpmyadmin.net
-- 
-- 主机: localhost
-- 生成日期: 2008 年 08 月 15 日 14:30
-- 服务器版本: 5.0.45
-- PHP 版本: 5.2.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- 
-- 数据库: `lazy2`
-- 

-- --------------------------------------------------------

-- 
-- 表的结构 `lazy_content_model`
-- 

CREATE TABLE IF NOT EXISTS `lazy_content_model` (
  `modelid` int(11) NOT NULL auto_increment,
  `modelname` varchar(50) NOT NULL COMMENT '模型名称',
  `modelename` varchar(50) NOT NULL COMMENT '模型英文标识',
  `modelpath` varchar(100) NOT NULL COMMENT '生成文件的命名规则',
  `setkeyword` varchar(50) COMMENT '自动获取关键词',
  `description` varchar(50) COMMENT '自动获取简述',
  `sortemplate` varchar(50) COMMENT '列表页模板',
  `pagetemplate` varchar(50) COMMENT '内容页模板',
  `modelfields` text COMMENT '字段序列',
  `modelstate` tinyint(1) default '1' COMMENT '1:启用',
  PRIMARY KEY  (`modelid`),
  UNIQUE KEY `modelename` (`modelename`),
  KEY `modelstate` (`modelstate`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- 表的结构 `lazy_content_sort`
--

CREATE TABLE IF NOT EXISTS `lazy_content_sort` (
  `sortid` int(11) NOT NULL auto_increment,
  `sortname` varchar(100) NOT NULL COMMENT '分类名称',
  `sortpath` varchar(255) NOT NULL COMMENT '路径',
  `sortemplate` varchar(50) COMMENT '列表页模板',
  `pagetemplate` varchar(50) COMMENT '内容页模板',
  `parentid` int(11) default '0' COMMENT '父类id',
  PRIMARY KEY  (`sortid`),
  KEY `parentid` (`parentid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `lazy_content_sort_model`
--

CREATE TABLE IF NOT EXISTS `lazy_content_sort_model` (
  `joinid` int(11) NOT NULL auto_increment,
  `sortid` int(11) NOT NULL COMMENT '分类ID',
  `modelid` int(11) NOT NULL COMMENT '模型ID',
  PRIMARY KEY  (`joinid`),
  KEY `sortid` (`sortid`),
  KEY `modelid` (`modelid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- 表的结构 `lazy_keywords`
-- 

CREATE TABLE IF NOT EXISTS `lazy_keywords` (
  `keyid` int(11) NOT NULL auto_increment COMMENT '编号',
  `keyword` varchar(50) NOT NULL COMMENT '关键词',
  `keysum` int(11) default '0' COMMENT '使用次数',
  PRIMARY KEY  (`keyid`),
  UNIQUE KEY `keyword` (`keyword`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- 
-- 导出表中的数据 `lazy_system_group`
-- 

INSERT INTO `lazy_system_group` (`groupid`, `groupename`, `groupname`, `purview`, `system`) VALUES 
(1, 'super', '超级管理员', 'system/system,system/users,system/webftp,system/module,system/settings,content/onepage', 1),
(2, 'admin', '普通管理员', 'system/system,system/users,system/webftp,system/module,system/settings,content/onepage', 1),
(3, 'user', '注册用户', '', 1);

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
  KEY `groupid` (`groupid`),
  KEY `usermail` (`usermail`),
  KEY `isdel` (`isdel`),
  KEY `islock` (`islock`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- 
-- 导出表中的数据 `lazy_system_users`
-- 

INSERT INTO `lazy_system_users` (`userid`, `groupid`, `username`, `userpass`, `userkey`, `usermail`, `question`, `answer`, `language`, `editor`, `regdate`, `isdel`, `islock`) VALUES 
(1, 1, 'admin', 'e1f385d47f79d5ab1921fb85bcecedda', 'd1b148', 'mylukin@gmail.com', '888', '8888', 'zh-cn', 'fckeditor', 1215399176, 0, 0);
