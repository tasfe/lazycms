<?php System::header();?>
<?php echo menu($module->L('list/group/@title').'|'.url(C('CURRENT_MODULE')).';'.$module->L('common/addgroup').'|'.url(C('CURRENT_MODULE'),'GroupEdit').';'.$menu.';'.$module->L('common/leadin').'|'.url(C('CURRENT_MODULE'),'LeadIn').';'.L('config/@title').'|'.url(C('CURRENT_MODULE'),'Config'));?>
<div class="content">
    <?php echo $module->outHTML;?>
</div>
<?php System::footer();?>