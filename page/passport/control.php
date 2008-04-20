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
    // _index *** *** www.LazyCMS.net *** ***
    function _index(){
        // 会员分组列表
        $this->checker(C('CURRENT_MODULE'));
        $db = getConn();
        $dp = O('Record');
        $dp->action = url(C('CURRENT_MODULE'),'GroupSet');
        $dp->result = $db->query("SELECT `g`.*,count(`u`.`userid`) AS `count`
                                    FROM `#@_passport_group` AS `g` 
                                    LEFT JOIN `#@_passport` AS `u` ON `g`.`groupid` = `u`.`groupid`
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

        $tpl = getTpl($this);
        $tpl->display('index.php');
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
        $sql     = "groupname,groupename";//1
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
                        'groupname'  => $data[0],
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
        
        $tpl = getTpl($this);
        $tpl->assign(array(
            'groupid'    => $groupid,
            'groupname'  => htmlencode($data[0]),
            'groupename' => htmlencode($data[1]),
            'menu'       => $menu,
            'readonly'   => !empty($groupid) ? ' readonly="true"' : null,
        ));
        $tpl->display('groupedit.php');
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
        $tpl = getTpl($this);
        $tpl->display('groupleadin.php');
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
        $tpl = getTpl($this);
        $tpl->assign('groupid',$groupid);
        $tpl->display('fields.php');
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
        $tpl = getTpl($this);
        $tpl->assign(array(
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
        $tpl->display('fieldsedit.php');
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
        $tpl = getTpl($this);
        $groupid = isset($_GET['groupid']) ? (int)$_GET['groupid'] : (int)Passport::getTopGroupId();
        $userid = isset($_REQUEST['userid']) ? (int)$_REQUEST['userid'] : null;
        $model = Passport::getModel($groupid);
                
        if (empty($userid)) {
            $menu  = $model['groupname'].'|'.url(C('CURRENT_MODULE'),'List','groupid='.$groupid).';'.$this->L('common/adduser').'|#|true';
        } else {
            $menu  = $model['groupname'].'|'.url(C('CURRENT_MODULE'),'List','groupid='.$groupid).';'.$this->L('common/edituser').'|#|true;'.$this->L('common/adduser').'|'.url(C('CURRENT_MODULE'),'Edit','groupid='.$groupid);
        }
        
        //$this->outHTML = $label->fetch;
        
        $tpl->assign(array(
            'userid' => $userid,
            'groupid'=> $groupid,
            'menu'   => $menu,
        ));
        $tpl->display('edit.php');
    }
    // _list *** *** www.LazyCMS.net *** ***
    function _list(){
    	$this->checker(C('CURRENT_MODULE'));
        $groupid = isset($_GET['groupid']) ? (int)$_GET['groupid'] : null;
        $db = getConn();
        $model = Passport::getModel($groupid);
        
        $dp = O('Record');
        $dp->create("SELECT * FROM `#@_passport` AS `p` LEFT JOIN `#@_passport_group` AS `pg` ON `p`.`groupid`=`pg`.`groupid` WHERE `p`.`groupid`='{$groupid}' ORDER BY `p`.`userid` DESC");
        $dp->action = url(C('CURRENT_MODULE'),'Set','groupid='.$groupid);
        $dp->url = url(C('CURRENT_MODULE'),'List','groupid='.$groupid.'&page=$');
        $dp->but = $dp->button().$dp->plist();
        $dp->td  = "cklist(K[0]) + K[0] + ') <a href=\"".url(C('CURRENT_MODULE'),'Edit','groupid='.$groupid.'&userid=$',"' + K[0] + '")."\">' + K[1] + '</a> '";
        $dp->td  = 'ison(K[2])';
        $dp->td  = 'ison(K[3])';
        $dp->td  = 'ison(K[4])';
        $dp->td  = 'ison(K[5])';
        $dp->td  = 'ison(K[6])';
        $dp->td  = "ico('edit','".url(C('CURRENT_MODULE'),'Edit','groupid='.$groupid.'&userid=$',"' + K[0] + '")."')";
        $dp->open();
        $dp->thead  = '<tr><th>'.$this->L('list/user/id').') '.$this->L('list/user/name').'</th><th>'.$this->L('list/user/group').'</th><th>'.$this->L('list/user/email').'</th><th>'.$this->L('list/user/date').'</th><th>'.$this->L('list/user/islock').'</th><th>'.$this->L('list/action').'</th></tr>';
        while ($data = $dp->result()) {
            $dp->tbody = "ll(".$data['userid'].",'".t2js(htmlencode($data['username']))."','".t2js(htmlencode($data['groupname']))."','".t2js(htmlencode($data['usermail']))."','".date('Y-m-d H:i:s',$data['userdate'])."','".$data['islock']."');";
        }
        $dp->close();

        $this->outHTML = $dp->fetch;

        $tpl = getTpl($this);
        $tpl->assign(array(
            'menu' => $model['groupname'].'|#|true;'.$this->L('common/adduser').'|'.url(C('CURRENT_MODULE'),'Edit','groupid='.$groupid),
        ));
        $tpl->display('list.php');
    }
}