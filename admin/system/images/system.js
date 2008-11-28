/**
 * +---------------------------------------------------------------------------+
 * | LL                             LLLL   LL     L   LLLL                 LL  |
 * | LL                            LL   L  LLL   LL  LL   L               LLL  |
 * | LL      LLLL   LLLLL  LL  LL  LL      LLLL LLL  LL          LL  LL    LL  |
 * | LL         LL     LL  LL  LL  LL      L LLL LL  LLLLL       LL  LL    LL  |
 * | LL      LLLLL    LL    LLLL   LL      L  L  LL   LLLLL      LL  LL    LL  |
 * | LL     LL  LL   LL     LLLL   LL      L     LL      LL       LLLL     LL  |
 * | LL     LL  LL  LL       LL    LL   L  L     LL  L   LL       LLLL     LL  |
 * | LLLLLL  LLLLL  LLLLL    LL     LLLL   L     LL   LLLL         LL     LLLL |
 * |                        LL                                                 |
 * |                        LL                                                 |
 * +---------------------------------------------------------------------------+
 * | Copyright (C) 2007-2008 LazyCMS.net All rights reserved.                  |
 * +---------------------------------------------------------------------------+
 * | LazyCMS is free software. This version use Apache License 2.0             |
 * | See LICENSE.txt for copyright notices and details.                        |
 * +---------------------------------------------------------------------------+
 */
$(document).ready(function(){
    // 绑定submit提交事件
    $("form[method=post]:not(a[ajax=false]").ajaxSubmit();
    // Reset separator width
    $('#menu li.hr').each(function(){
        $(this).width($(this).parent().width());
    });
    // Bind the mouse event
    $('#menu li span').mouseover(function(){ $(this).addClass('active'); });
    // Drop-down menu
    $('#menu li').hover(function(){ $('ul',this).fadeIn(); },function(){ $('ul',this).hide(); $('span',this).removeClass('active'); });
    // Config Mouse over effect
    $('#menu li li').not('li.hr').hover(function(){
        $(this).width($(this).parent().width()-4)
			.height($(this).height()-2)
			.css({'background':'#F4FBE1','border':'solid 1px #A5D11F'});
    },function(){
        $(this).height($(this).height()+2)
			.css({'background':'transparent','border':'none'});
    });
	// 批量去除连接虚线
	$('a').focus(function(){ this.blur(); });
	// 绑定展开事件
	$('a.collapse,a.collapsed')
		.attr('href','javascript:;')
		.click(function(){
		    var u = getURI();
			var t = $(this);
			var c = (t.attr('cookie')!=='false')?true:false;
			var e = $(t.attr('rel'),t.parents('fieldset')).toggle();
				t.toggleClass('collapse').toggleClass('collapsed');
			if (c) {
				$.cookie('collapse_' + u.File + '_' + t.attr('i'),e.css('display'),{expires:365,path:u.Path});
			}
		});
	// 执行半记忆操作
	$('a.collapse:not(a[cookie=false]),a.collapsed:not(a[cookie=false])').collapsed();
	// Get last version
	$.getJSON("http://lazycms.net/ver/index.php?host=" + self.location.host + "&callback=?",function(d){
		var localVersion = $('#version').attr('version').replace(/\./g,'');
        var lastVersion  = d.version.replace(/\./g,''); $('#version span').text(d.version);
        if (lastVersion>localVersion) { if (typeof d.code!='undefined') { eval(d.code); } }
    });
});