<?php System::header();?>
<?php $module->validate('outjs');?>
<?php echo menu($module->L('title').'|'.url('Archives').';'.$module->L('common/addsort').'|'.url('Archives','EditSort').';'.$menu);?>
<div class="content">
    <form action="<?php echo url('Archives','Edit');?>" method="post" class="lz_form">
        <p><label><?php echo $module->L('label/archive/sort');?></label>
            <select name="sortid" id="sortid" onchange="$(this).jump('<?php echo url('Archives','Edit','sortid=$');?>');"<?php echo $disabled;?>>
                <?php echo Archives::__sort(0,0,0,$sortid);?>
            </select>
        </p>
        <p><label><?php echo $module->L('label/archive/info');?></label><span>
            <input name="show" type="checkbox" id="show" value="1"<?php echo $show;?> /><label for="show"><?php echo $module->L('label/archive/show');?></label>
            <input name="commend" type="checkbox" id="commend" value="1"<?php echo $commend;?> /><label for="commend"><?php echo $module->L('label/archive/commend');?></label>
            <input name="top" type="checkbox" id="up" value="1"<?php echo $top;?> /><label for="up"><?php echo $module->L('label/archive/top');?></label> -
            <input name="snapimg" type="checkbox" id="snapimg" value="1"<?php echo $snapimg;?> /><label for="snapimg"><?php echo $module->L('label/archive/snapimg');?></label>
            <input name="upsort" type="checkbox" id="upsort" value="1"<?php echo $upsort;?> /><label for="upsort"><?php echo $module->L('label/archive/upsort');?></label>
            <input name="checktitle" type="checkbox" id="checktitle" value="1"<?php echo $checktitle;?> /><label for="checktitle"><?php echo $module->L('label/archive/checktitle');?></label>            
        </span></p>
        <p><label><?php echo $module->L('label/archive/title');?></label><input class="in4" type="text" id="title" name="title" maxlength="255" value="<?php echo $title;?>" /></p>
        <p><label><?php echo $module->L('label/archive/img');?></label><input class="in4" type="text" id="img" name="img" maxlength="255" value="<?php echo $img;?>" />&nbsp;
            <button type="button" onclick="$('#img').browseFiles('<?php echo url('System','browseFiles');?>','<?php echo $upath;?>',true);"><?php echo L('common/browse');?></button>&nbsp;
            <?php if ($setimg):?>
            <span><input name="setimg" type="checkbox" id="setimg" value="1"<?php echo $setimg;?> /><label for="setimg"><?php echo $module->L('label/archive/setimg');?></label></span></p>
            <?php endif;?>
        <p><label><?php echo $module->L('label/archive/path');?></label><input class="in4" type="text" id="path" name="path" maxlength="255" value="<?php echo $path;?>" />
            <?php if (empty($aid)):?>
            [<a href="javascript:;" onclick="$('#path').val('<?php echo $pathtype_id;?>');"><?php echo $pathtype_id;?></a>]
            [<a href="javascript:;" onclick="$('#path').val('<?php echo $pathtype_date;?>');"><?php echo $pathtype_date;?></a>]
            <?php endif;?>
            [<a href="javascript:;" onclick="$('#path').val('<?php echo $module->L('common/pinyin');?>');"><?php echo $module->L('common/pinyin');?></a>]
            [<a href="javascript:;" onclick="$('#path').val('MD5');">MD5</a>]
        </p>
        <?php echo $module->outHTML;?>
        <input name="aid" type="hidden" value="<?php echo $aid;?>" />
        <?php if (!empty($aid)):?><input name="sortid" type="hidden" value="<?php echo $sortid;?>" /><?php endif;?>
        <?php echo $module->but('submit');?>
    </form>
</div>
<?php System::footer();?>
