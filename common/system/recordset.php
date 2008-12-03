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
 * 记录显示封装类
 *
 */
class Recordset{
    var $url;
    var $action;
    
    var $length;
    var $page;
    var $size;
    var $result;
    var $totalRows;
    var $totalPages;
    
    var $but;
    var $_td;
    var $thead;
    var $_tbody;
    var $_fetch;

    var $_db;
    var $_hl;

    /**
     * 兼容PHP5模式
     *
     */
    function __construct(){
        $this->action = null;
        $this->url  = null;
        $this->page = !empty($_REQUEST['page']) ? $_REQUEST['page'] : 1;
        $this->size = !empty($_REQUEST['size']) ? $_REQUEST['size'] : 15;
        $this->_db   = get_conn();
    }
    /**
     * 初始化
     *
     * @return Recordset
     */
    function Recordset(){
        $this->__construct();
    }
    /**
     * 执行SQL
     *
     * @param string $sql
     * @param int    $size
     */
    function create($sql,$size=0){
        if (!empty($size)) { $this->size = $size; }
        $this->totalRows  = $this->_db->count($sql);
        $this->totalPages = ceil($this->totalRows/$this->size);
        $this->totalPages = ((int)$this->totalPages == 0) ? 1 : $this->totalPages;
        if ((int)$this->page < (int)$this->totalPages) {
            $this->length = $this->size;
        } elseif ((int)$this->page >= (int)$this->totalPages) {
            $this->length = $this->totalRows - (($this->totalPages-1) * $this->size);
        }
        if ((int)$this->page > (int)$this->totalPages) {
            $this->page = $this->totalPages;
        }
        $sql .= ' LIMIT '.$this->size.' OFFSET '.($this->page-1)*$this->size.';';
        $this->result = $this->_db->query($sql);
    }
    /**
     * 取得结果集
     *
     * @param int $type
     * @return array
     */
    function result($type=1){
        if (is_resource($this->result) || is_object($this->result)) {
            return $this->_db->fetch($this->result,$type);
        }
    }
    /**
     * 显示按钮
     *
     * @param string $p1
     * @return string
     */
    function button($p1=null){
        if ((int)$this->length > 0) {
            $disabled = null;
        } else {
            $disabled = ' disabled="disabled"';
        }
        $R = '<div class="button"><button onclick="checkALL(this,\'all\');" type="button"'.$disabled.'>'.l('Select all').'</button><button onclick="checkALL(this);" type="button"'.$disabled.'>'.l('Reset select').'</button>';
        $p2 = '<button onclick="$(this).gp(\'delete\');" type="button"'.$disabled.'>'.l('Delete').'</button>';
        if (!empty($p1)) {
            $R1 = explode('|',$p1);
            foreach ($R1 as $v) {
                if ($v!='-') {
                    $R2 = explode(':',$v);
                    $R.= '<button onclick="$(this).gp(\''.$R2[0].'\');" type="button"'.$disabled.'>'.$R2[1].'</button>';
                }
            }
        }
        $R.= $p2;
        $R.= '</div>';
        return $R;
    }
    /**
     * 显示分页列表
     *
     * @return string
     */
    function plist(){
        return pagelist($this->url,$this->page,$this->totalPages,$this->totalRows);
    }
    /**
     * 显示form表单头
     *
     */
    function open(){
        $this->_hl = '<form name="form1" id="form1"  action="'.$this->action.'" class="form">';
        if ((int)$this->length > 0) {
            $this->_hl.= "<script type=\"text/javascript\">var lazy_delete = '".l('Delete')."';var lazy_clear = '".l('Confirm clear')."';function E(){var K = arguments;document.writeln('<tr>'+".$this->_td."'</tr>');};function R(){ var K = arguments; return '<tr>'+".$this->_td."'</tr>';}</script>";    
        }
        $this->_hl.= '<table class="table" cellspacing="0">';
    }
    /**
     * 关闭form表单
     *
     */
    function close(){
        $this->_hl.= "<thead>".$this->thead."</thead><tbody>";
        if ((int)$this->length > 0) {
            $this->_hl.= '<script type="text/javascript">'.$this->_tbody.'</script>';
        } else {
            preg_match_all('/<th>[^>]+<\/th>/',$this->thead,$R);
            $this->_hl.= '<tr><td colspan="'.count($R[0]).'" class="tc">&nbsp;</td></tr>';
        }
        $this->_hl.= '</tbody></table>';
        $this->_hl.= '<div class="but">'.$this->but.'</div>';
        $this->_hl.= '</form>';
    }
    /**
     * 取得HTML代码
     *
     * @return string
     */
    function fetch(){
        return $this->_hl;
    }
    /**
     * 输出HTML代码
     *
     */
    function display(){
        echo $this->_hl;
    }
    /**
     * 设置td
     *
     */
    function td($value){
        $this->_td.= "'<td>'+".$value."+'</td>'+";
    }
    /**
     * 设置tbody
     *
     */
    function tbody($value){
        $this->_tbody.= $value."\n";
    }
}