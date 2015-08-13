# 评论标签 #

评论标签不区分大小写


# 标签解析 #

**最新评论示例：**
```
{comments type="new" number="10"}
    <p><a href="{$path}#cmt_list">{$content size="20"}</a></p>
{/comments}
```

**评论模版示例：**
```
{pagelist /}
{comments type="list" number="10" order="DESC"}
<div class="dt">
    <span class="author">
        <span class="address">{$address}</span>
        <img src="{$avatar}" alt="{$author}" /><span class="name">{$author}</span>
    </span>
    <span class="post-time">{$date mode="Y年m月d日 H:i"}</span>
    <div class="clear"><br/></div>
</div>
<div class="dd">
    {contents}
    <div class="citation">
        {$contents_deep}
        <div class="citation-title">
            <span class="author">
                <span class="address">{$address}</span>
                <img src="{$avatar}" alt="{$author}" /><span class="name">{$author}</span>
            </span>
            <span class="post-time">{$date mode="Y年m月d日 H:i"}</span>
            <div class="clear"><br/></div>
        </div>
        <p class="content">{$content}</p>
    </div>
    {/contents}
    <p class="content">{$content}</p>
    <div class="toolbar" id="toolbar-{$cmtid}"><a href="#cmt_reply" onclick="comment_reply({$cmtid});">回复</a></div>
</div>
{/comments}
{pagelist /}
```