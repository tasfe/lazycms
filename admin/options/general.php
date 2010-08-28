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
 */
// 接客了！。。。
require dirname(__FILE__).'/../admin.php';
// 得到客人信息
$_ADMIN = user_current();
// 姿势
$action  = isset($_REQUEST['action'])?$_REQUEST['action']:null;
$referer = referer(PHP_FILE,false);
// 保存
$validate = new Validate();
if ($validate->post()) {
    $options = $_POST; unset($options['referer']);
    $validate->check('SiteName',VALIDATE_EMPTY,__('Please enter the site name.'));
    if (!$validate->is_error()) {
        C($options);
    }
    admin_success(__('Settings saved.'),"LazyCMS.redirect('".$referer."');");
} else {
    // 标题
    admin_head('title',__('General Settings'));
    admin_head('scripts',array('js/options'));
    admin_head('loadevents','options_init');
    
    include ADMIN_PATH.'/admin-header.php';
    echo '<div class="wrap">';
    echo   '<h2>'.admin_head('title').'</h2>';
    echo   '<form action="'.PHP_FILE.'" method="post" name="options" id="options">';
    echo     '<fieldset>';
    echo       '<table class="form-table">';
    echo           '<tr>';
    echo               '<th><label for="SiteName">'.__('Site name').'<span class="description">'.__('(required)').'</span></label></th>';
    echo               '<td><input id="SiteName" name="SiteName" type="text" size="50" value="'.C('SiteName').'" /></td>';
    echo           '</tr>';
    echo           '<tr>';
    echo               '<th><label for="Template">'.__('Template').'<span class="description">'.__('(required)').'</span></label></th>';
    echo               '<td><input id="Template" name="Template" type="text" size="20" value="'.C('Template').'" /></td>';
    echo           '</tr>';
    echo           '<tr>';
    echo               '<th><label for="TemplateSuffixs">'.__('Template suffix').'<span class="description">'.__('(required)').'</span></label></th>';
    echo               '<td><input id="TemplateSuffixs" name="TemplateSuffixs" type="text" size="50" value="'.C('TemplateSuffixs').'" /></td>';
    echo           '</tr>';
    echo           '<tr>';
    echo               '<th><label for="HTMLFileSuffix">'.__('HTML file suffix').'<span class="description">'.__('(required)').'</span></label></th>';
    echo               '<td><input id="HTMLFileSuffix" name="HTMLFileSuffix" type="text" size="10" value="'.C('HTMLFileSuffix').'" /></td>';
    echo           '</tr>';
    echo       '</table>';
    echo     '</fieldset>';
    echo     '<input type="hidden" name="referer" value="'.$referer.'" />';
    echo     '<p class="submit"><button type="submit">'.__('Save Changes').'</button></p>';
    echo   '</form>';
    echo '</div>';
    include ADMIN_PATH.'/admin-footer.php';
}


