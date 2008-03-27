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
 * | Copyright (c) 2007-2008 LazyCMS.net All rights reserved.                  |
 * +---------------------------------------------------------------------------+
 * | 许可协议，请查看源代码中附带的 LICENSE.txt 文件，                         |
 * | 或者访问 http://www.lazycms.net/ 获得详细信息。                           |
 * +---------------------------------------------------------------------------+
 * | Author: Lukin <mylukin@gmail.com>                                         |
 * +---------------------------------------------------------------------------+
 */
defined('CORE_PATH') or die('Restricted access!');
/**
 * 文件上传类
 *
 * @copyright   Copyright (c) 2007-2008 lazycms.net All rights reserved.
 * @author      Lukin <mylukin@gmail.com>
 */
// Record *** *** www.LazyCMS.net *** ***
class UpLoadFile extends Lazy{
	// 上传的文件太大
	const UPLOAD_ERR_MAX_SIZE   = 5;
	// 上传的文件类型不允许
	const UPLOAD_ERR_FORBID_EXT = 8;
	// 非法提交
	const UPLOAD_ERR_NO_SUBMIT  = 9;

	// 单个文件大小
	private $maxSize = 0;
	
	// 保存路径
	private $savePath;
	
	// 允许的文件后缀
	private $allowExts;

	// 错误信息
	private $error = 0;

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
	public function save($l1,$l2=null){
		// $l1:file
		if (!isset($_FILES[$l1])) { return false; }
        $Info = $_FILES[$l1];
        $Info['ext'] = $this->getExt($Info['name']);
		// 返回错误信息
		if ($Info['error'] != 0){
			$this->error = $Info['error'];
			return false;
		}
		if (empty($l2)){
            $l3 = $this->savePath.microtime(true).salt().'.'.$Info['ext'];
			$l2 = LAZY_PATH.$l3;
            $Info['path'] = C('SITE_BASE').$l3;
		} else {
            if (file_exists($l2)) {
                $l2 = substr($l2,0,strrpos($l2,'/')).'/'.basename($l2,'.'.$Info['ext']).salt().'.'.$Info['ext'];
            }
            $Info['path'] = $l2;
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
			if (move_uploaded_file($Info['tmp_name'],$l2)){
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
	public function saves($l1=array()){
	    if (empty($l1)) {
            $I2 = $_FILES;
        } else {
            $I2 = $l1;
        }
        $I1 = array();
        foreach ($I2 as $name=>$path) {
            if (empty($l1)) {
                $file = $this->save($name);
            } else {
                $file = $this->save($name,$path);
            }
            if ($file) {
                $I1[$name] = $file;
            } else {
                if ($this->error!=4) {
                    $I1[$name] = $this->getError();
                }
            }
        }
        return $I1;
	}
	// getError *** *** www.LazyCMS.net *** ***
	public function getError(){
        if ($this->error!=0) {
            return L('error/upload/err'.$this->error);
        }
	}
	// getExt *** *** www.LazyCMS.net *** ***
	private function getExt($l1){
		// $l1:filename
        $I1 = pathinfo($l1);
		return $I1['extension'];
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
	private function checkExt($l1){
		$l1 = strtolower($l1);
		$l2 = $this->allowExts;
		if (!is_array($l2)) {
			$l2 = explode(",",$l2);
			$l2 = array_map('strtolower',$l2);
		}
		return in_array($l1,$l2) ? true : false;
	}
}