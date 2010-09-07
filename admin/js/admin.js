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

LazyCMS.Loading = $('<div class="loading"><img class="os" src="' + LazyCMS.ADMIN_ROOT + 'images/loading.gif" />Loading...</div>').css({width:'100px',position:'fixed',top:'5px',right:'5px'});

// IE6.0下的动作
if ($.browser.msie && $.browser.version == '6.0') {
	$(document).ready(function(){
		LazyCMS.dialog({
			title:_('Upgrade Tips'),name: 'upgreade_tip', className:'tips', masked:false, float:'rb',
			body: '<strong>' + _('Current:') + '</strong>IE ' + $.browser.version + _('(Recommended to upgrade to IE 7.0 +)') + '<br /><strong>' + _('Recommended:') + '</strong>IE 7.0+, FF 2+, Safari 3.0+'			
		});
		var load_move = function(){
			LazyCMS.Loading.css({
				position:'absolute',
				top:($(window).scrollTop() + 5) + 'px',
				left:($(window).width() - 100 - 20) + 'px'
			});
		}; load_move();

		if (!LazyCMS.COUNT_VAR.Loading) {
			$(window).scroll(load_move).resize(load_move);
			LazyCMS.COUNT_VAR.Loading = true;
		}
	});
}


// 设置全局 AJAX 默认选项
$.ajaxSetup({
    beforeSend: function(s){
        LazyCMS.Loading.css({'z-index':$('*').maxIndex() + 1}).appendTo('body');
    },
    error:function(xhr,status,error) {
        LazyCMS.dialog({
            title:_('System Error') + ':' + status, styles:{ overflow:'auto', width:'700px',height:'350px' }, body:error
        });
        LazyCMS.Loading.remove();
    },
    complete: function(){
        LazyCMS.Loading.remove();
    }
});

(function ($) {
	// 退出登录
	$.fn.logout = function(){
		var url = this.attr('href');
		return LazyCMS.confirm(_('Confirm Logout?'),function(r){
			if (r) {
				LazyCMS.redirect(url);
			}
		});
	}
	// 初始化菜单
    $.fn.init_menu = function(){
        // 下拉按钮点击的事件
        $('.head .toggle',this).click(function(){
			var head = $(this).parent();
				head.toggleClass('expand',$('.submenu',head).slideToggle('fast'));
        });
		// 还缺少记录COOKIE
    }
})(jQuery);

/**
 * 内容分词
 * 
 * @param content
 * @param callback
 */
getTerms = function(content,callback) {
    var data = [];
    if (content!='') {
        $.post(LazyCMS.ADMIN_ROOT + 'index.php',{action:'getTerms',content:content},function(r){
            data = LazyCMS.ajaxResult(r);
            if (callback) callback(data);
        },'json');
    } else {
        if (callback) callback(data);
    }
}