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
  fieldefault varchar(255) default NULL,
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
  userkey varchar(6) default NULL,
  usermail varchar(100),
  question varchar(50) default NULL,
  answer varchar(50) default NULL,
  language varchar(30) default NULL,
  editor varchar(30) default NULL,
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
