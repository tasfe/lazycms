<?php $module->validate('outjs');?>
<?php echo Passport::navigation('navuser');?>
<form action="<?php echo url(C('CURRENT_MODULE'),'UserConfig');?>" method="post" class="lz_form">
    <p><label><?php echo $module->L('label/user/name');?></label><input class="in2" type="text" id="username" name="username" readonly="true" maxlength="30" value="<?php echo $username;?>" /></p>
    <p><label><?php echo $module->L('label/user/mail');?></label><input class="in3" type="text" id="usermail" name="usermail" maxlength="200" value="<?php echo $usermail;?>" /><span>
        <input name="mailis" type="checkbox" id="mailis" value="1"<?php echo $mailis;?> /><label for="mailis"><?php echo $module->L('label/user/mailis');?></label>
    </span></p>
    <?php echo $module->outHTML;?>
    <p><label><?php echo $module->L('label/user/question');?></label><input class="in3" type="text" id="question" name="question" maxlength="255" value="<?php echo $question;?>" /></p>
    <p><label><?php echo $module->L('label/user/answer');?></label><input class="in4" type="text" id="answer" name="answer" maxlength="255" value="<?php echo $answer;?>" /></p>
    <p><label><?php echo $module->L('label/user/language');?></label>
        <select name="language" id="language">
            <?php echo formOpts('@.system.language','xml','<option value="#value#"#selected#>#name#</option>',$language);?>
        </select>
    </p>
    <p><label><?php echo $module->L('label/user/editor');?></label>
    <select name="editor" id="editor">
        <?php echo formOpts('@.system.editor','dir','<option value="#value#"#selected#>#name#</option>',$editor);?>
    </select>
    </p>
    <?php echo $module->but('submit');?>
</form>

