<?php
/**
 * +---------------------------------------------------------------------------+
 * | LL                             LLLL   LL     L   LLLL                 LL  |
 * | LL                            LL   L  LLL   LL  LL   L               LLL  |
 * | LL      LLLL   LLLLL  LL  LL  LL      LLLL LLL  LL          LL  LL    LL  |
 * | LL         LL     LL  LL  LL  LL      L LLL LL  LLLLL       LL  LL    LL  |
 * | LL      LLLLL    LL    LLLL   LL      L  L  LL   LLLLL      LL  LL    LL  |
 * | LL     LL  LL   LL     LLLL   LL      L     LL      LL       LLLL     LL  |
 * | LL     LL  LL  LL       LL    LL   L  L     LL  L   LL       LLLL     LL  |
 * | LLLLLL  LLLLL  LLLLL    LL     LLLL   L     LL   LLLL         LL     LLLL |
 * |                        LL                                                 |
 * |                        LL                                                 |
 * +---------------------------------------------------------------------------+
 * | Copyright (c) 2007-2008 LazyCMS.net All rights reserved.                  |
 * +---------------------------------------------------------------------------+
 * | 许可协议，请查看源代码中附带的 LICENSE.txt 文件，                         |
 * | 或者访问 http://www.lazycms.net/ 获得详细信息。                           |
 * +---------------------------------------------------------------------------+
 */
require '../../global.php';
/**
 * 系统设置
 *
 * @copyright   Copyright (c) 2007-2008 lazycms.net All rights reserved.
 * @author      Lukin <mylukin@gmail.com>
 * @date        2008-6-26
 */

// lazy_before *** *** www.LazyCMS.net *** ***
function lazy_before(){
    // 设置公共菜单
    G('TABS',
        L('users/group/@title').':users.php;'.
        L('users/group/add/@title').':users.php?action=group_edit;'.
        L('users/user/add/@title').':users.php?action=user_edit'
    );
    // 权限验证
    check_login('users');
}
// lazy_default *** *** www.LazyCMS.net *** ***
function lazy_default(){
    $db = get_conn();
    $ds = new Recordset();
    $ds->create("SELECT `sg`.*,count(`su`.`userid`) AS `count`
                    FROM `#@_system_group` AS `sg` 
                    LEFT JOIN `#@_system_users` AS `su` ON (`sg`.`groupid` = `su`.`groupid` And `su`.`isdel`=0)
                    GROUP BY `sg`.`groupid`
                    ORDER BY `sg`.`groupid` DESC");
    $ds->action = PHP_FILE."?action=group_set";
    $ds->but = $ds->button();
    $ds->td  = "cklist(K[0]) + K[0] + ') <a href=\"users.php?action=user_list&groupid=' + K[0] + '\">' + K[1] + '</a>'";
    $ds->td  = "K[2]";
    $ds->td  = "'".$db->config('prefix')."system_group_' + K[2]";
    $ds->td  = "K[3]";
    $ds->td  = "icon('add','users.php?action=user_edit&groupid=' + K[0]) + icon('edit','users.php?action=group_edit&groupid=' + K[0])";
    $ds->open();
    $ds->thead = '<tr><th>ID) '.L('users/group/list/name').'</th><th>'.L('users/group/list/logo').'</th><th>'.L('users/group/list/addtable').'</th><th>'.L('users/group/list/count').'</th><th>'.L('common/action').'</th></tr>';
    while ($rs = $ds->result()) {
        $ds->tbody = "ll(".$rs['groupid'].",'".t2js(h2encode($rs['groupname']))."','".t2js(h2encode($rs['groupename']))."',".$rs['count'].");";
    }
    $ds->close();

    print_x(L('users/group/@title'),$ds->fetch());
}
// lazy_group_set *** *** www.LazyCMS.net *** ***
function lazy_group_set(){
    $db = get_conn();
    $submit = isset($_POST['submit']) ? strtolower($_POST['submit']) : null;
    $lists  = isset($_POST['lists']) ? $_POST['lists'] : null;
    switch($submit){
        case 'delete':
            empty($lists) ? echo_json(L('users/group/pop/select'),0) : null ;
            $db->exec("DELETE FROM `#@_system_group` WHERE `system`=0 AND `groupid` IN({$lists});");
            echo_json(array(
                'text' => L('users/group/pop/deleteok'),
                'url'  => $_SERVER["HTTP_REFERER"],
            ),1);
            break;
    }
}
// lazy_group_edit *** *** www.LazyCMS.net *** ***
function lazy_group_edit(){
    $groupid    = isset($_REQUEST['groupid']) ? $_REQUEST['groupid'] : 0;
    $groupename = isset($_POST['groupename']) ? $_POST['groupename'] : null;
    $groupname  = isset($_POST['groupname']) ? $_POST['groupname'] : null;
    $purview    = isset($_POST['purview']) ? $_POST['purview'] : null;
    $purview    = is_array($purview) ? implode(',',$purview) : null;
    $title      = empty($groupid) ? L('users/group/add/@title') : L('users/group/edit/@title');
    $db = get_conn(); $val = new Validate();
    if ($val->method()) {
        $inSQL = !empty($groupid) ? " AND `groupid`<>'{$groupid}'" : null;
        $val->check('groupname|0|'.L('users/group/check/name').';groupname|4|'.L('users/group/check/name1')."|SELECT COUNT(groupid) FROM `#@_system_group` WHERE `groupname`='#pro#'{$inSQL}")
            ->check('groupename|0|'.L('users/group/check/logo').';groupename|4|'.L('users/group/check/logo1')."|SELECT COUNT(groupid) FROM `#@_system_group` WHERE `groupename`='#pro#'{$inSQL}");
        if ($val->isVal()) {
            $val->out();
        } else {
            if (empty($groupid)) {
                $db->insert('#@_system_group',array(
                    'groupename' => $groupename,
                    'groupname'  => $groupname,
                    'purview'    => $purview,
                    'system'     => 0,
                ));
                $text = L('users/group/pop/addok');
            } else {
                $db->update('#@_system_group',array(
                    'groupename' => $groupename,
                    'groupname'  => $groupname,
                    'purview'    => $purview,
                ),DB::quoteInto('`groupid` = ?',$groupid));
                $text = L('users/group/pop/editok');
            }
            echo_json(array(
                'text' => $text,
                'url'  => 'users.php',
            ),1);
        }
    } else {
        if (!empty($groupid)) {
            $res = $db->query("SELECT * FROM `#@_system_group` WHERE `groupid`=?",$groupid);
            if ($rs = $db->fetch($res)) {
                $groupename = h2encode($rs['groupename']);
                $groupname  = h2encode($rs['groupname']);
                $purview    = h2encode($rs['purview']);
            }
        }
    }
    $module = include_file(COM_PATH.'/data/module.php');
    $hl = '<form id="form1" name="form1" method="post" action="">';
    $hl.= '<fieldset><legend rel="tab">'.$title.'</legend>';
    $hl.= '<p><label>'.L('users/group/add/name').'：</label><input tip="'.L('users/group/add/name').'::'.L('users/group/add/name/@tip').'" class="in2" type="text" name="groupname" id="groupname" value="'.$groupname.'" /></p>';
    $hl.= '<p><label>'.L('users/group/add/logo').'：</label><input tip="'.L('users/group/add/logo').'::'.L('users/group/add/logo/@tip').'" class="in2" type="text" name="groupename" id="groupename" value="'.$groupename.'"'.(!empty($groupid) ? ' readonly="true"' : null).' /></p>';
    $hl.= '<p><label>'.L('users/group/add/purview').'：</label><div class="purview">';
    foreach ($module as $k=>$v) {
        if (isset($v['purview'])) {
            $hl.= '<input type="checkbox" name="'.$k.'" id="'.$k.'" class="__bigP" onclick="var checked = this.checked;$.each($(\'input.__'.$k.'\'),function(){ this.checked = checked; });" /><label for="'.$k.'"><strong>'.L('title',null,$k).'</strong></label><br/>';
            foreach ($v['purview'] as $i=>$p) {
                $checked = instr($purview,"{$k}/{$p}") ? ' checked="checked"' : null;
                $hl.= '<input type="checkbox" name="purview[]" id="'.$k.'_'.$i.'" class="__'.$k.'" onclick="Purview();" value="'.$k.'/'.$p.'"'.$checked.' /><label for="'.$k.'_'.$i.'">'.L("{$p}/@title",null,$k).'</label>';    
            }
            $hl.= '<br/>';
        }
    }
    $hl.= '</div></p></fieldset>';
    $hl.= but('save').'<input name="groupid" type="hidden" value="'.$groupid.'" /></form>';
    $hl.= '<script type="text/javascript">Purview();</script>';
    print_x($title,$hl);
}
// lazy_user_list *** *** www.LazyCMS.net *** ***
function lazy_user_list(){
    $db = get_conn();
    $groupid = isset($_REQUEST['groupid']) ? $_REQUEST['groupid'] : 0;
    $ds = new Recordset();
    $ds->create("SELECT * FROM `#@_system_users` WHERE `groupid`='{$groupid}' AND `isdel`=0 ORDER BY `userid` DESC");
    $ds->action = PHP_FILE."?action=user_set";
    $ds->url = 'users.php?action=user_list&page=$';
    $ds->but = $ds->button().$ds->plist();
    $ds->td  = "cklist(K[0]) + K[0] + ') <a href=\"users.php?action=user_edit&userid=' + K[0] + '\">' + K[1] + '</a>'";
    $ds->td  = "K[2]";
    $ds->td  = "K[3]";
    $ds->td  = "K[4]";
    $ds->td  = "lock(K[5])";
    $ds->open();
    $ds->thead = '<tr><th>ID) '.L('users/user/list/name').'</th><th>'.L('users/user/list/mail').'</th><th>'.L('users/user/list/language').'</th><th>'.L('users/user/list/regdate').'</th><th>'.L('users/user/list/state').'</th></tr>';
    while ($rs = $ds->result()) {
        $ds->tbody = "ll(".$rs['userid'].",'".t2js(h2encode($rs['username']))."','".t2js(h2encode($rs['usermail']))."','".t2js(h2encode(langbox($rs['language'])))."','".date('Y-m-d H:i:s',$rs['regdate'])."',".$rs['islock'].");";
    }
    $ds->close();

    print_x(L('users/group/@title'),$ds->fetch(),1);
}
// lazy_user_edit *** *** www.LazyCMS.net *** ***
function lazy_user_edit(){
    $db = get_conn(); require_file('common.php');

    $groupid  = isset($_REQUEST['groupid']) ? $_REQUEST['groupid'] : 0;
    $groupid  = empty($groupid) ? $db->result("SELECT `groupid` FROM `#@_system_group` WHERE 1=1 ORDER BY `groupid` DESC;") : $groupid;
    $userid   = isset($_REQUEST['userid']) ? $_REQUEST['userid'] : 0;
    $username = isset($_POST['username']) ? $_POST['username'] : null;
    $userpass = isset($_POST['userpass']) ? $_POST['userpass'] : null;
    $usermail = isset($_POST['usermail']) ? $_POST['usermail'] : null;
    $question = isset($_POST['question']) ? $_POST['question'] : null;
    $answer   = isset($_POST['answer']) ? $_POST['answer'] : null;
    $language = isset($_POST['language']) ? $_POST['language'] : null;
    $editor   = isset($_POST['editor']) ? $_POST['editor'] : null;
    $title    = empty($userid) ? L('users/user/add/@title') : L('users/user/edit/@title');
    $val = new Validate();
    if ($val->method()) {
        $inSQL = !empty($userid) ? " AND `userid`<>'{$userid}'" : null;
        $val->check('username|1|'.L('users/user/check/name').'|1-30;username|4|'.L('users/user/check/name1')."|SELECT COUNT(userid) FROM `#@_system_users` WHERE `username`='#pro#'{$inSQL}");
        if (empty($userid)) {
            $val->check('userpass|1|'.L('users/user/check/pass1').'|6-30;userpass|2|'.L('users/user/check/pass').'|userpass1');    
        }
        $val->check('usermail|0|'.L('users/user/check/mail').';usermail|validate|'.L('users/user/check/mail1').'|4');
        if ($val->isVal()) {
            $val->out();
        } else {
            if (empty($userid)) {
                $db->insert('#@_system_users',array(
                    'groupid'  => $groupid,
                    'username' => $username,
                    'userpass' => md5($userpass),
                    'userkey'  => '',
                    'usermail' => $usermail,
                    'question' => $question,
                    'answer'   => $answer,
                    'language' => $language,
                    'editor'   => $editor,
                    'regdate'  => now(),
                ));
                $text = L('users/user/pop/addok');
            } else {
                $row = array(
                    'groupid'  => $groupid,
                    'username' => $username,
                    'usermail' => $usermail,
                    'question' => $question,
                    'answer'   => $answer,
                    'language' => $language,
                    'editor'   => $editor,
                );
                if (!empty($userpass)) {
                    $row = array_merge($row,array(
                        'userpass' => md5($userpass),
                        'userkey'  => '',
                    ));
                }
                $db->update('#@_system_users',$row,DB::quoteInto('`userid` = ?',$userid));
                $text = L('users/user/pop/editok');
            }
            echo_json(array(
                'text' => $text,
                'url'  => 'users.php',
            ),1);
        }
    } else {
        if (!empty($userid)) {
            $res = $db->query("SELECT * FROM `#@_system_users` WHERE `userid`=?",$userid);
            if ($rs = $db->fetch($res)) {
                $groupid  = $rs['groupid'];
                $username = h2encode($rs['username']);
                $usermail = h2encode($rs['usermail']);
                $question = h2encode($rs['question']);
                $answer   = h2encode($rs['answer']);
                $language = h2encode($rs['language']);
                $editor   = h2encode($rs['editor']);
            }
        }
    }
    $hl = '<form id="form1" name="form1" method="post" action="">';
    $hl.= '<fieldset><legend rel="tab">'.$title.'</legend>';

    $hl.= '<p><label>'.L('users/user/add/group').'：</label>';
    $hl.= '<select name="groupid" id="groupid" tip="'.L('users/user/add/group').'::'.L('users/user/add/group/@tip').'">';
    $hl.= System::__group(0,0,$groupid);
    $hl.= '</select></p>';

    $hl.= '<p><label>'.L('users/user/add/name').'：</label><input tip="'.L('users/user/add/name').'::'.L('users/user/add/name/@tip').'" class="in2" type="text" name="username" id="username" value="'.$username.'" /></p>';
    $hl.= '<p><label>'.L('users/user/add/pass').'：</label><input tip="'.L('users/user/add/pass').'::'.L('users/user/add/pass/@tip').'" class="in2" type="password" name="userpass" id="userpass" /></p>';
    $hl.= '<p><label>'.L('users/user/add/pass1').'：</label><input tip="'.L('users/user/add/pass1').'::'.L('users/user/add/pass1/@tip').'" class="in2" type="password" name="userpass1" id="userpass1" /></p>';
    $hl.= '<p><label>'.L('users/user/add/mail').'：</label><input tip="'.L('users/user/add/mail').'::'.L('users/user/add/mail/@tip').'" class="in3" type="text" name="usermail" id="usermail" value="'.$usermail.'" /></p>';
    $hl.= '<p><label>'.L('users/user/add/question').'：</label><input tip="'.L('users/user/add/question').'::'.L('users/user/add/question/@tip').'" class="in3" type="text" name="question" id="question" value="'.$question.'" /></p>';
    $hl.= '<p><label>'.L('users/user/add/answer').'：</label><input tip="'.L('users/user/add/answer').'::'.L('users/user/add/answer/@tip').'" class="in4" type="text" name="answer" id="answer" value="'.$answer.'" /></p>';
    
    $hl.= '<p><label>'.L('users/user/add/language').'：</label>';
    $hl.= '<select name="language" id="language" tip="'.L('users/user/add/language').'::'.L('users/user/add/language/@tip').'">';
    $hl.= form_opts('@.language','xml','<option value="#value#"#selected#>#name#</option>',$language);
    $hl.= '</select></p>';
    
    $hl.= '<p><label>'.L('users/user/add/editor').'：</label>';
    $hl.= '<select name="editor" id="editor" tip="'.L('users/user/add/editor').'::'.L('users/user/add/editor/@tip').'">';
    $hl.= form_opts('@.editor','dir','<option value="#value#"#selected#>#name#</option>',$editor);
    $hl.= '</select></p>';

    $hl.= '</fieldset>';
    $hl.= but('save').'<input name="userid" type="hidden" value="'.$userid.'" /></form>';
    print_x($title,$hl);
}