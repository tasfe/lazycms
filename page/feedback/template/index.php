<?php $module->validate('outjs');?>
<form action="<?php echo url(C('CURRENT_MODULE'));?>" method="post" class="lz_form">
    <p><label><?php echo $module->L('label/title');?></label><input class="in3" type="text" id="fbtitle" name="fbtitle" value="<?php echo $fbtitle;?>" /></p>
    <p><label><?php echo $module->L('label/content');?></label><?php echo $module->editor('fbcontent',$fbcontent,$editor);?></p>
    <?php echo $module->outHTML;?>
    <?php echo $module->but('submit');?>
</form>