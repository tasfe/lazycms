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
 * | Copyright (C) 2007-2008 LazyCMS.com All rights reserved.                  |
 * +---------------------------------------------------------------------------+
 * | LazyCMS is free software. This version use Apache License 2.0             |
 * | See LICENSE.txt for copyright notices and details.                        |
 * +---------------------------------------------------------------------------+
 */
// 加载公共文件
require dirname(__FILE__).'/admin.php';
// 查询管理员信息
$_USER = user_current();
// 标题
system_head('title',  __('Users'));
system_head('styles', array('css/user'));
system_head('scripts',array('js/user'));
// 动作
$method = isset($_REQUEST['method'])?$_REQUEST['method']:null;

switch ($method) {
    // 强力插入
	case 'new':
	    // 权限检查
	    current_user_can('user-new');
	    // 重置标题
	    system_head('title',__('Add New User'));
	    // 添加JS事件
	    system_head('loadevents','user_manage_init');
	    include ADMIN_PATH.'/admin-header.php';
        // 显示页面
	    user_manage_page('add');
        include ADMIN_PATH.'/admin-footer.php';
	    break;
	// 活塞式运动，你懂得。。。
	case 'edit':
	    // 所属
        $parent_file = 'user.php';
	    // 权限检查
	    current_user_can('user-edit');
	    // 重置标题
	    system_head('title',__('Edit User'));
	    // 添加JS事件
	    system_head('loadevents','user_manage_init');
	    include ADMIN_PATH.'/admin-header.php';
	    user_manage_page('edit');
        include ADMIN_PATH.'/admin-footer.php';
	    break;
	// 保存用户
	case 'save':
	    $userid = isset($_POST['userid'])?$_POST['userid']:null;
	    current_user_can($userid?'user-edit':'user-new');
	    
        if (validate_is_post()) {
            $username  = isset($_POST['username'])?$_POST['username']:null;
            $password  = isset($_POST['password1'])?$_POST['password1']:null;
            $password2 = isset($_POST['password2'])?$_POST['password2']:null;
            $nickname  = isset($_POST['nickname'])?$_POST['nickname']:null;
            $email     = isset($_POST['email'])?$_POST['email']:null;
            $url       = isset($_POST['url'])?$_POST['url']:null;
            $desc      = isset($_POST['description'])?$_POST['description']:null;
            $bcpwd     = isset($_POST['BanChangePassword'])?$_POST['BanChangePassword']:null;
            $mplogin   = isset($_POST['MultiPersonLogin'])?$_POST['MultiPersonLogin']:'Yes';
            $roldes    = isset($_POST['roles'])?$_POST['roles']:array();
            if ($userid) {
            	$user = user_get_byid($userid); $is_exist = true;
            	if ($username != $user['name']) {
            		$is_exist = user_get_byname($username)?false:true;
            	}
                if ($user['roles']=='ALL') $roldes = 'ALL';
            	unset($user);
            } else {
                $is_exist = user_get_byname($username)?false:true;
            }
            // 验证用户名
            validate_check(array(
                // 用户名不能为空
                array('username',VALIDATE_EMPTY,__('The username field is empty.')),
                // 用户名长度必须是2-30个字符
                array('username',VALIDATE_LENGTH,__('The username field length must be %d-%d characters.'),2,30),
                // 用户已存在
                array('username',$is_exist,__('The username already exists.')),
            ));
            // 验证email
            validate_check(array(
                array('email',VALIDATE_EMPTY,__('Please enter an e-mail address.')),
                array('email',VALIDATE_IS_EMAIL,__('You must provide an e-mail address.'))
            ));
            // 验证密码
            if ((!$userid) || $password) {
                validate_check(array(
                    array('password1',VALIDATE_EMPTY,__('Please enter your password.')),
                    array('password2',VALIDATE_EMPTY,__('Please enter your password twice.')),
                    array('password1',VALIDATE_EQUAL,__('Your passwords do not match. Please try again.'),'password2'),
                ));
            }
            // 验证通过
            if (validate_is_ok()) {
                $username = esc_html($username);
                $email    = esc_html($email);
                $user_info = array(
                    'url'  => esc_html($url),
                    'roles' => $roldes,
                    'nickname' => esc_html($nickname),
                    'Administrator' => 'Yes',
                    'BanChangePassword' => $bcpwd,
                    'MultiPersonLogin'  => $mplogin,
                );
                // 编辑
                if ($userid) {
                    $user_info = array_merge($user_info,array(
                        'username'    => $username,
                        'description' => esc_html($desc)
                    ));
                    // 修改暗号
                    if ($password) {
                    	$user_info = array_merge($user_info,array(
                    	   'pass' => md5($password), 'authcode' => '',
                    	));
                    }
                    user_edit($userid,$user_info);
                    ajax_success(__('User updated.'),"LazyCMS.redirect('".PHP_FILE."');");
                } 
                // 强力插入
                else {
                    user_add($username,$password,$email,$user_info);
                    ajax_success(__('User created.'),"LazyCMS.redirect('".PHP_FILE."');");
                }
            }
        }
	    break;
	// 批量动作
	case 'bulk':
	    $action  = isset($_POST['action'])?$_POST['action']:null;
	    $listids = isset($_POST['listids'])?$_POST['listids']:null;
	    if (empty($listids)) {
	    	ajax_error(__('Did not select any item.'));
	    }
	    switch ($action) {
	        case 'delete':
	            current_user_can('user-delete');
	            foreach ($listids as $userid) {
	                if ($_USER['userid']==$userid) continue;
	            	user_delete($userid);
	            }
	            ajax_success(__('Users deleted.'),"LazyCMS.redirect('".referer()."');");
	            break;
            default:
                ajax_alert(__('Parameter is invalid.'));
                break;
	    }
	    break;
	default:
	    current_user_can('user-list');
	    system_head('loadevents','user_list_init');
        $result = pages_query("SELECT `userid` FROM `#@_user_meta` WHERE `key`='Administrator' AND `VALUE`='Yes' ORDER BY `userid` ASC");
        include ADMIN_PATH.'/admin-header.php';
        echo '<div class="wrap">';
        echo   '<h2>'.system_head('title').'<a class="button" href="'.PHP_FILE.'?method=new">'._x('Add New','user').'</a></h2>';
        echo   '<form action="'.PHP_FILE.'?method=bulk" method="post" name="userlist" id="userlist">';
        table_nav();
        echo       '<table class="data-table" cellspacing="0">';
        echo           '<thead>';
        table_thead();
        echo           '</thead>';
        echo           '<tfoot>';
        table_thead();
        echo           '</tfoot>';
        echo           '<tbody>';
        while ($data = pages_fetch($result)) {
            $user = user_get_byid($data['userid']);
            if ($user['userid']==$_USER['userid']) {
            	$href = ADMIN.'profile.php?referer='.PHP_FILE;
            	$actions = '<span class="edit"><a href="'.$href.'">'.__('Edit').'</a></span>';
            } else {
                $href = PHP_FILE.'?method=edit&userid='.$user['userid'];
                $actions = '<span class="edit"><a href="'.$href.'">'.__('Edit').'</a> | </span>';
                $actions.= '<span class="delete"><a href="javascript:;" onclick="user_delete('.$user['userid'].')">'.__('Delete').'</a></span>';
            }
            echo           '<tr>';
            echo               '<td class="check-column"><input type="checkbox" name="listids[]" value="'.$user['userid'].'" /></td>';
            echo               '<td><strong><a href="'.$href.'">'.$user['name'].'</a></strong><br/><div class="row-actions">'.$actions.'</div></td>';
            echo               '<td>'.$user['mail'].'</td>';
            echo               '<td>'.get_icon('c'.($user['status']+3)).'</td>';
            echo               '<td>'.$user['registered'].'</td>';
            echo           '</tr>';
        }
        echo           '</tbody>';
        echo       '</table>';
        table_nav();
        echo   '</form>';
        echo '</div>';
        include ADMIN_PATH.'/admin-footer.php';
        break;
}

/**
 * 批量操作
 *
 */
function table_nav() {
    // 分页地址
    $page_url = PHP_FILE.'?'.http_build_query(array(
        'page' => '$',
    ));
    echo '<div class="table-nav">';
    echo     '<select name="actions">';
    echo         '<option value="">'.__('Bulk Actions').'</option>';
    echo         '<option value="delete">'.__('Delete').'</option>';
    echo     '</select>';
    echo     '<button type="button">'.__('Apply').'</button>';
    echo     pages_list($page_url);
    echo '</div>';
}
/**
 * 表头
 *
 */
function table_thead() {
    echo '<tr>';
    echo     '<th class="check-column"><input type="checkbox" name="select" value="all" /></th>';
    echo     '<th>'.__('Username').'</th>';
    echo     '<th>'.__('E-mail').'</th>';
    echo     '<th>'.__('Status').'</th>';
    echo     '<th>'.__('Registered').'</th>';
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
    	$_USER  = user_get_byid($userid);
    }
    $username = isset($_USER['name'])?$_USER['name']:null;
    $nickname = isset($_USER['nickname'])?$_USER['nickname']:null;
    $email    = isset($_USER['mail'])?$_USER['mail']:null;
    $url      = isset($_USER['url'])?$_USER['url']:null;
    $desc     = isset($_USER['description'])?$_USER['description']:null;
    $bcpwd    = isset($_USER['BanChangePassword'])?$_USER['BanChangePassword']:null;
    $mplogin  = isset($_USER['MultiPersonLogin'])?$_USER['MultiPersonLogin']:'No';
    $roles    = isset($_USER['roles'])?$_USER['roles']:null;
    echo '<div class="wrap">';
    echo   '<h2>'.system_head('title').'</h2>';
    echo   '<form action="'.PHP_FILE.'?method=save" method="post" name="usermanage" id="usermanage">';
    echo     '<fieldset>';
    echo       '<table class="form-table">';
    echo           '<tr>';
    echo               '<th><label for="username">'.__('Username').'<span class="resume">'.__('(required)').'</span></label></th>';
    echo               '<td><input class="text" id="username" name="username" type="text" size="20" value="'.$username.'" /></td>';
    echo           '</tr>';
    echo           '<tr>';
    echo               '<th><label for="nickname">'.__('Nickname').'</label></th>';
    echo               '<td><input class="text" id="nickname" name="nickname" type="text" size="20" value="'.$nickname.'" /></td>';
    echo           '</tr>';
    echo           '<tr>';
    echo               '<th><label for="email">'.__('E-mail').'<span class="resume">'.__('(required)').'</span></label></th>';
    echo               '<td><input class="text" id="email" name="email" type="text" size="40" value="'.$email.'" /></td>';
    echo           '</tr>';
    echo           '<tr>';
    echo               '<th><label for="url">'.__('Website').'</label></th>';
    echo               '<td><input class="text" id="url" name="url" type="text" size="60" value="'.$url.'" /></td>';
    echo           '</tr>';
    if ($action == 'add') {
        echo       '<tr>';
        echo           '<th><label for="password1">'.__('Password').'<span class="resume">'.__('(twice,required)').'</span></label></th>';
        echo           '<td><input class="text" id="password1" name="password1" type="password" size="20" /><br/><input class="text" id="password2" name="password2" type="password" size="20" />';
        echo           '<br /><div id="pass-strength-result" class="pass-strength">'.__('Strength indicator').'</div></td>';
        echo       '</tr>';
    } else {
        echo       '<tr>';
        echo           '<th><label for="description">'.__('Biographical Info').'</label></th>';
        echo           '<td><textarea class="text" cols="70" rows="5" id="description" name="description">'.$desc.'</textarea>';
        echo               '<br/><span class="resume">'.__('Share a little biographical information to fill out your profile. This may be shown publicly.').'</span>';
        echo           '</td>';
        echo       '</tr>';
        echo       '<tr>';
        echo           '<th><label for="password1">'.__('New Password').'<span class="resume">'.__('(twice)').'</span></label></th>';
        echo           '<td><input class="text" id="password1" name="password1" type="password" size="20" />';
        echo               ' <span class="resume">'.__('If you would like to change the password type a new one. Otherwise leave this blank.').'</span>';
        echo               '<br/><input class="text" id="password2" name="password2" type="password" size="20" /> <span class="resume">'.__('Type your new password again.').'</span>';
        echo               '<br /><div id="pass-strength-result" class="pass-strength">'.__('Strength indicator').'</div>';
        echo           '</td>';
        echo       '</tr>';
    }
    echo           '<tr>';
    echo               '<th><label>'.__('Role').'</label></th>';
    echo               '<td>';
    echo                    '<div class="role-list">';
    echo                        '<label for="BanChangePassword"><input type="checkbox" name="BanChangePassword" id="BanChangePassword" value="Yes"'.($bcpwd=='Yes'?' checked="checked"':null).' />'.__('Ban Change Password').'</label>';
    echo                        '<label for="MultiPersonLogin"><input type="checkbox" name="MultiPersonLogin" id="MultiPersonLogin" value="No"'.($mplogin=='No'?' checked="checked"':null).' />'.__('Prohibition multi-person to login').'</label>';
    echo                    '</div>';
    echo                    system_purview($roles);
    echo                    '<button type="button" rel="select">'.__('Select / Deselect').'</button>';
    echo               '</td>';
    echo           '</tr>';
    echo       '</table>';
    echo   '</fieldset>';
    echo   '<p class="submit">';
    if ($action=='add') {
        echo   '<button type="submit">'.__('Add User').'</button>';
    } else {
        echo   '<button type="submit">'.__('Update User').'</button><input type="hidden" name="userid" value="'.$userid.'" />';
    }
    echo       '<button type="button" onclick="LazyCMS.redirect(\''.$referer.'\')">'.__('Back').'</button>';
    echo   '</p>';
    echo  '</form>';
    echo '</div>';
}