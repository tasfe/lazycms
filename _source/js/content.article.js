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
			// 执行半记忆操作
			$(document).SemiMemory();
	};
	$.fn.toggleSorts = function(){
		var sorts = $('#__sorts').css({width:'300px',left:this.offset().left,top:this.offset().top,'z-index':100,position:'absolute'}).toggle();
			$('.head',sorts).css({width:'295px'});
			$('.body',sorts).css({padding:'5px'});
		return this;
	};
	$.fn.setSortId = function(){
		if (this.hasClass('disabled')) { return ; }
		$('#__sorts').hide(); $('#sortid').val(this.attr('value')); $('#__sortname').text(this.attr('label'));
	}
})(jQuery);
