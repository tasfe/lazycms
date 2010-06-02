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
$_ADMIN = ModuleUser::current(); 
// 标题
admin_head('title',  _('Users'));
admin_head('styles', array('css/user'));
admin_head('scripts',array('js/user'));
// 动作
$action  = isset($_REQUEST['action'])?$_REQUEST['action']:null;

switch ($action) {
    // 添加用户
	case 'new':
	    // 权限检查
	    current_user_can('user-new');
	    // 重置标题
	    admin_head('title',_('Add New User'));
	    // 添加JS事件
	    admin_head('loadevents','user_manage_init');
	    include ADMIN_PATH.'/admin-header.php';
        // 显示页面
	    user_manage_page('add');
        include ADMIN_PATH.'/admin-footer.php';
	    break;
	// 编辑用户
	case 'edit':
	    // 所属
        $parent_file = 'user.php';
	    // 权限检查
	    current_user_can('user-edit');
	    // 重置标题
	    admin_head('title',_('Edit User'));
	    // 添加JS事件
	    admin_head('loadevents','user_manage_init');
	    include ADMIN_PATH.'/admin-header.php';
	    user_manage_page('edit');
        include ADMIN_PATH.'/admin-footer.php';
	    break;
    // 删除用户
    case 'delete':
        // 权限检查
	    current_user_can('user-delete');
        $userid = isset($_GET['userid'])?$_GET['userid']:null;
        if (ModuleUser::delete_user_by_id($userid)) {
        	admin_success(_('User deleted.'),"LazyCMS.redirect('".PHP_FILE."');");
        } else {
            admin_error(_('User delete fail.'));
        }
        break;
	// 保存用户
	case 'save':
	    $userid  = isset($_POST['userid'])?$_POST['userid']:null;
	    $purview = $userid?'user-edit':'user-new';
	    current_user_can($purview);
	    $validate = new Validate();
        if ($validate->post()) {
            $username  = isset($_POST['username'])?$_POST['username']:null;
            $password  = isset($_POST['password1'])?$_POST['password1']:null;
            $password2 = isset($_POST['password2'])?$_POST['password2']:null;
            $nickname  = isset($_POST['nickname'])?$_POST['nickname']:null;
            $email     = isset($_POST['email'])?$_POST['email']:null;
            $url       = isset($_POST['url'])?$_POST['url']:null;
            $desc      = isset($_POST['description'])?$_POST['description']:null;
            $roldes    = isset($_POST['roles'])?$_POST['roles']:array();
            if ($userid) {
            	$user = ModuleUser::get_user_by_id($userid); $is_exist = true; 
            	if ($username != $user['name']) {
            		$is_exist = ModuleUser::get_user_by_name($username)?false:true;
            	}
            	unset($user);
            } else {
                $is_exist = ModuleUser::get_user_by_name($username)?false:true;
            }
            // 验证用户名
            $validate->check(array(
                // 用户名不能为空
                array('username',VALIDATE_EMPTY,_('The username field is empty.')),
                // 用户名长度必须是2-30个字符
                array('username',VALIDATE_LENGTH,_('The username field length must be %d-%d characters.'),2,30),
                // 用户已存在
                array('username',$is_exist,_('The username already exists.')),
            ));
            // 验证email
            $validate->check(array(
                array('email',VALIDATE_EMPTY,_('Please enter an e-mail address.')),
                array('email',VALIDATE_IS_EMAIL,_('The e-mail address isn\'t correct.'))
            ));
            // 验证密码
            if ((!$userid) || $password) {
                $validate->check(array(
                    array('password1',VALIDATE_EMPTY,_('Please enter your password.')),
                    array('password2',VALIDATE_EMPTY,_('Please enter your password twice.')),
                    array('password1',VALIDATE_EQUAL,_('Please enter the same password in the two password fields.'),'password2'),
                ));
            }
            // 验证通过
            if (!$validate->is_error()) {
                $username = esc_html($username);
                $email    = esc_html($email);
                $user_info = array(
                    'url'  => esc_html($url),
                    'roles' => $roldes,
                    'nickname' => esc_html($nickname),
                    'Administrator' => 'Yes'
                );
                // 编辑
                if ($userid) {
                    $user_info = array_merge($user_info,array(
                        'username'    => $username,
                        'description' => esc_html($desc)
                    ));
                    // 修改密码
                    if ($password) {
                    	$user_info = array_merge($user_info,array(
                    	   'pass' => md5($password), 'authcode' => '',
                    	));
                    }
                    ModuleUser::fill_user_info($userid,$user_info);
                    // 保存用户信息
                    admin_success(_('User updated.'),"LazyCMS.redirect('".PHP_FILE."');");
                } 
                // 添加
                else {
                    ModuleUser::create_user($username,$password,$email,$user_info);
                    // 保存用户信息
                    admin_success(_('User created.'),"LazyCMS.redirect('".PHP_FILE."');");
                }
            }
        }
	    break;
	// 批量动作
	case 'bulk':
	    $actions = isset($_POST['actions'])?$_POST['actions']:null;
	    $listids = isset($_POST['listids'])?$_POST['listids']:null;
	    if (empty($listids)) {
	    	admin_error(_('Did not select any item.'));
	    }
	    switch ($actions) {
	        case 'delete':
	            current_user_can('user-delete');
	            foreach ($listids as $userid) {
	                if ($_ADMIN['userid']==$userid) continue;
	            	ModuleUser::delete_user_by_id($userid);
	            }
	            admin_success(_('Users deleted.'),"LazyCMS.redirect('".PHP_FILE."');"); 
	            break;
	    }
	    break;
	default:
	    current_user_can('user-list');
	    admin_head('loadevents','user_list_init');
	    $admins = ModuleUser::get_adminis();
        include ADMIN_PATH.'/admin-header.php';
        echo '<div class="wrap">';
        echo   '<h2>'.admin_head('title').'<a class="btn" href="'.PHP_FILE.'?action=new">'.__('Add New','user').'</a></h2>';
        echo   '<form action="'.PHP_FILE.'?action=bulk" method="post" name="userlist" id="userlist">';
        actions();
        echo       '<table class="data-table" cellspacing="0">';
        echo           '<thead>';
        thead();
        echo           '</thead>';
        echo           '<tfoot>';
        thead();
        echo           '</tfoot>';
        echo           '<tbody>';
        foreach ($admins as $admin) {
            if ($admin['userid']==$_ADMIN['userid']) {
            	$href = ADMIN_ROOT.'profile.php?referer='.PHP_FILE;
            	$actions = '<span class="edit"><a href="'.$href.'">'._('Edit').'</a></span>';
            } else {
                $href = PHP_FILE.'?action=edit&userid='.$admin['userid'];
                $actions = '<span class="edit"><a href="'.$href.'">'._('Edit').'</a> | </span>';
                $actions.= '<span class="delete"><a href="'.PHP_FILE.'?action=delete&userid='.$admin['userid'].'">'._('Delete').'</a></span>';
            }
            echo           '<tr>';
            echo               '<td class="check-column"><input type="checkbox" name="listids[]" value="'.$admin['userid'].'" /></td>';
            echo               '<td><strong><a href="'.$href.'">'.$admin['name'].'</a></strong><br/><div class="row-actions">'.$actions.'</div></td>';
            echo               '<td>'.$admin['mail'].'</td>';
            echo               '<td>'.$admin['status'].'</td>';
            echo               '<td>'.$admin['registered'].'</td>';
            echo           '</tr>';
        }
        echo           '</tbody>';
        echo       '</table>';
        actions();
        echo   '</form>';
        echo '</div>';
        include ADMIN_PATH.'/admin-footer.php';
        break;
}

/**
 * 批量操作
 *
 */
function actions() {
    echo '<div class="actions">';
    echo     '<select name="actions">';
    echo         '<option value="">'._('Bulk Actions').'</option>';
    echo         '<option value="delete">'._('Delete').'</option>';
    echo     '</select>';
    echo     '<button type="button">'._('Apply').'</button>';
    echo '</div>';
}
/**
 * 表头
 *
 */
function thead() {
    echo '<tr>';
    echo     '<th class="check-column"><input type="checkbox" name="select" value="all" /></th>';
    echo     '<th>'._('Username').'</th>';
    echo     '<th>'._('E-mail').'</th>';
    echo     '<th>'._('Status').'</th>';
    echo     '<th>'._('Registered').'</th>';
    echo '</tr>';
}

/**
 * 用户管理页面
 *
 * @param string $action
 */
function user_manage_page($action) {
    $referer = referer(PHP_FILE);
    $userid  = isset($_GET['userid'])?$_GET['userid']:0;
    if ($action!='add') {
    	$_USER  = ModuleUser::get_user_by_id($userid);
    }
    $username = isset($_USER['name'])?$_USER['name']:null;
    $nickname = isset($_USER['nickname'])?$_USER['nickname']:null;
    $email    = isset($_USER['mail'])?$_USER['mail']:null;
    $url      = isset($_USER['url'])?$_USER['url']:null;
    $desc     = isset($_USER['description'])?$_USER['description']:null;
    $roles    = isset($_USER['roles'])?$_USER['roles']:null;
    echo '<div class="wrap">';
    echo   '<h2>'.admin_head('title').'</h2>';
    echo   '<form action="'.PHP_FILE.'?action=save" method="post" name="usermanage" id="usermanage">';
    echo     '<fieldset>';
    echo       '<table class="form-table">';
    echo           '<tr>';
    echo               '<th><label for="username">'._('Username').' <span class="description">'._('(required)').'</span></label></th>';
    echo               '<td><input id="username" name="username" type="text" size="20" value="'.$username.'" /></td>';
    echo           '</tr>';
    echo           '<tr>';
    echo               '<th><label for="nickname">'._('Nickname').'</label></th>';
    echo               '<td><input id="nickname" name="nickname" type="text" size="20" value="'.$nickname.'" /></td>';
    echo           '</tr>';
    echo           '<tr>';
    echo               '<th><label for="email">'._('E-mail').' <span class="description">'._('(required)').'</span></label></th>';
    echo               '<td><input id="email" name="email" type="text" size="40" value="'.$email.'" /></td>';
    echo           '</tr>';
    echo           '<tr>';
    echo               '<th><label for="url">'._('Website').'</label></th>';
    echo               '<td><input id="url" name="url" type="text" size="60" value="'.$url.'" /></td>';
    echo           '</tr>';
    if ($action!='add') {
    	echo       '<tr>';
        echo           '<th><label for="description">'._('Biographical Info').'</label></th>';
        echo           '<td><textarea cols="70" rows="5" id="description" name="description">'.$desc.'</textarea>';
        echo               '<br/><span class="description">'._('Share a little biographical information to fill out your profile. This may be shown publicly.').'</span>';
        echo           '</td>';
        echo       '</tr>';
        echo       '<tr>';
        echo           '<th><label for="password1">'._('New Password').' <span class="description">'._('(twice)').'</span></label></th>';
        echo           '<td><input id="password1" name="password1" type="password" size="20" />';
        echo               '<span class="description">'._('If you would like to change the password type a new one. Otherwise leave this blank.').'</span>';
        echo               '<br/><input id="password2" name="password2" type="password" size="20" /> <span class="description">'._('Type your new password again.').'</span>';
        echo           '</td>';
        echo       '</tr>';;
    } else {
        echo       '<tr>';
        echo           '<th><label for="password1">'._('Password').' <span class="description">'._('(twice,required)').'</span></label></th>';
        echo           '<td><input id="password1" name="password1" type="password" size="20" /><br/><input id="password2" name="password2" type="password" size="20" /></td>';
        echo       '</tr>';
        echo       '<tr>';
        echo           '<th><label for="send_password">'._('Send Password?').'</label></th>';
        echo           '<td><label for="send_password"><input type="checkbox" name="send_password" id="send_password" value="1" /> '._('Send this password to the new user by email.').'</label></td>';
        echo       '</tr>';
    }
    echo           '<tr>';
    echo               '<th><label>'._('Role').'</label></th>';
    echo               '<td>';
    echo                    admin_purview($roles);
    echo               '</td>';
    echo           '</tr>';
    echo       '</table>';
    echo   '</fieldset>';
    if ($action=='add') {
        echo   '<p class="submit"><button type="submit">'._('Add User').'</button> <button type="button" onclick="self.location.replace(\''.$referer.'\')">'._('Back').'</button></p>';
    } else {
        echo   '<input type="hidden" name="userid" value="'.$userid.'" />';
        echo   '<p class="submit"><button type="submit">'._('Update User').'</button> <button type="button" onclick="self.location.replace(\''.$referer.'\')">'._('Back').'</button></p>';
    }
    echo   '</form>';
    echo '</div>';
}