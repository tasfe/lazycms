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
 * | Author: Lukin <mylukin@gmail.com>                                         |
 * +---------------------------------------------------------------------------+
 */
defined('CORE_PATH') or die('Restricted access!');
/**
 * Control 层
 *
 * @copyright   Copyright (c) 2007-2008 lazycms.net All rights reserved.
 * @author      Lukin <mylukin@gmail.com>
 */
// Control 类名称必须 以Lazy开头，且继承 LazyCMS基础类
class LazyPassport extends LazyCMS{
    public $passport = array();
    // _index *** *** www.LazyCMS.net *** ***
    function _index(){
        // 会员分组列表
        $this->checker(C('CURRENT_MODULE'));
        $db = getConn();
        $dp = O('Record');
        $dp->action = url(C('CURRENT_MODULE'),'GroupSet');
        $dp->result = $db->query("SELECT `g`.*,count(`p`.`userid`) AS `count`
                                    FROM `#@_passport_group` AS `g` 
                                    LEFT JOIN `#@_passport` AS `p` ON (`g`.`groupid` = `p`.`groupid` And `p`.`isdel`=0)
                                    GROUP BY `g`.`groupid`
                                    ORDER BY `g`.`groupid` DESC");
        $dp->length = $db->count($dp->result);
        $dp->but = $dp->button();
        $dp->td  = "cklist(K[0]) + K[0] + ') <a href=\"".url(C('CURRENT_MODULE'),'List','groupid=$',"' + K[0] + '")."\">' + K[1] + '</a>'";
        $dp->td  = "K[2]";
        $dp->td  = "K[3]";
        $dp->td  = "state(K[4],'".url(C('CURRENT_MODULE'),'GroupState','groupid=$&state=1',"' + K[0] + '")."','".url(C('CURRENT_MODULE'),'GroupState','groupid=$&state=0',"' + K[0] + '")."')";
        $dp->td  = "K[5]";
        $dp->td  = "ico('add','".url(C('CURRENT_MODULE'),'Edit','groupid=$',"' + K[0] + '")."') + ico('edit','".url(C('CURRENT_MODULE'),'GroupEdit','groupid=$',"' + K[0] + '")."') + ico('export','".url(C('CURRENT_MODULE'),'Export','groupid=$',"' + K[0] + '")."') + ico('fields','".url(C('CURRENT_MODULE'),'Fields','groupid=$',"' + K[0] + '")."')";
        $dp->open();
        $dp->thead  = '<tr><th>'.$this->L('list/group/id').') '.$this->L('list/group/name').'</th><th>'.$this->L('list/group/ename').'</th><th>'.$this->L('list/group/addtable').'</th><th>'.$this->L('list/group/state').'</th><th>'.$this->L('list/group/count').'</th><th class="wp2">'.$this->L('list/action').'</th></tr>';
        while ($data = $dp->result()) {
            $dp->tbody = "ll(".$data['groupid'].",'".t2js(htmlencode($data['groupname']))."','".t2js(htmlencode($data['groupename']))."','".t2js(htmlencode(str_replace('#@_',C('DSN_PREFIX'),$data['grouptable'])))."',".$data['groupstate'].",".$data['count'].");";
        }
        $dp->close();

        $this->outHTML = $dp->fetch;


        $this->display('index.php');
    }
    // _groupset *** *** www.LazyCMS.net *** ***
    function _groupset(){
        clearCache();
        $this->checker(C('CURRENT_MODULE'),true);
        $db     = getConn();
        $submit = isset($_POST['submit']) ? $_POST['submit'] : null;
        switch($submit){
            case 'delete' :
                $lists = isset($_POST['lists']) ? $_POST['lists'] : null;
                if (empty($lists)) {
                    $this->poping($this->L('pop/group/select'),0);
                }
                $res = $db->query("SELECT `grouptable` FROM `#@_passport_group` WHERE `groupid` IN({$lists})");
                while ($data = $db->fetch($res,0)){
                    $db->exec("DROP TABLE IF EXISTS `".$data[0]."`;");
                }
                $db->exec("DELETE FROM `#@_passport_fields` WHERE `groupid` IN({$lists});");
                $db->exec("DELETE FROM `#@_passport_group` WHERE `groupid` IN({$lists});");
                $this->poping($this->L('pop/group/deleteok'),1);
                break;
            default :
                $this->poping(L('error/invalid'),0);
                break;
        }
    }
    // _groupstate *** *** www.LazyCMS.net *** ***
    function _groupstate(){
        $this->checker(C('CURRENT_MODULE'));
        $groupid = isset($_GET['groupid']) ? (int)$_GET['groupid'] : null;
        $state   = isset($_GET['state']) ? (int)$_GET['state'] : null;
        $db  = getConn();
        $set = array('groupstate' => $state);
        $where = $db->quoteInto('`groupid` = ?',$groupid);
        $db->update('#@_passport_group',$set,$where);
        redirect($_SERVER['HTTP_REFERER']);
    }
    // _groupedit *** *** www.LazyCMS.net *** ***
    function _groupedit(){
        $this->checker(C('CURRENT_MODULE'));
        $db      = getConn();
        $groupid = isset($_REQUEST['groupid']) ? (int)$_REQUEST['groupid'] : null;
        $sql     = "groupname,groupename,template";//2
        foreach (explode(',',$sql) as $val) {
            $data[]= isset($_POST[$val]) ? $_POST[$val] : null;
        }
        if (empty($groupid)) {
            $menu = $this->L('common/addgroup').'|#|true';
        } else {
            $menu = $this->L('common/addgroup').'|'.url(C('CURRENT_MODULE'),'GroupEdit').';'.$this->L('common/editgroup').'|#|true';
        }
       
        $this->validate(array(
            'groupname'  => $this->check('groupname|1|'.$this->L('check/group/name').'|1-50'),
            'groupename' => $this->check('groupename|1|'.$this->L('check/group/ename').'|1-50;groupename|validate|'.$this->L('check/group/ename1').'|^[A-Za-z0-9\_]+$'),
        ));

        if ($this->method()) {
            if ($this->validate()) {
                if(empty($groupid)){//insert
                    $row = array(
                        'groupname'  => $data[0],
                        'groupename' => $data[1],
                        'grouptable' => '#@_passport_group_'.$data[1],
                        'template'   => $data[2],
                    );
                    $db->insert('#@_passport_group',$row);
                    // 删除已存在的表
                    $db->exec("DROP TABLE IF EXISTS `#@_passport_group_".$data[1]."`;");
                    // 创建新表
                    $db->exec("CREATE TABLE IF NOT EXISTS `#@_passport_group_".$data[1]."` (
                                `userid` int(11) NOT NULL,
                                PRIMARY KEY (`userid`)
                               ) ENGINE=MyISAM DEFAULT CHARSET=#~lang~#;");
                } else {//update
                    $set = array(
                        'groupname' => $data[0],
                        'template'  => $data[2],
                    );
                    $where = $db->quoteInto('`groupid` = ?',$groupid);
                    $db->update('#@_passport_group',$set,$where);
                }
                redirect(url(C('CURRENT_MODULE')));
            }
        } else {
            if (!empty($groupid)) {
                $res   = $db->query("SELECT {$sql} FROM `#@_passport_group` WHERE `groupid` = ?;",$groupid);
                if (!$data = $db->fetch($res,0)) {
                    throwError(L('error/invalid'));
                }    
            }
        }
        
        $tplPath = C('TEMPLATE_PATH');
        $tplDef  = C('TEMPLATE_DEF');
        $this->assign(array(
            'groupid'    => $groupid,
            'groupname'  => htmlencode($data[0]),
            'groupename' => htmlencode($data[1]),
            'template'   => !empty($data[2]) ? $data[2] : "{$tplPath}/{$tplDef}",
            'menu'       => $menu,
            'readonly'   => !empty($groupid) ? ' readonly="true"' : null,
        ));
        $this->display('groupedit.php');
    }
    // _export *** *** www.LazyCMS.net *** ***
    function _export(){
        clearCache();
        $this->checker(C('CURRENT_MODULE'));
        $db = getConn();
        $groupid = isset($_GET['groupid']) ? (int)$_GET['groupid'] : null;
        
        $XML = array();
        $res = $db->query("SELECT * FROM `#@_passport_group` WHERE `groupid`=?;",$groupid);
        if ($data = $db->fetch($res)) {
            unset($data['groupid']);
            $groupName = $data['groupename'];
            $data['grouptable'] = str_replace('#@_','',$data['grouptable']);
            $XML['group'] = $data;
        } else {
            $groupName = 'Error';
        }
        $fields = array();
        $res = $db->query("SELECT * FROM `#@_passport_fields` WHERE `groupid`=? ORDER BY `fieldorder` ASC,`fieldid` ASC;",$groupid);
        while ($data = $db->fetch($res)){
            unset($data['fieldid'],$data['groupid'],$data['fieldorder']);
            $fields[] = $data;
        }
        $XML['fields'] = $fields;
        ob_start();
        header("Content-type: application/octet-stream; charset=utf-8");
        header("Content-Disposition: attachment; filename=LazyCMS_".C('CURRENT_MODULE').'_'.$groupName.".xml");
        header("Pragma: no-cache");
        header("Expires: 0");
        echo xmlcode($XML);
        ob_flush();
    }
    // _leadin *** *** www.LazyCMS.net *** ***
    function _leadin(){
        $this->checker(C('CURRENT_MODULE'));
        $field = 'group';
        if ($this->method()) {
            $upload = O('UpLoadFile');
            $upload->allowExts = "xml";
            $upload->maxSize   = 500*1024;//500K
            $folder = LAZY_PATH.C('UPFILE_PATH');mkdirs($folder);
            if ($file = $upload->save($field,$folder.'/'.basename($_FILES[$field]['name']))) {
                $groupCode = loadFile($file['path']); @unlink($file['path']);
                if (!empty($groupCode)) {
                    Passport::installModel($groupCode);
                }
                redirect(url(C('CURRENT_MODULE')));
            } else {
                $this->validate(array(
                    $field => $upload->getError(),
                ));
            }
        }

        $this->display('groupleadin.php');
    }
    // _fields *** *** www.LazyCMS.net *** ***
    function _fields(){
        $this->checker(C('CURRENT_MODULE'));
        $db      = getConn();
        $groupid = isset($_REQUEST['groupid']) ? (int)$_REQUEST['groupid'] : null;
        $dp = O('Record');
        $dp->action = url(C('CURRENT_MODULE'),'FieldSet','groupid='.$groupid);
        $dp->result = $db->query("SELECT * FROM `#@_passport_fields` WHERE `groupid`= ? ORDER BY `fieldorder` ASC, `fieldid` ASC;",$groupid);
        $dp->length = $db->count($dp->result);
        $dp->but = $dp->button();
        $dp->td  = "cklist(K[0]) + '<a href=\"".url(C('CURRENT_MODULE'),'FieldsEdit','groupid=:groupid&fieldid=:fieldid',array('groupid'=>"'+K[7]+'",'fieldid'=>"'+K[0]+'"))."\">' + K[0] + ') ' + K[1] + '</a>'";
        $dp->td  = "K[2]";
        $dp->td  = "K[3] + (K[9]=='input' ? '(' + K[4] + ')' : '')";
        $dp->td  = "(K[5]=='' ? 'NULL' : K[5])";
        $dp->td  = "index(K[8],K[6],'".url(C('CURRENT_MODULE'),'FieldIndex','groupid=:groupid&fieldid=:fieldid&index=0',array('groupid'=>"'+K[7]+'",'fieldid'=>"'+K[0]+'"))."','".url(C('CURRENT_MODULE'),'FieldIndex','groupid=:groupid&fieldid=:fieldid&index=1',array('groupid'=>"'+K[7]+'",'fieldid'=>"'+K[0]+'"))."')";
        $dp->td  = "ico('edit','".url(C('CURRENT_MODULE'),'FieldsEdit','groupid=:groupid&fieldid=:fieldid',array('groupid'=>"'+K[7]+'",'fieldid'=>"'+K[0]+'"))."') + updown('up',K[0]) + updown('down',K[0])";
        $dp->open();
        $dp->thead = '<tr><th>'.$this->L('list/field/id').') '.$this->L('list/field/name').'</th><th>'.$this->L('list/field/ename').'</th><th>'.$this->L('list/field/type').'</th><th>'.$this->L('list/field/default').'</th><th>'.$this->L('list/field/key').'</th><th>'.$this->L('list/action').'</th></tr>';
        while ($data = $dp->result()) {
            $dp->tbody = "ll(".$data['fieldid'].",'".t2js(htmlencode($data['fieldname']))."','".t2js(htmlencode($data['fieldename']))."','".$this->L('list/field/type/'.$data['inputtype'])."','".t2js(htmlencode($data['fieldlength']))."','".t2js(htmlencode($data['fieldefault']))."',".$data['fieldindex'].",".$data['groupid'].",".(int)instr('text,mediumtext',$data['fieldtype']).",'".$data['inputtype']."');";
        }
        $dp->close();
        $this->outHTML = $dp->fetch;

        $this->assign('groupid',$groupid);
        $this->display('fields.php');
    }
    // _fieldindex *** *** www.LazyCMS.net *** ***
    function _fieldindex(){
        $this->checker(C('CURRENT_MODULE'));
        $fieldid = isset($_GET['fieldid']) ? (int)$_GET['fieldid'] : null;
        $groupid = isset($_GET['groupid']) ? (int)$_GET['groupid'] : null;
        $index   = isset($_GET['index']) ? (int)$_GET['index'] : null;
        $db      = getConn();
        try{
            $where     = $db->quoteInto('`groupid` = :groupid AND `fieldid`= :fieldid ',array('groupid'=>$groupid,'fieldid'=>$fieldid));
            $grouptable= $db->result("SELECT `grouptable` FROM `#@_passport_group` WHERE `groupid`='{$groupid}';");
            $fieldname = $db->result("SELECT `fieldename` FROM `#@_passport_fields` WHERE {$where};");
            // 修改为不索引
            if (empty($index)){
                $db->exec("ALTER TABLE `{$grouptable}` DROP INDEX `{$fieldname}`;");
            } else {
                $db->exec("ALTER TABLE `{$grouptable}` ADD INDEX ( `{$fieldname}` ) ;");
            }
            
            $set = array('fieldindex' => $index);
            $db->update('#@_passport_fields',$set,$where);
        } catch(Error $err){}
        redirect($_SERVER['HTTP_REFERER']);
    }
    // _fieldsedit *** *** www.LazyCMS.net *** ***
    function _fieldsedit(){
        $this->checker(C('CURRENT_MODULE'));
        $db      = getConn();
        $groupid = isset($_REQUEST['groupid']) ? (int)$_REQUEST['groupid'] : null;
        $fieldid = isset($_REQUEST['fieldid']) ? (int)$_REQUEST['fieldid'] : null;
        $sql     = "fieldname,fieldename,fieldtype,fieldlength,fieldefault,fieldindex,inputtype,fieldvalue";//7
        foreach (explode(',',$sql) as $val) {
            $data[] = isset($_POST[$val]) ? $_POST[$val] : null;
        }
        $data[8] = isset($_POST['oldfieldename']) ? $_POST['oldfieldename'] : null;
        if (empty($fieldid)) {
            $menu = $this->L('list/field/add').'|#|true';
        } else {
            $menu = $this->L('list/field/add').'|'.url(C('CURRENT_MODULE'),'FieldsEdit','groupid='.$groupid).';'.$this->L('list/field/edit').'|#|true';
        }
        $this->validate(array(
            'fieldname'  => $this->check('fieldname|1|'.$this->L('check/field/name').'|1-50'),
            'fieldename' => $this->check('fieldename|1|'.$this->L('check/field/ename').'|1-50;fieldename|validate|'.$this->L('check/field/ename1').'|^[A-Za-z0-9\_]+$'),
            'fieldlength'=> instr('input',$data[6]) ? $this->check('fieldlength|1|'.$this->L('check/field/length').'|1-255;fieldlength|validate|'.$this->L('check/field/length1').'|2') : null,
            'fieldvalue' => instr('radio,checkbox,select',$data[6]) ? $this->check('fieldvalue|0|'.$this->L('check/field/value')) : null,
        ));
        if ($this->method()) {
            if ($this->validate()) {
                // 取得附加表
                $grouptable = $db->result("SELECT `grouptable` FROM `#@_passport_group` WHERE `groupid`='{$groupid}';");
                if (instr('text,mediumtext,datetime',$data[2])) {
                    $data[3] = null;
                } else {
                    $data[3] = !empty($data[3]) ? $data[3] : 255;
                }
                $length  = !empty($data[3]) ? "( ".$data[3]." ) " : null;
                if ((string)$data[2]!='datetime') {
                    $default = (string)$data[4] ? " DEFAULT '".t2js($data[4])."' " : null;
                } else {
                    $default = null;
                }
                if(empty($fieldid)){//insert
                    $row = array(
                        'fieldorder'  => $db->max('fieldid','#@_passport_fields'),
                        'groupid'     => $groupid,
                        'fieldname'   => $data[0],
                        'fieldename'  => $data[1],
                        'fieldtype'   => $data[2],
                        'fieldlength' => $data[3],
                        'fieldefault' => $data[4],
                        'fieldindex'  => $data[5],
                        'inputtype'   => $data[6],
                        'fieldvalue'  => $data[7],
                    );
                    $db->insert('#@_passport_fields',$row);
                    // 向附加表添加对应字段
                    $SQL     = "ALTER TABLE `{$grouptable}` ADD ";
                    $db->exec($SQL."`".$data[1]."` ".$data[2].$length.$default.";");
                    // 添加为索引字段
                    if (!empty($data[5])){ $db->exec($SQL."INDEX ( `".$data[1]."` ) ;"); }
                } else {//update
                    // 修改字段
                    $set = array(
                        'fieldname'   => $data[0],
                        'fieldename'  => $data[1],
                        'fieldtype'   => $data[2],
                        'fieldlength' => $data[3],
                        'fieldefault' => $data[4],
                        'inputtype'   => $data[6],
                        'fieldvalue'  => $data[7],
                    );
                    $where = $db->quoteInto('`groupid` = :groupid AND `fieldid`= :fieldid ',array('groupid'=>$groupid,'fieldid'=>$fieldid));
                    try{ // 删除索引，并修改字段为不索引
                        if (instr('text,mediumtext',$data[2])) {
                            $db->exec("ALTER TABLE `{$grouptable}` DROP INDEX `".$data[1]."`;");
                            $set = array_merge($set,array('fieldindex' => 0));
                        }
                    } catch(Error $err){}
                    $db->update('#@_passport_fields',$set,$where);
                    $db->exec("ALTER TABLE `{$grouptable}` CHANGE `".$data[8]."` `".$data[1]."` ".$data[2].$length.$default.";");
                }
                redirect(url(C('CURRENT_MODULE'),'Fields',"groupid={$groupid}"));
            }
        } else {
            if (!empty($groupid) && !empty($fieldid)) {
                $res   = $db->query("SELECT {$sql} FROM `#@_passport_fields` WHERE `groupid` = :groupid AND `fieldid`= :fieldid;",array('groupid'=>$groupid,'fieldid'=>$fieldid));
                if (!$data = $db->fetch($res,0)) {
                    throwError(L('error/invalid'));
                }
            }
        }

        $this->assign(array(
            'fieldid'     => $fieldid,
            'groupid'     => $groupid,
            'fieldname'   => htmlencode($data[0]),
            'fieldename'  => htmlencode($data[1]),
            'fieldtype'   => htmlencode($data[2]),
            'fieldlength' => htmlencode($data[3]),
            'fieldefault' => htmlencode($data[4]),
            'inputtype'   => htmlencode($data[6]),
            'fieldvalue'  => htmlencode($data[7]),
            'menu'        => $menu,
            'fieldindex'  => !empty($data[5]) ? ' checked="true"' : null,
            'readonly'    => (!empty($groupid) && !empty($data[5])) ? ' readonly="true"' : null,
        ));
        $this->display('fieldsedit.php');
    }
    // _fieldset *** *** www.LazyCMS.net *** ***
    function _fieldset(){
        clearCache();
        $this->checker(C('CURRENT_MODULE'),true);
        $db      = getConn();
        $groupid = isset($_REQUEST['groupid']) ? (int)$_REQUEST['groupid'] : null;
        $submit  = isset($_POST['submit']) ? $_POST['submit'] : null;
        $lists   = isset($_POST['lists']) ? $_POST['lists'] : null;
        switch($submit){
            case 'delete' :
                if (empty($lists)) {
                    $this->poping($this->L('pop/field/select'),0);
                }
                // 取得附加表
                $grouptable = $db->result("SELECT `grouptable` FROM `#@_passport_group` WHERE `groupid`='{$groupid}';");
                // 组合删除数据库字段的SQL语句
                $DelSQL = "ALTER TABLE `{$grouptable}` ";
                $res = $db->query("SELECT `fieldename` FROM `#@_passport_fields` WHERE `groupid`= ? AND `fieldid` IN({$lists});",$groupid);
                while ($data = $db->fetch($res,0)){
                    $DelSQL.= " DROP `".$data[0]."`,";
                }
                $DelSQL = rtrim($DelSQL,',').";";
                try { // 屏蔽所有错误
                    // 执行删除字段操作
                    $db->exec($DelSQL);
                    $db->exec("DELETE FROM `#@_passport_fields` WHERE `groupid`= ? AND `fieldid` IN({$lists});",$groupid);
                    $this->poping($this->L('pop/field/deletefieldok'),1);
                } catch (Error $err) {
                    $db->exec("DELETE FROM `#@_passport_fields` WHERE `groupid`= ? AND `fieldid` IN({$lists});",$groupid);
                    $this->poping($this->L('pop/field/deletefielderr'),1);
                }
                break;
            case 'updown' :
                $updown = isset($_POST['updown']) ? (string)$_POST['updown'] : null;
                $num    = isset($_POST['num']) ? (int)$_POST['num'] : null;
                $updown = $updown=='down' ? 'up' : 'down';
                $this->order("#@_passport_fields,fieldid,fieldorder","{$lists},{$updown},{$num}","`groupid`='{$groupid}'");
                break;
            default :
                $this->poping(L('error/invalid'),0);
                break;
        }
    }
    // _edit *** *** www.LazyCMS.net *** ***
    function _edit(){
        $this->checker(C('CURRENT_MODULE'));
        $db  = getConn();

        $groupid = isset($_REQUEST['groupid']) ? (int)$_REQUEST['groupid'] : (int)Passport::getTopGroupId(); if ((int)$groupid==0) { throwError($this->L('error/nogroup')); }
        $userid = isset($_REQUEST['userid']) ? (int)$_REQUEST['userid'] : null;
        $model = Passport::getModel($groupid);
                
        if (empty($userid)) {
            $menu  = $model['groupname'].'|'.url(C('CURRENT_MODULE'),'List','groupid='.$groupid).';'.$this->L('common/adduser').'|#|true';
        } else {
            $menu  = $model['groupname'].'|'.url(C('CURRENT_MODULE'),'List','groupid='.$groupid).';'.$this->L('common/edituser').'|#|true;'.$this->L('common/adduser').'|'.url(C('CURRENT_MODULE'),'Edit','groupid='.$groupid);
        }
        
        $username  = isset($_POST['username']) ? $_POST['username'] : null;
        $userpass  = isset($_POST['userpass']) ? $_POST['userpass'] : null;
        $userpass1 = isset($_POST['userpass1']) ? $_POST['userpass1'] : null;
        $usermail  = isset($_POST['usermail']) ? $_POST['usermail'] : null;
        $mailis    = isset($_POST['mailis']) ? $_POST['mailis'] : null;
        $question  = isset($_POST['question']) ? $_POST['question'] : null;
        $answer    = isset($_POST['answer']) ? $_POST['answer'] : null;
        $language  = isset($_POST['language']) ? $_POST['language'] : null;
        $editor    = isset($_POST['editor']) ? $_POST['editor'] : null;
        $islock    = isset($_POST['islock']) ? (int)$_POST['islock'] : null;

        if (empty($userid)) {
            $ckname = $this->check("username|1|".$this->L('check/user/name')."|1-30;username|3|".$this->L('check/user/name1')."|SELECT COUNT(`userid`) FROM `#@_passport` WHERE `username`='#pro#'");
        } else {
            $ckname = $this->check("username|1|".$this->L('check/user/name')."|1-30");
        }
        $this->validate(array(
            'username' => $ckname,
            'userpass' => (!empty($userpass) || !empty($userpass1) || empty($userid)) ? $this->check('userpass|2|'.$this->L('check/user/contrast').'|userpass1;userpass|1|'.$this->L('check/user/pwdsize').'|6-30') : null,
            'usermail' => $this->check("usermail|0|".$this->L('check/user/mail').";usermail|validate|".$this->L('check/user/mail1')."|4"),
        ));

        $label = O('Label');
        $label->create("SELECT * FROM `#@_passport_fields` WHERE `groupid` = ? ORDER BY `fieldorder` ASC, `fieldid` ASC;",$model['groupid']);
        $formData  = array(); $fieldData = array();
        while ($data = $label->result()) {
            $fieldData[$data['fieldename']] = $data;
            $formData[$data['fieldename']]  = isset($_POST[$data['fieldename']]) ? $_POST[$data['fieldename']] : null;
            if (is_array($formData[$data['fieldename']])) {
                $formData[$data['fieldename']] = implode(',',$formData[$data['fieldename']]);
            }
        }

        if ($this->method()) {
            if ($this->validate()) {
                if(!empty($userpass)){
                    $userkey  = salt();
                    $userpass = md5($userpass.$userkey);
                }
                if (empty($userid)) { // insert
                    $row = array(
                        'groupid'  => (int)$groupid,
                        'username' => (string)$username,
                        'userpass' => (string)$userpass,
                        'userkey'  => (string)$userkey,
                        'userdate' => now(),
                        'usermail' => (string)$usermail,
                        'mailis'   => (int)$mailis,
                        'question' => (string)$question,
                        'answer'   => (string)$answer,
                        'language' => (string)$language,
                        'editor'   => (string)$editor,
                        'islock'   => (int)$islock,
                    );
                    $db->insert('#@_passport',$row);
                    $userid = $db->lastInsertId();
                    $addrows = $formData; $addrows['userid'] = $userid;
                    $db->insert($model['grouptable'],$addrows);
                } else { // update
                    $set = array(
                        'groupid'  => (int)$groupid,
                        'username' => (string)$username,
                        'usermail' => (string)$usermail,
                        'mailis'   => (int)$mailis,
                        'question' => (string)$question,
                        'answer'   => (string)$answer,
                        'language' => (string)$language,
                        'editor'   => (string)$editor,
                        'islock'   => (int)$islock,
                    );
                    // 更新密码
                    if(!empty($userpass)){
                        $row = array(
                            'userpass' => $userpass,
                            'userkey'  => $userkey,
                        );
                        $set = array_merge($set,$row);
                    }
                    $where = $db->quoteInto('`userid` = ?',$userid);
                    $db->update('#@_passport',$set,$where);
                    if (!empty($formData)) {
                        $num = $db->count("SELECT * FROM `".$model['grouptable']."` WHERE `userid` = '{$userid}';");
                        if ($num>0) {
                            $where = $db->quoteInto('`userid` = ?',$userid);
                            $db->update($model['grouptable'],$formData,$where);    
                        } else {
                            $addrows = $formData; $addrows['userid'] = $userid;
                            $db->insert($model['grouptable'],$addrows);    
                        }
                    }
                }
                redirect(url(C('CURRENT_MODULE'),'List','groupid='.$groupid));return true;
            }
        } else {
            if (!empty($userid)) {
                $res   = $db->query("SELECT * FROM `#@_passport` WHERE `userid` = ?;",$userid);
                if ($data = $db->fetch($res)) {
                    $groupid  = $data['groupid'];
                    $username = $data['username'];
                    $usermail = $data['usermail'];
                    $mailis   = $data['mailis'];
                    $question = $data['question'];
                    $answer   = $data['answer'];
                    $language = $data['language'];
                    $editor   = $data['editor'];
                    $islock   = $data['islock'];
                    $formData = Passport::getData($userid,$model['grouptable']);
                } else {
                    throwError(L('error/invalid'));
                }
            }
        }

        while (list($name,$data) = each($fieldData)) {
            $label->p = '<p><label>'.$data['fieldname'].'</label>'.$label->tag($data,htmlencode($formData[$name])).'</p>';
        }
        $this->outHTML = $label->fetch;

        $this->assign(array(
            'userid'   => $userid,
            'groupid'  => $groupid,
            'username' => htmlencode($username),
            'usermail' => htmlencode($usermail),
            'mailis'   => !empty($mailis) ? ' checked="checked"' : null,
            'question' => htmlencode($question),
            'answer'   => htmlencode($answer),
            'language' => htmlencode($language),
            'editor'   => $editor,
            'islock'   => $islock,
            'menu'     => $menu,
        ));
        $this->display('edit.php');
    }
    // _list *** *** www.LazyCMS.net *** ***
    function _list(){
        $this->checker(C('CURRENT_MODULE'));
        $groupid = isset($_GET['groupid']) ? (int)$_GET['groupid'] : null;
        $db = getConn();
        $model = Passport::getModel($groupid);
        $dp = O('Record');
        $dp->create("SELECT * FROM `#@_passport` AS `p` LEFT JOIN `#@_passport_group` AS `pg` ON `p`.`groupid`=`pg`.`groupid` WHERE `p`.`groupid`='{$groupid}' AND `p`.`isdel`=0 ORDER BY `p`.`userid` DESC");
        $dp->action = url(C('CURRENT_MODULE'),'Set','groupid='.$groupid);
        $dp->url = url(C('CURRENT_MODULE'),'List','groupid='.$groupid.'&page=$');
        $dp->but = $dp->button('lock:'.$this->L('label/user/islock/is1').'|unlock:'.$this->L('label/user/islock/is0')).$dp->plist();
        $dp->td  = "cklist(K[0]) + K[0] + ') <a href=\"".url(C('CURRENT_MODULE'),'Edit','groupid='.$groupid.'&userid=$',"' + K[0] + '")."\">' + K[1] + '</a> '";
        $dp->td  = 'K[2]';
        $dp->td  = "'<a href=\"mailto:'+K[3]+'\">'+K[3]+'</a>'";
        $dp->td  = 'K[4]';
        $dp->td  = 'ison(K[5])';
        $dp->td  = "ico('edit','".url(C('CURRENT_MODULE'),'Edit','groupid='.$groupid.'&userid=$',"' + K[0] + '")."')";
        $dp->open();
        $dp->thead  = '<tr><th>'.$this->L('list/user/id').') '.$this->L('list/user/name').'</th><th>'.$this->L('list/user/group').'</th><th>'.$this->L('list/user/email').'</th><th>'.$this->L('list/user/date').'</th><th>'.$this->L('list/user/islock').'</th><th>'.$this->L('list/action').'</th></tr>';
        while ($data = $dp->result()) {
            $dp->tbody = "ll(".$data['userid'].",'".t2js(htmlencode($data['username']))."','".t2js(htmlencode($data['groupname']))."','".t2js(htmlencode($data['usermail']))."','".date('Y-m-d H:i:s',$data['userdate'])."',".$data['islock'].");";
        }
        $dp->close();

        $this->outHTML = $dp->fetch;


        $this->assign(array(
            'menu' => $model['groupname'].'|#|true;'.$this->L('common/adduser').'|'.url(C('CURRENT_MODULE'),'Edit','groupid='.$groupid),
        ));
        $this->display('list.php');
    }
    // _set *** *** www.LazyCMS.net *** ***
    function _set(){
        clearCache();
        $this->checker(C('CURRENT_MODULE'),true);$db = getConn();
        $groupid = isset($_GET['groupid']) ? (int)$_GET['groupid'] : null;
        $submit  = isset($_POST['submit']) ? $_POST['submit'] : null;
        $lists = isset($_POST['lists']) ? $_POST['lists'] : null;
        if (empty($lists)) {
            $this->poping($this->L('pop/user/select'),0);
        }
        switch($submit){
            case 'delete' :
                $model = Passport::getModel($groupid);
                $db->exec("UPDATE `#@_passport` SET `isdel`=1 WHERE `userid` IN({$lists});");
                $db->exec("DELETE FROM `".$model['grouptable']."` WHERE `userid` IN({$lists});");
                $this->poping($this->L('pop/user/deleteok'),1);
                break;
            case 'lock' :
                $db->update('#@_passport',array('islock'=>1),"`userid` IN({$lists})");
                $this->poping($this->L('pop/user/lockok'),1);
                break;
            case 'unlock' :
                $db->update('#@_passport',array('islock'=>0),"`userid` IN({$lists})");
                $this->poping($this->L('pop/user/unlockok'),1);
                break;
            default :
                $this->poping(L('error/invalid'),0);
                break;
        }
    }
    // _config *** *** www.LazyCMS.net *** ***
    function _config(){
        $db = getConn();
        $this->checker(C('CURRENT_MODULE')); 
        $sql = "navlogout,navlogin,navuser,reservename";//3
        foreach (explode(',',$sql) as $val) {
            $data[]= isset($_POST[$val]) ? $_POST[$val] : null;unset($_POST[$val]);
        }
        $this->config(C('CURRENT_MODULE'));
        if ($this->method()) {
            $set = array(
                'navlogout'   => (string)$data[0],
                'navlogin'    => (string)$data[1],
                'navuser'     => (string)$data[2],
                'reservename' => (string)$data[3],
            );
            $db->update('#@_passport_config',$set,"`systemname` = 'LazyCMS'");
        } else {
            $res = $db->query("SELECT {$sql} FROM `#@_passport_config` WHERE `systemname` = 'LazyCMS';");
            if (!$data = $db->fetch($res,0)) {
                throwError(L('error/invalid'));
            }
        }
        $this->assign(array(
            'navlogout' => htmlencode($data[0]),
            'navlogin'  => htmlencode($data[1]),
            'navuser'   => htmlencode($data[2]),
            'reservename' => htmlencode($data[3]),
        ));
        $this->display('config.php');
    }
    // _register *** *** www.LazyCMS.net *** ***
    function _register(){
        $db = getConn();
        $groupid = isset($_REQUEST['groupid']) ? (int)$_REQUEST['groupid'] : (int)Passport::getTopGroupId(); if ((int)$groupid==0) { throwError($this->L('error/nogroup')); }
        $model = Passport::getModel($groupid);
        $username  = isset($_POST['username']) ? $_POST['username'] : null;
        $userpass  = isset($_POST['userpass']) ? $_POST['userpass'] : null;
        $usermail  = isset($_POST['usermail']) ? $_POST['usermail'] : null;
        $mailis    = isset($_POST['mailis']) ? $_POST['mailis'] : null;
        $question  = isset($_POST['question']) ? $_POST['question'] : null;
        $answer    = isset($_POST['answer']) ? $_POST['answer'] : null;
        $islock = M(C('CURRENT_MODULE'),'PASSPORT_REG_PASS');

        $this->validate(array(
            'username' => $this->check("username|1|".$this->L('check/user/name')."|1-30;username|3|".$this->L('check/user/name1')."|SELECT COUNT(`userid`) FROM `#@_passport` WHERE `username`='#pro#'"),
            'userpass' => $this->check('userpass|2|'.$this->L('check/user/contrast').'|userpass1;userpass|1|'.$this->L('check/user/pwdsize').'|6-30'),
            'usermail' => $this->check("usermail|0|".$this->L('check/user/mail').";usermail|validate|".$this->L('check/user/mail1')."|4"),
        ));

        $label = O('Label');
        $label->create("SELECT * FROM `#@_passport_fields` WHERE `groupid` = ? ORDER BY `fieldorder` ASC, `fieldid` ASC;",$model['groupid']);
        $formData  = array(); $fieldData = array();
        while ($data = $label->result()) {
            $fieldData[$data['fieldename']] = $data;
            $formData[$data['fieldename']]  = isset($_POST[$data['fieldename']]) ? $_POST[$data['fieldename']] : null;
            if (is_array($formData[$data['fieldename']])) {
                $formData[$data['fieldename']] = implode(',',$formData[$data['fieldename']]);
            }
        }

        if ($this->method()) {
            if ($this->validate()) {
                if(!empty($userpass)){
                    $userkey  = salt();
                    $userpass = md5($userpass.$userkey);
                }
                $row = array(
                    'groupid'  => (int)$groupid,
                    'username' => (string)$username,
                    'userpass' => (string)$userpass,
                    'userkey'  => (string)$userkey,
                    'userdate' => now(),
                    'usermail' => (string)$usermail,
                    'mailis'   => (int)$mailis,
                    'question' => (string)$question,
                    'answer'   => (string)$answer,
                    'language' => C('LANGUAGE'),
                    'islock'   => (int)$islock,
                );
                $db->insert('#@_passport',$row);
                $userid = $db->lastInsertId();
                $addrows = $formData; $addrows['userid'] = $userid;
                $db->insert($model['grouptable'],$addrows);

                $this->succeed(array(
                    $this->L('register/ok'),
                    $this->L('register/login').'|'.url(C('CURRENT_MODULE'),'Login'),
                    $this->L('common/home').'|'.C('SITE_BASE'),
                ));
                $innerHTML = $this->succeed();
            }
        }

        while (list($name,$data) = each($fieldData)) {
            $label->p = '<p><label>'.$data['fieldname'].'</label>'.$label->tag($data,htmlencode($formData[$name])).'</p>';
        }
        $this->outHTML = $label->fetch;

        $this->assign(array(
            'groupid'  => $model['groupid'],
            'username' => htmlencode($username),
            'usermail' => htmlencode($usermail),
            'mailis'   => !empty($mailis) ? ' checked="checked"' : null,
            'question' => htmlencode($question),
            'answer'   => htmlencode($answer),
        ));
        if (!isset($innerHTML)) {
            $innerHTML = $this->fetch('register.php');
        }
        $tag  = O('Tags');
        $HTML = $tag->read($model['template']);
        $tag->clear();
        $tag->value('title',encode($this->L('register/@title')));
        $tag->value('group',encode($model['groupname']));
        $tag->value('inside',encode($innerHTML));
        $outHTML = $tag->create($HTML,$tag->getValue());
        echo $outHTML;
    }
    // _Login *** *** www.LazyCMS.net *** ***
    function _Login(){
        $db = getConn(); $tag  = O('Tags');
        $username = Cookie::get('username');
        $userpass = Cookie::get('userpass');
        if (!empty($username) && !empty($userpass)) {
            $res = $db->query("SELECT * FROM `#@_passport` WHERE `username` = ?;",$username);
            if ($data = $db->fetch($res)) {
                if ($userpass==$data['userpass']) {
                    redirect(url(C('CURRENT_MODULE'),'Main'));exit();
                }
            }
        }

        $username = isset($_POST['username']) ? $_POST['username'] : null;
        $userpass = isset($_POST['userpass']) ? $_POST['userpass'] : null;
        $keep     = isset($_POST['keep']) ? $_POST['keep'] : null;
        $this->validate(array(
            'username' => $this->check("username|1|".$this->L('login/check/name')."|1-30"),
            'userpass' => $this->check('userpass|1|'.$this->L('login/check/pass').'|6-30'),
        ));
        if ($this->method() && $this->validate()) {
            $validity  = $keep ? (now()+3600*24*7) : null;
            $res   = $db->query("SELECT * FROM `#@_passport` WHERE `username` = ?;",$username);
            if ($data = $db->fetch($res)) {
                $md5pass = md5($userpass.$data['userkey']);
                if ($md5pass==$data['userpass']) {
                    $newkey  = substr($md5pass,0,6);
                    $newpass = md5($userpass.$newkey);
                    // 更新数据
                    $set = array(
                        'userpass' => $newpass,
                        'userkey'  => $newkey,
                    );
                    $where = $db->quoteInto('`username` = ?',$username);
                    $db->update('#@_passport',$set,$where);
                    // 设置登陆信息
                    Cookie::set('username',$username,$validity);
                    Cookie::set('userpass',$newpass,$validity);
                    Cookie::set('language',$data['language'],$validity);
                    redirect(url(C('CURRENT_MODULE'),'Main'));
                } else {
                    // 密码不正确，登录失败
                    $this->validate(array(
                        'userpass' => L('login/check/error2'),
                    ));
                }
            } else {
                // 用户名不存在，登录失败
                $this->validate(array(
                    'username' => L('login/check/error1'),
                ));
            }
        }

        $this->assign('username',htmlencode($username));
        $HTML = $tag->read(M(C('CURRENT_MODULE'),'PASSPORT_LOGIN_TPL'));
        $tag->clear();
        $tag->value('title',encode($this->L('login/@title')));
        $tag->value('inside',encode($this->fetch('login.php')));
        $outHTML = $tag->create($HTML,$tag->getValue());
        echo $outHTML;
    }
    // _main *** *** www.LazyCMS.net *** ***
    function _main(){
        if (!Passport::checker()) {
            $this->_logout();exit();
        }
        $db = getConn(); $tag  = O('Tags');
        $HTML = $tag->read(M(C('CURRENT_MODULE'),'PASSPORT_USERCENTER_TPL'));
        $tag->clear();
        $tag->value('title',encode($this->L('usercenter/@title')));
        $tag->value('inside',encode($this->fetch('usercenter.php')));
        $outHTML = $tag->create($HTML,$tag->getValue());
        echo $outHTML;
    }
    // _updatepass *** *** www.LazyCMS.net *** ***
    function _updatepass(){
        if (!Passport::checker()) {
            $this->_logout();exit();
        }
        $db = getConn(); $tag  = O('Tags');
        $oldpass = isset($_POST['oldpass']) ? trim($_POST['oldpass']) : null;
        $newpass = isset($_POST['newpass']) ? trim($_POST['newpass']) : null;
        $md5oldpass  = md5($oldpass.$this->passport['userkey']);
        $isOldPassOk = ($md5oldpass == Cookie::get('userpass')) ? true : false;
        $this->validate(array(
            'oldpass' => $this->check("oldpass|1|".$this->L('usercenter/updatepass/check/oldpass')."|6-30;oldpass|5|".$this->L('usercenter/updatepass/check/error1')."|".$isOldPassOk),
            'newpass' => $this->check('newpass|2|'.$this->L('check/user/contrast').'|newpass1;newpass|1|'.$this->L('check/user/pwdsize').'|6-30'),
        ));
        if ($this->method() && $this->validate()) {
            $newkey  = substr($md5oldpass,0,6);
            $newpass = md5($newpass.$newkey);
            // 更新数据
            $set = array(
                'userpass' => $newpass,
                'userkey'  => $newkey,
            );
            $where = $db->quoteInto('`username` = ?',$this->passport['username']);
            $db->update('#@_passport',$set,$where);
            Cookie::set('userpass',$newpass);
            $this->succeed(array(
                $this->L('usercenter/updatepass/ok'),
                $this->L('common/usercenter').'|'.url(C('CURRENT_MODULE'),'Main'),
                $this->L('common/home').'|'.C('SITE_BASE'),
            ));
            $innerHTML = $this->succeed();
        }
        if (!isset($innerHTML)) {
            $innerHTML = $this->fetch('updatepass.php');
        }
        $HTML = $tag->read(M(C('CURRENT_MODULE'),'PASSPORT_USERCENTER_TPL'));
        $tag->clear();
        $tag->value('title',encode($this->L('usercenter/updatepass/@title')));
        $tag->value('inside',encode($innerHTML));
        $outHTML = $tag->create($HTML,$tag->getValue());
        echo $outHTML;
    }
    // _userconfig *** *** www.LazyCMS.net *** ***
    function _userconfig(){
        if (!Passport::checker()) {
            $this->_logout();exit();
        }
        $db = getConn(); $tag  = O('Tags');
        $userid    = $this->passport['userid'];
        $groupid   = $this->passport['groupid'];
        $grouptable= $this->passport['grouptable'];
        $username  = $this->passport['username'];
        $usermail  = isset($_POST['usermail']) ? $_POST['usermail'] : null;
        $mailis    = isset($_POST['mailis']) ? $_POST['mailis'] : null;
        $question  = isset($_POST['question']) ? $_POST['question'] : null;
        $answer    = isset($_POST['answer']) ? $_POST['answer'] : null;
        $language  = isset($_POST['language']) ? $_POST['language'] : null;
        $editor    = isset($_POST['editor']) ? $_POST['editor'] : null;

        $this->validate(array(
            'usermail' => $this->check("usermail|0|".$this->L('check/user/mail').";usermail|validate|".$this->L('check/user/mail1')."|4"),
        ));

        $label = O('Label');
        $label->create("SELECT * FROM `#@_passport_fields` WHERE `groupid` = ? ORDER BY `fieldorder` ASC, `fieldid` ASC;",$groupid);
        $formData  = array(); $fieldData = array();
        while ($data = $label->result()) {
            $fieldData[$data['fieldename']] = $data;
            $formData[$data['fieldename']]  = isset($_POST[$data['fieldename']]) ? $_POST[$data['fieldename']] : null;
            if (is_array($formData[$data['fieldename']])) {
                $formData[$data['fieldename']] = implode(',',$formData[$data['fieldename']]);
            }
        }
        if ($this->method()) {
            if ($this->validate()) {
                $set = array(
                    'usermail' => (string)$usermail,
                    'mailis'   => (int)$mailis,
                    'question' => (string)$question,
                    'answer'   => (string)$answer,
                    'language' => (string)$language,
                    'editor'   => (string)$editor,
                );
                $where = $db->quoteInto('`userid` = ?',$userid);
                $db->update('#@_passport',$set,$where);
                if (!empty($formData)) {
                    $num = $db->count("SELECT * FROM `".$grouptable."` WHERE `userid` = '{$userid}';");
                    if ($num>0) {
                        $where = $db->quoteInto('`userid` = ?',$userid);
                        $db->update($grouptable,$formData,$where);    
                    } else {
                        $addrows = $formData; $addrows['userid'] = $userid;
                        $db->insert($grouptable,$addrows);
                    }
                }
                $this->succeed(array(
                    $this->L('usercenter/config/ok'),
                    $this->L('common/usercenter').'|'.url(C('CURRENT_MODULE'),'Main'),
                    $this->L('common/home').'|'.C('SITE_BASE'),
                ));
                $innerHTML = $this->succeed();
            }
        } else {
            $usermail = $this->passport['usermail'];
            $mailis   = $this->passport['mailis'];
            $question = $this->passport['question'];
            $answer   = $this->passport['answer'];
            $language = $this->passport['language'];
            $editor   = $this->passport['editor'];
            $formData = Passport::getData($userid,$grouptable);
        }

        while (list($name,$data) = each($fieldData)) {
            $label->p = '<p><label>'.$data['fieldname'].'</label>'.$label->tag($data,htmlencode($formData[$name])).'</p>';
        }
        $this->outHTML = $label->fetch;

        $this->assign(array(
            'username' => htmlencode($username),
            'usermail' => htmlencode($usermail),
            'mailis'   => !empty($mailis) ? ' checked="checked"' : null,
            'question' => htmlencode($question),
            'answer'   => htmlencode($answer),
            'language' => htmlencode($language),
            'editor'   => htmlencode($editor),
        ));
        if (!isset($innerHTML)) {
            $innerHTML = $this->fetch('userconfig.php');
        }
        $HTML = $tag->read(M(C('CURRENT_MODULE'),'PASSPORT_USERCENTER_TPL'));
        $tag->clear();
        $tag->value('title',encode($this->L('usercenter/config/@title')));
        $tag->value('inside',encode($innerHTML));
        $outHTML = $tag->create($HTML,$tag->getValue());
        echo $outHTML;
    }
    // _logout *** *** www.LazyCMS.net *** ***
    function _logout(){
        // 清空cookie
        Cookie::delete('username');
        Cookie::delete('userpass');
        // 跳转到登录页
        redirect(url(C('CURRENT_MODULE'),'Login'));
    }
    // _usernav *** *** www.LazyCMS.net *** ***
    function _usernav(){
        if (Passport::checker()) {
            echo t2js(Passport::navigation('navlogout'),true);
        } else {
            echo t2js(Passport::navigation(),true);
        }
    }
}