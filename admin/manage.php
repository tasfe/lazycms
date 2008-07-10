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
    check_login('manage','logout.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head><base target="main" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo L('manage/@title');?></title>
<link href="system/images/style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../common/js/jquery.js?ver=1.2.6"></script>
<script type="text/javascript" src="../common/js/jquery.lazycms.js?ver=1.0"></script>
<script type="text/javascript" src="system/images/script.js"></script>
</head>

<body>
<div id="top">
    <div class="logo"><a href="manage.php" target="_top"><img src="system/images/logo.png" alt="LazyCMS <?php echo LAZY_VERSION;?>" /></a></div>
    <div id="version">Version: <span><?php echo LAZY_VERSION;?></span></div>
    <div class="shortcut"><a href="javascript:;" onclick="toggleShortcut();"><img src="../common/images/icon/fav.png" /></a><a href="javascript:;" onclick="addShortcut()"><img src="../common/images/icon/fav-add.png" /></a></div>
    <ul id="menu">
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
    </ul>
    <ul class="menu">
        <li><a href="<?php echo C('SITE_BASE');?>" target="_blank">预览网站</a></li>
        <li><a href="logout.php" target="_top" onclick="return confirm('<?php echo L('confirm/logout');?>')">退出登录</a></li>
    </ul>
</div>


<iframe src="about:blank" id="main" name="main" width="99%" marginwidth="0" height="510" marginheight="0" scrolling="no" frameborder="0"></iframe>

<div id="footer"><a href="http://www.lazycms.net" target="_blank">Copyright &copy; LazyCMS.net All Rights Reserved.</a></div>

<div id="toolbar">
    <div class="logo"><a href="manage.php"><img src="system/images/toolbar-logo.png" /></a></div>
</div>
</body>
</html>
<?php }?>