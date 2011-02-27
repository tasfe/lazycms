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

/**
 * 图片本地化
 *
 * @param string $content
 * @return string
 */
function media_localized_images($content) {
    include_file(COM_PATH.'/system/httplib.php');
    if (preg_match_all('/<img[^>]+src\s*=([^\s\>]+)[^>]*>/i', $content, $matchs)) {
        $matchs[1] = array_unique($matchs[1]);
        foreach ($matchs[1] as $url) {
            $str = $url;
            $url = trim(trim(trim($url),'"'), "'");
            // 不符合URL，跳过
            if (!validate_is($url, VALIDATE_IS_URL)) continue;
            // 符合，下载文件
            $aurl = httplib_parse_url($url);
            $resp = httplib_get($url, array(
                'timeout'   => 60,
                'headers'   => array(
                    'referer' => $aurl['referer'],
                ),
            ));
            if (httplib_retrieve_response_code($resp) == 200) {
                // 取得文件后缀
                $ctype  = httplib_retrieve_header($resp, 'content-type');
                if (($pos=strrpos($ctype, '/')) !== false) {
                    $suffix = substr($ctype, $pos + 1);
                } else {
                    $suffix = pathinfo($aurl['path'], PATHINFO_EXTENSION);
                }
                $body   = httplib_retrieve_body($resp);
                $md5sum = md5($body);
                // 文件不存在
                if ($media = media_get($md5sum)) {
                    $mediaid = $media['mediaid'];
                } else {
                    $file = ABS_PATH . '/' . MEDIA_PATH . '/images/' . $md5sum . '.' . $suffix;
                    mkdirs(dirname($file)); file_put_contents($file, $body);
                    // 添加
                    $path    = ROOT . MEDIA_PATH . '/images/' . $md5sum . '.' . $suffix;
                    $mediaid = media_add($md5sum, pathinfo($file, PATHINFO_FILENAME), $suffix, strlen($body), $path);
                }
                // 替换原始内容
                $content = str_replace($str, '"['.$mediaid.']"', $content);
            }
        }
    }
    return $content;
}
/**
 * 取得 media 信息
 *
 * @param int|string $id
 * @return array|null
 */
function media_get($id) {
    $ckey = 'media.id.'.$id;
    $data = fcache_get($ckey);
    if (fcache_not_null($data)) return $data;

    $db = get_conn();
    if (strlen($id) == 32) {
        $rs = $db->query("SELECT * FROM `#@_media` WHERE `md5sum`='%s' LIMIT 0,1;", $id);
    } else {
        $rs = $db->query("SELECT * FROM `#@_media` WHERE `mediaid`=%d LIMIT 0,1;", $id);
    }
    if ($data = $db->fetch($rs)) {
        fcache_set($ckey, $data);
    }
    return $data;
}
/**
 * 把ID转换为实际文件
 *
 * @param string $content
 * @return string
 */
function media_decode($content) {
    if (preg_match_all('/(<img[^>]+src\s*=")\[(\d+)\]("[^>]*>)/i', $content, $matchs)) {
        foreach ($matchs[2] as $i=>$id) {
            $media   = media_get($id);
            $content = str_replace($matchs[0][$i], $matchs[1][$i].$media['path'].$matchs[3][$i], $content);
        }
    }
    return $content;
}
/**
 * 添加媒体
 *
 * @param string $md5sum
 * @param string $name
 * @param string $type
 * @param int $size
 * @param string $path
 * @return int|bool
 */
function media_add($md5sum, $name, $type, $size, $path) {
    global $_USER; $db = get_conn();
    // 获取管理员信息
    if (!isset($_USER)) $_USER = user_current(false);
    return $db->insert('#@_media', array(
        'name'      => $name,
        'md5sum'    => $md5sum,
        'type'      => $type,
        'size'      => $size,
        'path'      => $path,
        'userid'    => $_USER['userid'],
        'addtime'   => time(),
    ));
}