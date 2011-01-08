var cmtstate, replyurl = ajaxinfo = listsurl = comment = '';
var scripts = document.getElementsByTagName("script"); eval(scripts[ scripts.length - 1 ].innerHTML);
// 允许评论才显示
if (cmtstate=='Yes') {
    // 评论表单
    document.write([
        '<a name="cmt_post"></a>',
        comment_reply_form(),
        '<a name="cmt_list"></a>',
        '<div class="comment_list"></div>'
    ].join(''));

    comment_ajax_submit();
    
    $(document).ready(function(){
        comment_set_form();
        // 刷新评论数
        comment_jsonp_info(ajaxinfo);
        // 显示评论列表
        comment_loading(listsurl);
    });
    


}

/**
 * 刷新评论数
 *
 * @param url
 */
function comment_jsonp_info(url) {
    $.getJSON(url + '&callback=?', function(data){
        var wrap = $('form#cmt_form');
        $('span.cmt-count').text(data[0]);
        $('div.top em:eq(0)', wrap).text(data[0]);
        $('div.top em:eq(1)', wrap).text(data[1]);
    });
}
/**
 * 加载评论
 *
 * @param url
 */
function comment_loading(url) {
    $.ajax({
        cache: false, dataType: 'html', type: 'GET', url: url,
        success: function(r) {
            $('div.comment_list').html(r);
            $('div.comment_list div.pages a').click(function(){
                comment_loading(this.href);
                return false;
            });
        }
    });
}
/**
 * 回复评论表单
 * 
 * @param cmtid
 */
function comment_reply_form(cmtid) {
    cmtid = cmtid || 0;
    return [
        '<div class="comments" id="cmt_reply_form_' + cmtid + '">',
            '<form action="' + replyurl + '" method="post" name="cmt_form" id="cmt_form">',
                '<div class="top">' + (cmtid ? '<strong>回复评论:</strong>' : '<h3>网友评论:</h3><a href="#cmt_list">已有<em>0</em>条评论，共<em>0</em>人参与评论。</a>') + '</div>',
                '<div class="info">',
                    '<p><label for="author">名&nbsp; &nbsp; &nbsp; 称：</label><input class="text" name="author" id="author" type="text" size="20" /></p>',
                    '<p><label for="mail">电子邮件：</label><input class="text" name="mail" id="mail" type="text" size="30" /></p>',
                    '<p><label for="url">网&nbsp; &nbsp; &nbsp; 站：</label><input class="text" name="url" id="url" type="text" size="50" value="http://" /></p>',
                '</div>',
                '<div class="textarea"><textarea class="text" id="cmt_content" name="content"></textarea></div>',
                '<input type="hidden" name="parent" value="' + cmtid + '">',
                '<div class="bottom"><button type="submit">发表评论</button>' + (cmtid ? '<button type="button" onclick="$(\'#cmt_reply_form_' + cmtid + '\').remove();">取消</button>' : '') + '</div>',
            '</form>',
        '</div>'
    ].join('');
}
/**
 * 回复评论
 * 
 * @param cmtid
 */
function comment_reply(cmtid) {
    $('div.dd div.comments').remove();
    $('#toolbar-' + cmtid).after(comment_reply_form(cmtid));    
    comment_set_form();
    comment_ajax_submit();
}
/**
 * 绑定提交事件
 */
function comment_ajax_submit() {
    // 绑定评论提交事件
    $('form#cmt_form').ajaxSubmit(function(r){
        // 清空发表的内容
        $('textarea', this).val('');
        // 刷新评论数
        comment_jsonp_info(ajaxinfo);
        // 刷新评论
        comment_loading(listsurl);
        // 保存用户信息
        var wrap = $('form#cmt_form');
        CMS.setCookie('comment_user', 'author', $('input[name=author]',wrap).val());
        CMS.setCookie('comment_user', 'mail', $('input[name=mail]',wrap).val());
        CMS.setCookie('comment_user', 'url', $('input[name=url]',wrap).val());
    });
}
/**
 * 设置表单内容
 */
function comment_set_form() {
    var wrap   = $('form#cmt_form'),
        author = CMS.getCookie('comment_user','author'),
        email  = CMS.getCookie('comment_user','mail'),
        url    = CMS.getCookie('comment_user','url');
    if (author !== null) $('input[name=author]',wrap).val(author);
    if (email !== null)  $('input[name=mail]',wrap).val(email);
    if (url !== null)    $('input[name=url]',wrap).val(url);
}