<?php System::header();?>
<?php echo menu($module->L('title').'|'.url(C('CURRENT_MODULE'),'Admin').';'.$module->L('common/tag').'|'.url(C('CURRENT_MODULE'),'Admin','tag=1').';'.$module->L('common/config').'|'.url(C('CURRENT_MODULE'),'Config').'|true;'.$module->L('common/fields').'|'.url(C('CURRENT_MODULE'),'Fields').';'.$module->L('common/addfields').'|'.url(C('CURRENT_MODULE'),'FieldsEdit'));?>
<div class="content">
  <form action="<?php echo url(C('CURRENT_MODULE'),'Config');?>" method="post" class="lz_form">
    <?php echo $module->succeed();?>
    <?php echo $module->outHTML;?>
    <?php echo $module->but('submit');?>
  </form>
</div>
<?php System::footer();?>
