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
    /*
    import('class.splitword');
    $sw = new SplitWord;
    $GLOBALS['_endTime'] = microtime(TRUE);
    $GLOBALS['_beginTime1'] = microtime(TRUE);
    $result = $sw->getWord("古装大片《赤壁》马上就要上映了，由于片中众星云集自然也吸引了众多影迷的关注。不过，近日有网友指出，《赤壁》早前发布的一张横版海报居然抄袭了2007年由好莱坞制作发行的大片《300勇士》海报，除了画面基调相符，连其中的细节都如出一辙。 ",4);
    echo implode(' ',$result);
    $GLOBALS['_endTime1'] = microtime(TRUE);
    echo '<br/>Process Times '.number_format($GLOBALS['_endTime']-$GLOBALS['_beginTime'],6).'s';
    echo '<br/>Process Times '.number_format($GLOBALS['_endTime1']-$GLOBALS['_beginTime1'],6).'s';
    */
    echo date('Y-m-d H:i:s',now());
}