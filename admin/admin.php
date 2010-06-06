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
// 定义管理后台路径
defined('ADMIN_PATH') or define('ADMIN_PATH',dirname(__FILE__));
// 加载公共文件
require ADMIN_PATH.'/../global.php';
// 后台的目录
define('ADMIN_ROOT',WEB_ROOT.str_replace('\\','/',substr(ADMIN_PATH,strlen(ABS_PATH)+1)).'/');
// 加载公共模块
require_file(COM_PATH.'/module/system.php');
require_file(COM_PATH.'/module/user.php');
require_file(COM_PATH.'/module/model.php');
require_file(COM_PATH.'/module/sort.php');

/**
 * 验证用户权限
 *
 * @param string $action
 * @param bool $is_redirect
 * @return bool
 */
function current_user_can($action,$is_redirect=true) {
    global $_ADMIN; $result = false;
    $user = ModuleUser::current(false);
    if (isset($user['roles'])) {
    	if (in_array($action,$user['roles'])) {
    		$result = true;
    	}
    }
    // 权限不足
    if (!$result && $is_redirect) {
    	if (is_ajax()) {
    		// 显示未登录的提示警告
            if (is_accept_json()) {
        	    echo_json('Alert',_('Restricted access, please contact the administrator.'));
            } else {
                exit(_('Restricted access, please contact the administrator.'));
            }
    	} else {
    	    admin_head('title',_('Restricted access'));
    	    include ADMIN_PATH.'/admin-header.php';
    	    echo error_page(_('Restricted access'),_('Restricted access, please contact the administrator.'));
    	    include ADMIN_PATH.'/admin-footer.php';
    		exit();
    	}
    }
    return $result;
}

/**
 * 设置head变量
 *
 * @param string $key
 * @param mixed $value
 * @return mixed
 */
function admin_head($key,$value=null) {
    static $head = array();
    // 赋值
    if (!is_null($value)) {
    	$head[$key] = $value;
    }
    return isset($head[$key])?$head[$key]:null;
}
/**
 * 输出后台加载css连接
 *
 * @return string
 */
function admin_css(){
    static $CSS = array(); $files = array();
    $args = func_get_args(); if (empty($args)) return ;
    foreach ($args as $file) $files[] = !strncasecmp($file,'css/',4) ? substr( $file, 4 ) : $file;
    $loads = implode(',',$files);
    if (isset($CSS[$loads])) {
    	$loader = $CSS[$loads];
    } else {
        require_file(COM_PATH.'/system/loader.php');
    	// 实例化loader类
        $loader = new StylesLoader();
        $CSS[$loads] = $loader;
    }
    // 加载样式表
    $version = $loader->get_version($loads);
    // 输出HTML
    printf('<link href="'.ADMIN_ROOT.'loader.php?%s" rel="stylesheet" type="text/css" />',str_replace('%2C',',',http_build_query(array(
        'type' => 'css',
        'load' => $loads,
        'lang' => language(),
        'ver'  => $version,
    ))));
}
/**
 * 输出后台加载js连接
 *
 * @return string
 */
function admin_script(){
    static $JSS = array(); $files = array();
    $args = func_get_args(); if (empty($args)) return ;
    foreach ($args as $file) $files[] = !strncasecmp($file,'js/',3) ? substr($file, 3) : $file;
    $loads = implode(',',$files);
    if (isset($JSS[$loads])) {
    	$loader = $JSS[$loads];
    } else {
        require_file(COM_PATH.'/system/loader.php');
    	// 实例化loader类
        $loader = new ScriptsLoader();
        $JSS[$loads] = $loader;
    }
    // 加载样式表
    $version = $loader->get_version($loads);
    // 输出HTML
    printf('<script type="text/javascript" src="'.ADMIN_ROOT.'loader.php?%s"></script>',str_replace('%2C',',',http_build_query(array(
        'type' => 'js',
        'load' => $loads,
        'lang' => language(),
        'ver'  => $version,
    ))));
}

/**
 * 输出后台警告信息
 *
 * @param string $message
 * @param string $url
 */
function admin_alert($message,$call=null){
    $call = $call ? array('CALL' => $call) : null;
    echo_json('Alert',$message,$call);
}
function admin_success($message,$call=null){
    $call = $call ? array('CALL' => $call) : null;
    echo_json('Success',$message,$call);
}
function admin_error($message,$call=null){
    $call = $call ? array('CALL' => $call) : null;
    echo_json('Error',$message,$call);
}
/**
 * 权限列表
 *
 * @return array
 */
function admin_purview($data=null) {
    if (!is_array($data)) $data = array();
    $purview = array(
        'post' => array(
            '_LABEL_'    => _('Posts'),
            'categories' => _('Categories'),
            'post-new'   => __('Add New','post'),
            'post-list'  => __('List','post'),
            'post-edit'  => __('Edit','post'),
            'post-del'   => __('Delete','post'),
        ),
        'model' => array(
            '_LABEL_'      => _('Models'),
            'model-list'   => __('List','model'),
            'model-new'    => __('Add New','model'),
            'model-edit'   => __('Edit','model'),
            'model-delete' => __('Delete','model'),
            'model-import' => __('Import','model'),
            'model-export' => __('Export','model'),
            'model-fields' => __('Fields','model'),
        ),
        'user' => array(
            '_LABEL_'     => _('Users'),
            'user-list'   => __('List','user'),
            'user-new'    => __('Add New','user'),
            'user-edit'   => __('Edit','user'),
            'user-delete' => __('Delete','user'),
        ),
    );
    $hl = '<div class="role-list">';
    foreach ($purview as $k=>$pv) {
        $title = $pv['_LABEL_']; unset($pv['_LABEL_']);
        $roles = null; $parent_checked = ' checked="checked"';
        foreach ($pv as $sk=>$spv) {
            $checked = in_array($sk,$data)?' checked="checked"':null;
            $parent_checked = empty($checked)?'':$parent_checked;
        	$roles.= '<label><input type="checkbox" name="roles[]" rel="'.$k.'" value="'.$sk.'"'.$checked.' /> '.$spv.'</label>';
        }
        $hl.= '<p><label><input type="checkbox" name="parent[]" class="parent-'.$k.'" value="'.$k.'"'.$parent_checked.' /> <strong>'.$title.'</strong></label><br/>'.$roles.'</p>';
    }
    $hl.= '</div>';
    return $hl;
}
/**
 * 输出后台菜单
 *
 * @param array $menus
 * @author  Lukin <my@lukin.cn>
 */
function admin_menu($menus){
    global $parent_file;
    // 自动植入配置
    $is_first = true; $is_last = false;
    // 设置默认参数
    if (!empty($_SERVER['QUERY_STRING'])) {
        parse_str($_SERVER['QUERY_STRING'],$query);
        if (!isset($query['action'])) {
            $query = array_merge(array('action' => 'default'),$query);
        }
        $query = '?'.http_build_query($query);
    } else {
        $query = '?action=default';
    }
    if (!isset($parent_file)) {
    	$parent_file = PHP_FILE.$query;
    } else {
        $parent_file = ADMIN_ROOT.(strpos($parent_file,'?')!==false?$parent_file:$parent_file.'?action=default');
    }
    // 循环所有的菜单
    while (list($k,$menu) = each($menus)) {
        // 数组是菜单
        if (is_array($menu)) {
            // 检查是否需要展开菜单
            $is_expand = false; $submenus = array();
            if (isset($menu[3]) && is_array($menu[3])) {
                foreach ($menu[3] as $href) {
                    $href[1]    = ADMIN_ROOT.$href[1];
                    $url_query  = strpos($href[1],'?')!==false?$href[1]:$href[1].'?action=default';
                    $href[2]    = !strncasecmp($parent_file,$url_query,strlen($url_query))?true:false;
                    $is_expand  = !strncasecmp($parent_file,$url_query,strlen($url_query))?true:$is_expand;
                    $submenus[] = $href;
                }
            }
            $menu[1] = ADMIN_ROOT.$menu[1];
            $current = !strncasecmp($parent_file,$menu[1],strlen($menu[1])) || $is_expand ? ' current expand' : null;
            $is_last = is_array(current($menus)) ? $is_last : true; $class = '';
            if ($is_first) {
                $class.= ' first';
            }
            if ($is_last) {
                $class.= ' last';
            }
        	
            echo '<li id="menu-'.$k.'" class="head'.$class.$current.'">';
        	echo '<a href="'.$menu[1].'" class="image"><img src="'.ADMIN_ROOT.'images/white.gif" class="'.$menu[2].' os" /></a>';
        	echo '<a href="'.$menu[1].'" class="text'.$class.'">'.$menu[0].'</a>';
        	// 展示子菜单
        	if (!empty($submenus)) {
        	    echo '<a href="javascript:;" class="toggle"><br/></a>';
        	    echo '<dl class="submenu">';
        	    echo '<dt>'.$menu[0].'</dt>';
        		foreach ($submenus as $submenu) {
        		    $current = $submenu[2]?' class="current"':null;
        			echo '<dd'.$current.'><a href="'.$submenu[1].'">'.$submenu[0].'</a></dd>';
        		}
        		echo '</dl>';
        	}
        	echo '</li>'; $is_first = false;
    	}
    	// 否则是分隔符
    	else {
    		echo '<li class="separator"><a href="javascript:;"><br/></a></li>';
            $is_first = true; $is_last = false;
    	}
    }
    return true;
}
