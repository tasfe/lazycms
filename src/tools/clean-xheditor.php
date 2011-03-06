<?php
// 当前绝对路径
define('ABS_PATH',dirname(__FILE__));

// 删除没用的文件
$op_files = array(
    array('D','demos'),
    array('D','jquery'),
    array('D','serverscript'),
    array('D','xheditor_emot'),
    array('C','src/xheditor-zh-cn.src.js','xheditor.js','translate'),
    array('D','src'),
    array('M','LGPL-LICENSE.txt','LGPL-LICENSE'),
    array('D','*.txt'),
    array('D','*.html'),
    array('D','*.min.js'),
    array('D','xheditor_plugins'),
    array('A','plugins'),
    array('M','xheditor_skin','skins'),
    array('D','skins/default'),
    array('D','skins/o2007blue'),
    array('D','skins/o2007silver'),
    array('D','skins/vista'),
    array('M','skins/nostyle','skins/default','process_css'),
);

foreach ($op_files as $action) {
    switch($action[0]) {
        // 添加文件夹
        case 'A':
            @mkdirs(ABS_PATH.'/'.$action[1]);
            break;
        // 删除文件
        case 'D':
            foreach((array)glob($action[1]) as $file) {
                $abs_path = ABS_PATH.'/'.$file;
                if (is_dir($abs_path)) {
                    @rmdirs($abs_path);
                    echo "Delete floder: {$file}\n";
                } elseif(is_file($abs_path)) {
                    @unlink($abs_path);
                    echo "Delete file: {$file}\n";
                }
            }
            break;
        // 文件改名
        case 'M':
            if (count($action)>=3) {
                @rename(ABS_PATH.'/'.$action[1], ABS_PATH.'/'.$action[2]);
                echo "Changed path ".$action[1]." to ".$action[2]."\n";
                // 执行回调函数
                if (isset($action[3])) {
                    call_user_func($action[3], $action[1], $action[2]);
                }
            }
            break;
        // 复制文件
        case 'C':
            if (count($action)>=3) {
                @copy(ABS_PATH.'/'.$action[1], ABS_PATH.'/'.$action[2]);
                echo "Copyed path ".$action[1]." to ".$action[2]."\n";
                // 执行回调函数
                if (isset($action[3])) {
                    call_user_func($action[3], $action[1], $action[2]);
                }

            }
            break;
    }
}
// 处理CSS
function process_css($source,$target) {
    if ($source=='skins/nostyle' && $target=='skins/default') {
        $content = file_get_contents(ABS_PATH.'/'.$target.'/ui.css');
        $content = str_replace('.xhe_nostyle', '.xhe_default', $content);
        file_put_contents(ABS_PATH.'/'.$target.'/ui.css', $content);
    }
}
// 翻译
function translate($source,$target) {
    $translates = array(
        array("xheditor_skin/","skins/"),
        array("xheditor_emot/","emots/"),
        array("[\w-\:]","[\w\-\:]"),
        array("tool=arrEmbed[target.type.toLowerCase()];","var lazyType=$(target).attr('lazytype'); tool=lazyType ? lazyType : arrEmbed[target.type.toLowerCase()];"),
        array("t:'普通段落'","t:_('Paragraph')"),
        array("t:'标题1'","t:_('Heading 1')"),
        array("t:'标题2'","t:_('Heading 2')"),
        array("t:'标题3'","t:_('Heading 3')"),
        array("t:'标题4'","t:_('Heading 4')"),
        array("t:'标题5'","t:_('Heading 5')"),
        array("t:'标题6'","t:_('Heading 6')"),
        array("t:'已编排格式'","t:_('Preformatted')"),
        array("t:'地址'","t:_('Address')"),
        array("t:'极小'","t:_('xx-small')"),
        array("t:'特小'","t:_('x-small')"),
        array("t:'小'","t:_('small')"),
        array("t:'中'","t:_('medium')"),
        array("t:'大'","t:_('large')"),
        array("t:'特大'","t:_('x-large')"),
        array("t:'极大'","t:_('xx-large')"),
        array("{n:'宋体',c:'SimSun'},{n:'仿宋体',c:'FangSong_GB2312'},{n:'黑体',c:'SimHei'},{n:'楷体',c:'KaiTi_GB2312'},{n:'微软雅黑',c:'Microsoft YaHei'},{n:'Arial'},","{n:'Arial'},"),
        array("s:'左对齐'","s:_('Align left')"),
        array("s:'居中'","s:_('Align center')"),
        array("s:'右对齐'","s:_('Align right')"),
        array("s:'两端对齐'","s:_('Align full')"),
        array("s:'数字列表'","s:_('Ordered list')"),
        array("s:'符号列表'","s:_('Unordered list')"),
        array("使用键盘快捷键(Ctrl+V)把内容粘贴到方框里，按 确定","' + _('Use Ctrl+V on your keyboard to paste the text.') + '"),
        array("value=\"确定\"","value=\"' + _('Ok') + '\""),
        array("链接地址:","' + _('Link URL:') + '"),
        array("打开方式:","' + _('Target:&nbsp;&nbsp;') + '"),
        array("链接文字:","' + _('Link Text:') + '"),
        array("图片文件:","' + _('Img URL:&nbsp;') + '"),
        array("替换文本:","' + _('Alt text:') + '"),
        array("对齐方式:","' + _('Alignment:') + '"),
        array("宽度高度:","' + _('Dimension:') + '"),
        array("边框大小:","' + _('Border:&nbsp;&nbsp;&nbsp;') + '"),
        array("水平间距:","' + _('Hspace:&nbsp;&nbsp;&nbsp;') + '"),
        array("垂直间距:","' + _('Vspace:') + '"),
        array("动画文件:","' + _('Flash URL:') + '"),
        array("媒体文件:","' + _('Media URL:') + '"),
        array("行数列数:","' + _('Rows&Cols:&nbsp;&nbsp;') + '"),
        array("标题单元:","' + _('Headers:&nbsp;&nbsp;&nbsp;&nbsp;') + '"),
        array("表格间距:","' + _('CellSpacing:') + '"),
        array("表格填充:","' + _('CellPadding:') + '"),
        array("表格标题:","' + _('Caption:&nbsp;&nbsp;&nbsp;&nbsp;') + '"),
        array("边框大小: <input type=\"text\" id=\"xheTableBorder\"","' + _('Border:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;') + ' <input type=\"text\" id=\"xheTableBorder\""),
        array("<option selected=\"selected\" value=\"\">默认</option>","<option selected=\"selected\" value=\"\">' + _('Default') +'</option>"),
        array("<option value=\"_blank\">新窗口</option>","<option value=\"_blank\">' + _('New window') + '</option>"),
        array("<option value=\"_self\">当前窗口</option>","<option value=\"_self\">' + _('Same window') + '</option>"),
        array("<option value=\"_parent\">父窗口</option>","<option value=\"_parent\">' + _('Parent window') + '</option>"),
        array("<option value=\"left\">左对齐</option>","<option value=\"left\">' + _('Left') + '</option>"),
        array("<option value=\"right\">右对齐</option>","<option value=\"right\">' + _('Right') + '</option>"),
        array("<option value=\"top\">顶端</option>","<option value=\"top\">' + _('Top') + '</option>"),
        array("<option value=\"middle\">居中</option>","<option value=\"middle\">' + _('Middle') + '</option>"),
        array("<option value=\"center\">居中</option>","<option value=\"center\">' + _('Center') + '</option>"),
        array("<option value=\"baseline\">基线</option>","<option value=\"baseline\">' + _('Baseline') + '</option>"),
        array("<option value=\"bottom\">底边</option>","<option value=\"bottom\">' + _('Bottom') + '</option>"),
        array("<option selected=\"selected\" value=\"\">无</option>","<option selected=\"selected\" value=\"\">' + _('None') + '</option>"),
        array("<option value=\"row\">第一行</option>","<option value=\"row\">' + _('First row') + '</option>"),
        array("<option value=\"col\">第一列</option>","<option value=\"col\">' + _('First column') + '</option>"),
        array("<option value=\"both\">第一行和第一列</option>","<option value=\"both\">' + _('Both') + '</option>"),
        array("xhEditor是基于jQuery开发的跨平台轻量XHTML编辑器，基于<a href=\"http://www.gnu.org/licenses/lgpl.html\" target=\"_blank\">LGPL</a>开源协议发布。","' + _('xhEditor is a platform independent WYSWYG XHTML editor based by jQuery,released as Open Source under <a href=\"http://www.gnu.org/licenses/lgpl.html\" target=\"_blank\">LGPL</a>.') + '"),
        array("name:'默认'","name:_('Default')"),
        array("'smile':'微笑'","'smile':_('Smile')"),
        array("'tongue':'吐舌头'","'tongue':_('Tongue')"),
        array("'titter':'偷笑'","'titter':_('Titter')"),
        array("'laugh':'大笑'","'laugh':_('Laugh')"),
        array("'sad':'难过'","'sad':_('Sad')"),
        array("'wronged':'委屈'","'wronged':_('Wronged')"),
        array("'fastcry':'快哭了'","'fastcry':_('Fast cry')"),
        array("'cry':'哭'","'cry':_('Cry')"),
        array("'wail':'大哭'","'wail':_('Wail')"),
        array("'mad':'生气'","'mad':_('Mad')"),
        array("'knock':'敲打'","'knock':_('Knock')"),
        array("'curse':'骂人'","'curse':_('Curse')"),
        array("'crazy':'抓狂'","'crazy':_('Crazy')"),
        array("'angry':'发火'","'angry':_('Angry')"),
        array("'ohmy':'惊讶'","'ohmy':_('Oh my')"),
        array("'awkward':'尴尬'","'awkward':_('Awkward')"),
        array("'panic':'惊恐'","'panic':_('Panic')"),
        array("'shy':'害羞'","'shy':_('Shy')"),
        array("'cute':'可怜'","'cute':_('Cute')"),
        array("'envy':'羡慕'","'envy':_('Envy')"),
        array("'proud':'得意'","'proud':_('Proud')"),
        array("'struggle':'奋斗'","'struggle':_('Struggle')"),
        array("'quiet':'安静'","'quiet':_('Quiet')"),
        array("'shutup':'闭嘴'","'shutup':_('Shut up')"),
        array("'doubt':'疑问'","'doubt':_('Doubt')"),
        array("'despise':'鄙视'","'despise':_('Despise')"),
        array("'sleep':'睡觉'","'sleep':_('Sleep')"),
        array("'bye':'再见'","'bye':_('Bye')"),
        array("t:'剪切 (Ctrl+X)'","t:_('Cut (Ctrl+X)')"),
        array("t:'复制 (Ctrl+C)'","t:_('Copy (Ctrl+C)')"),
        array("t:'粘贴 (Ctrl+V)'","t:_('Paste (Ctrl+V)')"),
        array("t:'粘贴文本'","t:_('Paste as plain text')"),
        array("t:'段落标签'","t:_('Block tag')"),
        array("t:'字体'","t:_('Font family')"),
        array("t:'字体大小'","t:_('Font size')"),
        array("t:'加粗 (Ctrl+B)'","t:_('Bold (Ctrl+B)')"),
        array("t:'斜体 (Ctrl+I)'","t:_('Italic (Ctrl+I)')"),
        array("t:'下划线 (Ctrl+U)'","t:_('Underline (Ctrl+U)')"),
        array("t:'删除线 (Ctrl+S)'","t:_('Strikethrough (Ctrl+S)')"),
        array("t:'字体颜色'","t:_('Select text color')"),
        array("t:'背景颜色'","t:_('Select background color')"),
        array("t:'全选 (Ctrl+A)'","t:_('SelectAll (Ctrl+A)')"),
        array("t:'删除文字格式'","t:_('Remove formatting')"),
        array("t:'对齐'","t:_('Align')"),
        array("t:'列表'","t:_('List')"),
        array("t:'减少缩进 (Shift+Tab)'","t:_('Outdent (Shift+Tab)')"),
        array("t:'增加缩进 (Tab)'","t:_('Indent (Tab)')"),
        array("t:'超链接 (Ctrl+K)'","t:_('Insert/edit link (Ctrl+K)')"),
        array("t:'取消超链接'","t:_('Unlink')"),
        array("t:'图片'","t:_('Insert/edit image')"),
        array("t:'Flash动画'","t:_('Insert/edit flash')"),
        array("t:'多媒体文件'","t:_('Insert/edit media')"),
        array("t:'表情'","t:_('Emotions')"),
        array("t:'表格'","t:_('Insert a new table')"),
        array("t:'源代码'","t:_('Edit source code')"),
        array("t:'预览'","t:_('Preview')"),
        array("t:'打印 (Ctrl+P)'","t:_('Print (Ctrl+P)')"),
        array("t:'全屏编辑 (Esc)'","t:_('Toggle fullscreen (Esc)')"),
        array("t:'关于 xhEditor'","t:_('About xhEditor')"),
        array("defLinkText:'点击打开链接'","defLinkText:_('Click to open link')"),
        array("'当前textarea处于隐藏状态，请将之显示后再初始化xhEditor，或者直接设置textarea的width和height样式'","_('Current textarea is hidden, please make it show before initialization xhEditor, or directly initialize the height.')"),
		array("'上传文件的扩展名必需为：'","_('Upload file extension required for this: ')"),
		array("'每次只能拖放上传同一类型文件'","_('You can only drag and drop the same type of file.')"),
		array("<title>预览</title>","<title>' + _('Preview') + '</title>"),
		array("文件上传中，请稍候……","' + _('File uploading,please wait...') + '"),
		array("'请不要一次上传超过'+upMultiple+'个文件'","_('Please do not upload more then \{\$upMultiple\} files.').replace('\{\$upMultiple\}', upMultiple)"),
		array("'文件上传中(Esc取消上传)'","_('File uploading(Esc cancel)')"),
		array("' 上传接口发生错误！","_(' upload interface error!') + '"),
		array("返回的错误内容为:","' +  _('return error:') + '"),
		array("title=\"关闭 (Esc)\"","title=\"' + _('Close (Esc)') + '\""),
		array("value=\"取消\"","value=\"' + _('Cancel') + '\""),
		array("'您的浏览器安全设置不允许使用剪切操作，请使用键盘快捷键(Ctrl + X)来完成'","_('Currently not supported by your browser, use keyboard shortcuts(Ctrl+X) instead.')"),
		array("'您的浏览器安全设置不允许使用复制操作，请使用键盘快捷键(Ctrl + C)来完成'","_('Currently not supported by your browser, use keyboard shortcuts(Ctrl+C) instead.')"),
		array("'您的浏览器安全设置不允许使用粘贴操作，请使用键盘快捷键(Ctrl + V)来完成'","_('Currently not supported by your browser, use keyboard shortcuts(Ctrl+V) instead.')"),
		array("'上传文件扩展名必需为: '","_('Upload file extension required for this: ')"),
        array("'上传'","_('Upload')"),
        array("'上传文件'","_('Upload file')"),
        array("arrCmd=['Link','Img','Flash','Media']","arrCmd=['Link','Img','Flash','Media','Video']"),
        /*array("_this.showIframeModal(_('Upload file'),toUrl.substr(1),setUploadMsg,null,null,function(){bShowPanel=true;});","var wh,w=null,h=null,i,s = toUrl.substr(1);
                if ((i=s.indexOf('||')) != -1) {
                    wh = s.substr(0,i);
                    s  = s.substr(i+2);
                    w  = wh.substr(0, wh.indexOf('x'));
                    h  = wh.substr(wh.indexOf('x')+1);
                }
                _this.showIframeModal(_('Upload file'),s,setUploadMsg,w,h,function(){bShowPanel=true;});"),*/

    );
    $content = file_get_contents(ABS_PATH.'/'.$source);
    foreach($translates as $t) {
        $content = str_replace($t[0],$t[1],$content);
    }
    file_put_contents(ABS_PATH.'/'.$target, $content);
}


/**
 * 删除文件夹
 *
 * @param string $path		要删除的文件夹路径
 * @return bool
 */
function rmdirs($path){
    if ($dh=@opendir($path)) {
        while (false !== ($file=readdir($dh))) {
            if ($file != "." && $file != "..") {
                $file_path = $path.'/'.$file;
                is_dir($file_path) ? rmdirs($file_path) : @unlink($file_path);
            }
        }
        closedir($dh);
    }
    return @rmdir($path);
}
/**
 * 批量创建目录
 *
 * @param string $path   文件夹路径
 * @param int    $mode   权限
 * @return bool
 */
function mkdirs($path, $mode = 0777){
    if (!is_dir($path)) {
        mkdirs(dirname($path), $mode);
        return mkdir($path, $mode);
    }
    return true;
}