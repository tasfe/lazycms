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
var LazyCMS = window.LazyCMS = window.CMS = {
    // javascript libaray version
    version: '1.0',
    // 语言包对象
    L10n: {},
    // 后台根目录
    ADMIN_ROOT: '/admin/',
    // 站点根目录
    WEB_ROOT: '/',
    // Loading...
    Loading: $('<div class="loading">Loading...</div>').css({width:'90px',position:'fixed',top:'5px',right:'5px'}),
    // 初始化
    init: function() {
        // codeing...
    },
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
     * 添加事件
     *
     * @param func
     */
    addLoadEvent: function(func){
        if(typeof jQuery!="undefined") {
            jQuery(document).ready(func);
        } else if (typeof LazyCMS.init!='function') {
            LazyCMS.init = func;
        } else {
            var old_init = LazyCMS.init;
                LazyCMS.init = function(){
                    old_init(); func();
                }
        }
    },
    /**
     * 多语言翻译
     *
     * @param msgid     英文
     * @param context   上下文
     * @param module    具体模块
     */
    translate: function(msgid,context,module){
		context = context || 'common';  module  = module || null;
		var language = LazyCMS.L10n, context = language[context] || {};
		if (module) context = context[module] || {};
		result = context[msgid] || msgid;
        return result;
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
            name:'alert', title:_('Alert'),close:false,styles:{ 'max-width':'600px', 'min-width':'400px' },
            top:100, body:message,
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
            name:'confirm', title:_('Confirm'),styles:{ width:'400px' },
            top:100, body:'<div class="icon" style="background-position:0px -80px;"></div><div class="content"><h6>' + message + '</h6></div>',
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
        if ($.inArray(code, ['Success','Return'])==-1) data = null;
        return data;
    },
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
    dialog: function(options,callback) {
        // body
        var body = $('body');
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
        var target = $('div.' + opts.name,body);
            if (target.is('div')) {
                dialog = target;
            } else {
                body.append(dialog);
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
        var overflow = dialog.css('overflow'); dialog.float(opts).css({overflow:''}).show();
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
        return $.post(LazyCMS.ADMIN_ROOT + url,params,null,'json');
    }
};

window._ = LazyCMS.translate;

// 等同于PHP parse_str
function parse_str(str) {
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

// Password strength meter
function password_strength(username, password1, password2) {
	var short_pass = 1, bad_pass = 2, good_pass = 3, strong_pass = 4, mismatch = 5, symbol_size = 0, natLog, score;

	// password 1 != password 2
	if ( (password1 != password2) && password2.length > 0)
		return mismatch

	//password < 4
	if ( password1.length < 4 )
		return short_pass

	//password1 == username
	if ( password1.toLowerCase() == username.toLowerCase() )
		return bad_pass;

	if ( password1.match(/[0-9]/) )
		symbol_size +=10;
	if ( password1.match(/[a-z]/) )
		symbol_size +=26;
	if ( password1.match(/[A-Z]/) )
		symbol_size +=26;
	if ( password1.match(/[^a-zA-Z0-9]/) )
		symbol_size +=31;

	natLog = Math.log( Math.pow(symbol_size, password1.length) );
	score = natLog / Math.LN2;

	if (score < 40 )
		return bad_pass

	if (score < 56 )
		return good_pass

    return strong_pass;
}