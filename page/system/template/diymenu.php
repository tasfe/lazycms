<?php System::header();?>
<?php echo menu($module->L('config/@title').'|'.url('System','Config').';'.$module->L('module/@title').'|'.url('System','Module').';'.$module->L('diymenu/@title').'|'.url('System','DiyMenu').'|true;'.$module->L('account/@title').'|'.url('System','MyAccount'));?>
<div class="content">
    <form action="<?php echo url('System','DiyMenu');?>" method="post" class="lz_form">
        <?php echo $module->succeed();?>
        <p><label><?php echo $module->L('diymenu/@title');?></label><textarea name="diymenu" rows="25" cols="70" class="in6"><?php echo $diyMenu;?></textarea></p>
        <p><pre><?php echo $module->L('diymenu/memo');?></pre></p>
        <?php echo $module->but('save');?>
    </form>
</div>
<?php System::footer();?>