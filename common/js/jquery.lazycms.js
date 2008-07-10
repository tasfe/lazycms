// 函数加载 JavaScript *** *** www.LazyCMS.net *** ***
function LoadScript(plugin){
    var url = $("script[@src*=jquery.lazycms]").attr("src").replace("jquery.lazycms.js","jquery." + plugin + ".js");
    document.write('<scr' + 'ipt type="text/javascript" src="' + url + '"><\/scr' + 'ipt>');
}

function icon(l1,l2){ var IMG  = '<img src="../../common/images/icon/'+l1+'.png" alt="'+l1.toUpperCase()+'" class="os" />';  var HREF = '<a href="'+l2+'" title="'+l1.toUpperCase()+'">'+IMG+'</a>'; if (typeof l2 == "undefined") { return IMG; } else { return HREF; }}
function cklist(l1){ return '<input name="list" id="list_'+l1+'" type="checkbox" value="'+l1+'"/>'; }
function lock(l1){ return l1 ? icon('lock') : icon('lock-open'); }
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
	$.fn.gp = function(l1,url){
		var form = this.parents('form');
		var url  = url||form.attr('action');
		if (l1!="" || l1!="-") {
            var I1 = escape(l1);
        }
		var isconfirm;
        if (I1=='delete') {
            isconfirm = confirm(lazy_delete);
        } else if (I1=='clear') {
            isconfirm = confirm(lazy_clear);
        } else {
            isconfirm = true;
        }
		if (I1!='-' && isconfirm) {
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
				data: {'submit':I1,'lists':lists},
				error: function(){
					$.post(url,{'submit':I1,'lists':lists},function(data){
						alert(data);
					});
				},
				success: function(data){
					$(form).ajaxTip(data);
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
		$('.jTip',this).remove();
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
			error: function(data,msg){
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
					form.ajaxTip(data);
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
	$.fn.ajaxTip = function(params){
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
			$this.next('.jTip').remove();
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
			$this.after('<div class="jTip"><div class="jTip-body">' + text + '</div><div class="jTip-foot"></div></div>');
			var jTip    = $this.next('.jTip'); jTip.width(width);
			var jHeight = jTip.height();
				jTip.css({'top':(e.pageY - jHeight ) + 'px','left':(e.pageX + 2) + 'px'}).fadeIn('fast');
				$(selector,$this).mousemove(function(e){
					jTip.css({'top':(e.pageY - jHeight ) + 'px','left':(e.pageX + 2) + 'px'});
				});
		},function(){
			$this.next('.jTip').remove();
		});
		return this;
	}
	$.fn.floatdiv = function(position){
		var isIE6  = false; if ($.browser.msie && $.browser.version=='6.0') { isIE6=true; }
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

    // Some named colors to work with
    // From Interface by Stefan Petre
    // http://interface.eyecon.ro/

    var colors = {
        aqua:[0,255,255],
        azure:[240,255,255],
        beige:[245,245,220],
        black:[0,0,0],
        blue:[0,0,255],
        brown:[165,42,42],
        cyan:[0,255,255],
        darkblue:[0,0,139],
        darkcyan:[0,139,139],
        darkgrey:[169,169,169],
        darkgreen:[0,100,0],
        darkkhaki:[189,183,107],
        darkmagenta:[139,0,139],
        darkolivegreen:[85,107,47],
        darkorange:[255,140,0],
        darkorchid:[153,50,204],
        darkred:[139,0,0],
        darksalmon:[233,150,122],
        darkviolet:[148,0,211],
        fuchsia:[255,0,255],
        gold:[255,215,0],
        green:[0,128,0],
        indigo:[75,0,130],
        khaki:[240,230,140],
        lightblue:[173,216,230],
        lightcyan:[224,255,255],
        lightgreen:[144,238,144],
        lightgrey:[211,211,211],
        lightpink:[255,182,193],
        lightyellow:[255,255,224],
        lime:[0,255,0],
        magenta:[255,0,255],
        maroon:[128,0,0],
        navy:[0,0,128],
        olive:[128,128,0],
        orange:[255,165,0],
        pink:[255,192,203],
        purple:[128,0,128],
        violet:[128,0,128],
        red:[255,0,0],
        silver:[192,192,192],
        white:[255,255,255],
        yellow:[255,255,0],
        transparent: [255,255,255]
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

/*
 * ppDrag 0.1 - Extremely Fast Drag&Drop for jQuery
 * http://ppdrag.ppetrov.com/
 *
 * Copyright (c) 2008 Peter Petrov (ppetrov AT ppetrov DOT com)
 * Licensed under the LGPL (LGPL-LICENSE.txt) license.
 *
 * FILE:jquery.ppdrag.js
 * 
 * Note: at the moment ppDrag doesn't support elements with static positioning. Please use either relative, absolute, or fixed positioning. 
 *
 * Example:
 * 
 * $(document).ready(function() {
 *   // Activate ppDrag
 *   $("#element1").ppdrag();
 *   // (optional) Specify options
 *   $("#element2").ppdrag({ zIndex: 1000 });
 * });
 * // (optional) When no longer needed, you can deactivate ppDrag.
 * $("#element2").ppdrag("destroy");
 */
(function($) {
	
	$.fn.ppdrag = function(options) {
		if (typeof options == 'string') {
			if (options == 'destroy') return this.each(function() {
				$.ppdrag.removeEvent(this, 'mousedown', $.ppdrag.start, false);
				$.data(this, 'pp-ppdrag', null);
			});
		}
		return this.each(function() {
			$.data(this, 'pp-ppdrag', { options: $.extend({}, options) });
			$.ppdrag.addEvent(this, 'mousedown', $.ppdrag.start, false);
		});
	};
	
	$.ppdrag = {
		start: function(event) {
			if (!$.ppdrag.current) {
				$.ppdrag.current = { 
					el: this,
					oleft: parseInt(this.style.left) || 0,
					otop: parseInt(this.style.top) || 0,
					ox: event.pageX || event.screenX,
					oy: event.pageY || event.screenY
				};
				var current = $.ppdrag.current;
				var data = $.data(current.el, 'pp-ppdrag');
				if (data.options.zIndex) {
					current.zIndex = current.el.style.zIndex;
					current.el.style.zIndex = data.options.zIndex;
				}
				$.ppdrag.addEvent(document, 'mouseup', $.ppdrag.stop, true);
				$.ppdrag.addEvent(document, 'mousemove', $.ppdrag.drag, true);
			}
			if (event.stopPropagation) event.stopPropagation();
			if (event.preventDefault) event.preventDefault();
			return false;
		},
		
		drag: function(event) {
			if (!event) var event = window.event;
			var current = $.ppdrag.current;
			current.el.style.left = (current.oleft + (event.pageX || event.screenX) - current.ox) + 'px';
			current.el.style.top = (current.otop + (event.pageY || event.screenY) - current.oy) + 'px';
			if (event.stopPropagation) event.stopPropagation();
			if (event.preventDefault) event.preventDefault();
			return false;
		},
		
		stop: function(event) {
			var current = $.ppdrag.current;
			var data = $.data(current.el, 'pp-ppdrag');
			$.ppdrag.removeEvent(document, 'mousemove', $.ppdrag.drag, true);
			$.ppdrag.removeEvent(document, 'mouseup', $.ppdrag.stop, true);
			if (data.options.zIndex) {
				current.el.style.zIndex = current.zIndex;
			}
			if (data.options.stop) {
				data.options.stop.apply(current.el);
			}
			$.ppdrag.current = null;
			if (event.stopPropagation) event.stopPropagation();
			if (event.preventDefault) event.preventDefault();
			return false;
		},
		
		addEvent: function(obj, type, fn, mode) {
			if (obj.addEventListener)
				obj.addEventListener(type, fn, mode);
			else if (obj.attachEvent) {
				obj["e"+type+fn] = fn;
				obj[type+fn] = function() { obj["e"+type+fn](window.event); }
				obj.attachEvent("on"+type, obj[type+fn]);
			}
		},
		
		removeEvent: function(obj, type, fn, mode) {
			if (obj.removeEventListener)
				obj.removeEventListener(type, fn, mode);
			else if (obj.detachEvent) {
				obj.detachEvent("on"+type, obj[type+fn]);
				obj[type+fn] = null;
				obj["e"+type+fn] = null;
			}
		}
		
	};

})(jQuery);