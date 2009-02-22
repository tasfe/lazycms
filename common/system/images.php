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
 * 图像处理类
 */
class Images{
    // getImageInfo *** *** www.LazyCMS.net *** ***
    static function getImageInfo($img) {
        $imageInfo = getimagesize($img);
        if ($imageInfo !== false) {
            $imageType = strtolower(substr(image_type_to_extension($imageInfo[2]),1));
            $imageSize = filesize($img);
            $info = array(
                "width"  => $imageInfo[0],
                "height" => $imageInfo[1],
                "type"   => $imageType,
                "size"   => $imageSize,
                "mime"   => $imageInfo['mime']
            );
            return $info;
        }else {
            return false;
        }
    }
    // thumb *** *** www.LazyCMS.net *** ***
    static function thumb($image,$filename='',$maxWidth=100,$maxHeight=100,$suffix='_thumb') {
        // 获取原图信息
        $info  = Images::getImageInfo($image); 
        if($info !== false) {
            // 原图大小
            $srcWidth  = $info['width'];
            $srcHeight = $info['height'];
            $type = pathinfo($image,PATHINFO_EXTENSION);
            $type = empty($type) ? $info['type'] : $type;
            $type = strtolower($type); unset($info);
            $scale = min($maxWidth/$srcWidth, $maxHeight/$srcHeight); // 计算缩放比例

            // 缩略图尺寸
            $width  = ((int)$srcWidth>(int)$maxWidth) ? (int)($srcWidth * $scale): $srcWidth;
            $height = ((int)$srcHeight>(int)$maxHeight) ? (int)($srcHeight * $scale): $srcHeight;

            // 载入原图
            $createFun = 'ImageCreateFrom'.($type=='jpg'?'jpeg':$type);
            $srcImg    = $createFun($image); 

            //创建缩略图
            if ($type!='gif' && function_exists('imagecreatetruecolor')) {
                $thumbImg = imagecreatetruecolor($width, $height);
            } else {
                $thumbImg = imagecreate($width, $height);
            }

            // 复制图片
            if (function_exists("ImageCopyResampled")) {
                ImageCopyResampled($thumbImg, $srcImg, 0, 0, 0, 0, $width, $height, $srcWidth,$srcHeight);
            } else {
                ImageCopyResized($thumbImg, $srcImg, 0, 0, 0, 0, $width, $height,  $srcWidth,$srcHeight); 
            }

            if('gif'==$type || 'png'==$type) {
                $background_color = imagecolorallocate($thumbImg,0,255,0);  //  指派一个绿色  
                imagecolortransparent($thumbImg,$background_color);  //  设置为透明色，若注释掉该行则输出绿色的图 
            }

            // 对jpeg图形设置隔行扫描
            if('jpg'==$type || 'jpeg'==$type) { imageinterlace($thumbImg,1); }

            // 生成图片
            $imageFun = 'image'.($type=='jpg'?'jpeg':$type);
            
            if (empty($filename)) {
                $filename = substr($image,0,strrpos($image, '.')).$suffix.'.'.$type;
            } else {
                $path = pathinfo($filename,PATHINFO_DIRNAME);
                mkdirs($path);
            }

            $imageFun($thumbImg,$filename); 
            ImageDestroy($thumbImg);
            ImageDestroy($srcImg);
            return $filename;
         }
         return false;
    }
}