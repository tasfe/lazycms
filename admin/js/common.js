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

// 设置全局 AJAX 默认选项
$.ajaxSetup({
    beforeSend: LazyCMS.success,
    error:function(xhr,status,error) {
        var title = $.parseJSON(xhr.getResponseHeader('X-Dialog-title'));
            title = title || _('System Error');
        LazyCMS.dialog({
            title:title, styles:{ overflow:'auto', width:'700px',height:'350px' }, body: xhr.responseText
        });
        LazyCMS.Loading.remove();
    },
    complete: function(){
        LazyCMS.Loading.remove();
    }
});

// 兼容IE6.0
if ($.browser.msie && $.browser.version == '6.0') {
    $(document).ready(function(){
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
    /**
     * 内容分词
     *
     * @param content
     */
    $.fn.getTerms = function(content) {
        var data = [], _this = this;
        if (content!='') {
            $.post(LazyCMS.ADMIN_ROOT + 'index.php',{method:'terms',content:content},function(r){
                if (r) {
                    _this.val(r.join(','));
                } else {
                    _this.val('');
                }
            },'json');
        } else {
            _this.val('');
        }
        return this;
    }
    /**
     * 发布文章进度检查
     */
    $.fn.publish = function() {
        var _this = this, form_exist = this.is('form');
        $.ajax({
            cache: false, type:'GET', dataType:'json',
            url: LazyCMS.ADMIN_ROOT + 'index.php?method=publish',
            beforeSend:function(xhr, s){
                LazyCMS.success(xhr,s,true);
            },
            success: function(r){
                if (r) {
                    var tr = $('tr#publish-' + r.pubid,_this);
                        $('td:eq(2)',tr).text(r.total);
                        $('td:eq(3)',tr).text(r.complete);
                        $('td:eq(4)',tr).text(r.begintime);
                        $('td:eq(5)',tr).text(r.elapsetime);
                        $('td:eq(6)',tr).text(r.endtime);
                        $('td:eq(7)',tr).html(r.state);
                    _this.publish();
                }
            }
        });
        return this;
    }
})(jQuery);


// 执行生成进度
function common_publish() {
    $('form#publishlist').publish();
}