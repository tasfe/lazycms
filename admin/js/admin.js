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

LazyCMS.Loading = $('<div class="loading"><img class="os" src="' + LazyCMS.ADMIN_ROOT + 'images/loading.gif" />Loading...</div>').css({width:'100px',position:'fixed',top:'5px',right:'5px'});

// IE6.0下的动作
if ($.browser.msie && $.browser.version == '6.0') {
	$(document).ready(function(){
		LazyCMS.dialog({
			title:'升级提示',
			body: '<strong>当前版本：</strong>IE ' + $.browser.version + '（推荐升级至IE 7.0+）<br /><strong>需求版本：</strong>IE 7.0+, FF 2+, Safari 3.0+',
			name: 'upgreade_tip',
			masked:false, way:'rb',
			styles:{width:'250px'}
		});
		var load_move = function(){
			LazyCMS.Loading.css({
				position:'absolute',
				top:($(window).scrollTop() + 5) + 'px',
				left:($(window).width() - 100 - 20) + 'px'
			});
		}; load_move();
		$(window).scroll(function(){load_move()}).resize(function(){load_move()});
	});
}


// 设置全局 AJAX 默认选项
$.ajaxSetup({
    cache: false,
    beforeSend: function(s){
        LazyCMS.Loading.css({'z-index':$('*').maxIndex() + 1}).appendTo('body');
    },
    complete: function(){
        LazyCMS.Loading.remove();
    }
});

(function ($) {
	// 退出登录
	$.fn.logout = function(){
		var url = this.attr('href');
		return LazyCMS.confirm(_('common.confirm.logout'),function(r){
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