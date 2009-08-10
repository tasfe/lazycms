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
 * Cookie 管理类
 */
class Cookie{
    /**
     * 判断Cookie是否存在
     *
     * @param string $name
     * @return bool
     */
    function is_set($name) {
        return isset($_COOKIE[c('COOKIE_PREFIX')][$name]);
    }
    /**
     * 获取某个Cookie值
     *
     * @param string $name
     * @return mixed
     */
    function get($name) {
        return isset($_COOKIE[c('COOKIE_PREFIX')][$name]) ? $_COOKIE[c('COOKIE_PREFIX')][$name] : null;
    }
    /**
     * 设置某个Cookie值
     *
     * @param string $name
     * @param string $value
     * @param int    $expire
     * @param string $path
     * @param string $domain
     */
    function set($name,$value,$expire=0,$path='/',$domain='',$prefix=true) {
        if(empty($domain)) { $domain = c('COOKIE_DOMAIN'); }
        $name = $prefix?c('COOKIE_PREFIX').'['.$name.']':$name;
        setcookie($name,$value,$expire,$path,$domain);
    }
    /**
     * 删除某个Cookie值
     *
     * @param string $name
     * @param string $path
     * @param string $domain
     */
    function delete($name,$path='/',$domain='') {
        if(empty($domain)) { $domain = c('COOKIE_DOMAIN'); }
        Cookie::set($name,'',now()-3600,$path,$domain);
    }
}
