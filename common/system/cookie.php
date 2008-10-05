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
 * Cookie管理类
 *
 * @copyright   Copyright (c) 2007-2008 lazycms.net All rights reserved.
 * @author      Lukin <mylukin@gmail.com>
 * @date        2008-6-18
 */
// Cookie *** *** www.LazyCMS.net *** ***
class Cookie{
    // 判断Cookie是否存在
    static function is_set($name) {
        return isset($_COOKIE[C('COOKIE_PREFIX')][$name]);
    }

    // 获取某个Cookie值
    static function get($name) {
        return isset($_COOKIE[C('COOKIE_PREFIX')][$name]) ? $_COOKIE[C('COOKIE_PREFIX')][$name] : null;
    }

    // 设置某个Cookie值
    static function set($name,$value,$expire=0,$path='/',$domain='') {
        if(empty($domain)) { $domain = C('COOKIE_DOMAIN'); }
        setcookie(C('COOKIE_PREFIX').'['.$name.']',$value,$expire,$path,$domain);
    }

    // 删除某个Cookie值
    static function delete($name,$path='/',$domain='') {
        if(empty($domain)) { $domain = C('COOKIE_DOMAIN'); }
        self::set($name,'',now() - 3600,$path,$domain);
    }
}
