var debug = false;
// 加载所需的JavaScript文件，必须在文档完全载入之前加载。
LoadScript('json');LoadScript('blockUI');

//图片循环
var ImgName = Array();
var ImgNum  = 15;//图片数量
for(var i=1;i<=ImgNum;i++){
    ImgName[i-1] = template + "/images/shows/PIC_" + i + ".jpg";
}

function playImg(id){
    var isIE   = document.all ? true : false;
    var objImg = document.getElementById(id);
    var num    = Math.round(Math.random()*(ImgName.length-2));//数值＝总图片数量-2
    if (isIE) {
        objImg.style.filter = "blendTrans(Duration=1.5)";
　      objImg.filters[0].apply();
    }
　  objImg.src = ImgName[num];
    if (isIE) { objImg.filters[0].play(); }
    var myTimeOut = setTimeout("playImg('" + id + "')",6000);
}

window.onload=function(){ playImg('showImg'); }

//不允许被放入框架中
if(top.location !== self.location){ top.location=self.location; }

// 函数加载JavaScript *** *** www.LazyCMS.net *** ***
function LoadScript(plugin){
    if (plugin.indexOf('/')!=-1) {
        var url = $("script[@src*=jquery.common]").attr("src").replace("system/js/jquery.common.js",plugin);
    } else {
        var url = $("script[@src*=jquery.common]").attr("src").replace("jquery.common.js","jquery." + plugin + ".js");
    }
    document.write('<scr' + 'ipt type="text/javascript" src="' + url + '"><\/scr' + 'ipt>');
}