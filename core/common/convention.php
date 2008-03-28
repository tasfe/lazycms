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
 * | Copyright (c) 2007-2008 LazyCMS.net All rights reserved.                  |
 * +---------------------------------------------------------------------------+
 * | 许可协议，请查看源代码中附带的 LICENSE.txt 文件，                         |
 * | 或者访问 http://www.lazycms.net/ 获得详细信息。                           |
 * +---------------------------------------------------------------------------+
 * | Author: Lukin <mylukin@gmail.com>                                         |
 * +---------------------------------------------------------------------------+
 */
defined('CORE_PATH') or die('Restricted access!');
/**
 * LazyCMS 惯例配置文件
 * 
 * 请不要修改该文件，如果要覆盖惯例配置的值，请修改（custom/config.php）配置文件
 *
 * 惯例配置定义 变量名大小写任意，都会统一转换成小写
 * 如果要覆盖惯例配置的值，请修改（custom/config.php）配置文件
 * 所有配置参数都可以在生效前动态改变
 *
 * @copyright   Copyright (c) 2007-2008 lazycms.net All rights reserved.
 * @author      Lukin <mylukin@gmail.com>
 */
return array(
    /* 系统设置 */
    'LANGUAGE'   => 'zh-cn',         // 网站默认语言包
    'TIME_ZONE'  => 'Asia/Shanghai', // 设置系统时区，请参考http://php.net/manual/zh/timezones.php
    'SITE_BASE'  => '/',             // 程序安装目录，最后用 / 结束
    'SITE_MODE'  => false,           // 网站模式，true 动态模式，false静态模式
    'SITE_INDEX' => 'index.html',    // 默认首页，填写服务器可以支持的默认首页即可

    /* 上传路径 */
    'UPFILE_PATH'   => 'up_files',
    'UPFILE_SUFFIX' => 'png,gif,jpg,jpeg,bmp',

    /* 数据库设置 */
    'DSN_CONFIG' => 'mysql://root:123456@localhost:3306/lazycms', // 支持数组
    /*
    'DSN_CONFIG' => array(
        'scheme'=> 'mysql',
        'host'  => 'localhost',
        'port'  => '3306',
        'user'  => 'root',
        'pwd'   => '',
        'name'  => 'lazycms'
    ),
    */
    'DSN_PREFIX' => 'lazy_', //表前缀
    
    /* 模型表前缀 */
    'MODEL_PREFIX' => 'add',

    /* URL模式: URL_COMMON,URL_PATHINFO,URL_REWRITE */
    'URL_MODEL' => URL_COMMON,   // 默认为 URL_COMMON 模式
    'PATH_DEPR' => '/',          // PATHINFO参数之间分割号
    'ROUTER_ON' => true,         // 启用路由判断
    
    /* 系统变量设置 */
    'VAR_INDEX'  => 'index.php', // 程序默认入口文件
    'VAR_MODULE' => 'm',         // 默认模块获取变量
    'VAR_ACTION' => 'a',         // 默认操作获取变量
    'VAR_ROUTER' => 'r',         // 默认路由获取变量
    
    /* 系统目录设置 */
    'ADMIN_PATH' => 'admin', // 管理目录
    'PAGES_PATH' => 'page',  // 前台目录

    /* dubug模式 */
    'DEBUG_MODE' => true,

    /* 模块和操作设置 */
    'DEFAULT_MODULE' => 'Index', // 默认模块名称
    'DEFAULT_ACTION' => 'Index', // 默认操作名称

    /* 静态缓存设置 */
    'HTML_URL_SUFFIX' => '.htm',  // 伪静态后缀设置 or 生成文件的后缀

    /* Cookie设置 */
    'COOKIE_DOMAIN' => '',      // Cookie有效域名
    'COOKIE_PREFIX' => 'LAZY_', // Cookie前缀 避免冲突

    /* Template设置 */
    'TEMPLATE_PATH' => 'template',
    'TEMPLATE_EXT'  => '{php,html,shtml,shtm,htm}',
    'TEMPLATE_DEF'  => 'default.html',

    /* 分页标签 */
    'WEB_BREAK' => '<div style="page-break-after: always"><span style="display: none">&nbsp;</span></div>',
    /* 中文正则，请不要修改 */
    'CN_PATTERN' => "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/",
);
?>