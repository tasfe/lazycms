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
 * | Copyright (C) 2007-2010 LazyCMS.com All rights reserved.                  |
 * +---------------------------------------------------------------------------+
 * | LazyCMS is free software. See LICENSE for copyright notices and details.  |
 * +---------------------------------------------------------------------------+
 */
// 接客了！。。。
include dirname(__FILE__).'/../admin.php';
// 得到客人信息
$_USER = user_current();
// 权限验证
current_user_can('option-posts');
// 姿势
$referer = referer(PHP_FILE,false);
// 保存
if (validate_is_post()) {
    $options = $_POST;
    foreach($options as $k=>$v) {
        if (!strncasecmp($k,'eselect_',8)) {
            unset($options[$k]); continue;
        }
    }
    unset($options['referer']);
    validate_check(array(
        array('Comments-Path',VALIDATE_EMPTY,__('Please input the Comments Path.')),
        array('Comments-Path',VALIDATE_IS_PATH,sprintf(_x('The path can not contain any of the following characters %s','setting'), esc_html('* : < > | \\'))),
    ));
    if (validate_is_ok()) {
        C($options);
    }
    ajax_success(__('Settings saved.'),"LazyCMS.redirect('".$referer."');");
} else {
    // 标题
    system_head('title',__('Posts Settings'));
    system_head('scripts',array('js/options'));
    system_head('loadevents','options_init');

    include ADMIN_PATH.'/admin-header.php';
    echo '<div class="wrap">';
    echo   '<h2>'.system_head('title').'</h2>';
    echo   '<form action="'.PHP_FILE.'" method="post" name="options" id="options">';
    echo     '<fieldset>';
    echo       '<table class="form-table">';
    echo           '<tr>';
    echo               '<th><label for="Template-404">'.__('404 Template').'</label></th>';
    echo               '<td><select name="TPL-404" id="Template-404">';
    echo                       options(system_themes_path(),C('TemplateSuffixs'),'<option value="#value#"#selected#>#name#</option>',C('TPL-404'));
    echo                   '</select>';
    echo               '</td>';
    echo           '</tr>';
    echo           '<tr>';
    echo               '<th><label for="Template-search">'.__('Search Template').'</label></th>';
    echo               '<td><select name="TPL-Search" id="Template-search">';
    echo                       options(system_themes_path(),C('TemplateSuffixs'),'<option value="#value#"#selected#>#name#</option>',C('TPL-Search'));
    echo                   '</select>';
    echo               '</td>';
    echo           '</tr>';
    echo           '<tr>';
    echo               '<th><label for="Template-comments">'.__('Comments Template').'</label></th>';
    echo               '<td><select name="TPL-Comments" id="Template-comments">';
    echo                       options(system_themes_path(),C('TemplateSuffixs'),'<option value="#value#"#selected#>#name#</option>',C('TPL-Comments'));
    echo                   '</select>';
    echo               '</td>';
    echo           '</tr>';
    echo           '<tr>';
    echo               '<th><label for="Comments-Path">'.__('Comments Path').'<span class="resume">'.__('(required)').'</span></label></th>';
    echo               '<td><input class="text" id="Comments-Path" name="Comments-Path" type="text" size="30" value="'.C('Comments-Path').'" /></td>';
    echo           '</tr>';
    echo           '<tr>';
    echo               '<th><label>'.__('Tags Service').'<div class="resume">'.__('(Get the Network tags of service)').'</div></label></th>';
    echo               '<td>';
    echo                   '<label><input name="Tags-Service" type="radio" value="1"'.(C('Tags-Service')?' checked="checked"':'').' />'.__('Enable').'</label>';
    echo                   '<label><input name="Tags-Service" type="radio" value="0"'.(!C('Tags-Service')?' checked="checked"':'').' />'.__('Disable').'</label>';
    echo               '</td>';
    echo           '</tr>';
    echo       '</table>';
    echo     '</fieldset>';
    echo     '<input type="hidden" name="referer" value="'.$referer.'" />';
    echo     '<p class="submit"><button type="submit">'.__('Save Changes').'</button></p>';
    echo   '</form>';
    echo '</div>';
    include ADMIN_PATH.'/admin-footer.php';
}


