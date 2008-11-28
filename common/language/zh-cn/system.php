<?php
/**
 * +---------------------------------------------------------------------------+
 * | LL                             LLLL   LL     L   LLLL                 LL  |
 * | LL                            LL   L  LLL   LL  LL   L               LLL  |
 * | LL      LLLL   LLLLL  LL  LL  LL      LLLL LLL  LL          LL  LL    LL  |
 * | LL         LL     LL  LL  LL  LL      L LLL LL  LLLLL       LL  LL    LL  |
 * | LL      LLLLL    LL    LLLL   LL      L  L  LL   LLLLL      LL  LL    LL  |
 * | LL     LL  LL   LL     LLLL   LL      L     LL      LL       LLLL     LL  |
 * | LL     LL  LL  LL       LL    LL   L  L     LL  L   LL       LLLL     LL  |
 * | LLLLLL  LLLLL  LLLLL    LL     LLLL   L     LL   LLLL         LL     LLLL |
 * |                        LL                                                 |
 * |                        LL                                                 |
 * +---------------------------------------------------------------------------+
 * | Copyright (C) 2007-2008 LazyCMS.net All rights reserved.                  |
 * +---------------------------------------------------------------------------+
 * | LazyCMS is free software. This version use Apache License 2.0             |
 * | See LICENSE.txt for copyright notices and details.                        |
 * +---------------------------------------------------------------------------+
 */
defined('COM_PATH') or die('Restricted access!');
/**
 * 系统语言包
 */
return array(
    /* 公用语言 */
    'ON'    => '√',
    'OFF'   => '×',
    'Help'  => '帮助',
    'Save'  => '保存',
    'Reset' => '重置',
    'Submit'    => '提交',
    'Default'   => '默认',
    'Delete'    => '删除',
    'Manage'    => '管理',
    'Logout'    => '退出登录',
    'Preview'   => '预览网站',
    'Select all'    => '全选',
    'Reset select'  => '反选',
    
    /* Error */
    'Error invalid' => '您输入的参数有误！',
    
    /* JavaScript确认 */
    'Confirm clear' => '确定要删除吗？',
    'Confirm logout' => '确定要退出吗？',
    
    /* 页面异常 */
    'Back'      => '返回',
    'Back home' => '回首页',
    'Try again' => '重试',
    'System error'      => '系统发生错误',
    'Error message'     => '错误信息',
    'Error position'    => '错误位置',
    
    /* 数据库异常 */
    'Parse DSN error'   => '数据库连接字符串，设置错误。',
    'Database no link'  => '数据库链接出错，请检查数据库设置！',
    'No select Database'    => '没有找到指定的数据库！',
    'Database no extension' => '-_-!!请先开启%s扩展支持！',
    'Database version lower'    => '%s数据库版本低于%s，请升级数据库版本！',
    
    /* 上传错误信息 */
    'Upload error 0' => 'POST值超过了 post_max_size(%s)',
    'Upload error 1' => '上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值',
    'Upload error 2' => '上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值',
    'Upload error 3' => '文件只有部分被上传',
    'Upload error 4' => '没有文件被上传',
    'Upload error 5' => '上传的文件太大',
    'Upload error 6' => '找不到临时文件夹',
    'Upload error 7' => '文件写入失败',
    'Upload error 8' => '上传的文件类型不允许',
    'Upload error 9' => '非法提交',
    
    /* 其他 */
    'No Main'       => '没有定义默认函数：lazy_main()',
    'No function'   => '执行的函数不存在：%s<br/>请在 %s 定义一个 %s 函数',
    'Auto keywords' => '自动获取关键词',
    'Last Version'  => '最新版本',
    
    /* 登录页面 */
    'Login title'    => '管理后台登录',
    'Login name'     => '用户名',
    'Login pass'     => '密&nbsp; &nbsp;码',
    'Login language' => '语&nbsp; &nbsp;言',
    'Login submit'   => '登录',
    'Login description' => '<p>请使用有效的用户名和密码登录后台。</p><p>运行环境：PHP 4.3.3+、MySQL 4.1+</p><p><a href="../">返回网站首页</a></p>',
    'Cookie expire'     => '有效期',
    'Cookie expire process'   => '浏览器进程',
    'Cookie expire 1 hour'    => '一小时',
    'Cookie expire 1 day'     => '一天',
    'Cookie expire 1 week'    => '一周',
    'Cookie expire 1 month'   => '一个月',
    'Cookie expire permanent' => '永久',
    
    /* 登录错误验证 */
    'Login check name'  => '管理员名称不能为空或过长',
    'Login check pass'  => '管理员密码不能为空或过长',
    
    /* 系统管理 */
    'Cpanel'     => '控制面板',
    'Webftp'     => '文件管理',
    'Modules'    => '安装卸载',
    'Settings'   => '系统设置',
    'Official Website'  => '官方网站',
    'Support Forums'    => '支持论坛',
    'System manage'     => '系统管理',
    'System config'     => '配置信息',
    
    /* System info */
    'System info'   => '系统信息',
    'Server OS'     => '服务器系统',
    'WebServer'     => '服务器解译引擎',
    'User agent'    => '浏览器类型',
    'GD version'    => 'GD版本',
    'PHP version'   => 'PHP版本',
    'PHP SAPI name' => 'PHP接口',
    'System version'    => 'LazyCMS版本',
    'Database version'  => '数据库版本',
    
    
    /* PHP Settings */
    'PHP Settings'  => 'PHP设置',
    'PHP zlib'      => 'Zlib开启',
    'PHP iconv'     => 'iconv可用',
    'PHP mbstring'  => 'mbstring启用',
    'PHP safe mode' => '安全模式',
    'PHP file uploads'      => '文件上传',
    'PHP open base dir'     => '开放根目录',
    'PHP post max size'     => '最大POST数据',
    'PHP display errors'    => '显示错误',
    'PHP allow url fopen'       => '打开远程连接',
    'PHP magic quotes gpc'      => '魔术引用',
    'PHP disable functions'     => '已关闭的功能',
    'PHP upload max file size'  => '最大上传文件',
    
    /* Directory */
    'Directory'         => '目录属性',
    'Directory path'    => '路径',
    'Directory read'    => '可读',
    'Directory write'   => '可写',
    
    /* Site settings */
    'Site settings' => '网站设置',
    'Site name'     => '网站名称',
    'Language'      => '界面语言',
    'RSS number'    => 'RSS Feed',
    'Related keywords'  => '获取长尾关键词',
    
    /* Upload settings */
    'Upload settings'   => '上传设置',
    'Upload allow ext'  => '允许的文件类型',
    'Upload max size'   => '最大上传尺寸',
    'Upload file path'  => '上传文件路径',
    'Upload image path' => '上传图片路径',
    'Upload image ext'  => '允许的图片类型',
    
    /* 验证信息 */
    'Site check name'    => '网站名称不能为空',
    'Upload check allow ext'    => '必须填写允许上传的文件类型',
    'Upload check error ext'    => '格式错误，以英文“,”逗号分隔。',
    'Upload check max size'     => '不能为空，如不限制，则填“0”。',
    'Upload check file path'    => '上传的文件路径不能为空',
    'Upload check error path'   => '路径格式错误，不支持特殊符号，不能以/开始或结束',
    'Upload check image path'   => '上传的图片路径不能为空',
    'Upload check image ext'    => '必须填写允许上传的图片类型',
    'Upload check DSN config'   => '数据库连接字符串不能为空',
    'Upload check max size is number'   => '格式错误，必须是数字。',
    'Upload check DSN config error format'  => '数据库连接字符串格式不正确',
    
    /* Server settings */
    'Server settings'   => '服务器设置',
    'Server time zone'  => '时区设置',
    'Server DSN config' => '数据库设置',
    
    /* Time zone */
    'Time zone' => array (
        '-12'  => '(UTC -12:00) 西部国际日期变更线，艾尼威多克，夸贾林环礁',
        '-11'  => '(UTC -11:00) 中途岛，萨摩亚群岛',
        '-10'  => '(UTC -10:00) 夏威夷',
        '-9.5' => '(UTC -09:30) 泰奥海伊，马克萨斯群岛',
        '-9'   => '(UTC -09:00) 阿拉斯加',
        '-8'   => '(UTC -08:00) 美国西部标准时间 (美国及加拿大)',
        '-7'   => '(UTC -07:00) 山地时间(美国及加拿大)',
        '-6'   => '(UTC -06:00) 中部时间(美国及加拿大)，墨西哥城',
        '-5'   => '(UTC -05:00) 东部时间(美国  &amp; 加拿大)，波哥大，利马',
        '-4'   => '(UTC -04:00) 大西洋时间(加拿大)，加拉加斯，拉巴斯',
        '-3.5' => '(UTC -03:30) 圣约翰，纽芬兰和拉布拉多',
        '-3'   => '(UTC -03:00) 巴西，布宜诺斯艾利斯，乔治敦',
        '-2'   => '(UTC -02:00) 中大西洋',
        '-1'   => '(UTC -01:00) 亚速尔群岛，佛得角群岛',
        '0'    => '(UTC 00:00) 西欧时间，伦敦，里斯本，卡萨布兰卡',
        '1'    => '(UTC +01:00) 中欧时间(布鲁塞斯，哥本哈根，马德里，巴黎)',
        '2'    => '(UTC +02:00) 东欧时间(伊士坦布尔，耶路撒冷，加里宁格勒，南非)',
        '3'    => '(UTC +03:00) 莫斯科，巴格达，奈洛比，圣彼德斯堡',
        '3.5'  => '(UTC +03:30) 德黑兰',
        '4'    => '(UTC +04:00) 阿布扎比，马斯喀特，巴库，第比利斯',
        '4.5'  => '(UTC +04:30) 喀布尔',
        '5'    => '(UTC +05:00) 叶卡捷琳堡，伊斯兰堡，卡拉奇，塔什干',
        '5.5'  => '(UTC +05:30) 孟买，加尔各答，新德里，马德拉斯',
        '5.75' => '(UTC +05:45) 加德满都',
        '6'    => '(UTC +06:00) 科伦波，达卡，新埃布尔利亚，阿拉木图',
        '6.3'  => '(UTC +06:30) 亚贡(缅甸)',
        '7'    => '(UTC +07:00) 曼谷，河内，雅加达',
        '8'    => '(UTC +08:00) 北京时间，佩思，新加坡，香港，台北',
        '8.75' => '(UTC +08:00) 澳大利亚部时间',
        '9'    => '(UTC +09:00) 东京，汉城，大阪，札幌',
        '9.5'  => '(UTC +09:30) 阿德莱德，达尔文，雅库茨克',
        '10'   => '(UTC +10:00) 澳大利亚东部标准时间，关岛，符拉迪沃斯托克',
        '10.5' => '(UTC +10:30) 豪勋爵岛 (澳大利亚)',
        '11'   => '(UTC +11:00) 马加丹，索罗门群岛，新喀里多尼亚',
        '11.3' => '(UTC +11:30) 诺福克岛',
        '12'   => '(UTC +12:00) 奥克兰，惠灵顿，斐济',
        '12.75'=> '(UTC +12:45) 查塔姆岛',
        '13'   => '(UTC +13:00) 汤加',
        '14'   => '(UTC +14:00) 基里巴斯',
    )
);