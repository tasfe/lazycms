$(function(){
	// 设置导航菜单的滑动门
	var url = self.location.href.toLowerCase();
	$('#top .right ul li').each(function(i){
		var href = $('a',this).attr('href').toLowerCase();
		if (url.indexOf(href)!=-1 && href!='/') {
			$('a',this).addClass('selected');
		} else if(url.indexOf('/faq/')!=-1 && href.indexOf('/help/')!=-1) {
			$('a',this).addClass('selected');
		} else if(url.indexOf('/demo/')!=-1 && href.indexOf('/news/')!=-1) {
			$('a',this).addClass('selected');
		}
	});
});
