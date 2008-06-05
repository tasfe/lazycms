<?php System::header();?>
<?php $module->validate('outjs');?>
<?php echo menu($module->L('title').'|'.url(C('CURRENT_MODULE')).';'.$module->L('models/@title').'|'.url(C('CURRENT_MODULE'),'Models').';'.$module->L('common/addsort').'|'.url(C('CURRENT_MODULE'),'EditSort').';'.$menu.';'.L('config/@title').'|'.url(C('CURRENT_MODULE'),'Config'));?>
<div class="content">
    <form action="<?php echo url(C('CURRENT_MODULE'),'Edit');?>" method="post" class="lz_form">
        <p><label><?php echo $module->L('label/archive/sort');?></label>
            <select name="sortid" id="sortid" <?php if (empty($aid)):?>onchange="$(this).jump('<?php echo url(C('CURRENT_MODULE'),'Edit','sortid=$');?>');"<?php endif;?>>
                <?php echo Archives::__sort(0,0,0,$sortid);?>
            </select>
        </p>
        <p><label><?php echo $module->L('label/archive/info');?></label><span>
            <input name="show" type="checkbox" id="show" value="1"<?php echo $show;?> /><label for="show"><?php echo $module->L('label/archive/show');?></label>
            <input name="commend" type="checkbox" id="commend" value="1"<?php echo $commend;?> /><label for="commend"><?php echo $module->L('label/archive/commend');?></label>
            <input name="top" type="checkbox" id="up" value="1"<?php echo $top;?> /><label for="up"><?php echo $module->L('label/archive/top');?></label> -
            <input name="snapimg" type="checkbox" id="snapimg" value="1"<?php echo $snapimg;?> /><label for="snapimg"><?php echo $module->L('label/archive/snapimg');?></label>
            <input name="upsort" type="checkbox" id="upsort" value="1"<?php echo $upsort;?> /><label for="upsort"><?php echo $module->L('label/archive/upsort');?></label>
            <input name="uphome" type="checkbox" id="uphome" value="1"<?php echo $uphome;?> /><label for="uphome"><?php echo $module->L('label/archive/uphome');?></label>
            <input name="checktitle" type="checkbox" id="checktitle" value="1"<?php echo $checktitle;?> /><label for="checktitle"><?php echo $module->L('label/archive/checktitle');?></label>            
        </span></p>
        <p><label><?php echo $module->L('label/archive/title');?></label><input class="in4" type="text" id="title" name="title" maxlength="255" value="<?php echo $title;?>" /></p>
        <p><label><?php echo $module->L('label/archive/img');?></label><input class="in4" type="text" id="img" name="img" maxlength="255" value="<?php echo $img;?>" />&nbsp;
            <button type="button" onclick="$('#img').browseFiles('<?php echo url('System','browseFiles');?>','<?php echo $upath;?>',true);"><?php echo L('common/browse');?></button>
            <script type="text/javascript">
            <?php if (!empty($img) && !is_file(LAZY_PATH.$img)):?>
            document.write(ico('tip'));
            <?php else:?>
            document.write(image('<?php echo !empty($img) ? C("SITE_BASE").$img : null;?>'));
            <?php endif;?>
            </script>
            <?php if ($setimg):?>
            &nbsp;<span><input name="setimg" type="checkbox" id="setimg" value="1"<?php echo $setimg;?> /><label for="setimg"><?php echo $module->L('label/archive/setimg');?></label></span></p>
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
        <p><label><?php echo $module->L('label/archive/keywords');?></label><input class="in4" type="text" id="keywords" name="keywords" maxlength="255" value="<?php echo $keywords;?>" /></p>
        <p><label><?php echo $module->L('label/archive/description');?></label><textarea name="description" id="description" rows="5" class="in4"><?php echo $description;?></textarea></p>
        <p><label><?php echo $module->L('label/archive/date');?></label><input class="in2 date-pick" type="text" id="date" name="date" value="<?php echo $date;?>" /></p>
        <input name="aid" type="hidden" value="<?php echo $aid;?>" />
        <?php echo $module->but('submit');?>
    </form>
</div>
<?php System::footer();?>
