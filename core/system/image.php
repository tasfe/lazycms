<?php
// Image *** *** www.LazyCMS.net *** ***
class Image{
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
        $info  = Image::getImageInfo($image); 
        if($info !== false) {
            $srcWidth  = $info['width'];
            $srcHeight = $info['height'];
            $pathinfo  = pathinfo($image);
            $type = $pathinfo['extension'];
            $type = empty($type) ? $info['type'] : $type;
			$type = strtolower($type); unset($info);
            $scale = min($maxWidth/$srcWidth, $maxHeight/$srcHeight); // 计算缩放比例

            // 缩略图尺寸
            $width  = (int)($srcWidth*$scale);
            $height = (int)($srcHeight*$scale);

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
                $paths = pathinfo($filename); 
                $path  = $paths['dirname'];
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