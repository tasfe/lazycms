<?php System::header();?>
<?php echo menu($module->L('title').'|'.url(C('CURRENT_MODULE')).';'.$module->L('common/add').'|'.url(C('CURRENT_MODULE'),'Edit').';'.L('config/@title').'|'.url(C('CURRENT_MODULE'),'Config').'|#|true');?>
<div class="content">
  <form action="<?php echo url(C('CURRENT_MODULE'),'Config');?>" method="post" class="lz_form">
    <?php echo $module->succeed();?>
    <?php echo $module->outHTML;?>
    <?php echo $module->but('submit');?>
  </form>
</div>
<?php System::footer();?>
