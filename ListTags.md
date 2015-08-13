# 简介 #

列表标签不区分大小写，支持指定的嵌套规则


# 标签解析 #

**文章列表示例：**
```
{list type="list" number="10" order="DESC"}
    <p>[<a href="{$sortpath}">{$sortname}</a>] <a href="{$path}" title="{$title}">{$title size="30"}</a> <em>{$date mode='Y年m月d日 H:i'}</em></p>
{/list}
```

**分页标签**
```
{pagelist /}
```

**标签参数解析**
```
type      调用文章类型
 ………… new     最新文章
 ………… hot     热门文章
 ………… chill   冷门文章

number    调用文章的数量或每页显示数量
zebra     斑马线，必须为数字，如zebra="2"，当第2、4、8等整除于zebra参数值的时候，输出1，其他输出0
order     排序，默认值为desc，要按id顺序输出，则设置值为asc；限在type="list"的时候有效
sortid    指定栏目调用，支持多栏目同时调用，用英文逗号分开
```

**循环体内变量标签**
```
{$zebra}       斑马线
{$postid}      文章ID
{$sortid}      分类ID
{$userid}      作者ID
{$author}      作者名称
{$title}       文章标题
{$views}       展示数
{$comment}     评论数
{$path}        文章地址
{$content}     文章内容
 ………… link 关键词链接地址，默认 [$inst]tags.php?q=$
 ………… tags 链接数量，可指定范围例：tags="5-10"
{$date}        添加时间，通过mode="Y-m-d H:i:s"属性格式化时间
{$edittime}    修改时间
{$keywords}    关键词
{$description} Meta简述
{$sortname}    分类名称
{$sortpath}    分类路径

```

**可嵌套的标签**
```
{tags}
    <a href="{$inst}tags.php?q={$name}" title="{$name}">{$name}</a>
{/tags}
```