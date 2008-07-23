<?php
/**
 * +---------------------------------------------------------------------------+
 * | LL                             LLLL   LL     L   LLLL                 LL  |
 * | LL                            LL   L  LLL   LL  LL   L               LLL  |
 * | LL      LLLL   LLLLL  LL  LL  LL      LLLL LLL  LL          LL  LL    LL  |
 * | LL         LL     LL  LL  LL  LL      L LLL LL  LLLLL       LL  LL    LL  |
 * | LL      LLLLL    LL    LLLL   LL      L  L  LL   LLLLL      LL  LL    LL  |
 * | LL     LL  LL   LL     LLLL   LL      L     LL      LL       LLLL     LL  |
 * | LL     LL  LL  LL       LL    LL   L  L     LL  L   LL       LLLL     LL  |
 * | LLLLLL  LLLLL  LLLLL    LL     LLLL   L     LL   LLLL         LL     LLLL |
 * |                        LL                                                 |
 * |                        LL                                                 |
 * +---------------------------------------------------------------------------+
 * | Copyright (c) 2007-2008 LazyCMS.net All rights reserved.                  |
 * +---------------------------------------------------------------------------+
 * | 许可协议，请查看源代码中附带的 LICENSE.txt 文件，                         |
 * | 或者访问 http://www.lazycms.net/ 获得详细信息。                           |
 * +---------------------------------------------------------------------------+
 */
require '../global.php';
/**
 * 退出登陆
 *
 * @copyright   Copyright (c) 2007-2008 lazycms.net All rights reserved.
 * @author      Lukin <mylukin@gmail.com>
 * @date        2008-6-25
 */
// lazy_default *** *** www.LazyCMS.net *** ***
function lazy_default(){ 
    set_time_limit(0);
    //lazysql_open(LAZY_PATH.'/LazyCMS#DataBase');
    $db = DB::factory('lazysql://path=LazyCMS#DataBase');
    $db->select_db();
    /*
    $db->exec("
        create table `article`(
          `artid` '0' primary,
          `arttitle` '' index,
          `artcontent` ''
        );
    ");
    */
    //$db->exec("drop table `article`;");
    $db->exec("truncate table `article`;");
    
    $GLOBALS['_beginTime'] = microtime(true);
    $i = 0;
    for ($i=0; $i<10; $i++) { 
        $db->exec("INSERT INTO `article`(`arttitle`,`artcontent`)values('','{$i}.测试内容');"); 
    }
    //$db->exec("alter table `article` drop `title`;");
    $GLOBALS['_endTime'] = microtime(true);
    echo number_format($GLOBALS['_endTime']-$GLOBALS['_beginTime'],6);
}