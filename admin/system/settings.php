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
require '../../global.php';
/**
 * 系统设置
 * 
 */
// *** *** www.LazyCMS.net *** *** //
function lazy_before(){
    System::tabs(
        l('Settings').':settings.php;'.
        l('System info').':sysinfo.php;'.
        l('System config').':sysinfo.php?action=config;'.
        l('Directory').':sysinfo.php?action=directory;'.
        l('PHP Settings').':sysinfo.php?action=phpinfo'
    );
}
// *** *** www.LazyCMS.net *** *** //
function lazy_main(){
    $val = new Validate();
    if ($val->method()) {
        $DSN_CONFIG = isset($_POST['DSN_CONFIG']) ? $_POST['DSN_CONFIG'] : null;
        $val->check('SITE_NAME|0|'.l('Site check name'));
        $val->check('UPLOAD_ALLOW_EXT|0|'.l('Upload check allow ext').';UPLOAD_ALLOW_EXT|validate|'.l('Upload check error ext').'|^[\w\,]+$');
        $val->check('UPLOAD_MAX_SIZE|0|'.l('Upload check max size').';UPLOAD_MAX_SIZE|validate|'.l('Upload check max size is number').'|2');
        $val->check('UPLOAD_FILE_PATH|0|'.l('Upload check file path').';UPLOAD_FILE_PATH|5|'.l('Upload check error path'));
        $val->check('UPLOAD_IMAGE_PATH|0|'.l('Upload check image path').';UPLOAD_IMAGE_PATH|5|'.l('Upload check error path'));
        $val->check('UPLOAD_IMAGE_EXT|0|'.l('Upload check image ext').';UPLOAD_IMAGE_EXT|validate|'.l('Upload check error ext').'|^[\w\,]+$');
        $val->check('DSN_CONFIG|0|'.l('Upload check DSN config').';DSN_CONFIG|3|'.l('Upload check DSN config error format').'|'.validate($DSN_CONFIG,'^((\w+):\/\/path\=(.+)$)|(^(\w+):\/\/([^\/:]+)(:([^@]+)?)?@(\w+)(:(\d+))?(\/(\w+)\/(\w+)|\/(\w+))$)'));
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
                'TIME_ZONE',
                'DSN_CONFIG'
            );
            foreach ($fields as $field) {
                $data = isset($_POST[$field]) ? $_POST[$field] : c($field);
                if ($data!='true' && $data!='false' && !is_numeric($data)) {
                    $data = "'{$data}'";
                }
                $config = preg_replace('/(\''.$field.'\'( |\t)*\=\>( |\t)*)((true|false|null|[-\d]+)|\'.+\'),/ie','\'\\1\'.$data.\',\'',$config);
            }
            save_file(COM_PATH.'/config.php',$config);
            alert('MESSAGE',0);
        }
    }
    System::header(l('Settings'));
    echo '<form id="form1" name="form1" method="post" action="">';
    echo '<fieldset><legend><a class="collapsed" rel=".show">'.l('Site settings').'</a></legend>';
    echo '<div class="show">';
    echo '<p><label>'.l('Site name').':</label><input class="in2" type="text" name="SITE_NAME" id="SITE_NAME" value="'.c('SITE_NAME').'"></p>';

    echo '<p><label>'.l('Language').':</label>';
    echo '<select name="LANGUAGE" id="LANGUAGE">';
    echo form_opts('@.language','lang','<option value="#value#"#selected#>#name#</option>',c('LANGUAGE'));
    echo '</select></p>';

    echo '<p><label>'.l('RSS number').':</label>';
    echo '<select name="RSS_NUMBER" id="RSS_NUMBER">';
    foreach (array(5,10,15,20,25,30,35,50,100) as $number) {
        $selected = c('RSS_NUMBER') == $number ? ' selected="selected"' : null;
        echo '<option value="'.$number.'"'.$selected.'>'.$number.'</option>';
    }
    echo '</select></p>';
    
    echo '<p><label>'.l('Related keywords').':</label><span>';
    echo '<input type="radio" name="GET_RELATED_KEY" id="GET_RELATED_KEY[1]" value="true"'.((c('GET_RELATED_KEY') == 1) ? ' checked="checked"':null).'/><label for="GET_RELATED_KEY[1]">'.l('True').'</label> ';
    echo '<input type="radio" name="GET_RELATED_KEY" id="GET_RELATED_KEY[0]" value="false"'.((c('GET_RELATED_KEY') == 0) ? ' checked="checked"':null).'/><label for="GET_RELATED_KEY[0]">'.l('False').'</label>';
    echo '</span></p>';

    echo '</div></fieldset>';

    echo '<fieldset><legend><a class="collapsed" rel=".show">'.l('Upload settings').'</a></legend>';
    echo '<div class="show">';
    echo '<p><label>'.l('Upload allow ext').':</label><input class="in3" type="text" name="UPLOAD_ALLOW_EXT" id="UPLOAD_ALLOW_EXT" value="'.c('UPLOAD_ALLOW_EXT').'"></p>';
    echo '<p><label>'.l('Upload max size').':</label><input class="in1" type="text" name="UPLOAD_MAX_SIZE" id="UPLOAD_MAX_SIZE" value="'.c('UPLOAD_MAX_SIZE').'"></p>';
    echo '<p><label>'.l('Upload file path').':</label><input class="in2" type="text" name="UPLOAD_FILE_PATH" id="UPLOAD_FILE_PATH" value="'.c('UPLOAD_FILE_PATH').'"></p>';
    echo '<p><label>'.l('Upload image path').':</label><input class="in2" type="text" name="UPLOAD_IMAGE_PATH" id="UPLOAD_IMAGE_PATH" value="'.c('UPLOAD_IMAGE_PATH').'"></p>';
    echo '<p><label>'.l('Upload image ext').':</label><input class="in2" type="text" name="UPLOAD_IMAGE_EXT" id="UPLOAD_IMAGE_EXT" value="'.c('UPLOAD_IMAGE_EXT').'"></p>';
    echo '</div></fieldset>';
    
    echo '<fieldset><legend><a class="collapse" rel=".show">'.l('Server settings').'</a></legend>';
    echo '<div class="show">';
    echo '<p><label>'.l('Server time zone').':</label>';
    echo '<select name="TIME_ZONE" id="TIME_ZONE">';
    foreach (l('Time zone') as $hour => $zone) {
        $selected = (c('TIME_ZONE')==(string)$hour) ? 'selected="selected"' : null;
        echo '<option value="'.$hour.'"'.$selected.'>'.$zone.'</option>';
    }
    echo '</select></p>';

    echo '<p><label>'.l('Server DSN config').':</label><input class="in4" type="text" name="DSN_CONFIG" id="DSN_CONFIG" value="'.c('DSN_CONFIG').'"></p>';

    echo '</div></fieldset>';

    echo but('Save');
    echo '</form>';
}
// *** *** www.LazyCMS.net *** *** //
function lazy_after(){
    System::footer();
}