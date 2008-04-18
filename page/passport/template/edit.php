<?php System::header();?>
<?php $module->validate('outjs');?>
<?php echo menu($module->L('list/group/@title').'|#|true;'.$module->L('common/addgroup').'|'.url(C('CURRENT_MODULE'),'GroupEdit').';'.$module->L('common/adduser').'|'.url(C('CURRENT_MODULE'),'Edit').';'.$module->L('common/leadin').'|'.url(C('CURRENT_MODULE'),'LeadIn'));?>
<div class="content">
    <form action="<?php echo url(C('CURRENT_MODULE'),'Edit');?>" method="post" class="lz_form">
        
        <?php echo $module->outHTML;?>
        <?php echo $module->but('submit');?>
    </form>
</div>
<?php System::footer();?>
