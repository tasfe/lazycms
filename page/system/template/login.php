<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $module->L('login/@title');?> | LazyCMS v1.1</title>
<script type="text/javascript" src="../system/js/jquery.js"></script>
<script type="text/javascript" src="../system/js/jquery.lazycms.js"></script>
<style type="text/css">
body{ width:100%; height:100%; margin:0; padding:0; background:#FBFBFB; text-align:center;}
body,th,td,p{ line-height:130%; font-family:Verdana; font-size:12px;}
dl,dt,dd{ margin:0; padding:0;}
#login{ width:270px; margin:15% auto 0 auto; border-style:solid; border-width:3px; border-color:#CECECE #666 #666 #CECECE;}
#login dt{ font-size:14px; font-weight:bold; text-align:left; letter-spacing:3px; color:#FFFFFF; padding:5px 10px; background:#193441; margin-bottom:10px; border-bottom:solid 3px #CECECE;}
#login dd{ padding:5px 10px; clear:both;}
#login dd label{ width:70px; font-weight:bold; display:block; float:left;}
#login dd label.error{ width:150px; font-weight:normal; text-align:left; display:inline; color:#993300; clear:both; cursor:pointer; margin-left:70px;}
#adminname, #adminpass{ width:150px; display:block; float:left; border-style:solid; border-width:1px; border-color:#666 #CECECE #CECECE #666; background:#FBFBFB;}
#login dd button{ border-style:solid; border-width:1px; border-color:#CECECE #666 #666 #CECECE; background:#F5F5F5; padding:2px 8px; letter-spacing:3px;}
#login .save{width:auto; display:inline; float:none; font-weight:normal;}
</style>
<?php $module->validate('outjs');?>
</head>

<body>
<form id="form1" name="form1" method="post" action="<?php echo url('System','Login');?>" target="_top">
<dl id="login">
    <dt><?php echo $module->L('login/@title');?></dt>
    <dd><label><?php echo $module->L('login/name');?></label><input name="adminname" type="text" id="adminname" maxlength="50" value="<?php echo $adminname;?>"/></dd>
    <dd><label><?php echo $module->L('login/pass');?></label><input name="adminpass" type="password" id="adminpass" maxlength="50"/></dd>
    <dd><input name="save" type="checkbox" id="save" value="1"<?php if ($save==1){ echo ' checked="checked"'; }?> /><label for="save" class="save"><?php echo $module->L('login/save');?></label> &nbsp; &nbsp; <button type="submit"><?php echo $module->L('login/submit');?></button></dd>
</dl>
</form>

</body>
</html>
