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
require '../../global.php';
/**
 * 退出登陆
 *
 * @copyright   Copyright (c) 2007-2008 lazycms.net All rights reserved.
 * @author      Lukin <mylukin@gmail.com>
 * @date        2008-6-25
 */
// lazy_default *** *** www.LazyCMS.net *** ***
function lazy_default(){
    $username = isset($_POST['username']) ? $_POST['username'] : null;
    
    $val = new Validate();
    if ($val->method()) {
        $val->check('username|1|'.L('users/user/check/name').'|1-30');
        if ($val->isVal()) {
            var_dump($val->fetch());exit;
        }
    }

    $hl = '<form id="form1" name="form1" method="post" action="">';
    $hl.= '<p><label>'.L('users/user/add/name').'：</label><input tip="'.L('users/user/add/name').'::'.L('users/user/add/name/@tip').'" class="in2" type="text" name="username" id="username" value="'.$username.'" /></p>';
    $hl.= but('save').'</form>';
    print_x('test',$hl);
}