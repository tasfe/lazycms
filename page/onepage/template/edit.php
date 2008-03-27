<?php System::header();?>
<?php $module->validate('outjs');?>
<?php echo menu($module->L('title').'|'.url('Onepage').';'.$menu);?>
<div class="content">
    <form action="<?php echo url('Onepage','Edit');?>" method="post" class="lz_form">
        <p><label><?php echo $module->L('label/name');?></label><input class="in3" type="text" id="onename" name="onename" maxlength="50" value="<?php echo $onename;?>" /></p>
        <p><label><?php echo $module->L('label/title');?></label><input class="in4" type="text" id="onetitle" name="onetitle" maxlength="100" value="<?php echo $onetitle;?>" /></p>
        <p><label><?php echo $module->L('label/path');?></label><input class="in4" type="text" id="onepath" name="onepath" maxlength="100" value="<?php echo $onepath;?>" /></p>
        <p><label><?php echo $module->L('label/content');?></label><?php echo $module->editor('onecontent',$onecontent);?></p>
        <p><label><?php echo $module->L('label/keyword');?></label><input class="in4" type="text" id="onekeyword" name="onekeyword" maxlength="50" value="<?php echo $onekeyword;?>" /></p>
        <p><label><?php echo $module->L('label/description');?></label><textarea name="onedescription" id="onedescription" rows="5" class="in4"><?php echo $onedescription;?></textarea></p>
        <p><label><?php echo $module->L('label/template1');?></label>
            <select name="onetemplate1">
            <?php echo formTmp('<option value="#value#"#selected#>#name#</option>',$onetemplate1);?>
            </select>
        </p>
        <p><label><?php echo $module->L('label/template2');?></label>
            <select name="onetemplate2">
            <?php echo formTmp('<option value="#value#"#selected#>#name#</option>',$onetemplate2,C('CURRENT_MODULE'));?>
            </select>
        </p>
        <input name="oneid" type="hidden" value="<?php echo $oneid;?>" />
        <input name="oldpath" type="hidden" value="<?php echo $onepath;?>" />
        <?php echo $module->but('submit');?>
    </form>
</div>
<?php System::footer();?>