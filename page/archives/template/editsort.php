<?php System::header();?>
<?php $module->validate('outjs');?>
<?php echo menu($module->L('title').'|'.url(C('CURRENT_PATH')).';'.$menu.';'.$module->L('common/addpage').'|'.url(C('CURRENT_PATH'),'Edit'));?>
<div class="content">
    <form action="<?php echo url(C('CURRENT_PATH'),'EditSort');?>" method="post" class="lz_form">
        <p><label><?php echo $module->L('label/supsort');?></label>
            <select name="sortid1">
                <option value="0"><?php echo $module->L("common/root");?></option>
                <?php echo Archives::__sort(0,$sortid,0,$sortid1);?>
            </select>
        </p>
        <p><label><?php echo $module->L('label/sortname');?></label><input class="in2" type="text" id="sortname" name="sortname" maxlength="50" value="<?php echo $sortname;?>" /></p>
        <p><label><?php echo $module->L('label/sortpath');?></label><input class="in4" type="text" id="sortpath" name="sortpath" maxlength="255" value="<?php echo $sortpath;?>" /></p>
        <p><label><?php echo $module->L('label/model');?></label>
            <select name="modelid" id="modelid" onchange="$(this).setTemplate('#modelname','#sorttemplate2','#pagetemplate2');"<?php echo $disabled;?>>
                <?php echo Archives::__model($modelid);?>
            </select>
        </p>
        <p><label><?php echo $module->L('label/keywords');?></label><input class="in4" type="text" id="keywords" name="keywords" maxlength="255" value="<?php echo $keywords;?>" /></p>
        <p><label><?php echo $module->L('label/description');?></label><textarea name="description" id="description" rows="5" class="in4"><?php echo $description;?></textarea></p>
        <p><label><?php echo $module->L('label/sorttemplate1');?></label><input class="in3" type="text" id="sorttemplate1" name="sorttemplate1" maxlength="50" readonly="true" value="<?php echo $sorttemplate1;?>" />&nbsp;<button type="button" onclick="$('#sorttemplate1').browseFiles('<?php echo url('System','browseFiles');?>');"><?php echo L('common/browse');?></button></p>
        <p><label><?php echo $module->L('label/sorttemplate2');?></label><input class="in3" type="text" id="sorttemplate2" name="sorttemplate2" maxlength="50" readonly="true" value="<?php echo $sorttemplate2;?>" />&nbsp;<button type="button" onclick="$('#sorttemplate2').browseFiles('<?php echo url('System','browseFiles');?>');"><?php echo L('common/browse');?></button></p>
        <p><label><?php echo $module->L('label/pagetemplate1');?></label><input class="in3" type="text" id="pagetemplate1" name="pagetemplate1" maxlength="50" readonly="true" value="<?php echo $pagetemplate1;?>" />&nbsp;<button type="button" onclick="$('#pagetemplate1').browseFiles('<?php echo url('System','browseFiles');?>');"><?php echo L('common/browse');?></button></p>
        <p><label><?php echo $module->L('label/pagetemplate2');?></label><input class="in3" type="text" id="pagetemplate2" name="pagetemplate2" maxlength="50" readonly="true" value="<?php echo $pagetemplate2;?>" />&nbsp;<button type="button" onclick="$('#pagetemplate2').browseFiles('<?php echo url('System','browseFiles');?>');"><?php echo L('common/browse');?></button></p>
        <input name="sortid" type="hidden" value="<?php echo $sortid;?>" />
        <input name="modelname" id="modelname" type="hidden" />
        <?php echo $module->but('submit');?>
        <script type="text/javascript">$('#modelid').setTemplate('#modelname','#sorttemplate2','#pagetemplate2');</script>
    </form>
</div>
<?php System::footer();?>