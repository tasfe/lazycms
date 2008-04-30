<?php System::header();?>
<?php echo menu($menu.';'.$module->L('common/config').'|'.url(C('CURRENT_MODULE'),'Config').';'.$module->L('common/fields').'|'.url(C('CURRENT_MODULE'),'Fields').';'.$module->L('common/addfields').'|'.url(C('CURRENT_MODULE'),'FieldsEdit'));?>
<div class="content">
    <?php echo $module->outHTML;?>
</div>
<?php System::footer();?>