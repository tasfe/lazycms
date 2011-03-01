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
defined('COM_PATH') or die('Restricted access!');

// 上传的文件太大
define('UPLOAD_ERR_MAX_SIZE',   5);
// 上传的文件类型不允许
define('UPLOAD_ERR_FORBID_EXT', 8);
// 非法提交
define('UPLOAD_ERR_NO_SUBMIT',  9);
// 没有权限写入文件
define('UPLOAD_ERR_NO_PURVIEW', 10);

/**
 * 文件上传类
 *
 * @author  Lukin <my@lukin.cn>
 * @version $Id$
 */
class UpLoadFile{
    // 单个文件大小，0:为不限制
    var $max_size = 0;
    // 保存路径
    var $save_path;
    // 允许的文件后缀
    var $allow_exts;
    // 错误信息
    var $_error = 0;

    function UpLoadFile() {
        $args = func_get_args();
		call_user_func_array( array(&$this, '__construct'), $args );
	}

    function __construct(){
        
	}
}
