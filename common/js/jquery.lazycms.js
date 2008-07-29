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

// 函数加载 JavaScript *** *** www.LazyCMS.net *** ***
function LoadScript(plugin){
    var url = $("script[@src*=jquery.lazycms]").attr("src").replace("jquery.lazycms.js","jquery." + plugin + ".js");
    document.write('<scr' + 'ipt type="text/javascript" src="' + url + '"><\/scr' + 'ipt>');
}

function icon(p1,p2){ var IMG  = '<img src="../../common/images/icon/'+p1+'.png" alt="'+p1.toUpperCase()+'" class="os" />';  var HREF = '<a href="'+p2+'" title="'+p1.toUpperCase()+'">'+IMG+'</a>'; if (typeof p2 == "undefined") { return IMG; } else { return HREF; }}
function cklist(p1){ return '<input name="list" id="list_'+p1+'" type="checkbox" value="'+p1+'"/>'; }
function lock(p1){ return p1 ? icon('lock') : icon('lock-open'); }
// checkALL *** *** www.LazyCMS.net *** ***
function checkALL(e){
	$.each($(e.form).find('input:checkbox'),function(i,a){
		if (checkALL.arguments[1]!=undefined) { 
			this.checked = true;
		} else {
			this.checked = !this.checked;
		}
	});
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
// autoTitle *** *** www.LazyCMS.net *** ***
function autoTitle(){
	var title = $('#box fieldset legend[@rel=tab]').text();
	if (title!=='') {
		$('#tabs li.active a').text(title);
	}
}
// autoTitle *** *** www.LazyCMS.net *** ***
function toggleFieldset(p1,p2){
	// 展开事件
	$(p1).toggleClass('collapse').toggleClass('collapsed');
	$(p2,$(p1).parents('fieldset')).toggle();
	parent.$('#main').height($(document).find('body').height()+7);
	return false;
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
	$.fn.menuDrag = function(p1){
		/**
		这里暂停一下，制作起来很麻烦
		$(p1,this).css({cursor:'move',position:'relative'}).mousedown(function(){
			$('a',this).unbind().click(function(){return false});

		}).find('a').css({cursor:'move'});
		*/
	}
	$.fn.gp = function(p1,url){
		var form = this.parents('form');
		var url  = url||form.attr('action');
		if (p1!="" || p1!="-") {
            var R = escape(p1);
        }
		var isconfirm;
        if (R=='delete') {
            isconfirm = confirm(lazy_delete);
        } else if (R=='clear') {
            isconfirm = confirm(lazy_clear);
        } else {
            isconfirm = true;
        }
		if (R!='-' && isconfirm) {
			var lists = "";
            $('input:checkbox',form).each(function(){
                if(this.checked){
                    if(lists==""){
                        lists = this.value;
                    }else{
                        lists += "," + this.value;
                    }
                }
            });
			$.ajax({
				cache: false,
				dataType: 'json',
				url: url,
				type: 'POST',
				data: {'submit':R,'lists':lists},
				error: function(){
					$.post(url,{'submit':R,'lists':lists},function(data){
						alert(data);
					});
				},
				success: function(data){
					$.ajaxTip(data);
					if (typeof data.url != 'undefined') {
						if (typeof data.sleep == 'undefined') { data.sleep = 0; }
						window.setTimeout("self.location.href = '" + data.url + "';",data.sleep*1000);
					}
				}
			});
		}
		return this;
	}
	$.fn.apply = function(){
		if ($('input[@name=___method]').is('input')==false) {
			this.append('<input name="___method" type="hidden" value="apply" />');
		}
		return this.data('___method','apply').ajaxSubmit();
	}
	$.fn.save = function(){
		$('input[@name=___method]').remove();
		return this.data('___method','submit').ajaxSubmit();
	}
    // 封装 ajaxSubmit
	$.fn.ajaxSubmit = function(){
		var form = this;
		var method = this.data('___method');
		var submit = $('button.' + method,form);
		// 先释放绑定的所有事件，清除错误样式
		$('input.error').unbind().toggleClass('error');
		// 移除所有 Tips 信息
		$('.jTip').remove();
		// 取得 action 地址
		var url = form.attr('action'); if (url==''||typeof url=='undefined') { url = self.location.href; }
		// 设置登录按钮
		submit.attr('disabled',true);
		// ajax submit
		$.ajax({
			cache: false,
			dataType: 'json',
			url: url,
			type: form.attr('method').toUpperCase(),
			data: form.serializeArray(),
			error: function(){
				// 输出错误
				$.ajax({
					cache: false,
					url: url,
					type: form.attr('method').toUpperCase(),
					data: form.serializeArray(),
					success: function(data){
						alert(data);
					}
				});
			},
			success: function(data){
				if (data.length>0) {
					form.error(data);
				} else {
					$.ajaxTip(data);
					if (typeof data.url != 'undefined' && method != 'apply') {
						if (typeof data.sleep == 'undefined') { data.sleep = 0; }
						window.setTimeout("self.location.href = '" + data.url + "';",data.sleep*1000);
					}
				}
			},
			complete: function(){
				submit.attr('disabled',false);
			}
		});
		return false;
	}
	// 封装 ajaxTip
	$.ajaxTip = function(params){
		// 解析传入的参数，并更新对话框的内容
		var data = params||{}; if (typeof data == 'string'){ data = parent.$.parseJSON(data); }
		var color = '#30A9F3';
		if (typeof data.text!='undefined') {
			parent.$('#tip').remove();
			parent.$('body').append('<div id="tip">' + data.text + '</div>');
			if (typeof data.status!='undefined') {
				switch (data.status) {
					case 'error': color = '#993300'; break;
					case 'success': color = '#009900'; break;
					case 'tips': color = '#FF6600'; break;
				}
				parent.$("#tip").css('background-color',color);
			}
			parent.$("#tip").hide().show().animate({backgroundColor:'#FF00FF'},600).animate({backgroundColor:color},600).floatdiv({top:56});
			parent.window.setTimeout("parent.$('#tip').slideUp('fast');",6000);
		}
	}
	$.fn.error = function(v){
		var error = v||{};
		if (typeof v == 'string'){ error = $.parseJSON(v); }
		if (error==undefined) { return ; }
		for (var i=0;i<error.length;i++) {
			$('#'+error[i].id).unbind().attr('error',error[i].text).addClass('error');
		}
		this.tips('error','input.error');
		return this;
	};
	$.fn.tips = function(attr,selector){
		var $this = this;
		$(selector,this).mouseover(function(){
			if (this.type=='text' || this.type=='password') {
				$(this).focus();
			}
		});
		$(selector,this).hover(function(e){
			var width = 200; // 默认宽度
			$('.jTip').remove();
			var text  = $(this).attr(attr);
			
			if (text.indexOf('::')>-1) {
				var title = '<strong>' + text.substr(0,text.indexOf('::')) + '</strong><br/>';
				var text  = text.substr(text.indexOf('::')+2);
					if (text.indexOf('::')>-1) {
						width = text.substr(0,text.indexOf('::'));
						text  = text.substr(text.indexOf('::')+2);
					}
					text = title + text;
			}
			$('body').append('<div class="jTip"><div class="jTip-body">' + text + '</div><div class="jTip-foot"></div></div>');
			var jTip    = $('.jTip'); jTip.width(width);
			var jHeight = jTip.height();
				jTip.css({'top':(e.pageY - jHeight ) + 'px','left':(e.pageX + 2) + 'px'}).fadeIn('fast');
				$(selector,$this).mousemove(function(e){
					jTip.css({'top':(e.pageY - jHeight ) + 'px','left':(e.pageX + 2) + 'px'});
				});
		},function(){
			$('.jTip').remove();
		});
		return this;
	}
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
	}
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
			return undefined;
		return eval('('+v+')');
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


/*
 * ContextMenu - jQuery plugin for right-click context menus
 *
 * Author: Chris Domigan
 * Contributors: Dan G. Switzer, II
 * Parts of this plugin are inspired by Joern Zaefferer's Tooltip plugin
 *
 * Dual licensed under the MIT and GPL licenses:
 *   http://www.opensource.org/licenses/mit-license.php
 *   http://www.gnu.org/licenses/gpl.html
 *
 * Version: r2
 * Date: 16 July 2007
 *
 * For documentation visit http://www.trendskitchens.co.nz/jquery/contextmenu/
 *
 */

(function($) {

 	var menu, shadow, trigger, content, hash, currentTarget;
  var defaults = {
    menuStyle: {
      listStyle: 'none',
      padding: '1px',
      margin: '0px',
      backgroundColor: '#fff',
      border: '1px solid #999',
      width: '100px'
    },
    itemStyle: {
      margin: '0px',
      color: '#000',
      display: 'block',
      cursor: 'default',
      padding: '3px',
      border: '1px solid #fff',
      backgroundColor: 'transparent'
    },
    itemHoverStyle: {
      border: '1px solid #0a246a',
      backgroundColor: '#b6bdd2'
    },
    eventPosX: 'pageX',
    eventPosY: 'pageY',
    shadow : true,
    onContextMenu: null,
    onShowMenu: null
 	};

  $.fn.contextMenu = function(id, options) {
    if (!menu) {                                      // Create singleton menu
      menu = $('<div id="jqContextMenu"></div>')
               .hide()
               .css({position:'absolute', zIndex:'500'})
               .appendTo('body')
               .bind('click', function(e) {
                 e.stopPropagation();
               });
    }
    if (!shadow) {
      shadow = $('<div></div>')
                 .css({backgroundColor:'#000',position:'absolute',opacity:0.2,zIndex:499})
                 .appendTo('body')
                 .hide();
    }
    hash = hash || [];
    hash.push({
      id : id,
      menuStyle: $.extend({}, defaults.menuStyle, options.menuStyle || {}),
      itemStyle: $.extend({}, defaults.itemStyle, options.itemStyle || {}),
      itemHoverStyle: $.extend({}, defaults.itemHoverStyle, options.itemHoverStyle || {}),
      bindings: options.bindings || {},
      shadow: options.shadow || options.shadow === false ? options.shadow : defaults.shadow,
      onContextMenu: options.onContextMenu || defaults.onContextMenu,
      onShowMenu: options.onShowMenu || defaults.onShowMenu,
      eventPosX: options.eventPosX || defaults.eventPosX,
      eventPosY: options.eventPosY || defaults.eventPosY
    });

    var index = hash.length - 1;
    $(this).bind('contextmenu', function(e) {
      // Check if onContextMenu() defined
      var bShowContext = (!!hash[index].onContextMenu) ? hash[index].onContextMenu(e) : true;
      if (bShowContext) display(index, this, e, options);
      return false;
    });
    return this;
  };

  function display(index, trigger, e, options) {
    var cur = hash[index];
    content = $('#'+cur.id).find('ul:first').clone(true);
    content.css(cur.menuStyle).find('li').css(cur.itemStyle).hover(
      function() {
        $(this).css(cur.itemHoverStyle);
      },
      function(){
        $(this).css(cur.itemStyle);
      }
    ).find('img').css({verticalAlign:'middle',paddingRight:'2px'});

    // Send the content to the menu
    menu.html(content);

    // if there's an onShowMenu, run it now -- must run after content has been added
		// if you try to alter the content variable before the menu.html(), IE6 has issues
		// updating the content
    if (!!cur.onShowMenu) menu = cur.onShowMenu(e, menu);

    $.each(cur.bindings, function(id, func) {
      $('#'+id, menu).bind('click', function(e) {
        hide();
        func(trigger, currentTarget);
      });
    });

    menu.css({'left':e[cur.eventPosX],'top':e[cur.eventPosY]}).show();
    if (cur.shadow) shadow.css({width:menu.width(),height:menu.height(),left:e.pageX+2,top:e.pageY+2}).show();
    $(document).one('click', hide);
  }

  function hide() {
    menu.hide();
    shadow.hide();
  }

  // Apply defaults
  $.contextMenu = {
    defaults : function(userDefaults) {
      $.each(userDefaults, function(i, val) {
        if (typeof val == 'object' && defaults[i]) {
          $.extend(defaults[i], val);
        }
        else defaults[i] = val;
      });
    }
  };

})(jQuery);

$(function() {
  $('div.contextMenu').hide();
});