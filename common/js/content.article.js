/**
 * +---------------------------------------------------------------------------+
 * | LL                             LLLL   LL     L   LLLL               LLLL  |
 * | LL                            LL   L  LLL   LL  LL   L             LL  LL |
 * | LL      LLLL   LLLLL  LL  LL  LL      LLLL LLL  LL         LL  LL      LL |
 * | LL         LL     LL  LL  LL  LL      L LLL LL  LLLLL      LL  LL     LL  |
 * | LL      LLLLL    LL    LLLL   LL      L  L  LL   LLLLL     LL  LL    LL   |
 * | LL     LL  LL   LL     LLLL   LL      L     LL      LL      LLLL    LL    |
 * | LL     LL  LL  LL       LL    LL   L  L     LL  L   LL      LLLL   LL     |
 * | LLLLLL  LLLLL  LLLLL    LL     LLLL   L     LL   LLLL        LL    LLLLLL |
 * |                        LL                                                 |
 * |                        LL                                                 |
 * +---------------------------------------------------------------------------+
 * | Copyright (C) 2007-2008 LazyCMS.net All rights reserved.                  |
 * +---------------------------------------------------------------------------+
 * | LazyCMS is free software. This version use Apache License 2.0             |
 * | See LICENSE.txt for copyright notices and details.                        |
 * +---------------------------------------------------------------------------+
 */

(function($) {
    $.fn.viewFields = function(){
		var inputs = '';
		var model  = $('option:selected',this).val();
		var fields = $.parseJSON($('#fields_'+model).val());
		var checked = $('#fields_'+model).attr('checked');
			$(fields).each(function(){
				if (this.intype=='input') {
					inputs+= '<input type="checkbox" id="fields[' + this.ename + ']" name="fields[' + this.ename + ']" cookie="true" value="' + this.label + '"' + ((checked==this.ename)?' checked="checked"':'') + '/><label for="fields[' + this.ename + ']">' + this.label + '</label>';
				}
			});
			if (inputs=='')	{
				$('#fields').parents('p').hide().end().html(inputs);
			} else {
				$('#fields').parents('p').show().end().html(inputs);
			}
	};
	$.fn.toggleSorts = function(){
		var sorts = $('#sorts').css({width:'500px',left:this.offset().left,top:this.offset().top,'z-index':100,position:'absolute'}).slideToggle('fast');
			$('.head',sorts).css({width:'495px'});
			$('.body',sorts).css({padding:'5px'});
			$('ul',sorts).css({margin:'0 0 0 20px',padding:0});
		return this;
	};
	$.fn.setSorts = function(){
		this.selectSorts();
	    $('#sorts').slideToggle('fast');
		return this;
	};
	$.fn.selectSorts = function(){
	    var sorts = new Array();
	    $('#sorts input:checked').each(function(i){
            sorts[i] = $(this).next().text();
	    });
	    var text = sorts.join(',');
	        text = text.length>25?text.substr(0,25)+'...':text;
	    this.text(text==''?this.attr('empty'):text);
		return this;
	};
	// ªÒ»°∑÷¥ 
	$.fn.getKeywords = function(id){
		var t = this; t.val('Loading...');
		var v = $(id).val();
		if (v=='') {
			t.val('');
			return this;
		}
		$.post('article.php?action=keywords',{title:v},function(d){
			t.val(d);
		});
		return this;
	};
})(jQuery);