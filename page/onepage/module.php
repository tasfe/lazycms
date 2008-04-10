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
class Onepage{
    // updateIndex *** *** www.LazyCMS.net *** ***
    static function updateIndex(){
        $db  = getConn();
        $res = $db->query("SELECT `oneid` FROM `#@_onepage` WHERE `ishome` = '1'");
        if ($data = $db->fetch($res,0)) {
            self::create($data[0]);
        }
        unset($db);
    }
    // index *** *** www.LazyCMS.net *** ***
    static function index(){
        $db  = getConn();
        $res = $db->query("SELECT `oneid` FROM `#@_onepage` WHERE `ishome` = '1'");
        if ($data = $db->fetch($res,0)) {
            echo self::view($data[0]);
        } else {
            throwError(L('error/nodefault'));
        }
        unset($db);
    }
    // view *** *** www.LazyCMS.net *** ***
    static function view($l1){
        $oneid = $l1;
        $db    = getConn();        
        $sql   = "onename,onetitle,onepath,onekeyword,onedescription,onecontent,onetemplate1,onetemplate2";//7
        $where = $db->quoteInto('WHERE `oneid` = ?',$oneid);
        $res   = $db->query("SELECT {$sql} FROM `#@_onepage` {$where}");
        if ($data = $db->fetch($res,0)) {
            $tag  = O('Tags');
            $HTML = $tag->read($data[6],$data[7]);
            $tag->clear();
            $tag->value('name',encode(htmlencode($data[0])));
            $tag->value('title',encode(htmlencode($data[1])));
            $tag->value('path',encode($data[2]));
            $tag->value('keywords',encode(htmlencode($data[3])));
            $tag->value('description',encode(htmlencode($data[4])));
            $tag->value('content',encode($data[5]));
            $tag->value('guide',encode(htmlencode($data[0])));
            $outHTML = $tag->create($HTML,$tag->getValue());
            return $outHTML;
        }
    }
    // create *** *** www.LazyCMS.net *** ***
    static function create($l1,$l2=null){
        // 网站启动动态模式，不生成任何文件
        if (C('SITE_MODE')) { return ; }
        $oneid = $l1;
        $db    = getConn();       
        $res   = $db->query("SELECT `oneid`,`onepath` FROM `#@_onepage` WHERE `oneid` IN({$oneid})");
        while ($data = $db->fetch($res,0)) {
            $outHTML = self::view($data[0]);
            // 文件路径修改，删除老文件
            if (!empty($l2) && $data[1]!=$l2) {
                $paths = explode('/',$l2);
                if (strpos($paths[count($paths)-1],'.')!==false){ //文件
                    @unlink(LAZY_PATH.$l2);
                    if (strpos($l2,'/')!==false){
                        $path = substr($l2,0,strlen($l2)-strlen($paths[count($paths)-1]));
                        rmdirs(LAZY_PATH.$path,false);
                    }
                } else { //目录
                    @unlink(LAZY_PATH.$l2.'/'.C('SITE_INDEX'));
                    rmdirs(LAZY_PATH.$l2,false);
                }
            }
            // 生成新文件
            $paths = explode('/',$data[1]);
            if (strpos($paths[count($paths)-1],'.')!==false){ //文件
                if (strpos($data[1],'/')!==false){
                    $path = substr($data[1],0,strlen($data[1])-strlen($paths[count($paths)-1]));
                    mkdirs(LAZY_PATH.$path);
                }
                saveFile(LAZY_PATH.$data[1],$outHTML);
            } else { //目录
                mkdirs(LAZY_PATH.$data[1]);
                saveFile(LAZY_PATH.$data[1].'/'.C('SITE_INDEX'),$outHTML);
            }
        }
    }
    // show *** *** www.LazyCMS.net *** ***
    static function show($l1){
        $oneid = $l1;
        $db    = getConn();       
        $where = $db->quoteInto("WHERE `oneid` = ?",$oneid);
        $res   = $db->query("SELECT `onepath`,`ishome` FROM `#@_onepage` {$where}");
        if ($data = $db->fetch($res,0)) {
            // 被设为首页，直接返回网站根路径
            if ($data[1]) {
                return C('SITE_BASE');
            }
            if (C('SITE_MODE')) {
                return url('Onepage','ShowPage','oneid='.$l1);
            } else {
                return C('SITE_BASE').$data[0];
            }
        }
    }
    // tags *** *** www.LazyCMS.net *** ***
    static function tags($tags){
        // 根据id返回链接
        $id = Tags::getLabel($tags,'id');
        if (!empty($id)) {
            return self::show($id);
        }
        return true;
    }
    // instsql *** *** www.LazyCMS.net *** ***
    static function instSQL(){
        return <<<SQL
            // 单页面表
            DROP TABLE IF EXISTS `#@_onepage`;
            CREATE TABLE IF NOT EXISTS `#@_onepage` (
              `oneid` int(11) NOT NULL auto_increment,  # 编号
              `oneorder` int(11) default '0',           # 排序号
              `onetitle` varchar(100) NOT NULL,         # 页面标题
              `onepath` varchar(100),                   # 页面路径
              `onename` varchar(50),                    # 后台显示名称
              `onekeyword` varchar(50),                 # 关键词
              `onedescription` varchar(250),            # 简介
              `onecontent` text,                        # 内容
              `onetemplate1` varchar(50),               # 外模板
              `onetemplate2` varchar(50),               # 内模板
              `ishome` enum('0','1') default '0',       # 是否为首页
              PRIMARY KEY  (`oneid`)
            ) ENGINE=MyISAM DEFAULT CHARSET=#~lang~#;
SQL;
    }
}