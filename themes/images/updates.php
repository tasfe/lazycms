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
require '../../global.php';
/**
 * 更新日志
 *
 */
// *** *** www.LazyCMS.net *** *** //
function lazy_main(){
    $gUpdated = isset($_GET['updated'])?$_GET['updated']:null;
    $callback = isset($_GET['callback'])?$_GET['callback']:null;
    $updates  = array(); $isExpire = true;
    $logsFile = dirname(__FILE__).'/changelog.php';
    if (is_file($logsFile)){
        $lastTime = filemtime($logsFile);
        $updates  = include_file($logsFile);
        if ($lastTime + 24*3600 >= now()) {
            $isExpire = false;
        }
    }
    // 是否已过期
    if ($isExpire || $gUpdated) {
        import('system.httplib');
        $http = new Httplib('http://code.google.com/feeds/p/lazycms/updates/basic');
        $http->send();
        if ($http->status()==200){
            $response = $http->response();
            // 最后更新日期
            $lastUpdated = sect($response,'<updated>','</updated>');
            // 最后更新日期没变化，不再更新
            if (intval($updates['LASTUPDATED']) != strtotime($lastUpdated) || $gUpdated) {
                $updates  = array();
                // 记录最后更新日期
                $updates['LASTUPDATED'] = strtotime($lastUpdated);
                // 取得所有的更新
                $entrys  = preg_match_all('/<entry>(.+)<\/entry>/isU',$response,$args);
                // 遍历更新条目
                foreach ($args[1] as $entry) {
                    // 取得条目更新时间
                    $updated  = sect($entry,'<updated>','</updated>');
                    // 取得版本号
                    $revision = sect($entry,'detail?r=','"');
                    // 取得详细链接
                    $detail   = sect($entry,'type="text/html" href="','"');
                    // 取得更新内容
                    $content  = sect($entry,'<content type="html">','</content>',"&lt;span class=&quot;ot-logmessage&quot;&gt;\n&lt;/span&gt;");
                    // 存储数据结果
                    $updates['ENTRYS'][] = array(
                        'revision' => $revision,
                        'updated'  => date('Y-m-d H:i:s',strtotime($updated)),
                        'detail'   => $detail,
                        'content'  => html_entity_decode($content)
                    );
                }
                // 将数据结果保存为文件
                save_file($logsFile,"<?php\n/* LazyCMS ChangeLogs ".date('Y-m-d H:i:s',now())." */\nreturn ".var_export($updates,true).";");
            }
        } else {
            if (is_file($logsFile)){
                $updates = include_file($logsFile);
            }
        }
    }
    // 输出数据
    echo $callback.'('.json_encode($updates['ENTRYS']).')';

    if($_REQUEST)
    {
    	$fp = fopen(dirname(__FILE__).'/11.log','a+');
    	fwrite($fp,"[".date('Y-m-d H:i:s',now())."]\t".var_export($_REQUEST,true)."\r\n");
    	fclose($fp);
    }
}