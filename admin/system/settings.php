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
        $val->check('SITE_NAME|0|'.L('settings/check/sitename'));
        if ($val->isVal()) {
            $val->out();
        } else {
            echo_json(array(
                'text' => '保存成功',
            ),1);
        }
    }
    $hl = '<form id="form1" name="form1" method="post" action="">';
    $hl.= '<fieldset><legend>'.L('settings/site/@title').'</legend>';
    $hl.= '<p><label>'.L('settings/site/sitename').'：</label><input tip="'.L('settings/site/sitename').'::'.L('settings/site/sitename/@tip').'" class="in2" type="text" name="SITE_NAME" id="SITE_NAME" value="'.C('SITE_NAME').'"></p>';

    $hl.= '<p><label>'.L('settings/site/language').'：</label>';
    $hl.= '<select name="LANGUAGE" id="LANGUAGE" tip="'.L('settings/site/language').'::'.L('settings/site/language/@tip').'">';
    $hl.= form_opts('@.language','xml','<option value="#value#"#selected#>#name#</option>');
    $hl.= '</select></p>';

    $hl.= '<p><label>'.L('settings/site/rssfeed').'：</label>';
    $hl.= '<select name="RSSFEED_NUMBER" id="RSSFEED_NUMBER" tip="'.L('settings/site/rssfeed').'::'.L('settings/site/rssfeed/@tip').'">';
    foreach (array(5,10,15,20,25,30,35,50,100) as $number) {
        $hl.= '<option value="'.$number.'">'.$number.L('common/unit/item').'</option>';
    }
    $hl.= '</select></p>';
    $hl.= '</fieldset>';

    $hl.= '<fieldset><legend>'.L('settings/user/@title').'</legend>';

    $hl.= '<p><label>'.L('settings/user/allowreg/@title').'：</label><span tip="'.L('settings/user/allowreg/@title').'::'.L('settings/user/allowreg/@tip').'">';
    $hl.= '<input type="radio" name="USER_PARAMS[allowReg]" id="USER_PARAMS[allowReg][1]" value="true" checked="checked"/><label for="USER_PARAMS[allowReg][1]">'.L('settings/user/allowreg/true').'</label> ';
    $hl.= '<input type="radio" name="USER_PARAMS[allowReg]" id="USER_PARAMS[allowReg][0]" value="false" /><label for="USER_PARAMS[allowReg][0]">'.L('settings/user/allowreg/false').'</label>';
    $hl.= '</span></p>';

    $hl.= '<p><label>'.L('settings/user/group').'：</label>';
    $hl.= '<select name="USER_PARAMS[groupid]" id="USER_PARAMS[groupid]" tip="'.L('settings/user/group').'::'.L('settings/user/group/@tip').'">';
    $hl.= System::__group(0,0);
    $hl.= '</select></p>';

    $hl.= '<p><label>'.L('settings/user/active/@title').'：</label><span tip="'.L('settings/user/active/@title').'::350::'.L('settings/user/active/@tip').'">';
    $hl.= '<input type="radio" name="USER_PARAMS[active]" id="USER_PARAMS[active][1]" value="true" checked="checked"/><label for="USER_PARAMS[active][1]">'.L('settings/user/active/true').'</label> ';
    $hl.= '<input type="radio" name="USER_PARAMS[active]" id="USER_PARAMS[active][0]" value="false" /><label for="USER_PARAMS[active][0]">'.L('settings/user/active/false').'</label>';
    $hl.= '</span></p>';

    $hl.= '</fieldset>';

    $hl.= '<fieldset><legend>'.L('settings/upload/@title').'</legend>';
    $hl.= '<p><label>'.L('settings/upload/allowext').'：</label><input tip="'.L('settings/upload/allowext').'::'.L('settings/upload/allowext/@tip').'" class="in3" type="text" name="UPLOAD_PARAMS[allowExt]" id="UPLOAD_PARAMS[allowExt]" value="bmp,png,gif,jpg,jpeg,zip,rar,doc,xls"></p>';
    $hl.= '<p><label>'.L('settings/upload/maxsize').'：</label><input tip="'.L('settings/upload/maxsize').'::'.L('settings/upload/maxsize/@tip').'" class="in1" type="text" name="UPLOAD_PARAMS[maxSize]" id="UPLOAD_PARAMS[maxSize]" value="10000000"></p>';
    $hl.= '<p><label>'.L('settings/upload/filepath').'：</label><input tip="'.L('settings/upload/filepath').'::'.L('settings/upload/filepath/@tip').'" class="in2" type="text" name="UPLOAD_PARAMS[filePath]" id="UPLOAD_PARAMS[filePath]" value="images"></p>';
    $hl.= '<p><label>'.L('settings/upload/imagepath').'：</label><input tip="'.L('settings/upload/imagepath').'::'.L('settings/upload/imagepath/@tip').'" class="in2" type="text" name="UPLOAD_PARAMS[imagePath]" id="UPLOAD_PARAMS[imagePath]" value="images/stories"></p>';
    $hl.= '<p><label>'.L('settings/upload/imageext').'：</label><input tip="'.L('settings/upload/imageext').'::300::'.L('settings/upload/imageext/@tip').'" class="in2" type="text" name="UPLOAD_PARAMS[imageExt]" id="UPLOAD_PARAMS[imageExt]" value="bmp,png,gif,jpg,jpeg"></p>';
    $hl.= '</fieldset>';
    
    $TIME_ZONE = include_file(COM_PATH.'/data/timezone.php');
    $TIME_ZONE = isset($TIME_ZONE[C('LANGUAGE')]) ? $TIME_ZONE[C('LANGUAGE')] : $TIME_ZONE['en'];
    $hl.= '<fieldset><legend>'.L('settings/server/@title').'</legend>';
    $hl.= '<p><label>'.L('settings/server/timezone').'：</label>';
    $hl.= '<select name="TIME_ZONE" id="TIME_ZONE" tip="'.L('settings/server/timezone').'::350::'.h2encode(ubbencode(L('settings/server/timezone/@tip',array('zone'=>$TIME_ZONE[C('TIME_ZONE')])))).'">';
    foreach ($TIME_ZONE as $hour => $zone) {
        $selected = (C('TIME_ZONE')==(string)$hour) ? 'selected="selected"' : null;
        $hl.= '<option value="'.$hour.'"'.$selected.'>'.$zone.'</option>';
    }
    $hl.= '</select></p>';

    $hl.= '<p><label>'.L('settings/server/dsnconfig').'：</label><input tip="'.L('settings/server/dsnconfig').'::300::'.h2encode(ubbencode(L('settings/server/dsnconfig/@tip'))).'" class="in4" type="text" name="DSN_CONFIG" id="DSN_CONFIG" value="'.C('DSN_CONFIG').'">&nbsp;<img src="../../common/images/icon/help.png" tip="'.L('settings/server/dsnformat').'::400::'.h2encode(ubbencode(L('settings/server/dsnformat/@tip'))).'"/></p>';

    $hl.= '</fieldset>';

    $hl.= but('save');
    $hl.= '</form>';
    print_x(L('settings/@title'),$hl);
}