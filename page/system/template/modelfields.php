<?php System::header();?>
<?php echo menu($module->L('models/@title').'|'.url('System','Models').';'.$module->L('models/add').'|'.url('System','ModelEdit').';'.$module->L('models/field/@title').'|'.url('System','ModelFields').'|true;'.$module->L('models/field/add').'|'.url('System','ModelFieldsEdit','modelid='.$modelid));?>
<div class="content">
  <?php echo $module->outHTML;?>
</div>
<?php System::footer();?>
