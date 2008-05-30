<?php $module->validate('outjs');?>
<div class="content">
    <form action="<?php echo url(C('CURRENT_MODULE'),'Login');?>" method="post" class="lz_form">
        <p><label><?php echo $module->L('login/name');?></label><input class="in2" type="text" id="username" name="username" maxlength="30" value="<?php echo $username;?>" /></p>
        <p><label><?php echo $module->L('login/pass');?></label><input class="in2" type="password" id="userpass" name="userpass" maxlength="50" /></p>
        <p><input name="keep" type="checkbox" id="keep" value="1" /><span><label for="keep"><?php echo $module->L('login/keep');?></label></span></p>
        <div class="button">
            <button type="submit"><?php echo $module->L('login/submit');?></button>
            <button type="button" onclick="javascript:self.location.href='<?php echo url(C('CURRENT_MODULE'),'Register');?>';"><?php echo $module->L('register/reg');?></button>
        </div>
    </form>
</div>
