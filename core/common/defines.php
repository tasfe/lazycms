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
 * 系统定义文件
 *
 * @copyright   Copyright (c) 2007-2008 lazycms.net All rights reserved.
 * @author      Lukin <mylukin@gmail.com>
 */

// 系统信息
define('PHP_SAPI_NAME',php_sapi_name());
define('IS_APACHE',strstr($_SERVER['SERVER_SOFTWARE'], 'Apache') || strstr($_SERVER['SERVER_SOFTWARE'], 'LiteSpeed') );
define('IS_CGI',strncmp(PHP_SAPI_NAME,'cgi',3)==0 ? 1 : 0 );
define('IS_IIS',PHP_SAPI_NAME =='isapi' ? 1 : 0);

// 当前文件名
if (!defined('PHP_FILE')) {
    // CGI or FASTCGI模式下
    if (IS_CGI) {
        $_I1 = explode('.php',$_SERVER['PHP_SELF']);
        define('PHP_FILE',rtrim(str_replace($_SERVER['HTTP_HOST'],'',$_I1[0].'.php'),'/'));
    } else {
        define('PHP_FILE',rtrim($_SERVER['SCRIPT_NAME'],'/'));
    } unset($_I1);
}
if (!defined('LAZY_PATH')) {
    define('LAZY_PATH','./');
}
// 支持的URL模式
define('URL_COMMON',   0);   //普通模式
define('URL_PATHINFO', 1);   //PATHINFO模式
define('URL_REWRITE',  2);   //REWRITE模式

define('VENDOR_PATH',CORE_PATH.'/vendor');
// 为了方便导入第三方类库 设置vendor目录到include_path
set_include_path(get_include_path() . PATH_SEPARATOR . VENDOR_PATH);

define('LAZY_VERSION','1.1.0.0410');
?>