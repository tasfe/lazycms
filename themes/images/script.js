$(document).ready(function(){
	$('#menu a').click(function(){
		$('#menu a').removeClass();
		$(this).addClass('active');
	});
	// 批量去除连接虚线
    $('a').focus(function(){ this.blur(); });
});