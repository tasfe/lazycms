<?php System::header();?>
<?php echo menu($module->L('admin/list').'|#|true;'.$module->L('admin/add').'|'.url('System','AdminEdit'));?>
<div class="content">
  <?php echo $module->outHTML;?>
</div>
<?php System::footer();?>
