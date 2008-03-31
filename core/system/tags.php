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
 * LazyCMS 标签解析类
 *
 * @copyright   Copyright (c) 2007-2008 lazycms.net All rights reserved.
 * @author      Lukin <mylukin@gmail.com>
 */
// Template *** *** www.LazyCMS.net *** ***
class Tags extends Lazy{
    private $_inValue;
    // clear *** *** www.LazyCMS.net *** ***
    public function clear(){
        $this->_inValue = array();
    }
    // read *** *** www.LazyCMS.net *** ***
    public function read($l1,$l2=null){
        //$l1:外部模板  $l2:内部模板
        $l3 = LAZY_PATH;
        $l4 = C('TEMPLATE_DEF');
        $l5 = $l3.$l1;
        $l5 = is_file($l5) ? $l5 : $l3.C('TEMPLATE_PATH').'/'.$l4;
        $l6 = loadFile($l5); $l7 = null;
        if (strlen($l2)!==0) {
            $l7 = $l3.$l2;
            if (is_file($l7)) {
                $l8 = loadFile($l7);
            }
        }
        $l9 = empty($l8) ? $l6 : replace('/(\{lazy:)(inside) {0,}?(\/\})/i',$l8,$l6);
        return $this->format($l9);
    }
    // value *** *** www.LazyCMS.net *** ***
    public function value($l1,$l2){
        $this->_inValue[$l1] = $l2;
    }
    // create *** *** www.LazyCMS.net *** ***
    public function create($l1,$l2=null){
        $I1 = $l1;
        if (!empty($l2)) { $this->_inValue = $l2; }
        if (preg_match_all('/\{lazy\:(.+?[^{])\/\}|\{lazy\:(.+?)\}((.|\n)+?)\{\/lazy\}/i',$I1,$I2)) {
            foreach ($I2[0] as $v) {
                $I1 = str_replace($v,$this->parseTags($v),$I1); 
            }
        }
        return $I1;
    }
    // createhtm *** *** www.LazyCMS.net *** ***
    public function createhtm($l1,$l2=null){
        $I1 = $l1;
        if (!empty($l2)) { $this->_inValue = $l2; }
        if (preg_match_all('/(\(lazy\:).+?[^(](\/\))/i',$I1,$I2)) {
            foreach ($I2[0] as $v) {
                if (strpos($I1,$v)!==false){
                    $I1 = str_replace($v,$this->parseTags($v),$I1); 
                }
            }
        }
        return $I1;
    }
    // getValue *** *** www.LazyCMS.net *** ***
    public function getValue(){
        return $this->_inValue;
    }
    // format *** *** www.LazyCMS.net *** ***
    public function format($l1){
        return replace('/(<(script|link|img|input|embed|param|object|base|area|map|table|param).+?(src|href|background|value)\=.+?)(\.\.\/)*((images|js)\/.{0,}?>)/i','${1}'.C('SITE_BASE').C('TEMPLATE_PATH').'/${5}',$l1);
    }
    // parseTags *** *** www.LazyCMS.net *** ***
    public function parseTags($tags){
        static $i = 1;
        $module  = getObject();
        $tags    = htmldecode($tags);
        $tagName = sect($tags,"(lazy\:)","( |\/|\}|\))","");
        switch ((string)$tagName) {
            case 'sitename' : case 'sitemail' :
                $I1 = $module->system[$tagName];
                break;
            case 'cms':
                $I1 = '<p id="lazycms">Powered by: <a href="http://www.lazycms.net" style="font-weight:bold" target="_blank">'.$module->system['systemname'].'</a> <span>'.$module->system['systemver'].'</span></p>';
                break;
            case 'inst':
                $I1 = C('SITE_BASE');
                break;
            case 'page':
                $I1 = C('SITE_BASE').C('PAGES_PATH');
                break;
            case 'version':
                $I1 = $module->system['systemver'];
                break;
            case '++':
                $I1 = $i++;
                break;
            case 'keywords': case 'keyword':
                $I1 = $this->parseAtt($tags,'keywords');
                if (strlen($I1)==0) { 
                    $I1 = $this->parseAtt($tags,"title");
                }
                break;
            case 'description':
                $I1 = $this->parseAtt($tags,'description');
                if (strlen($I1)==0) { 
                    $I1 = $this->parseAtt($tags,"title");
                }
                break;
            case 'guide':
                $I1 = $this->parseAtt($tags,"guide");
                if (strlen($I1)==0) {
                    $I1 = '<a href="'.C('SITE_BASE').'">'.$module->system['sitename'].'</a> &gt;&gt; '.$this->parseAtt($tags,"title");
                } else {
                    $I1 = '<a href="'.C('SITE_BASE').'">'.$module->system['sitename'].'</a> &gt;&gt; '.$I1;
                }
                break;
            default :
                if (class_exists($tagName)) {
                    eval('$I1 = '.$tagName.'::tags($tags,$this->_inValue);');
                    $I1 = $this->parseAtt($tags,$I1,false);
                } else {
                    $I1 = $this->parseAtt($tags,$tagName);
                }
                break;
        }
        return $I1;
    }
    // parse *** *** www.LazyCMS.net *** ***
    public function parseValue($l1){
        $I1 = isset($this->_inValue[$l1]) ? $this->_inValue[$l1] : null;
        return decode($I1);
    }
    //parseAtt *** *** www.LazyCMS.net *** ***
    public function parseAtt($l1,$l2,$l3=true){
        if ($l3) {
            $l4 = $this->parseValue($l2);
        } else {
            $l4 = $l2;
        }
        if (strlen($l4)==0) { return ; }
        $I1 = $l4;
        
        $l5 = sect($l1,'size="','"','');//size
        if (validate($l5,2)) {
            if ((int)len($l4)>(int)$l5) {
                $I1 = cnsubstr($l4,$l5).'...';
            } else{
                $I1 = $l4;
            }
        }
        
        $l6 = sect($l1,'left="','"','');
        if (validate($l6,2)){
            $I1 = leftHTML($l4,$l6);
        }
        if (is_numeric($l4)) {
            $l7 = sect($l1,'mode="','"','');//datemode
            if ($l7!='') {
                $I1 = formatDate($l4,$l7);
            }
        }
        //image 暂时不写
        
        $l8 = sect($l1,'code="','"','');//code
        if (strlen($I1)>0) {
            $l8 = strtolower($l8);
            switch ($l8) {
                case 'javascript': case 'js':
                    $I1 = t2js($I1);
                    break;
                case 'xmlencode': case 'xml':
                    $I1 = xmlencode($I1);
                    break;
                case 'urlencode': case 'url':
                    $I1 = urlencode($I1);
                    break;
                case 'htmlencode':
                    $I1 = htmlencode($I1);
                    break;
            }
        }

        $l9 = sect($l1,'fun="','"','');//function
        if (strlen($l9)>0) {
            if (strpos($l9,'@me')!==false) { 
                $l9 = str_replace('@me',$I1,$l9);
            }
            eval('$I1 = '.$l9.';');
        }
        return $I1;
    }
    // getList *** *** www.LazyCMS.net *** ***
    public function getList($l1,$l2,$l3){
        $l4 = null;
        if (preg_match('/(\{lazy:'.$l2.').+?type="list".{0,}?(\})(.|\n)+?\{\/lazy\}/i',$l1,$I2)) {
            $l4 = $I2[0];
        }
        if ($l3===0) {
            $l5 = sect($l4,'(\})','(\{\/lazy\})','');
        } elseif ($l3===1){
            $l5 = $l4;
        } else{
            $l5 = $this->getLabel($l4,$l3);
        }
        return $l5;
    }
    // getLabel *** *** www.LazyCMS.net *** ***
    public function getLabel($l1,$l2){
        if ($l2===0) {
            $I1 = sect($l1,'(\})','(\{\/lazy\})','');
        } else {
            if (preg_match('/\{lazy\:(.+?)\}/i',$l1,$I2)) {
                $l3 = $I2[0];
            } else{
                $l3 = null;
            }
            $I1 = sect($l3,$l2.'="','"','');
        }
        if (validate($I1,2)==false) {
            switch ((string)$l2) {
                case 'number':
                    $I1 = 20;
                    break;
                case 'zebra':
                    $I1 = 1;
                    break;
            }
        }
        return $I1;
    }
}
?>