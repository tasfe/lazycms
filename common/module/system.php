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
tpl_add_plugin('system_list_tpl_plugin');

/**
 * 处理模版变量
 *
 * @param  $tag_name
 * @param  $tag
 * @return mixed    null 说明没有解析成功，会继续
 */
function system_tpl_plugin($tag_name,$tag) {
    switch ($tag_name) {
        case 'sitename':
            $result = C('SiteTitle');
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
            $result = WEB_ROOT.system_themes_path();
            break;
        case 'lang': case 'language':
            $result = C('Language');
            break;
        case 'cms': case 'lazycms':
            $result = '<span id="lazycms">Powered by: <a href="http://lazycms.com/" style="font-weight:bold" target="_blank">LazyCMS</a> '.LAZY_VERSION.'</span>';
            break;
        case 'jquery':
            $version = mid($tag,'ver="','"');
            if (!$version)
                $version = mid($tag,"ver='","'");
            $version = $version ? $version : '1.4.2';
            $result  = 'http://ajax.googleapis.com/ajax/libs/jquery/'.$version.'/jquery.min.js';
            break;
        default:
            $result = null;
            break;
    }
    return $result;
}

function system_list_tpl_plugin($tag_name,$tag) {
    if (!instr($tag_name,'post,list')) return null;
    // 列表类型
    $type = tpl_get_attr($tag,'type');
    // 类型为必填
    if (!$type) return null;
    // 分类ID
    $sortid = tpl_get_attr($tag,'sortid');
    // 显示条数
    $number = tpl_get_attr($tag,'number');
    // 校验数据
    $sortid = validate_is($sortid,VALIDATE_IS_LIST) ? $sortid : null;
    $number = validate_is($number,VALIDATE_IS_NUMERIC) ? $number : 10;
    // 处理
    switch ($type) {
        case 'new':
            break;
        case 'hot':
            break;
        case 'chill':
            break;
        default:
            $result = null;
    }
    return $result;
}
/**
 * 查询模版路径
 *
 * @return string
 */
function system_themes_path() {
    return TEMPLATE.'/'.C('Template');
}