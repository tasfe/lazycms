<?php System::header();?>
<?php echo menu($module->L('title').'|'.url(C('CURRENT_MODULE')).';'.$menu.';'.L('config/@title').'|'.url(C('CURRENT_MODULE'),'Config'));?>
<div class="content">
  <?php echo $module->outHTML;?>
</div>
<?php System::footer();?>
