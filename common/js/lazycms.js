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
    // 添加事件
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
    // translate
    translate: function(msgid){
        var arrMsg = msgid.split('.');
        var result = LazyCMS.L10n;
        for (var i=0;i<arrMsg.length;i++ ) {
            result = result[arrMsg[i]] || msgid;
        }
        return result;
    },
    // alert
    alert: function(message,callback,code) {
        var position,IE6_hacker;
        if (!$.isFunction(callback)) {
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
            name:'alert', title:_('common.alert'),close:false,styles:{ 'max-width':'600px', 'min-width':'400px' },
            top:100, body:message,
            buttons:[{
                focus:true,
                text:_('common.submit'),
                handler:function(opts){
                    LazyCMS.removeDialog('alert');
                    if ($.isFunction(callback)) callback();
                    return false;
                }
            }]
        },IE6_hacker);
    },
    // confirm
    confirm: function(message,callback){
        LazyCMS.dialog({
            name:'confirm', title:_('common.confirm.title'),styles:{ width:'400px' },
            top:100, body:'<div class="icon" style="background-position:0px -80px;"></div><div class="content"><h6>' + message + '</h6></div>',
            buttons:[{
                focus:true,
                text:_('common.submit'),
                handler:function(){
                    LazyCMS.removeDialog('confirm');
                    return callback.call(this,true);
                }
            },{
                text:_('common.cancel'),
                handler:function(){
                    LazyCMS.removeDialog('confirm');
                    return callback.call(this,false);
                }
            }]
        });
        return false;
    },
    // redirect
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
    // 创建遮罩层
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
                $(window).scroll(function(){ reposition() }).resize(function(){ reposition() });
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
    // 统一处理结果
    ajaxResult: function(result) {
        if ($.isPlainObject(result)) {
            var code = result.CODE, data = result.DATA;
            switch (code) {
                // 提示
                case 'Success': case 'Error': case 'Alert':
                    LazyCMS.alert(data,function(){
                        // 调用脚本
                        try { eval(result.CALL) } catch (e) {}
                    },code);
                    break;
                // 跳转
                case 'Redirect':
                    LazyCMS.redirect(data.Location,data.Time,data.Message);
                    break;
                // 处理验证异常
                case 'Validate':
                    $(document).error(data);
                    break;
                // 返回结果
                case 'Return':
                    return data;
                    break;
                // 默认返回结果对象
                default:
                    return result;
                    break;

            }
        } else {
            // show error
            LazyCMS.dialog({
                title:_('common.error'),styles:{ width:'700px' },body:result
            });
        }
    },
    // 弹出框
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
			way:'c',
            remove:function(){ LazyCMS.removeDialog(opts.name); },
            buttons:[]
        }, options||{});

        // 按钮个数
        var btnLength = opts.buttons.length;
        // 设置默认名称
        opts.name = opts.name?'lazy_dialog_' + opts.name:'lazy_dialog';
        // 定义弹出层对象
        var dialog = $('<div class="dialog window" dialog="' + opts.name + '" style="display:none;"><h1>Loading...</h1><div class="wrapper">Loading...</div></div>').css({position:'fixed'});
        var target = $('div[dialog=' + opts.name + ']',body);
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
        var styles = $.extend({overflow:'auto','z-index':$('*').maxIndex() + 1,height:'auto'},opts.styles); dialog.css(styles);

        // 设置标题
        $('h1',dialog).text(opts.title);

        // 设置内容
        if ($('div.wrapper','<div>' + opts.body + '</div>').is('div')) {
            $('.wrapper',dialog).replaceWith(opts.body);
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
		
		dialog.float(opts).css({overflow:''}).show();

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
    // 删除弹出
    removeDialog: function(name){
        var dialog = $.data(document,name);
            dialog = dialog ? dialog : $('[dialog=lazy_dialog_' + name + ']');
            dialog.remove(); LazyCMS.removeMask();
    },
    /**
     * 可编辑下拉框
     *
     * @example: <select edit="true" default="value"></select>
     */
    selectEdit: function(){
        // 重新调整位置
        $('select[edit=yes]').each(function(){
            try {
                var s = $(this); if (s.is('select')==false) { return ; }
                $(this).prev().css({width:(s.width() - 18) + 'px',top:s.position().top+'px'});
            } catch (e) {}
        });
        // 替换控件
        $('select[edit=true]').each(function(i){
            try {
                var s = $(this); if (s.is('select')==false) { return ; }
                var v = (s.attr('default')!='' && (typeof s.attr('default'))!='undefined')?s.attr('default'):s.val();
                var i = $('<input type="text" name="' + s.attr('name') + '" value="' + v + '" />')
                    .click(function(){ $(this).select(); })
                    .css({width:(s.width() - 18) + 'px',height:(s.height() - 2) + 'px',position:'absolute',top:s.position().top+'px',border:'none',margin:($.browser.msie?1:2)+'px 0 0 1px',padding:($.browser.msie?2:0)+'px 0 0 2px'})
                    .insertBefore(s);
                // 取得所有属性
                var attrs = s.getAttrs();
                
                var c = $('<select name="edit_select_' + s.attr('name') + '" edit="yes">' + s.html() + '</select>')
                    .change(function(){ $(this).prev().val(this.value);}).val(i.val());
                    if (s.attr('id')!=='') { c.attr('id',s.attr('id')); }
                    i.blur(function(){
                        c.val(this.value);
                    });
                    $.each(attrs,function(k,v){
                        if ($.inArray(k,['id','name','edit'])==-1 && v){
                            c.attr(k,v);
                        }
                    });
                    s.replaceWith(c);
            } catch (e) {}
        });
    }
};
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

window._ = LazyCMS.translate;

