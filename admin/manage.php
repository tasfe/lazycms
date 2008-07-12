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
require '../global.php';
/**
 * 管理中心
 *
 * @copyright   Copyright (c) 2007-2008 lazycms.net All rights reserved.
 * @author      Lukin <mylukin@gmail.com>
 * @date        2008-6-18
 */
// lazy_default *** *** www.LazyCMS.net *** ***
function lazy_default(){ 
    $_USER = check_login('manage','logout.php');
    $hl = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
    $hl.= '<html xmlns="http://www.w3.org/1999/xhtml"><head><base target="main" /><meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
    $hl.= '<title>'.L('manage/@title').'</title>';
    $hl.= '<link href="system/images/style.css" rel="stylesheet" type="text/css" />';
    $hl.= '<script type="text/javascript" src="../common/js/jquery.js?ver=1.2.6"></script>';
    $hl.= '<script type="text/javascript" src="../common/js/jquery.lazycms.js?ver=1.0"></script>';
    $hl.= '<script type="text/javascript" src="system/images/script.js"></script>';
    $hl.= '</head><body>';
    $hl.= '<div id="top">';
    $hl.= '<div class="logo"><a href="manage.php" target="_top"><img src="system/images/logo.png" alt="LazyCMS '.LAZY_VERSION.'" /></a></div>';
    $hl.= '<div id="version">Version: <span>'.LAZY_VERSION.'</span></div>';
    $hl.= '<div class="shortcut"><a href="javascript:;" onclick="toggleShortcut();"><img src="../common/images/icon/fav.png" /></a><a href="javascript:;" onclick="toggleAddShortcut()"><img src="../common/images/icon/fav-add.png" /></a></div>';
    $hl.= '<ul id="menu">
        <li><span>系统管理<b class="down-arrow"></b></span>
            <ul>
                <li><a href="manage.php" class="icon-16-cpanel" target="_top">控制面板</a></li>
                <li class="hr"></li>
                <li><a href="system/users.php" class="icon-16-user">用户管理</a></li>
                <li><a href="#" class="icon-16-media">文件管理</a></li>
                <li class="hr"></li>
                <li><a href="#" class="icon-16-install">安装卸载</a></li>
                <li><a href="system/settings.php" class="icon-16-config">全局设置</a></li>
                <li class="hr"></li>
                <li><a href="logout.php" class="icon-16-logout" target="_top">退出登录</a></li>
            </ul>
        </li>
        <li><span>内容管理<b class="down-arrow"></b></span>
            <ul>
                <li><a href="#" class="icon-16-page">单页管理</a></li>
                <li class="hr"></li>
                <li><a href="#" class="icon-16-article">文章管理</a></li>
                <li><a href="#" class="icon-16-trash">回收站</a></li>
                <li class="hr"></li>
                <li><a href="#" class="icon-16-category">分类管理</a></li>
                <li><a href="#" class="icon-16-model">模型管理</a></li>
            </ul>
        </li>
        <li><span>帮助<b class="down-arrow"></b></span>
            <ul>
                <li><a href="http://www.lazycms.net/" class="icon-16-home" target="_blank">官方网站</a></li>
                <li><a href="http://forums.lazycms.net/" class="icon-16-help" target="_blank">支持论坛</a></li>
                <li class="hr"></li>
                <li><a href="system/sysinfo.php" class="icon-16-info">系统信息</a></li>
            </ul>
        </li>
    </ul>';
    $hl.= '<ul class="menu"><li><a href="../" target="_blank">预览网站</a></li><li><a href="logout.php" target="_top" onclick="return confirm(\''.L('confirm/logout').'\')">退出登录</a></li></ul>';
    $hl.= '</div>';
    $hl.= '<iframe src="about:blank" id="main" name="main" width="99%" marginwidth="0" height="510" marginheight="0" scrolling="no" frameborder="0"></iframe>';
    $hl.= '<div id="footer"><a href="http://www.lazycms.net" target="_blank">Copyright &copy; LazyCMS.net All Rights Reserved.</a></div>';
    $hl.= '<div id="shortcut"><div class="head"><strong>'.L('shortcut/@title').'</strong><a href="javascript:;" onclick="toggleShortcut();">×</a></div><div class="body"></div></div>';
    $hl.= '<div id="toolbar"><div class="logo"><a href="manage.php"><img src="system/images/toolbar-logo.png" /></a></div></div>';

    $hl.= '<div id="addShortcut"><div class="head"><strong>'.L('shortcut/add/@title').'</strong><a href="javascript:;" onclick="toggleAddShortcut();">×</a></div><div class="body">';
    $hl.= '<p><label>'.L('shortcut/add/name').'：</label><input class="in2" type="text" name="ShortcutName" id="ShortcutName" value=""></p>';
    $hl.= '<p><label>'.L('shortcut/add/url').'：</label><input class="in3" type="text" name="ShortcutUrl" id="ShortcutUrl" value=""></p>';
    $hl.= '<p><label>'.L('shortcut/add/sort').'：</label>';
    $hl.= '<select name="ShortcutSort" id="ShortcutSort">';
    $XML = 'system/data/'.$_USER['username'].'/shortcut.xml';
    if (is_file($XML)) {
        $DOM   = new DOMDocument; $DOM->load($XML);
        $xPath = new DOMXPath($DOM);
        $dt    = $xPath->evaluate("//root/dl/dt");
        for ($i=0; $i<$dt->length; $i++) {
            $val = $dt->item($i)->nodeValue;
            $hl.= '<option value="'.$val.'">'.$val.'</option>';
        }
    } else {
        $hl.= '<option value="">-- '.L('shortcut/add/create1').' --</option>';
    }
    $hl.= '</select>&nbsp;<button type="button" onclick="toggleShortcutSort()">'.L('shortcut/add/create').'</button></p>';
    $hl.= '<p class="tr"><button type="button">'.L('shortcut/button/add').'</button>&nbsp;<button type="button" onclick="toggleAddShortcut()">'.L('shortcut/button/cancel').'</button></p>';
    $hl.= '<dl><dt><strong>'.L('shortcut/add/create').'</strong><a href="javascript:;" onclick="toggleShortcutSort();">×</a></dt><dd>';
    $hl.= '<p><label>'.L('shortcut/add/sortname').'：</label><input class="in2" type="text" name="ShortcutSortName" id="ShortcutSortName" error="'.L('shortcut/check/sortname').'"></p>';
    $hl.= '<p class="tr"><button type="button" onclick="submitShortcutSort()">'.L('shortcut/button/add').'</button>&nbsp;<button type="button" onclick="toggleShortcutSort()">'.L('shortcut/button/cancel').'</button></p>';
    $hl.= '</dd></dl>';
    $hl.= '</div></div>';

    $hl.= '</body></html>'; echo $hl;
}
