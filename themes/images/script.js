$(document).ready(function(){
	$('#menu a').click(function(){
		$('#menu a').removeClass();
		$(this).addClass('active');
	});
	// 批量去除连接虚线
    $('a').focus(function(){ this.blur(); });

	$('#download label,.radioToggle').click(function(){
		var thisToggle = $(this).is('.radioToggle') ? $(this) : $(this).prev();
		var checkBox = thisToggle.prev();
			checkBox.trigger('click');
		$('.radioToggle').removeClass('checked');
		thisToggle.addClass('checked');
		return false; 
	});
});