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
 *
 * LazyCMS
 *
 * @author  Lukin <my@lukin.cn>
 * @version $Id$
 */
var LazyCMS = window.LazyCMS = window.CMS = {
    // javascript libaray version
    version: '1.0',
    // 语言包对象
    L10n: {},
    // URI对象
    URI: {},
    // 后台根目录
    ADMIN: '/admin/',
    // 站点根目录
    ROOT: '/',
    // Loading...
    Loading: $('<div class="loading">Loading...</div>').css({width:'90px',position:'fixed',top:'5px',right:'5px'}),
	// 计数变量
	COUNT_VAR: {
		Loading:false,
		Masked:false,
		Float:{
			Scroll:false,
			Resize:false
		}
	},
    /**
     * ajax success
     * 
     * @param xhr
     * @param s
     */
    success: function(xhr, s, loading){
        // 接管success
        s.orisuccess = s.success;
        // 自定义success
        s.success = function(data, status, xhr) {
            if (xhr && xhr.getResponseHeader('X-Powered-By') && s.url.toLowerCase().indexOf('.php') != -1) {
                if (xhr.getResponseHeader('X-Powered-By').indexOf("\x4c\x61\x7a\x79\x43\x4d\x53") == -1) return ;
            }
            var data = LazyCMS.ajaxSuccess.apply(this,arguments);
            if (null!==data && s.orisuccess) {
                s.orisuccess.call(this, data, status, xhr);
            }
        }
        if (typeof loading == 'undefined') {
            LazyCMS.Loading.css({'z-index':$('*').maxIndex() + 1}).appendTo('body');
        }
    },
    /**
     * 多语言翻译
     *
     * @param msgid     英文
     */
    translate: function(msgid){
        return LazyCMS.L10n[msgid] || msgid;
    },
    /**
     * 模拟alert
     * 
     * @param message   消息内容，html格式
     * @param callback  点击确定之后的回调函数
     * @param code      警告类型：Success，Error，Other
     */
    alert: function(message,callback,code) {
        var position,IE6_hacker;
        if (callback && !$.isFunction(callback)) {
            code = callback;
        }
        if (code) {
            switch (code) {
                case 'Success':
                    position = 'background-position:0px 0px;';
                    break;
                case 'Error':
                    position = 'background-position:0px -40px;';
                    break;
                default:
                    position = 'background-position:0px -80px;';
                    break;
            }
            message = '<div class="icon" style="' + position + '"></div><div class="content"><h6>' + message + '</h6></div>';
        }
        // 为万恶的IE6实现 min-width max-width
        if ($.browser.msie && $.browser.version == '6.0') {
            IE6_hacker = function(){
                var cont_width = $('.content',this).width(),
                    cont_min_width = parseInt($('.content',this).css('min-width')),
                    cont_max_width = parseInt($('.content',this).css('max-width'));
                    $('.content',this).width(Math.min(Math.max(cont_width,cont_min_width),cont_max_width));
            };
        }
        LazyCMS.dialog({
            name:'alert', title:_('Alert'), close:false, styles:{ 'top':-100, 'max-width':'600px', 'min-width':'400px' },
            body:message,
            buttons:[{
                focus:true,
                text:_('Submit'),
                handler:function(opts){
                    LazyCMS.removeDialog('alert');
                    if ($.isFunction(callback)) callback();
                    return false;
                }
            }]
        },IE6_hacker);
    },
    /**
     * 模拟确认框
     *
     * @param message   消息内容
     * @param callback  回调函数
     * @example LazyCMS.confirm('message',function(r){
     *              if (r) {
     *                  coding...
     *              }
     *           });
     */
    confirm: function(message,callback){
        LazyCMS.dialog({
            name:'confirm', title:_('Confirm'),styles:{ 'top':-100, width:'400px' },
            body:'<div class="icon" style="background-position:0px -80px;"></div><div class="content"><h6>' + message + '</h6></div>',
            buttons:[{
                focus:true,
                text:_('Submit'),
                handler:function(){
                    LazyCMS.removeDialog('confirm');
                    return callback.call(this,true);
                }
            },{
                text:_('Cancel'),
                handler:function(){
                    LazyCMS.removeDialog('confirm');
                    return callback.call(this,false);
                }
            }]
        });
        return false;
    },
    /**
     * URL跳转
     *
     * @param url
     * @param time
     * @param message
     */
    redirect: function(url,time,message) {
        if (typeof url != 'undefined' && url != '') {
            url = url.replace('&amp;', '&');
            var win = top || window;
                win.location.replace(url);
        }
    },
    // 是否存在遮罩层
    isMask: function(){
        return $('div#lazy_mask').is('div');
    },
    /**
     * 创建遮罩层
     * 
     * @param options
     *          {
     *              width:宽度，默认100%,
     *              height:高度，默认100%,
     *              opacity:透明度，默认 0.7,
     *              background:遮罩层颜色，默认#fff              
     *          }
     */
    masked: function(options) {
        var isMask   = LazyCMS.isMask();
        var maskDiv  = isMask ? $('div#lazy_mask') : $('<div id="lazy_mask"></div>');
        var maxIndex = $('div#lazy_mask,div.dialog').maxIndex();

        // 设置样式
        if (!isMask) {
            // 默认设置
            var styles = $.extend({
                width:'100%',
                height:'100%',
                left:0,
                top:0,
                opacity:0.7,
                background:'#fff',
                position:'fixed',
                'z-index': (maxIndex + 1)
            }, options||{});
            $.extend(styles,{'filter':'alpha(opacity=' + (100 * styles.opacity) + ')', '-moz-opacity':styles.opacity});
            maskDiv.css(styles);
        }
        // 重置遮罩层
        else if (isMask && options) {
            if (options.opacity) {
                $.extend(options,{'filter':'alpha(opacity=' + (100 * options.opacity) + ')', '-moz-opacity':options.opacity});
            }
            maskDiv.css(options);
        }
        if (!isMask) {
            // 添加遮罩层
            $('body').append(maskDiv);
            // 窗口改变大小
            if ($.browser.msie && $.browser.version == '6.0') {
                var reposition = function(){
                    maskDiv.css({ 'position':'absolute','top':$(window).scrollTop() + 'px'});
                }
				if (!LazyCMS.COUNT_VAR.Masked) {
					$(window).scroll(reposition).resize(reposition);
					LazyCMS.COUNT_VAR.Masked = true;
				}
                // 干掉IE6那邪恶的option
                reposition(); $('div#lazy_mask').bgiframe();
            }
        }
        return maskDiv;
    },
    // 移除遮罩层
    removeMask: function() {
        if ($('div.dialog').size()==0) {
            LazyCMS.masked().remove();
        } else {
            LazyCMS.masked({'z-index':$('div.dialog').maxIndex() - 1});
        }
    },
    /**
     * 统一处理ajax返回结果
     * 
     * @param data      ajax response
     * @param status    
     * @param xhr
     */
    ajaxSuccess: function(data, status, xhr) {
        var code = xhr.getResponseHeader('X-LazyCMS-Code');
        switch (code) {
            // 提示
            case 'Success': case 'Error': case 'Alert':
                LazyCMS.alert(data,function(){
                    // 调用脚本
                    try { eval(xhr.getResponseHeader('X-LazyCMS-Eval')) } catch (e) {}
                },code);
                break;
            // 确认
            case 'Confirm':
                LazyCMS.confirm(data, function(r){
                    // 调用脚本
                    if (r) {
                        try { eval(xhr.getResponseHeader('X-LazyCMS-Submit')) } catch (e) {}
                    } else {
                        try { eval(xhr.getResponseHeader('X-LazyCMS-Cancel')) } catch (e) {}
                    }
                });
                break;
            // 跳转
            case 'Redirect':
                LazyCMS.redirect(data.Location, data.Time, data.Message);
                break;
            // 处理验证异常
            case 'Validate':
                $(document).error(data);
                break;
            // 返回结果
            case 'Return': default:
                break;
        }
        if (code && $.inArray(code, ['Success','Return'])==-1) data = null;
        return data;
    },
    /**
     * 模拟的弹出框
     * 
     * @param options
     * @param callback
     */
    dialog: function(options,callback) {
        return $('body').dialog(options,callback);
    },
    /**
     * 删除弹出框
     *
     * @param name  options.name
     */
    removeDialog: function(name){
        var dialog = $.data(document,name);
            dialog = dialog ? dialog : $('.lazy_dialog_' + name);
            dialog.remove(); LazyCMS.removeMask();
    },
    /**
     * 可编辑下拉框
     *
     * @example: <select edit="true" default="value"></select>
     */
    eselect: function() {
        $('select[edit=true]').each(function(){
            try {
                var that  = $(this); if (that.is('select')===false) return ;
                var name  = that.attr('name');
                var val   = (that.attr('default')!='' && (typeof that.attr('default'))!='undefined') ? that.attr('default') : that.val();
                var input = $('<input class="text" type="text" name="' + name + '" value="' + val + '" />');
                var width = +that.outerWidth()-20;
                    if ($.browser.msie && $.browser.version == '6.0') {
                        input.width(width);
                        input.height(that.height());
                    } else {
                        input.width(width);
                        input.height(that.outerHeight());
                    }
                    input.css({'margin-right':'2px'});
                    that.wrapAll('<span class="eselect" style="display:inline-block; width:' + (+that.outerWidth()+width+8) + 'px;"></span>');
                    that.parent().prepend(input);
                var select = $('<select name="eselect_' + name + '" edit="yes">' + that.html() + '</select>')
                    .change(function(){
                        $(this).prev().val(this.value);
                    })
                    .val(val);
                    if (that.attr('id')!=='') select.attr('id',that.attr('id'));

                var attrs  = that.getAttrs();
                    $.each(attrs,function(k,v){
                        if ($.inArray(k,['id','name','edit'])==-1 && v){
                            select.attr(k,v);
                        }
                    });
                    that.replaceWith(select);

            } catch (e) {}
        });
    },
    // 执行动作请求
    postAction: function(url,action,listid) {
        var listids = [];
        if ($.isArray(listid)) {
            listids = listid;
        } else {
            listids.push(listid);
        }
        var params = {'listids':listids};
        if ($.isPlainObject(action)) {
            params = $.extend(params,action);
        } else {
            params = $.extend(params,{action:action});
        }
        return $.post(LazyCMS.ADMIN + url,params,null,'json');
    },
    /**
     * 设置cookie
     *
     * @param name
     * @param key
     * @param val
     * @param options
     */
    setCookie: function(name,key,val,options) {
        options = options || {};
        var cookie  = $.cookie(name),
            opts    = $.extend({ expires: 365, path: LazyCMS.ROOT }, options),
            cookies = cookie===null ? {} : LazyCMS.parse_str(cookie);
        // 取值
        if (arguments.length == 2) {
            if (cookies[key]) return cookies[key];
            else return null;
        }
        // 赋值
        else {
            cookies[key] = val;
            return $.cookie(name, $.param(cookies), opts);
        }
    },
    /**
     * 取得cookie
     *
     * @param name
     * @param key
     */
    getCookie: function(name,key) {
        return LazyCMS.setCookie(name,key);
    },
    /**
     * 等同于PHP parse_str
     * 
     * @param str
     */
    parse_str: function(str) {
        var pairs = str.split('&'),params = {}, urldecode = function(s){
            return decodeURIComponent(s.replace(/\+/g, '%20'));
        };
        $.each(pairs,function(i,pair){
            if ((pair = pair.split('='))[0]) {
                var key  = urldecode(pair.shift());
                var value = pair.length > 1 ? pair.join('=') : pair[0];
                if (value != undefined) value = urldecode(value);

                if (key in params) {
                    if (!$.isArray(params[key])) {
                        params[key] = [params[key]];
                    }
                    params[key].push(value);
                } else {
                    params[key] = value;
                }
            }
        });
        return params;
    }
};

LazyCMS.URI.Host = (('https:' == self.location.protocol) ? 'https://'+self.location.hostname : 'http://'+self.location.hostname);
LazyCMS.URI.Path = self.location.href.replace(/\?(.*)/,'').replace(LazyCMS.URI.Host,'');
LazyCMS.URI.File = LazyCMS.URI.Path.split('/').pop();
LazyCMS.URI.Path = LazyCMS.URI.Path.substr(0,LazyCMS.URI.Path.lastIndexOf('/')+1);
LazyCMS.URI.Url  = LazyCMS.URI.Host + LazyCMS.URI.Path + LazyCMS.URI.File;

window._ = LazyCMS.translate;




/**
 * jQuery 扩展
 *
 * @author: Lukin<my@lukin.cn>
 * @date:   2010/1/21 17:11
 */
(function ($) {
    // 取得最大的zIndex
    $.fn.maxIndex = function(){
        var max = 0;
        this.each(function(){
            max = Math.max(max,this.style.zIndex);
        });
        return max;
    };
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
    };
    /**
     * 模拟的弹出框
     *
     * @param options   参数
     *          {
     *              name:标识,
     *              title:标题,
     *              body:内容,
     *              styles:css 样式,
     *              masked:是否需要遮罩，默认true,
     *              close:是否显示关闭按钮，默认true,
     *              float:浮动位置，默认居中，参数：c,lt,rt,lb,rb,
     *              remove:点击关闭按钮触发的事件,
     *              buttons:按钮
     *          }
     * @param callback  回调函数(dialog jquery对象,传入的 options)
     */
    $.fn.dialog = function(options,callback) {
        // this
        var _this = $(this);
        // 默认设置
        var opts = $.extend({
            title:'',
            body:'',
            styles:{},
            name:null,
            masked:true,
            close:true,
			float:'c',
			className:'dialog',
            remove:function(){ LazyCMS.removeDialog(opts.name); },
            buttons:[]
        }, options||{});

        // 按钮个数
        var btnLength = opts.buttons.length;
        // 设置默认名称
        opts.name = opts.name?'lazy_dialog_' + opts.name:'lazy_dialog';
        // 定义弹出层对象
        var dialog = $('<div class="' + opts.name + ' ' + opts.className + ' window" style="display:none;"><h1>Loading...</h1><div class="wrapper">Loading...</div></div>').css({position:'fixed'});
        var target = $('div.' + opts.name,_this);
            if (target.is('div')) {
                dialog = target;
            } else {
                _this.append(dialog);
            }
            // 添加删除事件
            dialog.removeDialog = opts.remove;
        // 添加遮罩层
        if (opts.masked) {
            LazyCMS.masked({'z-index':$('*').maxIndex() + 1});
        }

        // 添加关闭按钮
        if (opts.close) {
            if ($('.close',dialog).is('a')) {
                $('.close',dialog).click(function(){
                    dialog.removeDialog();
                });
            } else {
                $('<a href="javascript:;" class="close">Close</a>').click(dialog.removeDialog).insertAfter($('h1',dialog));
            }
        } else {
            $('.close',dialog).remove();
        }

        // 重新调整CSS
        var styles = $.extend({overflow:'','z-index':$('*').maxIndex() + 1,height:'auto'},opts.styles); dialog.css(styles);

        // 设置标题
        $('h1',dialog).text(opts.title);

        // 设置内容
        if ($('div.wrapper','<div>' + opts.body + '</div>').is('div')) {
            $('div.wrapper',dialog).replaceWith(opts.body);
        } else {
            $('.wrapper',dialog).html(opts.body + '<div class="clear"></div>');
            // 删除原来存在的按钮
            $('.buttons',dialog).remove();
        }
        // 绑定关闭
        $('[rel=close]',dialog).click(function(){
            dialog.removeDialog();
        });
        // 为万恶的IE6实现 min-width max-width
        if ($.browser.msie && $.browser.version == '6.0') {
            var width = dialog.width(),
                min_width = parseFloat(dialog.css('min-width')),
                max_width = parseFloat(dialog.css('max-width'));
            if (!isNaN(min_width)) {
                width = Math.max(width,min_width);
            }
            if (!isNaN(max_width)) {
                width = Math.min(width,max_width);
            }
            dialog.width(width);
        }
        var overflow = dialog.css('overflow');
        if (opts.float) dialog.float({float:opts.float,top:styles.top});
        dialog.css({overflow:''}).show();
        if (overflow=='auto') {
            var wrapper = $('div.wrapper',dialog),
                paddtop = parseFloat(wrapper.css('padding-top')),
                paddbottom = parseFloat(wrapper.css('padding-bottom')),
                h1_height  = $('h1',dialog).outerHeight();
                wrapper.css({overflow:'auto',width:wrapper.width(),height:dialog.height()-(paddtop+paddbottom)-h1_height});
        }

        // 添加按钮
        if (btnLength > 0) {
            $('.wrapper',dialog).after('<div class="buttons"></div>');
            for (var i=0;i<btnLength;i++) {
                var button = $('<button type="button">' + opts.buttons[i].text + '</button>');
                    // 绑定按钮事件
                    (function(i){
                        button.click(function(){
                            if ($.isFunction(opts.buttons[i].handler)) opts.buttons[i].handler.call(dialog,opts);
                            return false;
                        });
                    })(i);
                    $('.buttons',dialog).append(button);
                    // 设置按钮类型
                    opts.buttons[i].type && button.attr('type',opts.buttons[i].type) || null;
                    // 设置鼠标焦点
                    opts.buttons[i].focus && button.focus() || null;
            }
        }
        // 保存对象
        $.data(document,opts.name,dialog);
        // 执行回调函数
        if ($.isFunction(callback)) callback.call(dialog,opts);

        return dialog;
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
            elm = $('#'+this.id, wrap);
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
    };
    // 半记忆功能
    $.fn.semiauto = function() {
        var name = LazyCMS.URI.File.substr(0,LazyCMS.URI.File.lastIndexOf('.')),
            opts = { path: LazyCMS.URI.Path };
        // 下拉框处理
        $('select[cookie=true]',this).each(function(i){
            var t = $(this); t.attr('guid',i);
            var c = LazyCMS.getCookie(name + '_select', 's' + i);
            if (c !== null) {
                $('option:selected',this).attr('selected',false);
                $('option[value=' + c + ']',this).attr('selected',true);
            }
        }).change(function(){
            LazyCMS.setCookie(name + '_select', 's' + $(this).attr('guid'), this.value, opts);
        });
        // 多选处理
        $('input:checkbox[cookie=true]',this).each(function(i){
            var t = $(this); t.attr('guid',i);
            var c = LazyCMS.getCookie(name + '_checkbox', 'c' + i);
            if (c !== null) {
                this.checked = c == 'true';
            }
        }).click(function(){
            LazyCMS.setCookie(name + '_checkbox', 'c' + $(this).attr('guid'), this.checked, opts);
        });
        // 更多属性处理
        $('fieldset[cookie=true]',this).each(function(i){
            var t = $(this); t.attr('guid',i);
            var c = LazyCMS.getCookie(name + '_fieldset', 'f' + i);
            if (c !== null) {
                t.toggleClass('closed', c == 'true');
            }
        }).find('a.toggle,h3').click(function(){
            LazyCMS.setCookie(name + '_fieldset', 'f' + $(this).parents('fieldset').attr('guid'), !$(this).parents('fieldset').hasClass('closed'), opts);
        });
        return this;
    };
    /**
     * ajax 表单提交
     *
     * @param callback
     */
    $.fn.ajaxSubmit = function(callback){
        return this.each(function(){
            var _this = $(this);
                _this.submit(function(){
                    // 取消样式
                    $('.input_error,.textarea_error,.ul_error',_this).removeClass('input_error').removeClass('textarea_error').removeClass('ul_error');
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
    };
    // 绑定批量操作事件
    $.fn.actions = function(callback) {
        // 取得 action 地址
        var form   = $(this);

        // 绑定全选事件
        $('input[name=select]',form).click(function(){
            $('input[name^=list]:checkbox,input[name=select]:checkbox',form).attr('checked',this.checked);
        });
        // 表格背景变色
        $('tbody tr',form).hover(function(){
            $('td',this).css({'background-color':'#FFFFCC'});
            $('.row-actions',this).css({'visibility': 'visible'});
        },function(){
            $('td',this).css({'background-color':'#FFFFFF'});
            $('.row-actions',this).css({'visibility': 'hidden'});
        });

        var header = form.attr('header'), method = form.attr('method'),
            url    = header && $.trim(header.substr(header.indexOf(' '))) || form.attr('action');
            method = header && $.trim(header.substring(0,header.indexOf(' '))) || method;

        if (url=='' || typeof url=='undefined') url = self.location.href;
        $('.table-nav',form).each(function(i){
            var _this  = $(this);
            $('button[type=button]:not([onclick])',_this).click(function(){
                var button  = $(this), listids = [] ,action = $('select[name=actions]',_this).val(),
                // 提交方法
                submit = function(url,data) {
                    button.attr('disabled',true);
                    $.ajax({
                        dataType: 'json', url: url, data: data,
                        type: method.toUpperCase() || 'GET',
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
    };
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

        css = function(way) {
			var r = {};
			switch (way) {
				case 'c': case 'Center':
					r = {
                        left:($(window).width() - _this.outerWidth())/2 + (isNaN(+opts.left) ? 0 : opts.left),
                        top:($(window).height() - _this.outerHeight())/2 + (isNaN(+opts.top) ? 0 : opts.top)
                    };
					break;
				case 'lt': case 'LeftTop':
					r = { left:(isNaN(+opts.left) ? 0 : opts.left), top:(isNaN(+opts.top) ? 0 : opts.top) };
					break;
				case 'rt': case 'RightTop':
					r = { right:(isNaN(+opts.right) ? 0 : opts.right), top:(isNaN(+opts.top) ? 0 : opts.top) };
					break;
				case 'lb': case 'LeftBottom':
					r = { left:(isNaN(+opts.left) ? 0 : opts.left), bottom:(isNaN(+opts.bottom) ? 0 : opts.bottom) };
					break;
				case 'rb': case 'RightBottom':
					r = { right:(isNaN(+opts.right) ? 0 : opts.right), bottom:(isNaN(+opts.bottom) ? 0 : opts.bottom) };
					break;
                case 'cb': case 'CenterBottom':
					r = {
                        left:($(window).width() - _this.outerWidth())/2 + (isNaN(+opts.left) ? 0 : opts.left),
                        bottom:(isNaN(+opts.bottom) ? 0 : opts.bottom)
                    };
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
		$(window).resize(position);
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
            var prop = function(n){ return n&&n.constructor==Number?n+'px':n; },
                html = '<iframe class="bgiframe" frameborder="0" tabindex="-1" src="'+s.src+'" '+
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
})(jQuery);