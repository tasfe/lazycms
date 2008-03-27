<?php System::header();?>
<?php echo menu($module->L('title').'|'.url('Archives').'|true;'.$module->L('common/addsort').'|'.url('Archives','EditSort').';'.$module->L('common/addpage').'|'.url('Archives','Edit'));?>
<div class="content">
    <?php echo $module->outHTML;?>
</div>
<?php System::footer();?>