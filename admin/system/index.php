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
    $db = get_conn();
    $gdInfo = function_exists('gd_info') ? gd_info() : array('GD Version'=>'none');
    System::header();
    echo '<div id="shortcut" class="panel">';
    echo '  <div class="head"><strong>快捷方式</strong></div>';
    echo '  <div class="body">';
    echo '      <dl>';
    echo '          <dt>内容管理</dt>';
    echo '          <dd><a href="#">内容管理</a></dd>';
    echo '      </dl>';
    echo '  </div>';
    echo '</div>';
    echo '<div id="sysinfo" class="panel">';
    echo '  <div class="head"><strong>系统信息</strong></div>';
    echo '  <div class="body">';
    echo '      <dl>';
    echo '          <dt>服务器环境</dt>';
    echo '          <dd>';
    echo '              <p><label>'.l('Server OS').':</label>'.php_uname().'</p>';
    echo '              <p><label>'.l('Database version').':</label>'.$db->config('scheme').' '.$db->version().'</p>';
    echo '              <p><label>'.l('PHP version').':</label>'.PHP_VERSION.'</p>';
    echo '              <p><label>'.l('GD version').':</label>'.$gdInfo['GD Version'].'</p>';
    echo '              <p><label>LazyCMS:</label>'.LAZY_VERSION.'</p>';
    echo '          </dd>';
    echo '      </dl>';
    echo '      <dl>';
    echo '          <dt>函数依赖</dt>';
    echo '          <dd><a href="#">内容管理</a></dd>';
    echo '      </dl>';
    echo '      <dl>';
    echo '          <dt>其他信息</dt>';
    echo '          <dd><a href="#">内容管理</a></dd>';
    echo '      </dl>';
    echo '  </div>';
    echo '</div>';
    System::footer();
}
