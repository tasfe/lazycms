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
defined('ADMIN_PATH') or define('ADMIN_PATH', dirname(__FILE__));
// 加载公共文件
include ADMIN_PATH.'/../global.php';
// 后台的目录
define('ADMIN',ROOT.str_replace('\\','/',substr(ADMIN_PATH,strlen(ABS_PATH)+1)).'/');
// js css 加载类
include_file(COM_PATH.'/system/loader.php');
// 添加 CSS
func_add_callback('loader_add_css', language(), sprintf('/admin/css/%s.css', language()));
// 加载公共模块
include_modules();
// 检查是否已配置
defined('NO_REDIRECT') or define('NO_REDIRECT', false);
if (!NO_REDIRECT && (!is_file(ABS_PATH.'/config.php') || !installed())) {
    redirect(ADMIN.'install.php');
}
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
            if (instr($action, $user['roles'])) {
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
        	    ajax_alert($text);
            } else {
                echo $text; exit();
            }
    	} else {
            global $_USER;
    	    system_head('title',__('Restricted access'));
    	    include ADMIN_PATH.'/admin-header.php';
    	    echo error_page(__('Restricted access'),__('Restricted access, please contact the administrator.'));
    	    include ADMIN_PATH.'/admin-footer.php';
    		exit();
    	}
    }
    return $result;
}
