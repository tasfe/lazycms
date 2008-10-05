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
 * | Copyright (C) 2007-2008 LazyCMS.net All rights reserved.                  |
 * +---------------------------------------------------------------------------+
 * | LazyCMS is free software. This version use Apache License 2.0             |
 * | See LICENSE.txt for copyright notices and details.                        |
 * +---------------------------------------------------------------------------+
 */
defined('COM_PATH') or die('Restricted access!');
/**
 * 系统模块的公共函数
 *
 * @copyright   Copyright (c) 2007-2008 lazycms.net All rights reserved.
 * @author      Lukin <mylukin@gmail.com>
 * @date        2008-7-6
 */
// System_System *** *** www.LazyCMS.net *** ***
class System_System{
    // __group *** *** www.LazyCMS.net *** ***
    static function __group($p1,$p2=null){
        // $p1:groupid, $p2:selected
        $R = null; $db = get_conn();
        $res = $db->query("SELECT `groupid`,`groupname` FROM `#@_system_group` WHERE 1=1 ORDER BY `groupid` DESC;");
        while ($rs = $db->fetch($res,0)) {
            if ($p1 != $rs[0]) {
                $selected = ((int)$p2 == (int)$rs[0]) ? ' selected="selected"' : null;
                $R.= '<option value="'.$rs[0].'"'.$selected.'>'.$rs[1].'</option>';
            }
        }
        return $R;
    }
    // getKeywords *** *** www.LazyCMS.net *** ***
    static function getKeywords($p1){
        $keywords = $RemoteKeywords = array();
        import('system.downloader');
        import('system.splitword');
        // 先使用本地词库分词
        $sw = new SplitWord();
        $keywords = $sw->getWord($p1);
        // 使用远程分词
        $d = new DownLoader("http://keyword.lazycms.net/related_kw.php","POST",10);
        $d->send(array('title'=>rawurlencode($p1)));
        // 请求成功
        if ($d->status() == 200) {
            $XML = $d->body();
            // 取出关键词为数组
            if (preg_match_all('/\<kw\>\<\!\[CDATA\[(.+)\]\]\>\<\/kw\>/i',$XML,$Regs)) {
                $RemoteKeywords = $Regs[1];
            }
        }
        // 合并两次分词的结果
        if (!empty($RemoteKeywords)) {
            foreach ($RemoteKeywords as $keyword) {
                if (array_search_value($keyword,$keywords)===false) {
                    $keywords[] = $keyword;
                }
            }
        }
        return $keywords;
    }
}
