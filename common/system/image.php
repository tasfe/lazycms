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

class Image {
    /**
     * 取得图片信息
     *
     * @param string $file
     * @return array|bool
     */
    function info($file) {
        if ($info = getimagesize($file)) {
            return array(
                'width'  => $info[0],
                'height' => $info[1],
                'type'   => strtolower(image_type_to_extension($info[2], false)),
                'size'   => filesize($file),
                'mime'   => $info['mime']
            );
        }
        return false;
    }
    /**
     * 缩略图
     *
     * @param string $image
     * @param int $max_w
     * @param int $max_h
     * @param string $toname
     * @return bool|null
     */
    function thumb($image, $max_w=100, $max_h=100, $toname=null) {
        // 获取原图信息
        if($info = Image::info($image)) {
            // 原图大小
            $src_w = $info['width']; $src_h = $info['height'];
            $type  = $info['type'] ? $info['type'] : strtolower(pathinfo($image, PATHINFO_EXTENSION));
            // 计算缩放比例
            $scale = min($max_w / $src_w, $max_h / $src_h);

            // 缩略图尺寸
            $width  = ((int)$src_w > (int)$max_w) ? (int)($src_w * $scale) : $src_w;
            $height = ((int)$src_h > (int)$max_h) ? (int)($src_h * $scale) : $src_h;

            // 载入原图
            $create = 'imagecreatefrom' . ($type == 'jpg' ? 'jpeg' : $type);
            $srcimg = $create($image);

            //创建缩略图
            if ($type != 'gif' && function_exists('imagecreatetruecolor')) {
                $thumb = imagecreatetruecolor($width, $height);
            } else {
                $thumb = imagecreate($width, $height);
            }

            // 复制图片
            if (function_exists('imagecopyresampled')) {
                imagecopyresampled($thumb, $srcimg, 0, 0, 0, 0, $width, $height, $src_w, $src_h);
            } else {
                imagecopyresized($thumb, $srcimg, 0, 0, 0, 0, $width, $height, $src_w, $src_h);
            }

            if ('gif' == $type || 'png' == $type) {
                // 指派一个绿色
                $background_color = imagecolorallocate($thumb, 0, 255, 0);
                // 设置为透明色，若注释掉该行则输出绿色的图
                imagecolortransparent($thumb, $background_color);
            }

            // 对jpeg图形设置隔行扫描
            if ('jpg' == $type || 'jpeg' == $type) imageinterlace($thumb, 1);

            // 生成图片
            $imagefun = 'image' . ($type == 'jpg' ? 'jpeg' : $type);

            if ($toname) {
                mkdirs(dirname($toname)); $imagefun($thumb, $toname);
            } else {
                $toname = $image; $imagefun($thumb);
            }
            imagedestroy($thumb); imagedestroy($srcimg);
            return $toname;
         }
         return false;
    }
}

if (!function_exists('image_type_to_extension')) {
    /**
     * Get file extension for image type
     *
     * @param int $imagetype
     * @param bool $include_dot
     * @return bool|string
     */
    function image_type_to_extension($imagetype, $include_dot=true) {
        if (empty($imagetype)) return false;
        $dot = $include_dot ? '.' : '';
        switch ($imagetype) {
            case IMAGETYPE_GIF       : return $dot.'gif';
            case IMAGETYPE_JPEG      : return $dot.'jpg';
            case IMAGETYPE_PNG       : return $dot.'png';
            case IMAGETYPE_SWF       : return $dot.'swf';
            case IMAGETYPE_PSD       : return $dot.'psd';
            case IMAGETYPE_BMP       : return $dot.'bmp';
            case IMAGETYPE_TIFF_II   : return $dot.'tiff';
            case IMAGETYPE_TIFF_MM   : return $dot.'tiff';
            case IMAGETYPE_JPC       : return $dot.'jpc';
            case IMAGETYPE_JP2       : return $dot.'jp2';
            case IMAGETYPE_JPX       : return $dot.'jpf';
            case IMAGETYPE_JB2       : return $dot.'jb2';
            case IMAGETYPE_SWC       : return $dot.'swc';
            case IMAGETYPE_IFF       : return $dot.'aiff';
            case IMAGETYPE_WBMP      : return $dot.'wbmp';
            case IMAGETYPE_XBM       : return $dot.'xbm';
            case IMAGETYPE_ICO       : return $dot.'ico';
            default                  : return false;
        }
    }
}