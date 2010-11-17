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
 * @author  Lukin <my@lukin.cn>
 * @version $Id$
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
    };
    /**
     * 检查密码强度
     *
     * @param user
     * @param pass1
     * @param pass2
     */
    $.fn.check_pass_strength = function(user,pass1,pass2) {
        this.removeClass('short bad good strong');
        if ( ! pass1 ) {
            return this.html( _('Strength indicator') );
        }
        // Password strength meter
        var password_strength = function(username, password1, password2) {
            var short_pass = 1, bad_pass = 2, good_pass = 3, strong_pass = 4, mismatch = 5, symbol_size = 0, natLog, score;
                username = username || '';

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
        };

        var strength = password_strength(user, pass1, pass2);

        switch ( strength ) {
            case 2:
                this.addClass('bad').html( _('Weak') );
                break;
            case 3:
                this.addClass('good').html( _('Medium') );
                break;
            case 4:
                this.addClass('strong').html( _('Strong') );
                break;
            case 5:
                this.addClass('short').html( _('Mismatch') );
                break;
            default:
                this.addClass('short').html( _('Very weak') );
        }
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
                _this.unbind('submit').submit(function(){
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
        var header = form.attr('header'), method = form.attr('method'),
            url    = header && $.trim(header.substr(header.indexOf(' '))) || form.attr('action');
            method = header && $.trim(header.substring(0,header.indexOf(' '))) || method;
            
        if (url=='' || typeof url=='undefined') url = self.location.href;
        $('.table-nav',form).each(function(i){
            var _this  = $(this);
            $('button[type=button]',_this).click(function(){
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
    };
})(jQuery);