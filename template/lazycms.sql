-- phpMyAdmin SQL Dump
-- version 2.10.3-rc1
-- http://www.phpmyadmin.net
-- 
-- 主机: localhost
-- 生成日期: 2008 年 04 月 07 日 16:35
-- 服务器版本: 5.0.45
-- PHP 版本: 5.2.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- 
-- 数据库: `lazycms`
-- 

-- --------------------------------------------------------

-- 
-- 表的结构 `lazy_addarticle`
-- 

CREATE TABLE IF NOT EXISTS `lazy_addarticle` (
  `aid` int(11) NOT NULL,
  `content` mediumtext,
  PRIMARY KEY  (`aid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- 导出表中的数据 `lazy_addarticle`
-- 

INSERT INTO `lazy_addarticle` (`aid`, `content`) VALUES 
(1, '<p>常见问题常见问题常见问题常见问题常见问题常见问题常见问题常见问题常见问题常见问题常见问题常见问题常见问题常见问题常见问题常见问题</p>\r\n<p>&nbsp;</p>\r\n<p>常见问题常见问题常见问题常见问题常见问题常见问题</p>\r\n<p>常见问题常见问题常见问题常见问题常见问题常见问题常见问题常见问题常见问题常见问题常见问题常见问题常见问题常见问题常见问题常见问题</p>\r\n<p>&nbsp;</p>\r\n<p>常见问题常见问题常见问题常见问题常见问题常见问题</p>\r\n<p>常见问题常见问题常见问题常见问题常见问题常见问题常见问题常见问题常见问题常见问题常见问题常见问题常见问题常见问题常见问题常见问题</p>\r\n<p>&nbsp;</p>\r\n<p>常见问题常见问题常见问题常见问题常见问题常见问题</p>\r\n<p>常见问题常见问题常见问题常见问题常见问题常见问题常见问题常见问题常见问题常见问题常见问题常见问题常见问题常见问题常见问题常见问题</p>\r\n<p>&nbsp;</p>\r\n<p>常见问题常见问题常见问题常见问题常见问题常见问题</p>'),
(3, '<p>常见问题二常见问题二常见问题二常见问题二常见问题二常见问题二常见问题二常见问题二常见问题二常见问题二</p>\r\n<p>常见问题二常见问题二常见问题二常见问题二常见问题二常见问题二常见问题二常见问题二常见问题二常见问题二</p>\r\n<p>常见问题二常见问题二常见问题二常见问题二常见问题二常见问题二常见问题二常见问题二常见问题二常见问题二</p>\r\n<p>常见问题二常见问题二常见问题二常见问题二常见问题二常见问题二常见问题二常见问题二常见问题二常见问题二</p>\r\n<p>常见问题二常见问题二常见问题二常见问题二常见问题二常见问题二常见问题二常见问题二常见问题二常见问题二</p>\r\n<p>常见问题二常见问题二常见问题二常见问题二常见问题二常见问题二常见问题二常见问题二常见问题二常见问题二</p>\r\n<p>常见问题二常见问题二常见问题二常见问题二常见问题二常见问题二常见问题二常见问题二常见问题二常见问题二</p>\r\n<p>常见问题二常见问题二常见问题二常见问题二常见问题二常见问题二常见问题二常见问题二常见问题二常见问题二</p>\r\n<p>常见问题二常见问题二常见问题二常见问题二常见问题二常见问题二常见问题二常见问题二常见问题二常见问题二</p>'),
(9, '<p>初次安装200元</p>');

-- --------------------------------------------------------

-- 
-- 表的结构 `lazy_adddemo`
-- 

CREATE TABLE IF NOT EXISTS `lazy_adddemo` (
  `aid` int(11) NOT NULL,
  `author` varchar(50) default NULL,
  `systemver` varchar(255) default NULL,
  `url` varchar(255) default NULL,
  `content` mediumtext,
  PRIMARY KEY  (`aid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- 导出表中的数据 `lazy_adddemo`
-- 

INSERT INTO `lazy_adddemo` (`aid`, `author`, `systemver`, `url`, `content`) VALUES 
(11, 'Lukin', '1.1,1.0', 'http://www.lazycms.net', '<p><img alt="" src="/up_files/22.jpg" /></p>'),
(12, 'Lukin', '1.0', 'http://www.lazycms.net', '<p><img alt="" src="/up_files/50cb51.gif" /></p>');

-- --------------------------------------------------------

-- 
-- 表的结构 `lazy_adddownload`
-- 

CREATE TABLE IF NOT EXISTS `lazy_adddownload` (
  `aid` int(11) NOT NULL,
  `downurl` varchar(255) default NULL,
  PRIMARY KEY  (`aid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- 导出表中的数据 `lazy_adddownload`
-- 

INSERT INTO `lazy_adddownload` (`aid`, `downurl`) VALUES 
(7, 'up_files/DSC00261.jpg'),
(8, 'up_files/12.jpg');

-- --------------------------------------------------------

-- 
-- 表的结构 `lazy_addtemplate`
-- 

CREATE TABLE IF NOT EXISTS `lazy_addtemplate` (
  `aid` int(11) NOT NULL,
  `author` varchar(50) default NULL,
  `systemver` varchar(255) default NULL,
  `url` varchar(255) default NULL,
  `downurl` varchar(255) default NULL,
  `content` mediumtext,
  PRIMARY KEY  (`aid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- 导出表中的数据 `lazy_addtemplate`
-- 

INSERT INTO `lazy_addtemplate` (`aid`, `author`, `systemver`, `url`, `downurl`, `content`) VALUES 
(6, 'Lukin', '1.1', 'http://www.lazycms.net', 'up_files/DSC00261.jpg', '<p><img alt="" src="/up_files/DSC00261.jpg" /></p>');

-- --------------------------------------------------------

-- 
-- 表的结构 `lazy_admin`
-- 

CREATE TABLE IF NOT EXISTS `lazy_admin` (
  `adminid` int(11) NOT NULL auto_increment,
  `adminname` varchar(30) NOT NULL,
  `adminpass` varchar(32) NOT NULL,
  `adminkey` varchar(6) default NULL,
  `adminlevel` text,
  `adminlanguage` varchar(30) NOT NULL,
  `admineditor` varchar(30) NOT NULL,
  `admindate` int(11) default '0',
  `diymenu` text,
  PRIMARY KEY  (`adminid`),
  KEY `adminname` (`adminname`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- 
-- 导出表中的数据 `lazy_admin`
-- 

INSERT INTO `lazy_admin` (`adminid`, `adminname`, `adminpass`, `adminkey`, `adminlevel`, `adminlanguage`, `admineditor`, `admindate`, `diymenu`) VALUES 
(1, 'Lukin', 'fdda558e85f3d2319f1c9c03b992e33d', '05b0f2', 'admin', 'zh-cn', 'fckeditor', 1207402970, NULL),
(2, 'admin', '167106eedfc2750dac3dc38ce7884d37', '067174', 'admin', 'zh-cn', 'fckeditor', 1207482967, '');

-- --------------------------------------------------------

-- 
-- 表的结构 `lazy_archives`
-- 

CREATE TABLE IF NOT EXISTS `lazy_archives` (
  `id` int(11) NOT NULL auto_increment,
  `sortid` int(11) default '0',
  `order` int(11) default '0',
  `title` varchar(255) NOT NULL,
  `show` tinyint(1) default '0',
  `commend` tinyint(1) default '0',
  `top` tinyint(1) default '0',
  `img` varchar(255) default NULL,
  `path` varchar(255) NOT NULL,
  `date` int(11) NOT NULL,
  `hits` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `path` (`path`),
  KEY `sortid` (`sortid`),
  KEY `show` (`show`),
  KEY `commend` (`commend`),
  KEY `top` (`top`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

-- 
-- 导出表中的数据 `lazy_archives`
-- 

INSERT INTO `lazy_archives` (`id`, `sortid`, `order`, `title`, `show`, `commend`, `top`, `img`, `path`, `date`, `hits`) VALUES 
(1, 2, 1, '常见问题常见问题常见问题常见问题常见问题常见问题', 1, 0, 0, '', 'ChangJianWenTiChangJianWenTiChangJianWenTiChangJianWenTiChangJianWenTiChangJianWenTi.htm', 1207324800, 11),
(5, 3, 5, '美女模板美女模板', 1, 0, 0, 'up_files/6979239.jpg', 'MeiNvMoBanMeiNvMoBan.htm', 1207411200, 4),
(3, 2, 3, '常见问题二常见问题二常见问题二常见问题二常见问题二常见问题二', 1, 0, 0, 'up_files/6979239.jpg', 'ChangJianWenTiErChangJianWenTiErChangJianWenTiErChangJianWenTiErChangJianWenTiErChangJianWenTiEr.htm', 1207411200, 26),
(4, 3, 4, '高级模板啊', 1, 0, 0, 'up_files/DSC00261.jpg', 'GaoJiMoBanA.htm', 1207411200, 5),
(6, 3, 6, '免费模板', 1, 0, 0, 'up_files/12.jpg', 'MianFeiMoBan.htm', 1207411200, 7),
(7, 4, 7, '下载LazyCMS 最新版本', 1, 0, 0, '', 'XiaZaiLazyCMS-ZuiXinBanBen.htm', 1207411200, 0),
(8, 4, 8, '下载 LazyCMS 1.1.0.0406', 1, 1, 0, '', 'XiaZai-LazyCMS-1-1-0-0406.htm', 1207411200, 5),
(9, 5, 9, '初次安装200元', 1, 0, 0, '', 'ChuCiAnZhuang200Yuan.htm', 1207411200, 14),
(11, 6, 10, 'LazyCMS 官方网站', 1, 0, 0, 'up_files/22.jpg', 'LazyCMS-GuanFangWangZhan.htm', 1207497600, 0),
(12, 6, 12, '问问去玩儿', 1, 0, 0, 'up_files/50cb51.gif', 'WenWenQuWanEr.htm', 1207584000, 0);

-- --------------------------------------------------------

-- 
-- 表的结构 `lazy_diymenu`
-- 

CREATE TABLE IF NOT EXISTS `lazy_diymenu` (
  `diymenuid` int(11) NOT NULL auto_increment,
  `diymenulang` varchar(10) NOT NULL,
  `diymenu` text,
  PRIMARY KEY  (`diymenuid`),
  KEY `diymenulang` (`diymenulang`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- 
-- 导出表中的数据 `lazy_diymenu`
-- 

INSERT INTO `lazy_diymenu` (`diymenuid`, `diymenulang`, `diymenu`) VALUES 
(1, 'zh-cn', '文档管理|url(''Archives'')\r\n单页面|url(''Onepage'')\r\n自定义菜单|url(''System'',''DiyMenu'')\r\n技术支持|javascript:;\r\n    官方网站|http://www.lazycms.net/\r\n    论坛支持|http://forums.lazycms.net/');

-- --------------------------------------------------------

-- 
-- 表的结构 `lazy_fields`
-- 

CREATE TABLE IF NOT EXISTS `lazy_fields` (
  `fieldid` int(11) NOT NULL auto_increment,
  `modelid` int(11) NOT NULL,
  `fieldorder` int(11) default NULL,
  `fieldname` varchar(50) default NULL,
  `fieldename` varchar(50) default NULL,
  `fieldtype` varchar(20) default NULL,
  `fieldlength` varchar(255) default NULL,
  `fieldefault` varchar(255) default NULL,
  `fieldindex` enum('0','1') default '0',
  `inputtype` varchar(20) default NULL,
  `fieldvalue` varchar(255) default NULL,
  PRIMARY KEY  (`fieldid`),
  KEY `modelid` (`modelid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=23 ;

-- 
-- 导出表中的数据 `lazy_fields`
-- 

INSERT INTO `lazy_fields` (`fieldid`, `modelid`, `fieldorder`, `fieldname`, `fieldename`, `fieldtype`, `fieldlength`, `fieldefault`, `fieldindex`, `inputtype`, `fieldvalue`) VALUES 
(8, 3, 8, '设计制作', 'author', 'varchar', '50', '', '0', 'input', ''),
(3, 1, 3, '内容', 'content', 'mediumtext', '', '', '0', 'editor', ''),
(13, 4, 13, '下载地址', 'downurl', 'varchar', '255', '', '0', 'upfile', ''),
(9, 3, 9, '系统版本', 'systemver', 'varchar', '255', '', '0', 'select', 'LazyCMS 1.1:1.1\r\nLazyCMS 1.0:1.0'),
(10, 3, 10, '演示地址', 'url', 'varchar', '255', '', '0', 'input', ''),
(11, 3, 11, '下载地址', 'downurl', 'varchar', '255', '', '0', 'upfile', ''),
(12, 3, 12, '模板介绍', 'content', 'mediumtext', '', '', '0', 'editor', ''),
(21, 6, 21, '演示地址', 'url', 'varchar', '255', '', '0', 'input', ''),
(20, 6, 20, '系统版本', 'systemver', 'varchar', '255', '', '0', 'checkbox', 'LazyCMS 1.1:1.1\r\nLazyCMS 1.0:1.0'),
(19, 6, 14, '设计制作', 'author', 'varchar', '50', '', '0', 'input', ''),
(22, 6, 22, '网站介绍', 'content', 'mediumtext', '', '', '0', 'editor', '');

-- --------------------------------------------------------

-- 
-- 表的结构 `lazy_log`
-- 

CREATE TABLE IF NOT EXISTS `lazy_log` (
  `logid` int(11) NOT NULL auto_increment,
  `adminname` varchar(30) NOT NULL,
  `ip` varchar(20) NOT NULL,
  `lognum` int(11) default '0',
  `logdate` int(11) default '0',
  PRIMARY KEY  (`logid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;

-- 
-- 导出表中的数据 `lazy_log`
-- 

INSERT INTO `lazy_log` (`logid`, `adminname`, `ip`, `lognum`, `logdate`) VALUES 
(1, 'Lukin', '127.0.0.1', 1, 1207402984),
(2, 'Lukin', '127.0.0.1', 1, 1207412765),
(3, 'Lukin', '127.0.0.1', 1, 1207424414),
(4, 'lukin', '221.228.40.236', 1, 1207482518),
(5, 'Lukin', '127.0.0.1', 3, 1207482564),
(6, 'Lukin', '58.37.183.200', 1, 1207482954),
(7, 'lukin', '221.228.40.236', 1, 1207483021),
(8, 'Lukin', '58.37.183.200', 3, 1207483081),
(9, 'admin', '58.37.183.200', 1, 1207483090),
(10, 'admin', '127.0.0.1', 1, 1207484044),
(11, 'admin', '127.0.0.1', 1, 1207484184),
(12, 'admin', '127.0.0.1', 3, 1207489478),
(13, 'admin', '127.0.0.1', 1, 1207489514),
(14, 'admin', '127.0.0.1', 1, 1207489709),
(15, 'admin', '127.0.0.1', 3, 1207490273),
(16, 'admin', '127.0.0.1', 1, 1207574444);

-- --------------------------------------------------------

-- 
-- 表的结构 `lazy_model`
-- 

CREATE TABLE IF NOT EXISTS `lazy_model` (
  `modelid` int(11) NOT NULL auto_increment,
  `modelname` varchar(50) NOT NULL,
  `modelename` varchar(50) NOT NULL,
  `maintable` varchar(50) NOT NULL,
  `addtable` varchar(50) NOT NULL,
  `modelstate` enum('0','1') default '0',
  PRIMARY KEY  (`modelid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

-- 
-- 导出表中的数据 `lazy_model`
-- 

INSERT INTO `lazy_model` (`modelid`, `modelname`, `modelename`, `maintable`, `addtable`, `modelstate`) VALUES 
(1, '文章模型', 'article', 'lazy_archives', 'lazy_addarticle', '0'),
(4, '程序下载', 'download', 'lazy_archives', 'lazy_adddownload', '0'),
(3, '模板下载', 'template', 'lazy_archives', 'lazy_addtemplate', '0'),
(6, '演示网站', 'demo', 'lazy_archives', 'lazy_adddemo', '0');

-- --------------------------------------------------------

-- 
-- 表的结构 `lazy_onepage`
-- 

CREATE TABLE IF NOT EXISTS `lazy_onepage` (
  `oneid` int(11) NOT NULL auto_increment,
  `oneorder` int(11) default '0',
  `onetitle` varchar(100) NOT NULL,
  `onepath` varchar(100) default NULL,
  `onename` varchar(50) default NULL,
  `onekeyword` varchar(50) default NULL,
  `onedescription` varchar(250) default NULL,
  `onecontent` text,
  `onetemplate1` varchar(50) default NULL,
  `onetemplate2` varchar(50) default NULL,
  `ishome` enum('0','1') default '0',
  PRIMARY KEY  (`oneid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

-- 
-- 导出表中的数据 `lazy_onepage`
-- 

INSERT INTO `lazy_onepage` (`oneid`, `oneorder`, `onetitle`, `onepath`, `onename`, `onekeyword`, `onedescription`, `onecontent`, `onetemplate1`, `onetemplate2`, `ishome`) VALUES 
(1, 1, '力争做国内最优秀的内容管理系统(CMS)', 'index.htm', '首页', 'PHP,Lazy,CMS,内容管理系统', 'LazyCMS 是一款小巧、高效、人性化的开源内容管理系统。可装配、可自定义模型，使得进一步扩展功能和二次开发更加得心应手。运行环境：PHP 5+、MySQL 4.1+', '<p><strong>免费！</strong>可以用在商业用途网站。包括个人及企业网站，而无需支付使用费用，仅需保留LazyCMS支持信息链接即可。详细协议请阅读许可协议文档。</p>', 'template/home.html', 'template/inside/onepage/default.html', '1'),
(2, 2, '关于LazyCMS', 'about.html', '关于LazyCMS', 'Lazy,CMS', '关于LazyCMS', '<p>关于LazyCMS</p>', 'template/default.html', 'template/inside/onepage/default.html', '0'),
(3, 3, '联系方式', 'contact.html', '联系方式', '', '联系方式', '<p>联系方式</p>', 'template/default.html', 'template/inside/onepage/default.html', '0'),
(4, 4, '付款方式', 'payment.html', '付款方式', '', '付款方式', '<p>付款方式</p>', 'template/default.html', 'template/inside/onepage/default.html', '0'),
(5, 5, '许可协议', 'license', '许可协议', 'Lazy,CMS', '\r\n    本软件是自由软件，遵循 Apache License 2.0 许可协议 <http://www.apache.org/licenses/LICENSE-2.0>\r\n    本软件的版权归 LazyCMS官方 所有，且受《中华人民共和国计算机软件保护条例》等知识产权法律及国际条约与惯例的保护。\r\n    本协议适用且仅适用于 LazyCMS 1.x 版本，LazyCMS官方 拥有对本协议的最终解释权。\r\n    无论个人或组织、盈利与否、用途如何（包括以学习和研究为目的', '<div class="license">\r\n<ul>\r\n    <li>本软件是自由软件，遵循 Apache License 2.0 许可协议 &lt;<a target="_blank" href="http://www.apache.org/licenses/LICENSE-2.0">http://www.apache.org/licenses/LICENSE-2.0</a>&gt;</li>\r\n    <li>本软件的版权归 LazyCMS官方 所有，且受《中华人民共和国计算机软件保护条例》等知识产权法律及国际条约与惯例的保护。</li>\r\n    <li>本协议适用且仅适用于 LazyCMS 1.x 版本，LazyCMS官方 拥有对本协议的最终解释权。</li>\r\n    <li><u>无论个人或组织、盈利与否、用途如何（包括以学习和研究为目的），均需仔细阅读本协议，在理解、同意、并遵守本协议的全部条款后，方可开始使用本软件。</u></li>\r\n</ul>\r\n<blockquote>\r\n<h4><strong>I.协议许可和限制</strong></h4>\r\n<ol>\r\n    <li><u>未经作者书面许可，不得衍生出私有软件。</u></li>\r\n    <li>使用者所生成的网站，首页要包含软件的版权信息；不得对后台版权进行修改。</li>\r\n    <li><u>您拥有使用本软件构建的网站全部内容所有权，并独立承担与这些内容的相关法律义务。</u></li>\r\n    <li>您可以对源码进行修改及优化，但要保证源码的完整性；修改后的代码版权归开发者所有，未经开发者许可，不得私自发布。</li>\r\n    <li><u>您将本软件应用在商业用途时，需遵守以下几条：</u>\r\n    <ol>\r\n        <li><u>使用本软件建设网站时，无需支付使用费用，但需保留LazyCMS支持链接信息。</u></li>\r\n        <li>本源码可以用在商业用途，但不可以更名销售，若有OEM需求，请和作者联系。</li>\r\n        <li>若网站性质等因素所限，不适合保留支持信息，请与作者联系取得书面授权。</li>\r\n    </ol>\r\n    </li>\r\n</ol>\r\n<h4><strong>II.有限担保和免责声明</strong></h4>\r\n<ol>\r\n    <li>本软件及所附带的文件是作为不提供任何明确的或隐含的赔偿或担保的形式提供的。</li>\r\n    <li>用户出于自愿而使用本软件，您必须了解使用本软件的风险，在尚未购买产品技术服务之前，我们不承诺提供任何形式的技术支持、使用担保，也不承担任何因使用本软件而产生问题的相关责任。</li>\r\n    <li>LazyCMS官方不对使用本软件构建的网站中的文章或信息承担责任。</li>\r\n</ol>\r\n<p>本协议保留作者的版权信息在许可协议文本之内，不得擅自修改其信息。</p>\r\n<p>2007-11，第1.0版 (保留对此许可协议的更新及解释权力)<br />\r\n协议著作权所有 &copy; LazyCMS.net&nbsp;&nbsp;&nbsp; &nbsp; &nbsp; 软件版权所有 &copy; LazyCMS.net</p>\r\n<p>作者：Lukin&nbsp;&nbsp;&nbsp; 邮箱：<a href="mailto:mylukin@gmail.com">mylukin@gmail.com</a></p>\r\n</blockquote></div>', 'template/default.html', 'template/inside/onepage/default.html', '0'),
(6, 6, '最近更新', 'news', '最近更新', '', '最近更新', '<p>最近更新</p>', 'template/default.html', 'template/inside/onepage/news.html', '0');

-- --------------------------------------------------------

-- 
-- 表的结构 `lazy_sort`
-- 

CREATE TABLE IF NOT EXISTS `lazy_sort` (
  `sortid` int(11) NOT NULL auto_increment,
  `sortid1` int(11) default '0',
  `modelid` int(11) default '0',
  `sortorder` int(11) NOT NULL,
  `sortname` varchar(50) NOT NULL,
  `sortpath` varchar(255) NOT NULL,
  `keywords` varchar(255) default NULL,
  `description` varchar(255) default NULL,
  `sortopen` enum('0','1') default '0',
  `sorttemplate1` varchar(255) default NULL,
  `sorttemplate2` varchar(255) default NULL,
  `pagetemplate1` varchar(255) default NULL,
  `pagetemplate2` varchar(255) default NULL,
  PRIMARY KEY  (`sortid`),
  UNIQUE KEY `sortpath` (`sortpath`),
  KEY `sortid1` (`sortid1`),
  KEY `sortname` (`sortname`),
  KEY `modelid` (`modelid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

-- 
-- 导出表中的数据 `lazy_sort`
-- 

INSERT INTO `lazy_sort` (`sortid`, `sortid1`, `modelid`, `sortorder`, `sortname`, `sortpath`, `keywords`, `description`, `sortopen`, `sorttemplate1`, `sorttemplate2`, `pagetemplate1`, `pagetemplate2`) VALUES 
(1, 0, 1, 1, '使用帮助', 'help', '', '', '0', 'template/default.html', 'template/inside/article[list]/default.html', 'template/default.html', 'template/inside/article[page]/default.html'),
(2, 0, 1, 2, '常见问题', 'faq', '', '', '0', 'template/default.html', 'template/inside/article[list]/default.html', 'template/default.html', 'template/inside/article[page]/default.html'),
(3, 0, 3, 3, '模板下载', 'download/template', '', '', '0', 'template/default.html', 'template/inside/template[list]/default.html', 'template/default.html', 'template/inside/template[page]/default.html'),
(4, 0, 4, 4, '程序下载', 'download', '', '', '0', 'template/default.html', 'template/inside/download[list]/default.html', 'template/default.html', 'template/inside/download[page]/default.html'),
(5, 0, 1, 5, '服务项目', 'service', '', '', '0', 'template/default.html', 'template/inside/article[list]/default.html', 'template/default.html', 'template/inside/article[page]/default.html'),
(6, 0, 6, 6, '演示网站', 'demo', '', '', '0', 'template/default.html', 'template/inside/demo[list]/default.html', 'template/default.html', 'template/inside/demo[page]/default.html');

-- --------------------------------------------------------

-- 
-- 表的结构 `lazy_system`
-- 

CREATE TABLE IF NOT EXISTS `lazy_system` (
  `systemname` varchar(10) NOT NULL default 'LazyCMS',
  `systemver` varchar(10) NOT NULL,
  `dbversion` varchar(10) NOT NULL,
  `sitename` varchar(50) NOT NULL,
  `sitemail` varchar(100) NOT NULL,
  `sitekeywords` text,
  `lockip` text,
  `modules` text,
  `instdate` int(11) default '0',
  PRIMARY KEY  (`systemname`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- 导出表中的数据 `lazy_system`
-- 

INSERT INTO `lazy_system` (`systemname`, `systemver`, `dbversion`, `sitename`, `sitemail`, `sitekeywords`, `lockip`, `modules`, `instdate`) VALUES 
('LazyCMS', '1.1.0.0406', '1.1.0', 'LazyCMS内容管理系统', 'mylukin@gmail.com', 'PHP,Lazy,CMS,内容管理系统', '', 'archives,onepage', 1207402970);
