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
 * LazyCMS 配置文件
 * 
 * 配置定义 变量名大小写任意，都会统一转换成小写
 * 所有配置参数都可以在生效前动态改变
 */
return array(
    /* 网站设置 */
    'SITE_NAME'  => 'LazyCMS v2.0 演示站',
    'LANGUAGE'   => 'zh-cn',
    'RSS_NUMBER' => 10,
    'TEMPLATE'   => 'themes',
    'GET_RELATED_KEY' => false,

    /* 上传设置 */
    'UPLOAD_ALLOW_EXT' => 'bmp,png,gif,jpg,jpeg,zip,rar,doc,xls',
    'UPLOAD_MAX_SIZE'  => 10000000,
    'UPLOAD_FILE_PATH' => 'images',
    'UPLOAD_IMAGE_PATH'=> 'images/stories',
    'UPLOAD_IMAGE_EXT' => 'bmp,png,gif,jpg,jpeg',

    /* 服务器设置 */
    'TIME_ZONE'  => 8,
    'DSN_CONFIG' => 'mysql://root@localhost/lazy/lazy2',

    /* Cookie设置 */
    'COOKIE_DOMAIN' => '',
    'COOKIE_PREFIX' => 'LAZY_',
);
