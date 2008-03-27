<?php System::header();?>
<?php echo menu($module->L('admin/@title').'|'.url('System','Main').';'.$module->L('log/@title').'|'.url('System','Log').'|true');?>
<div class="content">
  <?php echo $module->outHTML;?>
</div>
<?php System::footer();?>
