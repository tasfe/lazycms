<?php System::header();?>
<?php echo menu($module->L('config/@title').'|'.url('System','Config').';'.$module->L('module/@title').'|#|true;'.$module->L('diymenu/@title').'|'.url('System','DiyMenu').';'.$module->L('account/@title').'|'.url('System','MyAccount'));?>
<div class="content">
  <?php echo $module->outHTML;?>
</div>
<?php System::footer();?>
