<?php System::header();?>
<?php echo menu($module->L('title').'|#|true;'.$module->L('common/addsort').'|'.url(C('CURRENT_MODULE'),'EditSort').';'.$module->L('common/addpage').'|'.url(C('CURRENT_MODULE'),'Edit'));?>
<div class="content">
    <?php echo $module->outHTML;?>
</div>
<?php System::footer();?>