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
function common(){ return $("script[src*=js/jquery.js]").attr("src").replace(/(\/js\/jquery\.js\?(.*))/i,'');}
function lock(p1){ return p1 ? icon('a3') : icon('a4'); }
function cklist(p1){ return '<input name="list['+p1+']" id="list_'+p1+'" type="checkbox" value="'+p1+'"/>'; }
function icon(n,a,on){
	on = typeof(on)=='undefined'?'':on;
	if (on.substring(0,1) == '_') {
		on = ' target="' + on + '"';
	} else {
		on = ' onclick="' + on + '"';
	}
    var IMG = '<img class="os ' + n +'" src="' + common() + '/images/white.gif" />';
    var HREF = '<a href="' + a + '"' + on + '>' + IMG + '</a>';
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
// 将秒格式化为日期格式
function formatTime(time){
	var T = parseFloat(time);
	var H = Math.floor(T / (60 * 60));
	var M = Math.floor(T / 60) - H * 60;
	var S = Math.floor(T - (M * 60) - H * 60 * 60);
	return (H + ':' + M + ':' + S).replace(/\b(\w)\b/g, '0$1');
}
// loading加载条
window.loading = $('<div class="loading"><img class="os" src="' + common() + '/images/loading.gif" />Loading...</div>').css({width:'100px',position:'fixed',top:'5px',right:'5px'});
// IE6.0下的动作
if ($.browser.msie && $.browser.version == '6.0') {
	var load_move = function(){
		window.loading.css({
			position:'absolute',
			top:(document.documentElement.scrollTop + 5) + 'px',
			left:(document.documentElement.clientWidth - 100 - 20) + 'px'
		});
	};
	$(window).scroll(function(){load_move()}).resize(function(){load_move()});
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
	// 读取语言包
	$.t = function(p1){
		var R;
		try	{
			R = Language[p1];
		} catch (e) {
			R = p1;
		}
		return R;
	}
	// 缩放图片
	$.fn.bbimg = function(w,h){
		var scale = Math.min(w/this.width(),h/this.height());
		if (this.width() > w) {
			this.width(this.width()*scale);
		}
		if (this.height() > h) {
			this.height(this.height()*scale);
		}
		return this;
	}
    /**
     * 全选/反选
     *
     * @param string    p1   true:全选,false:
     */
    $.fn.checkALL = function(p1){
        p1 = p1||false;
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
    // 取得最大的zIndex
    $.fn.getMaxzIndex = function(){
		var max = 0;
		this.each(function(){
			max = Math.max(max,this.style.zIndex);
		});
		return max;
    }
    // 创建遮罩层
    $.fn.mask = function(style){
        var s = this.selector==''?$('body'):this;
        var m = $('<div class="mask" rel="mask"></div>');
        var z = $('.mask,.dialogUI').getMaxzIndex();
        // 默认设置
        style = $.extend({
            width:'100%',//Math.max(s.width(),$(document).width()) + 'px',
            height:'100%',//Math.max(s.height(),$(document).height()) + 'px',
            left:s.position().left + 'px',
            top:s.position().top + 'px',
            opacity:0.4,
            background:'#000000',
            position:'fixed',
            'z-index': (z + 1) * 200
        }, style||{});
        // 设置透明度
        $.extend(style,{'filter':'alpha(opacity=' + (100 * style.opacity) + ')', '-moz-opacity':style.opacity});
		// 窗口改变大小
		if ($.browser.msie && $.browser.version == '6.0') {
			var mMove = function(){
				m.css({ 'position':'absolute','top':document.documentElement.scrollTop + 'px'});
			}
			$(window).scroll(function(){mMove()}).resize(function(){mMove()});
		}
        // 设置样式
        m.css(style);
        // 添加遮罩层
        m.appendTo(s);

        return this;
    }
    /*
    */
	$.dialogUI = function(opts,callback){
		return $('body').dialogUI(opts,callback);
	};
    $.fn.dialogUI = function(opts,callback){
        var s = this;
        // 默认设置
        opts = $.extend({
            title:'',
            body:'',
            style:{},
            name:null,
			mask:true,
			close:true,
            remove:function(){
				dialog.remove(); $('.dialogUI[name=help]').remove();
                if ($('.dialogUI',s).size()==0) {
                    $('[rel=mask]',s).remove();
                } else {
                    $('[rel=mask]',s).css('z-index',$('.mask,.dialogUI').getMaxzIndex() - 1);
                }
            },
            buttons:[]
        }, opts||{});

        // 定义弹出层对象
        var dialog = $('<div class="dialogUI" style="display:none;"><div class="dialogBox"><div class="head"><strong>Loading...</strong></div><div class="body">Loading...</div></div></div>').css({position:'fixed'});
            opts.name==null?null:dialog.attr('name',opts.name);

        var target = $('.dialogUI[name=' + opts.name + ']',s);
            dialog = target.size()==0 ? dialog.appendTo(s) : target;

        // 添加遮罩层
		if (opts.mask) {
			if ($('[rel=mask]',s).size()==0) {
				$(this).mask();
			} else {
				$('[rel=mask]',s).css('z-index',$('.mask,.dialogUI').getMaxzIndex());
			}
		}

        // 添加关闭按钮
        $('.dialogBox > .head > a[rel=close]',dialog).remove();
        if (opts.close) {
            var close = $('<a href="javascript:;" rel="close"></a>').click(function(){
                opts.remove.call(dialog);
                return false;
            });
            close.insertAfter($('.dialogBox > .head > strong',dialog));
        }

        // 重新调整CSS
        var style = $.extend({overflow:'auto','z-index':$('.mask,.dialogUI').getMaxzIndex() + 1,background:'#FFFFFF',height:'auto'},opts.style); dialog.css(style);

		// 设置标题
        $('.dialogBox > .head > strong',dialog).text(opts.title);
        // 设置内容
        $('.dialogBox > .body',dialog).html(opts.body);
		if (style.overflow=='auto') {
			$('.dialogBox > .body',dialog).css({height:dialog.height() - 35 + 'px'});
		}
		// 定义窗口移动事件
		var dialogMove = function(opts){
			var top = parseInt(Math.min($('[rel=mask]',s).height(),document.documentElement.clientHeight)/2 - dialog.height()/2);
			// 设置CSS
			var CSS = {
				top:(typeof(style.top)=='undefined'?($.browser.msie && $.browser.version == '6.0'?document.documentElement.scrollTop + top:top):style.top) + 'px',
				left:(typeof(style.left)=='undefined'?parseInt($('[rel=mask]',s).width()/2 - dialog.width()/2):style.left) + 'px'
			};
			dialog.css($.extend(CSS,opts));
		}
		// 调整位置
		if ($.browser.msie && $.browser.version == '6.0') {
			dialog.css({position:'absolute'}); $(window).scroll(function(){dialogMove()});
		}
		$(window).resize(function(){dialogMove()});
		// 显示弹出层
		dialogMove({overflow:''}); dialog.show();

        // 添加按钮
        $('.dialogBox > .buttons',dialog).remove();
        if (opts.buttons.length > 0) {
            $('.dialogBox > .body',dialog).after('<div class="buttons"></div>');
            for (var i=0;i<opts.buttons.length;i++) {
                var button = $('<button type="button">' + opts.buttons[i].text + '</button>');
                    (function(i){
                        button.click(function(){
                            opts.buttons[i].handler.call(opts);
                            return false;
                        });
                    })(i);
                    button.appendTo($('.dialogBox > .buttons',dialog));
					typeof(opts.buttons[i].type) != 'undefined'?button.attr('type',opts.buttons[i].type):null;
					typeof(opts.buttons[i].focus) != 'undefined'?
						opts.buttons[i].focus?button.focus():null:
						null;
            }
        }
		if ($.isFunction(callback)) {
			callback.call(opts,dialog);
		}
        return this;
    };
    // alert
    $.alert = function(message,callback,type){
		type = type||'ALERT';
		var position;
        switch (type) {
            case 'SUCCESS':
                position = 'background-position:0px 0px;';
                break;
            case 'ERROR':
                position = 'background-position:0px -40px;';
                break;
            default:
                position = 'background-position:0px -80px;';
                break;
        }
		$.dialogUI({
            name:'alert', title:$.t('alert'),close:false,style:{width:'400px'},
            body:'<div class="icon" style="' + position + '"></div><div class="content"><h3>' + message + '</h3></div>',
            buttons:[{
                focus:true,
                text:$.t('submit'),
                handler:function(){
					if ($.isFunction(callback)) {callback();}
					this.remove(); return false;
                }
            }]
        });
    }
    // confirm
    $.confirm = function(message,callback){
		callback = callback||function(){};
        $.dialogUI({
            name:'confirm', title:$.t('confirm'),style:{width:'400px'},
            body:'<div class="icon" style="background-position:0px -80px;"></div><div class="content"><h3>' + message + '</h3></div>',
            buttons:[{
                focus:true,
                text:$.t('submit'),
                handler:function(){
                    callback(true); this.remove();
                }
            },{
                text:$.t('cancel'),
                handler:function(){
                    callback(false); this.remove();
                }
            }]
        });
        return false;
    }
    // 跳转
    $.redirect = function(url){
        if (typeof url != 'undefined' && url != '') {
            self.location.href = url;
        }
    }
    // ajax连接
	$.fn.ajaxLink = function(act,item){
		$(this.parents('form')).ajaxButton({
		    act:act,
		    params:{lists:item}
		});
	}
	// ajax按钮
    $.fn.ajaxButton = function(opts){
        // 默认设置
        _opts = $.extend({
            act:null,
            url:null,
            params:{}
        }, opts||{});
        var frm = this;
        var act = typeof(opts)=='string'?opts:_opts.act;
        var url = _opts.url||frm.attr('action');
        if (act!='' || act!='-') {
            act = escape(act);
        }
        switch (act) {
            case 'delete':
                $.confirm(lazy_delete,function(r){
                    r?ajaxPost(act):false;
                });
                break;
            case 'clear':
                $.confirm(lazy_clear,function(r){
                    r?ajaxPost(act):false;
                });
                break;
            default:
                ajaxPost(act);
                break;
        }
        function ajaxPost(act){
            var ids = '';
            $('input:checked',frm).each(function(){
    			if(ids==''){
    				ids = this.value;
    			}else{
    				ids+= ',' + this.value;
    			}
    		});
    		var params = $.extend({'submit':act,'lists':ids},_opts.params||{});
            $.post(url,params,function(data){
				$.result(data);
            });
        }
    }
    /**
     * ajax Submit
     */
    $.fn.ajaxSubmit = function(callback){
        return this.each(function(){
            var This = $(this);
                This.unbind('submit').submit(function(){
                    // 先释放绑定的所有事件，清除错误样式
                    $('[rel=editerror]').remove();$('[error]').unbind().removeAttr('error').removeClass('error');
                    var button = $('button[type=submit]',this);
                        button.attr('disabled',true);
                    // 取得 action 地址
                    var url = This.attr('action'); if (url==''||typeof url=='undefined') { url = self.location.href; }
                    // 设置编辑器内容
                    if (typeof(tinyMCE) != 'undefined') {
                        $(tinyMCE.editors).each(function(){
                            $('#' + this.content.id).val(this.content.getContent());
                        });
                    }
                    // ajax submit
                    $.ajax({
                        cache: false, url: url,
                        type: This.attr('method').toUpperCase(),
                        data: This.serializeArray(),
                        success: function(data){
                            if (JSON = $.result(data)) {
								if ($.isFunction(callback)) { callback(JSON); }
                            }
                        },
                        complete: function(){
                            button.attr('disabled',false);
                            // 重载了此方法，所以必须要删除loading条
                            window.loading.remove();
                        }
                    });
                    return false;
                });
        });
    }
    // 错误处理
    $.result = function(data){
        var JSON = {};
        if (JSON = $.parseJSON(data)) {
            switch (JSON.CODE) {
                case 'VALIDATE': // 验证提示
                    var len = JSON.DATA.length;
                    for (var i=0;i<len;i++) {
                        if (typeof tinyMCE != 'undefined') {
                            if (typeof tinyMCE.get(JSON.DATA[i].id) != 'undefined') {
                                $('#' + JSON.DATA[i].id + '_ifr')
                                    .unbind().attr('error',JSON.DATA[i].text)
                                    .after('<div rel="editerror" style="width:100%;height:3px; background:#FFFFFF url(' + common() + '/images/invalid-line.gif) repeat-x left bottom !important;">&nbsp;</div>');
                            } else {
                                $('[name=' + JSON.DATA[i].id + ']').unbind().attr('error',JSON.DATA[i].text).addClass('error');
                            }
                        } else {
                            $('[name=' + JSON.DATA[i].id + ']').unbind().attr('error',JSON.DATA[i].text).addClass('error');
                        }
                    }
                    $('[error]').jTips();
                    break;
                case 'SUCCESS': case 'ERROR': case 'ALERT': // 几种提示
                    $.alert(JSON.DATA.MESSAGE,function(){
                        $.redirect(JSON.DATA.URL);
                    },JSON.CODE);
                    break;
                case 'REDIRECT':// 跳转
                    $.redirect(JSON.DATA.URL);
                    break;
				case 'PROCESS':
					// 定义常用变量
					var D       = JSON.DATA;
					var DATAS   = D.DATAS;
					var PARAM   = D.PARAM;
					var percent = DATAS.OVER / DATAS.TOTAL;
					var IS_CONTINUE = (DATAS.TOTAL==0 && DATAS.ALONE > 0)?false:true;
					// 重定义 [文档总数]和[已生成文档数]，防止出现0整除现象
					if (DATAS.OVER==0 && DATAS.TOTAL==0) { DATAS.TOTAL = DATAS.OVER = 1; }
					// 当前进度条对象
					var SG_PROCESS = $('#' + PARAM.lists);
					// 进度列表对象
					var DL_PROCESS = $('#process');
					// 进度列表里面的进度条个数
					var DD_PROCESS = $('dd',DL_PROCESS).size();
					// 计算出剩余时间
					var LeftTime = (DATAS.TOTAL - DATAS.OVER) * (DATAS.USED/DATAS.OVER);
						LeftTime = $.t('process/lefttime') + ' ' + (DATAS.USED < 1 ? $.t('process/unknown') : formatTime(LeftTime));
					// 当前进度条已添加到页面
					if (SG_PROCESS.is('dd')) {
						// 只刷新进度
						$('.process > div',SG_PROCESS).css({width:(percent*180).toFixed(2) + 'px'});
						$('.process > span',SG_PROCESS).text(LeftTime);
					} else {
						// 创建进度条
						SG_PROCESS = $('<dd id="' + PARAM.lists + '" style="display:none;"><div class="process"><div style="width:' + (percent*180).toFixed(2) + 'px"></div><span>' + LeftTime + '</span></div></dd>');
						DL_PROCESS.append(SG_PROCESS);
						var c = $.cookie('AJAX_PROCESS');
						if (c=='none' || c==null) {
							$('#process > dd').Minimized('none');
							//SG_PROCESS.slideDown();
						}
						// 设置显示00
						if (!IS_CONTINUE) {
							$('.process > span',SG_PROCESS).text($.t('process/lefttime') + ' ' + formatTime(0));
						}
						// 显示进度列表
						if (parseInt(DATAS.ALONE) == 0) {
							DL_PROCESS.slideDown();
						}
					}
					// 当前进度没有执行完
					if (parseInt(DATAS.OVER) < parseInt(DATAS.TOTAL)) {
						// 保证只有一条进程
						if (parseInt(DATAS.ALONE) > 0) {
							window.PROCESS.push(D); break;
						}
						// 循环提交执行进度
						$.ajax({
							type:'POST',
							url:D.ACTION,
							data:PARAM,
							beforeSend: function(s){},
							success:function(data){
								$.result(data);
							}
						});
					} else {
						// 进度已经执行完毕，1.5秒后 移除进度条
						setTimeout(function(){
							// 移除当前进度条
							SG_PROCESS.remove();
							// 继续执行队列
							if (IS_CONTINUE) { $.execProcess(); }
							// 判断进度列表里面是否还有为执行进度，没有则隐藏进度列表
							if (parseInt($('dd',DL_PROCESS).length)==0) {
								DL_PROCESS.slideUp('fast');
							}
						},1500);
					}
					break;
				default:
					return JSON.DATA;
					break;
            }
        } else {
            // 格式不符合，则出现错误
			$.dialogUI({name:'error',style:{width:'750px',height:'400px',overflow:'auto'}, title:$.t('error'), body:data});
        }
		return false;
    };
    /**
     * 气泡提示
     */
    $.fn.jTips = function(){
        return this.each(function(){
            $(this).hover(function(el){
                var jTip = $('body').append('<div class="jTip"><div class="jTip-body"></div><div class="jTip-foot"></div></div>').find('.jTip');
                var jHeight = jTip.height();
                var jObject = $(this);
                var jOffset = {left:0,top:document.documentElement.scrollTop};
                if ($(this).is('iframe')) {
                    jOffset = $(this).offset();
                    jObject = $(this).contents();
                }
				jTip.css({'top':((el.clientY + jOffset.top) - jHeight - 20 ) + 'px','left':(el.clientX + jOffset.left + 10) + 'px','z-index':$('.mask,.dialogUI').getMaxzIndex() + 1});
                jObject.mousemove(function(e){
                    jTip.css({'top':((e.clientY + jOffset.top) - jHeight - 20 ) + 'px','left':(e.clientX + jOffset.left + 10) + 'px','z-index':$('.mask,.dialogUI').getMaxzIndex() + 1});
                });
                jTip.fadeIn('fast').find('.jTip-body').html($(this).attr('error'));
            },function(){
                $('.jTip').remove();
            });
        });
    };
    // 获取分词
    $.fn.getKeywords = function(id){
        var t = this; t.val('Loading...');
        var v = $(id).val();
        if (v=='') {
            t.val('');
            return this;
        }
        $.post(common() + '/modules/system/gateway.php',{action:'keywords',title:v},function(d){
            t.val(d);
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
    $.parseJSON = function(s){
        if (!/^("(\\.|[^"\\\n\r])*?"|[,:{}\[\]0-9.\-+Eaeflnr-u \n\r\t])+?$/.test(s)) { return false; }
        try {
            return eval('(' + s + ')');
        } catch (ex) {
            // Ignore
            return false;
        }
    };
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
    // 折叠
    $.fn.collapsed = function(){
        var u = getURI();
        this.each(function(i){
            var t = $(this).parent(); t.attr('i',i);
            var r = $(t.attr('rel'),t.parents('fieldset'));
            var c = $.cookie('collapse_' + u.File + '_' + i);
            switch (c) {
                case 'block':
                    $('img',t).removeClass('a1').addClass('a2');
                    r.show();
                    break;
                case 'none':
                    $('img',t).removeClass('a2').addClass('a1');
                    r.hide();
                    break;
                default:
                    if ($('img',t).hasClass('a1')) {
                        r.hide();
                    } else {
                        r.show();
                    }
                    break;
            }
        });
        return this;
    }
    // 半记忆操作
    $.fn.SemiMemory = function(){
        var u = getURI();
        // checkbox
        $('input:checkbox[cookie=true]',this).each(function(i){
            var c = $.cookie('checkbox_' + u.File + '_' + $(this).attr('id'));
            if (c!==null) {
                this.checked = (c=='true') ? true : false;
            }
        }).click(function(){
            $.cookie('checkbox_' + u.File + '_' + $(this).attr('id'),this.checked,{expires:365,path:u.Path});
        });
        return this;
    }
    /**
     * 可编辑下拉框
     *
     * @example: <select edit="true" default="value"></select>
     */
    $.selectEdit = function(){
        // 重新调整位置
        $('select[edit=yes]').each(function(){
            try {
                var s = $(this); if (s.is('select')==false) { return ; }
                $(this).prev().css({top:s.position().top+'px'});
            } catch (e) {}
        });
        // 替换控件
        $('select[edit=true]').each(function(){
            try {
                var s = $(this); if (s.is('select')==false) { return ; }
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
    // Explorer
    $.fn.Explorer = function(path,exts){
        var This = this; var field = this.selector; path = path || '/'; exts = exts || '*';
        $.post(common() + '/modules/system/gateway.php',{action:'explorer',path:path,field:field,exts:exts},function(data){
            if (JSON = $.result(data)) {
                $.dialogUI({name:'explorer',style:{width:'600px',overflow:'hidden'},title:JSON.TITLE, body:JSON.BODY},function(s){
                    var dialog = this;
                    $('td[rel=preview]',s).click(function(){
						$('td[rel=preview]',s).find('div').andSelf().css({background:'#FFF',color:'#333333'});
						$(this).find('div').andSelf().css({background:'#316AC5',color:'#FFF'});
                        // 显示加载图片
                        window.loading.css({'z-index':$('.mask,.dialogUI').getMaxzIndex() + 1}).appendTo('body');
                        var src = $(this).attr('src');
                        var img = new Image();
                            img.src = src;
                            img.onload = function(){
                                var width = Math.min($(document).width()*0.9,img.width);
                                $.dialogUI({ name:'preview',style:{width:Math.max(150,width+25)},title:$.t('picture/preview'),body:'<img src="' + src + '" width="' + width + '" alt="' + src + '" />'},function(s){
                                    var dialog = this; window.loading.remove();
                                    $('div.body',s).css({'text-align':'center'}).click(function(){
                                        dialog.remove();
                                    });
                                });
                            }
                    });
                    $('td a[rel=insert]',s).click(function(){
                        var src = $(this).attr('src');
                        var id  = field.replace('#','');
                        if (typeof tinyMCE != 'undefined' && typeof tinyMCE.get(id) != 'undefined') {
							var ext = src.substr(src.lastIndexOf('.') + 1).toLowerCase(); var html = '';
							if ($.inArray(ext,['bmp','gif','jpg','jpeg','png'])!=-1) {
								html = '<img src="' + src + '" />';
							} else {
								html = '<a href="' + src + '">' + src.substr(src.lastIndexOf('/') + 1) + '</a>';
							}
                            tinyMCE.get(id).execCommand('mceInsertContent', false, html); dialog.remove();
                        } else {
                            This.val(src); dialog.remove();
                        }
                        return false;
                    });
                    $('td a[rel=delete]',s).click(function(){
                        var src = $(this).attr('src');
                        $.confirm($.t('confirm/delete'),function(r){
                            if (r) {
                                // 删除图片
                                $.post(common() + '/modules/system/gateway.php',{action:'explorer_delete',file:src},function(data){
                                    if ($.result(data)) {
                                        This.Explorer(path,exts);
                                    }
                                });
                            }
                        });
                    });
                });
            }
        });
        return this;
    }
    // 创建文件夹
    $.fn.CreateFolder = function(p,e){
        var This = this;
        $.get(common() + '/modules/system/gateway.php',{action:'explorer_create',path:p},function(data){
            if (JSON = $.result(data)) {
                $.dialogUI({
                    name:'CreateFolder',title:JSON.TITLE, body:JSON.BODY,
                    style:{width:'350px',overflow:'hidden'}
                },function(s){
                    var t = this;
                    $('[rel=cancel]',s).click(function(){
                        t.remove();
                    });
                    $('[help]').help();
                    $('form',s).ajaxSubmit(function(r){
                        if (r) {
                            This.Explorer(p,e);
                        }
                    });
                });
            }
        });
    }
    // 上传文件
    $.fn.UpLoadFile = function(p,e){
        var field = this.selector;
        $.post(common() + '/modules/system/gateway.php',{action:'explorer',path:p,field:field,exts:e,CMD:'upload'},function(data){
            if (JSON = $.result(data)) {
                $.dialogUI({name:'explorer',style:{width:'600px',overflow:'hidden'},title:JSON.TITLE, body:JSON.BODY});
            }
        });
    }
    // 帮助提示
    $.fn.help = function(s){
        if (typeof s != 'undefined') {
            var t = this;
            $('img',t).attr('src',common() + '/images/loading.gif').removeClass('h5');
            $.post(common() + '/modules/system/gateway.php',{action:'help',module:MODULE,path:s},function(data){
                if (JSON = $.result(data)) {
                    $.dialogUI({name:'help',style:{width:'600px',overflow:'hidden'}, title:JSON.TITLE, body:JSON.BODY});
                }
                $('img',t).attr('src',common() + '/images/white.gif').addClass('h5');
            });
        } else {
            return this.each(function(){
                var p = $(this).attr('help');
                $(this).siblings('a[rel=help]').remove();
                $('<a href="javascript:;" rel="help"><img class="h5 os" src="../system/images/white.gif" /></a>').click(function(){
                    var img = $('img',this).attr('src',common() + '/images/loading.gif').removeClass('h5');
                    $.post(common() + '/modules/system/gateway.php',{action:'help',module:MODULE,path:p},function(data){
                        img.attr('src',common() + '/images/white.gif').addClass('h5');
                        if (JSON = $.result(data)) {
                            var pos = img.offset();
                                if ((img.offset().left + 20 + 350) > $(document).width()) {
                                    pos.left = pos.left - 2 - 350;
                                } else {
                                    pos.left = pos.left + 20;
                                }
                            $('.dialogUI[name=help]').remove();
                            $.dialogUI({
                                mask:false, name:'help',
                                title:JSON.TITLE, body:JSON.BODY,
                                style:{width:'350px',overflow:'hidden',position:'absolute',top:pos.top + 8,left:pos.left}
                            });
                        } else {
                            return ;
                        }
                    });
                }).insertAfter(this);
                $('a').focus(function(){ this.blur(); });
            });
        }
    }
	// 最小化进程列表
	$.fn.Minimized = function(s){
		var u = getURI(); var d = s?s:this.css('display');
		if (d=='none' || d==null) {
			$('a',this.prev()).addClass('revert');
			this.slideDown();
		} else {
			$('a',this.prev()).removeClass('revert');
			this.slideUp();
		}
		$.cookie('AJAX_PROCESS',d,{expires:365,path:'/'});
	}
	// 任务进程
	$.fn.process = function(){
		var This = this; var LeftTime = 0;
		var process = $('<dl id="process"><dt><strong>' + $.t('process/title') + '</strong><a href="javascript:;" onclick="$(\'#process > dd\').Minimized();"></a></dt></dl>').css({display:'none'});
		$.ajax({
		    type:'POST',
		    url:common() + '/modules/system/gateway.php',
		    data:{action:'create',submit:'process'},
		    beforeSend: function(){},
		    success:function(data){
		        if (JSON = $.result(data)) {
    				// 有数据，创建进程列表
    				var length = JSON.length;
    				if (length) {
    					var percent = 0; var D = {};
    					for (var i=0;i<length;i++) {
    						// 追加到队列
    						D = JSON[i]; window.PROCESS.push(D);
    						// 任务没有达到100%的情况下才显示
    						if (parseInt(D.DATAS.OVER) < parseInt(D.DATAS.TOTAL)) {
    							// 防止0整除
    							if (D.DATAS.OVER==0 && D.DATAS.TOTAL==0) { D.DATAS.TOTAL = D.DATAS.OVER = 1; } percent = D.DATAS.OVER/D.DATAS.TOTAL;
    							// 计算出剩余时间
    							LeftTime = (D.DATAS.TOTAL - D.DATAS.OVER) * (D.DATAS.USED/D.DATAS.OVER);
    							LeftTime = $.t('process/lefttime') + ' ' + (D.DATAS.USED < 1 ? $.t('process/unknown') : formatTime(LeftTime));
    							process.append('<dd id="' + D.PARAM.lists + '" style="display:none;"><div class="process"><div style="width:' + (percent*180).toFixed(2) + 'px"></div><span>' + LeftTime + '</span></div></dd>');
    						}
    					}
    					// 有任务，显示
    					if ($('dd',process).size()>0) {
    						// 判断进度列表是否最小化
    						var c = $.cookie('AJAX_PROCESS');
    						if (c=='none' || c==null) {
    							$('#process > dd').Minimized();
    						}
    						process.slideDown();
    						// 执行进程队列
    						$.execProcess();
    					}
    				}
    			}
		    }
		});
		// 去除虚线
		$('a',process).focus(function(){ this.blur(); });
		// 添加到页面
		process.appendTo(This);
		// 添加IE6事件
		if ($.browser.msie && $.browser.version == '6.0') {
			var processMove = function(){
				process.css({
					'position':'absolute',
					'top':(document.documentElement.scrollTop + document.documentElement.clientHeight - process.get(0).clientHeight - 5) + 'px',
					'left':(document.documentElement.clientWidth - process.get(0).clientWidth - 5) + 'px'}
				);
			}
			$(window).scroll(function(){processMove()}).resize(function(){processMove()});
		}
		return this;
	}
	// 执行进程
	$.execProcess = function(){
		// 先删除进度到100%的任务
		var DATA = window.PROCESS.shift();
		if (typeof DATA == 'undefined') { return ; }
		// 执行进程
		$.ajax({
		    type:'POST',url:DATA.ACTION,data:DATA.PARAM,
		    beforeSend: function(){},
		    success:function(data){
		        $.result(data);
		    }
		});
	}
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

// 设置系统CSS
$.setStyle();

// 加载语言包
window.Language = new Array(); var language = $.cookie('language');
if (language==null) { language = 'zh-cn';} LoadScript('lang.' + language);
