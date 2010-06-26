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
function post_list_init() {
    var form = $('#postlist');
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
// 添加用户页面初始化
function post_manage_init() {
	var wrap = $('#postmanage');
	if ($('select[name=model]',wrap).is('select')) {
        var postid = $('input[name=postid]',wrap).val(),
            mcode  = $('select[name=model]',wrap).val();
	    // 绑定模型切换事件
    	$('select[name=model]',wrap).change(function(){
    	    post_manage_extend_attr.call(wrap,this.value,postid);
    	});
	    // 初始化
	    post_manage_extend_attr.call(wrap,mcode,postid);
	}
	// 绑定规则点击
	$('span.rules > a',wrap).click(function(){
	    var val = this.href.replace(self.location,'').replace('#','');
	    $('input[name=path]',wrap).val(val); return false;
	});
	// 绑定展开事件
	$('fieldset').each(function(i){
	    var fieldset = $(this);
	    $('a.toggle,h3',this).click(function(){
	        fieldset.toggleClass('closed');
	    });
	});
	// 提交事件
    $('form#postmanage').ajaxSubmit(function(data){
        LazyCMS.ajaxResult(data);
    });
}
// 获取扩展字段
function post_manage_extend_attr(model,postid) {
    var wrap = this,params = {action:'extend-attr',model:model};
        params = typeof(postid)!='undefined' ? $.extend(params,{postid:postid}) : params;
    $.post(LazyCMS.ADMIN_ROOT+'post.php',params,function(r){
		var data = LazyCMS.ajaxResult(r);
		if (data) {
			$('tbody.extend-attr',wrap).html(data);
			LazyCMS.selectEdit();
		}
    });
}