<?php System::header();?>
<?php echo menu($module->L('list/group/@title').'|'.url(C('CURRENT_MODULE')).';'.$module->L('list/field/@title').'|#|true;'.$module->L('list/field/add').'|'.url(C('CURRENT_MODULE'),'FieldsEdit','groupid='.$groupid).';'.$module->L('common/addgroup').'|'.url(C('CURRENT_MODULE'),'GroupEdit').';'.$module->L('common/adduser').'|'.url(C('CURRENT_MODULE'),'Edit').';'.$module->L('common/leadin').'|'.url(C('CURRENT_MODULE'),'LeadIn').';'.L('config/@title').'|'.url(C('CURRENT_MODULE'),'Config'));?>
<div class="content">
    <?php echo $module->outHTML;?>
</div>
<?php System::footer();?>