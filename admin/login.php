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
require '../global.php';
/**
 * 后台登录
 *
 * @copyright   Copyright (c) 2007-2008 lazycms.net All rights reserved.
 * @author      Lukin <mylukin@gmail.com>
 * @date        2008-6-18
 */

// lazy_before *** *** www.LazyCMS.net *** ***
function lazy_before(){
    // 定义当前模块，用来设置语言包
    G('MODULE','system');
}

// lazy_default *** *** www.LazyCMS.net *** ***
function lazy_default(){
    if (check_user('manage')) {
        redirect('manage.php'); return ;
    }
    $hl = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
    $hl.= '<html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
    $hl.= '<title>'.L('login/@title').'</title>';
    $hl.= '<link href="system/images/style.css" rel="stylesheet" type="text/css" />';
    $hl.= '<script type="text/javascript" src="../common/js/jquery.js?ver=1.2.6"></script>';
    $hl.= '<script type="text/javascript" src="../common/js/jquery.lazycms.js?ver=1.0"></script>';
    $hl.= '<script type="text/javascript"> $(document).ready(function(){ $("#username").focus(); }); </script>';
    $hl.= '</head><body>';
    $hl.= '<form id="login" name="login" method="post" action="login.php?action=check">';
    $hl.= '<div class="col1">'.L('login/description',array('root'=>'../')).'</div>';
    $hl.= '<dl class="col2">';
    $hl.= '<dt>'.L('login/@title').'</dt>';
    $hl.= '<dd><label>'.L('login/name').'</label><input type="text" name="username" id="username" tabindex="1" /></dd>';
    $hl.= '<dd><label>'.L('login/pass').'</label><input type="password" name="userpass" id="userpass" tabindex="2" /></dd>';
    $hl.= '<dd><label>'.L('login/cookie/@label').'</label>';
    $hl.= '<select name="cookie" id="cookie">';
    $hl.= '<option value="0" selected="selected">'.L('login/cookie/option1').'</option>';
    $hl.= '<option value="3600">'.L('login/cookie/option2').'</option>';
    $hl.= '<option value="86400">'.L('login/cookie/option3').'</option>';
    $hl.= '<option value="604800">'.L('login/cookie/option4').'</option>';
    $hl.= '<option value="2592000">'.L('login/cookie/option5').'</option>';
    $hl.= '<option value="31536000">'.L('login/cookie/option6').'</option>';
    $hl.= '</select>';
    $hl.= '</dd>';
    $hl.= '<dd><label>'.L('login/language').'</label>';
    $hl.= '<select name="language" id="language">';
    $hl.= '<option value="default">'.L('common/default').'</option>';
    $hl.= form_opts('@.language','xml','<option value="#value#"#selected#>#name#</option>');
    $hl.= '</select>';
    $hl.= '<dd><button type="submit" onclick="return $(this.form).ajaxSubmit();" tabindex="3">'.L('login/submit').'</button> <button type="reset">'.L('common/reset').'</button></dd>';
    $hl.= '</dl>';
    $hl.= '</form></body></html>';
    echo($hl);
}

// lazy_check *** *** www.LazyCMS.net *** ***
function lazy_check(){
    $val = new Validate(); if (!$val->method()) { return ;}
    $val->check('username|1|'.L('login/check/name').'|2-30');
    $val->check('userpass|1|'.L('login/check/pass').'|6-30');
    if ($val->isVal()) {
        $val->out();
    } else {
        $db = get_conn();
        $username = isset($_POST['username']) ? $_POST['username'] : null;
        $userpass = isset($_POST['userpass']) ? $_POST['userpass'] : null;
        $language = isset($_POST['language']) ? $_POST['language'] : null;
        $language = ($language == 'default')  ? C('LANGUAGE')      : $language;
        $cookie   = isset($_POST['cookie'])   ? $_POST['cookie']   : null;
        $cookie   = empty($cookie) ? $cookie : (now() + $cookie);
        $_USER    = check_user($username,$userpass);
        if ($_USER===false) {
            // 没有此管理员
            echo_json(array('text' => L('login/check/error1')),0);
        } elseif ($_USER === -1) {
            // 密码错误
            echo_json(array('text' => L('login/check/error2')),0);
        } else {
            // 设置登陆信息
            Cookie::set('username',$_USER['username'],$cookie);
            Cookie::set('userpass',$_USER['userpass'],$cookie);
            Cookie::set('language',$_USER['language'],$cookie);
            // 输出登录成功的信息
            echo_json(array(
                'text'  => L('login/success'),
                'sleep' => 3,
                'url'   => 'manage.php',
            ),1);
        }
    }
}
