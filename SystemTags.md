# 标签简介 #

LazyCMS使用的是XML风格的模版标签系统，标签不区分大小写。

# 公共标签属性 #

```
size ………… 限制标签输出的长度，例：{$title size="20"}
mode ………… 时间模式，例：{$date mode="Y-m-d H:i:s"}
code ………… 代码输出模式
     javascript,js    标签内容以js方式输出
     xmlencode,xml    标签内容转义为安全的XML实体
     urlencode,url    标签内容转移为urlencode
     htmlencode       标签内容转义为HTML实体

func ………… 应用函数，@me是此标签内容，例：{$title func="test(@me)"}
```

# 系统标签 #
```
{$sitename}                        网站名称
{$inst},{$webroot}                 网站根路径
{$host},{$domain}                  站点域名
{$ver},{$version}                  CMS版本号
{$theme},{$templet},{$template}    模版路径
{$lang},{$language}                站点默认语言
{$cms},{$lazycms}                  支持链接
{$guide}                           页面导航
{$keywords},{$keyword}             页面关键词
{$description}                     Meta简述 
{$jquery}                          jQuery Google CDN 地址
 ………… ver                      jQuery版本
```