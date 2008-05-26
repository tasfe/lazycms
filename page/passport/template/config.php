<?php System::header();?>
<?php echo menu($module->L('list/group/@title').'|'.url(C('CURRENT_MODULE')).';'.$module->L('common/addgroup').'|'.url(C('CURRENT_MODULE'),'GroupEdit').';'.$module->L('common/adduser').'|'.url(C('CURRENT_MODULE'),'Edit').';'.$module->L('common/leadin').'|'.url(C('CURRENT_MODULE'),'LeadIn').';'.L('config/@title').'|'.url(C('CURRENT_MODULE'),'Config').'|#|true');?>
<div class="content">
  <form action="<?php echo url(C('CURRENT_MODULE'),'Config');?>" method="post" class="lz_form">
    <?php echo $module->succeed();?>
    <?php echo $module->outHTML;?>
    <?php echo $module->but('submit');?>
  </form>
</div>
<?php System::footer();?>
