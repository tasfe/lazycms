<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $module->L('admin/title');?> | LazyCMS <?php echo $module->system['systemver'];?></title>
<link href="../system/images/iframe.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../system/js/jquery.js"></script>
<script type="text/javascript" src="../system/js/jquery.lazycms.js"></script>
<script type="text/javascript">
//控制窗体大小 *** *** www.LazyCMS.net *** ***
$(document).ready(function(){
    resizeWeb($('#toolbar').height());
    window.onresize = document.getElementsByTagName("body")[0].onresize = function (){
        resizeWeb($('#toolbar').height());
    }
});
//调整窗口大小 *** *** www.LazyCMS.net *** ***
function resizeWeb(xx){
    var win = winSize();
    var reg = /^\d+$/;
    var num = win.h - 1 - (xx?xx:0);
    if (reg.test(num)){
        $('#main').height(num);
    }
}
</script>
</head>

<body scroll="no">
<iframe src="<?php echo url('System','Main');?>" name="main" id="main" width="100%" marginwidth="0" height="95%" marginheight="0" scrolling="yes" frameborder="0" onload="resizeWeb($('#toolbar').height());"></iframe>
<div id="toolbar">
    <div class="logo"><a href="<?php echo url('System');?>"><img src="../system/images/toolbar_logo.png" /></a></div>
    <div class="close"><a href="javascript:;">&nbsp;</a></div>
</div>
</body>
</html>
