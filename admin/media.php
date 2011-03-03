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
$method = isset($_REQUEST['method'])?$_REQUEST['method']:null;

switch ($method) {
    case 'thumb':
        $id   = isset($_GET['id']) ? $_GET['id'] : null;
        $size = isset($_GET['size']) ? $_GET['size'] : null;
        $size = explode('x', $size);
        //header('Cache-Control: max-age='.(30*365*24*60*60));
        header("Content-type: image/png");
        $img = imagecreatetruecolor($size[0], $size[1]);
        $col = imagecolorallocate($img,128,0,0);
        imagefill($img,0,0,imagecolorallocate($img,255,255,255));
        imagefill($img,0,0,imagecolorallocate($img,255,255,255));
        imagestring($img, 5, (imagesx($img)-8*2)/2, 15, "No", $col);
        imagestring($img, 5, (imagesx($img)-8*7)/2, 30, "Access!", $col);
        imagepng($img); imagedestroy($img);
        if ($media = media_get($id)) {

        } else {

        }
        break;
}