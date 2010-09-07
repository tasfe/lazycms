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
function user_list_init() {
    var form = $('#userlist');
    // 绑定全选事件
    $('input[name=select]',form).click(function(){
        $('input[name^=list]:checkbox,input[name=select]:checkbox',form).attr('checked',this.checked);
    });
    $('tbody tr',form).hover(function(){
        $('td',this).css({'background-color':'#FFFFCC'});
        $('.row-actions',this).css({'visibility': 'visible'});
    },function(){
        $('td',this).css({'background-color':'#FFFFFF'});
        $('.row-actions',this).css({'visibility': 'hidden'});
    });
	// 绑定删除事件
	$('span.delete a',form).click(function(){
		var url = this.href;
		LazyCMS.confirm(_('Confirm Delete?'),function(r){
			if (r) {
				$.getJSON(url,function(data){
					LazyCMS.ajaxResult(data);
				});
			}
		});
		
		return false;
	});
	// 绑定提交事件
	form.actions(function(data){
        LazyCMS.ajaxResult(data);
    });
    
}
// 添加用户页面初始化
function user_manage_init() {
    // 初始化权限列表
    $('.role-list input[name^=parent]:checkbox').click(function(){
        $('.role-list input[name^=roles][rel=' + this.value + ']:checkbox').attr('checked',this.checked);
    });
    var rel,length,cklength,checked;
    $('.role-list input[name^=roles]:checkbox').click(function(){
        rel      = $(this).attr('rel');
        length   = $('.role-list input[rel=' + rel + ']').size();
        cklength = $('.role-list input[rel=' + rel + ']:checked').size();
        checked  = cklength >= length ? true : false;
        $('.role-list input.parent-' + rel + ':checkbox').attr('checked',checked);
    });
	$('form#usermanage').ajaxSubmit(function(data){
        LazyCMS.ajaxResult(data);
    });
}
// 我的配置页面初始化
function user_profile_init() {
	$('form#profile').ajaxSubmit(function(data){
        LazyCMS.ajaxResult(data);
    });
}
