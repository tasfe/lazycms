(function($) {
	// Purview *** *** www.LazyCMS.net *** ***
	$.Purview = function(){
		$('div.purview input.__bigP').each(function(){
			var checked = true;
			$('div.purview .__' + this.id).each(function(){
				if (checked) {
					checked = this.checked ? true : false;
				}
			});
			$('#' + this.id).attr('checked',checked);
		});
	}
})(jQuery);