<?php
/**
 * +---------------------------------------------------------------------------+
 * | LL                             LLLL   LL     L   LLLL               LLLL  |
 * | LL                            LL   L  LLL   LL  LL   L             LL  LL |
 * | LL      LLLL   LLLLL  LL  LL  LL      LLLL LLL  LL         LL  LL      LL |
 * | LL         LL     LL  LL  LL  LL      L LLL LL  LLLLL      LL  LL     LL  |
 * | LL      LLLLL    LL    LLLL   LL      L  L  LL   LLLLL     LL  LL    LL   |
 * | LL     LL  LL   LL     LLLL   LL      L     LL      LL      LLLL    LL    |
 * | LL     LL  LL  LL       LL    LL   L  L     LL  L   LL      LLLL   LL     |
 * | LLLLLL  LLLLL  LLLLL    LL     LLLL   L     LL   LLLL        LL    LLLLLL |
 * |                        LL                                                 |
 * |                        LL                                                 |
 * +---------------------------------------------------------------------------+
 * | Copyright (C) 2007-2008 LazyCMS.net All rights reserved.                  |
 * +---------------------------------------------------------------------------+
 * | LazyCMS is free software. This version use Apache License 2.0             |
 * | See LICENSE.txt for copyright notices and details.                        |
 * +---------------------------------------------------------------------------+
 
 * 
 */
require '../../global.php';
/**
 * 后台首页面
 *
 */
// *** *** www.LazyCMS.net *** *** //
function lazy_main(){
    System::purview('System.System');
    System::header();
    $db = get_conn();
    $gdInfo = function_exists('gd_info') ? gd_info() : array('GD Version'=>'none');
    echo '<div id="sysinfo" class="panel"><div class="body">';
    echo '<dl><dt>'.l('System info').'</dt><dd>';
    echo '<p><label>'.l('Server OS').':</label>'.php_uname().'</p>';
    echo '<p><label>'.l('Database version').':</label>'.$db->config('scheme').' '.$db->version().'</p>';
    echo '<p><label>'.l('PHP version').':</label>'.PHP_VERSION.'</p>';
    echo '<p><label>'.l('GD version').':</label>'.$gdInfo['GD Version'].'</p>';
    echo '<p><label>LazyCMS:</label>'.LAZY_VERSION.'</p>';
    echo '</dd></dl>';
    echo '<dl><dt>'.l('PHP Settings').'</dt><dd>';
    echo '<p><label>'.l('PHP allow url fopen').':</label>'.isok((function_exists('fsockopen') || function_exists('curl_exec'))).'</p>';
    echo '<p><label>'.l('PHP file uploads').':</label>'.get_php_setting('file_uploads').'</p>';
    echo '<p><label>'.l('PHP zlib').':</label>'.isok(extension_loaded('zlib')).'</p>';
    echo '<p><label>'.l('PHP iconv').':</label>'.isok(function_exists('iconv')).'</p>';
    echo '<p><label>'.l('PHP mbstring').':</label>'.isok(extension_loaded('mbstring')).'</p>';
    echo '<p><label>'.l('PHP upload max file size').':</label>'.get_cfg_var('upload_max_filesize').'</p>';
    echo '<p><label>'.l('PHP post max size').':</label>'.get_cfg_var('post_max_size').'</p>';
    echo '</dd></dl>';
    echo '</div></div>';

    echo '<div id="shortcut" class="panel"><div class="body">';
    echo '<dl><dt>内容管理</dt>';
    echo '<dd><a href="../content/onepage.php" class="icon-32-page">单页管理</a></dd>';
    echo '<dd><a href="../content/article.php" class="icon-32-article">文档管理</a></dd>';
    echo '<dd><a href="../content/sort.php" class="icon-32-sort">分类管理</a></dd>';
    echo '<dd><a href="../content/model.php" class="icon-32-model">模型管理</a></dd>';
    echo '</dl>';
    echo '<dl><dt>系统管理</dt>';
    echo '<dd><a href="../system/admin.php" class="icon-32-admin">用户管理</a></dd>';
    echo '<dd><a href="../system/files.php" class="icon-32-files">文件管理</a></dd>';
    echo '<dd><a href="../system/install.php" class="icon-32-install">安装卸载</a></dd>';
    echo '<dd><a href="../system/settings.php" class="icon-32-settings">系统设置</a></dd>';
    echo '</dl>';
    echo '</div></div>';
    System::footer();
}
