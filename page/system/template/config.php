<?php System::header();?>
<?php $module->validate('outjs');?>
<?php echo menu($module->L('config/@title').'|#|true;'.$module->L('diymenu/@title').'|'.url('System','DiyMenu').';'.$module->L('account/@title').'|'.url('System','MyAccount'));?>
<div class="content">
    <form action="<?php echo url('System','Config');?>" method="post" class="lz_form">
        <?php echo $module->succeed();?>
        <p><label><?php echo $module->L('config/sitename');?></label><input class="in2" type="text" id="sitename" name="sitename" maxlength="50" value="<?php echo $sitename;?>" /></p>
        <p><label><?php echo $module->L('config/sitemail');?></label><input class="in3" type="text" id="sitemail" name="sitemail" maxlength="100" value="<?php echo $sitemail;?>" /></p>
        <p><label><?php echo $module->L('config/sitemode/@title');?></label><span>
            <input type="radio" name="sitemode" id="sitemode_true" value="true"<?php echo $sitemode ? ' checked="checked"' : null;?>/><label for="sitemode_true"><?php echo $module->L('config/sitemode/true');?></label>
            <input type="radio" name="sitemode" id="sitemode_false" value="false"<?php echo !$sitemode ? ' checked="checked"' : null;?> /><label for="sitemode_false"><?php echo $module->L('config/sitemode/false');?></label>
        </span></p>
        <p><label><?php echo $module->L('config/urlmode/@title');?></label><span>
            <input type="radio" name="urlmode" id="sitemode_common" value="<?php echo URL_COMMON;?>"<?php echo $urlmode==URL_COMMON ? ' checked="checked"' : null;?>/><label for="sitemode_common"><?php echo $module->L('config/urlmode/common');?></label>
            <input type="radio" name="urlmode" id="sitemode_pathinfo" value="<?php echo URL_PATHINFO;?>"<?php echo $urlmode==URL_PATHINFO ? ' checked="checked"' : null;?> /><label for="sitemode_pathinfo"><?php echo $module->L('config/urlmode/pathinfo');?></label>
            <?php if (!IS_IIS):?><input type="radio" name="urlmode" id="sitemode_rewrite" value="<?php echo URL_REWRITE;?>"<?php echo $urlmode==URL_REWRITE ? ' checked="checked"' : null;?> /><label for="sitemode_rewrite"><?php echo $module->L('config/urlmode/rewrite');?></label><?php endif;?>
        </span></p>
        <p><label><?php echo $module->L('config/keywords').' '.$module->L('config/tip/keywords');?></label><textarea name="keywords" id="keywords" rows="10" class="in5"><?php echo $keywords;?></textarea></p>
        <p><label><?php echo $module->L('config/lockip').' '.$module->L('config/tip/lockip');?></label><textarea name="lockip" id="lockip" rows="10" class="in5"><?php echo $lockip;?></textarea></p>
        <?php echo $module->but('save');?>
    </form>
</div>
<?php System::footer();?>