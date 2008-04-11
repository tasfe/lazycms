<?php System::header();?>
<?php $module->validate('outjs');?>
<?php echo menu($module->L('title').'|'.url(C('CURRENT_MODULE')).';'.$module->L('models/@title').'|'.url(C('CURRENT_MODULE'),'Models').';'.$menu.';'.$module->L('models/leadin').'|'.url(C('CURRENT_MODULE'),'ModelLeadIn'));?>
<div class="content">
    <form action="<?php echo url(C('CURRENT_MODULE'),'ModelEdit');?>" method="post" class="lz_form">
        <p><label><?php echo $module->L('models/label/name');?></label><input id="modelname" name="modelname" type="text" maxlength="50" class="in2" value="<?php echo $modelname;?>"/></p>
        <p><label><?php echo $module->L('models/label/maintable');?></label>
            <select name="maintable" id="maintable">
                <?php echo Archives::showTables($maintable);?>
            </select>
            <span><input name="create" type="checkbox" id="create" value="1" onclick="$(this).change2input('#maintable');" /><label for="create">新建索引表</label></span>
        </p>
        <p><label><?php echo $module->L('models/label/ename');?></label><input id="modelename" name="modelename" type="text" maxlength="50" class="in3" value="<?php echo $modelename;?>"<?php echo $readonly;?>/></p>
        <?php if (empty($modelid)):?>
        <p><label><?php echo $module->L('models/label/fieldset');?></label>
        <blockquote class="red">
            <?php echo $module->L('models/label/fieldtip',array('root'=>C('SITE_BASE').C('PAGES_PATH')));?>
        </blockquote>
        </p>
        <?php endif;?>
        <input name="modelid" type="hidden" value="<?php echo $modelid;?>" />
        <?php echo $module->but('submit');?>
    </form>
</div>
<?php System::footer();?>