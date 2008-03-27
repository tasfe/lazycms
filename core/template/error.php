<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo L('error/system');?></title>
<style type="text/css">
body{ font-family: Verdana; font-size:14px;}
a{text-decoration:none;color:#174B73;}
a:hover{ text-decoration:none;color:#FF6600;}
.red{ color:red; font-weight:bold;}
.notice{ padding:10px; margin:5px; color:#666; background:#FCFCFC; border:1px solid #E0E0E0; }
    .notice h2{ border-bottom:1px solid #DDD; font-size:25px; margin-top:0; padding:8px 0;}
    .title{ margin:4px 0; color:#F60; font-weight:bold;}
    .message,#trace{ padding:1em; border:solid 1px #000; margin:10px 0; background:#FFD; line-height:150%;}
    .message{ background:#FFD; color:#2E2E2E; border:1px solid #E0E0E0; }
    #trace{ background:#E7F7FF; border:1px solid #E0E0E0; color:#535353;}
#footer{ color:#FF3300; margin:5pt auto; font-weight:bold; text-align:center;}
    #footer sup{color:gray;font-size:9pt}
    #footer span{color:silver}
</style>
</head>

<body>
<div class="notice">
    <h2><?php echo L('error/system');?></h2>
    <div><?php echo L('other/select');?> [ <a href="<?php echo getURL();?>"><?php echo L('common/tautology');?></a> ] [ <a href="javascript:history.back()"><?php echo L('common/back');?></a> ] <?php echo L('other/or');?> [ <a href="<?php echo C('SITE_BASE');?>" target="_top"><?php echo L('other/gohome');?></a> ]</div>
    <?php if(isset($e['file'])):?>
    <p><strong><?php echo L('error/position');?>:</strong>　FILE: <span class="red"><?php echo $e['file'] ;?></span>　LINE: <span class="red"><?php echo $e['line'];?></span></p>
    <?php endif?>
    <p class="title">[ <?php echo L('error/errinfo');?> ]</p>
    <p class="message"><?php echo $e['message'];?></p>
    <?php if(isset($e['trace'])):?>
    <p class="title">[ TRACE ]</p>
    <p id="trace"><?php echo nl2br($e['trace']);?></p> 
    <?php endif?>
</div>
<div id="footer"> LazyCMS <sup><?php echo LAZY_VERSION;?></sup></div>
</body>
</html>
