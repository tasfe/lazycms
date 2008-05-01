<?php System::header();?>
<?php $module->validate('outjs');?>
<?php echo menu($module->L('module/@title').'|'.url('System','Module').';'.$module->L('module/notinstall').'|'.url('System','Module','View=NotInstall').';'.$module->L('module/leadin/@title').'|'.url('System','ModuleLeadin').'|#|true');?>
<div class="content">
    <form action="<?php echo url('System','ModuleLeadin');?>" enctype="multipart/form-data" method="post" class="lz_form">
        <p><label><?php echo $module->L('module/leadin');?></label><input id="module" name="module" type="file" class="in3"/></p>
        <?php echo $module->but('submit');?>
    </form>
</div>
<?php System::footer();?>
