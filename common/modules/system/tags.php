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
 * 模块标签处理
 */
class System_Tags {
    /**
     * 解析标签
     *
     * @param object $tags      执行标签解析的对象
     * @param string $tagName   标签名称
     * @param string $tag       标签内容
     * @return array            通过标签解析对象的R方法返回
     */
    function ParseTags($tags,$tagName,$tag){
        $R = null; $isParse = true;
        switch ($tagName) {
            case 'sitename':
                $R = c('SITE_NAME'); break;
            case 'cms': case 'lazycms':
                $R = '<span id="lazycms">Powered by: <a href="http://www.lazycms.net" style="font-weight:bold" target="_blank">LazyCMS</a> <span>'.LAZY_VERSION.'</span></span>'; break;
            default:
                $isParse = false;
                break;
        }
        return $tags->R($isParse,$R);
    }
}