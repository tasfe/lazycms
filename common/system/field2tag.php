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
 * @date        2008-8-26
 */
// Field2Tag *** *** www.LazyCMS.net *** ***
class Field2Tag {
    private $model  = array();
    private $fields = array();
    private $val;
    // __construct *** *** www.LazyCMS.net *** ***
    function __construct($model){
        $this->model  = $model;
        $this->fields = object_deep(json_decode($model['modelfields']));
        $this->val    = new Validate();
    }
    // getVal *** *** www.LazyCMS.net *** ***
    public function getVal(){
        return $this->val;
    }
    // _POST *** *** www.LazyCMS.net *** ***
    public function _POST(){
        $R = array();
        foreach ($this->fields as $field) {
            $R[$field['ename']] = isset($_POST[$field['ename']])?$_POST[$field['ename']]:null;
            $R[$field['ename']] = is_array($R[$field['ename']])?implode(',',$R[$field['ename']]):$R[$field['ename']];
            if (!empty($field['validate'])) {
                $this->val->check($this->formatValidate($field['ename'],$field['validate']));
            }
        }
        return $R;
    }
    // formatValidate *** *** www.LazyCMS.net *** ***
    public function formatValidate($p1,$p2){
        $R  = null;
        $R1 = explode("\n",$p2);
        foreach ($R1 as $v) {
            $R[] = sprintf(trim($v),$p1);
        }
        return implode('',$R);
    }
    // getEditor *** *** www.LazyCMS.net *** ***
    public function getEditors(){
        $R = array();
        foreach ($this->fields as $i=>$field) {
            if (instr('editor,basic',$field['intype'])) {
                $R[$field['ename']] = $field['option'];
            }
        }
        return $R;
    }
    // tag *** *** www.LazyCMS.net *** ***
    public function tag($p1,$p2=false){
        $R = null;
        $f = $p1;
        $tip     = empty($f['tip'])?null:' tip="'.$f['label'].'::'.ubbencode(h2encode($f['tip'])).'"';
        $name    = $f['ename'];
        $length  = $f['length'];
        $default = ($p2===false) ? $f['default'] : $p2;
        $opts    = isset($f['option'])?$f['option']:null;
        switch ($f['intype']) {
            case 'input':
                $class   = $f['width']!='auto'?' class="'.$f['width'].'"':' class="in2"';
                $R = "<input{$tip}{$class} type=\"text\" name=\"{$name}\" id=\"{$name}\" maxlength=\"{$length}\" value=\"{$default}\" />";
                if ($this->model['setkeyword']==$name) {
                    $R.= '<span tip="'.L('common/autokeywords/@tip','system').'"><input type="checkbox" name="autokeywords" id="autokeywords" value="1" checked="checked" cookie="true" /><label for="autokeywords">'.L('common/autokeywords','system').'</label></span></p>';
                }
                break;
            case 'textarea':
                $class   = $f['width']!='auto'?' class="'.$f['width'].'"':' class="in4"';
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
                        $R.= "<input name=\"{$name}".($f['intype']=="checkbox"?"[]":null)."\" id=\"{$name}[{$k}]\" type=\"".$f['intype']."\" value=\"{$R2[1]}\"{$checked} /><label for=\"{$name}[{$k}]\">{$R2[0]}</label>";
                    }
                }
                $R.= '</span>';
                break;
            case 'editor': case 'basic':
                $width   = $this->class2px($f['width']);
                $setting = array(
                    'upimg'     => $opts['upimg'],
                    'upfile'    => $opts['upfile'],
                    'pagebreak' => $opts['break'],
                    'snapimg'   => array($opts['snapimg'],1),
                    'dellink'   => array($opts['dellink'],1),
                    'setimg'    => array($opts['setimg'],1),
                    'resize'    => $opts['resize'],
                    'value'     => $default,
                    'width'     => $width,
                );
                if ($f['intype']=='basic') {
                    $setting['toolbar'] = 'Basic';
                    $setting['height']  = "80px";
                }
                $setting['height'] = isset($setting['height'])?$setting['height']:"250px";
                $R = '<div class="box">'.editor($name,$setting).'</div>';
                break;
            case 'date':

                break;
            case 'upfile':

                break;
        }
        return $R;
    }
    // fetch *** *** www.LazyCMS.net *** ***
    public function fetch($p1,$p2=null){
        $R = null;
        foreach ($this->fields as $field) {
            $R.= str_replace(array('{label}','{object}'),array($field['label'],$this->tag($field,$p2[$field['ename']])),$p1);
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
