<?php System::header();?>
<?php $module->validate('outjs');?>
<?php echo menu($module->L('admin/list').'|'.url('System','Admin').';'.$menu);?>
<div class="content">
    <form action="<?php echo url('System','AdminEdit');?>" method="post" class="lz_form">
        <?php if(empty($adminid)):?>
        <p><label><?php echo $module->L('admin/label/name');?> (2-12)</label><input id="adminname" name="adminname" type="text" maxlength="12" class="in2" value="<?php echo $adminname;?>"/></p>
        <?php else:?>
        <p><label><?php echo $module->L('admin/label/name');?></label><input type="text" class="in2" value="<?php echo $adminname;?>" readonly="true"/><input name="adminid" type="hidden" value="<?php echo $adminid;?>" /></p>
        <?php endif?>
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
        <p class="checkbox"><label><?php echo $module->L('admin/label/level');?></label>
            <?php $checked = ($adminlevel=='admin')?' checked="checked"':'';?>
            <span><input type="checkbox" id="adminlevel" name="adminlevel" value="admin"<?php echo $checked;?> onclick="selevel();"  /><label for="adminlevel"><?php echo $module->L('admin/level/super');?></label></span>
            <span id="levels">
              <?php while(list(,$level) = each($levels)):?>
              <?php $checked = instr($adminlevel,$level)?' checked="checked"':'';?>
              <input type="checkbox" name="level[]" id="m_<?php echo $level;?>" value="<?php echo $level;?>"<?php echo $checked;?> /><label for="m_<?php echo $level;?>"><?php echo $module->L($level.'/@title');?></label> 
              <?php endwhile;?>
              <br/>
              <?php while(list(,$m) = each($modules)):?>
              <?php $checked = instr($adminlevel,$m)?' checked="checked"':'';?>
              <input type="checkbox" name="level[]" id="m_<?php echo $m;?>" value="<?php echo $m;?>"<?php echo $checked;?> /><label for="m_<?php echo $m;?>"><?php echo L('title',null,$m);?></label> 
              <?php endwhile;?>
            </span>
        </p>
        <script type="text/javascript">selevel();</script>
        <p><label><?php echo $module->L('account/diymenu');?></label><textarea name="diymenu" rows="15" class="in5"><?php echo $diymenu;?></textarea></p>
        <?php echo $module->but('submit');?>
    </form>
</div>
<?php System::footer();?>