<?php System::header();?>
<?php $module->validate('outjs');?>
<?php echo menu($module->L('title').'|'.url(C('CURRENT_MODULE')).';'.$module->L('models/@title').'|'.url(C('CURRENT_MODULE'),'Models').';'.$module->L('models/add').'|'.url(C('CURRENT_MODULE'),'ModelEdit').';'.$module->L('models/field/@title').'|'.url(C('CURRENT_MODULE'),'ModelFields','modelid='.$modelid).';'.$menu);?>
<div class="content">
    <form action="<?php echo url(C('CURRENT_MODULE'),'ModelFieldsEdit');?>" method="post" class="lz_form">
        <p><label><?php echo $module->L('models/field/label/name');?></label><input id="fieldname" name="fieldname" type="text" maxlength="50" class="in2" value="<?php echo $fieldname;?>"/></p>
        <p><label><?php echo $module->L('models/field/label/ename');?></label><input id="fieldename" name="fieldename" type="text" maxlength="50" class="in2" value="<?php echo $fieldename;?>"<?php echo $readonly;?>/>
        <?php if (empty($fieldid)):?>
            <span class="___toggle"><input id="fieldindex" name="fieldindex" type="checkbox" value="1"<?php echo $fieldindex;?> /> <label for="fieldindex"><?php echo $module->L('models/field/label/key');?></label></span>
        <?php endif;?>
        </p>
        <p><label><?php echo $module->L('models/field/label/type');?></label>
            <select name="inputtype" id="inputtype" onchange="$(this).selectType('#fieldtype');">
                <?php echo Archives::showTypes($inputtype);?>
            </select>
        </p>
        <p class="___length"><label><?php echo $module->L('models/field/label/length');?></label><input id="fieldlength" name="fieldlength" type="text" maxlength="255" class="in3" value="<?php echo $fieldlength;?>"/></p>
        <p class="___value"><label><?php echo $module->L('models/field/label/value');?></label><textarea name="fieldvalue" id="fieldvalue" rows="8" class="in4"><?php echo $fieldvalue;?></textarea></p>
        <p><label><?php echo $module->L('models/field/label/default');?></label><input id="fieldefault" name="fieldefault" type="text" maxlength="255" class="in3" value="<?php echo $fieldefault;?>"/></p>
        <input name="fieldtype" id="fieldtype" type="hidden" value="<?php echo $fieldtype;?>" />
        <input name="oldfieldename" type="hidden" value="<?php echo $fieldename;?>" />
        <input name="modelid" type="hidden" value="<?php echo $modelid;?>" />
        <input name="fieldid" type="hidden" value="<?php echo $fieldid;?>" />
        <?php echo $module->but('submit');?>
        <script type="text/javascript">$('#inputtype').selectType('#fieldtype');</script>
    </form>
</div>
<?php System::footer();?>