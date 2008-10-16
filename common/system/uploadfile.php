<?php
/**
 * +---------------------------------------------------------------------------+
 * | LL                             LLLL   LL     L   LLLL                 LL  |
 * | LL                            LL   L  LLL   LL  LL   L               LLL  |
 * | LL      LLLL   LLLLL  LL  LL  LL      LLLL LLL  LL          LL  LL    LL  |
 * | LL         LL     LL  LL  LL  LL      L LLL LL  LLLLL       LL  LL    LL  |
 * | LL      LLLLL    LL    LLLL   LL      L  L  LL   LLLLL      LL  LL    LL  |
 * | LL     LL  LL   LL     LLLL   LL      L     LL      LL       LLLL     LL  |
 * | LL     LL  LL  LL       LL    LL   L  L     LL  L   LL       LLLL     LL  |
 * | LLLLLL  LLLLL  LLLLL    LL     LLLL   L     LL   LLLL         LL     LLLL |
 * |                        LL                                                 |
 * |                        LL                                                 |
 * +---------------------------------------------------------------------------+
 * | Copyright (C) 2007-2008 LazyCMS.net All rights reserved.                  |
 * +---------------------------------------------------------------------------+
 * | LazyCMS is free software. This version use Apache License 2.0             |
 * | See LICENSE.txt for copyright notices and details.                        |
 * +---------------------------------------------------------------------------+
 */
//defined('COM_PATH') or die('Restricted access!');
/**
 * 文件上传类
 *
 * @copyright   Copyright (c) 2007-2008 lazycms.net All rights reserved.
 * @author      Lukin <mylukin@gmail.com>
 * @date        2008-10-15
 */
// UpLoadFile *** *** www.LazyCMS.net *** ***
class UpLoadFile{
    // 上传的文件太大
    const UPLOAD_ERR_MAX_SIZE   = 5;
    // 上传的文件类型不允许
    const UPLOAD_ERR_FORBID_EXT = 8;
    // 非法提交
    const UPLOAD_ERR_NO_SUBMIT  = 9;

    // 单个文件大小，0:为不限制
    private $maxSize = 0;
    
    // 保存路径
    private $savePath;
    
    // 允许的文件后缀
    private $allowExts;

    // 错误信息
    private $error = -1;

    // __construct *** *** www.LazyCMS.net *** ***
    function __construct(){
        if ((int)get_cfg_var('post_max_size') < (int)$_SERVER["CONTENT_LENGTH"]/1024/1024) {
            $this->error = 0;
        }
    }

    // __set *** *** www.LazyCMS.net *** ***
    private function __set($name ,$value){
        if(property_exists($this,$name)){
            switch ($name) {
                case 'savePath':
                    if (substr($value,-1)!=='/') {
                        $value .= '/';
                    }
                    $this->$name = $value;
                    break;
                case 'maxSize':
                    if (!is_numeric($value)) {
                        $value = 0;
                    }
                    $this->$name = $value;
                    break;
                default :
                    $this->$name = $value;
                    break;
            }
        }
    }
    // __get *** *** www.LazyCMS.net *** ***
    private function __get($name){
        if(isset($this->$name)){
            return $this->$name;
        } else {
            return null;
        }
    }
    // save *** *** www.LazyCMS.net *** ***
    public function save($p1,$p2=null){
        // $p1:file
        if (!isset($_FILES[$p1])) { return false; }
        $Info = $_FILES[$p1];
        $Info['ext'] = $this->getExt($Info['name']);
        // 返回错误信息
        if ((int)$Info['error'] != 0){
            $this->error = $Info['error'];
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
            if ($this->checkSize($Info['size'])){
                return false;
            }
            // 检查文件后缀
            if (!$this->checkExt($this->getExt($Info['name']))){
                $this->error = self::UPLOAD_ERR_FORBID_EXT;
                return false;
            }
            if (move_uploaded_file($Info['tmp_name'],$p2)){
                unset($Info['tmp_name'],$Info['error']);
                return $Info;
            } else {
                return false;
            }
        } else {
            $this->error = self::UPLOAD_ERR_FORBID_SUBMIT;
        }
        return false;
    }
    // saves *** *** www.LazyCMS.net *** ***
    public function saves($p1=array()){
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
                if ($this->error!=4) {
                    $R[$name] = $this->getError();
                }
            }
        }
        return $R;
    }
    // getError *** *** www.LazyCMS.net *** ***
    public function getError(){
        if ($this->error==0) {
            return L('error/upload/err'.$this->error,array('max'=>get_cfg_var('post_max_size')),'system');
        } else {
            return L('error/upload/err'.$this->error,'system');
        }
    }
    // getExt *** *** www.LazyCMS.net *** ***
    private function getExt($p1){
        // $p1:filename
        $R = pathinfo($p1);
        return $R['extension'];
    }
    // checkSize *** *** www.LazyCMS.net *** ***
    private function checkSize($size){
        if ($this->maxSize == 0) {
            return false;
        }
        if ((int)$size > (int)$this->maxSize) {
            $this->error = self::UPLOAD_ERR_MAX_SIZE;
            return true;
        }
        return false;
    }
    // checkExt *** *** www.LazyCMS.net *** ***
    private function checkExt($p1){
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