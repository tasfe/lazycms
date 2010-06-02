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
defined('COM_PATH') or die('Restricted access!');

class ModuleUser {
    /**
	 * 验证用户是否登录成功
	 *
	 * @return bool
	 */
	function current($is_redirect=true){
	    $user = null;
        // 取得 authcode
        $authcode = cookie::get('authcode');
        $is_login = $authcode?true:false;
        // 执行用户验证
        if ($is_login) {
            if ($user = ModuleUser::get_user_by_auth($authcode)) {
                $is_login = true;
            } else {
                $is_login = false;
            }
        }
        // 未登录，且跳转
        if (!$is_login && $is_redirect) {
        	if (is_ajax()) {
        		// 显示未登录的提示警告
        		echo_json('Alert',_('You are now logged out, please log in again!'),array('CALL'=>"LazyCMS.redirect('".ADMIN_ROOT."login.php');"));
        	} else {
        	    redirect(ADMIN_ROOT.'login.php');
        	}
        }
        return $user;
	}
    /**
     * 用户登录
     *
     * @param string $username
     * @param string $password
     * @return array $user  用户信息
     *         int   null1   没有此用户
     *         int   0      用户密码不正确
     *         int   负数   用户的其它状态，可能是被锁定
     */
	function login($username,$password){
		if ($user = ModuleUser::get_user_by_name($username)) {
		    if ((int)$user['status']!==0) {
		    	return $user['status'];
		    }
		    $md5_pass = md5($password.$user['authcode']);
            if ($md5_pass == $user['pass']) {
                $authcode = authcode($user['userid']);
                if ($authcode != $user['authcode']) {
                    // 生成需要更新的数据
                    $userinfo = array(
                        'pass'     => md5($password.$authcode),
                        'authcode' => $authcode,
                    );
                    // 更新数据
                    ModuleUser::fill_user_info($user['userid'],$userinfo);
                    // 合并新密码和key
                    $user = array_merge($user,$userinfo);
                }
                return $user;
            } else {
                // 密码不正确
                return 0;
            }
		} else {
			// 没有此用户
			return null;
		}
	}
    /**
     * 通过用户ID查询用户信息
     *
     * @param int $userid
     * @return array|null
     */
    function get_user_by_id($userid) {
        return ModuleUser::get_user($userid,0);
    }
    /**
     * 通过用户名查询用户信息
     *
     * @param string $name
     * @return array|null
     */
    function get_user_by_name($name) {
        return ModuleUser::get_user($name,1);
    }
    /**
     * 通过authcode查询用户信息
     *
     * @param string $authcode
     * @return array|null
     */
    function get_user_by_auth($authcode) {
        return ModuleUser::get_user($authcode,2);
    }
    /**
     * 取得用户信息
     *
     * @param string $param
     * @param int $type
     * @return array|null
     */
	function get_user($param,$type=0){
	    $db = get_conn(); if ((int)$type>2) return null;
	    $prefixs = array('user.userid.','user.name.','user.authcode.');
        $prefix  = $prefixs[$type];
        $value   = DataCache::get($prefix.$param);
        if (!empty($value)) return $value;
        
        switch($type){
            case 0:
                $where = sprintf("`userid`=%s",$db->escape($param));
                break;
            case 1:
                $where = sprintf("`name`=%s",$db->escape($param));
                break;
            case 2:
                $where = sprintf("`authcode`=%s",$db->escape($param));
                break;
        }
	    $rs = $db->query("SELECT * FROM `#@_user` WHERE {$where} LIMIT 0,1;");
		// 判断用户是否存在
		if ($user = $db->fetch($rs)) {
		    if ($meta = ModuleUser::get_user_meta($user['userid'])) {
		    	$user = array_merge($user,$meta);
		    }
		    // 保存到缓存
            DataCache::set($prefix.$param,$user);
			return $user;
		}
		return null;
	}
    /**
	 * 获取用户的详细信息
	 *
	 * @param int $userid
	 * @return array
	 */
	function get_user_meta($userid) {
	    $db = get_conn(); $result = array();
	    $rs = $db->query("SELECT * FROM `#@_user_meta` WHERE `userid`=%s;",$userid);
	    while ($row = $db->fetch($rs)) {
	        if (is_need_unserialize($row['type'])) {
               $result[$row['key']] = unserialize($row['value']);
            } else {
    	       $result[$row['key']] = $row['value'];
            }
	    }
	    return $result;
	}
	/**
	 * 取得后台所有的管理员
	 *
	 * @return array
	 */
	function get_adminis() {
	    $db = get_conn(); $result = array();
	    $rs = $db->query("SELECT * FROM `#@_user_meta` WHERE `key`='Administrator' AND `VALUE`='Yes' ORDER BY `userid` ASC;");
	    while ($row = $db->fetch($rs)) {
	        $result[] = ModuleUser::get_user_by_id($row['userid']);
	    }
	    return $result;
	}
	/**
	 * 创建用户
	 *
	 * @param string $name
	 * @param string $pass
	 * @param string $email
	 * @param array $data
	 * @return array
	 */
	function create_user($name,$pass,$email,$data=null) {
	    $db = get_conn();
	    // 插入用户
	    $userid = $db->insert('#@_user',array(
	       'name' => $name,
	       'pass' => $pass,
	       'mail' => $email,
	       'status' => 0,
	       'registered' => date(DATE_FORMAT,time()),
	    ));
	    // 生成authcode
	    $authcode = authcode($userid);
	    $user_info = array(
	       'pass' => md5($pass.$authcode),
	       'authcode' => $authcode,
	    );
	    if ($data) {
	    	$user_info = array_merge($user_info,$data);
	    }
	    // 更新用户资料
	    return ModuleUser::fill_user_info($userid,$user_info);
	}
	/**
	 * 填写用户信息
	 *
	 * @param int $userid
	 * @param array $data
	 * @return array|null
	 */
    function fill_user_info($userid,$data) {
        $db = get_conn(); $user_rows = $meta_rows = array();
        if ($user = ModuleUser::get_user_by_id($userid)) {
            foreach ($data as $field=>$value) {
                if ($db->is_field('#@_user',$field)) {
                    $user_rows[$field] = $value;
                } else {
                    $meta_rows[$field] = $value;
                }
            }
            // 更新数据
            if ($user_rows) {
                $db->update('#@_user',$user_rows,"`userid`=".$db->escape($userid));
            }
            if ($meta_rows) {
                ModuleUser::fill_user_meta($userid,$meta_rows);
            }
            // 清理用户缓存
            ModuleUser::clear_user_cache($userid);
            return array_merge($user,$data);
        }
        return null;
    }
    /**
     * 填写用户扩展信息
     *
     * @param int $userid
     * @param array $data
     * @return bool
     */
    function fill_user_meta($userid,$data) {
        $db = get_conn();
        if (!is_array($data)) return false;
        foreach ($data as $key=>$value) {
            // 获取变量类型
            $var_type = gettype($value);
            // 判断是否需要序列化
            $value = is_need_serialize($value) ? serialize($value) : $value;
            // 查询数据库里是否已经存在
            $length = (int) $db->result(vsprintf("SELECT COUNT(*) FROM `#@_user_meta` WHERE `userid`=%s AND `key`=%s;",array($userid,$db->escape($key))));
            // update
            if ($length > 0) {
                $db->update('#@_user_meta',array(
                    'value' => $value,
                    'type'  => $var_type,
                ),vsprintf("`userid`=%s AND `key`=%s",array($db->escape($userid),$db->escape($key))));
            }
            // insert
            else {
                // 保存到数据库里
                $db->insert('#@_user_meta',array(
                    'userid' => $userid,
                    'key'    => $key,
                    'value'  => $value,
                    'type'   => $var_type,
                ));
            }
        }
        return true;
    }
    /**
     * 清理用户缓存
     *
     * @param int $userid
     * @return bool
     */
    function clear_user_cache($userid) {
        if ($user = ModuleUser::get_user_by_id($userid)) {
            $prefix = 'user.';
            foreach (array('userid','name','authcode') as $field) {
                DataCache::delete($prefix.$field.'.'.$user[$field]);
            }
        }
        return true;
    }
    /**
     * 删除用户
     *
     * @param int $userid
     * @return bool
     */
    function delete_user_by_id($userid) {
        $db = get_conn(); if (!$userid) return ;
        if (ModuleUser::get_user_by_id($userid)) {
            ModuleUser::clear_user_cache($userid);
            $db->delete('#@_user',vsprintf('`userid`=%s',array($db->escape($userid))));
            $db->delete('#@_user_meta',vsprintf('`userid`=%s',array($db->escape($userid))));
            return true;
        }
        return false;
    }
}