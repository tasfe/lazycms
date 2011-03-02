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
define('UPLOAD_ERR_MAX_SIZE',       5);
// 没有权限写入文件
defined('UPLOAD_ERR_CANT_WRITE') or define('UPLOAD_ERR_CANT_WRITE',    7);
// A PHP extension stopped the file upload
defined('UPLOAD_ERR_EXTENSION') or define('UPLOAD_ERR_EXTENSION',      8);
// 上传的文件类型不允许
define('UPLOAD_ERR_FORBID_EXT',     9);
// POST值超过了 post_max_size
define('UPLOAD_ERR_POST_MAXSIZE',   10);
// 未知异常
define('UPLOAD_ERR_UNKNOWN',        999);

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
        // 添加PHP4下的析构函数
        register_shutdown_function( array(&$this, '__destruct') );
	}

    function __construct(){
        // 判断总大小
        if (intval(get_cfg_var('post_max_size')) < ($_SERVER['CONTENT_LENGTH']/1024/1024)) {
            $this->_error = UPLOAD_ERR_POST_MAXSIZE;
        }
        // HTML5上传
        if (isset($_SERVER['HTTP_CONTENT_DISPOSITION'])) {
            if (preg_match('/attachment;\s+name="(.+?)";\s+filename="(.+?)"/i', $_SERVER['HTTP_CONTENT_DISPOSITION'], $info)) {
                $error_level = error_reporting(0);
                $temp_dir  = ini_get('upload_tmp_dir');
                $temp_name = rtrim(($temp_dir == '' ? CACHE_PATH : $temp_dir), '/') . '/' . micro_time(true) . mt_rand(1, 9999);
                file_put_contents($temp_name, file_get_contents('php://input'));
                $_FILES[$info[1]] = array(
                    'name'      => $info[2],
                    'tmp_name'  => $temp_name,
                    'size'      => filesize($temp_name),
                    'type'      => $_SERVER['CONTENT_TYPE'],
                    'error'     => 0,
                );
                error_reporting($error_level);
            }
        }
	}

    /**
     * 保存文件
     *
     * @param string $name
     * @param string $toname
     * @return bool
     */
    function save($name, $toname=null) {
        if (!isset($_FILES[$name])) return false;
        $info = $_FILES[$name];
        // 返回错误信息
        if ($info['error'] > 0) {
            $this->_error = $info['error'];
            return false;
        }

        // 处理save path
        $this->save_path = trim($this->save_path, '/') . '/';
        $info['ext']  = strtolower(pathinfo($info['name'], PATHINFO_EXTENSION));
        $basename     = basename(pathinfo((empty($toname) ? $info['name'] : $toname), PATHINFO_BASENAME), '.'.$info['ext']);
        $info['path'] = $this->save_path . $basename . '.' . $info['ext'];
        // 检查文件大小
        if (0 < $this->max_size && intval($info['size']) > intval($this->max_size)) {
            $this->_error = UPLOAD_ERR_MAX_SIZE;
            return false;
        }
        // 检查文件后缀
        if ($this->allow_exts && $this->allow_exts != '*' && !instr($info['ext'], strtolower($this->allow_exts))) {
            $this->_error = UPLOAD_ERR_FORBID_EXT;
            return false;
        }
        // 移动文件
        $error_level = error_reporting(0);
        $target = ABS_PATH . '/' . $info['path'];
        mkdirs(dirname($target));
        $move_file = rename($info['tmp_name'], $target); chmod($target, 0755);
        error_reporting($error_level);
        if ($move_file){
            $info['url']  = ROOT . $info['path'];
            $info['path'] = $target;
            unset($info['tmp_name'], $info['error']);
            return $info;
        } else {
            $this->_error = UPLOAD_ERR_CANT_WRITE;
            return false;
        }
    }
    /**
     * 返回错误信息
     *
     * @return string
     */
    function error() {
        $errors = array(
            1 => __('The uploaded file exceeds the upload_max_filesize directive in php.ini.'),
            2 => __('The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.'),
            3 => __('The uploaded file was only partially uploaded.'),
            4 => __('No file was uploaded.'),
            5 => sprintf(__('The uploaded file exceeds the size limit(%s).'), format_size($this->max_size)),
            6 => __('Missing a temporary folder.'),
            7 => __('Failed to write file to disk.'),
            8 => __('A PHP extension stopped the file upload.'),
            9 => __('The uploaded file type is not allowed.'),
            10 => sprintf(__('The uploaded file exceeds the total size limit(%s).'), get_cfg_var('post_max_size')),
        );
        if ($this->_error ==0 ) return '';
        return isset($errors[$this->_error]) ? $errors[$this->_error] : __('Unknown error');
    }
    /**
     * 错误代码
     *
     * @return int
     */
    function errno() {
        return $this->_error;
    }
}
