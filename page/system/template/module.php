<?php System::header();?>
<?php echo menu($module->L('module/@title').'|#|true;'.$module->L('module/leadin/@title').'|'.url('System','ModuleLeadin'));?>
<div class="content">
  <?php echo $module->outHTML;?>
</div>
<?php System::footer();?>
