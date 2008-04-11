<?php System::header();?>
<?php $module->validate('outjs');?>
<?php echo menu($module->L('list/group/@title').'|'.url(C('CURRENT_MODULE')).';'.$module->L('common/addgroup').'|'.url(C('CURRENT_MODULE'),'GroupEdit').';'.$module->L('common/adduser').'|'.url(C('CURRENT_MODULE'),'Edit').';'.$module->L('common/leadin').'|'.url(C('CURRENT_MODULE'),'LeadIn').'|true');?>
<div class="content">
    <form action="<?php echo url(C('CURRENT_MODULE'),'LeadIn');?>" enctype="multipart/form-data" method="post" class="lz_form">
        <p><label><?php echo $module->L('label/leadin');?></label><input id="group" name="group" type="file" class="in3"/></p>
        <?php echo $module->but('submit');?>
    </form>
</div>
<?php System::footer();?>
