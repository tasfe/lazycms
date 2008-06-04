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
class LazyMyTags extends LazyCMS{
    // _index *** *** www.LazyCMS.net *** ***
    function _index(){
        $this->checker(C('CURRENT_MODULE'));
        $dp = O('Record');
        $dp->create('SELECT * FROM `#@_mytags` WHERE 1 ORDER BY `mtorder` DESC,`mtid` DESC');
        $dp->action = url(C('CURRENT_MODULE'),'Set');
        $dp->url = url(C('CURRENT_MODULE'),null,'page=$');
        $button  = !C('SITE_MODE') ? 'create:'.L('common/create') : null;
        $dp->but = $dp->button($button).$dp->plist();
        $dp->td  = "cklist(K[0]) + K[0] + ') <a href=\"".url(C('CURRENT_MODULE'),'Edit','mtid=$',"' + K[0] + '")."\">' + K[1] + '</a>'";
        $dp->td  = "'<div title=\"'+K[2]+'\">{lazy:mytags name=\"'+K[1]+'\"/}</div>'";
        $dp->td  = !C('SITE_MODE') ? "isExist(K[0],K[7],'create:' + K[6]) + K[6]" : "browse(K[6]) + K[6]";
        $dp->td  = "K[3]+'x'+K[4]";
        $dp->td  = "K[5]";
        $dp->td  = "ico('edit','".url(C('CURRENT_MODULE'),'Edit','mtid=$',"' + K[0] + '")."') + updown('up',K[0]) + updown('down',K[0])";
        $dp->open();
        $dp->thead  = '<tr><th>'.$this->L('list/id').') '.$this->L('list/name').'</th><th>'.$this->L('list/tag').'</th><th>'.$this->L('list/file').'</th><th>'.$this->L('list/size').'</th><th>'.$this->L('list/date').'</th><th class="wp2">'.$this->L('list/action').'</th></tr>';
        while ($data = $dp->result()) {
            $dp->tbody = "ll(".$data['mtid'].",'".t2js(htmlencode($data['mtname']))."','".t2js(htmlencode($data['mttitle']))."',".$data['mtwidth'].",".$data['mtheight'].",'".date('Y-m-d H:i:s',$data['mtdate'])."','".MyTags::show($data['mtid'])."',".(file_exists(LAZY_PATH.M(C('CURRENT_MODULE'),'MYTAGS_CREATE_FOLDER').'/'.$data['mtname'].$data['mtext']) ? 1 : 0).");";
        }
        $dp->close();

        $this->outHTML = $dp->fetch;


        $this->display('index.php');
    }
    // _showtags *** *** www.LazyCMS.net *** ***
    function _showtags(){
        $mtid = isset($_REQUEST['mtid']) ? (int)$_REQUEST['mtid'] : null;
        echo MyTags::view($mtid);
    }
    // _edit *** *** www.LazyCMS.net *** ***
    function _edit(){
        $this->checker(C('CURRENT_MODULE'));

        $db  = getConn();
        $sql = "mtname,mttitle,mttext,mtwidth,mtheight,mtext";//5
        $mtid = isset($_REQUEST['mtid']) ? (int)$_REQUEST['mtid'] : null;
        $oldname = isset($_POST['oldname'])?$_POST['oldname']:null;
        if (empty($mtid)) {
            $menu = $this->L('common/add').'|#|true';
        } else {
            $menu = $this->L('common/add').'|'.url(C('CURRENT_MODULE'),'Edit').';'.$this->L('common/edit').'|#|true';
        }
        // 循环取得各POST值
        foreach (explode(',',$sql) as $val) {
            $data[]= isset($_POST[$val]) ? $_POST[$val] : null;
        }
        $data[5] = isset($data[5]) ? $data[5] : M(C('CURRENT_MODULE'),'MYTAGS_ADD_DEF_EXT');
        if (!empty($mtid)) {
            $mtname = " and `mtid`<>".$mtid;
        }
        $mtname = "mtname|1|".$this->L('check/name')."|1-50;mtname|validate|".$this->L('check/name1')."|3;mtname|3|".$this->L('check/name2')."|SELECT COUNT(`mtid`) FROM `#@_mytags` WHERE `mtname`='#pro#'".(isset($mtname)?$mtname:'');
        $this->validate(array(
            'mtname'  => $this->check($mtname),
            'mttitle' => $this->check("mttitle|1|".$this->L('check/title')."|1-250"),
            'mttext'  => $this->check("mttext|0|".$this->L('check/text')),
        ));
        if ($this->method()) {
            if ($this->validate()) {
                if (empty($mtid)) { // insert
                    $row = array(
                        'mtorder'  => (int)$db->max('mtid','#@_mytags'),
                        'mtname'   => (string)$data[0],
                        'mttitle'  => (string)$data[1],
                        'mttext'   => (string)$data[2],
                        'mtwidth'  => (int)$data[3],
                        'mtheight' => (int)$data[4],
                        'mtext'    => (string)$data[5],
                        'mtdate'   => now(),
                    );
                    $db->insert('#@_mytags',$row);
                    $mtid = $db->lastInsertId();
                } else { // update
                    $set = array(
                        'mtname'   => (string)$data[0],
                        'mttitle'  => (string)$data[1],
                        'mttext'   => (string)$data[2],
                        'mtwidth'  => (int)$data[3],
                        'mtheight' => (int)$data[4],
                        'mtext'    => (string)$data[5],
                    );
                    $where = $db->quoteInto('`mtid` = ?',$mtid);
                    $db->update('#@_mytags',$set,$where);
                }
                MyTags::create($mtid,$oldname);
                redirect(url(C('CURRENT_MODULE')));return true;
            }
        } else {
            if (!empty($mtid)) {
                $res = $db->query("SELECT {$sql} FROM `#@_mytags` WHERE `mtid` = ?;",$mtid);
                if (!$data = $db->fetch($res,0)) {
                    throwError(L('error/invalid'));
                }
            }
        }
        $this->assign(array(
            'mtid'      => $mtid,
            'mtname'    => htmlencode($data[0]),
            'mttitle'   => htmlencode($data[1]),
            'mttext'    => htmlencode($data[2]),
            'mtwidth'   => htmlencode(isset($data[3])?$data[3]:0),
            'mtheight'  => htmlencode(isset($data[4])?$data[4]:0),
            'mtext'     => htmlencode($data[5]),
            'arrwidth'  => array(160,250,336,468,728),
            'arrheight' => array(60,90,250,280,600),
            'arrext'    => array('.htm','.html','.shtm','.shtml','.xml','.js'),
            'oldname'   => empty($oldname) ? $data[0].$data[5] : $oldname,
            'menu'      => $menu,
        ));
        $this->display('edit.php');
    }
    // _sortset *** *** www.LazyCMS.net *** ***
    function _set(){
        clearCache();
        $this->checker(C('CURRENT_MODULE'),true);
        $db     = getConn();
        $submit = isset($_POST['submit']) ? $_POST['submit'] : null;
        $lists  = isset($_POST['lists']) ? $_POST['lists'] : null;
        if (empty($lists)) {
            $this->poping($this->L('pop/select'),0);
        }
        switch($submit){
            case 'delete' :
                $res = $db->query("SELECT `mtname`,`mtext` FROM `#@_mytags` WHERE `mtid` IN({$lists})");
                while ($data = $db->fetch($res,0)){
                    @unlink(LAZY_PATH.M(C('CURRENT_MODULE'),'MYTAGS_CREATE_FOLDER').'/'.$data[0].$data[1]);
                }
                $db->exec("DELETE FROM `#@_mytags` WHERE `mtid` IN({$lists});");
                $this->poping($this->L('pop/deleteok'),1);
                break;
            case 'create' :
                MyTags::create($lists);
                $this->poping($this->L('pop/createok'),1);
                break;
            case 'updown' :
                $updown = isset($_POST['updown']) ? (string)$_POST['updown'] : null;
                $num    = isset($_POST['num']) ? (int)$_POST['num'] : null;
                $this->order("#@_mytags,mtid,mtorder","{$lists},{$updown},{$num}");
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
}