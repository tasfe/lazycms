SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

INSERT INTO `lazy_admin` (`adminid`, `adminname`, `adminpass`, `adminkey`, `adminlevel`, `adminlanguage`, `admineditor`, `admindate`, `diymenu`) VALUES
(2, 'admin', '7659c9dc5dfb182c9a2908c5a4a083ed', '167106', 'admin', 'zh-cn', 'fckeditor', 1207482967, '');


INSERT INTO `lazy_diymenu` (`diymenuid`, `diymenulang`, `diymenu`) VALUES
(1, 'zh-cn', '文档管理|url(''Archives'')\r\n单页面|url(''Onepage'')\r\n自定义菜单|url(''System'',''DiyMenu'')\r\n技术支持|javascript:;\r\n    官方网站|http://www.lazycms.net/\r\n    论坛支持|http://forums.lazycms.net/');


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


INSERT INTO `lazy_model` (`modelid`, `modelname`, `modelename`, `maintable`, `addtable`, `modelstate`) VALUES
(1, '文章模型', 'article', 'lazy_archives', 'lazy_addarticle', '0'),
(4, '程序下载', 'download', 'lazy_archives', 'lazy_adddownload', '0'),
(3, '模板下载', 'template', 'lazy_archives', 'lazy_addtemplate', '0'),
(6, '演示网站', 'demo', 'lazy_archives', 'lazy_adddemo', '0');

INSERT INTO `lazy_onepage` (`oneid`, `oneorder`, `onetitle`, `onepath`, `onename`, `onekeyword`, `onedescription`, `onecontent`, `onetemplate1`, `onetemplate2`, `ishome`) VALUES
(1, 1, '力争做国内最优秀的内容管理系统(CMS)', 'index.htm', '首页', 'PHP,Lazy,CMS,内容管理系统', 'LazyCMS 是一款小巧、高效、人性化的开源内容管理系统。可装配、可自定义模型，使得进一步扩展功能和二次开发更加得心应手。运行环境：PHP 5+、MySQL 4.1+', '<p><strong>免费！</strong>可以用在商业用途网站。包括个人及企业网站，而无需支付使用费用，仅需保留LazyCMS支持信息链接即可。详细协议请阅读许可协议文档。</p>', 'template/home.html', 'template/inside/onepage/default.html', '1'),
(2, 2, '关于LazyCMS', 'about.html', '关于LazyCMS', 'Lazy,CMS', '关于LazyCMS', '<p>关于LazyCMS</p>', 'template/default.html', 'template/inside/onepage/default.html', '0'),
(3, 3, '联系方式', 'contact.html', '联系方式', '', '联系方式', '<p>联系方式</p>', 'template/default.html', 'template/inside/onepage/default.html', '0'),
(4, 4, '付款方式', 'payment.html', '付款方式', '', '付款方式', '<p>付款方式</p>', 'template/default.html', 'template/inside/onepage/default.html', '0'),
(5, 5, '许可协议', 'license', '许可协议', 'Lazy,CMS', '\r\n    本软件是自由软件，遵循 Apache License 2.0 许可协议 <http://www.apache.org/licenses/LICENSE-2.0>\r\n    本软件的版权归 LazyCMS官方 所有，且受《中华人民共和国计算机软件保护条例》等知识产权法律及国际条约与惯例的保护。\r\n    本协议适用且仅适用于 LazyCMS 1.x 版本，LazyCMS官方 拥有对本协议的最终解释权。\r\n    无论个人或组织、盈利与否、用途如何（包括以学习和研究为目的', '<div class="license">\r\n<ul>\r\n    <li>本软件是自由软件，遵循 Apache License 2.0 许可协议 &lt;<a target="_blank" href="http://www.apache.org/licenses/LICENSE-2.0">http://www.apache.org/licenses/LICENSE-2.0</a>&gt;</li>\r\n    <li>本软件的版权归 LazyCMS官方 所有，且受《中华人民共和国计算机软件保护条例》等知识产权法律及国际条约与惯例的保护。</li>\r\n    <li>本协议适用且仅适用于 LazyCMS 1.x 版本，LazyCMS官方 拥有对本协议的最终解释权。</li>\r\n    <li><u>无论个人或组织、盈利与否、用途如何（包括以学习和研究为目的），均需仔细阅读本协议，在理解、同意、并遵守本协议的全部条款后，方可开始使用本软件。</u></li>\r\n</ul>\r\n<blockquote>\r\n<h4><strong>I.协议许可和限制</strong></h4>\r\n<ol>\r\n    <li><u>未经作者书面许可，不得衍生出私有软件。</u></li>\r\n    <li>使用者所生成的网站，首页要包含软件的版权信息；不得对后台版权进行修改。</li>\r\n    <li><u>您拥有使用本软件构建的网站全部内容所有权，并独立承担与这些内容的相关法律义务。</u></li>\r\n    <li>您可以对源码进行修改及优化，但要保证源码的完整性；修改后的代码版权归开发者所有，未经开发者许可，不得私自发布。</li>\r\n    <li><u>您将本软件应用在商业用途时，需遵守以下几条：</u>\r\n    <ol>\r\n        <li><u>使用本软件建设网站时，无需支付使用费用，但需保留LazyCMS支持链接信息。</u></li>\r\n        <li>本源码可以用在商业用途，但不可以更名销售，若有OEM需求，请和作者联系。</li>\r\n        <li>若网站性质等因素所限，不适合保留支持信息，请与作者联系取得书面授权。</li>\r\n    </ol>\r\n    </li>\r\n</ol>\r\n<h4><strong>II.有限担保和免责声明</strong></h4>\r\n<ol>\r\n    <li>本软件及所附带的文件是作为不提供任何明确的或隐含的赔偿或担保的形式提供的。</li>\r\n    <li>用户出于自愿而使用本软件，您必须了解使用本软件的风险，在尚未购买产品技术服务之前，我们不承诺提供任何形式的技术支持、使用担保，也不承担任何因使用本软件而产生问题的相关责任。</li>\r\n    <li>LazyCMS官方不对使用本软件构建的网站中的文章或信息承担责任。</li>\r\n</ol>\r\n<p>本协议保留作者的版权信息在许可协议文本之内，不得擅自修改其信息。</p>\r\n<p>2007-11，第1.0版 (保留对此许可协议的更新及解释权力)<br />\r\n协议著作权所有 &copy; LazyCMS.net&nbsp;&nbsp;&nbsp; &nbsp; &nbsp; 软件版权所有 &copy; LazyCMS.net</p>\r\n<p>作者：Lukin&nbsp;&nbsp;&nbsp; 邮箱：<a href="mailto:mylukin@gmail.com">mylukin@gmail.com</a></p>\r\n</blockquote></div>', 'template/default.html', 'template/inside/onepage/default.html', '0'),
(6, 6, '最近更新', 'news', '最近更新', '', '最近更新', '<p>最近更新</p>', 'template/default.html', 'template/inside/onepage/news.html', '0');


INSERT INTO `lazy_sort` (`sortid`, `sortid1`, `modelid`, `sortorder`, `sortname`, `sortpath`, `keywords`, `description`, `sortopen`, `sorttemplate1`, `sorttemplate2`, `pagetemplate1`, `pagetemplate2`) VALUES
(1, 0, 1, 1, '使用帮助', 'help', '', '', '0', 'template/default.html', 'template/inside/article[list]/default.html', 'template/default.html', 'template/inside/article[page]/default.html'),
(2, 0, 1, 2, '常见问题', 'faq', '', '', '0', 'template/default.html', 'template/inside/article[list]/default.html', 'template/default.html', 'template/inside/article[page]/default.html'),
(3, 0, 3, 3, '模板下载', 'download/template', '', '', '0', 'template/default.html', 'template/inside/template[list]/default.html', 'template/default.html', 'template/inside/template[page]/default.html'),
(4, 0, 4, 4, '程序下载', 'download', '', '', '0', 'template/default.html', 'template/inside/download[list]/default.html', 'template/default.html', 'template/inside/download[page]/default.html'),
(5, 0, 1, 5, '服务项目', 'service', '', '', '0', 'template/default.html', 'template/inside/article[list]/default.html', 'template/default.html', 'template/inside/article[page]/default.html'),
(6, 0, 6, 6, '演示网站', 'demo', '', '', '0', 'template/default.html', 'template/inside/demo[list]/default.html', 'template/default.html', 'template/inside/demo[page]/default.html');

INSERT INTO `lazy_system` (`systemname`, `systemver`, `dbversion`, `sitename`, `sitemail`, `sitekeywords`, `lockip`, `modules`, `instdate`) VALUES
('LazyCMS', '1.1.0.0408', '1.1.0', 'LazyCMS内容管理系统', 'mylukin@gmail.com', 'PHP,Lazy,CMS,内容管理系统', '', 'archives,onepage', 1207402970);
