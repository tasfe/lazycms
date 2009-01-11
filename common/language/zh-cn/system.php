<?php
/**
 * +---------------------------------------------------------------------------+
 * | LL                             LLLL   LL     L   LLLL               LLLL  |
 * | LL                            LL   L  LLL   LL  LL   L             LL  LL |
 * | LL      LLLL   LLLLL  LL  LL  LL      LLLL LLL  LL         LL  LL      LL |
 * | LL         LL     LL  LL  LL  LL      L LLL LL  LLLLL      LL  LL     LL  |
 * | LL      LLLLL    LL    LLLL   LL      L  L  LL   LLLLL     LL  LL    LL   |
 * | LL     LL  LL   LL     LLLL   LL      L     LL      LL      LLLL    LL    |
 * | LL     LL  LL  LL       LL    LL   L  L     LL  L   LL      LLLL   LL     |
 * | LLLLLL  LLLLL  LLLLL    LL     LLLL   L     LL   LLLL        LL    LLLLLL |
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
    /* 模块名称 */
    'name' => '系统管理',
    
    /* 公用语言 */
    'ON'    => '√',
    'OFF'   => '×',
    'add'   => '添加',
    'try'   => '重试',
    'edit'  => '编辑',
    'back'  => '返回',
    'home'  => '首页',
    'lock'  => '锁定',
    'save'  => '保存',
    'reset' => '重置',
    'delete'    => '删除',
    'manage'    => '管理',
    'unlock'    => '启用',
    'submit'    => '提交',
    'default'   => '默认',
    'reselect'  => '反选',
    'selectall' => '全选',
    'moreattr'  => '更多属性',

    'ajax'  => array(
        'alert'     => '系统提示',
        'confirm'   => '操作确认',
        'submit'    => '确认',
        'cancel'    => '取消',
    ),
    
    /* JavaScript确认 */
    'confirm'   => array(
        'delete'    => '确定要删除吗？',
        'clear'     => '确定要清空吗？',
        'logout'    => '确定要退出吗？',
        'reset'     => '确定要重置吗？',
    ),
    
    /* 数据库异常 */
    'database'  => array(
        'parseDSN'  => '数据库连接字符串，设置错误。',
        'nolink'    => '数据库链接出错，请检查数据库设置！',
        'noselectdb'    => '没有找到指定的数据库！',
        'noextension'   => '-_-!!请先开启%s扩展支持！',
        'versionlower'  => '%s数据库版本低于%s，请升级数据库版本！',
    ),
    
    /* 上传错误信息 */
    'upload'    => array(
        'error1'  => '上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值',
        'error2'  => '上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值',
        'error3'  => '文件只有部分被上传',
        'error4'  => '没有文件被上传',
        'error5'  => '上传的文件太大',
        'error6'  => '找不到临时文件夹',
        'error7'  => '文件写入失败',
        'error8'  => '上传的文件类型不允许',
        'error9'  => '非法提交',
        'error10' => 'POST值超过了 post_max_size(%s)',
    ),
    
    /* 错误信息 */
    'error' => array(
        'title'     => '系统发生错误',
        'invalid'   => '您输入的参数有误！',
        'nofunc'    => '执行的函数不存在：%s<br/>请在 %s 定义一个 %s 函数',
        'nomain'    => '没有定义默认函数：lazy_main()',
        'message'   => '错误信息',
        'position'  => '错误位置',
        'overtime'  => '您的登录已经超时，请重新登录！',
        'permission'    => '您没有权限查看此页，返回前页。',
        
    ),
    
    /* 登录页面 */
    'login' => array(
        'title' => '管理后台登录',
        'name'  => '用户名',
        'submit'   => '登录',
        'password' => '密&nbsp; &nbsp;码',
        'language' => '语&nbsp; &nbsp;言',
        'description' => '<p>请使用有效的用户名和密码登录后台。</p><p>运行环境：PHP 4.3.3+、MySQL 4.1+</p><p><a href="../../">返回网站首页</a></p>',
        'cookie'      => array(
            'expire'    => '有效期',
            'process'   => '浏览器进程',
            'hour'      => '一小时',
            'day'       => '一天',
            'week'      => '一周',
            'month'     => '一个月',
            'permanent' => '永久'
        ),
        'check' => array(
            'name'  => '管理员名称不能为空或过长',
            'error' => '用户名或者密码错误',
            'locked'   => '帐号被锁定，不允许登录',
            'password' => '管理员密码不能为空或过长'
        )
    ),
    
    /* 系统管理 */
    'system'    => array(
        'title' => '后台管理',
        'preview'   => '预览网站',
        'logout'    => '退出登录',
        'manage'    => '系统管理',
        'cpanel'    => '控制面板',
        'lastversion'   => '最新版本',
    ),
    
    /* 官方信息 */
    'official'  => array(
        'site'  => '官方网站',
        'forums'=> '支持论坛',
    ),

    /* 编辑器 */
    'editor'    => array(
        'upfile'    => '上传文件',
        'upimg'     => '上传图片',
        'break'     => '插入分页符',
        'snapimg'   => '下载远程图片',
        'dellink'   => '删除站外链接',
        'setimg'    => '设第一幅图为缩略图',
        'resize'    => '可调整大小'
    ),

    /* 我的帐户 */
    'myaccount' => array(
        'title' => '修改密码',
        'name'  => '登录名',
        'email' => 'Email',
        'language'  => '界面语言',
        'oldpass'   => '旧密码',
        'newpass'   => '新密码',
        'renewpass' => '确认密码',
        'check' => array(
            'oldpass'  => '旧密码长度不正确(6-30)',
            'oldpass1' => '旧密码不正确',
            'password' => '密码长度不正确(6-30)',
            'repassword'    => '两次输入的密码不一致',
            'email' => '邮箱不能为空',
            'email1'    => '邮箱格式不正确',
        ),
        'alert' => array(
            'success'   => '资料修改成功'
        )
    ),

    /* 管理员管理 */
    'admins'    => array(
        'title' => '后台用户',
        'add'   => '添加管理员',
        'edit'  => '编辑管理员',
        'name'  => '登录名',
        'email' => 'Email',
        'state' => '状态',
        'purview'    => '管理员权限',
        'language'   => '界面语言',
        'password'   => '密码',
        'repassword' => '确认密码',
        'check' => array(
            'name'  => '用户名不能为空',
            'name1' => '用户名不能重复',
            'password'  => '密码长度不正确',
            'repassword'    => '两次输入的密码不一致',
            'email' => '邮箱不能为空',
            'email1'    => '邮箱格式不正确',
        ),
        'alert' => array(
            'noselect'  => '请选择一个管理员！',    
            'add'       => '添加管理员成功',
            'edit'      => '编辑管理员成功',
            'lock'      => '锁定管理员成功',
            'unlock'    => '启用管理员成功',
            'delete'    => '删除管理员成功',
        )
    ),
    
    /* 模块管理 */
    'modules'   => array(
        'title' => '安装卸载',
    ),
    
    /* 文件管理 */
    'files'     => array(
        'title' => '文件管理'
    ),
    
    /* 系统设置 */
    'settings'  => array(
        'title' => '系统设置',
        'site'  => '网站设置',
        'sitename'  => '网站名称',
        'language'  => '界面语言',
        'RSS_number'    => 'RSS Feed',
        'Related_keywords'  => '获取长尾关键词',
        'upload'    => array(
            'title' => '上传设置',
            'allowext'  => '允许的文件类型',
            'maxsize'   => '最大上传尺寸',
            'filepath'  => '上传文件路径',
            'imagepath' => '上传图片路径',
            'imageext'  => '允许的图片类型',
        ),
        'server'    => array(
            'title' => '服务器设置',
            'timezone'  => '时区设置',
            'DSN_config'=> '数据库设置',
        ),
        'check' => array(
            /* 验证信息 */
            'sitename'    => '网站名称不能为空',
            'allowext'    => '必须填写允许上传的文件类型',
            'errorext'    => '格式错误，以英文“,”逗号分隔。',
            'maxsize'     => '不能为空，如不限制，则填“0”。',
            'maxsize1'    => '格式错误，必须是数字。',
            'filepath'    => '上传的文件路径不能为空',
            'errorpath'   => '路径格式错误，不支持特殊符号，不能以/开始或结束',
            'imagepath'   => '上传的图片路径不能为空',
            'imageext'    => '必须填写允许上传的图片类型',
            'DSNconfig'   => '数据库连接字符串不能为空',
            'DSNformat'   => '数据库连接字符串格式不正确',
        ),
        'alert' => array(
            'save'  => '系统设置保存成功'
        )
    ),
    
    /* 系统信息 */
    'sysinfo'   => array(
        'title' => '系统信息',
        'config'    => '配置信息',
        'directory' => array(
            'title' => '目录属性',
            'path'  => '路径',
            'read'  => '可读',
            'write' => '可写',
        ),
        'phpinfo'   => 'PHP设置',
        'zlib'      => 'Zlib开启',
        'iconv'     => 'Zlib开启',
        'mbstring'  => 'mbstring启用',
        'server_OS'     => '服务器系统',
        'GD_version'    => 'GD版本',
        'PHP_version'   => 'PHP版本',
        'PHP_SAPI_name' => 'PHP接口',
        'DB_version'    => '数据库版本',
        'version'       => 'LazyCMS版本',
        'software'      => '服务器解译引擎',
        'useragent'     => '浏览器类型',
        'safe_mode'     => '安全模式',
        'file_uploads'      => '文件上传',
        'post_max_size'     => '最大POST数据',
        'open_base_dir'     => '开放根目录',
        'display_errors'    => '显示错误',
        'magic_quotes_gpc'  => '魔术引用',
        'allow_url_fopen'   => '打开远程连接',
        'disable_functions' => '已关闭的功能',
        'upload_max_filesize'   => '最大上传文件',
        
    ),

    /* Time zone */
    'timezone' => array (
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
    ),

    /* 帮助信息 */
    'help'  => array(
        'title'     => '帮助',
        'settings'  => array(
            'title' => '设置帮助',
            'Related_keywords' => '
                |[b]True[/b]：自动从百度获取相关关键词，并写入本站的私有词库。
                |[b]False[/b]：不获取关键词。
            ',
            'allowext'  => '允许上传的文件扩展名，使用英文逗号分隔',
            'DSN'       =>  '
                |[b]连接字符串格式：[/b]
                |   mysql://用户名:[密码]@主机名:[端口][/数据库前缀]/数据库名称
                |
                |[b]例：[/b]
                |   mysql://root:123@localhost/lazy/lazycms
                |
                |[b]可选项：[/b]
                |   密码，端口（默认3306），数据库前缀
            ',
            
        ),
    )
);