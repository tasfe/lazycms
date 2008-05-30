<?php System::header();?>
<?php $module->validate('outjs');?>
<?php echo menu($module->L('list/group/@title').'|'.url(C('CURRENT_MODULE')).';'.$menu.';'.$module->L('common/adduser').'|'.url(C('CURRENT_MODULE'),'Edit').';'.$module->L('common/leadin').'|'.url(C('CURRENT_MODULE'),'LeadIn').';'.L('config/@title').'|'.url(C('CURRENT_MODULE'),'Config'));?>
<div class="content">
    <form action="<?php echo url(C('CURRENT_MODULE'),'GroupEdit');?>" method="post" class="lz_form">
        <p><label><?php echo $module->L('label/group/name');?></label><input id="groupname" name="groupname" type="text" maxlength="50" class="in2" value="<?php echo $groupname;?>"/></p>
        <p><label><?php echo $module->L('label/group/ename');?></label><input id="groupename" name="groupename" type="text" maxlength="50" class="in3" value="<?php echo $groupename;?>"<?php echo $readonly;?>/></p>
        <p><label><?php echo $module->L('label/group/template');?></label><input class="in3" type="text" id="template" name="template" maxlength="255" readonly="true" value="<?php echo $template;?>" />&nbsp;<button type="button" onclick="$('#template').browseFiles('<?php echo url('System','browseFiles');?>');"><?php echo L('common/browse');?></button></p>
        <?php if (empty($modelid)):?>
        <p><label><?php echo $module->L('label/group/fieldset');?></label>
        <blockquote class="red">
            <?php echo $module->L('label/group/fieldtip',array('root'=>C('SITE_BASE').C('PAGES_PATH')));?>
        </blockquote>
        </p>
        <?php endif;?>
        <input name="groupid" type="hidden" value="<?php echo $groupid;?>" />
        <?php echo $module->but('submit');?>
    </form>
</div>
<?php System::footer();?>