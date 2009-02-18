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
function common(){ return $("script[src*=js/jquery.js]").attr("src").replace(/(\/js\/jquery\.js\?(.*))/i,'');}
function lock(p1){ return p1 ? icon('a3') : icon('a4'); }
function cklist(p1){ return '<input name="list['+p1+']" id="list_'+p1+'" type="checkbox" value="'+p1+'"/>'; }
function icon(n,a){
    var IMG = '<img class="os ' + n +'" src="' + common() + '/images/white.gif" />';
    var HREF = '<a href="' + a + '">' + IMG + '</a>';
    if (typeof a == "undefined") { return IMG; } else { return HREF; }
}
function getURI(){
    var e = {};
        e.Host = (('https:' == self.location.protocol) ? 'https://'+self.location.hostname : 'http://'+self.location.hostname);
        e.Path = self.location.href.replace(/\?(.*)/,'').replace(e.Host,'');
        e.File = e.Path.split('/').pop();
        e.Path = e.Path.substr(0,e.Path.lastIndexOf('/')+1);
        e.Url  = e.Host + e.Path + e.File;
        return e;
}
// loading加载条
window.loading = $('<div class="loading"><img class="os" src="' + common() + '/images/loading.gif" />Loading...</div>').css({width:'100px'});
/**
 * 动态加载脚本
 *
 * @param string    p   文件名
 * @param function  c   回调函数
 */
function LoadScript(p,c){
    document.write('<scr' + 'ipt type="text/javascript" src="' + common() + '/js/' + p + '.js"><\/scr' + 'ipt>'); if ($.isFunction(c)) { $.getScript(u,c); }
}
/*
 * LazyCMS JS library for jQuery
 * http://www.lazycms.net
 *
 * Copyright (C) 2007-2008 LazyCMS.net All rights reserved.
 * LazyCMS is free software. This version use Apache License 2.0
 * See LICENSE.txt for copyright notices and details.
 */
(function ($) {
	// 缩放图片
	$.fn.bbimg = function(w,h){
		var scale = Math.min(w/this.width(),h/this.height());
		if (this.width() > w) {
			this.width(this.width()*scale);
		}
		if (this.height() > h) {
			this.height(this.height()*scale);
		}
		return this;
	}
    /**
     * 全选/反选
     *
     * @param string    p1   true:全选,false:
     */
    $.fn.checkALL = function(p1){ 
        var p1 = p1||false;
        $('input:checkbox',this).each(function(){ 
            if (p1) { 
                this.checked = true;
            } else { 
                this.checked = !this.checked; 
            }
        }); 
    }
    // 固定公用CSS
    $.setStyle = function(){
        var u = common();
        var C = "abcdefgh";//ijklmnopqrstuvwxyz
        var style = '<style type="text/css">\n';
            style += 'img.os{ width:16px; height:16px; vertical-align:middle; background-image:url(' + u + '/images/icons.png); padding:0px; margin:2px; }\n';
            for(var i=0;i<C.length;i++){
                for(var j=1;j<=9;j++){
                    style += 'img.' + C.charAt(i) + j + '{background-position:-' + (16*i+16) + 'px -' + 16*(j) + 'px;}\n';
                }
            }
            /* loading图片 */
            style += '.loading{ border:solid 1px #666666; background:#FFFFDF; padding:1px; padding-right:10px;}\n';
            /* 错误信息 */
            style += '.error{ border:solid 1px #FF0000 !important; background:url(' + u + '/images/invalid-line.gif) repeat-x left bottom !important;}\n';
            /* 按钮样式 */
            style += 'button{ letter-spacing:1px; padding:2px 4px; border:1px solid #c6d9e7; height:23px; color:#333333; background:url(' + u + '/images/buttons-bg.png) repeat-x;line-height:100%; vertical-align:middle; margin-right:6px; }\n';
            style += 'textarea{ font-size:12px; }\n';
            /* 宽度定义 */
            style += '.w0{ width: 100%; }\n';
            style += '.w1 { width: 5%; }\n';
            style += '.w2 { width: 10%; }\n';
            style += '.w3 { width: 15%; }\n';
            style += '.w4 { width: 20%; }\n';
            style += '.w5 { width: 50%; }\n';
            for(var i=1;i<=16;i++){
                style += '.w' + (i*50) + ' { width: ' + (i*50) + 'px; }\n';
            }
            /* 表单长度设置 */
            style += '.in{ border:solid 1px #7F9DB9; padding:2px; }\n';
            /* 对齐样式 */
            style += '.tl{ text-align:left; }\n';
            style += '.tr{ text-align:right; }\n';
            style += '.tc{ text-align:center; }\n';
            /* 左右浮动 */
            style += '.fl{float:left; display:block;}\n';
            style += '.fr{float:right; display:block;}\n';
            /* 显示隐藏 */
            style += '.hide{display:none;}\n';
            style += '.show{display:block;}\n';
            style += '</style>';
            document.write(style);
    }
    // 取得最大的zIndex
    $.fn.getMaxzIndex = function(){
		var max = 0;
		this.each(function(){
			max = Math.max(max,this.style.zIndex);
		});
		return max;
    }
    // 创建遮罩层
    $.fn.mask = function(style){
        var s = this.selector==''?$('body'):this;
        var m = $('<div class="mask" rel="mask"></div>');
        var z = $('.mask,.dialogUI').getMaxzIndex();
        // 默认设置
        style = $.extend({
            width:'100%',//Math.max(s.width(),$(document).width()) + 'px',
            height:Math.max(s.height(),$(document).height()) + 'px',
            left:s.position().left + 'px',
            top:s.position().top + 'px',
            opacity:0.4,
            background:'#000000',
            position:'absolute',
            'z-index': (z + 1) * 200
        }, style||{});
        // 设置透明度
        $.extend(style,{'filter':'alpha(opacity=' + (100 * style.opacity) + ')', '-moz-opacity':style.opacity});
        // 设置样式
        m.css(style);
        // 添加遮罩层
        m.appendTo(s);
		// 窗口改变大小
		$(window).resize(function(){
			m.css({ height:Math.max(s.height(),$(document).height()) + 'px' });
		});
        return this;
    }
    /*
    */
	$.dialogUI = function(opts,callback){
		return $('body').dialogUI(opts,callback);
	};
    $.fn.dialogUI = function(opts,callback){
        var s = this;
        // 默认设置
        opts = $.extend({
            title:'',
            body:'',
            style:{},
            name:null,
			mask:true,
            close:function(){
				dialog.remove(); $('.dialogUI[name=help]').remove();
                if ($('.dialogUI',s).size()==0) {
                    $('[rel=mask]',s).remove();
                } else {
                    $('[rel=mask]',s).css('z-index',$('.mask,.dialogUI').getMaxzIndex() - 1);
                }
            },
            buttons:[]
        }, opts||{});

        // 定义弹出层对象
        var dialog = $('<div class="dialogUI" style="display:none;"><div class="dialogBox"><div class="head"><strong>Loading...</strong></div><div class="body">Loading...</div></div></div>').css({position:'absolute'});
            opts.name==null?null:dialog.attr('name',opts.name);

        var target = $('.dialogUI[name=' + opts.name + ']',s);
            dialog = target.size()==0 ? dialog.appendTo(s) : target;
        
        // 添加遮罩层
		if (opts.mask) {		
			if ($('[rel=mask]',s).size()==0) {
				$(this).mask();
			} else {
				$('[rel=mask]',s).css('z-index',$('.mask,.dialogUI').getMaxzIndex());
			}
		}
		
        // 添加关闭按钮
        $('.dialogBox > .head > a[rel=close]',dialog).remove();
        if ($.isFunction(opts.close)) {
            var close = $('<a href="javascript:;" rel="close"></a>').click(function(){
                opts.close.call(dialog);
                return false;
            });
            close.insertAfter($('.dialogBox > .head > strong',dialog));
        }
		
        // 重新调整CSS
        var style = $.extend({overflow:'auto','z-index':$('.mask,.dialogUI').getMaxzIndex() + 1,background:'#FFFFFF',height:'auto'},opts.style); dialog.css(style);

		// 设置标题
        $('.dialogBox > .head > strong',dialog).text(opts.title);
        // 设置内容
        $('.dialogBox > .body',dialog).html(opts.body);
		if (style.overflow=='auto') {
			$('.dialogBox > .body',dialog).css({height:parseInt(dialog.height() - 35) + 'px'});
		}
		// 设置CSS
		var CSS = {
			left:parseInt($('[rel=mask]',s).width()/2 - dialog.width()/2),
			top:parseInt(Math.max($('[rel=mask]',s).height(),$(document).height())/2.5 - dialog.height()/2)
		};
		CSS.top = CSS.top<=0?10:CSS.top;
		// 窗口改变大小，调整位置
		$(window).resize(function(){
			dialog.css({
				top:(typeof(style.top)=='undefined'?CSS.top:style.top) + 'px',
				left:(typeof(style.left)=='undefined'?CSS.left:style.left) + 'px'
			});
		});
		// 设置位置
        dialog.css({ overflow:'',
            top:(typeof(style.top)=='undefined'?CSS.top:style.top) + 'px',
            left:(typeof(style.left)=='undefined'?CSS.left:style.left) + 'px'
        });

        // 显示弹出层
        dialog.show();

        // 添加按钮
        $('.dialogBox > .buttons',dialog).remove();
        if (opts.buttons.length > 0) {
            $('.dialogBox > .body',dialog).after('<div class="buttons"></div>');
            for (var i=0;i<opts.buttons.length;i++) {
                var button = $('<button type="button">' + opts.buttons[i].text + '</button>');
                    (function(i){
                        button.click(function(){
                            opts.buttons[i].handler.call(opts);
                            return false;
                        });
                    })(i);
                    button.appendTo($('.dialogBox > .buttons',dialog));
					typeof(opts.buttons[i].type) != 'undefined'?button.attr('type',opts.buttons[i].type):null;
					typeof(opts.buttons[i].focus) != 'undefined'?
						opts.buttons[i].focus?button.focus():null:
						null;
            }
        }
		if ($.isFunction(callback)) {
			callback.call(opts,dialog);
		}
        return this;
    }
    // alert
    $.alert = function(message,callback,type){
		type = type||'ALERT';
		var position;
        switch (type) {
            case 'SUCCESS':
                position = 'background-position:0px 0px;';
                break;
            case 'ERROR':
                position = 'background-position:0px -40px;';
                break;
            default:
                position = 'background-position:0px -80px;';
                break;
        }
		$.dialogUI({
            name:'alert', title:$.t('alert'),style:{width:'400px'},
            body:'<div class="icon" style="' + position + '"></div><div class="content"><h3>' + message + '</h3></div>',
            buttons:[{
                focus:true,
                text:$.t('submit'),
                handler:function(){
					if ($.isFunction(callback)) {callback();}
					this.close(); return false;
                }
            }]
        });
    }
    // confirm
    $.confirm = function(message,callback){
		callback = callback||function(){};
        $.dialogUI({
            name:'confirm', title:$.t('confirm'),style:{width:'400px'},
            body:'<div class="icon" style="background-position:0px -80px;"></div><div class="content"><h3>' + message + '</h3></div>',
            buttons:[{
                focus:true,
                text:$.t('submit'),
                handler:function(){
                    callback(true); this.close();
                }
            },{
                text:$.t('cancel'),
                handler:function(){
                    callback(false); this.close();
                }
            }]
        });
        return false;
    }
    // 跳转
    $.redirect = function(url){
        if (typeof url != 'undefined' && url != '') {
            self.location.href = url;
        }
    }
    
    // ajax按钮
    $.fn.ajaxButton = function(p,u){
        var R;
        var f = this;
        var u = u||f.attr('action');
        if (p!='' || p!='-') {
            p = escape(p);
        }

        switch (p) {
            case 'delete':
                $.confirm(lazy_delete,function(r){
                    r?ajaxPost(p):false;
                });
                break;
            case 'clear':
                $.confirm(lazy_clear,function(r){
                    r?ajaxPost(p):false;
                });
                break;
            default:
                ajaxPost(p);
                break;
        
        }

        function ajaxPost(submit){
            var lists = '';
            $('input:checkbox',f).each(function(){
                if(this.checked){
                    if(lists==''){
                        lists = this.value;
                    }else{
                        lists+= ',' + this.value;
                    }
                }
            });
            $.post(u,{'submit':submit,'lists':lists},function(data){
				$.result(data);
            });
        }
    }
    /**
     * ajax Submit
     */
    $.fn.ajaxSubmit = function(callback){
        return this.each(function(){
            var This = $(this);
                This.unbind('submit').submit(function(){
                    // 先释放绑定的所有事件，清除错误样式
                    $('[rel=editerror]').remove();$('[error]').unbind().removeAttr('error').removeClass('error');
                    var button = $('button[type=submit]',this);
                        button.attr('disabled',true);
                    // 取得 action 地址
                    var url = This.attr('action'); if (url==''||typeof url=='undefined') { url = self.location.href; }
                    // 设置编辑器内容
                    if (typeof(tinyMCE) != 'undefined') {
                        $(tinyMCE.editors).each(function(){
                            $('#' + this.content.id).val(this.content.getContent());
                        });
                    }
                    // ajax submit
                    $.ajax({
                        cache: false, url: url,
                        type: This.attr('method').toUpperCase(),
                        data: This.serializeArray(),
                        beforeSend: function(s){
                            s.setRequestHeader("AJAX_SUBMIT",true);
                            window.loading.css({position:'absolute',top:'5px',right:'5px'}).appendTo('body');
                        },
                        success: function(data){
                            if (JSON = $.result(data)) {
								if ($.isFunction(callback)) { callback(JSON); }
                            }
                        },
                        complete: function(){
                            button.attr('disabled',false);
                            // 重载了此方法，所以必须要删除loading条
                            window.loading.remove();
                        }
                    });
                    return false;
                });
        });
    }
    // 错误处理
    $.result = function(data){
        var JSON = {};
        if (JSON = $.parseJSON(data)) {
            switch (JSON.CODE) {
                case 'VALIDATE': // 验证提示
                    var len = JSON.DATA.length;
                    for (var i=0;i<len;i++) {
                        if (typeof tinyMCE != 'undefined') {
                            if (typeof tinyMCE.get(JSON.DATA[i].id) != 'undefined') {
                                $('#' + JSON.DATA[i].id + '_ifr')
                                    .unbind().attr('error',JSON.DATA[i].text)
                                    .after('<div rel="editerror" style="width:100%;height:3px; background:#FFFFFF url(' + common() + '/images/invalid-line.gif) repeat-x left bottom !important;">&nbsp;</div>');
                            } else {
                                $('[name=' + JSON.DATA[i].id + ']').unbind().attr('error',JSON.DATA[i].text).addClass('error');
                            }
                        } else {
                            $('[name=' + JSON.DATA[i].id + ']').unbind().attr('error',JSON.DATA[i].text).addClass('error');
                        }
                    }
                    $('[error]').jTips();
                    break;
                case 'SUCCESS': case 'ERROR': case 'ALERT': // 几种提示
                    $.alert(JSON.DATA.MESSAGE,function(){
                        $.redirect(JSON.DATA.URL);
                    },JSON.CODE);
                    break;
                case 'REDIRECT':// 跳转
                    $.redirect(JSON.DATA.URL);
                    break;
				default:
					return JSON.DATA;
					break;
            }
        } else {
            // 格式不符合，则出现错误
			$.dialogUI({name:'error',style:{width:'750px',height:'400px',overflow:'auto'}, title:$.t('error'), body:data});
        }
		return false;
    }
    /**
     * 气泡提示
     */
    $.fn.jTips = function(){
        return this.each(function(){
            $(this).hover(function(el){
                var jTip = $('body').append('<div class="jTip"><div class="jTip-body"></div><div class="jTip-foot"></div></div>').find('.jTip');
                var jHeight = jTip.height();
                var jObject = $(this);
                var jOffset = {left:0,top:document.documentElement.scrollTop};
                if ($(this).is('iframe')) {
                    jOffset = $(this).offset();
                    jObject = $(this).contents();
                }
				jTip.css({'top':((el.clientY + jOffset.top) - jHeight - 20 ) + 'px','left':(el.clientX + jOffset.left + 10) + 'px','z-index':$('.mask,.dialogUI').getMaxzIndex() + 1});
                jObject.mousemove(function(e){
                    jTip.css({'top':((e.clientY + jOffset.top) - jHeight - 20 ) + 'px','left':(e.clientX + jOffset.left + 10) + 'px','z-index':$('.mask,.dialogUI').getMaxzIndex() + 1});
                });
                jTip.fadeIn('fast').find('.jTip-body').html($(this).attr('error'));
            },function(){
                $('.jTip').remove();
            });
        });
    }
    // 获取分词
    $.fn.getKeywords = function(id){
        var t = this; t.val('Loading...');
        var v = $(id).val();
        if (v=='') {
            t.val('');
            return this;
        }
        $.post(common() + '/modules/system/gateway.php',{action:'keywords',title:v},function(d){
            t.val(d);
        });
        return this;
    };
    
})(jQuery);
/*
 * JSON  - JSON for jQuery
 *
 * FILE:jquery.json.js
 *
 * Example:
 *
 * $.toJSON(Object);
 * $.parseJSON(String);
 */
(function ($) {
    $.toJSON = function(o){
        var i, v, s = $.toJSON, t;
        if (o == null) return 'null';
        t = typeof o;
        if (t == 'string') {
            v = '\bb\tt\nn\ff\rr\""\'\'\\\\';
            return '"' + o.replace(/([\u0080-\uFFFF\x00-\x1f\"])/g, function(a, b) {
                i = v.indexOf(b);
                if (i + 1) return '\\' + v.charAt(i + 1);
                a = b.charCodeAt().toString(16);
                return '\\u' + '0000'.substring(a.length) + a;
            }) + '"';
        }
        if (t == 'object') {
            if (o instanceof Array) {
                for (i=0, v = '['; i<o.length; i++) v += (i > 0 ? ',' : '') + s(o[i]);
                return v + ']';
            }
            v = '{';
            for (i in o) v += typeof o[i] != 'function' ? (v.length > 1 ? ',"' : '"') + i + '":' + s(o[i]) : '';
            return v + '}';
        }
        return '' + o;
    },
    $.parseJSON = function(s){
        if (!/^("(\\.|[^"\\\n\r])*?"|[,:{}\[\]0-9.\-+Eaeflnr-u \n\r\t])+?$/.test(s)) { return false; }
        try {
            return eval('(' + s + ')');
        } catch (ex) {
            // Ignore
            return false;
        }
    }
})(jQuery);

/**
 * Cookie plugin
 *
 * Copyright (c) 2006 Klaus Hartl (stilbuero.de)
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 */

/**
 * Create a cookie with the given name and value and other optional parameters.
 *
 * @example $.cookie('the_cookie', 'the_value');
 * @desc Set the value of a cookie.
 * @example $.cookie('the_cookie', 'the_value', { expires: 7, path: '/', domain: 'jquery.com', secure: true });
 * @desc Create a cookie with all available options.
 * @example $.cookie('the_cookie', 'the_value');
 * @desc Create a session cookie.
 * @example $.cookie('the_cookie', null);
 * @desc Delete a cookie by passing null as value. Keep in mind that you have to use the same path and domain
 *       used when the cookie was set.
 *
 * @param String name The name of the cookie.
 * @param String value The value of the cookie.
 * @param Object options An object literal containing key/value pairs to provide optional cookie attributes.
 * @option Number|Date expires Either an integer specifying the expiration date from now on in days or a Date object.
 *                             If a negative value is specified (e.g. a date in the past), the cookie will be deleted.
 *                             If set to null or omitted, the cookie will be a session cookie and will not be retained
 *                             when the the browser exits.
 * @option String path The value of the path atribute of the cookie (default: path of page that created the cookie).
 * @option String domain The value of the domain attribute of the cookie (default: domain of page that created the cookie).
 * @option Boolean secure If true, the secure attribute of the cookie will be set and the cookie transmission will
 *                        require a secure protocol (like HTTPS).
 * @type undefined
 *
 * @name $.cookie
 * @cat Plugins/Cookie
 * @author Klaus Hartl/klaus.hartl@stilbuero.de
 */

/**
 * Get the value of a cookie with the given name.
 *
 * @example $.cookie('the_cookie');
 * @desc Get the value of a cookie.
 *
 * @param String name The name of the cookie.
 * @return The value of the cookie.
 * @type String
 *
 * @name $.cookie
 * @cat Plugins/Cookie
 * @author Klaus Hartl/klaus.hartl@stilbuero.de
 */
jQuery.cookie = function(name, value, options) {
    if (typeof value != 'undefined') { // name and value given, set cookie
        options = options || {};
        if (value === null) {
            value = '';
            options.expires = -1;
        }
        var expires = '';
        if (options.expires && (typeof options.expires == 'number' || options.expires.toUTCString)) {
            var date;
            if (typeof options.expires == 'number') {
                date = new Date();
                date.setTime(date.getTime() + (options.expires * 24 * 60 * 60 * 1000));
            } else {
                date = options.expires;
            }
            expires = '; expires=' + date.toUTCString(); // use expires attribute, max-age is not supported by IE
        }
        // CAUTION: Needed to parenthesize options.path and options.domain
        // in the following expressions, otherwise they evaluate to undefined
        // in the packed version for some reason...
        var path = options.path ? '; path=' + (options.path) : '';
        var domain = options.domain ? '; domain=' + (options.domain) : '';
        var secure = options.secure ? '; secure' : '';
        document.cookie = [name, '=', encodeURIComponent(value), expires, path, domain, secure].join('');
    } else { // only name given, get cookie
        var cookieValue = null;
        if (document.cookie && document.cookie != '') {
            var cookies = document.cookie.split(';');
            for (var i = 0; i < cookies.length; i++) {
                var cookie = jQuery.trim(cookies[i]);
                // Does this cookie string begin with the name we want?
                if (cookie.substring(0, name.length + 1) == (name + '=')) {
                    cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
                    break;
                }
            }
        }
        return cookieValue;
    }
};

// 设置系统CSS
$.setStyle();
