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
 * LazyCMS 模板解析类
 *
 * @copyright   Copyright (c) 2007-2008 lazycms.net All rights reserved.
 * @author      Lukin <mylukin@gmail.com>
 */
// Template *** *** www.LazyCMS.net *** ***
class Template extends Lazy{
    public $path;
    private $vars;
    // __construct *** *** www.LazyCMS.net *** ***
    public function __construct(){
        $this->path = LAZY_PATH.C('PAGES_PATH').'/'.strtolower(C('CURRENT_MODULE')).'/template';
        $this->vars = array();
    }
    // assign *** *** www.LazyCMS.net *** ***
    public function assign($l1,$l2=null){ // $l1:name, $l2:value
        if (is_array($l1) && is_null($l2)) {
            $this->vars = array_merge($this->vars, $l1);
        } else {
            $this->vars[$l1] = $l2;
        }
    }
    // fetch *** *** www.LazyCMS.net *** ***
    public function fetch($l1,$l2=null){ // $l1:file, $l2:cacheId
        // 生成输出内容并缓存
        extract($this->vars);
        ob_start();
        include $this->path.DIRECTORY_SEPARATOR.$l1;
        $I1 = ob_get_contents();
        ob_end_clean();
        return $I1;
    }
    // display *** *** www.LazyCMS.net *** ***
    public function display($l1,$l2=null){ // $l1:file, $l2:cacheId
        echo $this->fetch($l1,$l2);
    }
}
?>