-- 
-- 数据库: LazyCMS.db
-- 

-- --------------------------------------------------------

-- 
-- 表的结构 system_fields
-- 

CREATE TABLE system_fields (
  fieldid integer PRIMARY KEY,
  module varchar(50),
  fieldorder integer(11) default '0',
  fieldname varchar(100),
  fieldename varchar(50),
  fieldtype varchar(20),
  fieldlength varchar(255) default '50',
  fieldefault varchar(255),
  fieldvalue text,
  widgetype varchar(20)
);
CREATE INDEX fieldename ON system_fields(fieldename);
CREATE INDEX module ON system_fields(module);

-- 
-- 导出表中的数据 system_fields
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 system_group
-- 

CREATE TABLE system_group (
  groupid integer PRIMARY KEY,
  groupename varchar(50),
  groupname varchar(50),
  purview text,
  system tinyint(1) default '0',
  UNIQUE (groupename)
);
CREATE INDEX system ON system_group(system);

-- 
-- 导出表中的数据 system_group
-- 

INSERT INTO system_group (groupid, groupename, groupname, purview, system) VALUES (1, 'super', '超级管理员', 'system/system,system/users,system/webftp,system/module,system/settings', 1);
INSERT INTO system_group (groupid, groupename, groupname, purview, system) VALUES (2, 'admin', '普通管理员', 'system/system,system/users,system/webftp,system/module,system/settings', 1);
INSERT INTO system_group (groupid, groupename, groupname, purview, system) VALUES (3, 'user', '注册用户', null, 1);

-- --------------------------------------------------------

-- 
-- 表的结构 system_model
-- 

CREATE TABLE system_model (
  modelid integer PRIMARY KEY,
  modelname varchar(50),
  modelename varchar(50),
  maintable varchar(50),
  addtable varchar(50),
  modelstate tinyint(1) default '1'
);
CREATE INDEX modelename ON system_model(modelename);
CREATE INDEX modelstate ON system_model(modelstate);

-- 
-- 导出表中的数据 system_model
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 system_users
-- 

CREATE TABLE system_users (
  userid integer PRIMARY KEY,
  groupid int(11),
  username varchar(30),
  userpass varchar(32),
  userkey varchar(6),
  usermail varchar(100),
  question varchar(50),
  answer varchar(50),
  language varchar(30),
  editor varchar(30),
  regdate int(11),
  isdel tinyint(1) default '0',
  islock tinyint(1) default '0',
  UNIQUE (username)
);
CREATE INDEX groupid ON system_users(groupid);
CREATE INDEX usermail ON system_users(usermail);
CREATE INDEX isdel ON system_users(isdel);
CREATE INDEX islock ON system_users(islock);

-- 
-- 导出表中的数据 system_users
-- 

INSERT INTO system_users (userid, groupid, username, userpass, userkey, usermail, question, answer, language, editor, regdate, isdel, islock) VALUES 
(1, 1, 'admin', '8f93109624289e13e9a0742c2f4bcf0b', '0b3122', 'mylukin@gmail.com', '888', '8888', 'zh-cn', 'editor', 1215399176, 0, 0);



-- --------------------------------------------------------

-- 
-- 表的结构 article_sort
-- 
-- keywords:index.html 使用数据库字段里的内容，分页第二页使用其他关键词
-- description:同上

CREATE TABLE article_sort (
  sortid integer PRIMARY KEY,
  modelid int(11),
  sortid1 int(11),
  sortorder int(11),
  sortname varchar(50),
  sortpath varchar(255),
  keywords varchar(255),
  description varchar(255),
  sortopen tinyint(1) default '0',
  sorttemplate varchar(255),
  pagetemplate varchar(255),
  subdomain varchar(255),
  UNIQUE (sortname),
  UNIQUE (sortpath)
);
CREATE INDEX sortid1 ON article_sort(sortid1);
CREATE INDEX sortname ON article_sort(sortname);
CREATE INDEX modelid ON article_sort(modelid);


-- --------------------------------------------------------

-- 
-- 表的结构 article_index
-- 

CREATE TABLE article_index (
  artid integer PRIMARY KEY,
  sortid int(11),
  modelid int(11),
  artorder int(11),
  artpath varchar(255),
  artdate int(11),
  arthits int(11),
  artdigg int(11),
  description varchar(255),
  isdel tinyint(1) default '0',
  UNIQUE (artpath)
);
CREATE INDEX sortid ON article_index(sortid);
CREATE INDEX modelid ON article_index(modelid);


-- --------------------------------------------------------

-- 
-- 表的结构 article_keywords
-- 

CREATE TABLE article_keywords (
  keyid integer PRIMARY KEY,
  artid int(11),
  sortid int(11),
  keyword varchar(50),
  keysum int(11),
  UNIQUE (keyword)
);
CREATE INDEX artid ON article_keywords(artid);
CREATE INDEX sortid ON article_keywords(sortid);
CREATE INDEX keyword ON article_keywords(keyword);