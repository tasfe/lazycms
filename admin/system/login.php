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
    if (System::getAdmin()) { redirect('index.php'); return; }
    $val = new Validate();
    if ($val->method()) {
        $val->check('adminname|1|'.t('login/check/name').'|2-30');
        $val->check('adminpass|1|'.t('login/check/password').'|6-30');
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
            $_USER    = System::checkAdmin($adminname,$adminpass);
            if ($_USER) {
                // 验证管理员是否被锁定
                if ($_USER['islocked']) {
                    ajax_alert(t('login/check/locked'),0);
                }
                // 设置登陆信息
                Cookie::set('adminname',$_USER['adminname'],$cookie);
                Cookie::set('adminpass',$_USER['adminpass'],$cookie);
                Cookie::set('language',$_USER['language'],$cookie);
                redirect('index.php');
            } else {
                // 输出错误信息
                ajax_error(t('login/check/error'),0);
            }
            return ;
        }
    }
    echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
    echo '<html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
    echo '<title>'.t('login').'</title>';
    echo '<link href="images/style.css" rel="stylesheet" type="text/css" />';
    echo '<script type="text/javascript" src="../../common/js/jquery.js?ver=1.3"></script>';
    echo '<script type="text/javascript" src="../../common/js/lazycms.library.js?ver=1.0"></script>';
    echo '<script type="text/javascript" src="../../common/language/'.language().'/lang.js"></script>';
    echo '<script type="text/javascript">';
    echo '$(document).ready(function(){';
    echo '  $("#adminname").focus(); $("form[method=post]:not([ajax=false])").ajaxSubmit();';
    echo '});$.setStyle();</script>';
    echo '</head><body>';
    echo '<form id="login" name="login" method="post" action="'.PHP_FILE.'">';
    echo '<div class="col1">'.t('login/description').'</div>';
    echo '<dl class="col2">';
    echo '<dt>'.t('login').'</dt>';
    echo '<dd><label>'.t('login/name').'</label><input type="text" name="adminname" id="adminname" tabindex="1" /></dd>';
    echo '<dd><label>'.t('login/password').'</label><input type="password" name="adminpass" id="adminpass" tabindex="2" /></dd>';
    echo '<dd><label>'.t('login/cookie/expire').'</label>';
    echo '<select name="cookie" id="cookie">';
    $expire = array(
        0        => 'process',
        3600     => 'hour',
        86400    => 'day',
        604800   => 'week',
        2592000  => 'month',
        31536000 => 'permanent',
    );
    foreach ($expire as $v) {
        echo '<option value="'.$v.'">'.t('login/cookie/'.$v).'</option>';
    }
    echo '</select>';
    echo '</dd>';
    echo '<dd><label>'.t('login/language').'</label>';
    echo '<select name="language" id="language">';
    echo '<option value="default">'.t('default').'</option>';
    echo form_opts('@.language','lang','<option value="#value#"#selected#>#name#</option>');
    echo '</select>';
    echo '<dd><button type="submit" tabindex="3">'.t('login/submit').'</button> <button type="reset">'.t('reset').'</button></dd>';
    echo '</dl>';
    echo '</form></body></html>';
    echo($hl);
}