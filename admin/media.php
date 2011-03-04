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
// 加载公共文件
include dirname(__FILE__).'/admin.php';
// 查询管理员信息
$_USER = user_current();
// 动作
$method = isset($_REQUEST['method']) ? $_REQUEST['method'] : null;

switch ($method) {
    // 缩略图
    case 'thumb':
        $id   = isset($_GET['id']) ? $_GET['id'] : null;
        $size = isset($_GET['size']) ? $_GET['size'] : null;
        $size = explode('x', $size);
        if ($media = media_get($id)) {
            image_thumb($media['path'], $size[0], $size[1]);
        } else {
            header('Content-type: image/png');
            $img_res   = imagecreatetruecolor($size[0], $size[1]);
            $fontcolor = imagecolorallocate($img_res,128,0,0);
            $bgcolor   = imagecolorallocate($img_res,255,255,255);
            imagefill($img_res,0,0,$bgcolor);
            imagestring($img_res, 5, (imagesx($img_res)-8*3)/2, 15, 'Not', $fontcolor);
            imagestring($img_res, 5, (imagesx($img_res)-8*7)/2, 30, 'Support', $fontcolor);
            imagepng($img_res); imagedestroy($img_res);
        }
        break;
    // 文件下载
    case 'down':
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        if ($media = media_get($id)) {
            $useragent = $_SERVER['HTTP_USER_AGENT'];
            if (is_file($media['path'])) {
                header('Content-type: ' . mime_content_type($media['path']));
                header('Accept-Range : byte ');
                header('Accept-Length: ' . $media['size']);
                if (strpos($useragent, 'MSIE') !== false) {
                    header('Content-Disposition: attachment; filename="'.rawurlencode($media['name']).'"');
                } elseif (strpos($useragent, 'Firefox') !== false) {
                    header('Content-Disposition: attachment; filename*="utf8\' \''.rawurlencode($media['name']).'"');
                } else {
                    header('Content-Disposition: attachment; filename="'.$media['name'].'"');
                }
                readfile($media['path']);
            } else {
                header('Content-type: application/octet-stream');
            }
        }
        break;
}