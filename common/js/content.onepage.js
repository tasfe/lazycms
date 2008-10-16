(function($) {
	// viewFields *** *** www.LazyCMS.net *** ***
	$.fn.viewFields = function(){
		var inputs = '';
		var model  = $('option:selected',this).val();
		var fields = $.parseJSON($('#fields_'+model).val());
		var checked = $('#fields_'+model).attr('checked');
			$(fields).each(function(){
				if (this.intype=='input') {
					inputs+= '<input type="checkbox" id="fields[' + this.ename + ']" name="fields[' + this.ename + ']" value="' + this.label + '"' + ((checked==this.ename)?' checked="checked"':'') + '/><label for="fields[' + this.ename + ']">' + this.label + '</label>';
				}
			});
			if (inputs=='')	{
				$('#fields').parents('p').hide().end().html(inputs);changeHeight();
			} else {
				$('#fields').parents('p').show().end().html(inputs);changeHeight();
			}
	};
})(jQuery);
