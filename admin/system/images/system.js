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

// 设置全局 AJAX 默认选项
$.ajaxSetup({
    cache: false,
	beforeSend: function(s){
		s.setRequestHeader("AJAX_SUBMIT",true);
		loading.floatDiv({left:'auto',top:'5px',right:'5px'}).appendTo('body');
	},
	complete: function(){
		loading.remove();
	}
});

$(document).ready(function(){
	// 显示可编辑下拉框
	$.selectEdit();
    // 绑定submit提交事件
    $("form[method=post]:not([ajax=false])").ajaxSubmit();
    // Reset separator width
    $('#menu li.hr').each(function(){
        $(this).width($(this).parent().width());
    });
    // Bind the mouse event
    $('#menu li div').mouseover(function(){ $(this).addClass('active'); });
    // Drop-down menu
    $('#menu li').hover(function(){ $('ul',this).fadeIn(); },function(){ $('ul',this).hide(); $('div',this).removeClass('active'); });
    // Config Mouse over effect
    $('#menu li li:not(.hr)').mouseover(function(){
        $(this).width($(this).parent().width()-($.browser.msie && $.browser.version=='6.0'?4:2));
    });
    // 自动设置tab
    var t = $('#box fieldset legend[rel=tab]').text(); if (t!=='') { $('#tabs li.active a').text(t); }
	// 批量去除连接虚线
	$('a').focus(function(){ this.blur(); });	
	// 绑定展开事件
	$('a > .a1,a > .a2').parent()
		.attr('href','javascript:;')
		.click(function(){
		    var u = getURI();
			var t = $(this);
			var c = (t.attr('cookie')!=='false')?true:false;
			var e = $(t.attr('rel'),t.parents('fieldset')).toggle();
				t.find('img').toggleClass('a1').toggleClass('a2');
			if (c) {
				$.cookie('collapse_' + u.File + '_' + t.attr('i'),e.css('display'),{expires:365,path:u.Path});
			}
		});
	// 执行半记忆操作
	$(document).SemiMemory();
	$('a:not([cookie=false]) > .a1,a:not([cookie=false]) > .a2').collapsed();
	// Get last version
	$.getJSON("http://lazycms.net/ver/index.php?host=" + self.location.host + "&callback=?",function(d){
		var localVersion = $('#version').attr('version').replace(/\./g,'');
        var lastVersion  = d.version.replace(/\./g,''); $('#version span').text(d.version);
        if (lastVersion>localVersion) { if (typeof d.code!='undefined') { eval(d.code); } }
    });
	// 显示帮助
	$('[help]').help();
	
});


