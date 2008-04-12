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
class LazyOnepage extends LazyCMS{
    // _index *** *** www.LazyCMS.net *** ***
    function _index(){
        $this->checker('onepage');
        $dp = O('Record');
        $dp->create('SELECT * FROM `#@_onepage` WHERE 1 ORDER BY `oneorder` DESC,`oneid` DESC');
        $dp->action = url('Onepage','Set');
        $dp->url = url('Onepage',null,'page=$');
        $button  = !C('SITE_MODE') ? 'create:'.L('common/create') : null;
        $dp->but = $dp->button($button).$dp->plist();
        $dp->td  = "cklist(K[0]) + K[0] + ') <a href=\"".url('Onepage','Edit','oneid=$',"' + K[0] + '")."\">' + K[1] + '</a>'";
        $dp->td  = !C('SITE_MODE') ? "isExist(K[0],K[4],'create:' + K[2]) + K[2]" : "browse(K[2]) + K[2]";
        // 静态网站模式，不显示设为首页，将文件设置为服务器默认文档即可
        if (C('SITE_MODE')) {
            $dp->td  = "home(K[3],'".url('Onepage','SetHome','oneid=$',"' + K[0] + '")."')";
        }
        $dp->td  = "ico('edit','".url('Onepage','Edit','oneid=$',"' + K[0] + '")."') + updown('up',K[0]) + updown('down',K[0])";
        $dp->open();
        $dp->thead  = '<tr><th>'.$this->L('list/id').') '.$this->L('list/name').'</th><th>'.$this->L('list/path').'</th>'.(C('SITE_MODE') ? '<th>'.$this->L('list/home').'</th>' : null).'<th class="wp2">'.$this->L('list/action').'</th></tr>';
        while ($data = $dp->result()) {
            $dp->tbody = "ll(".$data['oneid'].",'".t2js(htmlencode($data['onename']))."','".Onepage::show($data['oneid'])."',".$data['ishome'].",".(file_exists(LAZY_PATH.$data['onepath']) ? 1 : 0).");";
        }
        $dp->close();

        $this->outHTML = $dp->fetch;

        $tpl = getTpl($this);
        $tpl->display('index.php');
    }
    // _show *** *** www.LazyCMS.net *** ***
    function _showpage(){
        $oneid = isset($_REQUEST['oneid']) ? (int)$_REQUEST['oneid'] : null;
        echo Onepage::view($oneid);
    }
    // _set *** *** www.LazyCMS.net *** ***
    function _set(){
        clearCache();
        $this->checker('onepage',true);
        $db     = getConn();
        $submit = isset($_POST['submit']) ? $_POST['submit'] : null;
        $lists  = isset($_POST['lists']) ? $_POST['lists'] : null;
        if (empty($lists)) {
            $this->poping($this->L('pop/select'),0);
        }
        switch($submit){
            case 'delete' :
                $res = $db->query("SELECT `onepath` FROM `#@_onepage` WHERE `oneid` IN({$lists})");
                while ($data = $db->fetch($res,0)){
                    $paths = explode('/',$data[0]);
                    if (strpos($paths[count($paths)-1],'.')!==false){ //文件
                        @unlink(LAZY_PATH.$data[0]);
                        if (strpos($data[0],'/')!==false){
                            $path = substr($data[0],0,strlen($data[0])-strlen($paths[count($paths)-1]));
                            rmdirs(LAZY_PATH.$path,false);
                        }
                    } else { //目录
                        @unlink(LAZY_PATH.$data[0].'/'.C('SITE_INDEX'));
                        rmdirs(LAZY_PATH.$data[0],false);
                    }
                }
                $db->exec("DELETE FROM `#@_onepage` WHERE `oneid` IN({$lists});");
                $this->poping($this->L('pop/deleteok'),1);
                break;
            case 'create' :
                Onepage::create($lists);
                $this->poping($this->L('pop/createok'),1);
                break;
            case 'updown' :
                $updown = isset($_POST['updown']) ? (string)$_POST['updown'] : null;
                $num    = isset($_POST['num']) ? (int)$_POST['num'] : null;
                $this->order("#@_onepage,oneid,oneorder","{$lists},{$updown},{$num}");
                break;
            default :
                $this->poping(L('error/invalid'),0);
                break;
        }
    }
    // _sethome *** *** www.LazyCMS.net *** ***
    function _sethome(){
        $this->checker('onepage');
        $oneid = isset($_REQUEST['oneid']) ? (int)$_REQUEST['oneid'] : null;
        $db  = getConn();
        $db->exec("UPDATE `#@_onepage` SET `ishome`='0' WHERE 1;");
        $set = array(
            'ishome' => '1',
        );
        $where = $db->quoteInto('`oneid` = ?',$oneid);
        $db->update('#@_onepage',$set,$where);
        redirect($_SERVER['HTTP_REFERER']);
    }
    // _edit *** *** www.LazyCMS.net *** ***
    function _edit(){
        $this->checker('onepage');
        $tpl = getTpl($this);
        $db  = getConn();
        $sql = "onename,onetitle,onepath,onecontent,onekeyword,onedescription,onetemplate1,onetemplate2";//7
        $oneid = isset($_REQUEST['oneid']) ? (int)$_REQUEST['oneid'] : null;
        if (empty($oneid)) {
            $menu = $this->L('common/add').'|#|true';
        } else {
            $menu = $this->L('common/add').'|'.url('Onepage','Edit').';'.$this->L('common/edit').'|#|true';
        }
        // 循环取得各POST值
        foreach (explode(',',$sql) as $val) {
            $data[]= isset($_POST[$val]) ? $_POST[$val] : null;
        }
        $data[8] = isset($_POST['oldpath']) ? $_POST['oldpath'] : null;
        if (!empty($oneid)) {
            $onepath = " AND `oneid` <> ".$oneid;
        }
        $this->validate(array(
            'onename'    => $this->check("onename|1|".$this->L('check/name')."|1-50"),
            'onetitle'   => $this->check("onetitle|1|".$this->L('check/title')."|1-100"),
            'onepath'    => $this->check("onepath|1|".$this->L('check/path')."|1-100;onepath|4|".$this->L('check/path1').";onepath|3|".$this->L('check/path2')."|SELECT COUNT(`oneid`) FROM `#@_onepage` WHERE `onepath`='#pro#'".(isset($onepath) ? $onepath : '')),
            'onekeyword' => $this->check("onekeyword|1|".$this->L('check/keyword')."|0-50"),
            'onedescription' => $this->check("onedescription|1|".$this->L('check/description')."|0-250"),
        ));
        if ($this->method()) {
            if ($this->validate()) {
                $content = clearHTML($data[3]);
                if (empty($content)) {
                    $data[3] = L('error/rsnot');
                }
                if (empty($data[5])) {
                    $data[5] = lefte($content,200);
                }
                if (empty($data[4])) {
                    $data[4] = $this->keys($data[0].$data[1].$data[3]);
                } else {
                    $data[4] = $this->keys(null,$data[4]);
                }
                if (empty($oneid)) { // insert
                    $num = $db->count("SELECT `oneid` FROM `#@_onepage` WHERE 1");
                    $row = array(
                        'oneorder'       => $db->max('oneid','#@_onepage'),
                        'onename'        => $data[0],
                        'onetitle'       => $data[1],
                        'onepath'        => $data[2],
                        'onecontent'     => $data[3],
                        'onekeyword'     => $data[4],
                        'onedescription' => $data[5],
                        'onetemplate1'   => $data[6],
                        'onetemplate2'   => $data[7],
                        'ishome'         => ($num>0 ? '0' : '1'),
                    );
                    $db->insert('#@_onepage',$row);
                    $oneid = $db->lastInsertId();
                } else { // update
                    $set = array(
                        'onename'        => $data[0],
                        'onetitle'       => $data[1],
                        'onepath'        => $data[2],
                        'onecontent'     => $data[3],
                        'onekeyword'     => $data[4],
                        'onedescription' => $data[5],
                        'onetemplate1'   => $data[6],
                        'onetemplate2'   => $data[7],
                    );
                    $where = $db->quoteInto('`oneid` = ?',$oneid);
                    $db->update('#@_onepage',$set,$where);
                }
                Onepage::create($oneid,$data[8]);
                redirect(url('Onepage'));
            }
        } else {
            if (!empty($oneid)) {
                $where = $db->quoteInto('WHERE `oneid` = ?',$oneid);
                $res   = $db->query("SELECT {$sql} FROM `#@_onepage` {$where};");
                if (!$data = $db->fetch($res,0)) {
                    throwError(L('error/invalid'));
                }    
            }
        }
        $tplPath = C('TEMPLATE_PATH');
        $tplDef  = C('TEMPLATE_DEF');
        $tpl->assign(array(
            'oneid'          => $oneid,
            'onename'        => htmlencode($data[0]),
            'onetitle'       => htmlencode($data[1]),
            'onepath'        => htmlencode($data[2]),
            'onecontent'     => $data[3],
            'onekeyword'     => htmlencode($data[4]),
            'onedescription' => htmlencode($data[5]),
            'onetemplate1'   => !empty($data[6]) ? $data[6] : "{$tplPath}/{$tplDef}",
            'onetemplate2'   => !empty($data[7]) ? $data[7] : "{$tplPath}/inside/onepage/{$tplDef}",
            'menu'           => $menu,
        ));
        $tpl->display('edit.php');
    }
}