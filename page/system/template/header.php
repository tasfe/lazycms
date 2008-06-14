<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo L('admin/title');?></title>
<link href="<?php echo C('SITE_BASE').C('PAGES_PATH');?>/system/images/style.css" rel="stylesheet" type="text/css" />
<?php if (is_file(LAZY_PATH.C('PAGES_PATH').'/'.strtolower(C('CURRENT_MODULE')).'/images/common.js')):?>
<script type="text/javascript">var module="<?php echo strtolower(C('CURRENT_MODULE'));?>";</script>
<?php endif;?>
<script type="text/javascript" src="<?php echo C('SITE_BASE').C('PAGES_PATH');?>/system/js/jquery.js"></script>
<script type="text/javascript" src="<?php echo C('SITE_BASE').C('PAGES_PATH');?>/system/js/jquery.lazycms.js"></script>
<script type="text/javascript">
if (top.location == self.location) { top.location = '<?php echo url("System");?>'; }
// 获取最新版本
var lz_verison = "<?php echo url('System','Version');?>";
$(function(){
    $('#verison','.content .main').load(lz_verison);
});
</script>
</head>

<body>
<div id="top">
    <div class="logo">
        <a href="<?php echo url('System','Main');?>"><img src="<?php echo C('SITE_BASE').C('PAGES_PATH');?>/system/images/logo.png" alt="LazyCMS <?php echo $module->system['systemver'];?>" /></a>
        <ul>
            <li><a href="<?php echo C('SITE_BASE');?>" target="_blank"><?php echo L('common/browhome');?></a></li>
            <li><a href="http://www.lazycms.net/" target="_blank"><?php echo L('parameters/osite');?></a></li>
            <li><a href="http://forums.lazycms.net/" target="_blank"><?php echo L('parameters/forums');?></a></li>
        </ul>
    </div>
    <div class="menu">
        <strong><?php echo Cookie::get('adminname');?></strong> -
        <a href="<?php echo url('System','Admin');?>">[<?php echo L('menu/admin');?>]</a> -
        <a href="<?php echo url('System','Config');?>">[<?php echo L('menu/config');?>]</a> -
        <a href="<?php echo url('System','Module');?>">[<?php echo L('menu/module');?>]</a> -
        <a href="<?php echo url('System','DiyMenu');?>">[<?php echo L('menu/diymenu');?>]</a> -
        <a href="<?php echo url('System','MyAccount');?>">[<?php echo L('menu/account');?>]</a> -
        <a href="<?php echo url('System','Logout');?>" target="_top" onclick="return confirm('<?php echo L('confirm/logout');?>');">[<?php echo L('login/out');?>]</a>
    </div>
    <ul class="diymenu">
        <?php echo $module->diymenu();?>
    </ul>
</div>
<div id="main">