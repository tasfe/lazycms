<?php System::header();?>
<?php echo menu($menu.$module->L('module/leadin/@title').'|'.url('System','ModuleLeadin'));?>
<div class="content">
  <?php echo $module->outHTML;?>
</div>
<?php System::footer();?>
