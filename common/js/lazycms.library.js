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
function debug(s){ alert('debug:' + s); }
function common(){ return $("script[src*=js/jquery.js]").attr("src").replace(/(\/js\/jquery\.js\?(.*))/i,'');}
function lock(p1){ return p1 ? icon('a3') : icon('a4'); }
function cklist(p1){ return '<input name="list" id="list_'+p1+'" type="checkbox" value="'+p1+'"/>'; }
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
	// 调用语言包
	$.t = function(p1){
		var R;
		var lang = $(document).data('language');
		try	{
			R = eval('lang.' + p1 + ';');
		} catch (e) {
			R = p1;
		}
		R = typeof R=='undefined'?p1:R;
		return R;
	}
	/**
	 * 可编辑下拉框
	 *
	 * @example: <select edit="true" default="value"></select>
	 */
	$.selectEdit = function(){
		// 重新调整位置
		$('select[edit=yes]').each(function(){
			try	{
				var s = $(this); if (s.is('select')==false) { return ; };
				$(this).prev().css({top:s.position().top+'px'});
			} catch (e) {}
		});
		// 替换控件
		$('select[edit=true]').each(function(){
			try	{
				var s = $(this); if (s.is('select')==false) { return ; };
				var v = (s.attr('default')!='' && (typeof s.attr('default'))!='undefined')?s.attr('default'):s.val();
				var i = $('<input type="text" name="' + s.attr('name') + '" value="' + v + '" />')
					.mouseover(function(){ $(this).select(); })
					.css({width:(s.width() - 18) + 'px',height:(s.height() - 2) + 'px',position:'absolute',top:s.position().top+'px',border:'none',margin:($.browser.msie?1:2)+'px 0 0 1px',padding:($.browser.msie?2:0)+'px 0 0 2px'})
					.insertBefore(s);
				var c = $('<select name="edit_select_' + s.attr('name') + '" edit="yes">' + s.html() + '</select>')
					.change(function(){ $(this).prev().val(this.value);})
					if (s.attr('id')!=='') c.attr('id',s.attr('id'));
					i.blur(function(){
						c.val(this.value);
					});
					s.replaceWith(c);
			} catch (e) {}
		});
		
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
	// 关闭遮罩层
    $.undialogUI = function(){
        $('#dialogUI,#dialogBox,#iframeCover').remove();
    }
	// 弹出遮罩层
	$.dialogUI = function(opts){
		$.undialogUI();
	    opts = $.extend({
			title:'',
			close:true,
			opacity:0.6,
            background:'#FFFFFF'
        }, opts||{});
        var height = $(document).height();
        if ($('#dialogUI').is('div')) { $('#dialogUI').remove(); }; $('body').append('<div id="dialogUI"></div>');
		
		// IE6 版本需要搞定 Select QJ Div 层的问题 -_-!!
		if ($.browser.msie && $.browser.version=='6.0') {
			if ($('#iframeCover').is('iframe') == false) {
				$('body').append('<iframe id="iframeCover" style="filter:alpha(opacity=0);position:absolute;z-index:999;left:0;top:0;height:' + height + 'px;width:100%;"></iframe>');
			}
		}
        $('#dialogUI').css({ width:'100%',height:height + 'px','left':0,'top':0,'position':'absolute','background':opts.background,'z-index':1000,'filter':'alpha(opacity=' + (100 * opts.opacity) + ')','-moz-opacity':opts.opacity,'opacity':opts.opacity});
        if ($('#dialogBox').is('div')) { $('#dialogBox').remove(); }
		$('body').append('<div id="dialogBox" class="dialog"><div class="head"><strong>' + opts.title + '</strong>' + (opts.close?'<a href="javascript:;" rel="close"></a>':'') + '</div></div>').find('#dialogBox')
			.floatDiv({width:'400px',top:$(document).height()/4,left:$(document).width()/2 - 200})
			.find('[rel=close]').click(function(){
				$.undialogUI();
				return false;
			});
		return $('#dialogBox');
	}
	// 显示层
	$.blockUI = function(title,body){
		$.dialogUI({title:title})
			.append('<div class="body">' + body + '</div>')
			.floatDiv({width:'500px',top:$(document).height()/4,left:$(document).width()/2 - 250});
	}
	// alert
    $.alert = function(message,callback,type){
		type = type||'alert';
		var position;
		switch (type) {
		    case 'success':
    		    position = 'background-position:0px 0px;';
    		    break;
    		case 'error':
    		    position = 'background-position:0px -40px;';
    		    break;
    		default:
    		    position = 'background-position:0px -80px;';
    		    break;
		}
		$.dialogUI({title:$.t('alert'),close:false})
			.append('<div class="body"><div class="icon" style="' + position + '"></div><div class="content"><h3>' + message + '</h3></div></div>')
			.append('<div class="button"><button type="button" rel="submit">' + $.t('submit') + '</button></div>')
			.floatDiv({width:'400px',top:$(document).height()/4,left:$(document).width()/2 - 200})
			.find('[rel=submit]').click(function(){
				$.undialogUI();
				if ($.isFunction(callback)) {callback();}
				return false;
			}).focus();
    }
	// confirm
	$.confirm = function(message,callback){
		$.dialogUI({title:$.t('confirm'),close:false})
			.append('<div class="body"><div class="icon" style="background-position:0px -80px;"></div><div class="content"><h3>' + message + '</h3></div></div>')
			.append('<div class="button"><button type="button" rel="submit">' + $.t('submit') + '</button><button type="button" rel="cancel">' + $.t('cancel') + '</button></div>')
			.floatDiv({width:'400px',top:$(document).height()/4,left:$(document).width()/2 - 200})
			.find('[rel=submit]').click(function(){
				$.undialogUI();
				callback(true);
			}).focus().end()
			.find('[rel=cancel]').click(function(){
				$.undialogUI();
				callback(false);
			});
		return false;
	}
	// 跳转
    $.redirect = function(url){
        if (typeof url != 'undefined' && url != '') {
            self.location.href = url;
        }
    }
	// 折叠
    $.fn.collapsed = function(){
        var u = getURI()
        this.each(function(i){
            var t = $(this).parent(); t.attr('i',i);
            var r = $(t.attr('rel'),t.parents('fieldset'));
            var c = $.cookie('collapse_' + u.File + '_' + i);
            switch (c) {
                case 'block':
                    t.find('img').removeClass('a1').addClass('a2');
                    r.show();
                    break;
                case 'none':
                    t.find('img').removeClass('a2').addClass('a1');
                    r.hide();
                    break;
                default:
                    if (t.attr('class')=='a1') {
                        r.hide();
                    } else {
                        r.show();
                    }
                    break;
            }
        });
        return this;
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
			$.ajax({
				cache: false,
				url: u,
				type: 'POST',
				data: {'submit':submit,'lists':lists},
				success: function(data){
					if (d = $.parseJSON(data)) {
                        $.result(d);
                    }
				}
			});
		}
	}
	// 错误处理
	$.result = function(d){
		switch (d.CODE) {
			case 'RESULT':// 返回结果
				return {TITLE:d.DATA.TITLE,BODY:d.DATA.BODY};
			case 'VALIDATE':// 显示错误消息
				var c = d.DATA.length;
				for (var i=0;i<c;i++) {
					$('[name='+d.DATA[i].id+']').unbind().attr('error',d.DATA[i].text).addClass('error');
				}
				$('[error]').jTips();
				break;
			case 'SUCCESS': // 成功提示
				$.alert(d.DATA.MESSAGE,function(){
					$.redirect(d.DATA.URL);
				},'success');
				break;
			case 'ERROR':	// 错误提示
				$.alert(d.DATA.MESSAGE,function(){
					$.redirect(d.DATA.URL);
				},'error');
				break;
			case 'ALERT':	// 警告提示
				$.alert(d.DATA.MESSAGE,function(){
					$.redirect(d.DATA.URL);
				},'alert');
				break;
			case 'REDIRECT':// 跳转
				$.redirect(d.DATA.URL);
				break;
			default:
				debug(data);
				break;
		}
		return false;
	}
    /**
     * ajax Submit
     */
    $.fn.ajaxSubmit = function(){
        this.submit(function(){
            // 先释放绑定的所有事件，清除错误样式
            $('[error]').unbind().removeAttr('error').removeClass('error');
            var t = $(this);
            var b = $('button[type=submit]',this);
            // 取得 action 地址
            var u = t.attr('action'); if (u==''||typeof u=='undefined') { u = self.location.href; }
            // 设置登录按钮
                b.attr('disabled',true);
            // 设置编辑器内容
            if (typeof tinyMCE!='undefined') {
                var editor = tinyMCE.editors;
                for (e in editor) {
                    $('#'+editor[e].id).val(editor[e].getContent());
                }
            }
            // ajax submit
            $.ajax({
                cache: false,
                url: u,
                type: t.attr('method').toUpperCase(),
                data: t.serializeArray(),
                beforeSend: function(s){
                    s.setRequestHeader("AJAX_SUBMIT",true);
					var N = Math.floor(Math.random()*100000); $(this).data('N',N);
					var load = $('<div id="loading' + N + '" class="loading"><img class="os" src="' + common() + '/images/loading.gif" />Loading...</div>');
						load.floatDiv({top:'5px',right:'5px'}).appendTo('body');
                },
                success: function(data){
                    if (d = $.parseJSON(data)) {
						$.result(d);
                    }
                },
                complete: function(){
                    b.attr('disabled',false);
					$('#loading' + $(this).data('N')).remove();
                }
            });
            return false;
        });
        return this;
    },
    /**
     * 气泡提示
     */
    $.fn.jTips = function(){
        var $this = $(this);        
        this.hover(function(){
            var jTip = $('body').append('<div class="jTip"><div class="jTip-body"></div><div class="jTip-foot"></div></div>').find('.jTip');
            var jHeight = jTip.height();
            $this.mousemove(function(e){
                jTip.css({'top':((e.clientY+document.documentElement.scrollTop) - jHeight - 20 ) + 'px','left':(e.clientX + 5) + 'px'});
            });
            jTip.fadeIn('fast').find('.jTip-body').html($(this).attr('error'));
        },function(){
            $('.jTip').remove();
        });
    }
	// 帮助提示
	$.fn.help = function(path){
		if (typeof path != 'undefined')	{
			var t = this;
			$('img',t).attr('src',common() + '/images/loading.gif').removeClass('h5');
			$.post('../system/help.php',{module:MODULE,path:path},function(data){
				if (data = $.result(data)) {
					$.blockUI(data.TITLE,data.BODY);
				}
				$('img',t).attr('src',common() + '/images/white.gif').addClass('h5');
			},'json');
		} else {
			return this.each(function(){
				var p = $(this).attr('help');
				$(this).siblings('a[rel=help]').remove();
				$('<a href="javascript:;" rel="help"><img class="h5 os" src="../system/images/white.gif" /></a>').click(function(){
					var img = $('img',this).attr('src',common() + '/images/loading.gif').removeClass('h5');
					$.post('../system/help.php',{module:MODULE,path:p},function(data){
						if (data = $.result(data)) {
							img.attr('src',common() + '/images/white.gif').addClass('h5');
							var pos = img.position();
								if ((img.offset().left + 20 + 400)>$(document).width()) {
									pos.left = pos.left - 2 - 400;
								} else {
									pos.left = pos.left + 20;
								}
							if ($('#dialogHelp').is('div'))	{ $('#dialogHelp').remove(); }
							var help = $('<div id="dialogHelp" class="dialog"><div class="head"><strong>' + data.TITLE + '</strong><a href="javascript:;" rel="close"></a></div><div class="body"></div></div>')
								.find('[rel=close]').click(function(){
									$('#dialogHelp').remove();
									return false;
								}).end()
								.find('.body').html(data.BODY).end()
								.css({width:'400px',position:'absolute','z-index':1000,top:pos.top + 8,left:pos.left})
								.insertAfter(img.parent());
						}
					},'json');
				}).insertAfter(this);
				$('a').focus(function(){ this.blur(); });
			});
		}
	}
    // 兼容的窗口改变大小事件
	$.fn.wresize = function(f){
	    var version = '1.1';
		var wresize = {fired: false, width: 0};
		function resizeOnce(){
			if ($.browser.msie) {
                if (!wresize.fired) {
                    wresize.fired = true;
                } else {
					var version = parseInt($.browser.version,10);
						wresize.fired = false;
					if (version<7) {
						return false;
					} else if (version==7) {
						//a vertical resize is fired once, an horizontal resize twice
						var width = $( window ).width();
						if ( width != wresize.width ) {
							wresize.width = width;
							return false;
						}
					}
				}
			}
			return true;
		}
		function handleWResize(e){
			if (resizeOnce()) {
				return f.apply(this, [e]);
			}
		}
		this.each(function(){
			if (this == window) {
				$(this).resize(handleWResize);
			} else {
				$(this).resize(f);
			}
		});
		return this;
	}
    /**
     * 任意位置浮动
     *
     * 兼容IE6、IE7、Firefox
     *
     * @params: string   position(可选，默认右下角)
     *          RB: 右下角
     *          RT: 右上角
     *          LB: 左下角
     *          LT: 左上角
     *          M:  居中
     *          object: {left:'',top:'',right:'',buttom:''}
     *
     * @example:    $(element).floatDiv(position);
     */
    $.fn.floatDiv = function(position){
        var isIE6  = $.browser.msie && $.browser.version=='6.0'?true:false;
        var width  = $(document).width();
        var height = $(document).height();
        return this.each(function(){
            var loc;
            if (typeof position == 'undefined' || typeof position == 'string'){
                switch (position) {
                    case 'RB' : loc = { right:'0px',bottom:'0px' }; break;
                    case 'LB' : loc = { left :'0px',bottom:'0px' }; break;
                    case 'LT' : loc = { left :'0px',top   :'0px' }; break;
                    case 'RT' : loc = { right:'0px',top   :'0px' }; break;
                    case 'M'  : 
                        var l = width / 2 -  $(this).width() / 2;
                        var t = height / 2 - $(this).height() / 2;
                            loc = {left:l + 'px',top:t + 'px'};
                        break;
                    default: loc = {right:'0px',bottom:'0px'}; break;
                }
            } else {
                loc = position;
            }
            $(this).css('z-index','9999').css(loc).css('position','fixed');
            if (isIE6) {
				$(this).css('position','absolute');
			}
			var $this = $(this);
            $(window).wresize(function(){
                $this.css('left',$(document).width()/2 - $this.width()/2);
            });
        });
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

/*
 * jqDnR - Minimalistic Drag'n'Resize for jQuery.
 *
 * Copyright (c) 2007 Brice Burgess <bhb@iceburg.net>, http://www.iceburg.net
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 * 
 * $Version: 2007.08.19 +r2
 */

(function($){
    $.fn.jqDrag   = function(h){return i(this,h,'d');};
    $.fn.jqResize = function(h){return i(this,h,'r');};
    $.jqDnR = {
        dnr:{},e:0,
        drag:function(v){
            if(M.k == 'd') {
                E.css({left:M.X+v.pageX-M.pX,top:M.Y+v.pageY-M.pY});
            } else {
                E.css({width:Math.max(v.pageX-M.pX+M.W,0),height:Math.max(v.pageY-M.pY+M.H,0)});
            }
            return false;
        },
        stop:function(){
            E.css('opacity',M.o);
            $().unbind('mousemove',J.drag).unbind('mouseup',J.stop);
        }
    };
    var J = $.jqDnR,
        M = J.dnr,
        E = J.e,
        f = function(k){ return parseInt(E.css(k))||false; },
        i = function(e,h,k){
            return e.each(function(){
                h = (h) ? $(h,e) : e;
                h.bind('mousedown',{e:e,k:k},function(v){
                    var d = v.data,p = {}; E = d.e;
                    // attempt utilization of dimensions plugin to fix IE issues
                    if (E.css('position') != 'relative'){
                        try{
                            E.position(p);
                        } catch(e){}
                    }
                    M = {
                        X:p.left||f('left')||0,
                        Y:p.top||f('top')||0,
                        W:f('width')||E[0].scrollWidth||0,
                        H:f('height')||E[0].scrollHeight||0,
                        pX:v.pageX,
                        pY:v.pageY,
                        k:d.k,
                        o:E.css('opacity')
                    };
                    E.css({opacity:0.8});
                    $().mousemove($.jqDnR.drag).mouseup($.jqDnR.stop);
                    return false;
                });
            });
        };
})(jQuery);


/**
 * TableDnD plug-in for JQuery, allows you to drag and drop table rows
 * You can set up various options to control how the system will work
 * Copyright (c) Denis Howlett <denish@isocra.com>
 * Licensed like jQuery, see http://docs.jquery.com/License.
 *
 * Configuration options:
 * 
 * onDragStyle
 *     This is the style that is assigned to the row during drag. There are limitations to the styles that can be
 *     associated with a row (such as you can't assign a border--well you can, but it won't be
 *     displayed). (So instead consider using onDragClass.) The CSS style to apply is specified as
 *     a map (as used in the jQuery css(...) function).
 * onDropStyle
 *     This is the style that is assigned to the row when it is dropped. As for onDragStyle, there are limitations
 *     to what you can do. Also this replaces the original style, so again consider using onDragClass which
 *     is simply added and then removed on drop.
 * onDragClass
 *     This class is added for the duration of the drag and then removed when the row is dropped. It is more
 *     flexible than using onDragStyle since it can be inherited by the row cells and other content. The default
 *     is class is tDnD_whileDrag. So to use the default, simply customise this CSS class in your
 *     stylesheet.
 * onDrop
 *     Pass a function that will be called when the row is dropped. The function takes 2 parameters: the table
 *     and the row that was dropped. You can work out the new order of the rows by using
 *     table.rows.
 * onDragStart
 *     Pass a function that will be called when the user starts dragging. The function takes 2 parameters: the
 *     table and the row which the user has started to drag.
 * onAllowDrop
 *     Pass a function that will be called as a row is over another row. If the function returns true, allow 
 *     dropping on that row, otherwise not. The function takes 2 parameters: the dragged row and the row under
 *     the cursor. It returns a boolean: true allows the drop, false doesn't allow it.
 * scrollAmount
 *     This is the number of pixels to scroll if the user moves the mouse cursor to the top or bottom of the
 *     window. The page should automatically scroll up or down as appropriate (tested in IE6, IE7, Safari, FF2,
 *     FF3 beta
 * dragHandle
 *     This is the name of a class that you assign to one or more cells in each row that is draggable. If you
 *     specify this class, then you are responsible for setting cursor: move in the CSS and only these cells
 *     will have the drag behaviour. If you do not specify a dragHandle, then you get the old behaviour where
 *     the whole row is draggable.
 * 
 * Other ways to control behaviour:
 *
 * Add class="nodrop" to any rows for which you don't want to allow dropping, and class="nodrag" to any rows
 * that you don't want to be draggable.
 *
 * Inside the onDrop method you can also call $.tableDnD.serialize() this returns a string of the form
 * <tableID>[]=<rowID1>&<tableID>[]=<rowID2> so that you can send this back to the server. The table must have
 * an ID as must all the rows.
 *
 * Other methods:
 *
 * $("...").tableDnDUpdate() 
 * Will update all the matching tables, that is it will reapply the mousedown method to the rows (or handle cells).
 * This is useful if you have updated the table rows using Ajax and you want to make the table draggable again.
 * The table maintains the original configuration (so you don't have to specify it again).
 *
 * $("...").tableDnDSerialize()
 * Will serialize and return the serialized string as above, but for each of the matching tables--so it can be
 * called from anywhere and isn't dependent on the currentTable being set up correctly before calling
 *
 * Known problems:
 * - Auto-scoll has some problems with IE7  (it scrolls even when it shouldn't), work-around: set scrollAmount to 0
 * 
 * Version 0.2: 2008-02-20 First public version
 * Version 0.3: 2008-02-07 Added onDragStart option
 *                         Made the scroll amount configurable (default is 5 as before)
 * Version 0.4: 2008-03-15 Changed the noDrag/noDrop attributes to nodrag/nodrop classes
 *                         Added onAllowDrop to control dropping
 *                         Fixed a bug which meant that you couldn't set the scroll amount in both directions
 *                         Added serialize method
 * Version 0.5: 2008-05-16 Changed so that if you specify a dragHandle class it doesn't make the whole row
 *                         draggable
 *                         Improved the serialize method to use a default (and settable) regular expression.
 *                         Added tableDnDupate() and tableDnDSerialize() to be called when you are outside the table
 */
jQuery.tableDnD = {
    /** Keep hold of the current table being dragged */
    currentTable : null,
    /** Keep hold of the current drag object if any */
    dragObject: null,
    /** The current mouse offset */
    mouseOffset: null,
    /** Remember the old value of Y so that we don't do too much processing */
    oldY: 0,

    /** Actually build the structure */
    build: function(options) {
        // Set up the defaults if any

        this.each(function() {
            // This is bound to each matching table, set up the defaults and override with user options
            this.tableDnDConfig = jQuery.extend({
                onDragStyle: null,
                onDropStyle: null,
                // Add in the default class for whileDragging
                onDragClass: "tDnD_whileDrag",
                onDrop: null,
                onDragStart: null,
                scrollAmount: 5,
                serializeRegexp: /[^\-]*$/, // The regular expression to use to trim row IDs
                serializeParamName: null, // If you want to specify another parameter name instead of the table ID
                dragHandle: null // If you give the name of a class here, then only Cells with this class will be draggable
            }, options || {});
            // Now make the rows draggable
            jQuery.tableDnD.makeDraggable(this);
        });

        // Now we need to capture the mouse up and mouse move event
        // We can use bind so that we don't interfere with other event handlers
        jQuery(document)
            .bind('mousemove', jQuery.tableDnD.mousemove)
            .bind('mouseup', jQuery.tableDnD.mouseup);

        // Don't break the chain
        return this;
    },

    /** This function makes all the rows on the table draggable apart from those marked as "NoDrag" */
    makeDraggable: function(table) {
        var config = table.tableDnDConfig;
        if (table.tableDnDConfig.dragHandle) {
            // We only need to add the event to the specified cells
            var cells = jQuery("td."+table.tableDnDConfig.dragHandle, table);
            cells.each(function() {
                // The cell is bound to "this"
                jQuery(this).mousedown(function(ev) {
                    jQuery.tableDnD.dragObject = this.parentNode;
                    jQuery.tableDnD.currentTable = table;
                    jQuery.tableDnD.mouseOffset = jQuery.tableDnD.getMouseOffset(this, ev);
                    if (config.onDragStart) {
                        // Call the onDrop method if there is one
                        config.onDragStart(table, this);
                    }
                    return false;
                });
            })
        } else {
            // For backwards compatibility, we add the event to the whole row
            var rows = jQuery("tr", table); // get all the rows as a wrapped set
            rows.each(function() {
                // Iterate through each row, the row is bound to "this"
                var row = jQuery(this);
                if (! row.hasClass("nodrag")) {
                    row.mousedown(function(ev) {
                        if (ev.target.tagName == "TD") {
                            jQuery.tableDnD.dragObject = this;
                            jQuery.tableDnD.currentTable = table;
                            jQuery.tableDnD.mouseOffset = jQuery.tableDnD.getMouseOffset(this, ev);
                            if (config.onDragStart) {
                                // Call the onDrop method if there is one
                                config.onDragStart(table, this);
                            }
                            return false;
                        }
                    }).css("cursor", "move"); // Store the tableDnD object
                }
            });
        }
    },

    updateTables: function() {
        this.each(function() {
            // this is now bound to each matching table
            if (this.tableDnDConfig) {
                jQuery.tableDnD.makeDraggable(this);
            }
        })
    },

    /** Get the mouse coordinates from the event (allowing for browser differences) */
    mouseCoords: function(ev){
        if(ev.pageX || ev.pageY){
            return {x:ev.pageX, y:ev.pageY};
        }
        return {
            x:ev.clientX + document.body.scrollLeft - document.body.clientLeft,
            y:ev.clientY + document.body.scrollTop  - document.body.clientTop
        };
    },

    /** Given a target element and a mouse event, get the mouse offset from that element.
        To do this we need the element's position and the mouse position */
    getMouseOffset: function(target, ev) {
        ev = ev || window.event;

        var docPos    = this.getPosition(target);
        var mousePos  = this.mouseCoords(ev);
        return {x:mousePos.x - docPos.x, y:mousePos.y - docPos.y};
    },

    /** Get the position of an element by going up the DOM tree and adding up all the offsets */
    getPosition: function(e){
        var left = 0;
        var top  = 0;
        /** Safari fix -- thanks to Luis Chato for this! */
        if (e.offsetHeight == 0) {
            /** Safari 2 doesn't correctly grab the offsetTop of a table row
            this is detailed here:
            http://jacob.peargrove.com/blog/2006/technical/table-row-offsettop-bug-in-safari/
            the solution is likewise noted there, grab the offset of a table cell in the row - the firstChild.
            note that firefox will return a text node as a first child, so designing a more thorough
            solution may need to take that into account, for now this seems to work in firefox, safari, ie */
            e = e.firstChild; // a table cell
        }

        while (e.offsetParent){
            left += e.offsetLeft;
            top  += e.offsetTop;
            e     = e.offsetParent;
        }

        left += e.offsetLeft;
        top  += e.offsetTop;

        return {x:left, y:top};
    },

    mousemove: function(ev) {
        if (jQuery.tableDnD.dragObject == null) {
            return;
        }

        var dragObj = jQuery(jQuery.tableDnD.dragObject);
        var config = jQuery.tableDnD.currentTable.tableDnDConfig;
        var mousePos = jQuery.tableDnD.mouseCoords(ev);
        var y = mousePos.y - jQuery.tableDnD.mouseOffset.y;
        //auto scroll the window
        var yOffset = window.pageYOffset;
        if (document.all) {
            // Windows version
            //yOffset=document.body.scrollTop;
            if (typeof document.compatMode != 'undefined' &&
                 document.compatMode != 'BackCompat') {
               yOffset = document.documentElement.scrollTop;
            }
            else if (typeof document.body != 'undefined') {
               yOffset=document.body.scrollTop;
            }

        }
            
        if (mousePos.y-yOffset < config.scrollAmount) {
            window.scrollBy(0, -config.scrollAmount);
        } else {
            var windowHeight = window.innerHeight ? window.innerHeight
                    : document.documentElement.clientHeight ? document.documentElement.clientHeight : document.body.clientHeight;
            if (windowHeight-(mousePos.y-yOffset) < config.scrollAmount) {
                window.scrollBy(0, config.scrollAmount);
            }
        }


        if (y != jQuery.tableDnD.oldY) {
            // work out if we're going up or down...
            var movingDown = y > jQuery.tableDnD.oldY;
            // update the old value
            jQuery.tableDnD.oldY = y;
            // update the style to show we're dragging
            if (config.onDragClass) {
                dragObj.addClass(config.onDragClass);
            } else {
                dragObj.css(config.onDragStyle);
            }
            // If we're over a row then move the dragged row to there so that the user sees the
            // effect dynamically
            var currentRow = jQuery.tableDnD.findDropTargetRow(dragObj, y);
            if (currentRow) {
                // TODO worry about what happens when there are multiple TBODIES
                if (movingDown && jQuery.tableDnD.dragObject != currentRow) {
                    jQuery.tableDnD.dragObject.parentNode.insertBefore(jQuery.tableDnD.dragObject, currentRow.nextSibling);
                } else if (! movingDown && jQuery.tableDnD.dragObject != currentRow) {
                    jQuery.tableDnD.dragObject.parentNode.insertBefore(jQuery.tableDnD.dragObject, currentRow);
                }
            }
        }

        return false;
    },

    /** We're only worried about the y position really, because we can only move rows up and down */
    findDropTargetRow: function(draggedRow, y) {
        var rows = jQuery.tableDnD.currentTable.rows;
        for (var i=0; i<rows.length; i++) {
            var row = rows[i];
            var rowY    = this.getPosition(row).y;
            var rowHeight = parseInt(row.offsetHeight)/2;
            if (row.offsetHeight == 0) {
                rowY = this.getPosition(row.firstChild).y;
                rowHeight = parseInt(row.firstChild.offsetHeight)/2;
            }
            // Because we always have to insert before, we need to offset the height a bit
            if ((y > rowY - rowHeight) && (y < (rowY + rowHeight))) {
                // that's the row we're over
                // If it's the same as the current row, ignore it
                if (row == draggedRow) {return null;}
                var config = jQuery.tableDnD.currentTable.tableDnDConfig;
                if (config.onAllowDrop) {
                    if (config.onAllowDrop(draggedRow, row)) {
                        return row;
                    } else {
                        return null;
                    }
                } else {
                    // If a row has nodrop class, then don't allow dropping (inspired by John Tarr and Famic)
                    var nodrop = jQuery(row).hasClass("nodrop");
                    if (! nodrop) {
                        return row;
                    } else {
                        return null;
                    }
                }
                return row;
            }
        }
        return null;
    },

    mouseup: function(e) {
        if (jQuery.tableDnD.currentTable && jQuery.tableDnD.dragObject) {
            var droppedRow = jQuery.tableDnD.dragObject;
            var config = jQuery.tableDnD.currentTable.tableDnDConfig;
            // If we have a dragObject, then we need to release it,
            // The row will already have been moved to the right place so we just reset stuff
            if (config.onDragClass) {
                jQuery(droppedRow).removeClass(config.onDragClass);
            } else {
                jQuery(droppedRow).css(config.onDropStyle);
            }
            jQuery.tableDnD.dragObject   = null;
            if (config.onDrop) {
                // Call the onDrop method if there is one
                config.onDrop(jQuery.tableDnD.currentTable, droppedRow);
            }
            jQuery.tableDnD.currentTable = null; // let go of the table too
        }
    },

    serialize: function() {
        if (jQuery.tableDnD.currentTable) {
            return jQuery.tableDnD.serializeTable(jQuery.tableDnD.currentTable);
        } else {
            return "Error: No Table id set, you need to set an id on your table and every row";
        }
    },

    serializeTable: function(table) {
        var result = "";
        var tableId = table.id;
        var rows = table.rows;
        for (var i=0; i<rows.length; i++) {
            if (result.length > 0) result += "&";
            var rowId = rows[i].id;
            if (rowId && rowId && table.tableDnDConfig && table.tableDnDConfig.serializeRegexp) {
                rowId = rowId.match(table.tableDnDConfig.serializeRegexp)[0];
            }

            result += tableId + '[]=' + rowId;
        }
        return result;
    },

    serializeTables: function() {
        var result = "";
        this.each(function() {
            // this is now bound to each matching table
            result += jQuery.tableDnD.serializeTable(this);
        });
        return result;
    }

}

jQuery.fn.extend(
    {
        tableDnD : jQuery.tableDnD.build,
        tableDnDUpdate : jQuery.tableDnD.updateTables,
        tableDnDSerialize: jQuery.tableDnD.serializeTables
    }
);

$.setStyle();