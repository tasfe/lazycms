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

LazyCMS.Loading = $('<div class="loading"><img class="os" src="' + LazyCMS.ADMIN + 'images/loading.gif" />Loading...</div>').css({width:'100px',position:'fixed',top:'5px',right:'5px'});

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
        var mode  = LazyCMS.setCookie('menu_setting', 'mode'),
            hover = function() {
                $('.folded li.head').unbind().hover(function(){
                    $('div.sub',this).addClass('open').bgIframe();
                },function(){
                    $('div.sub',this).removeClass('open');
                });
            };
        
        if (mode !== null) {
            $('#wrapper').toggleClass('folded',mode=='true');
            if (mode=='true') hover();
        }

        // 菜单模式切换
        $('li.separator',this).click(function(){
            $('#wrapper').toggleClass('folded'); hover();
            // 保存Cookie
            LazyCMS.setCookie('menu_setting', 'mode', $('#wrapper').hasClass('folded'));
        });
        // 去掉虚线
        $('li.separator a',this).focus(function(){
            this.blur();
        });        
        // 下拉按钮点击的事件
        $('.head .toggle',this).click(function(){
			var head = $(this).parent();
				head.toggleClass('expand',$('.sub',head).slideToggle('fast',function(){
                    LazyCMS.setCookie('menu_setting', 'm' + head.attr('menu_guid'), head.hasClass('expand'));
                }));
        });
        // 记录COOKIE
        $('.head',this).each(function(i){
            var t = $(this); t.attr('menu_guid',i);
            var c = LazyCMS.getCookie('menu_setting','m' + i);
            if (c !== null && !t.hasClass('current')) {
                t.toggleClass('expand',c=='true');
            }
        });
    }
    /**
     * 内容分词
     *
     * @param content
     */
    $.fn.getTerms = function(content) {
        var data = [], _this = this;
        // 处理分词内容
        content = $.trim(content.replace(/\<[^>]+?\>|\r|\n|\t|  /ig,''));
        if (content.length > 1024) content.substr(0,1024);
        if (content!='') {
            $.post(LazyCMS.ADMIN + 'index.php',{method:'terms',content:content},function(r){
                if (r) {
                    _this.val(r.join(','));
                } else {
                    _this.val('');
                }
            },'json');
        } else {
            LazyCMS.alert(_('Please enter the title or content!'), 'alert');
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
            url: LazyCMS.ADMIN + 'index.php?method=publish',
            beforeSend:function(xhr, s){
                LazyCMS.success(xhr,s,true);
            },
            success: function(r){
                if (r) {
                    var tr = $('tr#publish-' + r.pubid,_this);
                    if (tr.is('tr')) {
                        var total      = $('td:eq(2)',tr),
                            complete   = $('td:eq(3)',tr),
                            rate       = $('td:eq(4)',tr),
                            elapsetime = $('td:eq(5)',tr),
                            state      = $('td:eq(6)',tr);


                        $('.inner',rate).css('width',r.rate + 'px');
                        if ($('.text',rate).text().replace('%','') != r.rate)
                            $('.text',rate).text(r.rate + '%');
                        complete.text(r.complete);
                        elapsetime.text(r.elapsetime);
                        // 没有变化，则不做变更
                        if (total.text() != r.total)           total.text(r.total);
                        if (state.html().toLowerCase()
                                != r.state.toLowerCase())      state.html(r.state);
                    }
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