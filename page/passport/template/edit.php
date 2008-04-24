<?php System::header();?>
<?php $module->validate('outjs');?>
<?php echo menu($module->L('list/group/@title').'|'.url(C('CURRENT_MODULE')).';'.$module->L('common/addgroup').'|'.url(C('CURRENT_MODULE'),'GroupEdit').';'.$menu.';'.$module->L('common/leadin').'|'.url(C('CURRENT_MODULE'),'LeadIn'));?>
<div class="content">
    <form action="<?php echo url(C('CURRENT_MODULE'),'Edit');?>" method="post" class="lz_form">
        <p><label><?php echo $module->L('label/user/belongto');?></label>
            <select name="groupid" id="groupid" onchange="$(this).jump('<?php echo url(C('CURRENT_MODULE'),'Edit','groupid=$');?>');">
                <?php echo Passport::__group(0,0,$groupid);?>
            </select>
        </p>
        <p><label><?php echo $module->L('label/user/name');?></label><input class="in2" type="text" id="username" name="username" maxlength="30" value="<?php echo $username;?>" /></p>
        <p><label><?php echo $module->L('label/user/pass');?></label><input class="in2" type="password" id="userpass" name="userpass" maxlength="50" /></p>
        <p><label><?php echo $module->L('label/user/pass1');?></label><input class="in2" type="password" id="userpass1" name="userpass1" maxlength="50" /></p>
        <p><label><?php echo $module->L('label/user/mail');?></label><input class="in3" type="text" id="usermail" name="usermail" maxlength="100" value="<?php echo $usermail;?>" /><span>
            <input name="mailis" type="checkbox" id="mailis" value="1"<?php echo $mailis;?> /><label for="mailis"><?php echo $module->L('label/user/mailis');?></label>
        </span></p>
        <?php echo $module->outHTML;?>
        <p><label><?php echo $module->L('label/user/question');?></label><input class="in3" type="text" id="question" name="question" maxlength="50" value="<?php echo $question;?>" /></p>
        <p><label><?php echo $module->L('label/user/answer');?></label><input class="in4" type="text" id="answer" name="answer" maxlength="50" value="<?php echo $answer;?>" /></p>
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
        <p><label><?php echo $module->L('label/user/islock/@title');?></label><span>
            <input type="radio" name="islock" id="islock0" value="0"<?php echo !$islock ? ' checked="checked"' : null;?>/><label for="islock0"><?php echo $module->L('label/user/islock/is0');?></label>
            <input type="radio" name="islock" id="islock1" value="1"<?php echo $islock ? ' checked="checked"' : null;?> /><label for="islock1"><?php echo $module->L('label/user/islock/is1');?></label>
        </span></p>
        <input name="userid" type="hidden" value="<?php echo $userid;?>" />
        <?php echo $module->but('submit');?>
    </form>
</div>
<?php System::footer();?>
