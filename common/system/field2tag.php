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
        //print_r($this->fields);
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
        $tip     = empty($f['tip'])?null:' tip="'.$f['label'].'::'.$f['tip'].'"';
        $name    = $f['ename'];
        $class   = $f['width']!='auto'?' class="'.$f['width'].'"':null;
        $length  = $f['length'];
        $default = $f['default'];
        switch ($f['intype']) {
            case 'input':
                $R = "<input{$tip}{$class} type=\"text\" name=\"{$name}\" id=\"{$name}\" maxlength=\"{$length}\" value=\"{$default}\" />";
                break;
            case 'textarea':
                $R = "<textarea{$tip}{$class} name=\"{$name}\" id=\"{$name}\" rows=\"5\">{$default}</textarea>";
                break;
            case 'select':
                $R = "<select name=\"{$name}\" id=\"{$name}\">";
                $R1 = explode("\n",$f['value']);
                foreach ($R1 as $v) {
                    $v = trim($v);
                    if ($v!='') {
                        $R2 = explode(':',$v);
                        foreach ($R2 as &$a) { $a = h2encode($a); }
                        $selected = !empty($default) ? ((string)$default==(string)$R2[1] ? ' selected="selected"' : null) : null;
                        $R.= '<option value="'.$R2[1].'"'.$selected.'>'.$R2[0].'</option>';
                    }
                }
                $R.= '</select>';
                break;
            case 'radio': case 'checkbox':
                $R = '<span>';
                $R1 = explode("\n",$f['value']);
                foreach ($R1 as $k=>$v) {
                    $v = trim($v);
                    if ($v!='') {
                        $R2 = explode(':',$v);
                        foreach ($R2 as &$a) { $l3 = h2encode($a); }
                        $checked = !empty($default) ? (instr($default,$R2[1]) ? ' checked="checked"' : null) : null;
                        $R.= "<input name=\"{$name}\" id=\"{$name}[{$k}]\" type=\"".$f['intype']."\" value=\"{$R2[1]}\"{$checked} /><label for=\"{$name}[{$k}]\">{$R2[0]}</label>";
                    }
                }
                $R.= '</span>';
                break;
            case 'basic':
                $width = $f['width']=='auto'?'100%':$this->class2px($f['width']);
                $R = '<div class="box">'.editor($name,array(
                    'upimg'   => true,    
                    'snapimg' => array(1,1),
                    'dellink' => array(1,1),
                    'toolbar' => 'Basic',
                    'resize'  => true,
                    'value'   => $default,
                    'width'   => $width,
                    'height'  => 150,
                    'editor'  => 'fckeditor'
                )).'</div>';
                break;
            case 'editor':
                $width = $f['width']=='auto'?'100%':$this->class2px($f['width']);
                $R = '<div class="box">'.editor($name,array(
                    'upimg'     => true,
                    'upfile'    => true,
                    'pagebreak' => true,
                    'snapimg'   => array(1,1),
                    'dellink'   => array(1,1),
                    'setimg'    => array(1,1),
                    'resize'    => true,
                    'value'     => $default,
                    'width'     => $width,
                    'editor'    => 'fckeditor'
                )).'</div>';
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
    // class2px *** *** www.LazyCMS.net *** ***
    private function class2px($p1){
        $style = COM_PATH.'/images/style.css';
        if (is_file($style)) {
            if (preg_match_all('/\.(in(\d+)) *\{.*(width\:(.*))\;.*\}/iU',read_file($style),$ins)) {
                foreach ($ins[1] as $k=>$v) {
                    if ($p1==$v) {
                        return $ins[4][$k];
                    }
                }
            }
        }
    }
}