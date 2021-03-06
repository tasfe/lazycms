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
// 定义管理后台路径
defined('ADMIN_PATH') or define('ADMIN_PATH',dirname(__FILE__));
// 加载公共文件
include ADMIN_PATH.'/admin.php';
// 退出登录
$method = isset($_GET['method'])?$_GET['method']:null;
if ($method=='logout') {
    cookie_delete('authcode');
    redirect('login.php');
} else {
	$language = language();
}

// 只验证 POST 方式提交
if (validate_is_post()) {
    $username   = isset($_POST['username'])?$_POST['username']:null;
    $userpass   = isset($_POST['userpass'])?$_POST['userpass']:null;
    $language   = isset($_POST['language'])?$_POST['language']:null;
    $rememberme = isset($_POST['rememberme'])?$_POST['rememberme']:null;
    // 验证用户名
    validate_check(array(
        // 用户名不能为空
        array('username',VALIDATE_EMPTY,__('The username field is empty.')),
        // 用户名长度必须是2-30个字符
        array('username',VALIDATE_LENGTH,__('The username field length must be %d-%d characters.'),2,30)
    ));
    // 验证密码
    validate_check('userpass',VALIDATE_EMPTY,__('The password field is empty.'));
    // 验证通过
    if (validate_is_ok()) {
        // 提交到数据库验证用户名和密码
        if ($user = user_login($username,$userpass)) {
            $expire   = $rememberme=='forever' ? 365*86400 : 0;
            $language = $language=='default' ? C('Language') : $language;
            cookie_set('authcode',$user['authcode'],$expire);
            cookie_set('language',$language,$expire);
            redirect('index.php');
        } else {
            ajax_alert(__('Username or password error!'));
        }
    }
} else {
    if (user_current(false)) {
		redirect('index.php');
	}
}

// 登录页面
echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
echo '<html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
echo '<title>LazyCMS '.__('Administrator Login').'</title>'; loader_css('css/login'); loader_js('js/common'); loader_js('js/login');
echo '<script type="text/javascript">'.system_jslang().'</script>';
echo '</head>';
echo '<body>';
echo    '<form id="login" name="login" method="post" action="'.sprintf('%s?method=login',PHP_FILE).'">';
echo        '<div class="col1">'.sprintf(__('<p>LazyCMS is free, open source content management system.</p><p>Runtime:PHP 4.3.3+、MySQL 4.1+</p><p><a href="%s">Back Home</a></p>'),ROOT).'</div>';
echo        '<dl class="col2">';
echo            '<dt>'.__('Administrator Login').'</dt>';
echo            '<dd><label>'.__('Username').'</label><br/><input class="username text" type="text" name="username" id="username" tabindex="1" /></dd>';
echo            '<dd><label>'.__('Password').'</label><br/><input class="password text" type="password" name="userpass" id="userpass" tabindex="2" /></dd>';
echo            '<dd><label>'.__('Language').'</label><br/>';
echo                '<select name="language" id="language">';
echo                    '<option value="default">'.__('Default').'</option>';
echo                    '<option value="en"'.($language=='en'?' selected="selected"':null).'>'.__('English').'</option>';
echo                    options('@.locale','lang','<option value="#value#"#selected#>#name#</option>',$language);
echo                '</select>';
echo            '</dd>';
echo            '<dd class="remember"><input name="rememberme" type="checkbox" id="rememberme" value="forever" tabindex="3" /><label for="rememberme">'.__('Remember Me').'</label></dd>';
echo            '<dd class="submit"><button type="submit" tabindex="4">'.__('Login').'</button></dd>';
echo        '</dl>';
echo    '</form>';
echo '</body>';
echo '</html>';