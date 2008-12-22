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
	/**
     * 添加字段
     */
	$.fn.addFields = function(){
		var fields = $('#tableFields');
		var params = {width:'400px',top:$(document).height()/6,left:$(document).width()/2 - 200};
		$.getJSON(fields.attr('action'),function(data){
			if (data = $.result(data)) {
				$.dialogUI({title:data.TITLE})
					.append('<div class="body"></div>')
					.floatDiv(params)
					.find('.body')
					.html(data.BODY)
					.end()
					.find('[type=submit]').click(function(){
						$.undialogUI();
					}).focus().end()
					.find('[rel=cancel]').click(function(){
						$.undialogUI();
					});
				$('select[rel=change]').change(function(){
					$.__FieldTypeChange(this.value);
				});
				$.__FieldTypeChange($('select[rel=change]').val());
				$('[help]').help();
				$.selectEdit();
			}
		});
	}
	/**
     * 修改字段
     */
	$.fn.editFields = function(){
	
	}
	/**
     * 删除字段
     */
	$.fn.delFields = function(){
	
	}
	/**
	 * 更改字段类型触发的事件
	 */
	$.__FieldTypeChange = function(s){
		switch (s) {
			case 'input':
				$('#fieldvalue,#fieldoption').parents('p').slideUp('fast');
				$('#fieldlength').parents('p').slideDown('fast',function(){
					$('[help]').help(); $.selectEdit();
				});
				break;
			case 'radio': case 'checkbox': case 'select':
				$('#fieldlength,#fieldoption').parents('p').slideUp('fast');
				$('#fieldvalue').parents('p').slideDown('fast',function(){
					$('[help]').help(); $.selectEdit();
				});
				break;
			case 'basic': case 'editor':
				$('#fieldlength,#fieldvalue').parents('p').slideUp('fast');
				$('#fieldoption').parents('p').slideDown('fast',function(){
					$('[help]').help(); $.selectEdit();
				});
				break;
			default:
				$('#fieldlength,#fieldoption,#fieldvalue').parents('p').slideUp('fast');
				break;
		}
	}
	/**
     * 自动上传
     */
	$.fn.autoUpFile = function(){
		var f = this.parents('form');
			this.hide().after('<input type="text" value="UpLoading..." class="in w400 uploading" />');
			f.parents('fieldset').after('<iframe src="about:blank" id="tempform" name="tempform" style="display:none;width:0px;height:0px;border:none;"></iframe>');
			f.submit();
	}
})(jQuery);