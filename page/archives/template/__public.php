<?php System::header();?>
<?php echo menu($module->L('title').'|'.url(C('CURRENT_MODULE')).';'.$menu);?>
<div class="content">
  <?php echo $module->outHTML;?>
</div>
<?php System::footer();?>
