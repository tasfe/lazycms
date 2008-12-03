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
 * 后台登录
 *
 */
// *** *** www.LazyCMS.net *** *** //
function lazy_main() {
    if (check_admin('system')) { redirect('main.php'); return; }
    $val = new Validate();
    if ($val->method()) {
        $val->check('adminname|1|'.l('Login check name').'|2-30');
        $val->check('adminpass|1|'.l('Login check pass').'|6-30');
        if ($val->isVal()) {
            $val->out();
        } else {
            $db = get_conn();
            $adminname = isset($_POST['adminname']) ? $_POST['adminname'] : null;
            $adminpass = isset($_POST['adminpass']) ? $_POST['adminpass'] : null;
            $language = isset($_POST['language']) ? $_POST['language'] : null;
            $language = ($language == 'default')  ? c('LANGUAGE')      : $language;
            $cookie   = isset($_POST['cookie'])   ? $_POST['cookie']   : null;
            $cookie   = empty($cookie) ? $cookie : (now() + $cookie);
            $_USER    = check_admin($adminname,$adminpass);
            if ($_USER) {
                // 设置登陆信息
                Cookie::set('adminname',$_USER['adminname'],$cookie);
                Cookie::set('adminpass',$_USER['adminpass'],$cookie);
                Cookie::set('language',$_USER['language'],$cookie);
                // TODO: 跳转到main.php
                redirect('index.php');
            } else {
                // TODO: 输出错误信息
                echo_json('ALERT','用户名密码不正确');
            }
            return;
        }
    }
    $hl = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
    $hl.= '<html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
    $hl.= '<title>'.l('Login title').'</title>';
    $hl.= '<link href="images/style.css" rel="stylesheet" type="text/css" />';
    $hl.= '<script type="text/javascript" src="../../common/js/jquery.js?ver=1.2.6"></script>';
    $hl.= '<script type="text/javascript" src="../../common/js/lazycms.library.js?ver=1.0"></script>';
    $hl.= '<script type="text/javascript"> $(document).ready(function(){ $("#username").focus(); $("form[method=post]:not(form[ajax=false])").ajaxSubmit(); }); </script>';
    $hl.= '</head><body>';
    $hl.= '<form id="login" name="login" method="post" action="'.PHP_FILE.'">';
    $hl.= '<div class="col1">'.l('Login description').'</div>';
    $hl.= '<dl class="col2">';
    $hl.= '<dt>'.l('Login title').'</dt>';
    $hl.= '<dd><label>'.l('Login name').'</label><input type="text" name="adminname" id="adminname" tabindex="1" /></dd>';
    $hl.= '<dd><label>'.l('Login pass').'</label><input type="password" name="adminpass" id="adminpass" tabindex="2" /></dd>';
    $hl.= '<dd><label>'.l('Cookie expire').'</label>';
    $hl.= '<select name="cookie" id="cookie">';
    $expire = array(
        0        => 'process',
        3600     => '1 hour',
        86400    => '1 day',
        604800   => '1 week',
        2592000  => '1 month',
        31536000 => 'permanent',
    );
    foreach ($expire as $v) {
        $hl.= '<option value="'.$v.'">'.l('Cookie expire '.$v).'</option>';
    }
    $hl.= '</select>';
    $hl.= '</dd>';
    $hl.= '<dd><label>'.l('Login language').'</label>';
    $hl.= '<select name="language" id="language">';
    $hl.= '<option value="default">'.l('Default').'</option>';
    $hl.= form_opts('@.language','lang','<option value="#value#"#selected#>#name#</option>');
    $hl.= '</select>';
    $hl.= '<dd><button type="submit" tabindex="3">'.l('Login submit').'</button> <button type="reset">'.l('Reset').'</button></dd>';
    $hl.= '</dl>';
    $hl.= '</form></body></html>';
    echo($hl);
}