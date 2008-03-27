<?php System::header();?>
<?php $module->validate('outjs');?>
<?php echo menu($module->L('models/@title').'|'.url('System','Models').';'.$module->L('models/add').'|'.url('System','ModelEdit').';'.$module->L('models/leadin').'|#|true');?>
<div class="content">
    <form action="<?php echo url('System','ModelLeadIn');?>" enctype="multipart/form-data" method="post" class="lz_form">
        <p><label><?php echo $module->L('models/label/leadin');?></label><input id="model" name="model" type="file" class="in3"/></p>
        <?php echo $module->but('submit');?>
    </form>
</div>
<?php System::footer();?>
