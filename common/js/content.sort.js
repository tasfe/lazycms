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
     * 展开下拉树
     */
	$.fn.addSub = function(p1,p2,p3){
		var nbsp = ''; var e = getURI();
		var p2 = (typeof p2=='undefined') ? 1 : p2;
		var p3 = (typeof p3=='undefined') ? false : p3;
		if (p3==0||p3==false) { return ; }
		var tr = this.parents('tr');
		var td = $('td:first',tr);
		var os = $('img.d2,img.d3',td).css('cursor','pointer').unbind().click(function(){ 
				$.cookie('getSub_' + e.File + '_' + p1,true,{expires:365,path:e.Path}); $('#list_' + p1).addSub(p1,p2,p3); 
				return false;
			});
		if ($.cookie('getSub_' + e.File + '_' + p1)==null || $.cookie('getSub_' + e.File + '_' + p1)=='false') { return ; }
		var fm = td.parents('form');
		var tb = td.parents('tbody');
		var cs = tr.attr('class');
		var path = typeof(tr.attr('path'))=='undefined'?'':tr.attr('path');
		for (var i=0; i<p2; i++) { nbsp += "&nbsp; &nbsp;"; }
		
		if ($('tr[path^=' + path + p1 + ']:visible',tb).is("tr")==false) {
			os.hide();
			$('<img src="' + common() + '/images/loading.gif" class="os" />').insertBefore(os);
			$.post(fm.attr('action'),{submit:'getsub',lists:p1,space:p2},function(data){
				if (JSON = $.result(data)) {
					os.prev().remove();os.show();
					$(JSON).each(function(){
						os.removeClass('d1').removeClass('d2').addClass('d3');
						tr.after($("td:first input",eval(this.code)).before(nbsp).end().attr('path',path + p1 + '/').show());
						$('#list_' + this.id).addSub(this.id,p2 + 1,this.sub);
					});
				}
			});
		} else {
			// 当前对象存在
			os.removeClass('d1').removeClass('d3').addClass('d2');
			$.cookie('getSub_'+e.File+'_'+p1,false,{expires:365,path:e.Path});
			$('tr[path^=' + path + p1 + ']',tb).remove();
		}
	}
	/**
     * 自动选择模型
     */
	$.fn.selectModels = function(){
	    var models = this.find('option:selected').attr('models').split(',');
	    $('#models input:checkbox').each(function(){
	        if ($.inArray(this.value,models)==-1) {
	            this.checked = false;
	        } else {
	            this.checked = true;
	        }
	    });
	};
	
})(jQuery);