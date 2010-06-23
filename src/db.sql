/*
SQLyog 企业版 - MySQL GUI v7.14 
MySQL - 5.0.67-community-nt : Database - test
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

/*Table structure for table `lazy_model` */

DROP TABLE IF EXISTS `lazy_model`;

CREATE TABLE `lazy_model` (
  `modelid` bigint(20) unsigned NOT NULL auto_increment,
  `language` varchar(20) NOT NULL default 'en',
  `code` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `path` varchar(255) NOT NULL,
  `list` varchar(50) default NULL,
  `page` varchar(50) default NULL,
  `fields` longtext NOT NULL,
  `state` enum('0','1') NOT NULL default '1',
  PRIMARY KEY  (`modelid`),
  KEY `code` (`code`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

/*Data for the table `lazy_model` */

insert  into `lazy_model`(`modelid`,`language`,`code`,`name`,`path`,`list`,`page`,`fields`,`state`) values (1,'zh-CN','article','文章','%ID.html','default.html','default.html','a:1:{i:0;s:115:\"l=%E6%9D%A5%E6%BA%90&n=from&t=select&w=200px&s=%E6%9C%AA%E7%9F%A5%0A%E7%BD%91%E7%BB%9C%0ALukin&d=%E7%BD%91%E7%BB%9C\";}','1');
insert  into `lazy_model`(`modelid`,`language`,`code`,`name`,`path`,`list`,`page`,`fields`,`state`) values (2,'zh-CN','download','下载','%ID.html','default.html','default.html','a:1:{i:0;s:73:\"l=%E4%B8%8B%E8%BD%BD%E5%9C%B0%E5%9D%80&n=downurl&t=input&w=200px&c=255&d=\";}','1');

/*Table structure for table `lazy_option` */

DROP TABLE IF EXISTS `lazy_option`;

CREATE TABLE `lazy_option` (
  `module` varchar(50) NOT NULL,
  `code` varchar(255) NOT NULL,
  `value` longtext,
  `type` varchar(20) NOT NULL,
  UNIQUE KEY `opt_idx` (`code`,`module`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `lazy_option` */

insert  into `lazy_option`(`module`,`code`,`value`,`type`) values ('System','SiteName','LazyCMS v2.0 演示站','string');
insert  into `lazy_option`(`module`,`code`,`value`,`type`) values ('System','Template','themes','string');
insert  into `lazy_option`(`module`,`code`,`value`,`type`) values ('System','TemplateExts','htm,html,shtml','string');
insert  into `lazy_option`(`module`,`code`,`value`,`type`) values ('System','CreateFileExt','.html','string');

/*Table structure for table `lazy_pinyin` */

DROP TABLE IF EXISTS `lazy_pinyin`;

CREATE TABLE `lazy_pinyin` (
  `key` varchar(50) NOT NULL,
  `value` text,
  PRIMARY KEY  (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `lazy_pinyin` */

insert  into `lazy_pinyin`(`key`,`value`) values ('a','啊,阿,锕');
insert  into `lazy_pinyin`(`key`,`value`) values ('ai','埃,挨,哎,唉,哀,皑,癌,蔼,矮,艾,碍,爱,隘,诶,捱,嗳,嗌,嫒,瑷,暧,砹,锿,霭');
insert  into `lazy_pinyin`(`key`,`value`) values ('an','鞍,氨,安,俺,按,暗,岸,胺,案,谙,埯,揞,犴,庵,桉,铵,鹌,顸,黯');
insert  into `lazy_pinyin`(`key`,`value`) values ('ang','肮,昂,盎');
insert  into `lazy_pinyin`(`key`,`value`) values ('ao','凹,敖,熬,翱,袄,傲,奥,懊,澳,坳,拗,嗷,噢,岙,廒,遨,媪,骜,聱,螯,鏊,鳌,鏖');
insert  into `lazy_pinyin`(`key`,`value`) values ('ba','芭,捌,扒,叭,吧,笆,八,疤,巴,拔,跋,靶,把,耙,坝,霸,罢,爸,茇,菝,萆,捭,岜,灞,杷,钯,粑,鲅,魃');
insert  into `lazy_pinyin`(`key`,`value`) values ('bai','白,柏,百,摆,佰,败,拜,稗,薜,掰,鞴');
insert  into `lazy_pinyin`(`key`,`value`) values ('ban','斑,班,搬,扳,般,颁,板,版,扮,拌,伴,瓣,半,办,绊,阪,坂,豳,钣,瘢,癍,舨');
insert  into `lazy_pinyin`(`key`,`value`) values ('bang','邦,帮,梆,榜,膀,绑,棒,磅,蚌,镑,傍,谤,蒡,螃');
insert  into `lazy_pinyin`(`key`,`value`) values ('bao','苞,胞,包,褒,雹,保,堡,饱,宝,抱,报,暴,豹,鲍,爆,勹,葆,宀,孢,煲,鸨,褓,趵,龅');
insert  into `lazy_pinyin`(`key`,`value`) values ('bo','剥,薄,玻,菠,播,拨,钵,波,博,勃,搏,铂,箔,伯,帛,舶,脖,膊,渤,泊,驳,亳,蕃,啵,饽,檗,擘,礴,钹,鹁,簸,跛');
insert  into `lazy_pinyin`(`key`,`value`) values ('bei','杯,碑,悲,卑,北,辈,背,贝,钡,倍,狈,备,惫,焙,被,孛,陂,邶,埤,蓓,呗,怫,悖,碚,鹎,褙,鐾');
insert  into `lazy_pinyin`(`key`,`value`) values ('ben','奔,苯,本,笨,畚,坌,锛');
insert  into `lazy_pinyin`(`key`,`value`) values ('beng','崩,绷,甭,泵,蹦,迸,唪,嘣,甏');
insert  into `lazy_pinyin`(`key`,`value`) values ('bi','逼,鼻,比,鄙,笔,彼,碧,蓖,蔽,毕,毙,毖,币,庇,痹,闭,敝,弊,必,辟,壁,臂,避,陛,匕,仳,俾,芘,荜,荸,吡,哔,狴,庳,愎,滗,濞,弼,妣,婢,嬖,璧,贲,畀,铋,秕,裨,筚,箅,篦,舭,襞,跸,髀');
insert  into `lazy_pinyin`(`key`,`value`) values ('bian','鞭,边,编,贬,扁,便,变,卞,辨,辩,辫,遍,匾,弁,苄,忭,汴,缏,煸,砭,碥,稹,窆,蝙,笾,鳊');
insert  into `lazy_pinyin`(`key`,`value`) values ('biao','标,彪,膘,表,婊,骠,飑,飙,飚,灬,镖,镳,瘭,裱,鳔');
insert  into `lazy_pinyin`(`key`,`value`) values ('bie','鳖,憋,别,瘪,蹩,鳘');
insert  into `lazy_pinyin`(`key`,`value`) values ('bin','彬,斌,濒,滨,宾,摈,傧,浜,缤,玢,殡,膑,镔,髌,鬓');
insert  into `lazy_pinyin`(`key`,`value`) values ('bing','兵,冰,柄,丙,秉,饼,炳,病,并,禀,邴,摒,绠,枋,槟,燹');
insert  into `lazy_pinyin`(`key`,`value`) values ('bu','捕,卜,哺,补,埠,不,布,步,簿,部,怖,拊,卟,逋,瓿,晡,钚,醭');
insert  into `lazy_pinyin`(`key`,`value`) values ('ca','擦,嚓,礤');
insert  into `lazy_pinyin`(`key`,`value`) values ('cai','猜,裁,材,才,财,睬,踩,采,彩,菜,蔡');
insert  into `lazy_pinyin`(`key`,`value`) values ('can','餐,参,蚕,残,惭,惨,灿,骖,璨,粲,黪');
insert  into `lazy_pinyin`(`key`,`value`) values ('cang','苍,舱,仓,沧,藏,伧');
insert  into `lazy_pinyin`(`key`,`value`) values ('cao','操,糙,槽,曹,草,艹,嘈,漕,螬,艚');
insert  into `lazy_pinyin`(`key`,`value`) values ('ce','厕,策,侧,册,测,刂,帻,恻');
insert  into `lazy_pinyin`(`key`,`value`) values ('ceng','层,蹭,噌');
insert  into `lazy_pinyin`(`key`,`value`) values ('cha','插,叉,茬,茶,查,碴,搽,察,岔,差,诧,猹,馇,汊,姹,杈,楂,槎,檫,钗,锸,镲,衩');
insert  into `lazy_pinyin`(`key`,`value`) values ('chai','拆,柴,豺,侪,茈,瘥,虿,龇');
insert  into `lazy_pinyin`(`key`,`value`) values ('chan','搀,掺,蝉,馋,谗,缠,铲,产,阐,颤,冁,谄,谶,蒇,廛,忏,潺,澶,孱,羼,婵,嬗,骣,觇,禅,镡,裣,蟾,躔');
insert  into `lazy_pinyin`(`key`,`value`) values ('chang','昌,猖,场,尝,常,长,偿,肠,厂,敞,畅,唱,倡,伥,鬯,苌,菖,徜,怅,惝,阊,娼,嫦,昶,氅,鲳');
insert  into `lazy_pinyin`(`key`,`value`) values ('chao','超,抄,钞,朝,嘲,潮,巢,吵,炒,怊,绉,晁,耖');
insert  into `lazy_pinyin`(`key`,`value`) values ('che','车,扯,撤,掣,彻,澈,坼,屮,砗');
insert  into `lazy_pinyin`(`key`,`value`) values ('chen','郴,臣,辰,尘,晨,忱,沉,陈,趁,衬,称,谌,抻,嗔,宸,琛,榇,肜,胂,碜,龀');
insert  into `lazy_pinyin`(`key`,`value`) values ('cheng','撑,城,橙,成,呈,乘,程,惩,澄,诚,承,逞,骋,秤,埕,嵊,徵,浈,枨,柽,樘,晟,塍,瞠,铖,裎,蛏,酲');
insert  into `lazy_pinyin`(`key`,`value`) values ('chi','吃,痴,持,匙,池,迟,弛,驰,耻,齿,侈,尺,赤,翅,斥,炽,傺,墀,芪,茌,搋,叱,哧,啻,嗤,彳,饬,沲,媸,敕,胝,眙,眵,鸱,瘛,褫,蚩,螭,笞,篪,豉,踅,踟,魑');
insert  into `lazy_pinyin`(`key`,`value`) values ('chong','充,冲,虫,崇,宠,茺,忡,憧,铳,艟');
insert  into `lazy_pinyin`(`key`,`value`) values ('chou','抽,酬,畴,踌,稠,愁,筹,仇,绸,瞅,丑,俦,圳,帱,惆,溴,妯,瘳,雠,鲋');
insert  into `lazy_pinyin`(`key`,`value`) values ('chu','臭,初,出,橱,厨,躇,锄,雏,滁,除,楚,础,储,矗,搐,触,处,亍,刍,憷,绌,杵,楮,樗,蜍,蹰,黜');
insert  into `lazy_pinyin`(`key`,`value`) values ('chuan','揣,川,穿,椽,传,船,喘,串,掾,舛,惴,遄,巛,氚,钏,镩,舡');
insert  into `lazy_pinyin`(`key`,`value`) values ('chuang','疮,窗,幢,床,闯,创,怆');
insert  into `lazy_pinyin`(`key`,`value`) values ('chui','吹,炊,捶,锤,垂,陲,棰,槌');
insert  into `lazy_pinyin`(`key`,`value`) values ('chun','春,椿,醇,唇,淳,纯,蠢,促,莼,沌,肫,朐,鹑,蝽');
insert  into `lazy_pinyin`(`key`,`value`) values ('chuo','戳,绰,蔟,辶,辍,镞,踔,龊');
insert  into `lazy_pinyin`(`key`,`value`) values ('ci','疵,茨,磁,雌,辞,慈,瓷,词,此,刺,赐,次,荠,呲,嵯,鹚,螅,糍,趑');
insert  into `lazy_pinyin`(`key`,`value`) values ('cong','聪,葱,囱,匆,从,丛,偬,苁,淙,骢,琮,璁,枞');
insert  into `lazy_pinyin`(`key`,`value`) values ('cu','凑,粗,醋,簇,猝,殂,蹙');
insert  into `lazy_pinyin`(`key`,`value`) values ('cuan','蹿,篡,窜,汆,撺,昕,爨');
insert  into `lazy_pinyin`(`key`,`value`) values ('cui','摧,崔,催,脆,瘁,粹,淬,翠,萃,悴,璀,榱,隹');
insert  into `lazy_pinyin`(`key`,`value`) values ('cun','村,存,寸,磋,忖,皴');
insert  into `lazy_pinyin`(`key`,`value`) values ('cuo','撮,搓,措,挫,错,厝,脞,锉,矬,痤,鹾,蹉,躜');
insert  into `lazy_pinyin`(`key`,`value`) values ('da','搭,达,答,瘩,打,大,耷,哒,嗒,怛,妲,疸,褡,笪,靼,鞑');
insert  into `lazy_pinyin`(`key`,`value`) values ('dai','呆,歹,傣,戴,带,殆,代,贷,袋,待,逮,怠,埭,甙,呔,岱,迨,逯,骀,绐,玳,黛');
insert  into `lazy_pinyin`(`key`,`value`) values ('dan','耽,担,丹,单,郸,掸,胆,旦,氮,但,惮,淡,诞,弹,蛋,亻,儋,卩,萏,啖,澹,檐,殚,赕,眈,瘅,聃,箪');
insert  into `lazy_pinyin`(`key`,`value`) values ('dang','当,挡,党,荡,档,谠,凼,菪,宕,砀,铛,裆');
insert  into `lazy_pinyin`(`key`,`value`) values ('dao','刀,捣,蹈,倒,岛,祷,导,到,稻,悼,道,盗,叨,啁,忉,洮,氘,焘,忑,纛');
insert  into `lazy_pinyin`(`key`,`value`) values ('de','德,得,的,锝');
insert  into `lazy_pinyin`(`key`,`value`) values ('deng','蹬,灯,登,等,瞪,凳,邓,噔,嶝,戥,磴,镫,簦');
insert  into `lazy_pinyin`(`key`,`value`) values ('di','堤,低,滴,迪,敌,笛,狄,涤,翟,嫡,抵,底,地,蒂,第,帝,弟,递,缔,氐,籴,诋,谛,邸,坻,莜,荻,嘀,娣,柢,棣,觌,砥,碲,睇,镝,羝,骶');
insert  into `lazy_pinyin`(`key`,`value`) values ('dian','颠,掂,滇,碘,点,典,靛,垫,电,佃,甸,店,惦,奠,淀,殿,丶,阽,坫,埝,巅,玷,癜,癫,簟,踮');
insert  into `lazy_pinyin`(`key`,`value`) values ('diao','碉,叼,雕,凋,刁,掉,吊,钓,调,轺,铞,蜩,粜,貂');
insert  into `lazy_pinyin`(`key`,`value`) values ('die','跌,爹,碟,蝶,迭,谍,叠,佚,垤,堞,揲,喋,渫,轶,牒,瓞,褶,耋,蹀,鲽,鳎');
insert  into `lazy_pinyin`(`key`,`value`) values ('ding','丁,盯,叮,钉,顶,鼎,锭,定,订,丢,仃,啶,玎,腚,碇,町,铤,疔,耵,酊');
insert  into `lazy_pinyin`(`key`,`value`) values ('dong','东,冬,董,懂,动,栋,侗,恫,冻,洞,垌,咚,岽,峒,夂,氡,胨,胴,硐,鸫');
insert  into `lazy_pinyin`(`key`,`value`) values ('dou','兜,抖,斗,陡,豆,逗,痘,蔸,钭,窦,窬,蚪,篼,酡');
insert  into `lazy_pinyin`(`key`,`value`) values ('du','都,督,毒,犊,独,读,堵,睹,赌,杜,镀,肚,度,渡,妒,芏,嘟,渎,椟,橐,牍,蠹,笃,髑,黩');
insert  into `lazy_pinyin`(`key`,`value`) values ('duan','端,短,锻,段,断,缎,彖,椴,煅,簖');
insert  into `lazy_pinyin`(`key`,`value`) values ('dui','堆,兑,队,对,怼,憝,碓');
insert  into `lazy_pinyin`(`key`,`value`) values ('dun','墩,吨,蹲,敦,顿,囤,钝,盾,遁,炖,砘,礅,盹,镦,趸');
insert  into `lazy_pinyin`(`key`,`value`) values ('duo','掇,哆,多,夺,垛,躲,朵,跺,舵,剁,惰,堕,咄,哚,缍,柁,铎,裰,踱');
insert  into `lazy_pinyin`(`key`,`value`) values ('e','蛾,峨,鹅,俄,额,讹,娥,恶,厄,扼,遏,鄂,饿,噩,谔,垩,垭,苊,莪,萼,呃,愕,屙,婀,轭,曷,腭,硪,锇,锷,鹗,颚,鳄');
insert  into `lazy_pinyin`(`key`,`value`) values ('en','恩,蒽,摁,唔,嗯');
insert  into `lazy_pinyin`(`key`,`value`) values ('er','而,儿,耳,尔,饵,洱,二,贰,迩,珥,铒,鸸,鲕');
insert  into `lazy_pinyin`(`key`,`value`) values ('fa','发,罚,筏,伐,乏,阀,法,珐,垡,砝');
insert  into `lazy_pinyin`(`key`,`value`) values ('fan','藩,帆,番,翻,樊,矾,钒,繁,凡,烦,反,返,范,贩,犯,饭,泛,蘩,幡,犭,梵,攵,燔,畈,蹯');
insert  into `lazy_pinyin`(`key`,`value`) values ('fang','坊,芳,方,肪,房,防,妨,仿,访,纺,放,匚,邡,彷,钫,舫,鲂');
insert  into `lazy_pinyin`(`key`,`value`) values ('fei','菲,非,啡,飞,肥,匪,诽,吠,肺,废,沸,费,芾,狒,悱,淝,妃,绋,绯,榧,腓,斐,扉,祓,砩,镄,痱,蜚,篚,翡,霏,鲱');
insert  into `lazy_pinyin`(`key`,`value`) values ('fen','芬,酚,吩,氛,分,纷,坟,焚,汾,粉,奋,份,忿,愤,粪,偾,瀵,棼,愍,鲼,鼢');
insert  into `lazy_pinyin`(`key`,`value`) values ('feng','丰,封,枫,蜂,峰,锋,风,疯,烽,逢,冯,缝,讽,奉,凤,俸,酆,葑,沣,砜');
insert  into `lazy_pinyin`(`key`,`value`) values ('fu','佛,否,夫,敷,肤,孵,扶,拂,辐,幅,氟,符,伏,俘,服,浮,涪,福,袱,弗,甫,抚,辅,俯,釜,斧,脯,腑,府,腐,赴,副,覆,赋,复,傅,付,阜,父,腹,负,富,讣,附,妇,缚,咐,匐,凫,郛,芙,苻,茯,莩,菔,呋,幞,滏,艴,孚,驸,绂,桴,赙,黻,黼,罘,稃,馥,虍,蚨,蜉,蝠,蝮,麸,趺,跗,鳆');
insert  into `lazy_pinyin`(`key`,`value`) values ('ga','噶,嘎,蛤,尬,呷,尕,尜,旮,钆');
insert  into `lazy_pinyin`(`key`,`value`) values ('gai','该,改,概,钙,盖,溉,丐,陔,垓,戤,赅,胲');
insert  into `lazy_pinyin`(`key`,`value`) values ('gan','干,甘,杆,柑,竿,肝,赶,感,秆,敢,赣,坩,苷,尴,擀,泔,淦,澉,绀,橄,旰,矸,疳,酐');
insert  into `lazy_pinyin`(`key`,`value`) values ('gang','冈,刚,钢,缸,肛,纲,岗,港,戆,罡,颃,筻');
insert  into `lazy_pinyin`(`key`,`value`) values ('gong','杠,工,攻,功,恭,龚,供,躬,公,宫,弓,巩,汞,拱,贡,共,蕻,廾,咣,珙,肱,蚣,蛩,觥');
insert  into `lazy_pinyin`(`key`,`value`) values ('gao','篙,皋,高,膏,羔,糕,搞,镐,稿,告,睾,诰,郜,蒿,藁,缟,槔,槁,杲,锆');
insert  into `lazy_pinyin`(`key`,`value`) values ('ge','哥,歌,搁,戈,鸽,胳,疙,割,革,葛,格,阁,隔,铬,个,各,鬲,仡,哿,塥,嗝,纥,搿,膈,硌,铪,镉,袼,颌,虼,舸,骼,髂');
insert  into `lazy_pinyin`(`key`,`value`) values ('gei','给');
insert  into `lazy_pinyin`(`key`,`value`) values ('gen','根,跟,亘,茛,哏,艮');
insert  into `lazy_pinyin`(`key`,`value`) values ('geng','耕,更,庚,羹,埂,耿,梗,哽,赓,鲠');
insert  into `lazy_pinyin`(`key`,`value`) values ('gou','钩,勾,沟,苟,狗,垢,构,购,够,佝,诟,岣,遘,媾,缑,觏,彀,鸲,笱,篝,鞲');
insert  into `lazy_pinyin`(`key`,`value`) values ('gu','辜,菇,咕,箍,估,沽,孤,姑,鼓,古,蛊,骨,谷,股,故,顾,固,雇,嘏,诂,菰,哌,崮,汩,梏,轱,牯,牿,胍,臌,毂,瞽,罟,钴,锢,瓠,鸪,鹄,痼,蛄,酤,觚,鲴,骰,鹘');
insert  into `lazy_pinyin`(`key`,`value`) values ('gua','刮,瓜,剐,寡,挂,褂,卦,诖,呱,栝,鸹');
insert  into `lazy_pinyin`(`key`,`value`) values ('guai','乖,拐,怪,哙');
insert  into `lazy_pinyin`(`key`,`value`) values ('guan','棺,关,官,冠,观,管,馆,罐,惯,灌,贯,倌,莞,掼,涫,盥,鹳,鳏');
insert  into `lazy_pinyin`(`key`,`value`) values ('guang','光,广,逛,犷,桄,胱,疒');
insert  into `lazy_pinyin`(`key`,`value`) values ('gui','瑰,规,圭,硅,归,龟,闺,轨,鬼,诡,癸,桂,柜,跪,贵,刽,匦,刿,庋,宄,妫,桧,炅,晷,皈,簋,鲑,鳜');
insert  into `lazy_pinyin`(`key`,`value`) values ('gun','辊,滚,棍,丨,衮,绲,磙,鲧');
insert  into `lazy_pinyin`(`key`,`value`) values ('guo','锅,郭,国,果,裹,过,馘,蠃,埚,掴,呙,囗,帼,崞,猓,椁,虢,锞,聒,蜮,蜾,蝈');
insert  into `lazy_pinyin`(`key`,`value`) values ('ha','哈');
insert  into `lazy_pinyin`(`key`,`value`) values ('hai','骸,孩,海,氦,亥,害,骇,咴,嗨,颏,醢');
insert  into `lazy_pinyin`(`key`,`value`) values ('han','酣,憨,邯,韩,含,涵,寒,函,喊,罕,翰,撼,捍,旱,憾,悍,焊,汗,汉,邗,菡,撖,阚,瀚,晗,焓,颔,蚶,鼾');
insert  into `lazy_pinyin`(`key`,`value`) values ('hen','夯,痕,很,狠,恨');
insert  into `lazy_pinyin`(`key`,`value`) values ('hang','杭,航,沆,绗,珩,桁');
insert  into `lazy_pinyin`(`key`,`value`) values ('hao','壕,嚎,豪,毫,郝,好,耗,号,浩,薅,嗥,嚆,濠,灏,昊,皓,颢,蚝');
insert  into `lazy_pinyin`(`key`,`value`) values ('he','呵,喝,荷,菏,核,禾,和,何,合,盒,貉,阂,河,涸,赫,褐,鹤,贺,诃,劾,壑,藿,嗑,嗬,阖,盍,蚵,翮');
insert  into `lazy_pinyin`(`key`,`value`) values ('hei','嘿,黑');
insert  into `lazy_pinyin`(`key`,`value`) values ('heng','哼,亨,横,衡,恒,訇,蘅');
insert  into `lazy_pinyin`(`key`,`value`) values ('hong','轰,哄,烘,虹,鸿,洪,宏,弘,红,黉,讧,荭,薨,闳,泓');
insert  into `lazy_pinyin`(`key`,`value`) values ('hou','喉,侯,猴,吼,厚,候,后,堠,後,逅,瘊,篌,糇,鲎,骺');
insert  into `lazy_pinyin`(`key`,`value`) values ('hu','呼,乎,忽,瑚,壶,葫,胡,蝴,狐,糊,湖,弧,虎,唬,护,互,沪,户,冱,唿,囫,岵,猢,怙,惚,浒,滹,琥,槲,轷,觳,烀,煳,戽,扈,祜,鹕,鹱,笏,醐,斛');
insert  into `lazy_pinyin`(`key`,`value`) values ('hua','花,哗,华,猾,滑,画,划,化,话,劐,浍,骅,桦,铧,稞');
insert  into `lazy_pinyin`(`key`,`value`) values ('huai','槐,徊,怀,淮,坏,还,踝');
insert  into `lazy_pinyin`(`key`,`value`) values ('huan','欢,环,桓,缓,换,患,唤,痪,豢,焕,涣,宦,幻,郇,奂,垸,擐,圜,洹,浣,漶,寰,逭,缳,锾,鲩,鬟');
insert  into `lazy_pinyin`(`key`,`value`) values ('huang','荒,慌,黄,磺,蝗,簧,皇,凰,惶,煌,晃,幌,恍,谎,隍,徨,湟,潢,遑,璜,肓,癀,蟥,篁,鳇');
insert  into `lazy_pinyin`(`key`,`value`) values ('hui','灰,挥,辉,徽,恢,蛔,回,毁,悔,慧,卉,惠,晦,贿,秽,会,烩,汇,讳,诲,绘,诙,茴,荟,蕙,哕,喙,隳,洄,彗,缋,珲,晖,恚,虺,蟪,麾');
insert  into `lazy_pinyin`(`key`,`value`) values ('hun','荤,昏,婚,魂,浑,混,诨,馄,阍,溷,缗');
insert  into `lazy_pinyin`(`key`,`value`) values ('huo','豁,活,伙,火,获,或,惑,霍,货,祸,攉,嚯,夥,钬,锪,镬,耠,蠖');
insert  into `lazy_pinyin`(`key`,`value`) values ('ji','击,圾,基,机,畸,稽,积,箕,肌,饥,迹,激,讥,鸡,姬,绩,缉,吉,极,棘,辑,籍,集,及,急,疾,汲,即,嫉,级,挤,几,脊,己,蓟,技,冀,季,伎,祭,剂,悸,济,寄,寂,计,记,既,忌,际,妓,继,纪,居,丌,乩,剞,佶,佴,脔,墼,芨,芰,萁,蒺,蕺,掎,叽,咭,哜,唧,岌,嵴,洎,彐,屐,骥,畿,玑,楫,殛,戟,戢,赍,觊,犄,齑,矶,羁,嵇,稷,瘠,瘵,虮,笈,笄,暨,跻,跽,霁,鲚,鲫,髻,麂');
insert  into `lazy_pinyin`(`key`,`value`) values ('jia','嘉,枷,夹,佳,家,加,荚,颊,贾,甲,钾,假,稼,价,架,驾,嫁,伽,郏,拮,岬,浃,迦,珈,戛,胛,恝,铗,镓,痂,蛱,笳,袈,跏');
insert  into `lazy_pinyin`(`key`,`value`) values ('jian','歼,监,坚,尖,笺,间,煎,兼,肩,艰,奸,缄,茧,检,柬,碱,硷,拣,捡,简,俭,剪,减,荐,槛,鉴,践,贱,见,键,箭,件,健,舰,剑,饯,渐,溅,涧,建,僭,谏,谫,菅,蒹,搛,囝,湔,蹇,謇,缣,枧,柙,楗,戋,戬,牮,犍,毽,腱,睑,锏,鹣,裥,笕,箴,翦,趼,踺,鲣,鞯');
insert  into `lazy_pinyin`(`key`,`value`) values ('jiang','僵,姜,将,浆,江,疆,蒋,桨,奖,讲,匠,酱,降,茳,洚,绛,缰,犟,礓,耩,糨,豇');
insert  into `lazy_pinyin`(`key`,`value`) values ('jiao','蕉,椒,礁,焦,胶,交,郊,浇,骄,娇,嚼,搅,铰,矫,侥,脚,狡,角,饺,缴,绞,剿,教,酵,轿,较,叫,佼,僬,茭,挢,噍,峤,徼,姣,纟,敫,皎,鹪,蛟,醮,跤,鲛');
insert  into `lazy_pinyin`(`key`,`value`) values ('jie','窖,揭,接,皆,秸,街,阶,截,劫,节,桔,杰,捷,睫,竭,洁,结,解,姐,戒,藉,芥,界,借,介,疥,诫,届,偈,讦,诘,喈,嗟,獬,婕,孑,桀,獒,碣,锴,疖,袷,颉,蚧,羯,鲒,骱,髫');
insert  into `lazy_pinyin`(`key`,`value`) values ('jin','巾,筋,斤,金,今,津,襟,紧,锦,仅,谨,进,靳,晋,禁,近,烬,浸,尽,卺,荩,堇,噤,馑,廑,妗,缙,瑾,槿,赆,觐,钅,锓,衿,矜');
insert  into `lazy_pinyin`(`key`,`value`) values ('jing','劲,荆,兢,茎,睛,晶,鲸,京,惊,精,粳,经,井,警,景,颈,静,境,敬,镜,径,痉,靖,竟,竞,净,刭,儆,阱,菁,獍,憬,泾,迳,弪,婧,肼,胫,腈,旌');
insert  into `lazy_pinyin`(`key`,`value`) values ('jiong','炯,窘,冂,迥,扃');
insert  into `lazy_pinyin`(`key`,`value`) values ('jiu','揪,究,纠,玖,韭,久,灸,九,酒,厩,救,旧,臼,舅,咎,就,疚,僦,啾,阄,柩,桕,鹫,赳,鬏');
insert  into `lazy_pinyin`(`key`,`value`) values ('ju','鞠,拘,狙,疽,驹,菊,局,咀,矩,举,沮,聚,拒,据,巨,具,距,踞,锯,俱,句,惧,炬,剧,倨,讵,苣,苴,莒,掬,遽,屦,琚,枸,椐,榘,榉,橘,犋,飓,钜,锔,窭,裾,趄,醵,踽,龃,雎,鞫');
insert  into `lazy_pinyin`(`key`,`value`) values ('juan','捐,鹃,娟,倦,眷,卷,绢,鄄,狷,涓,桊,蠲,锩,镌,隽');
insert  into `lazy_pinyin`(`key`,`value`) values ('jue','撅,攫,抉,掘,倔,爵,觉,决,诀,绝,厥,劂,谲,矍,蕨,噘,崛,獗,孓,珏,桷,橛,爝,镢,蹶,觖');
insert  into `lazy_pinyin`(`key`,`value`) values ('jun','均,菌,钧,军,君,峻,俊,竣,浚,郡,骏,捃,狻,皲,筠,麇');
insert  into `lazy_pinyin`(`key`,`value`) values ('ka','喀,咖,卡,佧,咔,胩');
insert  into `lazy_pinyin`(`key`,`value`) values ('ke','咯,坷,苛,柯,棵,磕,颗,科,壳,咳,可,渴,克,刻,客,课,岢,恪,溘,骒,缂,珂,轲,氪,瞌,钶,疴,窠,蝌,髁');
insert  into `lazy_pinyin`(`key`,`value`) values ('kai','开,揩,楷,凯,慨,剀,垲,蒈,忾,恺,铠,锎');
insert  into `lazy_pinyin`(`key`,`value`) values ('kan','刊,堪,勘,坎,砍,看,侃,凵,莰,莶,戡,龛,瞰');
insert  into `lazy_pinyin`(`key`,`value`) values ('kang','康,慷,糠,扛,抗,亢,炕,坑,伉,闶,钪');
insert  into `lazy_pinyin`(`key`,`value`) values ('kao','考,拷,烤,靠,尻,栲,犒,铐');
insert  into `lazy_pinyin`(`key`,`value`) values ('ken','肯,啃,垦,恳,垠,裉,颀');
insert  into `lazy_pinyin`(`key`,`value`) values ('keng','吭,忐,铿');
insert  into `lazy_pinyin`(`key`,`value`) values ('kong','空,恐,孔,控,倥,崆,箜');
insert  into `lazy_pinyin`(`key`,`value`) values ('kou','抠,口,扣,寇,芤,蔻,叩,眍,筘');
insert  into `lazy_pinyin`(`key`,`value`) values ('ku','枯,哭,窟,苦,酷,库,裤,刳,堀,喾,绔,骷');
insert  into `lazy_pinyin`(`key`,`value`) values ('kua','夸,垮,挎,跨,胯,侉');
insert  into `lazy_pinyin`(`key`,`value`) values ('kuai','块,筷,侩,快,蒯,郐,蒉,狯,脍');
insert  into `lazy_pinyin`(`key`,`value`) values ('kuan','宽,款,髋');
insert  into `lazy_pinyin`(`key`,`value`) values ('kuang','匡,筐,狂,框,矿,眶,旷,况,诓,诳,邝,圹,夼,哐,纩,贶');
insert  into `lazy_pinyin`(`key`,`value`) values ('kui','亏,盔,岿,窥,葵,奎,魁,傀,馈,愧,溃,馗,匮,夔,隗,揆,喹,喟,悝,愦,阕,逵,暌,睽,聩,蝰,篑,臾,跬');
insert  into `lazy_pinyin`(`key`,`value`) values ('kun','坤,昆,捆,困,悃,阃,琨,锟,醌,鲲,髡');
insert  into `lazy_pinyin`(`key`,`value`) values ('kuo','括,扩,廓,阔,蛞');
insert  into `lazy_pinyin`(`key`,`value`) values ('la','垃,拉,喇,蜡,腊,辣,啦,剌,摺,邋,旯,砬,瘌');
insert  into `lazy_pinyin`(`key`,`value`) values ('lai','莱,来,赖,崃,徕,涞,濑,赉,睐,铼,癞,籁');
insert  into `lazy_pinyin`(`key`,`value`) values ('lan','蓝,婪,栏,拦,篮,阑,兰,澜,谰,揽,览,懒,缆,烂,滥,啉,岚,懔,漤,榄,斓,罱,镧,褴');
insert  into `lazy_pinyin`(`key`,`value`) values ('lang','琅,榔,狼,廊,郎,朗,浪,莨,蒗,啷,阆,锒,稂,螂');
insert  into `lazy_pinyin`(`key`,`value`) values ('lao','捞,劳,牢,老,佬,姥,酪,烙,涝,唠,崂,栳,铑,铹,痨,醪');
insert  into `lazy_pinyin`(`key`,`value`) values ('le','勒,乐,肋,仂,叻,嘞,泐,鳓');
insert  into `lazy_pinyin`(`key`,`value`) values ('lei','雷,镭,蕾,磊,累,儡,垒,擂,类,泪,羸,诔,荽,咧,漯,嫘,缧,檑,耒,酹');
insert  into `lazy_pinyin`(`key`,`value`) values ('ling','棱,冷,拎,玲,菱,零,龄,铃,伶,羚,凌,灵,陵,岭,领,另,令,酃,塄,苓,呤,囹,泠,绫,柃,棂,瓴,聆,蛉,翎,鲮');
insert  into `lazy_pinyin`(`key`,`value`) values ('leng','楞,愣');
insert  into `lazy_pinyin`(`key`,`value`) values ('li','厘,梨,犁,黎,篱,狸,离,漓,理,李,里,鲤,礼,莉,荔,吏,栗,丽,厉,励,砾,历,利,傈,例,俐,痢,立,粒,沥,隶,力,璃,哩,俪,俚,郦,坜,苈,莅,蓠,藜,捩,呖,唳,喱,猁,溧,澧,逦,娌,嫠,骊,缡,珞,枥,栎,轹,戾,砺,詈,罹,锂,鹂,疠,疬,蛎,蜊,蠡,笠,篥,粝,醴,跞,雳,鲡,鳢,黧');
insert  into `lazy_pinyin`(`key`,`value`) values ('lian','俩,联,莲,连,镰,廉,怜,涟,帘,敛,脸,链,恋,炼,练,挛,蔹,奁,潋,濂,娈,琏,楝,殓,臁,膦,裢,蠊,鲢');
insert  into `lazy_pinyin`(`key`,`value`) values ('liang','粮,凉,梁,粱,良,两,辆,量,晾,亮,谅,墚,椋,踉,靓,魉');
insert  into `lazy_pinyin`(`key`,`value`) values ('liao','撩,聊,僚,疗,燎,寥,辽,潦,了,撂,镣,廖,料,蓼,尥,嘹,獠,寮,缭,钌,鹩,耢');
insert  into `lazy_pinyin`(`key`,`value`) values ('lie','列,裂,烈,劣,猎,冽,埒,洌,趔,躐,鬣');
insert  into `lazy_pinyin`(`key`,`value`) values ('lin','琳,林,磷,霖,临,邻,鳞,淋,凛,赁,吝,蔺,嶙,廪,遴,檩,辚,瞵,粼,躏,麟');
insert  into `lazy_pinyin`(`key`,`value`) values ('liu','溜,琉,榴,硫,馏,留,刘,瘤,流,柳,六,抡,偻,蒌,泖,浏,遛,骝,绺,旒,熘,锍,镏,鹨,鎏');
insert  into `lazy_pinyin`(`key`,`value`) values ('long','龙,聋,咙,笼,窿,隆,垄,拢,陇,弄,垅,茏,泷,珑,栊,胧,砻,癃');
insert  into `lazy_pinyin`(`key`,`value`) values ('lou','楼,娄,搂,篓,漏,陋,喽,嵝,镂,瘘,耧,蝼,髅');
insert  into `lazy_pinyin`(`key`,`value`) values ('lu','芦,卢,颅,庐,炉,掳,卤,虏,鲁,麓,碌,露,路,赂,鹿,潞,禄,录,陆,戮,垆,摅,撸,噜,泸,渌,漉,璐,栌,橹,轳,辂,辘,氇,胪,镥,鸬,鹭,簏,舻,鲈');
insert  into `lazy_pinyin`(`key`,`value`) values ('lv','驴,吕,铝,侣,旅,履,屡,缕,虑,氯,律,率,滤,绿,捋,闾,榈,膂,稆,褛');
insert  into `lazy_pinyin`(`key`,`value`) values ('luan','峦,孪,滦,卵,乱,栾,鸾,銮');
insert  into `lazy_pinyin`(`key`,`value`) values ('lue','掠,略,锊');
insert  into `lazy_pinyin`(`key`,`value`) values ('lun','轮,伦,仑,沦,纶,论,囵');
insert  into `lazy_pinyin`(`key`,`value`) values ('luo','萝,螺,罗,逻,锣,箩,骡,裸,落,洛,骆,络,倮,荦,摞,猡,泺,椤,脶,镙,瘰,雒');
insert  into `lazy_pinyin`(`key`,`value`) values ('ma','妈,麻,玛,码,蚂,马,骂,嘛,吗,唛,犸,嬷,杩,麽');
insert  into `lazy_pinyin`(`key`,`value`) values ('mai','埋,买,麦,卖,迈,脉,劢,荬,咪,霾');
insert  into `lazy_pinyin`(`key`,`value`) values ('man','瞒,馒,蛮,满,蔓,曼,慢,漫,谩,墁,幔,缦,熳,镘,颟,螨,鳗,鞔');
insert  into `lazy_pinyin`(`key`,`value`) values ('mang','芒,茫,盲,忙,莽,邙,漭,朦,硭,蟒');
insert  into `lazy_pinyin`(`key`,`value`) values ('meng','氓,萌,蒙,檬,盟,锰,猛,梦,孟,勐,甍,瞢,懵,礞,虻,蜢,蠓,艋,艨,黾');
insert  into `lazy_pinyin`(`key`,`value`) values ('miao','猫,苗,描,瞄,藐,秒,渺,庙,妙,喵,邈,缈,缪,杪,淼,眇,鹋,蜱');
insert  into `lazy_pinyin`(`key`,`value`) values ('mao','茅,锚,毛,矛,铆,卯,茂,冒,帽,貌,贸,侔,袤,勖,茆,峁,瑁,昴,牦,耄,旄,懋,瞀,蛑,蝥,蟊,髦');
insert  into `lazy_pinyin`(`key`,`value`) values ('me','么');
insert  into `lazy_pinyin`(`key`,`value`) values ('mei','玫,枚,梅,酶,霉,煤,没,眉,媒,镁,每,美,昧,寐,妹,媚,坶,莓,嵋,猸,浼,湄,楣,镅,鹛,袂,魅');
insert  into `lazy_pinyin`(`key`,`value`) values ('men','门,闷,们,扪,玟,焖,懑,钔');
insert  into `lazy_pinyin`(`key`,`value`) values ('mi','眯,醚,靡,糜,迷,谜,弥,米,秘,觅,泌,蜜,密,幂,芈,冖,谧,蘼,嘧,猕,獯,汨,宓,弭,脒,敉,糸,縻,麋');
insert  into `lazy_pinyin`(`key`,`value`) values ('mian','棉,眠,绵,冕,免,勉,娩,缅,面,沔,湎,腼,眄');
insert  into `lazy_pinyin`(`key`,`value`) values ('mie','蔑,灭,咩,蠛,篾');
insert  into `lazy_pinyin`(`key`,`value`) values ('min','民,抿,皿,敏,悯,闽,苠,岷,闵,泯,珉');
insert  into `lazy_pinyin`(`key`,`value`) values ('ming','明,螟,鸣,铭,名,命,冥,茗,溟,暝,瞑,酩');
insert  into `lazy_pinyin`(`key`,`value`) values ('miu','谬');
insert  into `lazy_pinyin`(`key`,`value`) values ('mo','摸,摹,蘑,模,膜,磨,摩,魔,抹,末,莫,墨,默,沫,漠,寞,陌,谟,茉,蓦,馍,嫫,镆,秣,瘼,耱,蟆,貊,貘');
insert  into `lazy_pinyin`(`key`,`value`) values ('mou','谋,牟,某,厶,哞,婺,眸,鍪');
insert  into `lazy_pinyin`(`key`,`value`) values ('mu','拇,牡,亩,姆,母,墓,暮,幕,募,慕,木,目,睦,牧,穆,仫,苜,呒,沐,毪,钼');
insert  into `lazy_pinyin`(`key`,`value`) values ('na','拿,哪,呐,钠,那,娜,纳,内,捺,肭,镎,衲,箬');
insert  into `lazy_pinyin`(`key`,`value`) values ('nai','氖,乃,奶,耐,奈,鼐,艿,萘,柰');
insert  into `lazy_pinyin`(`key`,`value`) values ('nan','南,男,难,囊,喃,囡,楠,腩,蝻,赧');
insert  into `lazy_pinyin`(`key`,`value`) values ('nao','挠,脑,恼,闹,孬,垴,猱,瑙,硇,铙,蛲');
insert  into `lazy_pinyin`(`key`,`value`) values ('ne','淖,呢,讷');
insert  into `lazy_pinyin`(`key`,`value`) values ('nei','馁');
insert  into `lazy_pinyin`(`key`,`value`) values ('nen','嫩,能,枘,恁');
insert  into `lazy_pinyin`(`key`,`value`) values ('ni','妮,霓,倪,泥,尼,拟,你,匿,腻,逆,溺,伲,坭,猊,怩,滠,昵,旎,祢,慝,睨,铌,鲵');
insert  into `lazy_pinyin`(`key`,`value`) values ('nian','蔫,拈,年,碾,撵,捻,念,廿,辇,黏,鲇,鲶');
insert  into `lazy_pinyin`(`key`,`value`) values ('niang','娘,酿');
insert  into `lazy_pinyin`(`key`,`value`) values ('niao','鸟,尿,茑,嬲,脲,袅');
insert  into `lazy_pinyin`(`key`,`value`) values ('nie','捏,聂,孽,啮,镊,镍,涅,乜,陧,蘖,嗫,肀,颞,臬,蹑');
insert  into `lazy_pinyin`(`key`,`value`) values ('nin','您,柠');
insert  into `lazy_pinyin`(`key`,`value`) values ('ning','狞,凝,宁,拧,泞,佞,蓥,咛,甯,聍');
insert  into `lazy_pinyin`(`key`,`value`) values ('niu','牛,扭,钮,纽,狃,忸,妞,蚴');
insert  into `lazy_pinyin`(`key`,`value`) values ('nong','脓,浓,农,侬');
insert  into `lazy_pinyin`(`key`,`value`) values ('nu','奴,努,怒,呶,帑,弩,胬,孥,驽');
insert  into `lazy_pinyin`(`key`,`value`) values ('nv','女,恧,钕,衄');
insert  into `lazy_pinyin`(`key`,`value`) values ('nuan','暖');
insert  into `lazy_pinyin`(`key`,`value`) values ('nuenue','虐');
insert  into `lazy_pinyin`(`key`,`value`) values ('nue','疟,谑');
insert  into `lazy_pinyin`(`key`,`value`) values ('nuo','挪,懦,糯,诺,傩,搦,喏,锘');
insert  into `lazy_pinyin`(`key`,`value`) values ('ou','哦,欧,鸥,殴,藕,呕,偶,沤,怄,瓯,耦');
insert  into `lazy_pinyin`(`key`,`value`) values ('pa','啪,趴,爬,帕,怕,琶,葩,筢');
insert  into `lazy_pinyin`(`key`,`value`) values ('pai','拍,排,牌,徘,湃,派,俳,蒎');
insert  into `lazy_pinyin`(`key`,`value`) values ('pan','攀,潘,盘,磐,盼,畔,判,叛,爿,泮,袢,襻,蟠,蹒');
insert  into `lazy_pinyin`(`key`,`value`) values ('pang','乓,庞,旁,耪,胖,滂,逄');
insert  into `lazy_pinyin`(`key`,`value`) values ('pao','抛,咆,刨,炮,袍,跑,泡,匏,狍,庖,脬,疱');
insert  into `lazy_pinyin`(`key`,`value`) values ('pei','呸,胚,培,裴,赔,陪,配,佩,沛,掊,辔,帔,淠,旆,锫,醅,霈');
insert  into `lazy_pinyin`(`key`,`value`) values ('pen','喷,盆,湓');
insert  into `lazy_pinyin`(`key`,`value`) values ('peng','砰,抨,烹,澎,彭,蓬,棚,硼,篷,膨,朋,鹏,捧,碰,坯,堋,嘭,怦,蟛');
insert  into `lazy_pinyin`(`key`,`value`) values ('pi','砒,霹,批,披,劈,琵,毗,啤,脾,疲,皮,匹,痞,僻,屁,譬,丕,陴,邳,郫,圮,鼙,擗,噼,庀,媲,纰,枇,甓,睥,罴,铍,痦,癖,疋,蚍,貔');
insert  into `lazy_pinyin`(`key`,`value`) values ('pian','篇,偏,片,骗,谝,骈,犏,胼,褊,翩,蹁');
insert  into `lazy_pinyin`(`key`,`value`) values ('piao','飘,漂,瓢,票,剽,嘌,嫖,缥,殍,瞟,螵');
insert  into `lazy_pinyin`(`key`,`value`) values ('pie','撇,瞥,丿,苤,氕');
insert  into `lazy_pinyin`(`key`,`value`) values ('pin','拼,频,贫,品,聘,拚,姘,嫔,榀,牝,颦');
insert  into `lazy_pinyin`(`key`,`value`) values ('ping','乒,坪,苹,萍,平,凭,瓶,评,屏,俜,娉,枰,鲆');
insert  into `lazy_pinyin`(`key`,`value`) values ('po','坡,泼,颇,婆,破,魄,迫,粕,叵,鄱,溥,珀,钋,钷,皤,笸');
insert  into `lazy_pinyin`(`key`,`value`) values ('pou','剖,裒,踣');
insert  into `lazy_pinyin`(`key`,`value`) values ('pu','扑,铺,仆,莆,葡,菩,蒲,埔,朴,圃,普,浦,谱,曝,瀑,匍,噗,濮,璞,氆,镤,镨,蹼');
insert  into `lazy_pinyin`(`key`,`value`) values ('qi','期,欺,栖,戚,妻,七,凄,漆,柒,沏,其,棋,奇,歧,畦,崎,脐,齐,旗,祈,祁,骑,起,岂,乞,企,启,契,砌,器,气,迄,弃,汽,泣,讫,亟,亓,圻,芑,萋,葺,嘁,屺,岐,汔,淇,骐,绮,琪,琦,杞,桤,槭,欹,祺,憩,碛,蛴,蜞,綦,綮,趿,蹊,鳍,麒');
insert  into `lazy_pinyin`(`key`,`value`) values ('qia','掐,恰,洽,葜');
insert  into `lazy_pinyin`(`key`,`value`) values ('qian','牵,扦,钎,铅,千,迁,签,仟,谦,乾,黔,钱,钳,前,潜,遣,浅,谴,堑,嵌,欠,歉,佥,阡,芊,芡,荨,掮,岍,悭,慊,骞,搴,褰,缱,椠,肷,愆,钤,虔,箝');
insert  into `lazy_pinyin`(`key`,`value`) values ('qiang','枪,呛,腔,羌,墙,蔷,强,抢,嫱,樯,戗,炝,锖,锵,镪,襁,蜣,羟,跫,跄');
insert  into `lazy_pinyin`(`key`,`value`) values ('qiao','橇,锹,敲,悄,桥,瞧,乔,侨,巧,鞘,撬,翘,峭,俏,窍,劁,诮,谯,荞,愀,憔,缲,樵,毳,硗,跷,鞒');
insert  into `lazy_pinyin`(`key`,`value`) values ('qie','切,茄,且,怯,窃,郄,唼,惬,妾,挈,锲,箧');
insert  into `lazy_pinyin`(`key`,`value`) values ('qin','钦,侵,亲,秦,琴,勤,芹,擒,禽,寝,沁,芩,蓁,蕲,揿,吣,嗪,噙,溱,檎,螓,衾');
insert  into `lazy_pinyin`(`key`,`value`) values ('qing','青,轻,氢,倾,卿,清,擎,晴,氰,情,顷,请,庆,倩,苘,圊,檠,磬,蜻,罄,箐,謦,鲭,黥');
insert  into `lazy_pinyin`(`key`,`value`) values ('qiong','琼,穷,邛,茕,穹,筇,銎');
insert  into `lazy_pinyin`(`key`,`value`) values ('qiu','秋,丘,邱,球,求,囚,酋,泅,俅,氽,巯,艽,犰,湫,逑,遒,楸,赇,鸠,虬,蚯,蝤,裘,糗,鳅,鼽');
insert  into `lazy_pinyin`(`key`,`value`) values ('qu','趋,区,蛆,曲,躯,屈,驱,渠,取,娶,龋,趣,去,诎,劬,蕖,蘧,岖,衢,阒,璩,觑,氍,祛,磲,癯,蛐,蠼,麴,瞿,黢');
insert  into `lazy_pinyin`(`key`,`value`) values ('quan','圈,颧,权,醛,泉,全,痊,拳,犬,券,劝,诠,荃,獾,悛,绻,辁,畎,铨,蜷,筌,鬈');
insert  into `lazy_pinyin`(`key`,`value`) values ('que','缺,炔,瘸,却,鹊,榷,确,雀,阙,悫');
insert  into `lazy_pinyin`(`key`,`value`) values ('qun','裙,群,逡');
insert  into `lazy_pinyin`(`key`,`value`) values ('ran','然,燃,冉,染,苒,髯');
insert  into `lazy_pinyin`(`key`,`value`) values ('rang','瓤,壤,攘,嚷,让,禳,穰');
insert  into `lazy_pinyin`(`key`,`value`) values ('rao','饶,扰,绕,荛,娆,桡');
insert  into `lazy_pinyin`(`key`,`value`) values ('ruo','惹,若,弱');
insert  into `lazy_pinyin`(`key`,`value`) values ('re','热,偌');
insert  into `lazy_pinyin`(`key`,`value`) values ('ren','壬,仁,人,忍,韧,任,认,刃,妊,纫,仞,荏,葚,饪,轫,稔,衽');
insert  into `lazy_pinyin`(`key`,`value`) values ('reng','扔,仍');
insert  into `lazy_pinyin`(`key`,`value`) values ('ri','日');
insert  into `lazy_pinyin`(`key`,`value`) values ('rong','戎,茸,蓉,荣,融,熔,溶,容,绒,冗,嵘,狨,缛,榕,蝾');
insert  into `lazy_pinyin`(`key`,`value`) values ('rou','揉,柔,肉,糅,蹂,鞣');
insert  into `lazy_pinyin`(`key`,`value`) values ('ru','茹,蠕,儒,孺,如,辱,乳,汝,入,褥,蓐,薷,嚅,洳,溽,濡,铷,襦,颥');
insert  into `lazy_pinyin`(`key`,`value`) values ('ruan','软,阮,朊');
insert  into `lazy_pinyin`(`key`,`value`) values ('rui','蕊,瑞,锐,芮,蕤,睿,蚋');
insert  into `lazy_pinyin`(`key`,`value`) values ('run','闰,润');
insert  into `lazy_pinyin`(`key`,`value`) values ('sa','撒,洒,萨,卅,仨,挲,飒');
insert  into `lazy_pinyin`(`key`,`value`) values ('sai','腮,鳃,塞,赛,噻');
insert  into `lazy_pinyin`(`key`,`value`) values ('san','三,叁,伞,散,彡,馓,氵,毵,糁,霰');
insert  into `lazy_pinyin`(`key`,`value`) values ('sang','桑,嗓,丧,搡,磉,颡');
insert  into `lazy_pinyin`(`key`,`value`) values ('sao','搔,骚,扫,嫂,埽,臊,瘙,鳋');
insert  into `lazy_pinyin`(`key`,`value`) values ('se','瑟,色,涩,啬,铩,铯,穑');
insert  into `lazy_pinyin`(`key`,`value`) values ('sen','森');
insert  into `lazy_pinyin`(`key`,`value`) values ('seng','僧');
insert  into `lazy_pinyin`(`key`,`value`) values ('sha','莎,砂,杀,刹,沙,纱,傻,啥,煞,脎,歃,痧,裟,霎,鲨');
insert  into `lazy_pinyin`(`key`,`value`) values ('shai','筛,晒,酾');
insert  into `lazy_pinyin`(`key`,`value`) values ('shan','珊,苫,杉,山,删,煽,衫,闪,陕,擅,赡,膳,善,汕,扇,缮,剡,讪,鄯,埏,芟,潸,姗,骟,膻,钐,疝,蟮,舢,跚,鳝');
insert  into `lazy_pinyin`(`key`,`value`) values ('shang','墒,伤,商,赏,晌,上,尚,裳,垧,绱,殇,熵,觞');
insert  into `lazy_pinyin`(`key`,`value`) values ('shao','梢,捎,稍,烧,芍,勺,韶,少,哨,邵,绍,劭,苕,潲,蛸,笤,筲,艄');
insert  into `lazy_pinyin`(`key`,`value`) values ('she','奢,赊,蛇,舌,舍,赦,摄,射,慑,涉,社,设,厍,佘,猞,畲,麝');
insert  into `lazy_pinyin`(`key`,`value`) values ('shen','砷,申,呻,伸,身,深,娠,绅,神,沈,审,婶,甚,肾,慎,渗,诜,谂,吲,哂,渖,椹,矧,蜃');
insert  into `lazy_pinyin`(`key`,`value`) values ('sheng','声,生,甥,牲,升,绳,省,盛,剩,胜,圣,丞,渑,媵,眚,笙');
insert  into `lazy_pinyin`(`key`,`value`) values ('shi','师,失,狮,施,湿,诗,尸,虱,十,石,拾,时,什,食,蚀,实,识,史,矢,使,屎,驶,始,式,示,士,世,柿,事,拭,誓,逝,势,是,嗜,噬,适,仕,侍,释,饰,氏,市,恃,室,视,试,谥,埘,莳,蓍,弑,唑,饣,轼,耆,贳,炻,礻,铈,铊,螫,舐,筮,豕,鲥,鲺');
insert  into `lazy_pinyin`(`key`,`value`) values ('shou','收,手,首,守,寿,授,售,受,瘦,兽,扌,狩,绶,艏');
insert  into `lazy_pinyin`(`key`,`value`) values ('shu','蔬,枢,梳,殊,抒,输,叔,舒,淑,疏,书,赎,孰,熟,薯,暑,曙,署,蜀,黍,鼠,属,术,述,树,束,戍,竖,墅,庶,数,漱,恕,倏,塾,菽,忄,沭,涑,澍,姝,纾,毹,腧,殳,镯,秫,鹬');
insert  into `lazy_pinyin`(`key`,`value`) values ('shua','刷,耍,唰,涮');
insert  into `lazy_pinyin`(`key`,`value`) values ('shuai','摔,衰,甩,帅,蟀');
insert  into `lazy_pinyin`(`key`,`value`) values ('shuan','栓,拴,闩');
insert  into `lazy_pinyin`(`key`,`value`) values ('shuang','霜,双,爽,孀');
insert  into `lazy_pinyin`(`key`,`value`) values ('shui','谁,水,睡,税');
insert  into `lazy_pinyin`(`key`,`value`) values ('shun','吮,瞬,顺,舜,恂');
insert  into `lazy_pinyin`(`key`,`value`) values ('shuo','说,硕,朔,烁,蒴,搠,嗍,濯,妁,槊,铄');
insert  into `lazy_pinyin`(`key`,`value`) values ('si','斯,撕,嘶,思,私,司,丝,死,肆,寺,嗣,四,伺,似,饲,巳,厮,俟,兕,菥,咝,汜,泗,澌,姒,驷,缌,祀,祠,锶,鸶,耜,蛳,笥');
insert  into `lazy_pinyin`(`key`,`value`) values ('song','松,耸,怂,颂,送,宋,讼,诵,凇,菘,崧,嵩,忪,悚,淞,竦');
insert  into `lazy_pinyin`(`key`,`value`) values ('sou','搜,艘,擞,嗽,叟,嗖,嗾,馊,溲,飕,瞍,锼,螋');
insert  into `lazy_pinyin`(`key`,`value`) values ('su','苏,酥,俗,素,速,粟,僳,塑,溯,宿,诉,肃,夙,谡,蔌,嗉,愫,簌,觫,稣');
insert  into `lazy_pinyin`(`key`,`value`) values ('suan','酸,蒜,算');
insert  into `lazy_pinyin`(`key`,`value`) values ('sui','虽,隋,随,绥,髓,碎,岁,穗,遂,隧,祟,蓑,冫,谇,濉,邃,燧,眭,睢');
insert  into `lazy_pinyin`(`key`,`value`) values ('sun','孙,损,笋,荪,狲,飧,榫,跣,隼');
insert  into `lazy_pinyin`(`key`,`value`) values ('suo','梭,唆,缩,琐,索,锁,所,唢,嗦,娑,桫,睃,羧');
insert  into `lazy_pinyin`(`key`,`value`) values ('ta','塌,他,它,她,塔,獭,挞,蹋,踏,闼,溻,遢,榻,沓');
insert  into `lazy_pinyin`(`key`,`value`) values ('tai','胎,苔,抬,台,泰,酞,太,态,汰,邰,薹,肽,炱,钛,跆,鲐');
insert  into `lazy_pinyin`(`key`,`value`) values ('tan','坍,摊,贪,瘫,滩,坛,檀,痰,潭,谭,谈,坦,毯,袒,碳,探,叹,炭,郯,蕈,昙,钽,锬,覃');
insert  into `lazy_pinyin`(`key`,`value`) values ('tang','汤,塘,搪,堂,棠,膛,唐,糖,傥,饧,溏,瑭,铴,镗,耥,螗,螳,羰,醣');
insert  into `lazy_pinyin`(`key`,`value`) values ('thang','倘,躺,淌');
insert  into `lazy_pinyin`(`key`,`value`) values ('theng','趟,烫');
insert  into `lazy_pinyin`(`key`,`value`) values ('tao','掏,涛,滔,绦,萄,桃,逃,淘,陶,讨,套,挑,鼗,啕,韬,饕');
insert  into `lazy_pinyin`(`key`,`value`) values ('te','特');
insert  into `lazy_pinyin`(`key`,`value`) values ('teng','藤,腾,疼,誊,滕');
insert  into `lazy_pinyin`(`key`,`value`) values ('ti','梯,剔,踢,锑,提,题,蹄,啼,体,替,嚏,惕,涕,剃,屉,荑,悌,逖,绨,缇,鹈,裼,醍');
insert  into `lazy_pinyin`(`key`,`value`) values ('tian','天,添,填,田,甜,恬,舔,腆,掭,忝,阗,殄,畋,钿,蚺');
insert  into `lazy_pinyin`(`key`,`value`) values ('tiao','条,迢,眺,跳,佻,祧,铫,窕,龆,鲦');
insert  into `lazy_pinyin`(`key`,`value`) values ('tie','贴,铁,帖,萜,餮');
insert  into `lazy_pinyin`(`key`,`value`) values ('ting','厅,听,烃,汀,廷,停,亭,庭,挺,艇,莛,葶,婷,梃,蜓,霆');
insert  into `lazy_pinyin`(`key`,`value`) values ('tong','通,桐,酮,瞳,同,铜,彤,童,桶,捅,筒,统,痛,佟,僮,仝,茼,嗵,恸,潼,砼');
insert  into `lazy_pinyin`(`key`,`value`) values ('tou','偷,投,头,透,亠');
insert  into `lazy_pinyin`(`key`,`value`) values ('tu','凸,秃,突,图,徒,途,涂,屠,土,吐,兔,堍,荼,菟,钍,酴');
insert  into `lazy_pinyin`(`key`,`value`) values ('tuan','湍,团,疃');
insert  into `lazy_pinyin`(`key`,`value`) values ('tui','推,颓,腿,蜕,褪,退,忒,煺');
insert  into `lazy_pinyin`(`key`,`value`) values ('tun','吞,屯,臀,饨,暾,豚,窀');
insert  into `lazy_pinyin`(`key`,`value`) values ('tuo','拖,托,脱,鸵,陀,驮,驼,椭,妥,拓,唾,乇,佗,坨,庹,沱,柝,砣,箨,舄,跎,鼍');
insert  into `lazy_pinyin`(`key`,`value`) values ('wa','挖,哇,蛙,洼,娃,瓦,袜,佤,娲,腽');
insert  into `lazy_pinyin`(`key`,`value`) values ('wai','歪,外');
insert  into `lazy_pinyin`(`key`,`value`) values ('wan','豌,弯,湾,玩,顽,丸,烷,完,碗,挽,晚,皖,惋,宛,婉,万,腕,剜,芄,苋,菀,纨,绾,琬,脘,畹,蜿,箢');
insert  into `lazy_pinyin`(`key`,`value`) values ('wang','汪,王,亡,枉,网,往,旺,望,忘,妄,罔,尢,惘,辋,魍');
insert  into `lazy_pinyin`(`key`,`value`) values ('wei','威,巍,微,危,韦,违,桅,围,唯,惟,为,潍,维,苇,萎,委,伟,伪,尾,纬,未,蔚,味,畏,胃,喂,魏,位,渭,谓,尉,慰,卫,倭,偎,诿,隈,葳,薇,帏,帷,崴,嵬,猥,猬,闱,沩,洧,涠,逶,娓,玮,韪,軎,炜,煨,熨,痿,艉,鲔');
insert  into `lazy_pinyin`(`key`,`value`) values ('wen','瘟,温,蚊,文,闻,纹,吻,稳,紊,问,刎,愠,阌,汶,璺,韫,殁,雯');
insert  into `lazy_pinyin`(`key`,`value`) values ('weng','嗡,翁,瓮,蓊,蕹');
insert  into `lazy_pinyin`(`key`,`value`) values ('wo','挝,蜗,涡,窝,我,斡,卧,握,沃,莴,幄,渥,杌,肟,龌');
insert  into `lazy_pinyin`(`key`,`value`) values ('wu','巫,呜,钨,乌,污,诬,屋,无,芜,梧,吾,吴,毋,武,五,捂,午,舞,伍,侮,坞,戊,雾,晤,物,勿,务,悟,误,兀,仵,阢,邬,圬,芴,庑,怃,忤,浯,寤,迕,妩,骛,牾,焐,鹉,鹜,蜈,鋈,鼯');
insert  into `lazy_pinyin`(`key`,`value`) values ('xi','昔,熙,析,西,硒,矽,晰,嘻,吸,锡,牺,稀,息,希,悉,膝,夕,惜,熄,烯,溪,汐,犀,檄,袭,席,习,媳,喜,铣,洗,系,隙,戏,细,僖,兮,隰,郗,茜,葸,蓰,奚,唏,徙,饩,阋,浠,淅,屣,嬉,玺,樨,曦,觋,欷,熹,禊,禧,钸,皙,穸,蜥,蟋,舾,羲,粞,翕,醯,鼷');
insert  into `lazy_pinyin`(`key`,`value`) values ('xia','瞎,虾,匣,霞,辖,暇,峡,侠,狭,下,厦,夏,吓,掀,葭,嗄,狎,遐,瑕,硖,瘕,罅,黠');
insert  into `lazy_pinyin`(`key`,`value`) values ('xian','锨,先,仙,鲜,纤,咸,贤,衔,舷,闲,涎,弦,嫌,显,险,现,献,县,腺,馅,羡,宪,陷,限,线,冼,藓,岘,猃,暹,娴,氙,祆,鹇,痫,蚬,筅,籼,酰,跹');
insert  into `lazy_pinyin`(`key`,`value`) values ('xiang','相,厢,镶,香,箱,襄,湘,乡,翔,祥,详,想,响,享,项,巷,橡,像,向,象,芗,葙,饷,庠,骧,缃,蟓,鲞,飨');
insert  into `lazy_pinyin`(`key`,`value`) values ('xiao','萧,硝,霄,削,哮,嚣,销,消,宵,淆,晓,小,孝,校,肖,啸,笑,效,哓,咻,崤,潇,逍,骁,绡,枭,枵,筱,箫,魈');
insert  into `lazy_pinyin`(`key`,`value`) values ('xie','楔,些,歇,蝎,鞋,协,挟,携,邪,斜,胁,谐,写,械,卸,蟹,懈,泄,泻,谢,屑,偕,亵,勰,燮,薤,撷,廨,瀣,邂,绁,缬,榭,榍,歙,躞');
insert  into `lazy_pinyin`(`key`,`value`) values ('xin','薪,芯,锌,欣,辛,新,忻,心,信,衅,囟,馨,莘,歆,铽,鑫');
insert  into `lazy_pinyin`(`key`,`value`) values ('xing','星,腥,猩,惺,兴,刑,型,形,邢,行,醒,幸,杏,性,姓,陉,荇,荥,擤,悻,硎');
insert  into `lazy_pinyin`(`key`,`value`) values ('xiong','兄,凶,胸,匈,汹,雄,熊,芎');
insert  into `lazy_pinyin`(`key`,`value`) values ('xiu','休,修,羞,朽,嗅,锈,秀,袖,绣,莠,岫,馐,庥,鸺,貅,髹');
insert  into `lazy_pinyin`(`key`,`value`) values ('xu','墟,戌,需,虚,嘘,须,徐,许,蓄,酗,叙,旭,序,畜,恤,絮,婿,绪,续,讴,诩,圩,蓿,怵,洫,溆,顼,栩,煦,砉,盱,胥,糈,醑');
insert  into `lazy_pinyin`(`key`,`value`) values ('xuan','轩,喧,宣,悬,旋,玄,选,癣,眩,绚,儇,谖,萱,揎,馔,泫,洵,渲,漩,璇,楦,暄,炫,煊,碹,铉,镟,痃');
insert  into `lazy_pinyin`(`key`,`value`) values ('xue','靴,薛,学,穴,雪,血,噱,泶,鳕');
insert  into `lazy_pinyin`(`key`,`value`) values ('xun','勋,熏,循,旬,询,寻,驯,巡,殉,汛,训,讯,逊,迅,巽,埙,荀,薰,峋,徇,浔,曛,窨,醺,鲟');
insert  into `lazy_pinyin`(`key`,`value`) values ('ya','压,押,鸦,鸭,呀,丫,芽,牙,蚜,崖,衙,涯,雅,哑,亚,讶,伢,揠,吖,岈,迓,娅,琊,桠,氩,砑,睚,痖');
insert  into `lazy_pinyin`(`key`,`value`) values ('yan','焉,咽,阉,烟,淹,盐,严,研,蜒,岩,延,言,颜,阎,炎,沿,奄,掩,眼,衍,演,艳,堰,燕,厌,砚,雁,唁,彦,焰,宴,谚,验,厣,靥,赝,俨,偃,兖,讠,谳,郾,鄢,芫,菸,崦,恹,闫,阏,洇,湮,滟,妍,嫣,琰,晏,胭,腌,焱,罨,筵,酽,魇,餍,鼹');
insert  into `lazy_pinyin`(`key`,`value`) values ('yang','殃,央,鸯,秧,杨,扬,佯,疡,羊,洋,阳,氧,仰,痒,养,样,漾,徉,怏,泱,炀,烊,恙,蛘,鞅');
insert  into `lazy_pinyin`(`key`,`value`) values ('yao','邀,腰,妖,瑶,摇,尧,遥,窑,谣,姚,咬,舀,药,要,耀,夭,爻,吆,崾,徭,瀹,幺,珧,杳,曜,肴,鹞,窈,繇,鳐');
insert  into `lazy_pinyin`(`key`,`value`) values ('ye','椰,噎,耶,爷,野,冶,也,页,掖,业,叶,曳,腋,夜,液,谒,邺,揶,馀,晔,烨,铘');
insert  into `lazy_pinyin`(`key`,`value`) values ('yi','一,壹,医,揖,铱,依,伊,衣,颐,夷,遗,移,仪,胰,疑,沂,宜,姨,彝,椅,蚁,倚,已,乙,矣,以,艺,抑,易,邑,屹,亿,役,臆,逸,肄,疫,亦,裔,意,毅,忆,义,益,溢,诣,议,谊,译,异,翼,翌,绎,刈,劓,佾,诒,圪,圯,埸,懿,苡,薏,弈,奕,挹,弋,呓,咦,咿,噫,峄,嶷,猗,饴,怿,怡,悒,漪,迤,驿,缢,殪,贻,旖,熠,钇,镒,镱,痍,瘗,癔,翊,衤,蜴,舣,羿,翳,酏,黟');
insert  into `lazy_pinyin`(`key`,`value`) values ('yin','茵,荫,因,殷,音,阴,姻,吟,银,淫,寅,饮,尹,引,隐,印,胤,鄞,堙,茚,喑,狺,夤,氤,铟,瘾,蚓,霪,龈');
insert  into `lazy_pinyin`(`key`,`value`) values ('ying','英,樱,婴,鹰,应,缨,莹,萤,营,荧,蝇,迎,赢,盈,影,颖,硬,映,嬴,郢,茔,莺,萦,撄,嘤,膺,滢,潆,瀛,瑛,璎,楹,鹦,瘿,颍,罂');
insert  into `lazy_pinyin`(`key`,`value`) values ('yo','哟,唷');
insert  into `lazy_pinyin`(`key`,`value`) values ('yong','拥,佣,臃,痈,庸,雍,踊,蛹,咏,泳,涌,永,恿,勇,用,俑,壅,墉,慵,邕,镛,甬,鳙,饔');
insert  into `lazy_pinyin`(`key`,`value`) values ('you','幽,优,悠,忧,尤,由,邮,铀,犹,油,游,酉,有,友,右,佑,釉,诱,又,幼,卣,攸,侑,莸,呦,囿,宥,柚,猷,牖,铕,疣,蝣,鱿,黝,鼬');
insert  into `lazy_pinyin`(`key`,`value`) values ('yu','迂,淤,于,盂,榆,虞,愚,舆,余,俞,逾,鱼,愉,渝,渔,隅,予,娱,雨,与,屿,禹,宇,语,羽,玉,域,芋,郁,吁,遇,喻,峪,御,愈,欲,狱,育,誉,浴,寓,裕,预,豫,驭,禺,毓,伛,俣,谀,谕,萸,蓣,揄,喁,圄,圉,嵛,狳,饫,庾,阈,妪,妤,纡,瑜,昱,觎,腴,欤,於,煜,燠,聿,钰,鹆,瘐,瘀,窳,蝓,竽,舁,雩,龉');
insert  into `lazy_pinyin`(`key`,`value`) values ('yuan','鸳,渊,冤,元,垣,袁,原,援,辕,园,员,圆,猿,源,缘,远,苑,愿,怨,院,塬,沅,媛,瑗,橼,爰,眢,鸢,螈,鼋');
insert  into `lazy_pinyin`(`key`,`value`) values ('yue','曰,约,越,跃,钥,岳,粤,月,悦,阅,龠,樾,刖,钺');
insert  into `lazy_pinyin`(`key`,`value`) values ('yun','耘,云,郧,匀,陨,允,运,蕴,酝,晕,韵,孕,郓,芸,狁,恽,纭,殒,昀,氲');
insert  into `lazy_pinyin`(`key`,`value`) values ('za','匝,砸,杂,拶,咂');
insert  into `lazy_pinyin`(`key`,`value`) values ('zai','栽,哉,灾,宰,载,再,在,咱,崽,甾');
insert  into `lazy_pinyin`(`key`,`value`) values ('zan','攒,暂,赞,瓒,昝,簪,糌,趱,錾');
insert  into `lazy_pinyin`(`key`,`value`) values ('zang','赃,脏,葬,奘,戕,臧');
insert  into `lazy_pinyin`(`key`,`value`) values ('zao','遭,糟,凿,藻,枣,早,澡,蚤,躁,噪,造,皂,灶,燥,唣,缫');
insert  into `lazy_pinyin`(`key`,`value`) values ('ze','责,择,则,泽,仄,赜,啧,迮,昃,笮,箦,舴');
insert  into `lazy_pinyin`(`key`,`value`) values ('zei','贼');
insert  into `lazy_pinyin`(`key`,`value`) values ('zen','怎,谮');
insert  into `lazy_pinyin`(`key`,`value`) values ('zeng','增,憎,曾,赠,缯,甑,罾,锃');
insert  into `lazy_pinyin`(`key`,`value`) values ('zha','扎,喳,渣,札,轧,铡,闸,眨,栅,榨,咋,乍,炸,诈,揸,吒,咤,哳,怍,砟,痄,蚱,齄');
insert  into `lazy_pinyin`(`key`,`value`) values ('zhai','摘,斋,宅,窄,债,寨,砦');
insert  into `lazy_pinyin`(`key`,`value`) values ('zhan','瞻,毡,詹,粘,沾,盏,斩,辗,崭,展,蘸,栈,占,战,站,湛,绽,谵,搌,旃');
insert  into `lazy_pinyin`(`key`,`value`) values ('zhang','樟,章,彰,漳,张,掌,涨,杖,丈,帐,账,仗,胀,瘴,障,仉,鄣,幛,嶂,獐,嫜,璋,蟑');
insert  into `lazy_pinyin`(`key`,`value`) values ('zhao','招,昭,找,沼,赵,照,罩,兆,肇,召,爪,诏,棹,钊,笊');
insert  into `lazy_pinyin`(`key`,`value`) values ('zhe','遮,折,哲,蛰,辙,者,锗,蔗,这,浙,谪,陬,柘,辄,磔,鹧,褚,蜇,赭');
insert  into `lazy_pinyin`(`key`,`value`) values ('zhen','珍,斟,真,甄,砧,臻,贞,针,侦,枕,疹,诊,震,振,镇,阵,缜,桢,榛,轸,赈,胗,朕,祯,畛,鸩');
insert  into `lazy_pinyin`(`key`,`value`) values ('zheng','蒸,挣,睁,征,狰,争,怔,整,拯,正,政,帧,症,郑,证,诤,峥,钲,铮,筝');
insert  into `lazy_pinyin`(`key`,`value`) values ('zhi','芝,枝,支,吱,蜘,知,肢,脂,汁,之,织,职,直,植,殖,执,值,侄,址,指,止,趾,只,旨,纸,志,挚,掷,至,致,置,帜,峙,制,智,秩,稚,质,炙,痔,滞,治,窒,卮,陟,郅,埴,芷,摭,帙,忮,彘,咫,骘,栉,枳,栀,桎,轵,轾,攴,贽,膣,祉,祗,黹,雉,鸷,痣,蛭,絷,酯,跖,踬,踯,豸,觯');
insert  into `lazy_pinyin`(`key`,`value`) values ('zhong','中,盅,忠,钟,衷,终,种,肿,重,仲,众,冢,锺,螽,舂,舯,踵');
insert  into `lazy_pinyin`(`key`,`value`) values ('zhou','舟,周,州,洲,诌,粥,轴,肘,帚,咒,皱,宙,昼,骤,啄,着,倜,诹,荮,鬻,纣,胄,碡,籀,舳,酎,鲷');
insert  into `lazy_pinyin`(`key`,`value`) values ('zhu','珠,株,蛛,朱,猪,诸,诛,逐,竹,烛,煮,拄,瞩,嘱,主,著,柱,助,蛀,贮,铸,筑,住,注,祝,驻,伫,侏,邾,苎,茱,洙,渚,潴,驺,杼,槠,橥,炷,铢,疰,瘃,蚰,竺,箸,翥,躅,麈');
insert  into `lazy_pinyin`(`key`,`value`) values ('zhua','抓');
insert  into `lazy_pinyin`(`key`,`value`) values ('zhuai','拽');
insert  into `lazy_pinyin`(`key`,`value`) values ('zhuan','专,砖,转,撰,赚,篆,抟,啭,颛');
insert  into `lazy_pinyin`(`key`,`value`) values ('zhuang','桩,庄,装,妆,撞,壮,状,丬');
insert  into `lazy_pinyin`(`key`,`value`) values ('zhui','椎,锥,追,赘,坠,缀,萑,骓,缒');
insert  into `lazy_pinyin`(`key`,`value`) values ('zhun','谆,准');
insert  into `lazy_pinyin`(`key`,`value`) values ('zhuo','捉,拙,卓,桌,琢,茁,酌,灼,浊,倬,诼,廴,蕞,擢,啜,浞,涿,杓,焯,禚,斫');
insert  into `lazy_pinyin`(`key`,`value`) values ('zi','兹,咨,资,姿,滋,淄,孜,紫,仔,籽,滓,子,自,渍,字,谘,嵫,姊,孳,缁,梓,辎,赀,恣,眦,锱,秭,耔,笫,粢,觜,訾,鲻,髭');
insert  into `lazy_pinyin`(`key`,`value`) values ('zong','鬃,棕,踪,宗,综,总,纵,腙,粽');
insert  into `lazy_pinyin`(`key`,`value`) values ('zou','邹,走,奏,揍,鄹,鲰');
insert  into `lazy_pinyin`(`key`,`value`) values ('zu','租,足,卒,族,祖,诅,阻,组,俎,菹,啐,徂,驵,蹴');
insert  into `lazy_pinyin`(`key`,`value`) values ('zuan','钻,纂,攥,缵');
insert  into `lazy_pinyin`(`key`,`value`) values ('zui','嘴,醉,最,罪');
insert  into `lazy_pinyin`(`key`,`value`) values ('zun','尊,遵,撙,樽,鳟');
insert  into `lazy_pinyin`(`key`,`value`) values ('zuo','昨,左,佐,柞,做,作,坐,座,阝,阼,胙,祚,酢');
insert  into `lazy_pinyin`(`key`,`value`) values ('cou','薮,楱,辏,腠');
insert  into `lazy_pinyin`(`key`,`value`) values ('nang','攮,哝,囔,馕,曩');
insert  into `lazy_pinyin`(`key`,`value`) values ('o','喔');
insert  into `lazy_pinyin`(`key`,`value`) values ('dia','嗲');
insert  into `lazy_pinyin`(`key`,`value`) values ('chuai','嘬,膪,踹');
insert  into `lazy_pinyin`(`key`,`value`) values ('cen','岑,涔');
insert  into `lazy_pinyin`(`key`,`value`) values ('diu','铥');
insert  into `lazy_pinyin`(`key`,`value`) values ('nou','耨');
insert  into `lazy_pinyin`(`key`,`value`) values ('fou','缶');
insert  into `lazy_pinyin`(`key`,`value`) values ('bia','髟');

/*Table structure for table `lazy_post` */

DROP TABLE IF EXISTS `lazy_post`;

CREATE TABLE `lazy_post` (
  `postid` bigint(20) unsigned NOT NULL auto_increment,
  `model` varchar(50) NOT NULL,
  `author` bigint(20) NOT NULL default '0',
  `path` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` longtext,
  `passed` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`postid`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Data for the table `lazy_post` */

insert  into `lazy_post`(`postid`,`model`,`author`,`path`,`title`,`content`,`passed`) values (1,'zh-CN:article',1,'%ID.html','sdfsf','dsdsdf',0);

/*Table structure for table `lazy_post_meta` */

DROP TABLE IF EXISTS `lazy_post_meta`;

CREATE TABLE `lazy_post_meta` (
  `metaid` bigint(20) unsigned NOT NULL auto_increment,
  `postid` bigint(20) unsigned NOT NULL,
  `key` varchar(50) NOT NULL,
  `value` longtext,
  `type` varchar(20) NOT NULL,
  PRIMARY KEY  (`metaid`),
  UNIQUE KEY `postid_key_idx` (`postid`,`key`),
  KEY `postid` (`postid`),
  KEY `key` (`key`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Data for the table `lazy_post_meta` */

insert  into `lazy_post_meta`(`metaid`,`postid`,`key`,`value`,`type`) values (1,1,'from','网络','string');

/*Table structure for table `lazy_term` */

DROP TABLE IF EXISTS `lazy_term`;

CREATE TABLE `lazy_term` (
  `termid` bigint(20) unsigned NOT NULL auto_increment,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY  (`termid`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

/*Data for the table `lazy_term` */

insert  into `lazy_term`(`termid`,`name`) values (1,'新闻');
insert  into `lazy_term`(`termid`,`name`) values (2,'地方新闻');
insert  into `lazy_term`(`termid`,`name`) values (3,'社会新闻');

/*Table structure for table `lazy_term_relation` */

DROP TABLE IF EXISTS `lazy_term_relation`;

CREATE TABLE `lazy_term_relation` (
  `objectid` bigint(20) unsigned NOT NULL,
  `taxonomyid` bigint(20) unsigned NOT NULL,
  `order` bigint(20) unsigned NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `lazy_term_relation` */

insert  into `lazy_term_relation`(`objectid`,`taxonomyid`,`order`) values (1,1,0);
insert  into `lazy_term_relation`(`objectid`,`taxonomyid`,`order`) values (1,2,0);

/*Table structure for table `lazy_term_taxonomy` */

DROP TABLE IF EXISTS `lazy_term_taxonomy`;

CREATE TABLE `lazy_term_taxonomy` (
  `taxonomyid` bigint(20) unsigned NOT NULL auto_increment,
  `termid` bigint(20) unsigned NOT NULL,
  `type` varchar(50) NOT NULL default 'category',
  `parent` bigint(20) NOT NULL default '0',
  `count` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`taxonomyid`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

/*Data for the table `lazy_term_taxonomy` */

insert  into `lazy_term_taxonomy`(`taxonomyid`,`termid`,`type`,`parent`,`count`) values (1,1,'category',0,0);
insert  into `lazy_term_taxonomy`(`taxonomyid`,`termid`,`type`,`parent`,`count`) values (2,2,'category',1,0);
insert  into `lazy_term_taxonomy`(`taxonomyid`,`termid`,`type`,`parent`,`count`) values (3,3,'category',2,0);

/*Table structure for table `lazy_term_taxonomy_meta` */

DROP TABLE IF EXISTS `lazy_term_taxonomy_meta`;

CREATE TABLE `lazy_term_taxonomy_meta` (
  `metaid` bigint(20) unsigned NOT NULL auto_increment,
  `taxonomyid` bigint(20) unsigned NOT NULL,
  `key` varchar(50) default NULL,
  `value` longtext,
  `type` varchar(20) default NULL,
  PRIMARY KEY  (`metaid`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

/*Data for the table `lazy_term_taxonomy_meta` */

insert  into `lazy_term_taxonomy_meta`(`metaid`,`taxonomyid`,`key`,`value`,`type`) values (1,1,'path','%PY','string');
insert  into `lazy_term_taxonomy_meta`(`metaid`,`taxonomyid`,`key`,`value`,`type`) values (2,1,'list','','string');
insert  into `lazy_term_taxonomy_meta`(`metaid`,`taxonomyid`,`key`,`value`,`type`) values (3,2,'path','%PY','string');
insert  into `lazy_term_taxonomy_meta`(`metaid`,`taxonomyid`,`key`,`value`,`type`) values (4,2,'list','','string');
insert  into `lazy_term_taxonomy_meta`(`metaid`,`taxonomyid`,`key`,`value`,`type`) values (5,3,'path','%PY','string');
insert  into `lazy_term_taxonomy_meta`(`metaid`,`taxonomyid`,`key`,`value`,`type`) values (6,3,'list','','string');

/*Table structure for table `lazy_user` */

DROP TABLE IF EXISTS `lazy_user`;

CREATE TABLE `lazy_user` (
  `userid` bigint(20) unsigned NOT NULL auto_increment,
  `name` varchar(50) NOT NULL,
  `pass` varchar(50) NOT NULL,
  `mail` varchar(150) NOT NULL,
  `status` tinyint(1) default '0',
  `registered` timestamp NULL default '0000-00-00 00:00:00',
  `authcode` varchar(36) default NULL,
  PRIMARY KEY  (`userid`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

/*Data for the table `lazy_user` */

insert  into `lazy_user`(`userid`,`name`,`pass`,`mail`,`status`,`registered`,`authcode`) values (1,'admin','1dec98cdd2bb5dcfddaf68c261212715','my@lukin.cn',0,'0000-00-00 00:00:00','7D49FF4B-1484-59D2-2647-11EAFA047747');
insert  into `lazy_user`(`userid`,`name`,`pass`,`mail`,`status`,`registered`,`authcode`) values (2,'Lukin','655ae2801769868fbd093adda70c4deb','mmmm@sss.com',0,'2010-05-06 21:13:21','BB548E7C-80DA-373F-17E1-DC2A74CC41E4');

/*Table structure for table `lazy_user_meta` */

DROP TABLE IF EXISTS `lazy_user_meta`;

CREATE TABLE `lazy_user_meta` (
  `metaid` bigint(20) unsigned NOT NULL auto_increment,
  `userid` bigint(20) unsigned NOT NULL default '0',
  `key` varchar(50) NOT NULL,
  `value` longtext,
  `type` varchar(20) NOT NULL,
  PRIMARY KEY  (`metaid`),
  KEY `userid` (`userid`),
  KEY `key` (`key`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

/*Data for the table `lazy_user_meta` */

insert  into `lazy_user_meta`(`metaid`,`userid`,`key`,`value`,`type`) values (1,1,'Administrator','Yes','string');
insert  into `lazy_user_meta`(`metaid`,`userid`,`key`,`value`,`type`) values (2,1,'roles','a:16:{i:0;s:10:\"categories\";i:1;s:8:\"post-new\";i:2;s:9:\"post-list\";i:3;s:9:\"post-edit\";i:4;s:8:\"post-del\";i:5;s:10:\"model-list\";i:6;s:9:\"model-new\";i:7;s:10:\"model-edit\";i:8;s:12:\"model-delete\";i:9;s:12:\"model-import\";i:10;s:12:\"model-export\";i:11;s:12:\"model-fields\";i:12;s:9:\"user-list\";i:13;s:8:\"user-new\";i:14;s:9:\"user-edit\";i:15;s:11:\"user-delete\";}','array');
insert  into `lazy_user_meta`(`metaid`,`userid`,`key`,`value`,`type`) values (3,1,'username','admin','string');
insert  into `lazy_user_meta`(`metaid`,`userid`,`key`,`value`,`type`) values (4,1,'url','http://lukin.net','string');
insert  into `lazy_user_meta`(`metaid`,`userid`,`key`,`value`,`type`) values (5,1,'nickname','Lukin','string');
insert  into `lazy_user_meta`(`metaid`,`userid`,`key`,`value`,`type`) values (6,1,'description','','string');
insert  into `lazy_user_meta`(`metaid`,`userid`,`key`,`value`,`type`) values (7,2,'url','s','string');
insert  into `lazy_user_meta`(`metaid`,`userid`,`key`,`value`,`type`) values (8,2,'roles','a:16:{i:0;s:10:\"categories\";i:1;s:8:\"post-new\";i:2;s:9:\"post-list\";i:3;s:9:\"post-edit\";i:4;s:8:\"post-del\";i:5;s:10:\"model-list\";i:6;s:9:\"model-new\";i:7;s:10:\"model-edit\";i:8;s:12:\"model-delete\";i:9;s:12:\"model-import\";i:10;s:12:\"model-export\";i:11;s:12:\"model-fields\";i:12;s:9:\"user-list\";i:13;s:8:\"user-new\";i:14;s:9:\"user-edit\";i:15;s:11:\"user-delete\";}','array');
insert  into `lazy_user_meta`(`metaid`,`userid`,`key`,`value`,`type`) values (9,2,'nickname','Lukin','string');
insert  into `lazy_user_meta`(`metaid`,`userid`,`key`,`value`,`type`) values (10,2,'Administrator','Yes','string');
insert  into `lazy_user_meta`(`metaid`,`userid`,`key`,`value`,`type`) values (11,2,'description','','string');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;