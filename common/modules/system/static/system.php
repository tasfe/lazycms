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
 */
class System{
    /**
     * 输出后台header头
     *
     * @param string $title
     */
    function header($title=null,$selected=null){
        $tabs  = g('TABS'); $selected = !empty($selected) ? $selected.'|' : null;
        $title = empty($title) ? l('System manage') : $title.' - '.l('System manage');
        $hl = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
        $hl.= '<html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
        $hl.= '<title>'.$title.'</title>';
        $hl.= '<link href="../system/images/style.css" rel="stylesheet" type="text/css" />';
        $hl.= '<script type="text/javascript" src="../../common/js/jquery.js?ver=1.2.6"></script>';
        $hl.= '<script type="text/javascript" src="../../common/js/lazycms.library.js?ver=1.0"></script>';
        $hl.= '<script type="text/javascript" src="../system/images/system.js?ver=1.0"></script>';
        $hl.= '</head><body>';
        $hl.= '<div id="top">';
        $hl.= '<div class="logo"><a href="../system/index.php"><img src="../system/images/logo.png" alt="LazyCMS '.LAZY_VERSION.'" /></a></div>';
        $hl.= '<div id="version">'.l('Last Version').': <span>'.LAZY_VERSION.'</span></div>';
        $hl.= '<ul id="menu">';
        $hl.= '<li><span>'.l('System manage').'<b class="down-arrow"></b></span><ul>';
        $hl.= '    <li><a href="../system/index.php" class="icon-16-cpanel">'.l('Cpanel').'</a></li>';
        $hl.= '    <li class="hr"></li>';
        $hl.= '    <li><a href="../system/media.php" class="icon-16-media">'.l('Webftp').'</a></li>';
        $hl.= '    <li><a href="../system/install.php" class="icon-16-install">'.l('Modules').'</a></li>';
        $hl.= '    <li><a href="../system/settings.php" class="icon-16-config">'.l('Settings').'</a></li>';
        $hl.= '    <li class="hr"></li>';
        $hl.= '    <li><a href="../system/logout.php" class="icon-16-logout" onclick="return confirm(\''.l('Confirm logout').'\')">'.l('Logout').'</a></li>';
        $hl.= '</ul></li>';
        $hl.= '
            <li><span>内容管理<b class="down-arrow"></b></span>
                <ul>
                    <li><a href="../content/label.php" class="icon-16-label">标签中心</a></li>
                    <li><a href="../content/create.php" class="icon-16-create">生成中心</a></li>
                    <li class="hr"></li>
                    <li><a href="../content/onepage.php" class="icon-16-page">单页管理</a></li>
                    <li><a href="../content/article.php" class="icon-16-article">文档管理</a></li>
                    <li class="hr"></li>
                    <li><a href="../content/sort.php" class="icon-16-sort">分类管理</a></li>
                    <li><a href="../content/model.php" class="icon-16-model">模型管理</a></li>
                </ul>
            </li>';
    
        $hl.= '<li><span>'.l('Help').'<b class="down-arrow"></b></span><ul>';
        $hl.= '    <li><a href="http://www.lazycms.net/" class="icon-16-home" target="_blank">'.l('Official Website').'</a></li>';
        $hl.= '    <li><a href="http://forums.lazycms.net/" class="icon-16-help" target="_blank">'.l('Support Forums').'</a></li>';
        $hl.= '    <li class="hr"></li>';
        $hl.= '    <li><a href="../system/sysinfo.php" class="icon-16-info">'.l('System info').'</a></li>';
        $hl.= '    </ul>';
        $hl.= '</li></ul>';
        $hl.= '<ul class="menu"><li><a href="'.SITE_BASE.'" target="_blank">'.l('Preview').'</a></li><li><a href="../system/logout.php" onclick="return confirm(\''.l('Confirm logout').'\')">'.l('Logout').'</a></li></ul>';
        $hl.= '</div><div id="main">';
        if ($tabs) { 
            $hl.= menu($selected.$tabs); 
            $hl.= '<div id="box">';
        }
        echo $hl;
    }
    /**
     * 设置tabs菜单
     *
     * @param string $p1
     */
    function tabs($p1){
        g('TABS',$p1);
    }
    /**
     * 输出后台尾部
     *
     */
    function footer(){
        $hl = null;
        if (g('TABS')) {
            $hl.= '</div>';
        }
        $hl.= '</div><div id="footer"><a href="http://www.lazycms.net" target="_blank">Copyright &copy; LazyCMS.net All Rights Reserved.</a></div>';
        $hl.= '</body></html>'; echo $hl;
    }
    /**
     * 取得用户组下拉列表
     *
     * @param int $p1   groupid
     * @param int $p2   selected
     * @return string
     */
    function getGroups($p1,$p2=null){
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
    /**
     * 中文分词
     *
     * @param string $p1
     * @return array
     */
    function getKeywords($p1){
        $keywords = $RemoteKeywords = array();
        import('system.httplib');
        import('system.splitword');
        // 先使用本地词库分词
        $sw = new SplitWord();
        $keywords = $sw->getWord($p1);
        // 使用远程分词
        $d = new Httplib("http://keyword.lazycms.net/related_kw.php","POST",10);
        $d->send(array('title'=>rawurlencode($p1)));
        // 请求成功
        if ($d->status() == 200) {
            $XML = $d->response();
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
