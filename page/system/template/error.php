<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo L('error/system');?></title>
<style type="text/css">
body{ width:100%; height:100%; margin:0; padding:0; background:#FBFBFB;}
body,th,td,p{ line-height:130%; font-family:Verdana; font-size:12px;}
dl,dt,dd{ margin:0; padding:0;}
#poping{ width:450px; margin:13% auto 0 auto; border-style:solid; border-width:2px; border-color:#CECECE #666 #666 #CECECE;}
#poping dt{ font-size:14px; font-weight:bold; letter-spacing:1px; color:#FFFFFF; padding:5px 10px; background:#193441; border-bottom:solid 3px #CECECE; display:table;zoom:100%; margin-bottom:5px;}
#poping dt span{ float:left;}
#poping dt a{ float:right; color:#FFFFFF; text-decoration:none;}
#poping dd{ padding:5px 10px; clear:both; line-height:150%;}
</style>
</head>

<body>
<dl id="poping">
    <dt><span><?php echo L('error/system');?></span><a href="javascript:top.window.close();">×</a></dt>
    <dd><?php echo $Message;?><br/>[ <a href="javascript:history.back();"><?php echo L('common/back');?></a> ] [ <a href="javascript:top.window.close();"><?php echo L('common/close');?></a> ]</dd>
</dl>
</body>
</html>
