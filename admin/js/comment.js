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
 * | Copyright (C) 2007-2008 LazyCMS.com All rights reserved.                  |
 * +---------------------------------------------------------------------------+
 * | LazyCMS is free software. This version use Apache License 2.0             |
 * | See LICENSE.txt for copyright notices and details.                        |
 * +---------------------------------------------------------------------------+
 */
function comment_list_init() {
	// 绑定提交事件
	$('#comments').actions();
}
/**
 * 回复评论
 *
 * @param cmtid
 */
function comment_reply(cmtid) {
    var tr = $('tr#cmt-' + cmtid); LazyCMS.removeDialog('cmt_dialog');
    if ($('div.cmt_dialog', tr).is('div')===false) $('.row-actions', tr).after('<div class="cmt_dialog"></div>');
    $('.cmt_dialog', tr).dialog({
        name:'cmt_dialog', title:_('Reply comment'), masked: false, float:null, styles: {position:'absolute'},
        body:[
            '<div class="wrapper">',
                '<form action="' + LazyCMS.ADMIN + 'comment.php?method=reply" method="post" name="cmt_reply_frm" id="cmt_reply_frm">',
                    '<textarea class="text" name="content" id="content" rows="8" cols="50"></textarea>',
                    '<input type="hidden" name="parent" value="' + cmtid + '" />',
                    '<div class="buttons">',
                        '<button type="submit">' + _('Save') + '</button><button rel="close" type="button">' + _('Cancel') + '</button>',
                    '</div>',
                '</form>',
            '</div>',
        ].join('')
    },function(r){
        $('form', this).ajaxSubmit(function(){
            LazyCMS.removeDialog('cmt_dialog');
        });
    });
}
/**
 * 编辑评论
 *
 * @param cmtid
 */
function comment_edit(cmtid) {
    var tr = $('tr#cmt-' + cmtid); LazyCMS.removeDialog('cmt_dialog');
    if ($('div.cmt_dialog', tr).is('div')===false) $('.row-actions', tr).after('<div class="cmt_dialog"></div>');
    $.getJSON(LazyCMS.ADMIN + 'comment.php', {method: 'get', cmtid: cmtid} ,function(r){
        $('.cmt_dialog', tr).dialog({
            name:'cmt_dialog', title:_('Edit comment'), masked: false, float:null, styles: {position:'absolute'},
            body:[
                '<div class="wrapper">',
                    '<form action="' + LazyCMS.ADMIN + 'comment.php?method=edit" method="post" name="cmt_edit_frm" id="cmt_edit_frm">',
                        '<p><label>' + _('Author') + '</label><input class="text" type="text" size="20" name="author" value="' + r.author + '" /></p>',
                        '<p><label>' + _('Email') + '</label><input class="text" type="text" size="30" name="mail" value="' + r.mail + '" /></p>',
                        '<p><label>' + _('Url') + '</label><input class="text" type="text" size="40" name="url" value="' + r.url + '" /></p>',
                        '<textarea class="text" name="content" id="content" rows="8" cols="50">' + r.content + '</textarea>',
                        '<input type="hidden" name="cmtid" value="' + cmtid + '" />',
                        '<div class="buttons">',
                            '<button type="submit">' + _('Save') + '</button><button rel="close" type="button">' + _('Cancel') + '</button>',
                        '</div>',
                    '</form>',
                '</div>',
            ].join('')
        },function(r){
            $('form', this).ajaxSubmit(function(){
                LazyCMS.removeDialog('cmt_dialog');
            });
        });
    });
}
// 修改评论状态
function comment_state(action,cmtid){
    return LazyCMS.postAction('comment.php', {method:'bulk', action:action}, cmtid);
}
// 删除模型
function comment_delete(cmtid){
    LazyCMS.confirm(_('Confirm Delete?'),function(r){
        if (r) {
            LazyCMS.postAction('comment.php', {method:'bulk', action:'delete'}, cmtid);
        }
    });
}