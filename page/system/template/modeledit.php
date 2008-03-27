<?php System::header();?>
<?php $module->validate('outjs');?>
<?php echo menu($module->L('models/@title').'|'.url('System','Models').';'.$menu.';'.$module->L('models/leadin').'|'.url('System','ModelLeadIn'));?>
<div class="content">
    <form action="<?php echo url('System','ModelEdit');?>" method="post" class="lz_form">
        <p><label><?php echo $module->L('models/label/name');?></label><input id="modelname" name="modelname" type="text" maxlength="50" class="in2" value="<?php echo $modelname;?>"/></p>
        <p><label><?php echo $module->L('models/label/maintable');?></label>
            <select name="maintable" id="maintable">
                <?php echo System::showTables($maintable);?>
            </select>
        </p>
        <p><label><?php echo $module->L('models/label/ename');?></label><input id="modelename" name="modelename" type="text" maxlength="50" class="in3" value="<?php echo $modelename;?>"<?php echo $readonly;?>/></p>
        <?php if (empty($modelid)):?>
        <p><label><?php echo $module->L('models/label/fieldset');?></label>
        <blockquote class="red">
            <?php echo $module->L('models/label/fieldtip',array('root'=>LAZY_PATH.C('PAGES_PATH')));?>
        </blockquote>
        </p>
        <?php endif;?>
        <input name="modelid" type="hidden" value="<?php echo $modelid;?>" />
        <?php echo $module->but('submit');?>
    </form>
</div>
<?php System::footer();?>