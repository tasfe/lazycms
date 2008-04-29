<?php System::header();?>
<?php echo menu($module->L('title').'|#|true;'.$module->L('common/add').'|'.url(C('CURRENT_MODULE'),'Edit'));?>
<div class="content">
    <?php echo $module->outHTML;?>
</div>
<?php System::footer();?>