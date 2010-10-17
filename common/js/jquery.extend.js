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
 * | Copyright (C) 2007-2008 LazyCMS.com All rights reserved.                  |
 * +---------------------------------------------------------------------------+
 * | LazyCMS is free software. This version use Apache License 2.0             |
 * | See LICENSE.txt for copyright notices and details.                        |
 * +---------------------------------------------------------------------------+
 */
/**
 * jQuery 扩展
 *
 * @author: Lukin<my@lukin.cn>
 * @date:   2010/1/21 17:11
 */
(function ($) {
    /**
     * 在光标位置插入值
     * 
     * @param val
     */
    $.fn.insertVal = function(val) {
        return this.each(function(){ 
            // IE support
            if (document.selection && document.selection.createRange){
                this.focus();
                var sel = document.selection.createRange();
                    if (sel.text) {
                        sel.text += val;
                    } else {
                        this.value += val;
                    }
            } 
            // MOZILLA/NETSCAPE support
            else if (this.selectionStart || this.selectionStart == '0') {
                var start = this.selectionStart, end = this.selectionEnd, stp = this.scrollTop;
                this.value = this.value.substring(0, start) + val + this.value.substring(end, this.value.length);
                this.focus();
                this.selectionStart = start + val.length;
                this.selectionEnd = start + val.length;
                this.scrollTop = stp;
            }
            // Other
            else {
                this.value += val;
                this.focus();
            }
        });
    }
    /**
     * 取得一个对象的所有属性
     */
    $.fn.getAttrs = function() {
        var r = {};
        if (!this.length) return r;
        if ($.browser.msie) {
            var p = /([^'"= ]+)=('[^']*'|"[^"]*"|[^'" ]+)/g,
                s = this.attr('outerHTML'),
                t = /<[^>]*>/.exec(s)[0];
            while (m = p.exec(t)) {
                r[m[1]] = m[2].replace(/^['"]|['"]$/g, '');
            }
        } else {
            $.each(this.get(0).attributes,function(i){
                r[this.name] = this.value;
            });
        }
        return r;
    }
    // 取得最大的zIndex
    $.fn.maxIndex = function(){
        var max = 0;
        this.each(function(){
            max = Math.max(max,this.style.zIndex);
        });
        return max;
    }
    /**
     * 错误处理
     * 
     * @param data
     *          [
     *              {
     *                  id:输入框name,
     *                  text:错误信息
     *              },
     *              {
     *                  id:输入框name,
     *                  text:错误信息
     *              },
     *          ]
     */
    $.fn.error = function(data) {
        var wrap = this, s = '<ul>', elm, xheLayout;
        $.each(data,function(i){
            elm = $('[name=' + this.id + ']',wrap);
            if (elm.length > 0) {
                xheLayout = elm.next().next().find('.xheLayout');
                if (elm.is('textarea') && xheLayout.is('table')) {
                    xheLayout.addClass(elm.get(0).tagName.toLowerCase() + '_error');
                } else {
                    elm.addClass(elm.get(0).tagName.toLowerCase() + '_error');
                }
            }
            s+= '<li>' + this.text + '</li>';
        });
        s+= '</ul>';
        LazyCMS.alert(s);
    }
    /**
     * 设置对象的浮动位置
     * 
     * @param opts
     *          c|Center:居中
     *          lt|LeftTop:左上角
     *          rt|RightTop:右上角
     *          lb|LeftBottom:左下角
     *          rb|RightBottom:右下角
     */
	$.fn.float = function(opts) {
		var _this = this,way = 'c',css = function(){};
		if (typeof opts == 'object') {
			way = opts.float;
		} else if (typeof opts == 'string') {
			way = opts; opts = {};
		}
		opts = $.extend({left:0,top:0,right:0,bottom:0},opts);
		
		css = function(way) {
			var r = {};
			switch (way) {
				case 'c': case 'Center':
					r = { left:($(window).width() - _this.outerWidth())/2 - opts.left,top:($(window).height() - _this.outerHeight())/2 - opts.top };
					break;
				case 'lt': case 'LeftTop':
					r = { left:opts.left, top:opts.top };
					break;
				case 'rt': case 'RightTop':
					r = { right:opts.right, top:opts.top };
					break;
				case 'lb': case 'LeftBottom':
					r = { left:opts.left, bottom:opts.bottom };
					break;
				case 'rb': case 'RightBottom':
					r = { right:opts.right, bottom:opts.bottom };
					break;
			
			}
			if ($.browser.msie && $.browser.version == '6.0') {
				var top = r.top;
				if (isNaN(parseFloat(top))) {
					top = $(window).height() - _this.outerHeight();
				}
				r = $.extend(r,{ position:'absolute',top: $(window).scrollTop() + top });
			} else {
				r = $.extend(r,{ position:'fixed' });
			}
			this.css(r);
		}
		var position = function() { css.call(_this,way) }; position();
        // 兼容IE6
        if ($.browser.msie && $.browser.version == '6.0') {
			if (!LazyCMS.COUNT_VAR.Float.Scroll) {
				$(window).scroll(position);
				LazyCMS.COUNT_VAR.Float.Scroll = true;
			}
        }
        // 绑定窗口调整事件
		
		if (!LazyCMS.COUNT_VAR.Float.Resize) {
			$(window).resize(position);
			LazyCMS.COUNT_VAR.Float.Resize = true;
		}
        return this;
	}
    /**
     * ajax 表单提交
     * 
     * @param callback
     */
    $.fn.ajaxSubmit = function(callback){
        return this.each(function(){
            var _this = $(this);
                _this.unbind('submit').submit(function(){
                    // 取消样式
                    $('.input_error,.textarea_error',_this).removeClass('input_error').removeClass('textarea_error');
                    var button = $('button[type=submit]',this).attr('disabled',true);
                    // 取得 action 地址
                    var url = _this.attr('action'); if (url==''||typeof url=='undefined') { url = self.location.href; }

                    // ajax submit
                    $.ajax({
                        cache: false, url: url, dataType:'json',
                        type: _this.attr('method') && _this.attr('method').toUpperCase() || 'POST',
                        data: _this.serializeArray(),
                        success: function(data, status, xhr){
                            if ($.isFunction(callback)) callback.call(_this,data, status, xhr);
                        },
                        complete: function(){
                            button.attr('disabled',false); LazyCMS.Loading.remove();
                        }
                    });
                    return false;
                });
        });
    }
    // 绑定批量操作事件
    $.fn.actions = function(callback) {
        // 取得 action 地址
        var form = $(this);
        var url  = form.attr('action'); if (url==''||typeof url=='undefined') { url = self.location.href; }
        $('.table-nav',form).each(function(i){
            var _this  = $(this);
            $('button[type=button]',_this).click(function(){
                var button  = $(this), listids = [] ,action = $('select[name=actions]',_this).val(),
                // 提交方法
                submit = function(url,data) {
                    button.attr('disabled',true);
                    $.ajax({
                        dataType: 'json', url: url, data: data,
                        type: form.attr('method') && form.attr('method').toUpperCase() || 'POST',
                        success: function(data){
                            if ($.isFunction(callback)) callback.call(_this,data);
                        },
                        complete: function(){
                            button.attr('disabled',false); LazyCMS.Loading.remove();
                        }
                    });
                }

                if (action=='') {
                    return LazyCMS.alert(_('Did not select any action!'),'Error');
                }

                $('input:checkbox[name^=listids]:checked',form).each(function(){
                    listids.push(this.value);
                });

                switch (action) {
                    case 'delete':
                       LazyCMS.confirm(_('Confirm Delete?'),function(r){
                           if (r) {
                               submit(url,{
                                   'action':action,
                                   'listids':listids
                               });
                           }
                       });
                       break;
                   default:
                       submit(url,{
                           'action':action,
                           'listids':listids
                       });
                       break;
                }
            });
        });
    }

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
    }
    /**
     * The bgiframe is chainable and applies the iframe hack to get
     * around zIndex issues in IE6. It will only apply itself in IE6
     * and adds a class to the iframe called 'bgiframe'. The iframe
     * is appeneded as the first child of the matched element(s)
     * with a tabIndex and zIndex of -1.
     *
     * By default the plugin will take borders, sized with pixel units,
     * into account. If a different unit is used for the border's width,
     * then you will need to use the top and left settings as explained below.
     *
     * NOTICE: This plugin has been reported to cause perfromance problems
     * when used on elements that change properties (like width, height and
     * opacity) a lot in IE6. Most of these problems have been caused by
     * the expressions used to calculate the elements width, height and
     * borders. Some have reported it is due to the opacity filter. All
     * these settings can be changed if needed as explained below.
     *
     * @example $('div').bgiframe();
     * @before <div><p>Paragraph</p></div>
     * @result <div><iframe class="bgiframe".../><p>Paragraph</p></div>
     *
     * @param Map settings Optional settings to configure the iframe.
     * @option String|Number top The iframe must be offset to the top
     *      by the width of the top border. This should be a negative
     *      number representing the border-top-width. If a number is
     *      is used here, pixels will be assumed. Otherwise, be sure
     *      to specify a unit. An expression could also be used.
     *      By default the value is "auto" which will use an expression
     *      to get the border-top-width if it is in pixels.
     * @option String|Number left The iframe must be offset to the left
     *      by the width of the left border. This should be a negative
     *      number representing the border-left-width. If a number is
     *      is used here, pixels will be assumed. Otherwise, be sure
     *      to specify a unit. An expression could also be used.
     *      By default the value is "auto" which will use an expression
     *      to get the border-left-width if it is in pixels.
     * @option String|Number width This is the width of the iframe. If
     *      a number is used here, pixels will be assume. Otherwise, be sure
     *      to specify a unit. An experssion could also be used.
     *      By default the value is "auto" which will use an experssion
     *      to get the offsetWidth.
     * @option String|Number height This is the height of the iframe. If
     *      a number is used here, pixels will be assume. Otherwise, be sure
     *      to specify a unit. An experssion could also be used.
     *      By default the value is "auto" which will use an experssion
     *      to get the offsetHeight.
     * @option Boolean opacity This is a boolean representing whether or not
     *      to use opacity. If set to true, the opacity of 0 is applied. If
     *      set to false, the opacity filter is not applied. Default: true.
     * @option String src This setting is provided so that one could change
     *      the src of the iframe to whatever they need.
     *      Default: "javascript:false;"
     *
     * @name bgiframe
     * @type jQuery
     * @cat Plugins/bgiframe
     * @author Brandon Aaron (brandon.aaron@gmail.com || http://brandonaaron.net)
     */
    $.fn.bgIframe = $.fn.bgiframe = function(s) {
        // This is only for IE6
        if ( $.browser.msie && /6.0/.test(navigator.userAgent) ) {
            s = $.extend({
                top     : 'auto', // auto == .currentStyle.borderTopWidth
                left    : 'auto', // auto == .currentStyle.borderLeftWidth
                width   : 'auto', // auto == offsetWidth
                height  : 'auto', // auto == offsetHeight
                opacity : true,
                src     : 'javascript:false;'
            }, s || {});
            var prop = function(n){return n&&n.constructor==Number?n+'px':n;},
                html = '<iframe class="bgiframe"frameborder="0"tabindex="-1"src="'+s.src+'"'+
                           'style="display:block;position:absolute;z-index:-1;'+
                               (s.opacity !== false?'filter:Alpha(Opacity=\'0\');':'')+
                               'top:'+(s.top=='auto'?'expression(((parseInt(this.parentNode.currentStyle.borderTopWidth)||0)*-1)+\'px\')':prop(s.top))+';'+
                               'left:'+(s.left=='auto'?'expression(((parseInt(this.parentNode.currentStyle.borderLeftWidth)||0)*-1)+\'px\')':prop(s.left))+';'+
                               'width:'+(s.width=='auto'?'expression(this.parentNode.offsetWidth+\'px\')':prop(s.width))+';'+
                               'height:'+(s.height=='auto'?'expression(this.parentNode.offsetHeight+\'px\')':prop(s.height))+';'+
                        '"/>';
            return this.each(function() {
                if ( $('> iframe.bgiframe', this).length == 0 )
                    this.insertBefore( document.createElement(html), this.firstChild );
            });
        }
        return this;
    };
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
    $.cookie = function(name, value, options) {
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
})(jQuery);


