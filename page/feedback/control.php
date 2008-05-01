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
class LazyFeedBack extends LazyCMS{
    // _index *** *** www.LazyCMS.net *** ***
    function _index(){
        $db  = getConn();
        $fbtitle   = isset($_POST['fbtitle']) ? $_POST['fbtitle'] : null;
        $fbcontent = isset($_POST['fbcontent']) ? $_POST['fbcontent'] : null;
        
        $this->validate(array(
            'fbtitle'   => $this->check('fbtitle|1|'.$this->L('check/title').'|4-100'),
            'fbcontent' => $this->check("fbcontent|1|".$this->L('check/content')."|6-1000"),
        ));

        $label = O('Label');
        $label->create("SELECT * FROM `#@_feedback_fields` WHERE 1 ORDER BY `fieldorder` ASC, `fieldid` ASC;");
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
                $row = array(
                    'fbtitle'   => (string)$fbtitle,
                    'fbcontent' => (string)$fbcontent,
                    'fbip'      => ip(),
                    'fbdate'    => now(),
                );
                $db->insert('#@_feedback',$row);
                $fbid = $db->lastInsertId();
                $addrows = array_merge($formData,array('fbid'=>$fbid));
                $db->insert(FeedBack::$addTable,$addrows);
                $this->succeed(array(
                    $this->L('list/ok'),
                    $this->L('list/home').'|'.C('SITE_BASE'),
                ));
                $innerHTML = $this->succeed();
            }
        } else {
            while (list($name,$data) = each($fieldData)) {
                $label->p = '<p><label>'.$data['fieldname'].'</label>'.$label->tag($data,$formData[$name]).'</p>';
            }
            $this->outHTML = $label->fetch;    
        }

        $this->assign(array(
            'fbtitle'   => htmlencode($fbtitle),
            'fbcontent' => $fbcontent,
            'editor'    => array(
                'editor'  => 'fckeditor',
                'toolbar' => 'Basic',
                'width'   => 450,
                'height'  => 200,
                'print'   => true
            ),
        ));
        if (!isset($innerHTML)) {
            $innerHTML = $this->fetch('index.php');
        }
        $tag  = O('Tags');
        $HTML = $tag->read(M(C('CURRENT_MODULE'),'FEEDBACK_TEMPLATE'));
        $tag->clear();
        $tag->value('title',encode($this->L('title')));
        $tag->value('inside',encode($innerHTML));
        $outHTML = $tag->create($HTML,$tag->getValue());
        echo $outHTML;
    }
    // _index *** *** www.LazyCMS.net *** ***
    function _admin(){
        $this->checker(C('CURRENT_MODULE'));
        $tag = isset($_GET['tag']) ? $_GET['tag'] : 0;
        if ($tag) {
            $menu  = $this->L('title').'|'.url(C('CURRENT_MODULE'),'Admin').';'.$this->L('common/tag').'|#|true';
            $query = "tag=1&";
        } else {
            $menu  = $this->L('title').'|#|true;'.$this->L('common/tag').'|'.url(C('CURRENT_MODULE'),'Admin','tag=1');
            $query = null;
        }
        $db = getConn(); $where = $tag ? $db->quoteInto('WHERE `istag` = ?',$tag) : '';
        $dp = O('Record');
        $dp->create("SELECT * FROM `#@_feedback` {$where} ORDER BY `fbdate` DESC");
        $dp->action = url(C('CURRENT_MODULE'),'Set');
        $dp->url = url(C('CURRENT_MODULE'),'Admin',$query.'page=$');
        $dp->but = $dp->button('tag1:'.$this->L('common/tag1').'|tag0:'.$this->L('common/tag0')).$dp->plist();
        $onclick = " onclick=\"$(this).gm(\'view\',{lists:' + K[0] + '},{width:\'600px\',\'margin-left\':\'-300px\',height:\'300px\'});\"";
        $dp->td  = "cklist(K[0]) + feedback.tag(K[2],K[0]) + '<a href=\"javascript:void(0);\"{$onclick}>' + K[3] + '</a>'";
        $dp->td  = "'<a href=\"javascript:void(0);\"{$onclick}>' + K[4] + '</a>'";
        $dp->td  = "'<a href=\"".sprintf(M(C('CURRENT_MODULE'),'FEEDBACK_IP_SEARCH'),"' + K[5] + '")."\" target=\"_blank\">' + K[5] + '</a>'";
        $dp->td  = "K[6]";
        $dp->open();
        $dp->thead  = '<tr><th>'.$this->L('list/title').'</th><th>'.$this->L('list/content').'</th><th>'.$this->L('list/ip').'</th><th>'.$this->L('list/date').'</th></tr>';
        while ($data = $dp->result()) {
            $dp->tbody = "ll(".$data['fbid'].",".$data['isview'].",".$data['istag'].",'".t2js(htmlencode($data['fbtitle']))."','".t2js(htmlencode(FeedBack::getTitle($data['fbcontent'])))."','".t2js(htmlencode($data['fbip']))."','".date('Y-m-d H:i:s',$data['fbdate'])."');";
        }
        $dp->close();

        $this->outHTML = $dp->fetch;
        $this->assign('menu',$menu);
        $this->display('admin.php');
    }
    // _set *** *** www.LazyCMS.net *** ***
    function _set(){
        clearCache();
        $this->checker(C('CURRENT_MODULE'),true);
        $db     = getConn();
        $submit = isset($_POST['submit']) ? $_POST['submit'] : null;
        $lists  = isset($_POST['lists']) ? $_POST['lists'] : null;
        if (!instr('settag',$submit) && empty($lists)) {
            $this->poping($this->L('pop/select'),0);
        }
        switch($submit){
            case 'tag0':
                $db->update('#@_feedback',array('istag'=>0),"`fbid` IN({$lists})");
                $this->poping($this->L('pop/tagok'),1);
                break;
            case 'tag1':
                $db->update('#@_feedback',array('istag'=>1),"`fbid` IN({$lists})");
                $this->poping($this->L('pop/tagok'),1);
                break;
            case 'delete':
                $res = $db->query("SELECT `fbid` FROM `#@_feedback` WHERE `fbid` IN ({$lists}) AND `istag` = 0;");
                while ($data = $db->fetch($res,0)){
                    if (empty($Delids)) {
                        $Delids = $data[0];
                    } else {
                        $Delids.= ','.$data[0];
                    }
                }
                $db->exec("DELETE FROM `#@_feedback` WHERE `fbid` in ({$lists}) AND `istag` = 0;");
                $db->exec("DELETE FROM `".FeedBack::$addTable."` WHERE `fbid` in ({$Delids});");
                $this->poping($this->L('pop/deleteok'),1);
                break;
            case 'settag' :
                $action  = isset($_POST['action']) ? $_POST['action'] : null;
                $db->update('#@_feedback',array('istag'=>(int)$action),$db->quoteInto('`fbid` = ?',$lists));
                break;
            case 'view' :
                $res = $db->query("SELECT * FROM `#@_feedback` AS `fb` LEFT JOIN `".FeedBack::$addTable."` AS `fbc` ON `fb`.`fbid`=`fbc`.`fbid` WHERE `fb`.`fbid`=?;",$lists);
                if (!$data = $db->fetch($res)){}

                $data['fbtitle'] = htmlencode($data['fbtitle']);
                $main = '<div id="lz_form" class="lz_form">';
                $main.= '<div><label><strong>'.$this->L('list/title').'</strong></label><blockquote class="in">'.$data['fbtitle'].'</blockquote></div>';
                $main.= '<div><label><strong>'.$this->L('list/content').'</strong></label><blockquote class="in">'.$data['fbcontent'].'</blockquote></div>';
                // 读取自定义字段
                $res = $db->query("SELECT * FROM `#@_feedback_fields` WHERE 1 ORDER BY `fieldorder` ASC, `fieldid` ASC;");
                while ($info = $db->fetch($res)) {
                    $main.= '<div><label><strong>'.$info['fieldname'].'</strong></label><blockquote class="in">'.$data[$info['fieldename']].'</blockquote></div>';
                }
                $main.= '</div>';
                $main.= '<style type="text/css">';
                $main.= '#lz_form{ padding-top:0px; }';
                $main.= '#lz_form .in{ width:95%; margin:0 auto; padding:0 3px; }';
                $main.= '#lz_form div{ margin:10px 0 1px 0; }';
                $main.= '</style>';
                $this->poping(array(
                    'title' => $data['fbtitle'],
                    'main'  => $main,
                ));
                break;
            default :
                $this->poping(L('error/invalid'),0);
                break;
        }
    }
    // _config *** *** www.LazyCMS.net *** ***
    function _config(){
        $this->checker(C('CURRENT_MODULE'));
        $this->config(C('CURRENT_MODULE'));
        $this->display('config.php');
    }
    // _fields *** *** www.LazyCMS.net *** ***
    function _fields(){
        $this->checker(C('CURRENT_MODULE'));
        $db = getConn();
        $dp = O('Record');
        $dp->action = url(C('CURRENT_MODULE'),'FieldSet');
        $dp->result = $db->query("SELECT * FROM `#@_feedback_fields` WHERE 1 ORDER BY `fieldorder` ASC, `fieldid` ASC;");
        $dp->length = $db->count($dp->result);
        $dp->but = $dp->button();
        $dp->td  = "cklist(K[0]) + '<a href=\"".url(C('CURRENT_MODULE'),'FieldsEdit','fieldid=$',"'+K[0]+'")."\">' + K[0] + ') ' + K[1] + '</a>'";
        $dp->td  = "K[2]";
        $dp->td  = "K[3] + (K[6]=='input' ? '(' + K[4] + ')' : '')";
        $dp->td  = "(K[5]=='' ? 'NULL' : K[5])";
        $dp->td  = "ico('edit','".url(C('CURRENT_MODULE'),'FieldsEdit','fieldid=$',"'+K[0]+'")."') + updown('up',K[0]) + updown('down',K[0])";
        $dp->open();
        $dp->thead = '<tr><th>'.$this->L('list/field/id').') '.$this->L('list/field/name').'</th><th>'.$this->L('list/field/ename').'</th><th>'.$this->L('list/field/type').'</th><th>'.$this->L('list/field/default').'</th><th>'.$this->L('list/action').'</th></tr>';
        while ($data = $dp->result()) {
            $dp->tbody = "ll(".$data['fieldid'].",'".t2js(htmlencode($data['fieldname']))."','".t2js(htmlencode($data['fieldename']))."','".$this->L('list/field/type/'.$data['inputtype'])."','".t2js(htmlencode($data['fieldlength']))."','".t2js(htmlencode($data['fieldefault']))."','".$data['inputtype']."');";
        }
        $dp->close();
        $this->outHTML = $dp->fetch;
        $this->display('fields.php');
    }
    // _fieldset *** *** www.LazyCMS.net *** ***
    function _fieldset(){
        clearCache();
        $this->checker(C('CURRENT_MODULE'),true);
        $db      = getConn();
        $submit  = isset($_POST['submit']) ? $_POST['submit'] : null;
        $lists   = isset($_POST['lists']) ? $_POST['lists'] : null;
        switch($submit){
            case 'delete' :
                if (empty($lists)) {
                    $this->poping($this->L('pop/field/select'),0);
                }
                // 取得附加表
                $addtable = FeedBack::$addTable;
                // 组合删除数据库字段的SQL语句
                $DelSQL = "ALTER TABLE `{$addtable}` ";
                $res = $db->query("SELECT `fieldename` FROM `#@_feedback_fields` WHERE `fieldid` IN({$lists});");
                while ($data = $db->fetch($res,0)){
                    $DelSQL.= " DROP `".$data[0]."`,";
                }
                $DelSQL = rtrim($DelSQL,',').";";
                try { // 屏蔽所有错误
                    // 执行删除字段操作
                    $db->exec($DelSQL);
                    $db->exec("DELETE FROM `#@_feedback_fields` WHERE `fieldid` IN({$lists});");
                    $this->poping($this->L('pop/field/deletefieldok'),1);
                } catch (Error $err) {
                    $db->exec("DELETE FROM `#@_feedback_fields` WHERE `fieldid` IN({$lists});");
                    $this->poping($this->L('pop/field/deletefielderr'),1);
                }
                break;
            case 'updown' :
                $updown = isset($_POST['updown']) ? (string)$_POST['updown'] : null;
                $num    = isset($_POST['num']) ? (int)$_POST['num'] : null;
                $updown = $updown=='down' ? 'up' : 'down';
                $this->order("#@_feedback_fields,fieldid,fieldorder","{$lists},{$updown},{$num}");
                break;
            default :
                $this->poping(L('error/invalid'),0);
                break;
        }
    }
    // _fieldsedit *** *** www.LazyCMS.net *** ***
    function _fieldsedit(){
        $this->checker(C('CURRENT_MODULE'));
        $db      = getConn();
        $fieldid = isset($_REQUEST['fieldid']) ? (int)$_REQUEST['fieldid'] : null;
        $sql     = "fieldname,fieldename,fieldtype,fieldlength,fieldefault,inputtype,fieldvalue";//6
        foreach (explode(',',$sql) as $val) {
            $data[] = isset($_POST[$val]) ? $_POST[$val] : null;
        }
        $data[7] = isset($_POST['oldfieldename']) ? $_POST['oldfieldename'] : null;
        if (empty($fieldid)) {
            $menu = $this->L('common/addfields').'|#|true';
        } else {
            $menu = $this->L('common/addfields').'|'.url(C('CURRENT_MODULE'),'FieldsEdit').';'.$this->L('common/editfields').'|#|true';
        }
        $this->validate(array(
            'fieldname'  => $this->check('fieldname|1|'.$this->L('check/field/name').'|1-50'),
            'fieldename' => $this->check('fieldename|1|'.$this->L('check/field/ename').'|1-50;fieldename|validate|'.$this->L('check/field/ename1').'|^[A-Za-z0-9\_]+$'),
            'fieldlength'=> instr('input',$data[5]) ? $this->check('fieldlength|1|'.$this->L('check/field/length').'|1-255;fieldlength|validate|'.$this->L('check/field/length1').'|2') : null,
            'fieldvalue' => instr('radio,checkbox,select',$data[5]) ? $this->check('fieldvalue|0|'.$this->L('check/field/value')) : null,
        ));
        if ($this->method()) {
            if ($this->validate()) {
                // 取得附加表
                $addtable = FeedBack::$addTable;
                if (!$db->isTable($addtable)) {
                    $db->exec("CREATE TABLE IF NOT EXISTS `{$addtable}` (`fbid` int(11) NOT NULL,PRIMARY KEY (`fbid`)) ENGINE=MyISAM DEFAULT CHARSET=#~lang~#;");
                }
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
                        'fieldorder'  => $db->max('fieldid','#@_feedback_fields'),
                        'fieldname'   => $data[0],
                        'fieldename'  => $data[1],
                        'fieldtype'   => $data[2],
                        'fieldlength' => $data[3],
                        'fieldefault' => $data[4],
                        'inputtype'   => $data[5],
                        'fieldvalue'  => $data[6],
                    );
                    $db->insert('#@_feedback_fields',$row);
                    // 向附加表添加对应字段
                    $db->exec("ALTER TABLE `{$addtable}` ADD `".$data[1]."` ".$data[2].$length.$default.";");
                } else {//update
                    // 修改字段
                    $set = array(
                        'fieldname'   => $data[0],
                        'fieldename'  => $data[1],
                        'fieldtype'   => $data[2],
                        'fieldlength' => $data[3],
                        'fieldefault' => $data[4],
                        'inputtype'   => $data[5],
                        'fieldvalue'  => $data[6],
                    );
                    $db->update('#@_feedback_fields',$set,$db->quoteInto('`fieldid`= ? ',$fieldid));
                    $db->exec("ALTER TABLE `{$addtable}` CHANGE `".$data[7]."` `".$data[1]."` ".$data[2].$length.$default.";");
                }
                redirect(url(C('CURRENT_MODULE'),'Fields'));
            }
        } else {
            if (!empty($fieldid)) {
                $res   = $db->query("SELECT {$sql} FROM `#@_feedback_fields` WHERE `fieldid`= ?;",$fieldid);
                if (!$data = $db->fetch($res,0)) {
                    throwError(L('error/invalid'));
                }
            }
        }

        $this->assign(array(
            'fieldid'     => $fieldid,
            'fieldname'   => htmlencode($data[0]),
            'fieldename'  => htmlencode($data[1]),
            'fieldtype'   => htmlencode($data[2]),
            'fieldlength' => htmlencode($data[3]),
            'fieldefault' => htmlencode($data[4]),
            'inputtype'   => htmlencode($data[5]),
            'fieldvalue'  => htmlencode($data[6]),
            'menu'        => $menu,
        ));
        $this->display('fieldsedit.php');
    }
}