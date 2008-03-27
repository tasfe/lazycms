<?php System::header();?>
<?php echo menu($module->L('models/@title').'|#|true;'.$module->L('models/add').'|'.url('System','ModelEdit').';'.$module->L('models/leadin').'|'.url('System','ModelLeadIn'));?>
<div class="content">
  <?php echo $module->outHTML;?>
</div>
<?php System::footer();?>
