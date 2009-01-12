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
 * Field2Tag
 */
class Field2Tag{
    var $_model  = array();
    var $_fields = array();
    var $_val;
    /**
     * 兼容PHP5模式
     *
     */
    function __construct($model){
        $this->_model  = $model;
        $this->_fields = json_decode($model['modelfields']);
        $this->_val    = new Validate();
    }
    /**
     * 初始化
     *
     * @return Field2Tag
     */
    function Field2Tag($model){
        $this->__construct($model);
    }
    /**
     * 取得验证对象
     *
     * @return object
     */
    function getVal(){
        return $this->_val;
    }
    /**
     * 取得POST数据
     *
     * @return array
     */
    function _POST(){
        $R = array();
        foreach ($this->_fields as $field) {
            $R[$field->ename] = isset($_POST[$field->ename])?$_POST[$field->ename]:null;
            $R[$field->ename] = is_array($R[$field->ename])?implode(',',$R[$field->ename]):$R[$field->ename];
            if (!empty($field->validate)) {
                $this->_val->check($this->formatValidate($field->ename,$field->validate));
            }
        }
        return $R;
    }
    /**
     * 取得所有字段
     *
     * @return array
     */
    function _Fields(){
        $R = array();
        foreach ($this->_fields as $field) {
            $R[$field->ename] = $field;
        }
        return $R;
    }
    /**
     * 格式化验证
     *
     * @param string $p1
     * @param string $p2
     * @return string
     */
    function formatValidate($p1,$p2){
        $R  = null;
        $R1 = explode("\n",$p2);
        foreach ($R1 as $v) {
            $R[] = sprintf(trim($v),$p1);
        }
        return implode('',$R);
    }
    /**
     * 取得编辑器字段
     *
     * @return array
     */
    function getEditors(){
        $R = array();
        foreach ($this->_fields as $i=>$field) {
            if (instr('editor,basic',$field->intype)) {
                $R[$field->ename] = $field->option;
            }
        }
        return $R;
    }
    /**
     * 转换标签
     *
     * @param object $p1
     * @param bool $p2
     * @return string
     */
    function tag($p1,$p2=false){
        $R = null;
        $f = $p1;
        $tip     = empty($f->tip)?null:' tip="'.$f->label.'::'.ubbencode(h2c($f->tip)).'"';
        $name    = $f->ename;
        $length  = $f->length;
        $default = ($p2===false) ? $f->default : $p2;
        $width   = $f->width;
        $opts    = isset($f->option)?$f->option:null;
        switch ($f->intype) {
            case 'input':
                $R = "<input{$tip} style=\"width:{$width}\" type=\"text\" name=\"{$name}\" id=\"{$name}\" maxlength=\"{$length}\" value=\"{$default}\" />";
                if ($this->_model['setkeyword']==$name) {
                    $R.= '<span><input type="checkbox" name="autokeywords" id="autokeywords" value="1" checked="checked" cookie="true" /><label for="autokeywords">'.t('system::Auto keywords').'</label></span></p>';
                }
                break;
            case 'textarea':
                $R = "<textarea{$tip} style=\"width:{$width}\" name=\"{$name}\" id=\"{$name}\" rows=\"5\">{$default}</textarea>";
                break;
            case 'select':
                $R = "<select name=\"{$name}\" id=\"{$name}\">";
                $R1 = explode("\n",$f->value);
                foreach ($R1 as $v) {
                    $v = trim($v);
                    if ($v!='') {
                        $R2 = explode(':',$v);
                        $R2 = array_map('h2c',$R2);
                        $selected = !empty($default) ? ((string)$default==(string)$R2[0] ? ' selected="selected"' : null) : null;
                        $R.= '<option value="'.$R2[0].'"'.$selected.'>'.$R2[1].'</option>';
                    }
                }
                $R.= '</select>';
                break;
            case 'radio': case 'checkbox':
                $R = '<span>';
                $R1 = explode("\n",$f->value);
                foreach ($R1 as $k=>$v) {
                    $v = trim($v);
                    if ($v!='') {
                        $R2 = explode(':',$v);
                        $R2 = array_map('h2c',$R2);
                        $checked = !empty($default) ? (instr($default,$R2[0]) ? ' checked="checked"' : null) : null;
                        $R.= "<input name=\"{$name}".($f->intype=="checkbox"?"[{$k}]":null)."\" id=\"{$name}[{$k}]\" type=\"".$f->intype."\" value=\"{$R2[0]}\"{$checked} /><label for=\"{$name}[{$k}]\">{$R2[1]}</label>";
                    }
                }
                $R.= '</span>';
                break;
            case 'editor': case 'basic':
                $setting = array(
                    'upimg'     => $opts->upimg,
                    'upfile'    => $opts->upfile,
                    'pagebreak' => $opts->break,
                    'snapimg'   => array($opts->snapimg,1),
                    'dellink'   => array($opts->dellink,1),
                    'setimg'    => array($opts->setimg,1),
                    'resize'    => $opts->resize,
                    'value'     => $default,
                    'width'     => $width,
                );
                if ($f->intype=='basic') {
                    $setting['toolbar'] = 'Basic';
                    $setting['height']  = "80px";
                }
                $setting['height'] = isset($setting['height'])?$setting['height']:"250px";
                $R = '<span class="box">'.editor($name,$setting).'</span>';
                break;
            case 'date':

                break;
            case 'upfile':

                break;
        }
        return $R;
    }
    /**
     * 取得结果
     *
     * @param string $p1
     * @param array  $p2
     * @return string
     */
    function fetch($p1,$p2=null){
        $R = null;
        foreach ($this->_fields as $field) {
            $R.= str_replace(array('{label}','{object}'),array($field->label,$this->tag($field,$p2[$field->ename])),$p1);
        }
        return $R;
    }
}