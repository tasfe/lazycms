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
// 页面初始化
function publish_init() {
	var form = $('form#publish');
    // 绑定全选事件
    $('button[rel=select]',form).click(function(){
        $('input[name^=category]:checkbox',form).each(function(){
            this.checked = !this.checked;
        });
    });
    // 提交事件
    form.ajaxSubmit();
}
// 进程管理
function publish_list() {
	// 绑定提交事件
	$('form#publishlist').actions();
}
// 删除进程
function publish_delete(pubid){
    LazyCMS.confirm(_('Confirm Delete?'),function(r){
        if (r) {
            LazyCMS.postAction('publish.php', {method:'bulk', action:'delete'}, pubid);
        }
    });
}
// 清空
function publish_empty(){
    LazyCMS.confirm(_('Confirm Empty?'),function(r){
        if (r) {
            LazyCMS.postAction('publish.php', {method:'bulk', action:'empty'}, '1');
        }
    });
}
