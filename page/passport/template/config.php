<?php System::header();?>
<?php echo menu($module->L('list/group/@title').'|'.url(C('CURRENT_MODULE')).';'.$module->L('common/addgroup').'|'.url(C('CURRENT_MODULE'),'GroupEdit').';'.$module->L('common/adduser').'|'.url(C('CURRENT_MODULE'),'Edit').';'.$module->L('common/leadin').'|'.url(C('CURRENT_MODULE'),'LeadIn').';'.L('config/@title').'|'.url(C('CURRENT_MODULE'),'Config').'|#|true');?>
<div class="content">
  <form action="<?php echo url(C('CURRENT_MODULE'),'Config');?>" method="post" class="lz_form">
    <?php echo $module->succeed();?>
    <?php echo $module->outHTML;?>
    <p><label><?php echo $module->L('config/label/reservename');?></label><textarea name="reservename" id="reservename" rows="5" class="in5"><?php echo $reservename;?></textarea></p>
    <p><label><?php echo $module->L('config/label/navlogin');?></label><textarea name="navlogin" id="navlogin" rows="8" class="in5"><?php echo $navlogin;?></textarea></p>
    <p><label><?php echo $module->L('config/label/navlogout');?></label><textarea name="navlogout" id="navlogout" rows="8" class="in5"><?php echo $navlogout;?></textarea></p>
    <p><label><?php echo $module->L('config/label/navuser');?></label><textarea name="navuser" id="navuser" rows="8" class="in5"><?php echo $navuser;?></textarea></p>
    <?php echo $module->but('submit');?>
  </form>
</div>
<?php System::footer();?>
