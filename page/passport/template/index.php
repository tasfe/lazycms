<?php System::header();?>
<?php echo menu($module->L('list/group/@title').'|#|true;'.$module->L('common/addgroup').'|'.url(C('CURRENT_MODULE'),'GroupEdit').';'.$module->L('common/adduser').'|'.url(C('CURRENT_MODULE'),'Edit').';'.$module->L('common/leadin').'|'.url(C('CURRENT_MODULE'),'LeadIn'));?>
<div class="content">
    <?php echo $module->outHTML;?>
</div>
<?php System::footer();?>