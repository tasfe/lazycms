<?php System::header();?>
<?php $module->validate('outjs');?>
<?php echo menu($module->L('config/@title').'|'.url('System','Config').'|true;'.$module->L('module/@title').'|'.url('System','Module').';'.$module->L('diymenu/@title').'|'.url('System','DiyMenu').';'.$module->L('account/@title').'|'.url('System','MyAccount'));?>
<div class="content">
    <form action="<?php echo url('System','Config');?>" method="post" class="lz_form">
        <?php echo $module->succeed();?>
        <p><label><?php echo $module->L('config/sitename');?></label><input class="in2" type="text" id="sitename" name="sitename" maxlength="50" value="<?php echo $sitename;?>" /></p>
        <p><label><?php echo $module->L('config/sitemail');?></label><input class="in3" type="text" id="sitemail" name="sitemail" maxlength="100" value="<?php echo $sitemail;?>" /></p>
        <p><label><?php echo $module->L('config/keywords').' '.$module->L('config/tip/keywords');?></label><textarea name="keywords" id="keywords" rows="10" class="in5"><?php echo $keywords;?></textarea></p>
        <p><label><?php echo $module->L('config/lockip').' '.$module->L('config/tip/lockip');?></label><textarea name="lockip" id="lockip" rows="10" class="in5"><?php echo $lockip;?></textarea></p>
        <?php echo $module->but('save');?>
    </form>
</div>
<?php System::footer();?>