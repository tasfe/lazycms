var cmtstate, replyurl = ajaxinfo = comment = '';
var scripts = document.getElementsByTagName("script"); eval(scripts[ scripts.length - 1 ].innerHTML);
// 允许评论才显示
if (cmtstate=='Yes') {
    // 评论表单
    comment = '<div class="comments">';
    comment+=   '<form action="' + replyurl + '" method="post" name="cmt_form" id="cmt_form">';
    comment+=       '<div class="top"><strong>网友评论:</strong><a href="javascript:;">已有<em>0</em>条评论，共<em>0</em>人参与评论。</a></div>';
    comment+=       '<div class="info">';
    comment+=           '<p><label for="author">名&nbsp; &nbsp; &nbsp; 称：</label><input class="text" name="author" id="author" type="text" size="20" /></p>';
    comment+=           '<p><label for="email">电子邮件：</label><input class="text" name="email" id="email" type="text" size="30" /></p>';
    comment+=           '<p><label for="url">网&nbsp; &nbsp; &nbsp; 站：</label><input class="text" name="url" id="url" type="text" size="50" value="http://" /></p>';
    comment+=       '</div>';
    comment+=       '<div class="textarea"><textarea class="text" id="cmt_content" name="content"></textarea></div>';
    comment+=       '<div class="bottom"><button type="submit">发表评论</button></div>';
    comment+=   '</form>';
    comment+= '</div>';

    document.write(comment);
    document.write('<script type="text/javascript" src="' + ajaxinfo + '&callback=comment_ajax_info"><\/script>');

    $('form#cmt_form').ajaxSubmit(function(r){
        // TODO 刷新评论数
        // TODO 保存用户信息
    });
}
/**
 * 刷新评论数量
 *
 * @param data
 */
function comment_ajax_info(data) {
    var wrap = $('form#cmt_form');
    $('div.top em:eq(0)',wrap).text(data[0]);
    $('div.top em:eq(1)',wrap).text(data[1]);
}