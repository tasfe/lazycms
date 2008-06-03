<?php $module->validate('outjs');?>
<?php echo Passport::navigation('navuser');?>
<form action="<?php echo url(C('CURRENT_MODULE'),'UpdatePass');?>" method="post" class="lz_form">
    <p><label><?php echo $module->L('usercenter/updatepass/name');?></label><input class="in2" type="text" id="username" name="username" readonly="true" maxlength="30" value="<?php echo $module->passport['username'];?>" /></p>
    <p><label><?php echo $module->L('usercenter/updatepass/oldpass');?> (6-30)</label><input class="in2" type="password" id="oldpass" name="oldpass" maxlength="30" /></p>
    <p><label><?php echo $module->L('usercenter/updatepass/newpass');?> (6-30)</label><input class="in2" type="password" id="newpass" name="newpass" maxlength="30" /></p>
    <p><label><?php echo $module->L('usercenter/updatepass/newpass1');?></label><input class="in2" type="password" id="newpass1" name="newpass1" maxlength="30" /></p>
    <?php echo $module->but('submit');?>
</form>

