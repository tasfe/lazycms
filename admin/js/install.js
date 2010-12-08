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
function install_init() {
    $('button[rel=phpinfo]').click(function(){
        $('form#setup div.center').toggle();
    });

    $('form#setup_cfg').ajaxSubmit(function(){
        $('.dialog .buttons button').text(_('Rock it!'));
    });
    $('#password1').val('').keyup( install_check_pass_strength );
    $('#password2').val('').keyup( install_check_pass_strength );

    var dbtype = $('select#dbtype');
    if (dbtype.is('select')) {
        dbtype.change(function(){
            install_change_dbtype(this.value);
        });
        install_change_dbtype(dbtype.val());
    }
}
// 验证密码强弱
function install_check_pass_strength() {
    $('#pass-strength-result').check_pass_strength(
            $('#adminname').val(),
            $('#password1').val(),
            $('#password2').val()
    );
}
// 改变数据库类型
function install_change_dbtype(type) {
    // sqlite
    if (type.substr(0,6)=='sqlite' || type.substr(0,10)=='pdo_sqlite') {
        $('input#uname,input#pwd,input#dbhost').parents('tr').hide();
        var dbname = $('input#dbname').val();
        if (dbname.substr(-3) != '.db') {
            $('input#dbname').val($('input#dbname').attr('rel') + '.db');
        }
    }
    // mysql
    else {
        $('input#uname,input#pwd,input#dbhost').parents('tr').show();
        var dbname = $('input#dbname').val();
        if (dbname.substr(-3) == '.db') {
            $('input#dbname').val('test');
        }
    }
}