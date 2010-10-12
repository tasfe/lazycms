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
 * | Copyright (C) 2007-2008 LazyCMS.com All rights reserved.                  |
 * +---------------------------------------------------------------------------+
 * | LazyCMS is free software. This version use Apache License 2.0             |
 * | See LICENSE.txt for copyright notices and details.                        |
 * +---------------------------------------------------------------------------+
 */
function install_init() {
    $('form#setup').ajaxSubmit(function(){
        $('.dialog .buttons button').text(_('Rock it!'));
    });
    $('#passowrd1').val('').keyup( install_check_pass_strength );
    $('#passowrd2').val('').keyup( install_check_pass_strength );
}
// 验证密码强弱
function install_check_pass_strength() {
    var pass1 = $('#passowrd1').val(), user = $('#adminname').val(), pass2 = $('#passowrd2').val(), strength;

    $('#pass-strength-result').removeClass('short bad good strong');
    if ( ! pass1 ) {
        $('#pass-strength-result').html( _('Strength indicator') );
        return;
    }

    strength = password_strength(user, pass1, pass2);

    switch ( strength ) {
        case 2:
            $('#pass-strength-result').addClass('bad').html( _('Weak') );
            break;
        case 3:
            $('#pass-strength-result').addClass('good').html( _('Medium') );
            break;
        case 4:
            $('#pass-strength-result').addClass('strong').html( _('Strong') );
            break;
        case 5:
            $('#pass-strength-result').addClass('short').html( _('Mismatch') );
            break;
        default:
            $('#pass-strength-result').addClass('short').html( _('Very weak') );
    }
}