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
function sort_list_init() {
	// 绑定提交事件
	$('#sortlist').actions();
}

function sort_manage_init() {
    var wrap = $('#sortmanage'),listid = $('input:hidden[name=listid]',wrap).val();
        wrap.semiauto();
    // 绑定分类选择事件
    $('select[name=parent]',wrap).change(function(){
        var selected = $('option:selected',this);
        if (typeof(listid)=='undefined') {
            $('input[name=path]',wrap).val(selected.attr('path'));
        }
    });
    // 绑定模型选择事件
	if ($('select[name=model]',wrap).is('select')) {
        var taxonomyid = $('input[name=taxonomyid]', wrap).val(),
            mcode      = $('select[name=model]', wrap).val();
	    // 绑定模型切换事件
    	$('select[name=model]',wrap).change(function(){
            sort_manage_extend_attr.call(wrap, this.value, taxonomyid);
    	});
	    // 初始化
        sort_manage_extend_attr.call(wrap, mcode, taxonomyid);
	}
    // 绑定规则点击
	$('div.rules > a',wrap).click(function(){
	    var val = this.href.replace(self.location,'').replace('#','');
	    $('input[name=path]',wrap).insertVal(val); return false;
	});
    // 绑定展开事件
	$('fieldset').each(function(i){
	    var fieldset = $(this);
	    $('a.toggle,h3',this).click(function(){
	        fieldset.toggleClass('closed');
	    });
	});
    // 提交事件
    $('form#sortmanage').ajaxSubmit();
}
// 获取扩展字段
function sort_manage_extend_attr(model, taxonomyid) {
    var wrap = this, list = '',
        params = {method:'extend-attr',model:model};
        params = typeof(taxonomyid)!='undefined' ? $.extend(params,{listid:taxonomyid}) : params;

    $.post(LazyCMS.ADMIN + 'categories.php', params, function(data, status, xhr) {
        $('tbody.extend-attr', wrap).html(data);
        if (list = xhr.getResponseHeader('X-LazyCMS-List')) {
            $('select#listtemplate', wrap).val(list);
        }
        LazyCMS.eselect();
    });
}
// 生成分类
function sort_create(listid) {
    return LazyCMS.postAction('categories.php', {method:'bulk', action:'createlists'}, listid);
}
// 删除分类
function sort_delete(listid){
    LazyCMS.confirm(_('Confirm Delete?'),function(r){
        if (r) {
            LazyCMS.postAction('categories.php', {method:'bulk', action:'delete'}, listid);
        }
    });
}