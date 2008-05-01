<?php System::header();?>
<?php echo menu($module->L('title').'|#|true;'.$module->L('common/add').'|'.url(C('CURRENT_MODULE'),'Edit').';'.L('config/@title').'|'.url(C('CURRENT_MODULE'),'Config'));?>
<div class="content">
    <?php echo $module->outHTML;?>
</div>
<?php System::footer();?>