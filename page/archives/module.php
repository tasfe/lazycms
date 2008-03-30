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
 * Module 层
 *
 * @copyright   Copyright (c) 2007-2008 lazycms.net All rights reserved.
 * @author      Lukin <mylukin@gmail.com>
 */
class Archives{
    // getTopSortId *** *** www.LazyCMS.net *** ***
    static function getTopSortId(){
        $db  = getConn();
        $res = $db->query("SELECT `sortid`,`sortname` FROM `#@_sort` WHERE `sortid1` = '0' ORDER BY `sortorder` DESC,`sortid` DESC;");
        if ($data = $db->fetch($res,0)) {
            return $data[0];
        } else {
            return 0;
        }
    }
    // getModel *** *** www.LazyCMS.net *** ***
    static function getModel($l1){
        $db  = getConn();
        $res = $db->query("SELECT * FROM `#@_sort` AS `s` LEFT JOIN `#@_model` AS `m` ON `s`.`modelid` = `m`.`modelid` WHERE `s`.`sortid` = '{$l1}';");
        if ($data = $db->fetch($res)) {
            return $data;
        } else {
            return false;
        }
    }
    // getFields *** *** www.LazyCMS.net *** ***
    static function getFields($l1){
        $modeid = $l1;
        $fields = array();
        $db    = getConn();
        $where = $db->quoteInto('WHERE `modelid` = ?',$modeid);
        $res   = $db->query("SELECT * FROM `#@_fields` {$where};");
        while ($data = $db->fetch($res)) {
            $fields[] = $data['fieldename'];
        }
        return $fields;
    }
    // __sort *** *** www.LazyCMS.net *** ***
    static function __sort($l1,$l2,$l3=0,$l4=null){
        // $l1:sortid, $l2:current sortid, $l3:Space, $l4:selected
        $nbsp = null; $I1 = null;
        for ($i=0; $i<$l3; $i++) {
            $nbsp .= "&nbsp; &nbsp;";
        }
        $db  = getConn();
        $res = $db->query("SELECT `sortid`,`sortname` FROM `#@_sort` WHERE `sortid1` = '{$l1}' ORDER BY `sortorder` DESC,`sortid` DESC;");
        while ($data = $db->fetch($res,0)) {
            if ($l2 != $data[0]) {
                $selected = ((int)$l4 == (int)$data[0]) ? ' selected="selected"' : null;
                $I1 .= '<option value="'.$data[0].'"'.$selected.'>'.$nbsp.'├ '.$data[1].'</option>';
                if ($db->result("SELECT count(`sortid`) FROM `#@_sort` WHERE `sortid1`='{$data[0]}';") > 0) {
                    $I1 .= self::__sort($data[0],$l2,$l3+1,$l4);
                }
            }
        }
        return $I1;
    }
    // getData *** *** www.LazyCMS.net *** ***
    static function getData($l1,$l2){
        $db    = getConn(); $I1 = array();
        $where = $db->quoteInto('WHERE `aid` = ?',$l1);
        $res   = $db->query("SELECT * FROM `{$l2}` {$where};");
        if (!$data = $db->fetch($res)) {
            return false;
        }
        return $data;
    }
    // __model *** *** www.LazyCMS.net *** ***
    static function __model($l1){
        $db  = getConn(); $I1 = null;
        $res = $db->query("SELECT `modelid`,`modelname`,`modelename` FROM `#@_model` WHERE 1 ORDER BY `modelid` ASC;");
        while ($data = $db->fetch($res,0)) {
            $selected = ($l1 == $data[0]) ? ' selected="selected"' : null;
            $I1 .= '<option value="'.$data[0].'"'.$selected.' name="'.$data[2].'">'.$data[1].'['.$data[2].']</option>';
        }
        return $I1;
    }
    // showSort *** *** www.LazyCMS.net *** ***
    static function showSort($l1){
        $sortid = $l1;
        $db     = getConn();       
        $where  = $db->quoteInto("WHERE `sortid` = ?",$sortid);
        $res    = $db->query("SELECT `sortpath` FROM `#@_sort` {$where}");
        if ($data = $db->fetch($res,0)) {
            if (C('SITE_MODE')) {
                return url(C('CURRENT_PATH'),'ShowSort','sortid='.$sortid);
            } else {
                return C('SITE_BASE').$data[0];
            }
        }
    }
    // createSort *** *** www.LazyCMS.net *** ***
    static function createSort($l1,$l2=null){
        if (C('SITE_MODE')) { return ; }
        $sortid = $l1;
        $db     = getConn();       
        $res    = $db->query("SELECT `sortid`,`sortpath` FROM `#@_sort` WHERE `sortid` IN({$sortid})");
        while ($data = $db->fetch($res,0)) {
            $outHTML = self::viewSort($data[0]);
            // 文件路径修改，删除老文件
            if (!empty($l2) && $data[1]!=$l2) {
                @unlink(LAZY_PATH.$l2.'/'.C('SITE_INDEX'));
                $paths = explode('/',$l2);
                rmdirs(LAZY_PATH.$paths[0],false);
            }
            // 生成新文件
            mkdirs(LAZY_PATH.$data[1]);
            saveFile(LAZY_PATH.$data[1].'/'.C('SITE_INDEX'),$outHTML);
        }
    }
    // viewSort *** *** www.LazyCMS.net *** ***
    static function viewSort($l1,$page=1){
        $sortid = $l1; $tmpList = null;
        $db     = getConn();
        $model  = self::getModel($sortid);
        if (!$model) { return ;}
        $fields = self::getFields($model['modelid']);

        $path = self::showSort($sortid);
        $tag  = O('Tags');
        $HTML = $tag->read($model['sorttemplate1'],$model['sorttemplate2']);
        $HTMList = $tag->getList($HTML,$model['modelename'],1);
        $jsHTML  = $tag->getLabel($HTMList,0);
        $jsOrder = $tag->getLabel($HTMList,'order');
        $jsOrder = strtoupper($jsOrder)=='ASC' ? 'ASC' : 'DESC';
        $jsNumber= floor($tag->getLabel($HTMList,'number'));
        $zebra   = $tag->getLabel($HTMList,'zebra');
        $rand    = chr(3).salt(20).chr(2);//随机出来的替换参数
        $randpl  = chr(3).salt(16).chr(2);

        // 把 HTML 中的{lazy:...type=list/}标签替换为一个随机的标签；pagelist设置为一个随机标签
        $HTML = str_replace($HTMList,$rand,$HTML);

        // 替换模板中的标签
        $tag->clear();
        $tag->value('title',encode(htmlencode($model['sortname'])));
        $tag->value('sortname',encode(htmlencode($model['sortname'])));
        $tag->value('sortpath',encode($path));
        $tag->value('path',encode($path));
        $tag->value('keywords',encode(htmlencode($model['keywords'])));
        $tag->value('description',encode(htmlencode($model['description'])));
        $tag->value('pagelist',encode($randpl));
        
        $HTML = $tag->create($HTML);

        $res = $db->query("SELECT * FROM `".$model['maintable']."` AS `a` LEFT JOIN `".$model['addtable']."` AS `b` ON `a`.`id` = `b`.`aid` WHERE `a`.`sortid` = '{$sortid}' ORDER BY `a`.`order` {$jsOrder},`a`.`sortid` {$jsOrder};");
        $i = 1;
        while ($data = $db->fetch($res)) {
            $tag->clear();
            $tag->value('id',encode($data['id']));
            $tag->value('sortid',encode($data['sortid']));
            $tag->value('sortname',encode(htmlencode($model['sortname'])));
            $tag->value('sortpath',encode($path));
            $tag->value('title',encode(htmlencode($data['title'])));
            $tag->value('path',encode(self::showArchive($data['id'],$model)));
            $tag->value('date',encode($data['date']));
            $tag->value('zebra',encode(fmod($zebra,$i) ? 1 : 0));
            foreach ($fields as $k) {
                $tag->value($k,encode($data[$k]));
            }
            $tmpList.= $tag->createhtm($jsHTML);
            $i++;
        }
        $outHTML = str_replace($rand,$tmpList,$HTML);
        return $outHTML;
    }
    // showArchive *** *** www.LazyCMS.net *** ***
    static function showArchive($l1,$l2){
        $aid   = $l1;
        $model = $l2;
        $db    = getConn();       
        $where = $db->quoteInto("WHERE `b`.`id` = ?",$aid);
        $res   = $db->query("SELECT `a`.`sortpath`,`b`.`path` FROM `#@_sort` AS `a` LEFT JOIN `".$model['maintable']."` AS `b` ON `a`.`sortid` = `b`.`sortid` {$where}");
        if ($data = $db->fetch($res,0)) {
            if (C('SITE_MODE')) {
                return url(C('CURRENT_PATH'),'ShowArchive','aid='.$aid);
            } else {
                return C('SITE_BASE').$data[0].'/'.$data[1];
            }
        }
    }
    // isOpen *** *** www.LazyCMS.net *** ***
    static function isOpen($l1){
        $db    = getConn();
        $state = $db->result("SELECT `sortopen` FROM `#@_sort` WHERE `sortid` = '{$l1}';");
        $state = (string)$state == "1" ? 'true' : 'false';
        return $state;
    }
    // isSub *** *** www.LazyCMS.net *** ***
    static function isSub($l1){
        $db    = getConn();
        $state = $db->result("SELECT count(*) FROM `#@_sort` WHERE `sortid1` = '{$l1}';") > 0 ? '1' : '2';
        return $state;
    }
    // subSort *** *** www.LazyCMS.net *** ***
    static function subSort($l1,$l2=1){
        $state = self::isSub($l1);
        $onclick = ((int)$state == 1) ?' onclick="$(this).addsub('.$l1.','.$l2.');"' : null;
        if (self::isOpen($l1)=='true') {
            $state = 'loading';
        } else {
            $state = 'os/dir'.$state;
        }
        return t2js('<a href="javascript:;"'.$onclick.' id="dir'.$l1.'"><img src="'.LAZY_PATH.C('PAGES_PATH').'/system/images/'.$state.'.gif" class="os" /></a>');
    }
    // tags *** *** www.LazyCMS.net *** ***
    static function tags($tags,$inValue){ 
        return true;
    }
    // instsql *** *** www.LazyCMS.net *** ***
    static function instSQL(){
        return ;
    }
}