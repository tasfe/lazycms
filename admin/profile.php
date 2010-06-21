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
// 加载公共文件
require dirname(__FILE__).'/admin.php';
// 取得管理员信息
$_ADMIN = LCUser::current();
// 动作
$action  = isset($_REQUEST['action'])?$_REQUEST['action']:null;
$referer = referer(PHP_FILE,false);
// 保存我的配置
if ($action=='save') {
	$validate = new Validate();
    if ($validate->post()) {
        $userid    = isset($_ADMIN['userid'])?$_ADMIN['userid']:null;
        $password  = isset($_POST['password1'])?$_POST['password1']:null;
        $password2 = isset($_POST['password2'])?$_POST['password2']:null;
        $nickname  = isset($_POST['nickname'])?$_POST['nickname']:null;
        $email     = isset($_POST['email'])?$_POST['email']:null;
        $url       = isset($_POST['url'])?$_POST['url']:null;
        $desc      = isset($_POST['description'])?$_POST['description']:null;
        // 验证email
        $validate->check(array(
            array('email',VALIDATE_EMPTY,__('Please enter an e-mail address.')),
            array('email',VALIDATE_IS_EMAIL,__('The e-mail address isn\'t correct.'))
        ));
        // 验证密码
        if ($password || $password2) {
            $validate->check('password1',VALIDATE_EQUAL,__('Please enter the same password in the two password fields.'),'password2');
        }
        
        // 验证通过
        if (!$validate->is_error()) {
            $user_info = array(
                'url'  => esc_html($url),
                'mail' => esc_html($email),
                'nickname' => esc_html($nickname),
                'description' => esc_html($desc),
            );
            // 修改密码
            if ($password) {
            	$user_info = array_merge($user_info,array(
            	   'pass' => md5($password.$_ADMIN['authcode'])
            	));
            }
            LCUser::editUser($userid,$user_info);
            // 保存用户信息
            admin_success(__('User updated.'),"LazyCMS.redirect('".$referer."');");
        }
    }
} else {
    // 标题
    admin_head('title',__('Profile'));
    admin_head('styles', array('css/user'));
    admin_head('scripts',array('js/user'));
    admin_head('loadevents','user_profile_init');
    $username = isset($_ADMIN['name'])?$_ADMIN['name']:null;
    $nickname = isset($_ADMIN['nickname'])?$_ADMIN['nickname']:null;
    $email    = isset($_ADMIN['mail'])?$_ADMIN['mail']:null;
    $url      = isset($_ADMIN['url'])?$_ADMIN['url']:null;
    $desc     = isset($_ADMIN['description'])?$_ADMIN['description']:null;
    include ADMIN_PATH.'/admin-header.php';
    echo '<div class="wrap">';
    echo   '<h2>'.admin_head('title').'</h2>';
    echo   '<form action="'.PHP_FILE.'" method="post" name="profile" id="profile">';
    echo     '<fieldset>';
    echo       '<table class="form-table">';
    echo           '<tr>';
    echo               '<th><label for="username">'.__('Username').'</label></th>';
    echo               '<td><input id="username" name="username" type="text" size="20" value="'.$username.'" disabled="disabled" /></td>';
    echo           '</tr>';
    echo           '<tr>';
    echo               '<th><label for="nickname">'.__('Nickname').'</label></th>';
    echo               '<td><input id="nickname" name="nickname" type="text" size="20" value="'.$nickname.'" /></td>';
    echo           '</tr>';
    echo           '<tr>';
    echo               '<th><label for="email">'.__('E-mail').' <span class="description">'.__('(required)').'</span></label></th>';
    echo               '<td><input id="email" name="email" type="text" size="40" value="'.$email.'" /></td>';
    echo           '</tr>';
    echo           '<tr>';
    echo               '<th><label for="url">'.__('Website').'</label></th>';
    echo               '<td><input id="url" name="url" type="text" size="60" value="'.$url.'" /></td>';
    echo           '</tr>';
    echo           '<tr>';
    echo               '<th><label for="description">'.__('Biographical Info').'</label></th>';
    echo               '<td><textarea cols="70" rows="5" id="description" name="description">'.$desc.'</textarea>';
    echo                   '<br/><span class="description">'.__('Share a little biographical information to fill out your profile. This may be shown publicly.').'</span>';
    echo               '</td>';
    echo           '</tr>';
    echo           '<tr>';
    echo               '<th><label for="password1">'.__('Password').' <span class="description">'.__('(twice)').'</span></label></th>';
    echo               '<td><input id="password1" name="password1" type="password" size="20" /> ';
    echo                   '<span class="description">'.__('If you would like to change the password type a new one. Otherwise leave this blank.').'</span>';
    echo                   '<br/><input id="password2" name="password2" type="password" size="20" /> <span class="description">'.__('Type your new password again.').'</span>';
    echo               '</td>';
    echo           '</tr>';
    echo       '</table>';
    echo     '</fieldset>';
    echo     '<input type="hidden" name="action" value="save" />';
    echo     '<input type="hidden" name="referer" value="'.$referer.'" />';
    echo     '<p class="submit"><button type="submit">'.__('Update Profile').'</button></p>';
    echo   '</form>';
    echo '</div>';
    include ADMIN_PATH.'/admin-footer.php';
}


