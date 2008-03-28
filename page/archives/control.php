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
class LazyArchives extends LazyCMS{
    // _index *** *** www.LazyCMS.net *** ***
    function _index(){
        $this->checker('archives');
        $db = getConn();
        $dp = O('Record');
        $dp->action = url('Archives','SortSet');
        $dp->result = $db->query("SELECT `s`.*,`m`.`modelname`,count(`a`.`id`) AS `count` 
                                    FROM `#@_sort` AS `s` 
                                    LEFT JOIN `#@_archives` AS `a` ON `s`.`sortid` = `a`.`sortid`
                                    LEFT JOIN `#@_model` AS `m` ON `s`.`modelid` = `m`.`modelid`
                                    WHERE `s`.`sortid1`='0' 
                                    GROUP BY `s`.`sortid` 
                                    ORDER BY `s`.`sortorder` DESC,`s`.`sortid` DESC");
        $dp->length = $db->count($dp->result);
        $button  = !C('SITE_MODE') ? '-|create:'.$this->L('common/create').'|-|createsort:'.$this->L('common/createsort').'|createpage:'.$this->L('common/createpage').'|-|createall:'.$this->L('common/createall') : null;
        $dp->but = $dp->button($button);
        $dp->td  = "cklist(K[0]) + K[8] + K[0] + ') <a href=\"".url('Archives','List','sortid=$',"' + K[0] + '")."\">' + K[2] + '</a>'";
        $dp->td  = "K[3]";
        $dp->td  = "K[4]";
        $dp->td  = !C('SITE_MODE') ? "isExist(K[0],K[6],'createsort:".C('SITE_BASE')."' + K[5]) + K[5]" : "browse(K[7]) + K[7]";
        $dp->td  = "ico('new','".url('Archives','Edit','sortid=$',"' + K[0] + '")."') + ico('edit','".url('Archives','EditSort','sortid=$',"' + K[0] + '")."') + updown('up',K[0],K[1]) + updown('down',K[0],K[1])";
        $dp->open();
        $dp->thead  = '<tr><th>'.$this->L('sort/id').') '.$this->L('sort/name').'</th><th>'.$this->L('sort/model').'</th><th>'.$this->L('sort/count').'</th><th>'.$this->L('sort/path').'</th><th class="wp2">'.$this->L('sort/action').'</th></tr>';
        while ($data = $dp->result()) {
            $dp->tbody = "ll(".$data['sortid'].",".$data['sortid1'].",'".t2js(htmlencode($data['sortname']))."','".t2js(htmlencode($data['modelname']))."',".$data['count'].",'".htmlencode($data['sortpath'])."',".(file_exists(LAZY_PATH.$data['sortpath']) ? 1 : 0).",'".Archives::showSort($data['sortid'])."','".Archives::subSort($data['sortid'])."');";
            if (Archives::isOpen($data['sortid'])=="true") {
                $dp->tbody = "$('#dir".$data['sortid']."').addsub(".$data['sortid'].",1,".Archives::isOpen($data['sortid']).");";
            }
        }
        $dp->close();

        $this->outHTML = $dp->fetch;

        $tpl = getTpl($this);
        $tpl->display('index.php');
    }
    // _editsort *** *** www.LazyCMS.net *** ***
    function _editsort(){
        $this->checker('archives');
        $db  = getConn();
        $sql = "sortid1,modelid,sortname,sortpath,keywords,description,sorttemplate1,sorttemplate2,pagetemplate1,pagetemplate2";//9
        $sortid   = isset($_REQUEST['sortid']) ? (int)$_REQUEST['sortid'] : null;
        $modelNum = $db->result("SELECT count(`modelid`) FROM `#@_model` WHERE 1;");if ((int)$modelNum==0) { throwError(L('error/nomodels')); }
        if (empty($sortid)) {
            $menu = $this->L('common/addsort').'|#|true';
        } else {
            $menu = $this->L('common/addsort').'|'.url('Archives','EditSort').';'.$this->L('common/editsort').'|#|true';
        }
        foreach (explode(',',$sql) as $val) {
            $data[] = isset($_POST[$val]) ? $_POST[$val] : null;
        }
        $data[10] = isset($_POST['oldpath']) ? $_POST['oldpath'] : null;
        if (!empty($sortid)) {
            $sortpath = " AND `sortid` <> ".$sortid;
        }
        $this->validate(array(
            'sortname' => $this->check("sortname|1|".$this->L('check/sortname')."|1-50"),
            'sortpath' => $this->check("sortpath|1|".$this->L('check/path')."|1-100;sortpath|4|".$this->L('check/path1').";sortpath|3|".$this->L('check/path2')."|SELECT COUNT(`sortid`) FROM `#@_sort` WHERE `sortpath`='#pro#'".(isset($sortpath) ? $sortpath : '')),
            'modelid'  => empty($sortid) ? $this->check("modelid|0|".L("error/nomodels")) : null,
        ));
        if ($this->method()) {
            if ($this->validate()) {
                if (empty($sortid)) { // insert
                    $row = array(
                        'sortorder'     => $db->max('sortid','#@_sort'),
                        'sortid1'       => $data[0],
                        'modelid'       => $data[1],
                        'sortname'      => $data[2],
                        'sortpath'      => $data[3],
                        'keywords'      => $data[4],
                        'description'   => $data[5],
                        'sorttemplate1' => $data[6],
                        'sorttemplate2' => $data[7],
                        'pagetemplate1' => $data[8],
                        'pagetemplate2' => $data[9],
                    );
                    $db->insert('#@_sort',$row);
                    $sortid = $db->lastInsertId();
                } else { // update
                    $set = array(
                        'sortid1'       => $data[0],
                        'sortname'      => $data[2],
                        'sortpath'      => $data[3],
                        'keywords'      => $data[4],
                        'description'   => $data[5],
                        'sorttemplate1' => $data[6],
                        'sorttemplate2' => $data[7],
                        'pagetemplate1' => $data[8],
                        'pagetemplate2' => $data[9],
                    );
                    $where = $db->quoteInto('`sortid` = ?',$sortid);
                    $db->update('#@_sort',$set,$where);
                }
                Archives::createSort($sortid,$data[10]);
                redirect(url('Archives'));
            }
        } else {
            if (!empty($sortid)) {
                $where = $db->quoteInto('WHERE `sortid` = ?',$sortid);
                $res   = $db->query("SELECT {$sql} FROM `#@_sort` {$where};");
                if (!$data = $db->fetch($res,0)) {
                    throwError(L('error/invalid'));
                }
            }
        }

        $tplPath = C('TEMPLATE_PATH');
        $tplDef  = C('TEMPLATE_DEF');
        $tpl = getTpl($this);
        $tpl->assign(array(
            'sortid'        => $sortid,
            'sortid1'       => $data[0],
            'modelid'       => $data[1],
            'sortname'      => htmlencode($data[2]),
            'sortpath'      => htmlencode($data[3]),
            'keywords'      => htmlencode($data[4]),
            'description'   => htmlencode($data[5]),
            'sorttemplate1' => !empty($data[6]) ? $data[6] : "{$tplPath}/{$tplDef}",
            'sorttemplate2' => !empty($data[7]) ? $data[7] : "{$tplPath}/inside/$/{$tplDef}",
            'pagetemplate1' => !empty($data[8]) ? $data[8] : "{$tplPath}/{$tplDef}",
            'pagetemplate2' => !empty($data[9]) ? $data[9] : "{$tplPath}/inside/$/{$tplDef}",
            'disabled'      => !empty($sortid) ? ' disabled="disabled"' : null,
            'menu'          => $menu,
        ));
        $tpl->display('editsort.php');
    }
    // _sortset *** *** www.LazyCMS.net *** ***
    function _sortset(){
        clearCache();
        $this->checker('archives',true);
        $db     = getConn();
        $submit = isset($_POST['submit']) ? $_POST['submit'] : null;
        $lists  = isset($_POST['lists']) ? $_POST['lists'] : null;
        if (instr('delete,createsort,createpage,createall',$submit) && empty($lists)) {
            $this->poping($this->L('pop/select'),0);
        }
        switch($submit){
            case 'delete' :
                $db->exec("DELETE FROM `#@_sort` WHERE `sortid` IN({$lists});");
                $this->poping($this->L('pop/deleteok'),1);
                break;
            case 'create' :
                $this->poping($this->L('pop/createok'),1);
                break;
            case 'createsort' :
                Archives::createSort($lists);
                $this->poping($this->L('pop/createok'),1);
                break;
            case 'createpage' :
                $this->poping($this->L('pop/createok'),1);
                break;
            case 'createall' :
                $this->poping($this->L('pop/createok'),1);
                break;
            case 'updown' :
                $updown = isset($_POST['updown']) ? (string)$_POST['updown'] : null;
                $num    = isset($_POST['num']) ? (int)$_POST['num'] : null;
                $upid   = isset($_POST['upid']) ? (int)$_POST['upid'] : null;
                $this->order("sort,sortid,sortorder","{$lists},{$updown},{$num}","`sortid1`='{$upid}'");
                break;
            case 'getsub' :
                $space = isset($_POST['space']) ? $_POST['space'] : 1;
                $res   = $db->query("SELECT `s`.*,`m`.`modelname`,count(`a`.`id`) AS `count` 
                                        FROM `#@_sort` AS `s` 
                                        LEFT JOIN `#@_archives` AS `a` ON `s`.`sortid` = `a`.`sortid`
                                        LEFT JOIN `#@_model` AS `m` ON `s`.`modelid` = `m`.`modelid`
                                        WHERE `s`.`sortid1`='{$lists}' 
                                        GROUP BY `s`.`sortid` 
                                        ORDER BY `s`.`sortorder` ASC,`s`.`sortid` ASC");
                $array = array();
                while ($data = $db->fetch($res)) {
                    $array[] = array(
                        'sortid'=> $data['sortid'],
                        'isopen'=> (int)$data['sortopen'] == 0 ? false : true,
                        'issub' => Archives::isSub($data['sortid']),
                        'js'    => "lll(".$data['sortid'].",".$data['sortid1'].",'".t2js(htmlencode($data['sortname']))."','".t2js(htmlencode($data['modelname']))."',".$data['count'].",'".htmlencode($data['sortpath'])."',".(file_exists(LAZY_PATH.$data['sortpath']) ? 1 : 0).",'".Archives::showSort($data['sortid'])."','".Archives::subSort($data['sortid'],$space+1)."');"
                    );
                }
                $db->exec("UPDATE `#@_sort` SET `sortopen`='1' WHERE `sortid`='{$lists}';");
                echo json_encode($array);
                break;
            case 'isopen' :
                $db->exec("UPDATE `#@_sort` SET `sortopen`='0' WHERE `sortid`='{$lists}';");
                break;
            default :
                $this->poping(L('error/invalid'),0);
                break;
        }
    }
    // _list *** *** www.LazyCMS.net *** ***
    function _list(){
        $this->checker('archives');
        $sortid = isset($_GET['sortid']) ? (int)$_GET['sortid'] : null;
        $db = getConn();
        $model = Archives::getModel($sortid);
        $dp = O('Record');
        $dp->create("SELECT * FROM `".$model['maintable']."` WHERE `sortid`='{$sortid}' ORDER BY `order` DESC,`id` DESC");
        $dp->action = url('Archives','Set');
        $dp->url = url('Archives','List','page=$');
        $dp->but = $dp->button().$dp->plist();
        $dp->td  = "cklist(K[0]) + K[0] + ') <a href=\"".url('Archives','Edit','sortid='.$sortid.'&aid=$',"' + K[0] + '")."\">' + K[1] + '</a>'";
        $dp->td  = 'ison(K[2])';
        $dp->td  = 'ison(K[3])';
        $dp->td  = 'ison(K[4])';
        $dp->td  = !C('SITE_MODE') ? "isExist(K[0],K[8],'create:".C('SITE_BASE')."' + K[6]) + K[6]" : "browse(K[9]) + K[9]";
        $dp->td  = 'K[7]';
        $dp->td  = "ico('edit','".url('Archives','Edit','sortid='.$sortid.'&aid=$',"' + K[0] + '")."') + updown('up',K[0],0) + updown('down',K[0],0)";
        $dp->open();
        $dp->thead  = '<tr><th>'.$this->L('list/id').') '.$this->L('list/title').'</th><th>'.$this->L('list/show').'</th><th>'.$this->L('list/commend').'</th><th>'.$this->L('list/top').'</th><th>'.$this->L('list/path').'</th><th>'.$this->L('list/date').'</th><th>'.$this->L('list/action').'</th></tr>';
        while ($data = $dp->result()) {
            $dp->tbody = "ll(".$data['id'].",'".t2js(htmlencode($data['title']))."',".$data['show'].",".$data['commend'].",".$data['top'].",'".t2js(htmlencode($data['img']))."','".t2js(htmlencode($model['sortpath'].'/'.$data['path']))."','".date('Y-m-d H:i:s',$data['date'])."',".(file_exists(LAZY_PATH.$data['sortpath'].'/'.$data['path']) ? 1 : 0).",'".Archives::showArchive($data['id'])."');";
        }
        $dp->close();

        $this->outHTML = $dp->fetch;

        $tpl = getTpl($this);
        $tpl->assign(array(
            'menu' => $model['sortname'].'|#|true;'.$this->L('common/addpage').'|'.url('Archives','Edit','sortid='.$sortid),
        ));
        $tpl->display('list.php');
    }
    // _edit *** *** www.LazyCMS.net *** ***
    function _edit(){
        $this->checker('archives');

		$db  = getConn();
        $tpl = getTpl($this);
        $aid = isset($_REQUEST['aid']) ? (int)$_REQUEST['aid'] : null;
        
        $show    = isset($_POST['show'])     ? $_POST['show'] : true;
        $commend = isset($_POST['commend'])  ? $_POST['commend'] : null;
        $top     = isset($_POST['top'])      ? $_POST['top'] : null;
        $snapimg = isset($_POST['snapimg'])  ? $_POST['snapimg'] : true;
        $upsort  = isset($_POST['upsort'])   ? $_POST['upsort'] : null;
        $checktitle = isset($_POST['checktitle']) ? $_POST['checktitle'] : null;

        $title = isset($_POST['title']) ? $_POST['title'] : null;
        $img   = isset($_POST['img'])   ? $_POST['img'] : null;
        $path  = isset($_POST['path'])  ? $_POST['path'] : null;
        $pathtype = isset($_POST['pathtype']) ? $_POST['pathtype'] : null;
        
        $sortNum  = $db->result("SELECT count(`sortid`) FROM `#@_sort` WHERE 1;");if ((int)$sortNum==0) { throwError(L('error/nosort')); }
        $sortid   = isset($_REQUEST['sortid']) ? (int)$_REQUEST['sortid'] : (int)Archives::getTopSortId();
        $model    = Archives::getModel($sortid);
        
        $maxid = $db->max('id',$model['maintable']);
        
        $menu  = $model['sortname'].'|'.url('Archives','List','sortid='.$sortid).';'.$this->L('common/addpage').'|#|true';

        $label = O('Label');
        $where = $db->quoteInto('WHERE `modelid` = ?',$model['modelid']);
        $label->create("SELECT * FROM `#@_fields` {$where} ORDER BY `fieldorder` ASC, `fieldid` ASC;");
        $formData  = array();
        $fieldData = array();
        while ($data = $label->result()) {
            $fieldData[$data['fieldename']] = $data;
            $formData[$data['fieldename']]  = isset($_POST[$data['fieldename']]) ? $_POST[$data['fieldename']] : null;
        }

        // 需要检查标题
        if ($cktitle) {
            if (empty($aid)) {
                $cktitle = $this->check("title|1|".$this->L('check/title')."|1-255;title|3|".$this->L('check/title1')."|SELECT COUNT(`id`) FROM `".$model['maintable']."` WHERE `title`='#pro#';");
            } else {
                $cktitle = $this->check("title|1|".$this->L('check/title')."|1-255;title|3|".$this->L('check/title1')."|SELECT COUNT(`id`) FROM `".$model['maintable']."` WHERE `title`='#pro#' AND `id`<>'{$aid}';");
            }
        } else {
            $cktitle = $this->check("title|1|".$this->L('check/title')."|1-255");
        }
        // 路径检查
        if (empty($aid)) {
            $checkpath = $this->check("path|1|".$this->L('check/path')."|1-255;path|4|".$this->L('check/path1').";path|3|".$this->L('check/path2')."|SELECT COUNT(`id`) FROM `".$model['maintable']."` WHERE `path`='#pro#';");
        } else {
            $checkpath = $this->check("path|1|".$this->L('check/path')."|1-255;path|4|".$this->L('check/path1').";path|3|".$this->L('check/path2')."|SELECT COUNT(`id`) FROM `".$model['maintable']."` WHERE `path`='#pro#' AND `id`<>'{$aid}';");
        }
        $this->validate(array(
            'title' => $cktitle,
            'path'  => $checkpath,
        ));
        if ($this->method()) {
            if ($this->validate()) {
                if (empty($aid)) { // insert
                    $row = array(
                        'order'   => (int)$maxid,
                        'sortid'  => (int)$sortid,
                        'title'   => (string)$title,
                        'show'    => (int)$show,
                        'commend' => (int)$commend,
                        'top'     => (int)$top,
                        'img'     => (string)$img,
                        'path'    => (string)$path,
                        'date'    => now(),
                    );
                    $db->insert($model['maintable'],$row);
                    $aid = $db->lastInsertId();
                    $addrows = array_merge($formData,array('aid'=>$aid));
                    $db->insert($model['addtable'],$addrows);
                    redirect(url('Archives','List','sortid='.$sortid));
                } else { // update
                    $set = array(
                        'title'   => (string)$title,
                        'show'    => (int)$show,
                        'commend' => (int)$commend,
                        'top'     => (int)$top,
                        'img'     => (string)$img,
                        'path'    => (string)$path,
                    );
					$where = $db->quoteInto('`id` = ?',$aid);
                    $db->update($model['maintable'],$set,$where);
					$where = $db->quoteInto('`aid` = ?',$aid);
					$db->update($model['addtable'],$formData,$where);
					redirect(url('Archives','List','sortid='.$sortid));
                }
            }
        } else {
            if (!empty($aid)) {
                $where = $db->quoteInto('WHERE `id` = ?',$aid);
                $res   = $db->query("SELECT * FROM `".$model['maintable']."` {$where};");
                if ($data = $db->fetch($res)) {
                    $sortid  = $data['sortid'];
                    $title   = $data['title'];
                    $show    = $data['show'];
                    $commend = $data['commend'];
                    $top     = $data['top'];
                    $img     = $data['img'];
                    $path    = $data['path'];
                    $formData = Archives::getData($aid,$model['addtable']);
                } else {
                    throwError(L('error/invalid'));
                }
            }
        }

        while (list($name,$data) = each($fieldData)) {
            $label->p = '<p><label>'.$data['fieldname'].'</label>'.$label->tag($data,$formData[$name]).'</p>';
        }
        $this->outHTML = $label->fetch;

        $tpl->assign(array(
            'aid'    => $aid,
            'sortid' => $sortid,
            'title'  => $title,
            'img'    => $img,
            'path'   => empty($aid) ? $maxid.C('HTML_URL_SUFFIX') :$path,
            'show'   => !empty($show) ? ' checked="checked"' : null,
            'top'    => !empty($top) ? ' checked="checked"' : null,
            'snapimg' => !empty($snapimg) ? ' checked="checked"' : null,
            'upsort'  => !empty($upsort) ? ' checked="checked"' : null,
            'commend' => !empty($commend) ? ' checked="checked"' : null,
            'checktitle' => !empty($checktitle) ? ' checked="checked"' : null,
            'pathtype_id' => $maxid.C('HTML_URL_SUFFIX'),
            'pathtype_date' => date('Y/m/d/').$maxid,
            'upath' => C('UPFILE_PATH'),
			'disabled' => !empty($aid) ? ' disabled="disabled"' : null,
            'menu'  => $menu,
        ));
        $tpl->display('edit.php');
    }
}