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
 * 获取googlecode更新
 *
 * @return string
 */
function lazycms_gateway_updates() {
    $result = array();
    $ckey   = 'lazycms.gateway.updates';
    $result = fcache_get($ckey);
    if (fcache_not_null($result)) return $result;
    // 加载HTTP类
    include COM_PATH.'/system/httplib.php';
    // get feeds
    $r    = @httplib_get('http://code.google.com/feeds/p/lazycms/svnchanges/basic');
    $code = httplib_retrieve_response_code($r);
    if ($code == '200') {
        $body = httplib_retrieve_body($r);
        $result['more'] = mid($body,'/<link\s+?rel="alternate"[^>]+?href="/i','"');
        if (preg_match_all('/<entry>.+?<\/entry>/is',$body,$args)) {
            foreach($args[0] as $entry) {
                // 跳过WIKI
                if (preg_match('/\/wiki\/(.+?)\.wiki/',$entry)) continue;
                $content   = mid($entry,"/<content[^>]+?>.+&lt;br\/&gt;\n &lt;br\/&gt;/s","</content>");
                $alternate = mid($entry,'/<link\s+?rel="alternate"[^>]+?href="/i','"');
                $result['entrys'][] = array(
                    'id'      => mid($alternate,'detail?r='),
                    'updated' => date('Y-m-d H:i:s',strtotime(mid($entry,"<updated>","</updated>"))),
                    'link'    => $alternate,
                    'author'  => mid($entry,'/<author>.+?<name>/is','/<\/name>.+?<\/author>/is'),
                    'content' => nl2br($content),

                );
            }
        }
        fcache_set($ckey,$result,3600);
    }
    return $result;
}
