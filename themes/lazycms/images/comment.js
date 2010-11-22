var cmtstate, replyurl = ajaxinfo = listsurl = comment = '';
var scripts = document.getElementsByTagName("script"); eval(scripts[ scripts.length - 1 ].innerHTML);
// 允许评论才显示
if (cmtstate=='Yes') {
    // 评论表单
    comment = '<a name="cmt_post"></a><div class="comments">';
    comment+=   '<form action="' + replyurl + '" method="post" name="cmt_form" id="cmt_form">';
    comment+=       '<div class="top"><strong>网友评论:</strong><a href="#cmt_list">已有<em>0</em>条评论，共<em>0</em>人参与评论。</a></div>';
    comment+=       '<div class="info">';
    comment+=           '<p><label for="author">名&nbsp; &nbsp; &nbsp; 称：</label><input class="text" name="author" id="author" type="text" size="20" /></p>';
    comment+=           '<p><label for="email">电子邮件：</label><input class="text" name="email" id="email" type="text" size="30" /></p>';
    comment+=           '<p><label for="url">网&nbsp; &nbsp; &nbsp; 站：</label><input class="text" name="url" id="url" type="text" size="50" value="http://" /></p>';
    comment+=       '</div>';
    comment+=       '<div class="textarea"><textarea class="text" id="cmt_content" name="content"></textarea></div>';
    comment+=       '<div class="bottom"><button type="submit">发表评论</button></div>';
    comment+=   '</form>';
    comment+= '</div>';
    comment+= '<a name="cmt_list"></a>';
    comment+= '<div class="comment_list"></div>';

    document.write(comment);
    
    $(document).ready(function(){
        var wrap = $('form#cmt_form'),url = CMS.getCookie('comment_user','url');
        $('input[name=author]',wrap).val(CMS.getCookie('comment_user','author'));
        $('input[name=email]',wrap).val(CMS.getCookie('comment_user','email'));
        if (url !== null) $('input[name=url]',wrap).val(url);
        // 刷新评论数
        comment_jsonp_info(ajaxinfo);
        // 显示评论列表
        comment_loading(listsurl);
    });
    
    // 绑定评论提交事件
    $('form#cmt_form').ajaxSubmit(function(r){
        // 刷新评论数
        comment_jsonp_info(ajaxinfo);
        // 刷新评论
        comment_loading(listsurl);
        // 保存用户信息
        var wrap = $('form#cmt_form');
        CMS.setCookie('comment_user', 'author', $('input[name=author]',wrap).val());
        CMS.setCookie('comment_user', 'email', $('input[name=email]',wrap).val());
        CMS.setCookie('comment_user', 'url', $('input[name=url]',wrap).val());
    });

}

/**
 * 刷新评论数
 *
 * @param url
 */
function comment_jsonp_info(url) {
    $.getJSON(url + '&callback=?', function(data){
        var wrap = $('form#cmt_form'), post = $('#post');
        $('span.cmt-count', post).text(data[0]);
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