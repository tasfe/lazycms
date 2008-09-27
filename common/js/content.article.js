(function($) {
	// toggleSorts *** *** www.LazyCMS.net *** ***
	$.fn.toggleSorts = function(){
		$('#toggleSorts').slideToggle('fast',function(){changeHeight();});
		return this;
	};
	// setSelect *** *** www.LazyCMS.net *** ***
	$.fn.setSelect = function(){
		this.html('新闻,国内新闻,国际新闻,地方新闻,课件下载');
		return this;
	};
})(jQuery);
function changeHeight(){
	var e = {t:$('#toggleSorts').height(),d:$('#toggleSorts').css('display'),b:$('body').height()};
	if (e.t > e.b && e.d == 'block') {
		parent.$('#main').height(e.t+65);
	} else {
		parent.$('#main').height(e.b+7);
	}
}