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
 * | Copyright (C) 2007-2008 LazyCMS.com All rights reserved.                  |
 * +---------------------------------------------------------------------------+
 * | LazyCMS is free software. This version use Apache License 2.0             |
 * | See LICENSE.txt for copyright notices and details.                        |
 * +---------------------------------------------------------------------------+
 */
defined('COM_PATH') or die('Restricted access!');

// 注册模版变量处理函数
tpl_add_plugin('system_tpl_plugin');

/**
 * 处理模版变量
 *
 * @param  $tag_name
 * @param  $tag
 * @return mixed
 */
function system_tpl_plugin($tag_name,$tag) {
    switch ($tag_name) {
        case 'sitename':
            $result = C('SiteName');
            break;
        case 'inst': case 'webroot':
            $result = WEB_ROOT;
            break;
        case 'host': case 'domain':
            $result = HTTP_HOST;
            break;
        case 'ver': case 'version':
            $result = LAZY_VERSION;
            break;
        case 'theme': case 'templet': case 'template':
            $result = C('Template');
            break;
        case 'lang': case 'language':
            $result = LANGUAGE;
            break;
        case 'cms': case 'lazycms':
            $result = '<span id="lazycms">Powered by: <a href="http://lazycms.com/" style="font-weight:bold" target="_blank">LazyCMS</a> <span>'.LAZY_VERSION.'</span></span>';
            break;
        case 'jquery':
            $version = mid($tag,'ver="','"');
            $version = $version ? $version : '1.4.2';
            $result  = 'http://ajax.googleapis.com/ajax/libs/jquery/'.$version.'/jquery.min.js';
            break;
        default:
            $result = null;
            break;
    }
    return $result;
}
