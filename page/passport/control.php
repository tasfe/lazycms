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
        $dp->thead  = '<tr><th>'.$this->L('list/group/id').') '.$this->L('list/group/name').'</th><th>'.$this->L('list/group/ename').'</th><th>'.$this->L('list/group/addtable').'</th><th>'.$this->L('list/group/state').'</th><th>'.$this->L('list/group/count').'</th><th class="wp2">'.$this->L('list/group/action').'</th></tr>';
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
                        'grouptable' => '#@_user'.$data[1],
                    );
                    $db->insert('#@_passport_group',$row);
                    // 删除已存在的表
                    $db->exec("DROP TABLE IF EXISTS `#@_user".$data[1]."`;");
                    // 创建新表
                    $db->exec("CREATE TABLE IF NOT EXISTS `#@_user".$data[1]."` (
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
                $where = $db->quoteInto('WHERE `groupid` = ?',$groupid);
                $res   = $db->query("SELECT {$sql} FROM `#@_passport_group` {$where};");
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
        $res = $db->query("SELECT * FROM `#@_passport_group` WHERE `groupid`='{$groupid}';");
        if ($data = $db->fetch($res)) {
            unset($data['groupid']);
            $groupName = $data['groupename'];
			$data['grouptable'] = str_replace('#@_','',$data['grouptable']);
            $XML['group'] = $data;
        } else {
            $groupName = 'Error';
        }
        $fields = array();
        $res = $db->query("SELECT * FROM `#@_passport_fields` WHERE `groupid`='{$groupid}' ORDER BY `fieldorder` ASC,`fieldid` ASC;");
        while ($data = $db->fetch($res)){
            unset($data['fieldid'],$data['groupid'],$data['fieldorder']);
            $fields[] = $data;
        }
        $XML['fields'] = $fields;
        ob_start();
        header("Content-type: application/octet-stream; charset=utf-8");
        header("Content-Disposition: attachment; filename=LazyCMS_".$groupName.".mod");
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
            $upload->allowExts = "mod";
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
}