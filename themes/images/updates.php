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
    $callback = isset($_GET['callback'])?$_GET['callback']:null;
    $logsFile = dirname(__FILE__).'/../changelog.php';
    if (is_file($logsFile)){
        $updates = include_file($logsFile);
    } else {
        $updates  = array();
    }
    // 输出数据
    echo $callback.'('.json_encode($updates).')';
}

// *** *** www.LazyCMS.net *** *** //
function lazy_updated(){
    $input = file_get_contents('php://input','r');
    $key   = hmac_md5('AZD3t2Yt9dTungsX',$input);
    // key 验证通过
    if ($key==$_SERVER['HTTP_GOOGLE_CODE_PROJECT_HOSTING_HOOK_HMAC']) {
        $logsFile = dirname(__FILE__).'/../changelog.php';
        if (!is_file($logsFile)) { save_file($logsFile,"<?php\nreturn array();"); }
        $updates = include_file($logsFile);
        $upData  = json_decode($input);
        foreach ($upData->revisions as $entry) {
            array_unshift($updates, array(
                'revision' => $entry->revision,
                'updated'  => date('Y-m-d H:i:s',$entry->timestamp),
                'content'  => str_replace("\n","<br />",$entry->message)
            ));
        }
        $updates = array_slice($updates,0,20);
        save_file($logsFile,"<?php\nreturn ".var_export($updates,true).";");
        echo 'Authenticated';
    } else {
        echo 'Authentication failed!';
    }
}