<?php System::header();?>
<?php $module->validate('outjs');?>
<?php echo menu($module->L('title').'|'.url('Archives').';'.$module->L('common/addsort').'|'.url('Archives','EditSort').';'.$module->L('common/addpage').'|'.url('Archives','Edit').'|true');?>
<div class="content">
    <form action="<?php echo url('Archives','Edit');?>" method="post" class="lz_form">
        <p><label><?php echo $module->L('label/archive/sort');?></label>
            <select name="sortid" id="sortid" onchange="$(this).jump('<?php echo url('Archives','Edit','sortid=$');?>');">
                <?php echo Archives::__sort(0,0,0,$sortid);?>
            </select>
        </p>
        <p><label><?php echo $module->L('label/archive/info');?></label><span>
            <input name="show" type="checkbox" id="show" value="1" /><label for="show"><?php echo $module->L('label/archive/show');?></label>
            <input name="commend" type="checkbox" id="commend" value="1" /><label for="commend"><?php echo $module->L('label/archive/commend');?></label>
            <input name="top" type="checkbox" id="up" value="1" /><label for="up"><?php echo $module->L('label/archive/top');?></label> -
            <input name="snapimg" type="checkbox" id="snapimg" value="1" /><label for="snapimg"><?php echo $module->L('label/archive/snapimg');?></label>
            <input name="upsort" type="checkbox" id="upsort" value="1" /><label for="upsort"><?php echo $module->L('label/archive/upsort');?></label>
            <input name="checktitle" type="checkbox" id="checktitle" value="1" /><label for="checktitle"><?php echo $module->L('label/archive/checktitle');?></label>            
        </span></p>
        <p><label><?php echo $module->L('label/archive/title');?></label><input class="in4" type="text" id="title" name="title" maxlength="100" value="<?php echo $title;?>" /></p>
        <p><label><?php echo $module->L('label/archive/img');?></label><input class="in4" type="text" id="img" name="img" maxlength="255" value="<?php echo $img;?>" />&nbsp;<button type="button" onclick="$('#img').browseFiles('<?php echo url('System','browseFiles');?>','<?php echo $upath;?>',true);"><?php echo L('common/browse');?></button></p>
        <p><label><?php echo $module->L('label/archive/path');?></label><input class="in4" type="text" id="path" name="path" maxlength="255" value="<?php echo $path;?>" /></p>
        <?php echo $module->outHTML;?>
        <?php echo $module->but('submit');?>
    </form>
</div>
<?php System::footer();?>
