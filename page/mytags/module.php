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
class MyTags{
    // view *** *** www.LazyCMS.net *** ***
    static function view($l1){
        $mtid = $l1;
        $db   = getConn();        
        $res  = $db->query("SELECT `mttext`,`mtext` FROM `#@_mytags` WHERE `mtid` = ?",$mtid);
        if ($data = $db->fetch($res,0)) {
            $tag  = O('Tags');
            $outHTML = $tag->create($tag->format($data[0]));
            if ($data[1]=='.js') {
                $outHTML = t2js($outHTML,true);
            }
            return $outHTML;
        }
    }
    // create *** *** www.LazyCMS.net *** ***
    static function create($l1,$l2=null){
        // 网站启动动态模式，不生成任何文件
        if (C('SITE_MODE')) { return ; }
        $mtid = $l1;
        $db   = getConn();       
        $res  = $db->query("SELECT `mtid`,`mtname`,`mtext` FROM `#@_mytags` WHERE `mtid` IN({$mtid})");
        while ($data = $db->fetch($res,0)) {
            $folder = LAZY_PATH.M('MyTags','MYTAGS_CREATE_FOLDER');
            $outHTML = self::view($data[0]);
            $data[3] = $data[1].$data[2];
            // 文件路径修改，删除老文件
            if (!empty($l2) && $data[3]!=$l2) {
                @unlink($folder.'/'.$l2);
            }
            // 生成新文件
            mkdirs($folder);
            saveFile($folder.'/'.$data[3],$outHTML);
        }
    }
    // show *** *** www.LazyCMS.net *** ***
    static function show($l1){
        $mtid = $l1;
        $db   = getConn();
        $res  = $db->query("SELECT `mtname`,`mtext` FROM `#@_mytags` WHERE `mtid` = ?",$mtid);
        if ($data = $db->fetch($res,0)) {
            if (C('SITE_MODE')) {
                return url('MyTags','ShowTags','mtid='.$mtid);
            } else {
                return C('SITE_BASE').M('MyTags','MYTAGS_CREATE_FOLDER').'/'.$data[0].$data[1];
            }
        } else {
            return C('SITE_BASE');
        }
    }
    // tags *** *** www.LazyCMS.net *** ***
    static function tags($tags,$inValue){
        $db  = getConn(); $I1 = null; $tag = O('Tags');
        $jsname = $tag->getLabel($tags,'name');
        $jstype = $tag->getLabel($tags,'type');
        //验证name值必须为英文字母和数字构成
        if (!validate($jsname,3)) { return ; }
        $rs = $db->query("SELECT `mtwidth`,`mtheight`,`mttext`,`mtext`,`mtid` FROM `#@_mytags` WHERE `mtname`=?;",$jsname);
        if (!$data = $db->fetch($rs,0)) { return ; }
        switch (strtolower($jstype)) {
            case 'js':
                $I1 = '<script type="text/javascript" src="'.self::show($data[4]).'"></script>';
                break;
            case 'ssi':
                $I1 = '<!--#include virtual="'.self::show($data[4]).'"-->';
                break;
            case 'iframe':
                $I1 = '<iframe frameborder="0" id="lazy_mytags_'.$jsname.'" scrolling="no" width="'.$data[0].'" height="'.$data[1].'" src="'.self::show($data[4]).'"></iframe>';
                break;
            default:
                $data[2] = $tag->format($data[2]);
                if (strpos(strtolower($data[2]),'{lazy:')!==false) {
                    $I1 = $tag->create($data[2]);
                } else {
                    $I1 = $data[2];
                }
                break;
        }
        return $I1;
    }
    // uninstSQL *** *** www.LazyCMS.net *** ***
    static function uninstSQL(){
        return <<<SQL
            DROP TABLE IF EXISTS `#@_mytags`;
SQL;
    }
    // instsql *** *** www.LazyCMS.net *** ***
    static function instSQL(){
        return <<<SQL
            // 自定义标签
            CREATE TABLE IF NOT EXISTS `#@_mytags` (
              `mtid` int(11) NOT NULL auto_increment,
              `mtorder` int(11) default '0',            # 排序
              `mtname` varchar(50) NOT NULL,            # 名称
              `mttitle` varchar(250),                   # 简单介绍
              `mttext` text,                            # 内容
              `mtwidth` int(11) default '0',            # 宽
              `mtheight` int(11) default '0',           # 高
              `mtext` varchar(10),                      # 后缀
              `mtdate` int(11) default '0',             # 添加日期
              PRIMARY KEY  (`mtid`),
              KEY `adname` (`mtname`)
            ) ENGINE=MyISAM DEFAULT CHARSET=#~lang~#;
SQL;
    }
}