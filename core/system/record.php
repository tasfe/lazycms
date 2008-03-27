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
 * 记录集解析类
 *
 * @copyright   Copyright (c) 2007-2008 lazycms.net All rights reserved.
 * @author      Lukin <mylukin@gmail.com>
 */
// Record *** *** www.LazyCMS.net *** ***
class Record extends Lazy{

    public $length;
    public $page;
    public $size;
    public $result;
    public $totalRows;
    public $totalPages;
    
    private $but;
    private $td;
    private $thead;
    private $tbody;
    private $fetch;

    private $url;
    private $action;

    private $_db;
    private $_html;
    // __construct *** *** www.LazyCMS.net *** ***
    function __construct(){
        $this->action = url();
        $this->url  = url();
        $this->page = !empty($_REQUEST['page']) ? $_REQUEST['page'] : 1;
        $this->size = !empty($_REQUEST['size']) ? $_REQUEST['size'] : 10;
        $this->_db  = getConn();
    }
    //__set *** *** www.LazyCMS.net *** ***
    private function __set($name ,$value){
        if(property_exists($this,$name)){
            switch ($name) {
                case 'td':
                    $this->$name .= "'<td>'+".$value."+'</td>'+";
                    break;
                case 'but' :
                    $this->$name = '<div class="lz_but">'.$value.'</div>';
                    break;
                case 'tbody':
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
    public function create($sql,$size=0){
        if (!empty($size)) { $this->size = $size; }
        $this->totalRows  = $this->_db->count($sql);
        $this->totalPages = ceil($this->totalRows/$this->size);
        $this->totalPages = ((int)$this->totalPages == 0) ? 1 : $this->totalPages;
        if ((int)$this->page < (int)$this->totalPages) {
            $this->length = $this->size;
        } elseif ((int)$this->page >= (int)$this->totalPages) {
            $this->length = fmod($this->totalRows,$this->size);
        }
        if ((int)$this->page > (int)$this->totalPages) {
            $this->page = $this->totalPages;
        }
        $sql .= ' LIMIT '.$this->size.' OFFSET '.($this->page-1)*$this->size.';';
        $this->result = $this->_db->query($sql);
    }
    // result *** *** www.LazyCMS.net *** ***
    public function result($type=1){
         return $this->_db->fetch($this->result,$type);
    }
    // button *** *** www.LazyCMS.net *** ***
    public function button($l1=null){
        if ((int)$this->length > 0) {
            $disabled = null;
        } else {
            $disabled = ' disabled="disabled"';
        }
        $I1 = '<div class="button"><button onclick="checkALL(this,\'all\');" type="button"'.$disabled.'>'.L('common/selectall').'</button><button onclick="checkALL(this);" type="button"'.$disabled.'>'.L('common/reselect').'</button>';
        $l2 = '<button onclick="$(this).gm(\'delete\',{},{},\''.$this->action.'\');" type="button"'.$disabled.'>'.L('common/delete').'</button>';
        $l3 = '<option value="-" class="gray">&nbsp; ------------- &nbsp;</option>';
        if (!empty($l1)) {
            $I2 = explode('|',$l1);
            if (count($I2)>3) {
                $I1.= $l2."<select onchange=\"$(this).gm(this.value,{},{},'".$this->action."');if(this.options[this.selectedIndex].value){this.options[0].selected=true;}\"".$disabled."><option value=\"-\">".L('common/moreaction')."</option>";
                foreach ($I2 as $v) {
                    if ($v=='-') {
                        $I1.= $l3;
                    } else {
                        $I3 = explode(':',$v);
                        $I1.= "<option value=\"".$I3[0]."\">&nbsp;&nbsp;".$I3[1]."</option>";
                    }
                }
                $I1.= "</select>";
            } else {
                foreach ($I2 as $v) {
                    if ($v!='-') {
                        $I3 = explode(':',$v);
                        $I1.= "<button onclick=\"$(this).gm('".$I3[0]."',{},{},'".$this->action."');\" type=\"button\"".$disabled.">".$I3[1]."</button>";
                    }
                }
                $I1.= $l2;
            }
        } else {
            $I1.= $l2;
        }
        $I1.= '</div>';
        return $I1;
    }
    // plist *** *** www.LazyCMS.net *** ***
    public function plist(){
        return pagelist($this->url,$this->page,$this->totalPages,$this->totalRows);
    }
    // open *** *** www.LazyCMS.net *** ***
    public function open($l1=1){
        if ($l1==1) {
            $this->_html = "<form name=\"form1\" action=\"".$this->action."\" class=\"lz_form\">";
        }
        $this->_html.= "<script type=\"text/javascript\">var lz_delete = '".L('confirm/delete')."';var lz_clear = '".L('confirm/clear')."';var lz_but = '".t2js($this->but)."';document.writeln(lz_but);function ll(){var K = ll.arguments;document.writeln('<tr>'+".$this->td."'</tr>');};function lll(){ var K = lll.arguments; return '<tr>'+".$this->td."'</tr>';}</script>";
        $this->_html.= "<table class=\"lz_table\">";
    }
    // close *** *** www.LazyCMS.net *** ***
    public function close($l1=1){
        $this->_html.= "<thead>".$this->thead."</thead>";
        $this->_html.= "<tbody><script type=\"text/javascript\">".$this->tbody."</script></tbody>";
        $this->_html.= "</table><script type=\"text/javascript\">document.writeln(lz_but);</script>";
        if ($l1==1) { $this->_html.= "</form>"; }
    }
}
?>