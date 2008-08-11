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
CREATE INDEX IDX_system_fields__fieldename ON system_fields(fieldename);
CREATE INDEX IDX_system_fields__module ON system_fields(module);

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
CREATE INDEX IDX_system_group__system ON system_group(system);

-- 
-- 导出表中的数据 system_group
-- 

INSERT INTO system_group (groupid, groupename, groupname, purview, system) VALUES (1, 'super', '超级管理员', 'system/system,system/users,system/webftp,system/module,system/settings,content/onepage', 1);
INSERT INTO system_group (groupid, groupename, groupname, purview, system) VALUES (2, 'admin', '普通管理员', 'system/system,system/users,system/webftp,system/module,system/settings,content/onepage', 1);
INSERT INTO system_group (groupid, groupename, groupname, purview, system) VALUES (3, 'user', '注册用户', null, 1);

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
CREATE INDEX IDX_system_users__groupid ON system_users(groupid);
CREATE INDEX IDX_system_users__usermail ON system_users(usermail);
CREATE INDEX IDX_system_users__isdel ON system_users(isdel);
CREATE INDEX IDX_system_users__islock ON system_users(islock);

-- 
-- 导出表中的数据 system_users
-- 

INSERT INTO system_users (userid, groupid, username, userpass, userkey, usermail, question, answer, language, editor, regdate, isdel, islock) VALUES 
(1, 1, 'admin', '8f93109624289e13e9a0742c2f4bcf0b', '0b3122', 'mylukin@gmail.com', '888', '8888', 'zh-cn', 'fckeditor', 1215399176, 0, 0);

-- --------------------------------------------------------

-- 
-- 表的结构 content_model
-- 

CREATE TABLE content_model (
  modelid integer PRIMARY KEY,
  modelname varchar(50),
  modelename varchar(50),
  modeletable varchar(50),
  modelstate tinyint(1) default '1'
);
CREATE INDEX IDX_content_model__modelename ON content_model(modelename);
CREATE INDEX IDX_content_model__modelstate ON content_model(modelstate);

-- --------------------------------------------------------

-- 
-- 表的结构 content_article
-- 

-- CREATE TABLE content_article (
--   id integer PRIMARY KEY,
--   order int(11),
--   path varchar(255),
--   date int(11),
--   hits int(11),
--   digg int(11),
--   description varchar(255),
--   isdel tinyint(1) default '1',
--   UNIQUE (path)
-- );

-- --------------------------------------------------------

-- 
-- 表的结构 keywords
-- 

CREATE TABLE keywords (
  keyid integer PRIMARY KEY,
  keyword varchar(50),
  keysum int(11),
  UNIQUE (keyword)
);
CREATE INDEX IDX_keywords__keyword ON keywords(keyword);

-- --------------------------------------------------------

-- 
-- 表的结构 keyword_join 
-- 

CREATE TABLE keyword_join (
  kjid integer PRIMARY KEY,
  kjtype varchar(50),
  itemid int(11),
  keyid int(11)
);
CREATE INDEX IDX_keyword_join__kjtype ON keyword_join(kjtype);
CREATE INDEX IDX_keyword_join__itemid ON keyword_join(itemid);
CREATE INDEX IDX_keyword_join__keyid ON keyword_join(keyid);


-- --------------------------------------------------------

-- 
-- 表的结构 onepage
-- 
CREATE TABLE onepage (
  oneid integer PRIMARY KEY,
  oneid1 int(11),
  oneorder int(11),
  onetitle varchar(100),
  onepath varchar(255),
  onename varchar(50),
  onecontent text,
  onetemplate varchar(255),
  description varchar(250)
);