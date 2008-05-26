<?php System::header();?>
<?php $module->validate('outjs');?>
<?php echo menu($module->L('list/group/@title').'|'.url(C('CURRENT_MODULE')).';'.$module->L('list/field/@title').'|'.url(C('CURRENT_MODULE'),'Fields','groupid='.$groupid).';'.$menu.';'.$module->L('common/addgroup').'|'.url(C('CURRENT_MODULE'),'GroupEdit').';'.$module->L('common/adduser').'|'.url(C('CURRENT_MODULE'),'Edit').';'.$module->L('common/leadin').'|'.url(C('CURRENT_MODULE'),'LeadIn').';'.L('config/@title').'|'.url(C('CURRENT_MODULE'),'Config'));?>
<div class="content">
    <form action="<?php echo url(C('CURRENT_MODULE'),'FieldsEdit');?>" method="post" class="lz_form">
        <p><label><?php echo $module->L('label/field/name');?></label><input id="fieldname" name="fieldname" type="text" maxlength="50" class="in2" value="<?php echo $fieldname;?>"/></p>
        <p><label><?php echo $module->L('label/field/ename');?></label><input id="fieldename" name="fieldename" type="text" maxlength="50" class="in2" value="<?php echo $fieldename;?>"<?php echo $readonly;?>/>
        <?php if (empty($fieldid)):?>
            <span class="___toggle"><input id="fieldindex" name="fieldindex" type="checkbox" value="1"<?php echo $fieldindex;?> /> <label for="fieldindex"><?php echo $module->L('label/field/key');?></label></span>
        <?php endif;?>
        </p>
        <p><label><?php echo $module->L('label/field/type');?></label>
            <select name="inputtype" id="inputtype" onchange="$(this).selectType('#fieldtype');">
                <?php echo Passport::showTypes($inputtype);?>
            </select>
        </p>
        <p class="___length"><label><?php echo $module->L('label/field/length');?></label><input id="fieldlength" name="fieldlength" type="text" maxlength="255" class="in3" value="<?php echo $fieldlength;?>"/></p>
        <p class="___value"><label><?php echo $module->L('label/field/value');?></label><textarea name="fieldvalue" id="fieldvalue" rows="8" class="in4"><?php echo $fieldvalue;?></textarea></p>
        <p><label><?php echo $module->L('label/field/default');?></label><input id="fieldefault" name="fieldefault" type="text" maxlength="255" class="in3" value="<?php echo $fieldefault;?>"/></p>
        <input name="fieldtype" id="fieldtype" type="hidden" value="<?php echo $fieldtype;?>" />
        <input name="oldfieldename" type="hidden" value="<?php echo $fieldename;?>" />
        <input name="groupid" type="hidden" value="<?php echo $groupid;?>" />
        <input name="fieldid" type="hidden" value="<?php echo $fieldid;?>" />
        <?php echo $module->but('submit');?>
        <script type="text/javascript">$('#inputtype').selectType('#fieldtype');</script>
    </form>
</div>
<?php System::footer();?>