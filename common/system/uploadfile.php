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
/**
 * 文件上传类
 */
class UpLoadFile{
    // 上传的文件太大
    var $_UPLOAD_ERR_MAX_SIZE   = 5;
    // 上传的文件类型不允许
    var $_UPLOAD_ERR_FORBID_EXT = 8;
    // 非法提交
    var $_UPLOAD_ERR_NO_SUBMIT  = 9;

    // 单个文件大小，0:为不限制
    var $maxSize = 0;
    // 保存路径
    var $savePath;
    // 允许的文件后缀
    var $allowExts;
    // 错误信息
    var $_error = 0;
    /**
     * 兼容PHP5模式
     */
    function __construct(){
        if ((int)get_cfg_var('post_max_size') < (int)$_SERVER["CONTENT_LENGTH"]/1024/1024) {
            $this->_error = 10;
        }
    }
    /**
     * 初始化
     */
    function UpLoadFile(){
        $this->__construct();
    }
    /**
     * 保存上传的文件
     *
     * @param string $p1    field
     * @param string $p2    savePath
     * @return bool
     */
    function save($p1,$p2=null){
        if (!isset($_FILES[$p1])) { return false; }
        $Info = $_FILES[$p1];
        $Info['ext'] = $this->_getExt($Info['name']);
        // 返回错误信息
        if ((int)$Info['error'] > 0){
            $this->_error = $Info['error'];
            return false;
        }
        if (empty($p2)){
            $p3 = $this->savePath.microtime(true).salt().'.'.$Info['ext'];
            $p2 = LAZY_PATH.$p3;
            $Info['path'] = SITE_BASE.$p3;
        } else {
            if (file_exists($p2)) {
                $p2 = substr($p2,0,strrpos($p2,'/')).'/'.basename($p2,'.'.$Info['ext']).salt().'.'.$Info['ext'];
            }
            $p2 = substr($p2,0,strrpos($p2,'/')).'/'.pinyin(basename($p2,'.'.$Info['ext'])).'.'.$Info['ext'];
            $Info['path'] = $p2;
        }
        if (is_uploaded_file($Info['tmp_name'])){
            // 检查文件大小
            if ($this->_checkSize($Info['size'])){
                return false;
            }
            // 检查文件后缀
            if (!$this->_checkExt($this->_getExt($Info['name']))){
                $this->_error = $this->_UPLOAD_ERR_FORBID_EXT;
                return false;
            }
            
            if (move_uploaded_file($Info['tmp_name'],$p2)){
                unset($Info['tmp_name'],$Info['error']);
                return $Info;
            } else {
                return false;
            }
        } else {
            $this->_error = $this->_UPLOAD_ERR_FORBID_SUBMIT;
        }
        return false;
    }
    /**
     * 保存多个文件
     *
     * @param array $p1
     * @return array
     */
    function saves($p1=array()){
        if (empty($p1)) {
            $R1 = $_FILES;
        } else {
            $R1 = $p1;
        }
        $R = array();
        foreach ($R1 as $name=>$path) {
            if (empty($p1)) {
                $file = $this->save($name);
            } else {
                $file = $this->save($name,$path);
            }
            if ($file) {
                $R[$name] = $file;
            } else {
                if ($this->_error!=4) {
                    $R[$name] = $this->getError();
                }
            }
        }
        return $R;
    }
    /**
     * 取得错误信息
     *
     * @return string
     */
    function getError(){
        if ($this->_error==0) { return ; }
        if ($this->_error==10) {
            return l('Upload error '.$this->_error,array(get_cfg_var('post_max_size')));
        } else {
            return l('Upload error '.$this->_error);
        }
    }
    /**
     * 返回文件扩展名
     *
     * @param string $p1    filename
     * @return string
     */
    function _getExt($p1){
        $R = pathinfo($p1);
        return $R['extension'];
    }
    /**
     * 检查文件大小
     *
     * @param int $size
     * @return bool
     */
    function _checkSize($size){
        if ($this->maxSize == 0) {
            return false;
        }
        if ((int)$size > (int)$this->maxSize) {
            $this->_error = $this->_UPLOAD_ERR_MAX_SIZE;
            return true;
        }
        return false;
    }
    /**
     * 检查文件是否合法
     *
     * @param string $p1 扩展名
     * @return bool
     */
    function _checkExt($p1){
        $p1 = strtolower($p1);
        $p2 = $this->allowExts;
        if (empty($p2) || $p2=='*') {
            return true;
        }
        if (!is_array($p2)) {
            $p2 = explode(',',strtolower($p2));
        }
        return in_array($p1,$p2) ? true : false;
    }
}