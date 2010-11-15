// 网站根目录
var ROOT = window.ROOT = '/';
// URI
var URI = window.URI = {};
    URI.Host = (('https:' == self.location.protocol) ? 'https://'+self.location.hostname : 'http://'+self.location.hostname);
    URI.Path = self.location.href.replace(/\?(.*)/,'').replace(URI.Host,'');
    URI.File = URI.Path.split('/').pop();
    URI.Path = URI.Path.substr(0,URI.Path.lastIndexOf('/')+1);
    URI.Url  = URI.Host + URI.Path + URI.File;
// 接收传参
var scripts = document.getElementsByTagName("script"); eval(scripts[ scripts.length - 1 ].innerHTML);

(function ($) {
    /**
     * 初始化菜单
     */
    $.fn.init_menu = function(){
        $('li a',this).removeClass('active');
        $('li a',this).each(function(){
            if (URI.Url.substr(0,this.href.length) == this.href) {
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
        $.getJSON(ROOT + 'common/gateway.php?func=lazycms_updates',function(r){
            $('.loading',_this).remove();
            $.each(r.entrys,function(i,entry){
                updates.append([
                    '<li>',
                        '<p><a href="' + entry.link + '" target="_blank" class="revision">r' + entry.id + '</a>' + entry.updated + '</p>',
                        '<p class="comments">' + entry.title + '</p>',
                    '</li>'
                ].join('\n'));
            });
            updates.append('<li><p class="more"><a href="' + r.more + '" target="_blank">---- More&gt;&gt; </a></p></li>');
            _this.append(updates);
        });
        return this;
    }
})(jQuery);