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
function sort_list_init() {
    var form = $('#sortlist');
    // 绑定全选事件
    $('input[name=select]',form).click(function(){
        $('input[name^=list]:checkbox,input[name=select]:checkbox',form).attr('checked',this.checked);
    });
    $('tbody tr',form).hover(function(){
        $('td',this).css({'background-color':'#FFFFCC'});
        $('.row-actions',this).css({'visibility': 'visible'});
    },function(){
        $('td',this).css({'background-color':'#FFFFFF'});
        $('.row-actions',this).css({'visibility': 'hidden'});
    });
	// 绑定删除事件
	$('span.delete a',form).click(function(){
		var url = this.href;
		LazyCMS.confirm(_('Confirm Delete?'),function(r){
			if (r) {
				$.getJSON(url,function(data){
					LazyCMS.ajaxResult(data);
				});
			}
		});
		
		return false;
	});
	// 绑定提交事件
	form.actions(function(data){
        LazyCMS.ajaxResult(data);
    });
}
function sort_manage_init() {
    var wrap = $('#sortmanage'),sortid = $('input:hidden[name=sortid]',wrap).val();
    // 绑定分类选择事件
    $('select[name=parent]',wrap).change(function(){
        var selected = $('option:selected',this), models = selected.attr('model').split(',');
        if (typeof(sortid)=='undefined') {
            $('input[name=path]',wrap).val(selected.attr('path') + (selected.attr('path')==''?'':'/') );
        }
	    $('input[name^=type]:checkbox',wrap).each(function(){
	        if ($.inArray(this.value,models)==-1) {
	            this.checked = false;
	        } else {
	            this.checked = true;
	        }
	    });
    });
    // 绑定规则点击
	$('div.rules > a',wrap).click(function(){
	    var val = this.href.replace(self.location,'').replace('#','');
	    $('input[name=path]',wrap).insertVal(val); return false;
	});
    // 提交事件
    $('form#sortmanage').ajaxSubmit(function(data){
        LazyCMS.ajaxResult(data);
    });
}