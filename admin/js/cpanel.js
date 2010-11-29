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
 */
function cpanel_init() {
    // 记忆展开
    $('#admin-content').semiauto();
    // 表格背景变色
    $('.container .comment').hover(function(){
        $(this).css({'background-color':'#FFFFCC'});
        $('.row-actions',this).css({'visibility': 'visible'});
    },function(){
        $(this).css({'background-color':'#FFFFFF'});
        $('.row-actions',this).css({'visibility': 'hidden'});
    });
	// 绑定展开事件
	$('fieldset').each(function(i){
	    var fieldset = $(this);
	    $('a.toggle,h3',this).click(function(){
	        fieldset.toggleClass('closed');
	    });
	});
    // 取得新版本
    $.getJSON('http://lazycms.com/version.php?callback=?', {lang:$.cookie('language'), version:$('fieldset .server-env .version').text()}, function(r){
        $('fieldset .server-env .latest').html('<label>' + _('Latest Version:') + '</label>' + r);
    });
}