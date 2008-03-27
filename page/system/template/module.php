<?php System::header();?>
<?php echo menu($module->L('config/@title').'|'.url('System','Config').';'.$module->L('module/@title').'|'.url('System','Module').'|true;'.$module->L('diymenu/@title').'|'.url('System','DiyMenu').';'.$module->L('account/@title').'|'.url('System','MyAccount'));?>
<div class="content">
  <form action="<?php echo url('System','ModuleSet');?>" method="post" class="lz_form">
  <?php echo $module->outHTML;?>
  </form>
</div>
<?php System::footer();?>
