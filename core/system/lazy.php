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
 * LazyCMS 基础抽象类
 *
 * @copyright   Copyright (c) 2007-2008 lazycms.net All rights reserved.
 * @author      Lukin <mylukin@gmail.com>
 */
abstract class Lazy{
    //__set *** *** www.LazyCMS.net *** ***
    private function __set($name ,$value){
        if(property_exists($this,$name)){
            $this->$name = $value;
        }
    }
    //__get *** *** www.LazyCMS.net *** ***
    private function __get($name){
        if(isset($this->$name)){
            return $this->$name;
        }else {
            return null;
        }
    }
    //__isset *** *** www.LazyCMS.net *** ***
    private function __isset($name){
        return isset($this->$name);
    }
    //__unset *** *** www.LazyCMS.net *** ***
    private function __unset($name){
        unset($this->$name);
    }
    // urlencode *** *** www.LazyCMS.net *** ***
    protected function urlencode($l1){
        return str_replace(array('"',"'",'<','>'), array('%22','%27','%3C','%3E'), $l1);;
    }
    // urldecode *** *** www.LazyCMS.net *** ***
    protected function urldecode($l1){
        return str_replace(array('%22','%27','%3C','%3E'), array('"',"'",'<','>'), $l1);;
    }
    
}
?>