(function($) {
	// addFields *** *** www.LazyCMS.net *** ***
	$.fn.addFields = function(){
		alert(this.html());
	};
	// delFields *** *** www.LazyCMS.net *** ***
	$.fn.delFields = function(){
		$('input:checkbox',this).each(function(i){
			if (this.checked) {
				$(this).parents('tr').remove();
			}
		});
	};
})(jQuery);