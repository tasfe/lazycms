
系统支持mysql mysqli sqlite 三种连接方式

后台登录地址：admin

系统配置文件：common/config.php

所有文件必须引入：global.php 文件

common 共享文件目录

  class		-- 系统公用类库
  editor	-- 编辑器文件夹
  images	-- 系统公用图片
  js		-- 共同js库

themes 主题目录



目录权限设置

/			  -- 0777
/lazy2.sql		  -- 0777
/lazy2_sqlite.sql	  -- 0777
/install.php		  -- 0777
/common/config.php	  -- 0777
/common/data/module.php   -- 0777
/common/data/dict/	  -- 0777
/common/images/icons.css  -- 0644






自动载入语言包文件。
文章智能提取
模板扔掉内部模板，外部模板的概念，直接用{include:header.htm/}
菜单管理器

1.支持MySQL、SQLite 数据库，方便网站移植。
2.后台界面重新制作，更具人性化，简化操作，提高后台的易用性。
3.系统基础为会员系统、内容管理、自定义标签，其他功能一律以扩展模块展现。
4.半面向对象、半面向过程的系统内核，再次提高运行速度。
5.完善的会员系统，细化的系统权限。
6.方便快捷的文件管理功能，ajax批量上传文件。
7.内容管理分类无线分级，支持文章回收站，文章关键词内连，自动匹配相关文章。
8.添加文章，自动提供可选tags，网站可优化似有的专业词库，也可以直接使用搜狗的专业词库。
9.自定义模型，完善的自定义模型管理，增加验证机制。
10.支持多语言，多模板，模板防盗！
11.模块自由安装与卸载。