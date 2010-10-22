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
function message_init() {
    var hl = [
        '<style type="text/css">',
            '#lazy_chat{ width:450px; height:180px; overflow:hidden; }',
            '#lazy_chat.min{ height:50px; }',
            '#lazy_chat form { margin:0; padding:0; }',
            '#lazy_chat .mask{ position:absolute; width:100%; height:100%; left:0; top:0; background:#666; filter:alpha(opacity=20); -moz-opacity:0.2; opacity:0.2; }',
            '#lazy_chat .action{ position:absolute; top:3px; right:20px; height:9px; }',
            '#lazy_chat .action a{ text-decoration:none; }',
            '#lazy_chat .action .close, #lazy_chat .action .min, #lazy_chat .action .max { display:block; width:10px; height:9px; font-size:1px; line-height:1px; margin-left:8px; background:url(images/chat_close.gif) left top; float:left;}',
            '#lazy_chat .action .min { background-position: -10px top;}',
            '#lazy_chat .action .max { background-position: right top;}',
            '#lazy_chat .content { position:relative; width:430px; height:146px; margin-right:1px; padding:5px 10px 0px 10px; overflow:auto; text-align:left; background-color:transparent;}',
            '#lazy_chat.min .content { height:16px; overflow:hidden; }',
            '#lazy_chat .content p { margin:2px auto;}',
            '#lazy_chat .content p em{ font-size:9px; color:#999999; }',
            '#lazy_chat .content p span { color:#369; margin-right:5px;}',
            '#lazy_chat .send{ position:relative; clear:both; text-align:center; height:24px; padding:3px 5px; }',
	        '#lazy_chat .send .message{ width:270px; margin-right:6px; }',
	        '#lazy_chat .send button{ margin-right:6px; }',
        '</style>',
        '<div id="lazy_chat" class="min">',
            '<div class="mask">&nbsp;</div>',
            '<form action="' + LazyCMS.ADMIN_ROOT + 'index.php" method="post" name="lazy_chat">',
                '<div class="content"></div>',
                '<div class="send">',
                    '<input type="text" class="text message" maxlength="100" name="message" />',
                    '<input type="hidden" name="method" value="send_msg" />',
                    '<button type="submit">' + _('Send') + '</button>',
                    '<button type="button">' + _('Maximize') + '</button>',
                '</div>',
            '</form>',
        '</div>'
    ];
    $(hl.join('')).appendTo($('body'));
    var lazy_chat = $('div#lazy_chat');
        lazy_chat.float('cb');
    $('div.send button[type=button]',lazy_chat).click(function(){
        lazy_chat.toggleClass('min');
        if (lazy_chat.hasClass('min')) {
            $(this).text(_('Maximize'));
        } else {
            $(this).text(_('Minimize'));
        }
        $('div.content',lazy_chat).scrollTop(10000);
    });
    $('form[name=lazy_chat]',lazy_chat).ajaxSubmit(function(r){
        if (r.result=='ok') {
            $('input:text[name=message]',this).val('').focus();
        }
    });
    messgae_poll();
}
/**
 * 显示消息
 *
 * @param data
 */
function message_append(data) {
    var content = $('div#lazy_chat div.content');
        if ($('p',content).length >= 100) {
            $('p:eq(0)',content).remove();
        }
        content.append('<p><span>[' + data.sender + ']</span>  '+ _('Say: ') + data.content + ' <em>' + data.datetime + '</em></p>');
        content.scrollTop(10000);
}
/**
 * 获取并显示新消息
 */
function messgae_poll() {
    $.ajax({
        cache: false, type:'GET', dataType:'json',
        url: LazyCMS.ADMIN_ROOT + 'index.php?method=poll',
        beforeSend:function(xhr, s){
            LazyCMS.success(xhr,s,true);
        },
        success: function(r){
            var length = r.length;
            for(var i=0;i<length;i++) {
                message_append(r[i]);
            }
        },
        complete: function(){
            messgae_poll();
        }
    });
}