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
    var wrap = $('#sortmanage'),sortid = $('input:hidden[name=sortid]',wrap).val();
    // 绑定分类选择事件
    $('select[name=parent]',wrap).change(function(){
        var selected = $('option:selected',this);
        if (typeof(sortid)=='undefined') {
            $('input[name=path]',wrap).val(selected.attr('path'));
        }
    });
    // 绑定规则点击
	$('div.rules > a',wrap).click(function(){
	    var val = this.href.replace(self.location,'').replace('#','');
	    $('input[name=path]',wrap).insertVal(val); return false;
	});
    // 提交事件
    $('form#sortmanage').ajaxSubmit();
}
// 生成文章
function sort_create(sortid) {
    return LazyCMS.postAction('categories.php', {method:'bulk', action:'createlists'}, sortid);
}
// 删除分类
function sort_delete(sortid){
    LazyCMS.confirm(_('Confirm Delete?'),function(r){
        if (r) {
            LazyCMS.postAction('categories.php', {method:'bulk', action:'delete'}, sortid);
        }
    });
}