<?php System::header();?>
<?php $module->validate('outjs');?>
<?php echo menu($module->L('title').'|'.url(C('CURRENT_MODULE')).';'.$menu);?>
<div class="content">
    <form action="<?php echo url(C('CURRENT_MODULE'),'Edit');?>" method="post" class="lz_form">
        <p><label><?php echo $module->L('label/name');?></label><input class="in3" type="text" id="mtname" name="mtname" maxlength="50" value="<?php echo $mtname;?>" /></p>
        <p><label><?php echo $module->L('label/title');?></label><input class="in4" type="text" id="mttitle" name="mttitle" maxlength="100" value="<?php echo $mttitle;?>" /></p>
        <p><label><?php echo $module->L('label/text');?></label><textarea name="mttext" id="mttext" rows="15" class="in5"><?php echo $mttext;?></textarea></p>
        <p><label><?php echo $module->L('label/size/title');?></label><span>
            <input class="in0" style="text-align:right;" type="text" name="mtwidth" maxlength="5" value="<?php echo $mtwidth;?>" />px &nbsp;<label><?php echo $module->L('label/size/width');?></label>
            <?php while(list(,$width) = each($arrwidth)):?>
                <a href="javascript:;" onclick="$('input[@name=mtwidth]').val(<?php echo $width;?>);">[<?php echo $width;?>]</a>
            <?php endwhile;?>
            <br/>
            <input class="in0" style="text-align:right;" type="text" name="mtheight" maxlength="5" value="<?php echo $mtheight;?>" />px &nbsp;<label><?php echo $module->L('label/size/height');?></label>
            <?php while(list(,$height) = each($arrheight)):?>
                <a href="javascript:;" onclick="$('input[@name=mtheight]').val(<?php echo $height;?>);">[<?php echo $height;?>]</a>
            <?php endwhile;?>
            </span>
        </p>
        <p><label><?php echo $module->L('label/ext');?></label>
            <input class="in0" type="text" name="mtext" maxlength="5" value="<?php echo $mtext;?>" />
            <?php while(list(,$ext) = each($arrext)):?>
                <a href="javascript:;" onclick="$('input[@name=mtext]').val('<?php echo $ext;?>');">[<?php echo $ext;?>]</a>
            <?php endwhile;?>
        </p>
        <input name="mtid" type="hidden" value="<?php echo $mtid;?>" />
        <input name="oldname" type="hidden" value="<?php echo $oldname;?>" />
        <?php echo $module->but('submit');?>
    </form>
</div>
<?php System::footer();?>