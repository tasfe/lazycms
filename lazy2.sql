-- phpMyAdmin SQL Dump
-- version 2.10.3-rc1
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2009 年 07 月 05 日 22:41
-- 服务器版本: 5.0.67
-- PHP 版本: 5.2.6

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
  `iskeyword` varchar(50) default NULL COMMENT '自动获取关键词',
  `description` varchar(50) default NULL COMMENT '自动获取简述',
  `sortemplate` varchar(50) default NULL COMMENT '列表页模板',
  `pagetemplate` varchar(50) default NULL COMMENT '内容页模板',
  `modelfields` text COMMENT '字段序列',
  `modeltype` varchar(20) default NULL COMMENT '模型类型',
  `modelstate` tinyint(1) default '1' COMMENT '1:启用',
  PRIMARY KEY  (`modelid`),
  UNIQUE KEY `modelename` (`modelename`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 导出表中的数据 `lazy_content_model`
--


-- --------------------------------------------------------

--
-- 表的结构 `lazy_content_sort`
--

CREATE TABLE IF NOT EXISTS `lazy_content_sort` (
  `sortid` int(11) NOT NULL auto_increment,
  `sortname` varchar(100) NOT NULL COMMENT '分类名称',
  `sortpath` varchar(255) NOT NULL COMMENT '路径',
  `sortemplate` varchar(50) default NULL COMMENT '列表页模板',
  `pagetemplate` varchar(50) default NULL COMMENT '内容页模板',
  `parentid` int(11) default '0' COMMENT '父类id',
  PRIMARY KEY  (`sortid`),
  KEY `parentid` (`parentid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 导出表中的数据 `lazy_content_sort`
--


-- --------------------------------------------------------

--
-- 表的结构 `lazy_content_sort_join`
--

CREATE TABLE IF NOT EXISTS `lazy_content_sort_join` (
  `joinid` int(11) NOT NULL auto_increment,
  `sortid` int(11) NOT NULL COMMENT '分类ID',
  `modelid` int(11) NOT NULL COMMENT '模型ID',
  PRIMARY KEY  (`joinid`),
  KEY `sortid` (`sortid`),
  KEY `modelid` (`modelid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 导出表中的数据 `lazy_content_sort_join`
--


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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 导出表中的数据 `lazy_keywords`
--


-- --------------------------------------------------------

--
-- 表的结构 `lazy_system_admin`
--

CREATE TABLE IF NOT EXISTS `lazy_system_admin` (
  `adminid` int(11) NOT NULL auto_increment,
  `adminname` varchar(50) NOT NULL,
  `adminpass` varchar(50) NOT NULL,
  `adminkey` varchar(6) default NULL,
  `adminmail` varchar(150) NOT NULL,
  `purview` text,
  `language` varchar(20) NOT NULL,
  `islocked` tinyint(1) default '0',
  PRIMARY KEY  (`adminid`),
  UNIQUE KEY `adminname` (`adminname`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- 导出表中的数据 `lazy_system_admin`
--

INSERT INTO `lazy_system_admin` (`adminid`, `adminname`, `adminpass`, `adminkey`, `adminmail`, `purview`, `language`, `islocked`) VALUES
(1, 'admin', 'b62af8f00d5cd56ae5030bf775170010', '7fef61', 'mylukin@gmail.com', 'system::admins,system::modules,system::settings,system::sysinfo,content::label,content::create,content::onepage,content::article,content::sort,content::model', 'zh-cn', 0);

-- --------------------------------------------------------

--
-- 表的结构 `lazy_system_create`
--

CREATE TABLE IF NOT EXISTS `lazy_system_create` (
  `id` varchar(32) NOT NULL COMMENT '唯一ID',
  `total` int(11) NOT NULL default '0' COMMENT '总数',
  `create` int(11) NOT NULL default '0' COMMENT '已生成数',
  `usetime` float NOT NULL COMMENT '已用时间',
  `state` tinyint(1) NOT NULL default '0' COMMENT '状态',
  `data` text COMMENT '缓存数据',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 导出表中的数据 `lazy_system_create`
--


-- --------------------------------------------------------

--
-- 表的结构 `lazy_system_create_logs`
--

CREATE TABLE IF NOT EXISTS `lazy_system_create_logs` (
  `createid` varchar(32) NOT NULL,
  `model` varchar(200) default NULL,
  `dataid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`dataid`),
  UNIQUE KEY `m_d_c_idx` (`model`,`dataid`,`createid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 导出表中的数据 `lazy_system_create_logs`
--
