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
 * | Copyright (C) 2007-2010 LazyCMS.com All rights reserved.                  |
 * +---------------------------------------------------------------------------+
 * | LazyCMS is free software. See LICENSE for copyright notices and details.  |
 * +---------------------------------------------------------------------------+
 */
function post_list_init() {
	// 绑定提交事件
	$('#postlist').actions();
}
// 添加用户页面初始化
function post_manage_init() {
	var wrap = $('#postmanage');
    // 半记忆
    wrap.semiauto();
    // 绑定模型选择事件
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

	// 绑定展开事件
	$('fieldset').each(function(i){
	    var fieldset = $(this);
	    $('a.toggle,h3',this).click(function(){
	        fieldset.toggleClass('closed');
	    });
	});
    // 绑定规则点击
    $('div.rules > a:not([onclick])',wrap).click(function(){
        var val = this.href.replace(self.location,'').replace('#','');
        $('input[name=path]',wrap).insertVal(val); return false;
    });
    // 绑定获取关键词事件
    $('button[rel=keywords]',wrap).click(function(){
        $('input#keywords').val('Loading...').getTerms($('input#title',wrap).val(), $('textarea#content',wrap).val());
    });
    
	// 提交事件
    $('form#postmanage').ajaxSubmit();
}
// 获取扩展字段
function post_manage_extend_attr(model,postid) {
    var wrap = this, path = '',
        params = {method:'extend-attr',model:model};    
        params = typeof(postid)!='undefined' ? $.extend(params,{postid:postid}) : params;

    $.post(LazyCMS.ADMIN+'post.php',params,function(data, status, xhr) {
        $('tbody.extend-attr',wrap).html(data);
        if (path = xhr.getResponseHeader('X-LazyCMS-Path')) {
            $('input#path',wrap).val(path);
        }
        LazyCMS.eselect();
    });
}
// 生成文章
function post_create(postid) {
    return LazyCMS.postAction('post.php', {method:'bulk', action:'create'}, postid);
}
// 删除文章
function post_delete(postid){
    LazyCMS.confirm(_('Confirm Delete?'),function(r){
        if (r) {
            LazyCMS.postAction('post.php', {method:'bulk', action:'delete'}, postid);
        }
    }); 
}