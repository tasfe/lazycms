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
 * 记录集解析类
 *
 * @copyright   Copyright (c) 2007-2008 lazycms.net All rights reserved.
 * @author      Lukin <mylukin@gmail.com>
 * @date        2008-6-26
 */
// Recordset *** *** www.LazyCMS.net *** ***
class Recordset {
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

    private $db;
    private $hl;

    private $url;
    private $action;
    // __construct *** *** www.LazyCMS.net *** ***
    function __construct(){
        $this->action = null;
        $this->url  = null;
        $this->page = !empty($_REQUEST['page']) ? $_REQUEST['page'] : 1;
        $this->size = !empty($_REQUEST['size']) ? $_REQUEST['size'] : 15;
        $this->db   = get_conn();
    }
    // create *** *** www.LazyCMS.net *** ***
    public function create($sql,$size=0){
        if (!empty($size)) { $this->size = $size; }
        $this->totalRows  = $this->db->count($sql);
        $this->totalPages = ceil($this->totalRows/$this->size);
        $this->totalPages = ((int)$this->totalPages == 0) ? 1 : $this->totalPages;
        if ((int)$this->page < (int)$this->totalPages) {
            $this->length = $this->size;
        } elseif ((int)$this->page >= (int)$this->totalPages) {
            $this->length = fmod($this->totalRows,$this->size);
            if ($this->length==0) {
                $this->length = $this->size;
            }
        }
        if ((int)$this->page > (int)$this->totalPages) {
            $this->page = $this->totalPages;
        }
        $sql .= ' LIMIT '.$this->size.' OFFSET '.($this->page-1)*$this->size.';';
        $this->result = $this->db->query($sql);
    }
    // result *** *** www.LazyCMS.net *** ***
    public function result($type=1){
        if (is_resource($this->result) || is_object($this->result)) {
            return $this->db->fetch($this->result,$type);
        }
    }
    // button *** *** www.LazyCMS.net *** ***
    public function button($l1=null){
        if ((int)$this->length > 0) {
            $disabled = null;
        } else {
            $disabled = ' disabled="disabled"';
        }
        $I1 = '<div class="button"><button onclick="checkALL(this,\'all\');" type="button"'.$disabled.'>'.L('common/selectall').'</button><button onclick="checkALL(this);" type="button"'.$disabled.'>'.L('common/reselect').'</button>';
        $l2 = '<button onclick="$(this).gp(\'delete\');" type="button"'.$disabled.'>'.L('common/delete').'</button>';
        if (!empty($l1)) {
            $I2 = explode('|',$l1);
            foreach ($I2 as $v) {
                if ($v!='-') {
                    $I3 = explode(':',$v);
                    $I1.= '<button onclick="" type="button"'.$disabled.'>'.$I3[1].'</button>';
                }
            }
        }
        $I1.= $l2;
        $I1.= '</div>';
        return $I1;
    }
    // plist *** *** www.LazyCMS.net *** ***
    public function plist(){
        return pagelist($this->url,$this->page,$this->totalPages,$this->totalRows);
    }
    // open *** *** www.LazyCMS.net *** ***
    public function open(){
        $this->hl = '<form name="form1" id="form1"  action="'.$this->action.'" class="form">';
        $this->hl.= "<script type=\"text/javascript\">var lazy_delete = '".L('confirm/delete')."';var lazy_clear = '".L('confirm/clear')."';function ll(){var K = ll.arguments;document.writeln('<tr>'+".$this->td."'</tr>');};function lll(){ var K = lll.arguments; return '<tr>'+".$this->td."'</tr>';}</script>";
        $this->hl.= '<table class="table">';
    }
    // close *** *** www.LazyCMS.net *** ***
    public function close(){
        $this->hl.= "<thead>".$this->thead."</thead>";
        $this->hl.= '<tbody><script type="text/javascript">'.$this->tbody.'</script></tbody></table>';
        $this->hl.= '<div class="but">'.$this->but.'</div>';
        $this->hl.= '</form>';
    }
    // fetch *** *** www.LazyCMS.net *** ***
    public function fetch(){
        return $this->hl;
    }
    // display *** *** www.LazyCMS.net *** ***
    public function display(){
        echo $this->hl;
    }
    //__set *** *** www.LazyCMS.net *** ***
    private function __set($name ,$value){
        if(property_exists($this,$name)){
            switch ($name) {
                case 'td':
                    $this->$name.= "'<td>'+".$value."+'</td>'+";
                    break;
                case 'tbody':
                    $this->$name.= $value."\n";
                    break;
                default :
                    $this->$name = $value;
                    break;
            }
        }
    }
    //__get *** *** www.LazyCMS.net *** ***
    private function __get($name){
        if(isset($this->$name)){
            return $this->$name;
        } else {
            return null;
        }
    }
}
?>