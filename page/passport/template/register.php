<?php $module->validate('outjs');?>
<div class="content">
    <form action="<?php echo url(C('CURRENT_MODULE'),'Register');?>" method="post" class="lz_form">
        <p><label><?php echo $module->L('label/user/name');?></label><input class="in2" type="text" id="username" name="username" maxlength="30" value="<?php echo $username;?>" /></p>
        <p><label><?php echo $module->L('label/user/pass');?></label><input class="in2" type="password" id="userpass" name="userpass" maxlength="50" /></p>
        <p><label><?php echo $module->L('label/user/pass1');?></label><input class="in2" type="password" id="userpass1" name="userpass1" maxlength="50" /></p>
        <p><label><?php echo $module->L('label/user/mail');?></label><input class="in3" type="text" id="usermail" name="usermail" maxlength="100" value="<?php echo $usermail;?>" /><span>
            <input name="mailis" type="checkbox" id="mailis" value="1"<?php echo $mailis;?> /><label for="mailis"><?php echo $module->L('label/user/mailis');?></label>
        </span></p>
        <?php echo $module->outHTML;?>
        <p><label><?php echo $module->L('label/user/question');?></label><input class="in3" type="text" id="question" name="question" maxlength="50" value="<?php echo $question;?>" /></p>
        <p><label><?php echo $module->L('label/user/answer');?></label><input class="in4" type="text" id="answer" name="answer" maxlength="50" value="<?php echo $answer;?>" /></p>
        <input name="groupid" type="hidden" value="<?php echo $groupid;?>" />
        <?php echo $module->but('submit');?>
    </form>
</div>
