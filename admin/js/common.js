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

LazyCMS.Loading     = $('<div class="loading"><img class="os" src="' + LazyCMS.ADMIN + 'images/loading.gif" />Loading...</div>').css({width:'100px',position:'fixed',top:'5px',right:'5px'});

LazyCMS.UpLinkUrl   = LazyCMS.ADMIN + 'media.php?method=upload&type=file';
LazyCMS.UpLinkExt   = 'zip,rar,txt';
LazyCMS.UpImgUrl    = LazyCMS.ADMIN + 'media.php?method=upload&type=image';
LazyCMS.UpImgExt    = 'jpg,jpeg,gif,png';
LazyCMS.UpVideoUrl  = LazyCMS.ADMIN + 'media.php?method=upload&type=video';
LazyCMS.UpVideoExt  = 'flv,mp4';
LazyCMS.UpFlashUrl  = LazyCMS.ADMIN + 'media.php?method=upload&type=flash';
LazyCMS.UpFlashExt  = 'swf';

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
     * @param title
     * @param content
     */
    $.fn.getTerms = function(title, content) {
        var data = [], _this = this;
        // 处理分词内容
        content = $.trim(content.replace(/\<[^>]+?\>|\r|\n|\t|  /ig,''));
        if (content.length > 512) content.substr(0,512);
        if (title != '' || content != '') {
            $.post(LazyCMS.ADMIN + 'index.php',{method:'terms', title:title, content:content},function(r){
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
    /**
     * 文件浏览器
     */
    $.fn.explorer = function() {
        var input = this;
        $.post(LazyCMS.ADMIN + 'media.php?method=explorer', function(r) {
            var callback = arguments.callee;
            LazyCMS.dialog({
                title:_('Explorer'), name:'Explorer', styles:{ overflow:'auto', width:'600px',height:'410px' }, body: r
            }, function() {
                var $this = this, form = $('form', $this).ajaxSubmit(callback);
                // select change
                $('select[rel=submit]',$this).change(function(){
                    form.submit();
                });
                // 删除原先添加的按钮
                $('button', $this).click(function(){
                    $('input[rel=action]', $this).remove();
                });
                // 刷新
                $('button[rel=submit]', $this).click(function(){
                    form.submit();
                });
                // 删除
                $('button[rel=delete]', $this).click(function(){
                    LazyCMS.confirm(_('Confirm Delete?'), function(r) {
                        if (r) {
                            form.append('<input type="hidden" rel="action" name="action" value="delete" />'); form.submit();
                        }
                    });
                });
                // 上传
                $('input[name=filedata]', $this).change(function(){
                    var file = this;
                    upcheck(file.files ? file.files : [{fileName: file.value}], function(toUrl) {
                        $this.startUpload(file.files ? file.files : file, toUrl, '*', function() {
                            file.value = null; form.submit();
                        });
                    });
                });
                // 插入
                $('a[insert=true]', $this).click(function(){
                    var data = $.parseJSON($(this).parents('.unit').find('textarea').val());
                    if (input[0].pasteHTML) {
                        var i, html, type, types = ['Link','Img','Flash','Video'];
                        for (i in types) {
                            if ($.inArray(data.suffix, LazyCMS['Up' + types[i] + 'Ext'].split(',')) != -1) {
                                type = types[i]; break;
                            }
                        }
                        switch (type) {
                            case 'Img':
                                html = '<img src="' + data['url'] + '" width="' + data['width'] + '" height="' + data['height'] + '" alt="' + data['name'] + '" />';
                                break;
                            case 'Flash':
                                html = '<embed type="application/x-shockwave-flash" classid="clsid:d27cdb6e-ae6d-11cf-96b8-4445535400000" src="' + data['url'] + '" wmode="opaque" quality="high" menu="false" play="true" loop="true" allowfullscreen="true" height="' + data['height'] + '" width="' + data['width'] + '" />';
                                break;
                            case 'Video':
                                html = '<embed width="480" height="400" lazysrc="' + data['url'] + '" flashvars="file=' + data['url'] + '" src="'+LazyCMS.ROOT+'common/editor/plugins/mediaplayer/player.swf" quality="high" allowScriptAccess="always" allowFullScreen="true" type="application/x-shockwave-flash" lazytype="Flv" />';
                                break;
                            default: case 'Link':
                                html = '<a href="' + data['url'] + '">' + data['name'] + '</a>';
                                break;
                        }
                        input[0].pasteHTML(html);
                    } else {
                        input.val(data['url']);
                    }
                    return false;
                });
                // 绑定全选事件
                $('input[name=select]',form).click(function(){
                    $('input[name^=list]:checkbox,input[name=select]:checkbox',form).attr('checked',this.checked);
                });
                // 表格背景变色
                $('tbody tr',form).hover(function(){
                    $('td',this).css({'background-color':'#FFFFCC'});
                },function(){
                    $('td',this).css({'background-color':'#FFFFFF'});
                });
                // 图片actions
                $('.icons li',form).hover(function(){
                    $('div.mask,div.actions',this).css({'visibility': 'visible'});
                },function(){
                    $('div.mask,div.actions',this).css({'visibility': 'hidden'});
                });
                // 显示大图
                $('.icons a[target]', $this).click(function(){
                    $('.loading', $this).remove();
                    var src     = this.href, scale, width, height, max_w = $(window).width()*0.9, max_h = $(window).height()*0.9,
                        loading = $('<div class="loading"><img src="' + LazyCMS.ROOT + 'common/images/loading.gif" class="os" alt="Loading..." /></div>').appendTo($(this).parents('li'));
                    var image   = new Image();
                        image.onload = function(){
                            loading.remove();
                            scale  = Math.min(max_w / image.width, max_h / image.height);
                            width  = scale < 1 ? parseInt(image.width * scale) : image.width;
                            height = scale < 1 ? parseInt(image.height * scale) : image.height;
                            LazyCMS.dialog({
                                title:_('Picture Viewer'), name:'PicViewer', styles:{ overflow:'auto', 'min-width':200, 'min-height':150, width:width+10,height:height+33 },
                                body: [
                                    '<div class="wrapper pic-viewer" rel="close">',
                                        '<img src="' + src + '" width="' + width + '" height="' + height + '" rel="close" />',
                                        scale < 1 ? '<a href="' + src + '" target="_blank"><img src="' + LazyCMS.ROOT + 'common/images/resize.png" width="45" height="46" alt="' + _('Open a new window') + '" /></a>' : '',
                                    '</div>'
                                ].join('')
                            });
                        };
                        image.src = src;
                    return false;
                });
                // 翻页
                $('.pages a',$this).click(function(){
                    $.post(this.href, form.serializeArray(), callback);
                    return false;
                });
                // 拖放上传
                $this.unbind().bind('dragenter dragover', function(ev){ return false; }).bind('drop',function(ev){
                    var dataTransfer = ev.originalEvent.dataTransfer,fileList;
                    if (dataTransfer && (fileList = dataTransfer.files) && fileList.length > 0) {
                        upcheck(fileList, function(toUrl) {
                            $this.startUpload(fileList, toUrl, '*', function() {
                                form.submit();
                            });
                        });
                    }
                    return false;
                });
                // 上传之前检查
                function upcheck(fileList, callback) {
                    var i,cmd,arrCmd = ['Link','Img','Flash','Video'],arrExt = [],strExt;
                    for (i in arrCmd) {
                        cmd = arrCmd[i];
                        if (LazyCMS['Up' + cmd + 'Url'] && LazyCMS['Up' + cmd + 'Ext'] && LazyCMS['Up' + cmd + 'Url'].match(/^[^!].*/i))
                            arrExt.push(cmd + ':,' + LazyCMS['Up' + cmd + 'Ext']); //允许上传
                    }
                    //禁止上传
                    if (arrExt.length === 0) return false;
                    else strExt = arrExt.join(',');

                    var match,fileExt,cmd = null;
                    for (i = 0; i < fileList.length; i++) {
                        fileExt = fileList[i].fileName.replace(/.+\./, '');
                        if (match = strExt.match(new RegExp('(\\w+):[^:]*,' + fileExt + '(?:,|$)', 'i'))) {
                            if (!cmd) cmd = match[1];
                            else if (cmd !== match[1]) cmd = 2;
                        }
                        else cmd = 1;
                    }
                    if (cmd === 1) alert(_('Upload file extension required for this: ') + strExt.replace(/\w+:,/g, ''));
                    else if (cmd === 2) alert(_('You can only drag and drop the same type of file.'));
                    else if (cmd) {
                        if ($.isFunction(callback)) callback(LazyCMS['Up' + cmd + 'Url']);
                    }
                    return true;
                }
            });
        });
    }
    /**
     * 开始上传
     *
     * @param fileList
     * @param toUrl
     * @param limitExt
     * @param callback
     */
    $.fn.startUpload = function(fileList, toUrl, limitExt, callback) {
        // TODO 增加上传进度条
        var multiMsg = [];
        // HTML4 UPLOAD
        if (fileList.type && fileList.type.toLowerCase() == 'file') {
            var uid    = new Date().getTime(), guid = 'jUploadFrame' + uid;
            var iframe = $('<iframe name="' + guid + '" />').appendTo('body');
            var jForm  = $('<form action="' + toUrl + '" target="' + guid + '" method="post" enctype="multipart/form-data" class="hide"></form>').appendTo('body');
            var jOldFile = $(fileList), jNewFile = jOldFile.clone().attr('disabled','true');
		        jOldFile.before(jNewFile).appendTo(jForm); jForm.submit();
            // iframe onload
            iframe.load(function(){
                upcallback(iframe.contents().text(), true);
                // upload complete remove iframe and form
                iframe.remove(); jForm.remove();
            });
        }
        // HTML5 UPLOAD
        else {
            var i = 0, xhr, len = fileList.length;
            // 检查扩展名
            for (var j = 0; j < len; j++) if (!check_ext(fileList[j].fileName, limitExt)) return ;
            // 上传文件
            function upload_execute(text) {
                 var func = arguments.callee;
                // 当前文件上传完毕
                if ((!text || (text && upcallback(text, i === len) === true)) && i < len) {
                    xhr = new XMLHttpRequest();
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState === 4) func(xhr.responseText);
                    };
                    xhr.open('POST', toUrl);
                    xhr.setRequestHeader('Content-Type', fileList[i].type);
                    xhr.setRequestHeader('Content-Disposition', 'attachment; name="filedata"; filename="' + fileList[i].fileName + '"');
                    if (xhr.sendAsBinary)
                        xhr.sendAsBinary(fileList[i].getAsBinary());
                    else
                        xhr.send(fileList[i]);
                    i++;
                }
            }
            // 执行上传
            upload_execute();
        }
        // 上传完成的回调函数
        function upcallback(text, finish) {
            var data, result = false; try { data = $.parseJSON(text); } catch (e) {}

            if (data.err === undefined || data.msg === undefined)
                alert(toUrl + _(' upload interface error!') + '\r\n\r\n' + _('return error:') + ' \r\n\r\n' + text);
            else if(data.err)
                alert(data.err);
            else {
                multiMsg.push(data.msg);
                result = true;
            }
            if (finish && $.isFunction(callback)) callback(multiMsg);
            return result;
        }
        // 检查扩展名
        function check_ext(filename, limitExt) {
            if (limitExt === '*' || filename.match(new RegExp('\.(' + limitExt.replace(/,/g, '|') + ')$', 'i')))
                return true;
            else {
                alert(_('Upload file extension required for this: ') + limitExt);
                return false;
            }
        }
    }
})(jQuery);

// 执行生成进度
function common_publish() {
    $('form#publishlist').publish();
}