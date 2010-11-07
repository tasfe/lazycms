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
defined('ADMIN_PATH') or define('ADMIN_PATH', dirname(__FILE__));
// 加载公共文件
require ADMIN_PATH.'/../global.php';
// 后台的目录
define('ADMIN_ROOT',WEB_ROOT.str_replace('\\','/',substr(ADMIN_PATH,strlen(ABS_PATH)+1)).'/');

// 检查是否已配置
defined('NO_REDIRECT') or define('NO_REDIRECT', false);
if (!NO_REDIRECT && (!is_file(COM_PATH.'/config.php') || !installed())) {
    redirect(ADMIN_ROOT.'install.php'); exit();
}

// 加载模版处里类
require COM_PATH.'/system/template.php';
// 加载公共模块
require COM_PATH.'/module/system.php';
require COM_PATH.'/module/user.php';
require COM_PATH.'/module/model.php';
require COM_PATH.'/module/taxonomy.php';
require COM_PATH.'/module/post.php';
require COM_PATH.'/module/publish.php';

/**
 * 验证用户权限
 *
 * @param string $action
 * @param bool $is_redirect
 * @return bool
 */
function current_user_can($action,$is_redirect=true) {
    $result = false;
    $user = user_current(false);
    if (isset($user['Administrator']) && isset($user['roles'])) {
        // 超级管理员
        if($user['Administrator']=='Yes' && $user['roles']=='ALL') {
            $result = true;
        }
        // 普通管理员
        elseif ($user['Administrator']=='Yes') {
            if (instr($action,(array)$user['roles'])) {
                $result = true;
            }
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
    return isset($head[$key])?$head[$key]:array();
}
/**
 * 输出后台加载css连接
 *
 * @return string
 */
function admin_css(){
    static $CSS = array(); $files = array();
    $args = func_get_args();
    if (isset($args[0]) && is_array($args[0]))
        $args = $args[0];
    if (empty($args)) return ;

    foreach ($args as $file) {
        $files[] = !strncasecmp($file,'css/',4) ? substr( $file, 4 ) : $file;
    }     
    $loads = implode(',',$files);
    if (isset($CSS[$loads])) {
    	$loader = $CSS[$loads];
    } else {
        require_file(COM_PATH.'/system/loader.php');
    	// 实例化loader类
        $loader = new StylesLoader(language());
        $CSS[$loads] = $loader;
    }
    // 加载样式表
    $version = $loader->get_version($loads);
    // 输出HTML
    printf('<link href="'.ADMIN_ROOT.'loader.php?%s" rel="stylesheet" type="text/css" />',str_replace('%2C',',',http_build_query(array(
        'type' => 'css',
        'load' => $loads,
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
    $args = func_get_args();
    if (isset($args[0]) && is_array($args[0]))
        $args = $args[0];
    if (empty($args)) return ;
    
    foreach ($args as $file) {
        $files[] = !strncasecmp($file,'js/',3) ? substr($file, 3) : $file;
    }
    $loads = implode(',',$files);
    if (isset($JSS[$loads])) {
    	$loader = $JSS[$loads];
    } else {
        require_file(COM_PATH.'/system/loader.php');
    	// 实例化loader类
        $loader = new ScriptsLoader(language());
        $JSS[$loads] = $loader;
    }
    // 加载样式表
    $version = $loader->get_version($loads);
    // 输出HTML
    printf('<script type="text/javascript" src="'.ADMIN_ROOT.'loader.php?%s"></script>',str_replace('%2C',',',http_build_query(array(
        'type' => 'js',
        'load' => $loads,
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
    $purview = array(
        'cpanel' => array(
            '_LABEL_'   => __('Control Panel'),
            'publish'   => __('Publish Posts'),
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
        'models' => array(
            '_LABEL_'      => __('Models'),
            'model-list'   => _x('List','model'),
            'model-new'    => _x('Add New','model'),
            'model-edit'   => _x('Edit','model'),
            'model-delete' => _x('Delete','model'),
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
            if ($data=='ALL') {
                $checked = ' checked="checked"';
            } else {
                $checked = instr($sk,$data)?' checked="checked"':null;
            }
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
    global $parent_file,$_USER;
    // 获取管理员信息
    if (!isset($_USER)) $_USER = user_current(false);
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
        $parent_file = ADMIN_ROOT.(strpos($parent_file,'?')!==false ? $parent_file : $parent_file.'?method=default');
    }

    $menus_tree = array();
    // 预处理菜单
    while (list($k,$menu) = each($menus)) {
        if (is_array($menu)) {
            $submenus = array(); $is_expand = false; $has_submenu = false;
            if (!empty($menu[3]) && is_array($menu[3])) {
                $has_submenu = true;
                foreach ($menu[3] as $href) {
                    // 文件不存在，菜单也不能出现
                    if (!is_file(ADMIN_PATH.'/'.parse_url($href[1],PHP_URL_PATH))) continue;
                    $href[1]   = ADMIN_ROOT.$href[1];
                    $url_query = strpos($href[1],'?')!==false?$href[1]:$href[1].'?method=default';
                    $href[3]   = !strncasecmp($parent_file,$url_query,strlen($url_query))?true:false;
                    $is_expand = !strncasecmp($parent_file,$url_query,strlen($url_query))?true:$is_expand;
                    
                    // 子菜单需要权限才能访问，且用户要有权限
                    if (isset($href[2]) && (instr($href[2],$_USER['roles']) || $_USER['roles']=='ALL')) {
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
            echo '<a href="'.$menu['link'].'" class="image"><img src="'.ADMIN_ROOT.'images/t.gif" class="'.$menu['icon'].' os" /></a>';
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
/**
 * js公共语言包
 *
 * @return string
 */
function admin_jslang() {
    // js语言包
    $js_lang = array_merge(array(
        'System Error' => __('System Error'),
        'Alert'     => __('Alert'),
        'Submit'    => __('Submit'),
        'Confirm'   => __('Confirm'),
        'Cancel'    => __('Cancel'),
        'Save'      => __('Save'),
        'Close'     => __('Close'),
        'Edit'      => __('Edit'),
        'Delete'    => __('Delete'),
        'Search'    => __('Search'),
        'Address:'    => __('Address:'),
        'Insert Map'    => __('Insert Map'),
        'No record!'        => __('No record!'),
        'Confirm Logout?'   => __('Confirm Logout?'),
        'Confirm Delete?'   => __('Confirm Delete?'),
        'Use the model set' => __('Use the model set'),
        'Use the category set' => __('Use the category set'),
        'Did not select any action!' => __('Did not select any action!'),

    ),admin_head('jslang'));
    return sprintf('$.extend(LazyCMS.L10n,%s);',json_encode($js_lang));
}
/**
 * 编辑器语言
 *
 * @return array
 */
function admin_editor_lang() {
    return array(
        'Cancel'    => __('Cancel'),
        'Paragraph' => __('Paragraph'),
        'Heading 1' => __('Heading 1'),
        'Heading 2' => __('Heading 2'),
        'Heading 3' => __('Heading 3'),
        'Heading 4' => __('Heading 4'),
        'Heading 5' => __('Heading 5'),
        'Heading 6' => __('Heading 6'),
        'Preformatted' => __('Preformatted'),
        'Address' => __('Address'),
        'xx-small' => __('xx-small'),
        'x-small' => __('x-small'),
        'small' => __('small'),
        'medium' => __('medium'),
        'large' => __('large'),
        'x-large' => __('x-large'),
        'xx-large' => __('xx-large'),
        'Align left' => __('Align left'),
        'Align center' => __('Align center'),
        'Align right' => __('Align right'),
        'Align full' => __('Align full'),
        'Ordered list' => __('Ordered list'),
        'Unordered list' => __('Unordered list'),
        'Use Ctrl+V on your keyboard to paste the text.' => __('Use Ctrl+V on your keyboard to paste the text.'),
        'Ok' => __('Ok'),
        'Flv URL:' => __('Flv URL:'),
        'Link URL:' => __('Link URL:'),
        'Target:&nbsp;&nbsp;' => __('Target:&nbsp;&nbsp;'),
        'Link Text:' => __('Link Text:'),
        'Img URL:&nbsp;' => __('Img URL:&nbsp;'),
        'Alt text:' => __('Alt text:'),
        'Alignment:' => __('Alignment:'),
        'Dimension:' => __('Dimension:'),
        'Border:&nbsp;&nbsp;&nbsp;' => __('Border:&nbsp;&nbsp;&nbsp;'),
        'Hspace:&nbsp;&nbsp;&nbsp;' => __('Hspace:&nbsp;&nbsp;&nbsp;'),
        'Vspace:' => __('Vspace:'),
        'Flash URL:' => __('Flash URL:'),
        'Media URL:' => __('Media URL:'),
        'Rows&Cols:&nbsp;&nbsp;' => __('Rows&Cols:&nbsp;&nbsp;'),
        'Headers:&nbsp;&nbsp;&nbsp;&nbsp;' => __('Headers:&nbsp;&nbsp;&nbsp;&nbsp;'),
        'CellSpacing:' => __('CellSpacing:'),
        'CellPadding:' => __('CellPadding:'),
        'Caption:&nbsp;&nbsp;&nbsp;&nbsp;' => __('Caption:&nbsp;&nbsp;&nbsp;&nbsp;'),
        'Border:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' => __('Border:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'),
        'Default' => __('Default'),
        'New window' => __('New window'),
        'Same window' => __('Same window'),
        'Parent window' => __('Parent window'),
        'Left' => _x('Left','xhe'),
        'Right' => _x('Right','xhe'),
        'Top' => _x('Top','xhe'),
        'Middle' => _x('Middle','xhe'),
        'Center' => _x('Center','xhe'),
        'Baseline' => _x('Baseline','xhe'),
        'Bottom' => _x('Bottom','xhe'),
        'None' => __('None'),
        'First row' => __('First row'),
        'First column' => __('First column'),
        'Both' => __('Both'),
        'xhEditor is a platform independent WYSWYG XHTML editor based by jQuery,released as Open Source under <a href="http://www.gnu.org/licenses/lgpl.html" target="_blank">LGPL</a>.' => __('xhEditor is a platform independent WYSWYG XHTML editor based by jQuery,released as Open Source under <a href="http://www.gnu.org/licenses/lgpl.html" target="_blank">LGPL</a>.'),
        'Smile' => __('Smile'),
        'Tongue' => __('Tongue'),
        'Titter' => __('Titter'),
        'Laugh' => __('Laugh'),
        'Sad' => __('Sad'),
        'Wronged' => __('Wronged'),
        'Fast cry' => __('Fast cry'),
        'Cry' => __('Cry'),
        'Wail' => __('Wail'),
        'Mad' => __('Mad'),
        'Knock' => __('Knock'),
        'Curse' => __('Curse'),
        'Crazy' => __('Crazy'),
        'Angry' => __('Angry'),
        'Oh my' => __('Oh my'),
        'Awkward' => __('Awkward'),
        'Panic' => __('Panic'),
        'Shy' => __('Shy'),
        'Cute' => __('Cute'),
        'Envy' => __('Envy'),
        'Proud' => __('Proud'),
        'Struggle' => __('Struggle'),
        'Quiet' => __('Quiet'),
        'Shut up' => __('Shut up'),
        'Doubt' => __('Doubt'),
        'Despise' => __('Despise'),
        'Sleep' => __('Sleep'),
        'Bye' => __('Bye'),
        'Cut (Ctrl+X)' => __('Cut (Ctrl+X)'),
        'Copy (Ctrl+C)' => __('Copy (Ctrl+C)'),
        'Paste (Ctrl+V)' => __('Paste (Ctrl+V)'),
        'Paste as plain text' => __('Paste as plain text'),
        'Block tag' => __('Block tag'),
        'Font family' => __('Font family'),
        'Font size' => __('Font size'),
        'Bold (Ctrl+B)' => __('Bold (Ctrl+B)'),
        'Italic (Ctrl+I)' => __('Italic (Ctrl+I)'),
        'Underline (Ctrl+U)' => __('Underline (Ctrl+U)'),
        'Strikethrough (Ctrl+S)' => __('Strikethrough (Ctrl+S)'),
        'Select text color' => __('Select text color'),
        'Select background color' => __('Select background color'),
        'SelectAll (Ctrl+A)' => __('SelectAll (Ctrl+A)'),
        'Remove formatting' => __('Remove formatting'),
        'Align' => __('Align'),
        'List' => __('List'),
        'Outdent (Shift+Tab)' => __('Outdent (Shift+Tab)'),
        'Indent (Tab)' => __('Indent (Tab)'),
        'Insert/edit link (Ctrl+K)' => __('Insert/edit link (Ctrl+K)'),
        'Unlink' => __('Unlink'),
        'Insert/edit image' => __('Insert/edit image'),
        'Insert/edit flash' => __('Insert/edit flash'),
        'Insert/edit media' => __('Insert/edit media'),
        'Insert Flv Video' => __('Insert Flv Video'),
        'Insert Pagebreak' => __('Insert Pagebreak'),
        'Insert Google map' => __('Insert Google map'),
        'Google Maps' => __('Google Maps'),
        'Remove external links' => __('Remove external links'),
        'Emotions' => __('Emotions'),
        'Insert a new table' => __('Insert a new table'),
        'Edit source code' => __('Edit source code'),
        'Preview' => __('Preview'),
        'Print (Ctrl+P)' => __('Print (Ctrl+P)'),
        'Toggle fullscreen (Esc)' => __('Toggle fullscreen (Esc)'),
        'About xhEditor' => __('About xhEditor'),
        'Click to open link' => __('Click to open link'),
        'Current textarea is hidden, please make it show before initialization xhEditor, or directly initialize the height.' => __('Current textarea is hidden, please make it show before initialization xhEditor, or directly initialize the height.'),
        'Upload file extension required for this: ' => __('Upload file extension required for this: '),
        'You can only drag and drop the same type of file.' => __('You can only drag and drop the same type of file.'),
        'File uploading,please wait...' => __('File uploading,please wait...'),
        'Please do not upload more then {$upMultiple} files.' => __('Please do not upload more then {$upMultiple} files.'),
        'File uploading(Esc cancel)' => __('File uploading(Esc cancel)'),
        ' upload interface error!' => __(' upload interface error!'),
        'return error:' => __('return error:'),
        'Close (Esc)' => __('Close (Esc)'),
        'Currently not supported by your browser, use keyboard shortcuts(Ctrl+X) instead.' => __('Currently not supported by your browser, use keyboard shortcuts(Ctrl+X) instead.'),
        'Currently not supported by your browser, use keyboard shortcuts(Ctrl+C) instead.' => __('Currently not supported by your browser, use keyboard shortcuts(Ctrl+C) instead.'),
        'Currently not supported by your browser, use keyboard shortcuts(Ctrl+V) instead.' => __('Currently not supported by your browser, use keyboard shortcuts(Ctrl+V) instead.'),
        'Upload file extension required for this: ' => __('Upload file extension required for this: '),
    );
}