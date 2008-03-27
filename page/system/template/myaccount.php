<?php System::header();?>
<?php $module->validate('outjs');?>
<?php echo menu($module->L('config/@title').'|'.url('System','Config').';'.$module->L('module/@title').'|'.url('System','Module').';'.$module->L('diymenu/@title').'|'.url('System','DiyMenu').';'.$module->L('account/@title').'|#|true');?>
<div class="content">
    <form action="<?php echo url('System','MyAccount');?>" method="post" class="lz_form">
        <?php echo $module->succeed();?>
        <p><label><?php echo $module->L('admin/label/name');?></label><input type="text" class="in2" value="<?php echo $adminname;?>" readonly="true"/></p>
        <p><label><?php echo $module->L('admin/label/pass');?> (6-30)</label><input id="adminpass" name="adminpass" type="password" maxlength="30" class="in2"/></p>
        <p><label><?php echo $module->L('admin/label/pass1');?></label><input id="adminpass1" name="adminpass1" type="password" maxlength="30" class="in2"/></p>
        <p><label><?php echo $module->L('admin/label/language');?></label>
            <select name="adminlanguage" id="adminlanguage">
                <?php echo formOpts('@.system.language','xml','<option value="#value#"#selected#>#name#</option>',$adminlanguage);?>
            </select>
        </p>
        <p><label><?php echo $module->L('admin/label/editor');?></label>
        <select name="admineditor" id="admineditor">
            <?php echo formOpts('@.system.editor','dir','<option value="#value#"#selected#>#name#</option>',$admineditor);?>
        </select>
        </p>
        <p><label><?php echo $module->L('account/diymenu');?></label><textarea name="diymenu" rows="15" class="in5"><?php echo $diymenu;?></textarea></p>
        <?php echo $module->but('save');?>
    </form>
</div>
<?php System::footer();?>