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
        $suffixs   = C('UPIMG-Exts');
        $matchs[1] = array_unique($matchs[1]);
        foreach ($matchs[1] as $url) {
            $str = $url;
            $url = trim(trim(trim($url),'"'), "'");
            // 符合，下载文件
            if (validate_is($url, VALIDATE_IS_URL)) {
                if (strpos($url, '&amp;') !== false) $url = xmldecode($url);
                $aurl = httplib_parse_url($url);
                $resp = httplib_get($url, array(
                    'timeout'   => 60,
                    'headers'   => array(
                        'referer' => $aurl['referer'],
                    ),
                ));
                if (httplib_retrieve_response_code($resp) == 200) {
                    // 取得文件后缀
                    $suffix = pathinfo($aurl['path'], PATHINFO_EXTENSION);
                    if (!instr($suffix, $suffixs)) {
                        $ctype = httplib_retrieve_header($resp, 'content-type');
                        if (($pos=strrpos($ctype, '/')) !== false) {
                            $suffix = substr($ctype, $pos + 1);
                        }
                        // 文件后缀还不正确,强制指定jpg
                        if (!instr($suffix, $suffixs)) {
                            $suffix = 'jpg';
                        }
                    }
                    $body = httplib_retrieve_body($resp);
                    // sha1sum
                    $sha1sum = sha1($body);
                    // 不需要替换的文件
                    if ($file = media_no_add($sha1sum)) {
                        if ($str != '"'.ROOT.$file.'"') {
                            $content = str_replace($str, '"'.ROOT.$file.'"', $content);
                        }
                        continue;
                    }
                    // 文件存在
                    if (!($media = media_get($sha1sum))) {
                        $time = time();
                        $path = ABS_PATH . '/' . MEDIA_PATH . '/images/' . date('Y-m-d', $time) . '/' . $sha1sum . '.' . $suffix;
                        mkdirs(dirname($path)); file_put_contents($path, $body);
                        // 添加
                        $media = media_get(media_add('images', $sha1sum, pathinfo($aurl['path'], PATHINFO_BASENAME), strlen($body), $suffix, $time));
                    }
                    // 替换原始内容
                    $content = str_replace($str, '"'.$media['url'].'"', $content);
                }
            }
        }
    }
    return $content;
}
/**
 * 是否替换
 *
 * @param string $sha1sum
 * @return bool|null
 */
function media_no_add($sha1sum) {
    $file_sum  = array(
        '05556151b4c76b812e0f8f5d4619e39b' => 'common/images/emots/default/despise.gif',
        '344433f16e4c7458abbefe625ea5cf79' => 'common/images/emots/default/crazy.gif',
        '68e089023da6a669bda0019487c9261a' => 'common/images/emots/default/laugh.gif',
        'ae2bb1ad58cd2a251279992be3129798' => 'common/images/emots/default/angry.gif',
        'd5dff372bc0fb83ca5a370d930620394' => 'common/images/emots/default/fastcry.gif',
        '0bd0834f7ea3cc376703677b3759e79b' => 'common/images/emots/default/bye.gif',
        '36e7068fd4b6050a9ca5e97b6c5ad105' => 'common/images/emots/default/cute.gif',
        '7926792eaf17416c6e59e2ae43c77411' => 'common/images/emots/default/quiet.gif',
        'ae551c1340358d0d2e708cb9cb98a378' => 'common/images/emots/default/cry.gif',
        'db43f69d2445682946de87faeca3f320' => 'common/images/emots/default/smile.gif',
        '1b5402269f6667281f25eaab21b81cb2' => 'common/images/emots/default/struggle.gif',
        '4fbc8725aa18b52a49ae5f3167c2b2d8' => 'common/images/emots/default/mad.gif',
        '89b6eca141f5150a765a7416af7de044' => 'common/images/emots/default/sleep.gif',
        'b2b128d1603e3c81a0030619c9a78f29' => 'common/images/emots/default/sad.gif',
        'e366f331c7e86467887cdb44f83a8127' => 'common/images/emots/default/panic.gif',
        '20b47ef5c6c4081c7f5c1251f6c9760d' => 'common/images/emots/default/ohmy.gif',
        '5807abd3b5c78ec8d2d23cc4b882b3af' => 'common/images/emots/default/wail.gif',
        '90ed96b1a358301ac153a19717cd052c' => 'common/images/emots/default/titter.gif',
        'bbe2b8079f05b831631c1fdbdc671e2d' => 'common/images/emots/default/shy.gif',
        'fae6c3f152523d95fe152cd99b476f90' => 'common/images/emots/default/awkward.gif',
        '301cf1b333e201d1adfe8b9ae6ff277a' => 'common/images/emots/default/tongue.gif',
        '6658f97feb53ffb3151640f9dae1d70f' => 'common/images/emots/default/knock.gif',
        '91f2feec64292b805884d328247f2fa3' => 'common/images/emots/default/envy.gif',
        'bcae571397af456b800fa516d15a7d67' => 'common/images/emots/default/curse.gif',
        '30da1edf12e5942046091bad38c90f5b' => 'common/images/emots/default/doubt.gif',
        '68344b1f75c2b180640be3a9db31c11c' => 'common/images/emots/default/proud.gif',
        'a09b0c20b15ea8206bd133792d6dc203' => 'common/images/emots/default/shutup.gif',
        'bedebef3e120d4fe5a473a8fe23a293f' => 'common/images/emots/default/wronged.gif',
    );
    return isset($file_sum[$sha1sum]) ? isset($file_sum[$sha1sum]) : null;
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
    if (strlen($id) == 40) {
        $rs = $db->query("SELECT * FROM `#@_media` WHERE `sha1sum`='%s' LIMIT 0,1;", $id);
    } else {
        $rs = $db->query("SELECT * FROM `#@_media` WHERE `mediaid`=%d LIMIT 0,1;", $id);
    }
    if ($data = $db->fetch($rs)) {
        $file = media_file($data);
        $data['path'] = ABS_PATH . '/' . $file;
        $data['url']  = ROOT . $file;
        fcache_set($ckey, $data);
    }
    return $data;
}
/**
 * 获得media路径
 *
 * @param array $data
 * @return string
 */
function media_file($data) {
    if (!is_array($data)) $data = media_get(intval($data));
    return MEDIA_PATH . '/' . $data['folder'] . '/' . date('Y-m-d', $data['addtime']) . '/' . $data['sha1sum'] . '.' . $data['suffix'];
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
            $content = str_replace(
                $matchs[0][$i],
                $matchs[1][$i] . $media['url'] . $matchs[3][$i],
                $content
            );
        }
    }
    return $content;
}
/**
 * 添加媒体
 *
 * @param string $folder
 * @param string $sha1sum
 * @param string $name
 * @param int $size
 * @param string $suffix
 * @param int $addtime
 * @return int|bool
 */
function media_add($folder, $sha1sum, $name, $size, $suffix, $addtime=null) {
    global $_USER;
    // 获取管理员信息
    if (!isset($_USER)) $_USER = user_current(false);
    return @get_conn()->insert('#@_media', array(
        'folder'    => $folder,
        'sha1sum'   => $sha1sum,
        'name'      => $name,
        'suffix'    => $suffix,
        'size'      => $size,
        'userid'    => $_USER['userid'],
        'addtime'   => $addtime ? $addtime : time(),
    ));
}
/**
 * 清空缓存
 *
 * @param int $id
 * @return bool
 */
function media_clean_cache($id) {
    return fcache_delete('media.id.'.$id);
}
/**
 * 删除媒体
 *
 * @param int $id
 * @return bool
 */
function media_delete($id) {
    $id = intval($id);
    if (!$id) return false;
    if ($media = media_get($id)) {
        media_clean_cache($media['mediaid']);
        media_clean_cache($media['sha1sum']);
        get_conn()->delete('#@_media',array('mediaid'=>$id));
        // 删除文件
        $files = glob(dirname($media['path']).'/'.$media['sha1sum'].'*');
        foreach($files as $file) if (is_file($file)) unlink($file);
        return true;
    }
    return false;
}