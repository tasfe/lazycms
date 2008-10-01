(function($) {
	// toggleSorts *** *** www.LazyCMS.net *** ***
	$.toggleSorts = function(){
		$('#toggleSorts').slideToggle('fast',function(){$.changeHeight();});
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
				$('#fields').parents('p').hide();$.changeHeight();
			} else {
				$('#fields').parents('p').show().end().html(inputs);$.changeHeight();
			}
	};
})(jQuery);

