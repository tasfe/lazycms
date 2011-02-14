// 接收传参
var scripts = document.getElementsByTagName("script"); eval(scripts[ scripts.length - 1 ].innerHTML);

jQuery && (function ($) {
    LazyCMS.Loading = $('<div class="loading"><img class="os" src="' + LazyCMS.ROOT + 'common/images/loading.gif" />Loading...</div>').css({width:'100px',position:'fixed',top:'5px',right:'5px'});

    LazyCMS.L10n = {
        'Alert': '系统警告',
        'Submit': '确定'
    };

    // 设置全局 AJAX 默认选项
    $.ajaxSetup({
        beforeSend: LazyCMS.success,
        error:function(xhr,status,error) {
            if (xhr && xhr.getResponseHeader('Date')) {
                var title = $.parseJSON(xhr.getResponseHeader('X-Dialog-title'));
                    title = title || _('System Error');
                LazyCMS.dialog({
                    title:title, styles:{ overflow:'auto', width:'700px',height:'350px' }, body: xhr.responseText
                });
            }
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

    /**
     * 初始化菜单
     */
    $.fn.init_menu = function(){
        $('li a',this).removeClass('active');
        $('li a',this).each(function(){
            if (CMS.URI.Url.substr(0,this.href.length) == this.href) {
                $(this).addClass('active');
            }
        });
        if ($('li a',this).hasClass('active') === false) $('li a:eq(0)',this).addClass('active');
        return this;
    }
    /**
     * 更新
     */
    $.fn.updates = function(){
        var _this = this,updates = $('<ul></ul>');
        $.getJSON(CMS.ROOT + 'common/gateway.php?func=lazycms_updates',function(r){
            $('.load',_this).remove();
            $.each(r.entrys,function(i,entry){
                updates.append([
                    '<li>',
                        '<p><a href="' + entry.link + '" target="_blank" class="revision">r' + entry.id + '</a>' + entry.updated + '</p>',
                        '<p class="comments">' + entry.content + '</p>',
                    '</li>'
                ].join('\n'));
            });
            updates.append('<li><p class="more"><a href="' + r.more + '" target="_blank">---- More&gt;&gt; </a></p></li>');
            _this.append(updates);
        });
        return this;
    }
})(jQuery);