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
// 动作
$method = isset($_REQUEST['method'])?$_REQUEST['method']:null;

switch ($method) {
    // 进度
    case 'publish':
        $data = publish_exec();
        if ($data) {
            $data['total']      = number_format($data['total']);
            $data['complete']   = number_format($data['complete']);
            $data['begintime']  = ($data['begintime']>0 ? date('Y-m-d H:i:s',$data['begintime']) : '-------- --:--:--');
            $data['elapsetime'] = ($data['begintime']>0 || $data['elapsetime']>0 ? time_format('%H:%i:%s',$data['elapsetime']) : '--:--:--');
            $data['endtime']    = ($data['endtime']>0 ? date('Y-m-d H:i:s',$data['endtime']) : '-------- --:--:--');
            $data['state']      = '<img class="c'.($data['state']+1).' os" src="'.ADMIN_ROOT.'images/t.gif" />';
        }
        admin_return($data);
        break;
    // 取得关键词
    case 'terms':
        $content = isset($_REQUEST['content'])?$_REQUEST['content']:null;
        if ($content) {
            $terms = term_gets($content);
        } else {
            $terms = array();
        }
        admin_return(empty($terms) ? '' : $terms);
        break;
    // 默认页面
    default:
        // 设置标题
        admin_head('title',__('Control Panel'));
        // 加载头部
        include ADMIN_PATH.'/admin-header.php';
        
        // 加载尾部
        include ADMIN_PATH.'/admin-footer.php';
        break;
}

