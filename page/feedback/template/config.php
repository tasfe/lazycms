<?php System::header();?>
<?php echo menu($module->L('title').'|'.url(C('CURRENT_MODULE'),'Admin').';'.$module->L('common/tag').'|'.url(C('CURRENT_MODULE'),'Admin','tag=1').';'.$module->L('common/config').'|'.url(C('CURRENT_MODULE'),'Config').'|true;'.$module->L('common/fields').'|'.url(C('CURRENT_MODULE'),'Fields'));?>
<div class="content">
  <?php echo $module->outHTML;?>
</div>
<?php System::footer();?>
