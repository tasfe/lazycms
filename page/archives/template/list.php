<?php System::header();?>
<?php echo menu($module->L('title').'|'.url('Archives').';'.$module->L('common/addsort').'|'.url('Archives','EditSort').';'.$menu);?>
<div class="content">
    <?php echo $module->outHTML;?>
</div>
<?php System::footer();?>