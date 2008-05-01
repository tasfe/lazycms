<?php System::header();?>
<?php echo menu($module->L('title').'|'.url(C('CURRENT_MODULE'),'Admin').';'.$module->L('common/tag').'|'.url(C('CURRENT_MODULE'),'Admin','tag=1').';'.L('config/@title').'|'.url(C('CURRENT_MODULE'),'Config').';'.$module->L('common/fields').'|#|true;'.$module->L('common/addfields').'|'.url(C('CURRENT_MODULE'),'FieldsEdit'));?>
<div class="content">
  <?php echo $module->outHTML;?>
</div>
<?php System::footer();?>
