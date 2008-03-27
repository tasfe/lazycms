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
 * LazyCMS 标签对象
 *
 * @copyright   Copyright (c) 2007-2008 lazycms.net All rights reserved.
 * @author      Lukin <mylukin@gmail.com>
 */
// Template *** *** www.LazyCMS.net *** ***
class Label extends Lazy{
    public $result;

    private $p;
    private $fetch;

    private $_db;
    private $_html;
    // __construct *** *** www.LazyCMS.net *** ***
    function __construct(){
        $this->_db = getConn();
    }
    //__set *** *** www.LazyCMS.net *** ***
    private function __set($name ,$value){
        if(property_exists($this,$name)){
            switch ($name) {
                case 'p':
                    $this->$name .= $value."\n";
                    break;
                default :
                    $this->$name = $value;
                    break;
            }
        }
    }
    //__get *** *** www.LazyCMS.net *** ***
    private function __get($name){
        if ($name=='fetch') {
            return $this->_html;
        }
        if(isset($this->$name)){
            return $this->$name;
        } else {
            return null;
        }
    }
    // create *** *** www.LazyCMS.net *** ***
    public function create($l1){
        $this->result = $this->_db->query($l1);
    }
    // result *** *** www.LazyCMS.net *** ***
    public function result($type=1){
         return $this->_db->fetch($this->result,$type);
    }
    // p *** *** www.LazyCMS.net *** ***
    public function p($data){
        foreach ($data as &$v) {
            $v = htmlencode($v);
        }
        $P = $this->p;
        $P = str_replace('{fieldname}',$data['fieldname'],$P);
        $P = str_replace('{fieldinput}',$this->tag($data['inputtype'],$data),$P);
        $this->_html .= $P;
    }
    // tag *** *** www.LazyCMS.net *** ***
    private function tag($l1,$l2){
        $I1     = null;
        $l1     = strtolower($l1);
        $data   = $l2;
        $module = getObject();
        switch ($l1) {
            case 'input': 
                $I1 = '<input class="in4" type="text" id="'.$data['fieldename'].'" name="'.$data['fieldename'].'" maxlength="'.$data['fieldlength'].'" value="'.$data['fieldefault'].'" />';
                break;
            case 'textarea': 
                $I1 = '<textarea name="'.$data['fieldename'].'" id="'.$data['fieldename'].'" rows="5" class="in4">'.$data['fieldefault'].'</textarea>';
                break;
            case 'radio': 
                $I1 = '<span>';
                $I2 = explode("\n",$data['fieldvalue']);
                foreach ($I2 as $v) {
                    $I3 = explode(":",$v);
                    foreach ($I3 as &$l3) { $l3 = htmlencode($l3); }
                    $checked = !empty($data['fieldefault']) ? (instr($data['fieldefault'],$I3[1]) ? ' checked="checked"' : null) : null;
                    $I1.= '<input name="'.$data['fieldename'].'[]" id="'.$data['fieldename'].'_'.$I3[0].'" type="radio" value="'.$I3[1].'"'.$checked.' /><label for="'.$data['fieldename'].'_'.$I3[0].'">'.$I3[0].'</label>';
                }
                $I1.= '</span>';
                break;
            case 'checkbox': 
                $I1 = '<span>';
                $I2 = explode("\n",$data['fieldvalue']);
                foreach ($I2 as $v) {
                    $I3 = explode(":",$v);
                    foreach ($I3 as &$l3) { $l3 = htmlencode(trim($l3)); }
                    $checked = !empty($data['fieldefault']) ? (instr($data['fieldefault'],$I3[1]) ? ' checked="checked"' : null) : null;
                    $I1.= '<input name="'.$data['fieldename'].'[]" id="'.$data['fieldename'].'_'.$I3[0].'" type="checkbox" value="'.$I3[1].'"'.$checked.' /><label for="'.$data['fieldename'].'_'.$I3[0].'">'.$I3[0].'</label> ';
                }
                $I1.= '</span>';
                break;
            case 'select': 
                $I1 = '<select name="'.$data['fieldename'].'" id="'.$data['fieldename'].'">';
                $I2 = explode("\n",$data['fieldvalue']);
                foreach ($I2 as $v) {
                    $I3 = explode(":",$v);
                    foreach ($I3 as &$l3) { $l3 = htmlencode(trim($l3)); }
                    $selected = !empty($data['fieldefault']) ? ((string)$data['fieldefault']==(string)$I3[1] ? ' selected="selected"' : null) : null;
                    $I1.= '<option value="'.$I3[1].'"'.$selected.'>'.$I3[0].'</option>';
                }
                $I1.= '</select>';
                break;
            case 'basic': 
                $I1 = $module->editor($data['fieldename'],$data['fieldefault'],array(
                    'toolbar' => 'Basic',
                    'width'   => '450px',
                    'height'  => '150px',
                ));
                break;
            case 'editor': 
                $I1 = '<span><input type="checkbox" name="setfirst" id="setfirst" onclick="$(this).setFirst(\'#img\',\''.$_SERVER["HTTP_HOST"].C('SITE_BASE').'\');" /><label for="setfirst">设第一幅图为缩略图</label></span>';
				$I1.= $module->editor($data['fieldename'],$data['fieldefault']);
                break;
            case 'date': 
                try{
                    $default = eval('return '.$data['fieldefault'].';');
                } catch (Error $err){
                    $default = date('Y-m-d',time());
                }
                $I1 = '<input class="in2 date-pick" type="text" id="'.$data['fieldename'].'" name="'.$data['fieldename'].'" value="'.$default.'" />';
                break;
            case 'upfile': 
                $I1 = '<input class="in4" type="text" id="'.$data['fieldename'].'" name="'.$data['fieldename'].'" maxlength="'.$data['fieldlength'].'" value="'.$data['fieldefault'].'" />&nbsp;<button type="button" onclick="$(\'#'.$data['fieldename'].'\').browseFiles(\''.url('System','browseFiles').'\',\''.C('UPFILE_PATH').'\',true);">'.L('common/browse').'</button>';
                break;
        }
        return $I1;
    }
}
?>