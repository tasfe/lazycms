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
require '../../global.php';
/**
 * 系统设置
 *
 */
// *** *** www.LazyCMS.net *** *** //
function lazy_before(){
    no_cache();// 禁止缓存
    System::purview('system::settings');
    System::tabs(
        t('settings').':settings.php;'
    );
}
// *** *** www.LazyCMS.net *** *** //
function lazy_main(){
    $val = new Validate();
    if ($val->method()) {
        $DSN_CONFIG = isset($_POST['DSN_CONFIG']) ? $_POST['DSN_CONFIG'] : null;
        $_POST['TIME_DIFF'] = empty($_POST['TIME_DIFF']) ? 0 : $_POST['TIME_DIFF'];
        $val->check('SITE_NAME|0|'.t('settings/check/sitename'));
        $val->check('UPLOAD_ALLOW_EXT|0|'.t('settings/check/allowext').';UPLOAD_ALLOW_EXT|validate|'.t('settings/check/errorext').'|^[\w\,]+$');
        $val->check('UPLOAD_MAX_SIZE|0|'.t('settings/check/maxsize').';UPLOAD_MAX_SIZE|validate|'.t('settings/check/maxsize1').'|2');
        $val->check('UPLOAD_FILE_PATH|0|'.t('settings/check/filepath').';UPLOAD_FILE_PATH|5|'.t('settings/check/errorpath'));
        $val->check('UPLOAD_IMAGE_PATH|0|'.t('settings/check/imagepath').';UPLOAD_IMAGE_PATH|5|'.t('settings/check/errorpath'));
        $val->check('UPLOAD_IMAGE_EXT|0|'.t('settings/check/imageext').';UPLOAD_IMAGE_EXT|validate|'.t('settings/check/errorext').'|^[\w\,]+$');
        $val->check('TIME_DIFF|3|'.t('settings/check/timediff').'|'.is_numeric($_POST['TIME_DIFF']));
        $val->check('DSN_CONFIG|0|'.t('settings/check/DSNconfig').';DSN_CONFIG|3|'.t('settings/check/DSNformat').'|'.validate($DSN_CONFIG,'^((\w+)\:\/\/path\=(.+)$)|(^(\w+)\:\/\/([^\/:]+)(\:([^@]+)?)?@(\w+)(\:(\d+))?(\/([\w\-]+)\/([\w\-]+)|\/([\w\-]+))$)'));
        if ($val->isVal()) {
            $val->out();
        } else {
            // 读取 config.php
            $config = read_file(COM_PATH.'/config.php');
            $rs     = array();
            // 定义要获取的 input name
            $fields = array(
                'SITE_NAME',
                'LANGUAGE',
                'RSS_NUMBER',
                'GET_RELATED_KEY',
                'USER_ALLOW_REG',
                'USER_GROUP_REG',
                'USER_ACTIVE_REG',
                'UPLOAD_ALLOW_EXT',
                'UPLOAD_MAX_SIZE',
                'UPLOAD_FILE_PATH',
                'UPLOAD_IMAGE_PATH',
                'UPLOAD_IMAGE_EXT',
                'TIME_DIFF',
                'DSN_CONFIG'
            );
            foreach ($fields as $field) {
                $data = isset($_POST[$field]) ? $_POST[$field] : c($field);
                if ($data!='true' && $data!='false' && !is_numeric($data)) {
                    $data = "'{$data}'";
                }
                $config = preg_replace('/(\''.$field.'\'( |\t)*\=\>( |\t)*)((true|false|null|[\-\.\d]+)|\'.+\'),/ie','\'\\1\'.$data.\',\'',$config);
            }
            save_file(COM_PATH.'/config.php',$config);
            ajax_success(t('settings/alert/save'),0);
        }
    }
    System::header(t('settings'));
    echo '<form id="form1" name="form1" method="post" action="">';
    echo '<fieldset><legend><a rel=".show"><img class="a2 os" src="../system/images/white.gif" />'.t('settings/site').'</a></legend>';
    echo '<div class="show">';
    echo '<p><label>'.t('settings/sitename').':</label><input class="in w200" type="text" name="SITE_NAME" id="SITE_NAME" value="'.c('SITE_NAME').'"></p>';

    echo '<p><label>'.t('settings/language').':</label>';
    echo '<select name="LANGUAGE" id="LANGUAGE">';
    echo form_opts('@.language','lang','<option value="#value#"#selected#>#name#</option>',c('LANGUAGE'));
    echo '</select></p>';

    echo '<p><label>'.t('settings/RSS_number').':</label>';
    echo '<select name="RSS_NUMBER" id="RSS_NUMBER" edit="true" default="'.c('RSS_NUMBER').'">';
    foreach (array(5,10,15,20,25,30,35,50,100) as $number) {
        $selected = c('RSS_NUMBER') == $number ? ' selected="selected"' : null;
        echo '<option value="'.$number.'"'.$selected.'>'.$number.'</option>';
    }
    echo '</select></p>';

    echo '<p><label>'.t('settings/Related_keywords').':</label><span help="settings/Related_keywords">';
    echo '<input type="radio" name="GET_RELATED_KEY" id="GET_RELATED_KEY[1]" value="true"'.((c('GET_RELATED_KEY') == 1) ? ' checked="checked"':null).'/><label for="GET_RELATED_KEY[1]">'.t('True').'</label> ';
    echo '<input type="radio" name="GET_RELATED_KEY" id="GET_RELATED_KEY[0]" value="false"'.((c('GET_RELATED_KEY') == 0) ? ' checked="checked"':null).'/><label for="GET_RELATED_KEY[0]">'.t('False').'</label>';
    echo '</span></p>';

    echo '</div></fieldset>';

    echo '<fieldset><legend><a rel=".show"><img class="a2 os" src="../system/images/white.gif" />'.t('settings/upload').'</a></legend>';
    echo '<div class="show">';
    echo '<p><label>'.t('settings/upload/allowext').':</label><input help="settings/allowext" class="in w300" type="text" name="UPLOAD_ALLOW_EXT" id="UPLOAD_ALLOW_EXT" value="'.c('UPLOAD_ALLOW_EXT').'"></p>';
    echo '<p><label>'.t('settings/upload/maxsize').':</label><input class="in w100" type="text" name="UPLOAD_MAX_SIZE" id="UPLOAD_MAX_SIZE" value="'.c('UPLOAD_MAX_SIZE').'"> KB</p>';
    echo '<p><label>'.t('settings/upload/filepath').':</label><input class="in w200" type="text" name="UPLOAD_FILE_PATH" id="UPLOAD_FILE_PATH" value="'.c('UPLOAD_FILE_PATH').'"></p>';
    echo '<p><label>'.t('settings/upload/imagepath').':</label><input class="in w200" type="text" name="UPLOAD_IMAGE_PATH" id="UPLOAD_IMAGE_PATH" value="'.c('UPLOAD_IMAGE_PATH').'"></p>';
    echo '<p><label>'.t('settings/upload/imageext').':</label><input class="in w200" type="text" name="UPLOAD_IMAGE_EXT" id="UPLOAD_IMAGE_EXT" value="'.c('UPLOAD_IMAGE_EXT').'"></p>';
    echo '</div></fieldset>';

    echo '<fieldset><legend><a rel=".show"><img class="a1 os" src="../system/images/white.gif" />'.t('settings/server').'</a></legend>';
    echo '<div class="show">';
    echo '<p><label>'.t('settings/server/timediff').':</label><input help="settings/timediff" class="in w50" type="text" name="TIME_DIFF" id="TIME_DIFF" value="'.c('TIME_DIFF').'"></p>';

    echo '<p><label>'.t('settings/server/DSN_config').':</label><input help="settings/DSN" class="in w400" type="text" name="DSN_CONFIG" id="DSN_CONFIG" value="'.c('DSN_CONFIG').'"></p>';

    echo '</div></fieldset>';

    echo but('system::save');
    echo '</form>';
}
// *** *** www.LazyCMS.net *** *** //
function lazy_after(){
    System::footer();
}
