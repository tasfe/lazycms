var debug = false;
// 加载所需的JavaScript文件，必须在文档完全载入之前加载。
LoadScript('json');LoadScript('blockUI');
// 载入公用函数和后台专用函数 *** *** www.LazyCMS.net *** ***
LoadScript('common');LoadScript('lazycms.funs');
// 载入日历控制脚本 *** *** www.LazyCMS.net *** ***
LoadScript('date');LoadScript('bgiframe');LoadScript('datePicker');
// 实现自动加载模块JavaScript类库
if (typeof module!='undefined') { LoadScript(module+'/images/common.js'); }
// 实现公用方法 *** *** www.LazyCMS.net *** ***
$(function(){
    loadImage(path()+'/system/images/os/dir0.gif',path()+'/system/images/os/dir1.gif',path()+'/system/images/loading.gif');
    // td变色
    $('.main tr,.lz_table tr').hover(function(){
        $(this).addClass('selected');
    },function(){
        $(this).removeClass('selected');
    });
    // 在每个li上面绑定haver事件，鼠标触发下拉菜单
    $(".diymenu li").each(function(i){
        $(this).hover(function(){
            $('ul',this).show();
        },function(){
            $('ul',this).hide();
        });
    });
    // *** *** www.LazyCMS.net *** ***  日期选择器设置 开始  *** *** www.LazyCMS.net *** ***
    Date.firstDayOfWeek = 7;
    Date.format = 'yyyy-mm-dd';
    Date.dayNames = ['日', '一', '二', '三', '四', '五', '六'];
    Date.monthNames = ['一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月'];
    $.dpText = {
        TEXT_PREV_YEAR      :   '上一年',
        TEXT_PREV_MONTH     :   '上一月',
        TEXT_NEXT_YEAR      :   '下一年',
        TEXT_NEXT_MONTH     :   '下一月',
        TEXT_CLOSE          :   '关闭',
        TEXT_CHOOSE_DATE    :   '选择日期'
    };
    $(".date-pick").datePicker({startDate:'1996-01-01'}).dpSetPosition($.dpConst.POS_BOTTOM, $.dpConst.POS_LEFT);
        
    // *** *** www.LazyCMS.net *** ***  日期选择器设置 结束  *** *** www.LazyCMS.net *** ***
    $('.___images').each(function(i){
        $(this).attr('target','_blank');
        $(this).hover(function(){
            $(this).after('<div class="pop_image"><img src="' + this + '" /></div>');
        },function(){
            $(this).next('.pop_image').remove();
        });
    });
});
// 函数加载JavaScript *** *** www.LazyCMS.net *** ***
function LoadScript(plugin){
    if (plugin.indexOf('/')!=-1) {
        var url = $("script[@src*=jquery.lazycms]").attr("src").replace("system/js/jquery.lazycms.js",plugin);
    } else {
        var url = $("script[@src*=jquery.lazycms]").attr("src").replace("jquery.lazycms.js","jquery." + plugin + ".js");
    }
    document.write('<scr' + 'ipt type="text/javascript" src="' + url + '"><\/scr' + 'ipt>');
}


