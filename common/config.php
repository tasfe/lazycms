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
 */
defined('COM_PATH') or die('Restricted access!');
/**
 * LazyCMS 惯例配置文件
 *
 * 惯例配置定义 变量名大小写任意，都会统一转换成小写
 * 所有配置参数都可以在生效前动态改变
 *
 * @copyright   Copyright (c) 2007-2008 lazycms.net All rights reserved.
 * @author      Lukin <mylukin@gmail.com>
 * @date        2008-6-18
 */
return array(
    /* 网站设置 */
    'SITE_NAME'  => 'LazyCMS v2.0 演示站',
    'LANGUAGE'   => 'zh-cn',
    'TIME_ZONE'  => 8,
    'SITE_BASE'  => '/',

    /* 会员设置 */
    'USER_PARAMS'=> array(
        'allowReg' => true
    ),

    /* 数据库设置 */
    'DSN_CONFIG' => 'sqlite://path=db/LazyCMS.db',//mysqli://root@localhost/lazy/lazy2
    
    /* Cookie设置 */
    'COOKIE_DOMAIN' => '',
    'COOKIE_PREFIX' => 'LAZY_',
);
?>