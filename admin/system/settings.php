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
require '../../global.php';
/**
 * 系统设置
 *
 * @copyright   Copyright (c) 2007-2008 lazycms.net All rights reserved.
 * @author      Lukin <mylukin@gmail.com>
 * @date        2008-6-26
 */

// lazy_before *** *** www.LazyCMS.net *** ***
function lazy_before(){
    // 设置公共菜单
    G('TABS',L('settings/@title').':settings.php');
}
// lazy_default *** *** www.LazyCMS.net *** ***
function lazy_default(){ 
    check_login('settings'); require_file('common.php');
    $val = new Validate();
    if ($val->method()) {
        $DSN_CONFIG = isset($_POST['DSN_CONFIG']) ? $_POST['DSN_CONFIG'] : null;
        $val->check('SITE_NAME|0|'.L('settings/check/sitename'))
            ->check('UPLOAD_ALLOW_EXT|0|'.L('settings/check/allowext').';UPLOAD_ALLOW_EXT|validate|'.L('settings/check/errorext').'|^[\w\,]+$')
            ->check('UPLOAD_MAX_SIZE|0|'.L('settings/check/maxsize').';UPLOAD_MAX_SIZE|validate|'.L('settings/check/maxsize1').'|2')
            ->check('UPLOAD_FILE_PATH|0|'.L('settings/check/filepath').';UPLOAD_FILE_PATH|5|'.L('settings/check/errorpath'))
            ->check('UPLOAD_IMAGE_PATH|0|'.L('settings/check/imagepath').';UPLOAD_IMAGE_PATH|5|'.L('settings/check/errorpath'))
            ->check('UPLOAD_IMAGE_EXT|0|'.L('settings/check/imageext').';UPLOAD_IMAGE_EXT|validate|'.L('settings/check/errorext').'|^[\w\,]+$')
            ->check('DSN_CONFIG|0|'.L('settings/check/dsnconfig').';DSN_CONFIG|3|'.L('settings/check/dsnconfig1').'|'.validate($DSN_CONFIG,'^((\w+):\/\/path\=(.+)$)|(^(\w+):\/\/([^\/:]+)(:([^@]+)?)?@(\w+)(:(\d+))?(\/(\w+)\/(\w+)|\/(\w+))$)'))
            ;
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
                'TEMPLATE',
                'RSS_NUMBER',
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
                $data = isset($_POST[$field]) ? $_POST[$field] : C($field);
                if ($data!='true' && $data!='false' && !is_numeric($data)) {
                    $data = "'{$data}'";
                }
                $config = preg_replace('/(\''.$field.'\'( |\t)*\=\>( |\t)*)((true|false|null|[-\d]+)|\'.+\'),/ie','\'\\1\'.$data.\',\'',$config);
            }
            save_file(COM_PATH.'/config.php',$config);
            echo_json(L('settings/success'),1);
        }
    }
    $hl = '<form id="form1" name="form1" method="post" action="">';
    $hl.= '<fieldset><legend><a href="#" onclick="toggleFieldset(this,\'.show\')" class="collapsed">'.L('settings/site/@title').'</a></legend>';
    $hl.= '<div class="show">';
    $hl.= '<p><label>'.L('settings/site/sitename').'：</label><input tip="'.L('settings/site/sitename').'::'.L('settings/site/sitename/@tip').'" class="in2" type="text" name="SITE_NAME" id="SITE_NAME" value="'.C('SITE_NAME').'"></p>';

    $hl.= '<p><label>'.L('settings/site/language').'：</label>';
    $hl.= '<select name="LANGUAGE" id="LANGUAGE" tip="'.L('settings/site/language').'::'.L('settings/site/language/@tip').'">';
    $hl.= form_opts('@.language','xml','<option value="#value#"#selected#>#name#</option>',C('LANGUAGE'));
    $hl.= '</select></p>';

    $hl.= '<p><label>'.L('settings/site/template').'：</label>';
    $hl.= '<select name="TEMPLATE" id="TEMPLATE" tip="'.L('settings/site/template').'::'.L('settings/site/template/@tip').'">';
    $hl.= form_opts('themes','dir','<option value="#value#"#selected#>#name#</option>',C('TEMPLATE'));
    $hl.= '</select></p>';

    $hl.= '<p><label>'.L('settings/site/rssnumber').'：</label>';
    $hl.= '<select name="RSS_NUMBER" id="RSS_NUMBER" tip="'.L('settings/site/rssnumber').'::'.L('settings/site/rssnumber/@tip').'">';
    foreach (array(5,10,15,20,25,30,35,50,100) as $number) {
        $selected = C('RSS_NUMBER') == $number ? ' selected="selected"' : null;
        $hl.= '<option value="'.$number.'"'.$selected.'>'.$number.L('common/unit/item').'</option>';
    }
    $hl.= '</select></p>';
    $hl.= '</div></fieldset>';

    $hl.= '<fieldset><legend><a href="#" onclick="toggleFieldset(this,\'.show\')" class="collapsed">'.L('settings/user/@title').'</a></legend>';
    $hl.= '<div class="show">';
    $hl.= '<p><label>'.L('settings/user/allowreg/@title').'：</label><span tip="'.L('settings/user/allowreg/@title').'::'.L('settings/user/allowreg/@tip').'">';
    $hl.= '<input type="radio" name="USER_ALLOW_REG" id="USER_ALLOW_REG[1]" value="true"'.((C('USER_ALLOW_REG') == 1) ? ' checked="checked"':null).'/><label for="USER_ALLOW_REG[1]">'.L('settings/user/allowreg/true').'</label> ';
    $hl.= '<input type="radio" name="USER_ALLOW_REG" id="USER_ALLOW_REG[0]" value="false"'.((C('USER_ALLOW_REG') == 0) ? ' checked="checked"':null).'/><label for="USER_ALLOW_REG[0]">'.L('settings/user/allowreg/false').'</label>';
    $hl.= '</span></p>';

    $hl.= '<p><label>'.L('settings/user/group').'：</label>';
    $hl.= '<select name="USER_GROUP_REG" id="USER_GROUP_REG" tip="'.L('settings/user/group').'::'.L('settings/user/group/@tip').'">';
    $hl.= System::__group(0,C('USER_GROUP_REG'));
    $hl.= '</select></p>';

    $hl.= '<p><label>'.L('settings/user/active/@title').'：</label><span tip="'.L('settings/user/active/@title').'::350::'.L('settings/user/active/@tip').'">';
    $hl.= '<input type="radio" name="USER_ACTIVE_REG" id="USER_ACTIVE_REG[1]" value="true"'.((C('USER_ALLOW_REG') == 1) ? ' checked="checked"':null).'/><label for="USER_ACTIVE_REG[1]">'.L('settings/user/active/true').'</label> ';
    $hl.= '<input type="radio" name="USER_ACTIVE_REG" id="USER_ACTIVE_REG[0]" value="false"'.((C('USER_ALLOW_REG') == 0) ? ' checked="checked"':null).'/><label for="USER_ACTIVE_REG[0]">'.L('settings/user/active/false').'</label>';
    $hl.= '</span></p>';

    $hl.= '</div></fieldset>';

    $hl.= '<fieldset><legend><a href="#" onclick="toggleFieldset(this,\'.show\')" class="collapsed">'.L('settings/upload/@title').'</a></legend>';
    $hl.= '<div class="show">';
    $hl.= '<p><label>'.L('settings/upload/allowext').'：</label><input tip="'.L('settings/upload/allowext').'::'.L('settings/upload/allowext/@tip').'" class="in3" type="text" name="UPLOAD_ALLOW_EXT" id="UPLOAD_ALLOW_EXT" value="'.C('UPLOAD_ALLOW_EXT').'"></p>';
    $hl.= '<p><label>'.L('settings/upload/maxsize').'：</label><input tip="'.L('settings/upload/maxsize').'::'.L('settings/upload/maxsize/@tip').'" class="in1" type="text" name="UPLOAD_MAX_SIZE" id="UPLOAD_MAX_SIZE" value="'.C('UPLOAD_MAX_SIZE').'"></p>';
    $hl.= '<p><label>'.L('settings/upload/filepath').'：</label><input tip="'.L('settings/upload/filepath').'::'.L('settings/upload/filepath/@tip').'" class="in2" type="text" name="UPLOAD_FILE_PATH" id="UPLOAD_FILE_PATH" value="'.C('UPLOAD_FILE_PATH').'"></p>';
    $hl.= '<p><label>'.L('settings/upload/imagepath').'：</label><input tip="'.L('settings/upload/imagepath').'::'.L('settings/upload/imagepath/@tip').'" class="in2" type="text" name="UPLOAD_IMAGE_PATH" id="UPLOAD_IMAGE_PATH" value="'.C('UPLOAD_IMAGE_PATH').'"></p>';
    $hl.= '<p><label>'.L('settings/upload/imageext').'：</label><input tip="'.L('settings/upload/imageext').'::300::'.L('settings/upload/imageext/@tip').'" class="in2" type="text" name="UPLOAD_IMAGE_EXT" id="UPLOAD_IMAGE_EXT" value="'.C('UPLOAD_IMAGE_EXT').'"></p>';
    $hl.= '</div></fieldset>';
    
    $TIME_ZONE = include_file(COM_PATH.'/data/timezone.php');
    $TIME_ZONE = isset($TIME_ZONE[C('LANGUAGE')]) ? $TIME_ZONE[C('LANGUAGE')] : $TIME_ZONE['en'];
    $hl.= '<fieldset><legend><a href="#" onclick="toggleFieldset(this,\'.hide\')" class="collapse">'.L('settings/server/@title').'</a></legend>';
    $hl.= '<div class="hide">';
    $hl.= '<p><label>'.L('settings/server/timezone').'：</label>';
    $hl.= '<select name="TIME_ZONE" id="TIME_ZONE" tip="'.L('settings/server/timezone').'::350::'.h2encode(ubbencode(L('settings/server/timezone/@tip',array('zone'=>$TIME_ZONE[C('TIME_ZONE')])))).'">';
    foreach ($TIME_ZONE as $hour => $zone) {
        $selected = (C('TIME_ZONE')==(string)$hour) ? 'selected="selected"' : null;
        $hl.= '<option value="'.$hour.'"'.$selected.'>'.$zone.'</option>';
    }
    $hl.= '</select></p>';

    $hl.= '<p><label>'.L('settings/server/dsnconfig').'：</label><input tip="'.L('settings/server/dsnconfig').'::300::'.h2encode(ubbencode(L('settings/server/dsnconfig/@tip'))).'" class="in4" type="text" name="DSN_CONFIG" id="DSN_CONFIG" value="'.C('DSN_CONFIG').'">&nbsp;<img src="../../common/images/icon/help.png" tip="'.L('settings/server/dsnformat').'::400::'.h2encode(ubbencode(L('settings/server/dsnformat/@tip'))).'" class="os"/></p>';

    $hl.= '</div></fieldset>';

    $hl.= but('save');
    $hl.= '</form>';
    print_x(L('settings/@title'),$hl);
}