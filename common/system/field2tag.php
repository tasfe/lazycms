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
 * Field2Tag
 *
 * @copyright   Copyright (c) 2007-2008 lazycms.net All rights reserved.
 * @author      Lukin <mylukin@gmail.com>
 * @date        2008-8-3
 */
// Field2Tag *** *** www.LazyCMS.net *** ***
class Field2Tag {
    private $fields = array();
    // __construct *** *** www.LazyCMS.net *** ***
    function __construct($fields){
        $this->fields = json_decode($fields);
        foreach ($this->fields as &$f) { $f = (array)$f; }
        print_r($this->fields);
    }
    // _POST *** *** www.LazyCMS.net *** ***
    public function _POST(){
        $R = array();
        foreach ($this->fields as $field) {
            $R[$field['ename']] = isset($_POST[$field['ename']])?$_POST[$field['ename']]:null;
        }
        return $R;
    }
    // tag *** *** www.LazyCMS.net *** ***
    public function tag($p1){
        $R = null;
        $f = $p1;
        switch ($f['intype']) {
            case 'input':

                break;
            case 'textarea':

                break;
            case 'radio': case 'checkbox': case 'select':

                break;
            case 'basic':

                break;
            case 'editor':

                break;
            case 'date':

                break;
            case 'upfile':

                break;
        }
        return $R;
    }
    // fetch *** *** www.LazyCMS.net *** ***
    public function fetch($p1){
        $R = null;
        foreach ($this->fields as $field) {
            $R.= str_replace(array('{label}','{object}'),array($field['label'],$this->tag($field)),$p1);
        }
        return $R;
    }
}