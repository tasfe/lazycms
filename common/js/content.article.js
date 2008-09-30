(function($) {
	// toggleSorts *** *** www.LazyCMS.net *** ***
	$.toggleSorts = function(){
		$('#toggleSorts').slideToggle('fast',function(){$.changeHeight();});
		return this;
	};
	// setSorts *** *** www.LazyCMS.net *** ***
	$.fn.setSorts = function(){
		this.selectSorts();
	    $('#toggleSorts').slideToggle('fast',function(){
	        $.changeHeight();
	    });
		return this;
	};
	// selectSorts *** *** www.LazyCMS.net *** ***
	$.fn.selectSorts = function(){
	    var sorts = new Array();
	    $('#toggleSorts input:checked').each(function(i){
            sorts[i] = $(this).next().text();
	    });
	    var text = sorts.join(',');
	        text = text.length>25?text.substr(0,25)+'...':text;
	    this.text(text==''?this.attr('empty'):text);
		return this;
	};
	$.changeHeight = function(){
        var e = {t:$('#toggleSorts').height(),d:$('#toggleSorts').css('display'),b:$('body').height()};
        if (e.t > e.b && e.d == 'block') {
	        parent.$('#main').height(e.t+65);
        } else {
	        parent.$('#main').height(e.b+7);
        }
    }
})(jQuery);

