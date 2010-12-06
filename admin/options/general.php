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
current_user_can('option-general');
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
    validate_check('SiteTitle',VALIDATE_EMPTY,__('Please input the Site Title.'));
    validate_check('TemplateSuffixs',VALIDATE_EMPTY,__('Please input the Template suffix.'));
    validate_check('HTMLFileSuffix',VALIDATE_EMPTY,__('Please input the HTML file suffix.'));
    if (validate_is_ok()) {
        C($options);
    }
    ajax_success(__('Settings saved.'),"LazyCMS.redirect('".$referer."');");
} else {
    // 标题
    system_head('title',__('General Settings'));
    system_head('styles', array('css/options'));
    system_head('scripts',array('js/options'));
    system_head('loadevents','options_init');

    include ADMIN_PATH.'/admin-header.php';
    echo '<div class="wrap">';
    echo   '<h2>'.system_head('title').'</h2>';
    echo   '<form action="'.PHP_FILE.'" method="post" name="options" id="options">';
    echo     '<fieldset>';
    echo       '<table class="form-table">';
    echo           '<tr>';
    echo               '<th><label for="SiteTitle">'.__('Site Title').'<span class="resume">'.__('(required)').'</span></label></th>';
    echo               '<td><input class="text" id="SiteTitle" name="SiteTitle" type="text" size="50" value="'.C('SiteTitle').'" /></td>';
    echo           '</tr>';
    echo           '<tr>';
    echo               '<th><label for="Language">'._x('Language','setting').'</label></th>';
    echo               '<td>';
    echo                   '<select name="Language" id="Language">';
    echo                       '<option value="en"'.(C('Language')=='en'?' selected="selected"':null).'>'.__('English').'</option>';
    echo                       options('@.locale','lang','<option value="#value#"#selected#>#name#</option>',C('Language'));
    echo                   '</select>';
    echo               '</td>';
    echo           '</tr>';
    echo           '<tr>';
    echo               '<th><label for="timezone">'._x('Time zone','setting').'</label></th>';
    echo               '<td>';
    echo                   '<select name="Timezone" id="timezone">';
    foreach (time_zone_group() as $name=>$zones) {
        echo                   '<optgroup label="'.$name.'">';
        foreach ($zones as $k=>$v) {
            $selected = C('Timezone')==$name.'/'.$k ? ' selected="selected"' : '';
            echo                   '<option value="'.$name.'/'.$k.'"'.$selected.'>'.$v.'</option>';
        }
        echo                   '</optgroup>';
    }
    echo                   '</select>';
    $timezone = time_zone_set('UTC');
    echo                   '<span class="utc-time">'.sprintf(__('<abbr title="Coordinated Universal Time">UTC</abbr> time is <code>%s</code>'), date('Y-m-d H:i:s',time())).'</span>';
    time_zone_set($timezone);
    echo                   '<span class="local-time">'.sprintf(__('Local time is <code>%s</code>'), date('Y-m-d H:i:s',time())).'</span>';
    echo               '</td>';
    echo           '</tr>';
    echo           '<tr>';
    echo               '<th><label for="Template">'._x('Template','setting').'</label></th>';
    echo               '<td><select name="Template" id="Template">';
    echo                       options(TEMPLATE,'dir','<option value="#value#"#selected#>#name#</option>',C('Template'));
    echo                   '</select>';
    echo               '</td>';
    echo           '</tr>';
    echo           '<tr>';
    echo               '<th><label for="TemplateSuffixs">'.__('Template suffix').'<span class="resume">'.__('(required)').'</span></label></th>';
    echo               '<td><input class="text" id="TemplateSuffixs" name="TemplateSuffixs" type="text" size="50" value="'.C('TemplateSuffixs').'" /></td>';
    echo           '</tr>';
    echo           '<tr>';
    echo               '<th><label for="HTMLFileSuffix">'.__('HTML file suffix').'<span class="resume">'.__('(required)').'</span></label></th>';
    echo               '<td><select name="HTMLFileSuffix" id="HTMLFileSuffix" edit="true" default="'.C('HTMLFileSuffix').'">';
    foreach(array('.htm','.html','.shtml','.php','.xml','.wml') as $suffix) {
        echo                   '<option value="'.$suffix.'">'.$suffix.'</option>';
    }
    echo                   '</select>';
    echo               '</td>';
    echo           '</tr>';
    echo           '<tr>';
    echo               '<th><label>'.__('Compress').'</label></th>';
    echo               '<td>';
    echo                   '<label><input name="Compress" type="radio" value="1"'.(C('Compress')?' checked="checked"':'').' />'.__('Enable').'</label>';
    echo                   '<label><input name="Compress" type="radio" value="0"'.(!C('Compress')?' checked="checked"':'').' />'.__('Disable').'</label>';
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


