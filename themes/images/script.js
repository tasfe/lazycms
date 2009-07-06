$(document).ready(function(){
	// 顶部菜单滑动门
	$('#menu a').click(function(){
		$('#menu a').removeClass();
		$(this).addClass('active');
	});
	// 批量去除连接虚线
    $('a').focus(function(){ this.blur(); });
	// 绑定下载单选事件
	$('#download label,.radioToggle').click(function(){
		var thisToggle = $(this).is('.radioToggle') ? $(this) : $(this).prev();
		var checkBox = thisToggle.prev();
			checkBox.trigger('click');
		$('.radioToggle').removeClass('checked');
		thisToggle.addClass('checked');
		return false; 
	});
});

// 扩展函数
(function ($) {
	$.fn.lazy_updates = function(data){
		var s = '';
		this.html('<li><img class="os" src="' + common() + '/images/loading.gif" />Loading...</li>');
		for (var i=0;i<5;i++) {
			if (typeof data[i]=='undefined') break;
			s+= '<li><p><a href="' + data[i].detail + '" class="revision" target="_blank">r' + data[i].revision + '</a>' + data[i].updated + '</p><p class="comments">' + data[i].content + '</p></li>';
		}
		this.html(s);
		return this;
	}
})(jQuery);