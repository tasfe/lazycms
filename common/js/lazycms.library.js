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
					.click(function(){ $(this).select(); })
					.css({width:(s.width() - 18) + 'px',height:(s.height() - 2) + 'px',position:'absolute',top:s.position().top+'px',border:'none',margin:($.browser.msie?1:2)+'px 0 0 1px',padding:($.browser.msie?2:0)+'px 0 0 2px'})
					.insertBefore(s);
				var c = $('<select name="edit_select_' + s.attr('name') + '" edit="yes">' + s.html() + '</select>')
					.change(function(){ $(this).prev().val(this.value);}).val(i.val());
					if (s.attr('id')!=='') { c.attr('id',s.attr('id')); }
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
        var height = $(document).height(); $('#dialogHelp').remove();
        if ($('#dialogUI').is('div')) { $('#dialogUI').remove(); };
		$('body').append('<div id="dialogUI"></div>');
		
		// IE6 版本需要搞定 Select QJ Div 层的问题 -_-!!
		if ($.browser.msie && $.browser.version=='6.0') {
			if ($('#iframeCover').is('iframe') == false) {
				$('body').append('<iframe id="iframeCover" style="filter:alpha(opacity=0);position:absolute;z-index:99;left:0;top:0;height:' + height + 'px;width:100%;"></iframe>');
			}
		}
        $('#dialogUI').css({ width:'100%',height:height + 'px','left':0,'top':0,'position':'absolute','background':opts.background,'z-index':100,'filter':'alpha(opacity=' + (100 * opts.opacity) + ')','-moz-opacity':opts.opacity,'opacity':opts.opacity});
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
				return d.DATA;
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
				debug(d);
				break;
		}
		return false;
	}
    /**
     * ajax Submit
     */
    $.fn.ajaxSubmit = function(callback){
		callback = callback||function(){};
        this.unbind('submit').submit(function(){
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
						if (d = $.result(d)) {
							callback(d);
						}
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
                jTip.css({'top':((e.clientY+document.documentElement.scrollTop) - jHeight - 20 ) + 'px','left':(e.clientX + 5) + 'px','z-index':300});
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
							if ($('#dialogHelp').is('div'))	{ $('#dialogHelp').remove(); }
							var help = $('<div id="dialogHelp" class="dialog"><div class="head"><strong>' + data.TITLE + '</strong><a href="javascript:;" rel="close"></a></div><div class="body"></div></div>')
								.find('[rel=close]').click(function(){
									$('#dialogHelp').remove();
									return false;
								}).end()
								.find('.body').html(data.BODY).end()
								.css({position:'absolute','z-index':1000})
								.insertAfter(img.parent());
							var help = $('#dialogHelp');
							var body = $('.body',help);
								body.width(body.width() + 10);
							var width= body.width() + 10;
								help.width(width>350?350:width);
								body.width((width>350?350:width)-10);
								
							var pos = img.position();
								if ((img.offset().left + 20 + help.width())>$(document).width()) {
									pos.left = pos.left - 2 - help.width();
								} else {
									pos.left = pos.left + 20;
								}
								help.css({top:pos.top + 8,left:pos.left});
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
            $(this).css('z-index',200).css(loc).css('position','fixed');
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
// 设置系统CSS
$.setStyle();