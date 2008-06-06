
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
