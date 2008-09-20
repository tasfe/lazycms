/**
 * jQuery plugin list
 *
 * jquery.floatdiv.js
 * jquery.color.js
 * jquery.json.js
 * jquery.cookie.js
 * jquery.jqDnR.js
 * jquery.ContextMenu.js
 */
var sleepTimeout = null;
// 函数加载 JavaScript *** *** www.LazyCMS.net *** ***
function LoadScript(p){
    var u = $("script[@src*=jquery.lazycms]").attr("src").replace(/\?(.*)/,'').replace("jquery.lazycms.js",p + ".js");
    document.write('<scr' + 'ipt type="text/javascript" src="' + u + '"><\/scr' + 'ipt>');
}
function debug(v){ alert('debug:'+v); }
function lock(p1){ return p1 ? icon('lock') : icon('lock-open'); }
function changeHeight(){ parent.$('#main').height($(document).find('body').height()+7); }
function cklist(p1){ return '<input name="list" id="list_'+p1+'" type="checkbox" value="'+p1+'"/>'; }
function path(){ return $('script[@src*=jquery.js]').attr('src').replace(/\/js\/jquery\.js\?(.*)/,''); }
function autoTitle(){ var t = $('#box fieldset legend[@rel=tab]').text(); if (t!=='') { $('#tabs li.active a').text(t); } }
function checkALL(e){ var e = (typeof e=='object')?e.form:e; $('input:checkbox',e).each(function(){ if (typeof checkALL.arguments[1]!='undefined') { this.checked = true; } else { this.checked = !this.checked; }}); }
// icon *** *** www.LazyCMS.net *** ***
function icon(i,a){
	var cur = typeof a == "undefined"?' style="cursor:default;"':'';
	var IMG = '<i class="os icon-16-'+i+'"'+cur+'></i>';
	var HREF = '<a href="'+a+'">'+IMG+'</a>';
	if (typeof a == "undefined") { return IMG; } else { return HREF; }
}
// Purview *** *** www.LazyCMS.net *** ***
function Purview(){
	$('div.purview input.__bigP').each(function(){
		var checked = true;
		$('div.purview .__' + this.id).each(function(){
			if (checked) {
				checked = this.checked ? true : false;
			}
		});
		$('#' + this.id).attr('checked',checked);
	});
}
// SemiMemory *** *** www.LazyCMS.net *** ***
function SemiMemory(){
	var o = getHP();
	$('input:checkbox[@cookie=true]').each(function(i){
		var c = $.cookie('checkbox_'+o.File+'_'+$(this).attr('id'));
		if (c!==null) {
			this.checked = (c=='true') ? true : false;
		}
	}).click(function(){
		$.cookie('checkbox_'+o.File+'_'+$(this).attr('id'),this.checked,{expires:365,path:o.Path});
	});
	// 展开事件
	$('a.collapse,a.collapsed')
		.attr('href','javascript:;')
		.click(function(){
			var t = $(this);
			var c = (t.attr('cookie')!=='false')?true:false;
			var e = $(t.attr('rel'),t.parents('fieldset')).toggle();
				t.toggleClass('collapse').toggleClass('collapsed');
			if (c) {
				$.cookie('collapse_'+o.File+'_'+t.attr('i'),e.css('display'),{expires:365,path:o.Path});
			}
			changeHeight();
		});
	$('a.collapse:not(a[@cookie=false]),a.collapsed:not(a[@cookie=false])').collapsed();
	// 调整编辑器的高度
	$('iframe[@src*=fckeditor.html]').each(function(i){
		var id = this.id.replace('___Frame','');
		var height = $.cookie('editor_height_'+o.File+'_'+id);
		if (height!==null) {
			if (typeof $('#'+id).attr('rel') == 'undefined') {
				$('#'+id).attr('rel',this.height);
			}
			this.height = height;
		}
	});
}
// getHP *** *** www.LazyCMS.net *** ***
function getHP(){
	var e = {};
		e.Host = (('https:' == self.location.protocol) ? 'https://'+self.location.hostname : 'http://'+self.location.hostname);
		e.Path = self.location.href.replace(/\?(.*)/,'').replace(e.Host,'');
		e.File = e.Path.split('/').pop();
		e.Path = e.Path.substr(0,e.Path.lastIndexOf('/')+1);
		return e;
}

/*
 * LazyCMS JS library for jQuery
 * http://www.lazycms.net
 *
 * Copyright (c) 2008 LazyCMS.net
 * Licensed under the Apache 2.0 (LICENSE.txt) license.
 *
 * Example:
 *
 * $("#element").error(JSON String or Object);
 * $("#element").tips(JSON String or Object);
 */
 (function($) {
	// 展开事件 *** *** www.LazyCMS.net *** ***
	$.fn.collapsed = function(){
		var o = getHP()
		this.each(function(i){
			var t = $(this); t.attr('i',i);
			var r = $(t.attr('rel'),t.parents('fieldset'));
			var c = $.cookie('collapse_'+o.File+'_'+i);
			switch (c) {
				case 'block':
					t.removeClass('collapse').addClass('collapsed');
					r.show();
					break;
				case 'none':
					t.removeClass('collapsed').addClass('collapse');
					r.hide();
					break;
				default:
					if (t.attr('class')=='collapse') {
						r.hide();
					} else {
						r.show();
					}
					break;
			}
		});
		return this;
	}
	// 获取分词 *** *** www.LazyCMS.net *** ***
	$.fn.getKeywords = function(id){
		var t = this; t.val('Loading...');
		$.post('../system/keywords.php',{title:$(id).val()},function(d){
			t.val(d);
		});
		return this;
	};
	// 获取编辑器对象 *** *** www.LazyCMS.net *** ***
    $.fn.editor = function (){
		var t = this;
        var e = FCKeditorAPI.GetInstance(t.attr('name'));
		if (typeof e !== 'undefined') {
			$.extend(e,{
				// 计算编辑器内文字长度 *** *** www.LazyCMS.net *** ***
				length: function (){
					var d = this.EditorDocument;
					var l;
					if(document.all){
						l = d.body.innerText.length;
					} else {
						var r = d.createRange();
							r.selectNodeContents(d.body);
							l = r.toString().length;
					}
					return l;
				},
				// 读取和设置内容 *** *** www.LazyCMS.net *** ***
				html:function(s){
					if (typeof string=='undefined'){
						return this.GetXHTML(true);
					} else {
						this.SetHTML(s);
						return this;
					}
				},
				// 读取编辑器内的文字 *** *** www.LazyCMS.net *** ***
				val:function(){
					return this.GetXHTML(true);
				},
				// 追加内容 *** *** www.LazyCMS.net *** ***
				insert:function(s){
					if (this.EditMode != FCK_EDITMODE_WYSIWYG){
						this.SwitchEditMode(FCK_EDITMODE_WYSIWYG);
					}
					this.InsertHtml(s);
					return this;
				},
				// 调整编辑器大小 *** *** www.LazyCMS.net *** ***
				resize:function(p1,p2){
					var e = getHP();
					var o = t.nextAll('iframe[@src*=InstanceName='+t.attr('name')+']');
						if (typeof t.attr('rel') == 'undefined') {
							t.attr('rel',o.height());
						}
						if (p1=='+') {
							o.height(o.height()+p2);
						} else {
							if ((o.height()-p2) >= t.attr('rel')) {
								o.height(o.height()-p2);
							}
						}
						$.cookie('editor_height_'+e.File+'_'+t.attr('name'),o.height(),{expires:365,path:e.Path});
						changeHeight();
						return this;
				}
			});
			return e;
		} else {
			return this;
		}
    };
	// 列表上按钮的提交动作 *** *** www.LazyCMS.net *** ***
	$.fn.gp = function(p,u){
		var f = this.parents('form');
		var u  = u||f.attr('action');
		if (p!="" || p!="-") {
            var R = escape(p);
        }
		var ic;
        if (R=='delete') {
            ic = confirm(lazy_delete);
        } else if (R=='clear') {
            ic = confirm(lazy_clear);
        } else {
            ic = true;
        }
		if (R!='-' && ic) {
			var l = "";
            $('input:checkbox',f).each(function(){
                if(this.checked){
                    if(l==""){
                        l = this.value;
                    }else{
                        l += "," + this.value;
                    }
                }
            });
			$.ajax({
				cache: false,
				url: u,
				type: 'POST',
				data: {'submit':R,'lists':l},
				success: function(data){
					if (d = $.parseJSON(data)) {
						if (typeof d.text != 'undefined') {
							$.ajaxTip(d);
						}
						if (typeof d.url != 'undefined') {
							if (typeof d.sleep == 'undefined') { d.sleep = 0; }
							window.clearTimeout(sleepTimeout); sleepTimeout = window.setTimeout("self.location.href = '" + d.url + "';",d.sleep*1000);
						}
					} else {
						debug(data);
					}
				}
			});
		}
		return this;
	};
	// 应用按钮 *** *** www.LazyCMS.net *** ***
	$.fn.apply = function(){
		if ($('input[@name=___method]').is('input')==false) {
			this.append('<input name="___method" type="hidden" value="apply" />');
		}
		return this.data('___method','apply').ajaxSubmit();
	};
	// 保存按钮 *** *** www.LazyCMS.net *** ***
	$.fn.save = function(){
		$('input[@name=___method]').remove();
		return this.data('___method','submit').ajaxSubmit();
	};
    // 封装 ajaxSubmit *** *** www.LazyCMS.net *** ***
	$.fn.ajaxSubmit = function(){
		// 先释放绑定的所有事件，清除错误样式
		$('.error').unbind().toggleClass('error');
		// 移除所有 Tips 信息
		$('.jTip').remove();
		var t = this.tips('tip','[@tip]');
		var m = this.data('___method');
		var s = $('button.' + m,t);
		// 取得 action 地址
		var u = t.attr('action'); if (u==''||typeof u=='undefined') { u = self.location.href; }
		// 设置登录按钮
		s.attr('disabled',true);
		// ajax submit fckeditor必须进行 UpdateLinkedField
		for (var i=0;i<frames.length;++i){
			if (frames[i].FCK) {
				frames[i].FCK.UpdateLinkedField();
			}
		}
		// ajax submit
		$.ajax({
			cache: false,
			url: u,
			type: t.attr('method').toUpperCase(),
			data: t.serializeArray(),
			success: function(data){
				if (d = $.parseJSON(data)) {
					if (d.length>0) {
						t.error(d);
					} else {
						$.ajaxTip(d);
						if (typeof d.url != 'undefined' && m != 'apply') {
							if (typeof d.sleep == 'undefined') { d.sleep = 0; }
							window.clearTimeout(sleepTimeout); sleepTimeout = window.setTimeout("self.location.href = '" + d.url + "';",d.sleep*1000);
						}
					}
				} else {
					debug(data);
				}
			},
			complete: function(){
				s.attr('disabled',false);
			}
		});
		return false;
	};
	// 封装 ajaxTip *** *** www.LazyCMS.net *** ***
	$.ajaxTip = function(p){
		// 解析传入的参数，并更新对话框的内容
		var d = p||{}; if (typeof d == 'string'){ d = parent.$.parseJSON(d); }
		var c = '#30A9F3';
		if (typeof d.text!='undefined') {
			parent.$('#tip').remove();
			parent.$('body').append('<div id="tip">' + d.text + '</div>');
			if (typeof d.status!='undefined') {
				switch (d.status) {
					case 'error': c = '#993300'; break;
					case 'success': c = '#009900'; break;
					case 'tips': c = '#FF6600'; break;
				}
				parent.$("#tip").css('background-color',c);
			}
			parent.$("#tip").hide().show().animate({backgroundColor:'#FF00FF'},600).animate({backgroundColor:c},600).floatdiv({top:56});
			parent.$('.jTip').remove(); parent.window.clearTimeout(sleepTimeout);
			sleepTimeout = parent.window.setTimeout("parent.$('.jTip').remove();parent.$('#tip').slideUp('fast');",6000);
		}
	};
	// input错误提示 *** *** www.LazyCMS.net *** ***
	$.fn.error = function(p){
		var e = p||{};
		if (typeof p == 'string'){ e = $.parseJSON(p); }
		if (e==undefined) { return ; }
		for (var i=0;i<e.length;i++) {
			if ($('#'+e[i].id+'___Frame').is('iframe')) {
				$('#'+e[i].id+'___Frame').unbind().attr('error',e[i].text).addClass('error');
			} else {
				$('#'+e[i].id).unbind().attr('error',e[i].text).addClass('error');
			}
		}
		this.tips('error','.error');
		return this;
	};
	// 气泡提示 *** *** www.LazyCMS.net *** ***
	$.fn.tips = function(attr,selector){
		var t = this;
		$(selector,t).hover(function(e){
			var width = 200; // 默认宽度
			parent.$('.jTip').remove();
			var text = $(this).attr(attr);
			
			if (text.indexOf('::')>-1) {
				var title = text.substr(0,text.indexOf('::'));
					title = title!=''? '<strong>' + title + '</strong><br/>':'';
				var text  = text.substr(text.indexOf('::')+2);
					if (text.indexOf('::')>-1) {
						width = text.substr(0,text.indexOf('::'));
						text  = text.substr(text.indexOf('::')+2);
					}
					text = title + text;
			}
			parent.$('body').append('<div class="jTip"><div class="jTip-body">' + text + '</div><div class="jTip-foot"></div></div>');
			var jTop    = parent.$('#main').is('iframe')?parent.$('#top').height():0;
			var jTip    = parent.$('.jTip'); jTip.css('width',width+'px');
			var jHeight = jTip.height();
				jTip.css({'top':(e.clientY + jTop - jHeight ) + 'px','left':(e.clientX + 2) + 'px'}).fadeIn('fast');
				$(selector,t).mousemove(function(e){
					jTip.css({'top':(e.clientY + jTop - jHeight ) + 'px','left':(e.clientX + 2) + 'px'});
				});
		},function(){
			parent.$('.jTip').remove();
		});
		return this;
	};
	// 任意位置浮动 *** *** www.LazyCMS.net *** ***
	$.fn.floatdiv = function(position){
		var isIE6  = false;
		if ($.browser.msie && $.browser.version=='6.0') {
			//$("html").css("overflow-x","auto").css("overflow-y","hidden");
			isIE6=true;
		}
		// 如果一个页面有多个层带有position:absolute; 的style，则全部都受到影响，和被控制层一起移动。
		/* $("body").css({height:"100%",overflow:"auto"}); */
		var width  = $(document).width();
		var height = $(document).height();
		return this.each(function(){
			var loc;// 层的绝对定位位置
			if (typeof position == 'undefined' || typeof position == 'string'){
				switch (position) {
					case 'rightbottom': loc = { right:'0px',bottom:'0px' }; break;
					case 'leftbottom' : loc = { left :'0px',bottom:'0px' }; break;
					case 'lefttop'    : loc = { left :'0px',top   :'0px' }; break;
					case 'righttop'   : loc = { right:'0px',top   :'0px' }; break;
					case 'middle'     : 
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
		});
	};
})(jQuery);

/*
 * jQuery Color Animations
 * Copyright 2007 John Resig
 * Released under the MIT and GPL licenses.
 */
(function(jQuery){

    // We override the animation for all of these color styles
    jQuery.each(['backgroundColor', 'borderBottomColor', 'borderLeftColor', 'borderRightColor', 'borderTopColor', 'color', 'outlineColor'], function(i,attr){
        jQuery.fx.step[attr] = function(fx){
            if ( fx.state == 0 ) {
                fx.start = getColor( fx.elem, attr );
                fx.end = getRGB( fx.end );
            }

            fx.elem.style[attr] = "rgb(" + [
                Math.max(Math.min( parseInt((fx.pos * (fx.end[0] - fx.start[0])) + fx.start[0]), 255), 0),
                Math.max(Math.min( parseInt((fx.pos * (fx.end[1] - fx.start[1])) + fx.start[1]), 255), 0),
                Math.max(Math.min( parseInt((fx.pos * (fx.end[2] - fx.start[2])) + fx.start[2]), 255), 0)
            ].join(",") + ")";
        }
    });

    // Color Conversion functions from highlightFade
    // By Blair Mitchelmore
    // http://jquery.offput.ca/highlightFade/

    // Parse strings looking for color tuples [255,255,255]
    function getRGB(color) {
        var result;

        // Check if we're already dealing with an array of colors
        if ( color && color.constructor == Array && color.length == 3 )
            return color;

        // Look for rgb(num,num,num)
        if (result = /rgb\(\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*\)/.exec(color))
            return [parseInt(result[1]), parseInt(result[2]), parseInt(result[3])];

        // Look for rgb(num%,num%,num%)
        if (result = /rgb\(\s*([0-9]+(?:\.[0-9]+)?)\%\s*,\s*([0-9]+(?:\.[0-9]+)?)\%\s*,\s*([0-9]+(?:\.[0-9]+)?)\%\s*\)/.exec(color))
            return [parseFloat(result[1])*2.55, parseFloat(result[2])*2.55, parseFloat(result[3])*2.55];

        // Look for #a0b1c2
        if (result = /#([a-fA-F0-9]{2})([a-fA-F0-9]{2})([a-fA-F0-9]{2})/.exec(color))
            return [parseInt(result[1],16), parseInt(result[2],16), parseInt(result[3],16)];

        // Look for #fff
        if (result = /#([a-fA-F0-9])([a-fA-F0-9])([a-fA-F0-9])/.exec(color))
            return [parseInt(result[1]+result[1],16), parseInt(result[2]+result[2],16), parseInt(result[3]+result[3],16)];

        // Look for rgba(0, 0, 0, 0) == transparent in Safari 3
        if (result = /rgba\(0, 0, 0, 0\)/.exec(color))
            return colors['transparent']

        // Otherwise, we're most likely dealing with a named color
        return colors[jQuery.trim(color).toLowerCase()];
    }

    function getColor(elem, attr) {
        var color;

        do {
            color = jQuery.curCSS(elem, attr);

            // Keep going until we find an element that has color, or we hit the body
            if ( color != '' && color != 'transparent' || jQuery.nodeName(elem, "body") )
                break;

            attr = "backgroundColor";
        } while ( elem = elem.parentNode );

        return getRGB(color);
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
    var m = {
            '\b': '\\b',
            '\t': '\\t',
            '\n': '\\n',
            '\f': '\\f',
            '\r': '\\r',
            '"' : '\\"',
            '\\': '\\\\'
        },
        s = {
            'array': function (x) {
                var a = ['['], b, f, i, l = x.length, v;
                for (i = 0; i < l; i += 1) {
                    v = x[i];
                    f = s[typeof v];
                    if (f) {
                        v = f(v);
                        if (typeof v == 'string') {
                            if (b) {
                                a[a.length] = ',';
                            }
                            a[a.length] = v;
                            b = true;
                        }
                    }
                }
                a[a.length] = ']';
                return a.join('');
            },
            'boolean': function (x) {
                return String(x);
            },
            'null': function (x) {
                return "null";
            },
            'number': function (x) {
                return isFinite(x) ? String(x) : 'null';
            },
            'object': function (x) {
                if (x) {
                    if (x instanceof Array) {
                        return s.array(x);
                    }
                    var a = ['{'], b, f, i, v;
                    for (i in x) {
                        v = x[i];
                        f = s[typeof v];
                        if (f) {
                            v = f(v);
                            if (typeof v == 'string') {
                                if (b) {
                                    a[a.length] = ',';
                                }
                                a.push(s.string(i), ':', v);
                                b = true;
                            }
                        }
                    }
                    a[a.length] = '}';
                    return a.join('');
                }
                return 'null';
            },
            'string': function (x) {
                if (/["\\\x00-\x1f]/.test(x)) {
                    x = x.replace(/([\x00-\x1f\\"])/g, function(a, b) {
                        var c = m[b];
                        if (c) {
                            return c;
                        }
                        c = b.charCodeAt();
                        return '\\u00' +
                            Math.floor(c / 16).toString(16) +
                            (c % 16).toString(16);
                    });
                }
                return '"' + x + '"';
            }
        };

	$.toJSON = function(v) {
		var f = isNaN(v) ? s[typeof v] : s['number'];
		if (f) return f(v);
	};
	
	$.parseJSON = function(v, safe) {
		if (safe === undefined) safe = $.parseJSON.safe;
		if (safe && !/^("(\\.|[^"\\\n\r])*?"|[,:{}\[\]0-9.\-+Eaeflnr-u \n\r\t])+?$/.test(v))
			return false;
		try	{
			return eval('('+v+')');
		} catch (e)	{
			return false;
		}
		
	};
	
	$.parseJSON.safe = false;

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
