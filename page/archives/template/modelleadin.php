<?php System::header();?>
<?php $module->validate('outjs');?>
<?php echo menu($module->L('title').'|'.url(C('CURRENT_MODULE')).';'.$module->L('models/@title').'|'.url(C('CURRENT_MODULE'),'Models').';'.$module->L('models/add').'|'.url(C('CURRENT_MODULE'),'ModelEdit').';'.$module->L('models/leadin').'|#|true;'.L('config/@title').'|'.url(C('CURRENT_MODULE'),'Config'));?>
<div class="content">
    <form action="<?php echo url(C('CURRENT_MODULE'),'ModelLeadIn');?>" enctype="multipart/form-data" method="post" class="lz_form">
        <p><label><?php echo $module->L('models/label/leadin');?></label><input id="model" name="model" type="file" class="in3"/></p>
        <?php echo $module->but('submit');?>
    </form>
</div>
<?php System::footer();?>
