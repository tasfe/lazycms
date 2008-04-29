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
    // __construct *** *** www.LazyCMS.net *** ***
    public function __construct(){
        $this->clear();
    }
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
        $l9 = $this->format($l9);
        $js = '<script type="text/javascript" src="'.C('SITE_BASE').C('PAGES_PATH').'/system/js/jquery.js"></script>'.chr(10);
        $js.= '<script type="text/javascript" src="'.C('SITE_BASE').C('PAGES_PATH').'/system/js/jquery.common.js"></script>';
        if (preg_match('/<\/title>/i',$l9)) {
            $I1 = replace('/<\/title>/i',"\$0\n{$js}",$l9,1);
        } else {
            $I1 = replace('/<head>/i',"\$0\n{$js}",$l9,1);
        }
        return $I1;
    }
    // value *** *** www.LazyCMS.net *** ***
    public function value($l1,$l2){
        $this->_inValue[$l1] = $l2;
    }
    // create *** *** www.LazyCMS.net *** ***
    public function create($l1,$l2=null){
        $I1 = $l1;
        if (preg_match_all('/\{lazy\:(.+?[^{])\/\}|\{lazy\:(.+?)\}((.|\n)+?)\{\/lazy\}/i',$I1,$I2)) {
            foreach ($I2[0] as $v) {
                $I1 = str_replace($v,$this->parseTags($v,$l2),$I1); 
            }
        }
        return decode($I1);
    }
    // createhtm *** *** www.LazyCMS.net *** ***
    public function createhtm($l1,$l2=null){
        $I1 = $l1;
        if (preg_match_all('/(\(lazy\:).+?[^(](\/\))/i',$I1,$I2)) {
            foreach ($I2[0] as $v) {
                $I1 = str_replace($v,$this->parseTags($v,$l2),$I1); 
            }
        }
        return decode($I1);
    }
    // getValue *** *** www.LazyCMS.net *** ***
    public function getValue($l1=null){
        if (empty($l1)) {
            return $this->_inValue;
        } else {
            return $this->_inValue[$l1];
        }
    }
    // format *** *** www.LazyCMS.net *** ***
    public function format($l1){
        return replace('/(<(((script|link|img|input|embed|object|base|area|map|table|td|th|tr).+?(src|href|background))|((param).+?(src|value)))=([^\/]+?))((images|inside)\/.{0,}?\>)/i','${1}'.C('SITE_BASE').C('TEMPLATE_PATH').'/${10}',$l1);
    }
    // parseTags *** *** www.LazyCMS.net *** ***
    public function parseTags($tags,$inValue){
        static $i = 1; 
        $module  = getObject();
        $tags    = htmldecode($tags);
        $tagName = sect($tags,"(lazy\:)","( |\/|\}|\))");
        switch ((string)$tagName) {
            case 'sitename' : case 'sitemail' :
                $I1 = $module->system[$tagName];
                break;
            case 'cms': case 'lazycms':
                $I1 = '<span id="lazycms">Powered by: <a href="http://www.lazycms.net" style="font-weight:bold" target="_blank">'.$module->system['systemname'].'</a> <span>'.$module->system['systemver'].'</span></span>';
                break;
            case 'inst':
                $I1 = C('SITE_BASE');
                break;
            case 'page':
                $I1 = C('SITE_BASE').C('PAGES_PATH').'/';
                break;
            case 'version':
                $I1 = $module->system['systemver'];
                break;
            case '++':
                $I1 = $i++;
                break;
            case 'keywords': case 'keyword':
                $I1 = $this->parseAtt($tags,$inValue,'keywords');
                if (strlen($I1)==0) { 
                    $I1 = $this->parseAtt($tags,$inValue,'title');
                }
                break;
            case 'description':
                $I1 = $this->parseAtt($tags,$inValue,'description');
                if (strlen($I1)==0) { 
                    $I1 = $this->parseAtt($tags,$inValue,'title');
                }
                break;
            case 'guide':
                $I1 = $this->parseAtt($tags,$inValue,'guide');
                $I2 = $this->getLabel($tags,'name');
                if (strlen($I2)==0) { $I2 = L('common/home'); }
                if (strlen($I1)==0) {
                    $I1 = '<a href="'.C('SITE_BASE').'">'.$I2.'</a> &gt;&gt; '.$this->parseAtt($tags,$inValue,'title');
                } else {
                    $I1 = '<a href="'.C('SITE_BASE').'">'.$I2.'</a> &gt;&gt; '.$I1;
                }
                break;
            default :
                if (class_exists('Archives')) {
                    $I1 = Archives::tags($tags,$inValue);
                }
                
                if (empty($I1)) {
                    if (class_exists($tagName)) {
                        $obj = new $tagName();
                        if (method_exists($obj,'tags')) {
                            $I1 = $obj->tags($tags,$inValue);
                        } else {
                            $I1 = $tagName;
                        }
                        unset($obj);
                        // 无需再次进行解析
                        //$I1 = $this->parseAtt($tags,$inValue,$I1);
                    } else {
                        $I1 = $this->parseAtt($tags,$inValue,$tagName);
                    }
                }
                break;
        }
        return $I1;
    }
    // parse *** *** www.LazyCMS.net *** ***
    public function parseValue($l1,$l2){
        $I1 = $l2; return isset($I1[$l1]) ? decode($I1[$l1]) : null;
    }
    //parseAtt *** *** www.LazyCMS.net *** ***
    public function parseAtt($l1,$l2,$l3){
        $l4 = $this->parseValue($l3,$l2);
        if (strlen($l4)==0) {
            // no image
            if (strtolower($l3) == 'image') {
                return C('SITE_BASE').C('PAGES_PATH').'/system/images/notpic.gif';
            }
            return ; 
        }
        $I1 = $l4;

        // size
        $l5 = sect($l1,'size="','"');
        if (validate($l5,2)) {
            if ((int)len($l4)>(int)$l5) {
                $I1 = cnsubstr($l4,$l5).'...';
            } else{
                $I1 = $l4;
            }
        }

        // left
        $l5 = sect($l1,'left="','"');
        if (validate($l5,2)){
            $I1 = leftHTML($l4,$l5);
        }

        // datemode
        if (is_numeric($l4)) {
            $l5 = sect($l1,'mode="','"');
            if ($l5!='') {
                $I1 = formatDate($l4,$l5);
            }
        }
        
        // image
        if (strtolower($l3) == 'image') {
            $imgWidth  = sect($l1,'width="','"');
            $imgHeight = sect($l1,'height="','"');
            if (is_file(LAZY_PATH.$l4)) {
                if (is_numeric($imgWidth) && is_numeric($imgHeight) && function_exists('gd_info')) {
                    import('system.image'); $I2 = pathinfo($l4); 
                    $l8 = $I2['dirname'].'/TN/'.substr($I2['basename'],0,strrpos($I2['basename'], '.'))."_{$imgWidth}x{$imgHeight}.".$I2['extension'];
                    $I1 = C('SITE_BASE').$l8;
                    if (!is_file($l8)) {
                        Image::thumb(LAZY_PATH.$l4,LAZY_PATH.$l8,$imgWidth,$imgHeight);
                    }
                } else {
                    $I1 = C('SITE_BASE').$l4;
                }    
            } else {
                $I1 = C('SITE_BASE').C('PAGES_PATH').'/system/images/notpic.gif';
            }
        }

        // code
        $l6 = sect($l1,'code="','"');
        if (strlen($I1)>0) {
            switch (strtolower($l6)) {
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

        // 关键字加链接
        if ($l6!='htmlencode') {
            $l7 = sect($l1,'url="','"');
            $I2 = explode(',',$I1);
            if (strlen($l7)>0) {
                $l8 = $this->createhtm($l7); $I1 = null;
                foreach ($I2 as $key=>$val) {
                    $I1.= "<a href=\"{$l8}".urlencode($val)."\">{$val}</a>";
                }
            }
        }

        // 内容加链接
        $l5 = sect($l1,'key="','"');
        if (strlen($l5)>0) {
            $module = getObject();
            $keywords = $module->system['sitekeywords'];
            if (!empty($keywords)) {
                $l5 = $this->createhtm($l5);
                $keywords = str_replace(',','|',$keywords);
                $I1 = preg_replace_callback("/({$keywords})(?![^<]*<\/(a|b|strong|i|em)>)/i",create_function('$l1','return "<a href=\"'.$l5.'".urlencode($l1[0])."\" target=\"_blank\">".$l1[0]."</a>";'),$I1);
            }
        }

        // function
        $l5 = sect($l1,'fun="','"');
        if (strlen($l5)>0) {
            if (strpos($l5,'@me')!==false) { 
                $l5 = preg_replace("/'@me'|\"@me\"|@me/isU",'$I1',$l5);
            }
            eval('$I1 = '.$l5.';');
        }
        return encode($I1);
    }
    // getList *** *** www.LazyCMS.net *** ***
    public function getList($l1,$l2,$l3){
        $l4 = null;
        if (preg_match('/(\{lazy:'.$l2.').+?type="(list|sub|current)".{0,}?(\})(.|\n)+?\{\/lazy\}/i',$l1,$I2)) {
            $l4 = $I2[0];
        }
        if ($l3===0) {
            $l5 = sect($l4,'(\})','(\{\/lazy\})');
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
            $I1 = sect($l1,'(\})','(\{\/lazy\})');
        } else {
            if (preg_match('/\{lazy\:(.+?)\}/i',$l1,$I2)) {
                $l3 = $I2[0];
            } else{
                $l3 = null;
            }
            $I1 = sect($l3,$l2.'="','"');
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