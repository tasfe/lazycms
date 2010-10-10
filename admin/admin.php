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
// 定义管理后台路径
defined('ADMIN_PATH') or define('ADMIN_PATH',dirname(__FILE__));
// 加载公共文件
require ADMIN_PATH.'/../global.php';
// 后台的目录
define('ADMIN_ROOT',WEB_ROOT.str_replace('\\','/',substr(ADMIN_PATH,strlen(ABS_PATH)+1)).'/');
// 加载模版处里类
require COM_PATH.'/system/template.php';
// 加载公共模块
require COM_PATH.'/module/system.php';
require COM_PATH.'/module/user.php';
require COM_PATH.'/module/model.php';
require COM_PATH.'/module/taxonomy.php';
require COM_PATH.'/module/post.php';

/**
 * 验证用户权限
 *
 * @param string $action
 * @param bool $is_redirect
 * @return bool
 */
function current_user_can($action,$is_redirect=true) {
    global $_ADMIN; $result = false;
    $user = user_current(false);
    if (isset($user['roles'])) {
    	if (instr($action,$user['roles'])) {
    		$result = true;
    	}
    }
    // 权限不足
    if (!$result && $is_redirect) {
    	if (is_ajax()) {
            $text = __('Restricted access, please contact the administrator.');
    		// 显示未登录的提示警告
            if (is_accept_json()) {
        	    admin_alert($text);
            } else {
                echo $text; exit();
            }
    	} else {
    	    admin_head('title',__('Restricted access'));
    	    include ADMIN_PATH.'/admin-header.php';
    	    echo error_page(__('Restricted access'),__('Restricted access, please contact the administrator.'));
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
    $language = language(); $files[] = $language; $loads = implode(',',$files);
    if (isset($CSS[$loads])) {
    	$loader = $CSS[$loads];
    } else {
        require_once COM_PATH.'/system/loader.php';
    	// 实例化loader类
        $loader = new StylesLoader($language);
        $CSS[$loads] = $loader;
    }
    // 加载样式表
    $version = $loader->get_version($loads);
    // 输出HTML
    printf('<link href="'.ADMIN_ROOT.'loader.php?%s" rel="stylesheet" type="text/css" />',str_replace('%2C',',',http_build_query(array(
        'type' => 'css',
        'load' => $loads,
        'lang' => $language,
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
    $language = language(); $files[] = $language; $loads = implode(',',$files);
    if (isset($JSS[$loads])) {
    	$loader = $JSS[$loads];
    } else {
        require_once COM_PATH.'/system/loader.php';
    	// 实例化loader类
        $loader = new ScriptsLoader($language);
        $JSS[$loads] = $loader;
    }
    // 加载样式表
    $version = $loader->get_version($loads);
    // 输出HTML
    printf('<script type="text/javascript" src="'.ADMIN_ROOT.'loader.php?%s"></script>',str_replace('%2C',',',http_build_query(array(
        'type' => 'js',
        'load' => $loads,
        'lang' => $language,
        'ver'  => $version,
    ))));
}

/**
 * 输出后台警告信息
 *
 * @param string $message
 * @param string $eval
 */
function admin_alert($message,$eval=null){
    ajax_echo('Alert',$message,$eval);
}
function admin_success($message,$eval=null){
    ajax_echo('Success',$message,$eval);
}
function admin_error($message,$eval=null){
    ajax_echo('Error',$message,$eval);
}
function admin_return($data) {
    return ajax_echo('Return', $data);
}
/**
 * 权限列表
 *
 * @return array
 */
function admin_purview($data=null) {
    if (!is_array($data)) $data = array();
    $purview = array(
        'cpanel' => array(
            '_LABEL_'   => __('Control Panel'),
            'upgrade'   => __('Upgrade'),
        ),
        'posts' => array(
            '_LABEL_'     => __('Posts'),
            'categories'  => __('Categories'),
            'post-new'    => _x('Add New','post'),
            'post-list'   => _x('List','post'),
            'post-edit'   => _x('Edit','post'),
            'post-delete' => _x('Delete','post'),
        ),
        'pages' => array(
            '_LABEL_'     => __('Pages'),
            'page-list'   => _x('List','page'),
            'page-new'    => _x('Add New','page'),
            'page-edit'   => _x('Edit','page'),
            'page-delete' => _x('Delete','page'),
        ),
        /*'topic' => array(
            '_LABEL_'     => __('Topics'),
            'topic-list'   => _x('List','topic'),
            'topic-new'    => _x('Add New','topic'),
            'topic-edit'   => _x('Edit','topic'),
            'topic-delete' => _x('Delete','topic'),
        ),*/
        'models' => array(
            '_LABEL_'      => __('Models'),
            'model-list'   => _x('List','model'),
            'model-new'    => _x('Add New','model'),
            'model-edit'   => _x('Edit','model'),
            'model-delete' => _x('Delete','model'),
            'model-import' => _x('Import','model'),
            'model-export' => _x('Export','model'),
            'model-fields' => _x('Fields','model'),
        ),
        'users' => array(
            '_LABEL_'     => __('Users'),
            'user-list'   => _x('List','user'),
            'user-new'    => _x('Add New','user'),
            'user-edit'   => _x('Edit','user'),
            'user-delete' => _x('Delete','user'),
        ),
        'plugins' => array(
            '_LABEL_'       => __('Plugins'),
            'plugin-list'   => _x('List','plugin'),
            'plugin-new'    => _x('Add New','plugin'),
            'plugin-delete' => _x('Delete','plugin'),
        ),
        'tools' => array(
            '_LABEL_'       => __('Tools'),
            'clean-cache'   => __('Clean cache'),
        ),
        'settings' => array(
            '_LABEL_'        => __('Settings'),
            'option-general' => _x('General','setting'),
        )
    );
    $hl = '<div class="role-list">';
    foreach ($purview as $k=>$pv) {
        $title = $pv['_LABEL_']; unset($pv['_LABEL_']);
        $roles = null; $parent_checked = ' checked="checked"';
        foreach ($pv as $sk=>$spv) {
            $checked = instr($sk,$data)?' checked="checked"':null;
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
 * @param  $menus
 * @return bool
 */
function admin_menu($menus) {
    global $parent_file,$_ADMIN;
    // 获取管理员信息
    if (!isset($_ADMIN)) $_ADMIN = user_current(false);
    // 自动植入配置
    $is_first = true; $is_last = false;
    // 设置默认参数
    if (!empty($_SERVER['QUERY_STRING'])) {
        parse_str($_SERVER['QUERY_STRING'],$query);
        if (!isset($query['method'])) {
            $query = array_merge(array('method' => 'default'),$query);
        }
        $query = '?'.http_build_query($query);
    } else {
        $query = '?method=default';
    }
    if (!isset($parent_file)) {
    	$parent_file = PHP_FILE.$query;
    } else {
        $parent_file = ADMIN_ROOT.(strpos($parent_file,'?')!==false?$parent_file:$parent_file.'?method=default');
    }

    $menus_tree = array();
    // 预处理菜单
    while (list($k,$menu) = each($menus)) {
        if (is_array($menu)) {
            $submenus = array(); $is_expand = false; $has_submenu = false;
            if (!empty($menu[3]) && is_array($menu[3])) {
                $has_submenu = true;
                foreach ($menu[3] as $href) {
                    $href[1]   = ADMIN_ROOT.$href[1];
                    $url_query = strpos($href[1],'?')!==false?$href[1]:$href[1].'?method=default';
                    $href[3]   = !strncasecmp($parent_file,$url_query,strlen($url_query))?true:false;
                    $is_expand = !strncasecmp($parent_file,$url_query,strlen($url_query))?true:$is_expand;
                    // 子菜单需要权限才能访问，且用户要有权限
                    if (isset($href[2]) && instr($href[2],$_ADMIN['roles'])) {
                        $submenus[] = $href;
                    }
                    // 子菜单存在，不需要权限
                    elseif (empty($href[2])) {
                        $submenus[] = $href;
                    }
                }
            }
            
            // 存在子菜单，并且子菜单不为空，或者没有子菜单
            if ($has_submenu===true && !empty($submenus) || $has_submenu===false) {
                $menu[1] = ADMIN_ROOT.$menu[1];
                $current = !strncasecmp($parent_file,$menu[1],strlen($menu[1])) || $is_expand ? ' current' : '';
                $expand  = empty($submenus) || empty($current) ? '' : ' expand';
                $menu = array(
                    'text' => $menu[0],
                    'link' => $menu[1],
                    'icon' => $menu[2],
                    'current'  => $current,
                    'expand'   => $expand,
                    'submenus' => $submenus,
                );
                $menus_tree[$k] = $menu;
            }
        } else {
            $menus_tree[] = $menu;
        }
    }

    // 循环所有的菜单
    while (list($k,$menu) = each($menus_tree)) {
        // 数组是菜单
        if (is_array($menu)) {
            $is_last = is_array(current($menus_tree)) ? $is_last : true; $class = '';
            if ($is_first) $class.= ' first';
            if ($is_last)  $class.= ' last';
            echo '<li id="menu-'.$k.'" class="head'.$class.$menu['current'].$menu['expand'].'">';
            echo '<a href="'.$menu['link'].'" class="image"><img src="'.ADMIN_ROOT.'images/white.gif" class="'.$menu['icon'].' os" /></a>';
            echo '<a href="'.$menu['link'].'" class="text'.$class.'">'.$menu['text'].'</a>';
            // 展示子菜单
            if (!empty($menu['submenus'])) {
                echo '<a href="javascript:;" class="toggle"><br/></a>';
                echo '<dl class="submenu">';
                echo '<dt>'.$menu['text'].'</dt>';
                foreach ($menu['submenus'] as $submenu) {
                    $current = $submenu[3]?' class="current"':null;
                    echo '<dd'.$current.'><a href="'.$submenu[1].'">'.$submenu[0].'</a></dd>';
                }
                echo '</dl>';
            }
            echo '</li>';
            $is_first = false;
        }
        // 否则是分隔符
        else {
            echo '<li class="separator"><a href="javascript:;"><br/></a></li>';
            $is_first = true; $is_last = false;
        }
    }
    
    return true;
}