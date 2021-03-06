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
function user_list_init() {
	// 绑定提交事件
	$('#userlist').actions();
    
}
// 添加用户页面初始化
function user_manage_init() {
    // 绑定全选事件
    $('button[rel=select]').click(function(){
        $('.role-list input[name^=roles]:checkbox').each(function(){
            this.checked = !this.checked; user_role_checked($(this).attr('rel'));
        });
    });
    // 密码强度验证
    $('#password1').val('').keyup( user_check_pass_strength );
    $('#password2').val('').keyup( user_check_pass_strength );
    // 初始化权限列表
    $('.role-list input[name^=parent]:checkbox').click(function(){
        $('.role-list input[name^=roles][rel=' + this.value + ']:checkbox').attr('checked',this.checked);
    });

    $('.role-list input[name^=roles]:checkbox').click(function(){
        user_role_checked($(this).attr('rel'));
    });
	$('form#usermanage').ajaxSubmit();
}
/**
 * 权限选择
 * 
 * @param rel
 */
function user_role_checked(rel) {
    var length   = $('.role-list input[rel=' + rel + ']').size(),
        cklength = $('.role-list input[rel=' + rel + ']:checked').size(),
        checked  = cklength >= length;
    $('.role-list input.parent-' + rel + ':checkbox').attr('checked',checked);
}
// 我的配置页面初始化
function user_profile_init() {
    // 密码强度验证
    $('#password1').val('').keyup( user_check_pass_strength );
    $('#password2').val('').keyup( user_check_pass_strength );
    
	$('form#profile').ajaxSubmit();
}
// 删除用户
function user_delete(userid){
    LazyCMS.confirm(_('Confirm Delete?'),function(r){
        if (r) {
            LazyCMS.postAction('user.php', {method:'bulk', action:'delete'}, userid);
        }
    });
}
// 验证密码强弱
function user_check_pass_strength() {
    $('#pass-strength-result').check_pass_strength(
            $('#username').val(),
            $('#password1').val(),
            $('#password2').val()
    );
}