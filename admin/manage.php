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
// lazy_before *** *** www.LazyCMS.net *** ***
function lazy_before(){
    // 定义当前模块，用来设置语言包
    G('MODULE','system');
}
// lazy_default *** *** www.LazyCMS.net *** ***
function lazy_default(){ 
    $_USER = check_login('system','logout.php');
    $hl = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
    $hl.= '<html xmlns="http://www.w3.org/1999/xhtml"><head><base target="main" /><meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
    $hl.= '<title>'.L('system/@title').'</title>';
    $hl.= '<link href="system/images/style.css" rel="stylesheet" type="text/css" />';
    $hl.= '<script type="text/javascript" src="../common/js/jquery.js?ver=1.2.6"></script>';
    $hl.= '<script type="text/javascript" src="../common/js/jquery.lazycms.js?ver=1.0"></script>';
    $hl.= '<script type="text/javascript" src="system/images/script.js"></script>';
    $hl.= '</head><body>';
    $hl.= '<div id="top">';
    $hl.= '<div class="logo"><a href="manage.php" target="_top"><img src="system/images/logo.png" alt="LazyCMS '.LAZY_VERSION.'" /></a></div>';
    $hl.= '<div id="version">Version: <span>'.LAZY_VERSION.'</span></div>';
    $hl.= '<div class="shortcut"><a href="javascript:;" onclick="toggleShortcut();"><img src="../common/images/icon/fav.png" /></a><a href="javascript:;" onclick="toggleAddShortcut()"><img src="../common/images/icon/fav-add.png" /></a></div>';
    $hl.= '<ul id="menu">';
    $hl.= '<li><span>'.L('system/manage').'<b class="down-arrow"></b></span><ul>';
    $hl.= '    <li><a href="manage.php" class="icon-16-cpanel" target="_top">'.L('system/cpanel').'</a></li>';
    $hl.= '    <li class="hr"></li>';
    $hr = false;
    if (instr($_USER['purview'],'system/users')) {
        $hr = true;
        $hl.= '<li><a href="system/users.php" class="icon-16-user">'.L('users/@title').'</a></li>';    
    }
    if (instr($_USER['purview'],'system/webftp')) {
        $hr = true;
        $hl.= '<li><a href="system/media.php" class="icon-16-media">'.L('webftp/@title').'</a></li>';
    }
    if ($hr) {
        $hl.= '<li class="hr"></li>';
    }
    $hr = false;
    if (instr($_USER['purview'],'system/module')) {
        $hr = true;
        $hl.= '<li><a href="system/install.php" class="icon-16-install">'.L('module/@title').'</a></li>';
    }
    if (instr($_USER['purview'],'system/settings')) {
        $hr = true;
        $hl.= '<li><a href="system/settings.php" class="icon-16-config">'.L('settings/@title').'</a></li>';
    }
    if ($hr) {
        $hl.= '<li class="hr"></li>';
    }
    $hl.= '    <li><a href="logout.php" class="icon-16-logout" target="_top" onclick="return confirm(\''.L('confirm/logout').'\')">'.L('common/logout').'</a></li>';
    $hl.= '</ul></li>';

    $hl.= '    
        <li><span>内容管理<b class="down-arrow"></b></span>
            <ul>
                <li><a href="#">首页设置</a></li>
                <li><a href="content/onepage.php" class="icon-16-page">单页管理</a></li>
                <li class="hr"></li>
                <li><a href="content/article.php" class="icon-16-article">文档管理</a></li>
                <li><a href="content/trash.php" class="icon-16-trash">回收站</a></li>
                <li class="hr"></li>
                <li><a href="content/sort.php" class="icon-16-sort">分类管理</a></li>
                <li><a href="content/model.php" class="icon-16-model">模型管理</a></li>
            </ul>
        </li>';

    $hl.= '<li><span>'.L('common/help').'<b class="down-arrow"></b></span><ul>';
    $hl.= '    <li><a href="http://www.lazycms.net/" class="icon-16-home" target="_blank">'.L('common/osite').'</a></li>';
    $hl.= '    <li><a href="http://forums.lazycms.net/" class="icon-16-help" target="_blank">'.L('common/forums').'</a></li>';
    $hl.= '    <li class="hr"></li>';
    $hl.= '    <li><a href="system/sysinfo.php" class="icon-16-info">'.L('sysinfo/@title').'</a></li>';
    $hl.= '    </ul>';
    $hl.= '</li></ul>';

    $hl.= '<ul class="menu"><li><a href="../" target="_blank">'.L('common/preview').'</a></li><li><a href="logout.php" target="_top" onclick="return confirm(\''.L('confirm/logout').'\')">'.L('common/logout').'</a></li></ul>';
    $hl.= '</div>';
    $hl.= '<iframe src="about:blank" id="main" name="main" width="99%" marginwidth="0" height="510" marginheight="0" scrolling="no" frameborder="0"></iframe>';
    $hl.= '<div id="footer"><a href="http://www.lazycms.net" target="_blank">Copyright &copy; LazyCMS.net All Rights Reserved.</a></div>';
    $hl.= '<div id="shortcut" class="panel"><div class="head"><strong>'.L('shortcut/@title').'</strong><a href="javascript:;" onclick="toggleShortcut();">×</a></div><div class="body"></div></div>';
    $hl.= '<div id="toolbar"><div class="logo"><a href="manage.php"><img src="system/images/toolbar-logo.png" /></a></div></div>';

    $hl.= '<div id="addShortcut" class="panel"><div class="head"><strong>'.L('shortcut/add/@title').'</strong><a href="javascript:;" onclick="toggleAddShortcut();">×</a></div><div class="body">';
    $hl.= '<form id="formShortcut" name="formShortcut" method="post" action="manage.php?action=addShortcut">';
    $hl.= '<p><label>'.L('shortcut/add/name').'：</label><input class="in2" type="text" name="ShortcutName" id="ShortcutName"></p>';
    $hl.= '<p><label>'.L('shortcut/add/url').'：</label><input class="in3" type="text" name="ShortcutUrl" id="ShortcutUrl"></p>';
    $hl.= '<p><label>'.L('shortcut/add/sort').'：</label>';
    $hl.= '<select name="ShortcutSort" id="ShortcutSort">';
    $XML = 'system/data/'.$_USER['username'].'/shortcut.xml';
    if (is_file($XML)) {
        $DOM   = new DOMDocument; $DOM->load($XML);
        $xPath = new DOMXPath($DOM);
        $dt    = $xPath->evaluate("//root/dl/dt");
        if ($dt->length==0) {
            $hl.= '<option value="">-- No Category --</option>';
        }
        for ($i=0; $i<$dt->length; $i++) {
            $val = $dt->item($i)->nodeValue;
            $hl.= '<option value="'.$val.'">'.$val.'</option>';
        }
    } else {
        $hl.= '<option value="">-- No Category --</option>';
    }
    $hl.= '</select>&nbsp;<button type="button" onclick="return confirm(\''.L('confirm/delete').'\') ? deleteShortcutSort():false;">'.L('shortcut/button/delete').'</button><button type="button" onclick="toggleShortcutSort()">'.L('shortcut/button/create').'</button></p>';

    $ICONS = COM_PATH.'/images/icons.css';
    if (is_file($ICONS)) {
        if (preg_match_all('/\.icon\-32\-(\w+)/i',read_file($ICONS),$ios)) {
            $hl.= '<p><label>'.L('shortcut/add/icon').'：</label>';
            $hl.= '<div class="icons">';
            foreach ($ios[1] as $io) {
                $hl.= '<a href="javascript:;" onclick="selectIcon(this)" title="'.$io.'" class="icon-32-'.$io.'">&nbsp;</a>';
            }
            $hl.= '</div><input name="ShortcutIcon" id="ShortcutIcon" type="hidden" /></p>';
        }
    }

    $hl.= '<p class="tr"><button type="button" onclick="submitShortcut();">'.L('shortcut/button/add').'</button>&nbsp;<button type="button" onclick="toggleAddShortcut()">'.L('shortcut/button/cancel').'</button></p></form>';
    
    $hl.= '<dl><dt><strong>'.L('shortcut/button/create').'</strong><a href="javascript:;" onclick="toggleShortcutSort()">×</a></dt><dd>';
    $hl.= '<p><label>'.L('shortcut/add/sortname').'：</label><input class="in2" type="text" name="ShortcutSortName" id="ShortcutSortName"></p>';
    $hl.= '<p class="tr"><button type="button" onclick="submitShortcutSort()">'.L('shortcut/button/add').'</button>&nbsp;<button type="button" onclick="toggleShortcutSort()">'.L('shortcut/button/cancel').'</button></p>';
    $hl.= '</dd></dl>';
    $hl.= '</div></div>';

    $hl.= '</body></html>'; echo $hl;
}
// lazy_addShortcut *** *** www.LazyCMS.net *** ***
function lazy_addShortcut(){
    $_USER    = check_login('system','logout.php');
    $sName    = isset($_POST['ShortcutName']) ? $_POST['ShortcutName'] : null;
    $sUrl     = isset($_POST['ShortcutUrl']) ? $_POST['ShortcutUrl'] : null;
    $sortName = isset($_POST['ShortcutSort']) ? $_POST['ShortcutSort'] : null;
    $sClass   = isset($_POST['ShortcutIcon']) ? $_POST['ShortcutIcon'] : null;
    if (empty($sortName)) { echo_json(L('shortcut/check/selectsort'),0); }
    $val = new Validate();
    $val->check('ShortcutName|0|'.L('shortcut/check/name'));
    $val->check('ShortcutUrl|0|'.L('shortcut/check/url'));
    if ($val->isVal()) {
        $val->out();
    } else {
        $UserData = 'system/data/'.strtolower($_USER['username']);
        $XMLFile  = $UserData.'/shortcut.xml';
        if (is_file($XMLFile)) {
            $DOM   = new DOMDocument; $DOM->load($XMLFile);
            $xPath = new DOMXPath($DOM);
            $dt    = $xPath->evaluate("//root/dl/dt");
            for ($i=0; $i<$dt->length; $i++) {
                $v = $dt->item($i)->nodeValue;
                if ($sortName==$v) {
                    $a = $xPath->evaluate("//root/dl")->item($i)->appendChild($DOM->createElement('a'));
                    $a->setAttribute('href',xmlencode($sUrl));
                    if (!empty($sClass)) {
                        $a->setAttribute('class',xmlencode($sClass));
                    }
                    $a->nodeValue = xmlencode($sName);
                }
            }
            $DOM->save($XMLFile); echo('true');
        }
    }
}
// lazy_createSort *** *** www.LazyCMS.net *** ***
function lazy_createSort(){
    $_USER    = check_login('system','logout.php');
    $UserData = 'system/data/'.strtolower($_USER['username']);
    $XMLFile  = $UserData.'/shortcut.xml';
    $sortName = isset($_POST['ShortcutSortName']) ? $_POST['ShortcutSortName'] : null;
    $val = new Validate(); $Exist = 'true';
    $val->check('ShortcutSortName|0|'.L('shortcut/check/sortname'));
    if (is_file($XMLFile)) {
        $DOM   = new DOMDocument; $DOM->load($XMLFile);
        $xPath = new DOMXPath($DOM);
        $dt    = $xPath->evaluate("//root/dl/dt");
        for ($i=0; $i<$dt->length; $i++) {
            $v = $dt->item($i)->nodeValue;
            if ($sortName==$v) {
                $Exist = 'false';
            }
        }
        $val->check('ShortcutSortName|3|'.L('shortcut/check/sortname1').'|'.$Exist);
    }
    if ($val->isVal()) {
        $val->out();
    } else {
        if (is_file($XMLFile)) {
            // 增加一个节点
            $xPath->evaluate("//root")->item(0)->appendChild($DOM->createElement('dl'))->appendChild($DOM->createElement('dt'))->nodeValue = xmlencode($sortName);
            $DOM->save($XMLFile); echo_json(true,1);
        } else {
            mkdirs($UserData);
            $XML = '<?xml version="1.0" encoding="utf-8"?><root><dl><dt>'.xmlencode($sortName).'</dt></dl></root>';
            save_file($XMLFile,$XML); echo_json(true,1);
        }
    }
}
// lazy_deleteSort *** *** www.LazyCMS.net *** ***
function lazy_deleteSort(){
    $_USER    = check_login('system','logout.php');
    $UserData = 'system/data/'.strtolower($_USER['username']);
    $XMLFile  = $UserData.'/shortcut.xml';
    $sortName = isset($_POST['ShortcutSortName']) ? $_POST['ShortcutSortName'] : null;
    if (is_file($XMLFile)) {
        $XML = read_file($XMLFile);
        $XML = str_replace(array("\r","\n","\t","<dl>"),array('','','',"\n<dl>"),$XML);
        $XML = preg_replace('/\<dl\><dt\>'.xmlencode($sortName).'\<\/dt\>.*\<\/dl\>/i','',$XML);
        $XML = str_replace("\n",'',$XML);
        save_file($XMLFile,$XML); echo_json(true,1);
    }
}