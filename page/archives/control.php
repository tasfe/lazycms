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
        $this->checker(C('CURRENT_MODULE'));
        $db = getConn();
        $dp = O('Record');
        $dp->action = url(C('CURRENT_MODULE'),'SortSet');
        $dp->result = $db->query("SELECT `s`.*,`m`.`modelname` 
                                    FROM `#@_archives_sort` AS `s` 
                                    LEFT JOIN `#@_archives_model` AS `m` ON `s`.`modelid` = `m`.`modelid`
                                    WHERE `s`.`sortid1`='0' 
                                    ORDER BY `s`.`sortorder` DESC,`s`.`sortid` DESC");
        $dp->length = $db->count($dp->result);
        $button  = !C('SITE_MODE') ? '|-|createsort:'.$this->L('common/createsort').'|createpage:'.$this->L('common/createpage').'|-|createall:'.$this->L('common/createall') : null;
        $dp->but = $dp->button('-|createrss:'.$this->L('common/createrss').'|createsitemaps:'.$this->L('common/createsitemaps').$button);
        $dp->td  = "cklist(K[0]) + K[7] + K[0] + ') <a href=\"".url(C('CURRENT_MODULE'),'List','sortid=$',"' + K[0] + '")."\">' + K[2] + '</a>'";
        $dp->td  = "K[3]";
        $dp->td  = "K[4]";
        $dp->td  = !C('SITE_MODE') ? "isExist(K[0],K[6],'createsort:' + K[5]) + K[5]" : "browse(K[5]) + K[5]";
        $dp->td  = "ico('add','".url(C('CURRENT_MODULE'),'Edit','sortid=$',"' + K[0] + '")."') + ico('edit','".url(C('CURRENT_MODULE'),'EditSort','sortid=$',"' + K[0] + '")."') + updown('up',K[0],K[1]) + updown('down',K[0],K[1])";
        $dp->open();
        $dp->thead  = '<tr><th>'.$this->L('sort/id').') '.$this->L('sort/name').'</th><th>'.$this->L('sort/model').'</th><th>'.$this->L('sort/count').'</th><th>'.$this->L('sort/path').'</th><th class="wp2">'.$this->L('sort/action').'</th></tr>';
        while ($data = $dp->result()) {
            $dp->tbody = "ll(".$data['sortid'].",".$data['sortid1'].",'".t2js(htmlencode($data['sortname']))."','".t2js(htmlencode($data['modelname']))."',".Archives::Count($data['sortid']).",'".Archives::showSort($data['sortid'])."',".(file_exists(LAZY_PATH.$data['sortpath']) ? 1 : 0).",'".Archives::subSort($data['sortid'])."');";
            if (Archives::isOpen($data['sortid'])=="true") {
                $dp->tbody = "$('#dir".$data['sortid']."').addsub(".$data['sortid'].",1,".Archives::isOpen($data['sortid']).");";
            }
        }
        $dp->close();

        $this->outHTML = $dp->fetch;

        $this->display('index.php');
    }
    // _showsort *** *** www.LazyCMS.net *** ***
    function _showsort(){
        $sortid = isset($_GET['sortid']) ? (int)$_GET['sortid'] : 0;
        $page   = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        echo Archives::viewSort($sortid,$page);
    }
    // _showarchive *** *** www.LazyCMS.net *** ***
    function _showarchive(){
        $aid    = isset($_GET['aid']) ? (int)$_GET['aid'] : 0;
        $sortid = isset($_GET['sortid']) ? (int)$_GET['sortid'] : 0;
        $page   = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        echo Archives::viewArchive($sortid,$aid,$page);
    }
    // _editsort *** *** www.LazyCMS.net *** ***
    function _editsort(){
        $this->checker(C('CURRENT_MODULE'));
        $db  = getConn();
        $sql = "sortid1,modelid,sortname,sortpath,keywords,description,sorttemplate1,sorttemplate2,pagetemplate1,pagetemplate2";//9
        $sortid   = isset($_REQUEST['sortid']) ? (int)$_REQUEST['sortid'] : null;
        $modelNum = $db->result("SELECT count(`modelid`) FROM `#@_archives_model` WHERE 1;");if ((int)$modelNum==0) { throwError(L('error/nomodels')); }
        if (empty($sortid)) {
            $menu = $this->L('common/addsort').'|#|true';
        } else {
            $menu = $this->L('common/addsort').'|'.url(C('CURRENT_MODULE'),'EditSort').';'.$this->L('common/editsort').'|#|true';
        }
        foreach (explode(',',$sql) as $val) {
            $data[] = isset($_POST[$val]) ? $_POST[$val] : null;
        }
        if (!empty($sortid)) {
            $sortpath = " AND `sortid` <> ".$sortid;
        }
        $this->validate(array(
            'sortname' => $this->check("sortname|1|".$this->L('check/sortname')."|1-50"),
            'sortpath' => $this->check("sortpath|1|".$this->L('check/path')."|1-100;sortpath|4|".$this->L('check/path1').";sortpath|3|".$this->L('check/path2')."|SELECT COUNT(`sortid`) FROM `#@_archives_sort` WHERE `sortpath`='#pro#'".(isset($sortpath) ? $sortpath : '')),
            'modelid'  => empty($sortid) ? $this->check("modelid|0|".L("error/nomodels")) : null,
            'keywords' => !empty($data[4]) ? $this->check("keywords|1|".$this->L('check/keywords')."|1-250") : null,
            'description' => !empty($data[5]) ? $this->check("description|1|".$this->L('check/description')."|1-250") : null,
        ));
        if ($this->method()) {
            if ($this->validate()) {
                if (empty($sortid)) { // insert
                    $row = array(
                        'sortorder'     => $db->max('sortid','#@_archives_sort'),
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
                    $db->insert('#@_archives_sort',$row);
                } else { // update
                    $set = array(
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
                    $where = $db->quoteInto('`sortid` = ?',$sortid);
                    $db->update('#@_archives_sort',$set,$where);
                }
                redirect(url(C('CURRENT_MODULE')));
            }
        } else {
            if (!empty($sortid)) {
                $res   = $db->query("SELECT {$sql} FROM `#@_archives_sort` WHERE `sortid` = ?;",$sortid);
                if (!$data = $db->fetch($res,0)) {
                    throwError(L('error/invalid'));
                }
            }
        }

        $tplPath = C('TEMPLATE_PATH');
        $tplDef  = C('TEMPLATE_DEF');

        $this->assign(array(
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
        $this->display('editsort.php');
    }
    // _sortset *** *** www.LazyCMS.net *** ***
    function _sortset(){
        clearCache();
        $this->checker(C('CURRENT_MODULE'),true);
        $db     = getConn();
        $submit = isset($_POST['submit']) ? $_POST['submit'] : null;
        $lists  = isset($_POST['lists']) ? $_POST['lists'] : null;
        if (instr('delete,createsort,createpage,createall,createsitemaps',$submit) && empty($lists)) {
            $this->poping($this->L('pop/select'),0);
        }
        switch($submit){
            case 'delete' :
                $res = $db->query("SELECT `sortpath` FROM `#@_archives_sort` WHERE `sortid` IN({$lists})");
                while ($data = $db->fetch($res,0)){
                    Archives::delArchive($data[0],true);
                }
                $db->exec("DELETE FROM `#@_archives_sort` WHERE `sortid` IN({$lists});");
                $this->poping($this->L('pop/deleteok'),1);
                break;
            case 'createrss':
                Archives::createRss();
                $this->poping($this->L('pop/createok'),0);
                break;
            case 'createsitemaps':
                Archives::createSortSiteMaps($lists);
                Archives::createSiteMaps();
                $this->poping($this->L('pop/createok'),1);
                break;
            case 'createsort': case 'createpage': case 'createall':
                $js = '<script type="text/javascript">';
                $res = $db->query("SELECT * FROM `#@_archives_sort` WHERE `sortid` IN({$lists});");
                while ($data = $db->fetch($res)) {
                    $js.= "loading('{$submit}_".$data['sortid']."','".url(C('CURRENT_MODULE'),'loading',"submit={$submit}&lists=".$data['sortid']."")."','".$data['sortname']."');";
                }
                $js.= '</script>';
                $this->poping($this->L('pop/loading').$js,1);
                break;
            case 'updown' :
                $updown = isset($_POST['updown']) ? (string)$_POST['updown'] : null;
                $num    = isset($_POST['num']) ? (int)$_POST['num'] : null;
                $upid   = isset($_POST['upid']) ? (int)$_POST['upid'] : null;
                $this->order("#@_archives_sort,sortid,sortorder","{$lists},{$updown},{$num}","`sortid1`='{$upid}'");
                break;
            case 'getsub' :
                $space = isset($_POST['space']) ? $_POST['space'] : 1;
                $res   = $db->query("SELECT `s`.*,`m`.`modelname`
                                        FROM `#@_archives_sort` AS `s` 
                                        LEFT JOIN `#@_archives_model` AS `m` ON `s`.`modelid` = `m`.`modelid`
                                        WHERE `s`.`sortid1`= ?  
                                        ORDER BY `s`.`sortorder` ASC,`s`.`sortid` ASC;",$lists);
                $array = array();
                while ($data = $db->fetch($res)) {
                    $array[] = array(
                        'sortid'=> $data['sortid'],
                        'isopen'=> (int)$data['sortopen'] == 0 ? false : true,
                        'issub' => Archives::isSub($data['sortid']),
                        'js'    => "lll(".$data['sortid'].",".$data['sortid1'].",'".t2js(htmlencode($data['sortname']))."','".t2js(htmlencode($data['modelname']))."',".Archives::Count($data['sortid']).",'".Archives::showSort($data['sortid'])."',".(file_exists(LAZY_PATH.$data['sortpath']) ? 1 : 0).",'".Archives::subSort($data['sortid'],$space+1)."');"
                    );
                }
                $db->exec("UPDATE `#@_archives_sort` SET `sortopen`='1' WHERE `sortid`= ? ;",$lists);
                echo json_encode($array);
                break;
            case 'isopen' :
                $db->exec("UPDATE `#@_archives_sort` SET `sortopen`='0' WHERE `sortid`= ? ;",$lists);
                break;
            default :
                $this->poping(L('error/invalid'),0);
                break;
        }
    }
    // _loading *** *** www.LazyCMS.net *** ***
    function _loading(){
        clearCache();
        $this->checker(C('CURRENT_MODULE'));
        $db  = getConn();
        $submit  = isset($_GET['submit']) ? (string)$_GET['submit'] : null;
        $lists   = isset($_GET['lists']) ? (string)$_GET['lists'] : null;
        $page    = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $cacheDir = LAZY_PATH.C('HTML_CACHE_PATH').'/'; mkdirs($cacheDir);
        $sortFile = $cacheDir."Archive_CREATE_SORT_{$lists}.php";
        $pageFile = $cacheDir."Archive_CREATE_PAGE_{$lists}.php";
        Archives::createRss();
        switch($submit){
            case 'createsort' :
                // 清除缓存
                if (is_file($sortFile) && $page==1) { unlink($sortFile); }
                $percent = Archives::viewSort($lists,$page,true);
                if ($percent<100) { $page++; } else { Archives::createSortSiteMaps($lists); }
                echo json_encode(array(
                    'id'      => "{$submit}_{$lists}",
                    'percent' => $percent,
                    'url'     => url(C('CURRENT_MODULE'),'loading',"submit={$submit}&lists={$lists}&page={$page}"),
                    'sleep'   => C('HTML_CREATE_SLEEP'),
                ));
                break;
            case 'createpage' :
                @set_time_limit(0);
                $pageSize = 100;
                // 缓存公用不变内容
                $cachePath = $cacheDir."Archive_CREATEPAGE_{$lists}.php";
                // 清除缓存
                if (is_file($cachePath) && $page==1) { unlink($cachePath); }

                if (is_file($cachePath)) {
                    extract(include($cachePath));
                } else {
                    $model = Archives::getModel($lists);
                    $strSQL = "SELECT * FROM `".$model['maintable']."` WHERE `sortid`='{$lists}'";
                    $totalRows  = $db->count($strSQL);
                    $totalPages = ceil($totalRows/$pageSize);
                    $totalPages = ((int)$totalPages == 0) ? 1 : $totalPages;
                    // 有记录，生成缓存文件
                    if ((int)$totalRows > 0) {
                        saveFile($cachePath,"<?php \nreturn ".var_export(array(
                            'model'  => $model,
                            'strSQL' => $strSQL,
                            'totalRows'  => $totalRows,
                            'totalPages' => $totalPages,
                        ),true)."; \n?>");
                    }
                }
                if ((int)$page > (int)$totalPages) { $page = $totalPages; }
                $percent = round($page/$totalPages*100,2);
                $strSQL .= ' LIMIT '.$pageSize.' OFFSET '.($page-1)*$pageSize.';';
                $res = $db->query($strSQL);
                while ($data = $db->fetch($res)) {
                    Archives::viewArchive($lists,$data['id']);
                }
                if ($percent<100) { $page++; } else { Archives::createSortSiteMaps($lists); }
                echo json_encode(array(
                    'id'      => "{$submit}_{$lists}",
                    'percent' => $percent,
                    'url'     => url(C('CURRENT_MODULE'),'loading',"submit={$submit}&lists={$lists}&page={$page}"),
                    'sleep'   => C('HTML_CREATE_SLEEP'),
                ));
                break;
            case 'createall' :
                // 清除缓存
                if (is_file($sortFile) && $page==1) { unlink($sortFile); }
                if (is_file($pageFile) && $page==1) { unlink($pageFile); }
                $percent = Archives::viewSort($lists,$page,true,true);
                if ($percent<100) { $page++; } else { Archives::createSortSiteMaps($lists); }
                echo json_encode(array(
                    'id'      => "{$submit}_{$lists}",
                    'percent' => $percent,
                    'url'     => url(C('CURRENT_MODULE'),'loading',"submit={$submit}&lists={$lists}&page={$page}"),
                    'sleep'   => C('HTML_CREATE_SLEEP'),
                ));
                break;
            case 'create' :
                $I2 = explode(',',$lists); $count = count($I2);
                $ln = isset($_GET['ln']) ? (int)$_GET['ln'] : 0;
                $sortid = isset($_GET['sortid']) ? (int)$_GET['sortid'] : 0;
                if (is_file($pageFile) && $ln==0) { unlink($pageFile); }
                if ((int)$ln < (int)$count) {
                    Archives::viewArchive($sortid,$I2[$ln]);
                }
                $percent = round($ln/$count*100,2);
                if ($percent<100) { $ln++; }
                echo json_encode(array(
                    'id'      => "{$submit}_{$sortid}",
                    'percent' => $percent,
                    'url'     => url(C('CURRENT_MODULE'),'loading',"submit={$submit}&lists={$lists}&sortid={$sortid}&ln={$ln}"),
                    'sleep'   => C('HTML_CREATE_SLEEP'),
                ));
                break;
        }
    }
    // _list *** *** www.LazyCMS.net *** ***
    function _list(){
        $this->checker(C('CURRENT_MODULE'));
        $sortid = isset($_GET['sortid']) ? (int)$_GET['sortid'] : null;
        $db = getConn();
        $model = Archives::getModel($sortid);
        $dp = O('Record');
        $dp->create("SELECT * FROM `".$model['maintable']."` WHERE `sortid`='{$sortid}' ORDER BY `order` DESC,`id` DESC");
        $dp->action = url(C('CURRENT_MODULE'),'Set','sortid='.$sortid);
        $dp->url = url(C('CURRENT_MODULE'),'List','sortid='.$sortid.'&size='.$dp->size.'&page=$');
        $button  = !C('SITE_MODE') ? 'create:'.L('common/create') : null;
        $dp->but = $dp->button($button).$dp->plist();
        $dp->td  = "cklist(K[0]) + K[0] + ') <a href=\"".url(C('CURRENT_MODULE'),'Edit','sortid='.$sortid.'&aid=$',"' + K[0] + '")."\">' + K[1] + '</a> '+image(K[5])";
        $dp->td  = 'ison(K[2])';
        $dp->td  = 'ison(K[3])';
        $dp->td  = 'ison(K[4])';
        $dp->td  = !C('SITE_MODE') ? "isExist(K[0],K[8],'create:' + K[6])" : "browse(K[6])";
        $dp->td  = 'K[7]';
        $dp->td  = "ico('edit','".url(C('CURRENT_MODULE'),'Edit','sortid='.$sortid.'&aid=$',"' + K[0] + '")."') + updown('up',K[0],{$sortid}) + updown('down',K[0],{$sortid})";
        $dp->open();
        $dp->thead  = '<tr><th>'.$this->L('list/id').') '.$this->L('list/title').'</th><th>'.$this->L('list/show').'</th><th>'.$this->L('list/commend').'</th><th>'.$this->L('list/top').'</th><th>'.$this->L('list/path').'</th><th>'.$this->L('list/date').'</th><th>'.$this->L('list/action').'</th></tr>';
        while ($data = $dp->result()) {
			if (substr($data['path'],0,1)=='/') {
				$data['path'] = ltrim($data['path'],'/');
				$file_exists = file_exists(LAZY_PATH.$data['path']);
			} else {
				$file_exists = file_exists(LAZY_PATH.$model['sortpath'].'/'.$data['path']);
			}
            $dp->tbody = "ll(".$data['id'].",'".t2js(htmlencode($data['title']))."',".$data['show'].",".$data['commend'].",".$data['top'].",'".htmlencode(is_file(LAZY_PATH.$data['img']) ? C('SITE_BASE').$data['img'] : null)."','".Archives::showArchive($data['id'],$model)."','".date('Y-m-d H:i:s',$data['date'])."',".($file_exists ? 1 : 0).");";
        }
        $dp->close();
        $this->outHTML = $dp->fetch;

        $this->assign(array(
            'menu' => $model['sortname'].'|#|true;'.$this->L('common/addpage').'|'.url(C('CURRENT_MODULE'),'Edit','sortid='.$sortid),
        ));

        $this->display('list.php');
    }
    // _edit *** *** www.LazyCMS.net *** ***
    function _edit(){
        $this->checker(C('CURRENT_MODULE'));
        $db  = getConn();

        $aid = isset($_REQUEST['aid']) ? (int)$_REQUEST['aid'] : null;
        
        $CURRENT_MODULE = C('CURRENT_MODULE');
		$isCreateRoot   = M($CURRENT_MODULE,'ARCHIVES_CREATE_ROOTFILE');
		$create_path    = $isCreateRoot==true ? '/' : null;
        $show    = isset($_POST['show'])     ? $_POST['show'] : null;
        $commend = isset($_POST['commend'])  ? $_POST['commend'] : null;
        $top     = isset($_POST['top'])      ? $_POST['top'] : null;
        $snapimg = isset($_POST['snapimg'])  ? $_POST['snapimg'] : null;
        $upsort  = isset($_POST['upsort'])   ? $_POST['upsort'] : null;
        $uphome  = isset($_POST['uphome'])   ? $_POST['uphome'] : null;
        $checktitle = isset($_POST['checktitle']) ? $_POST['checktitle'] : null;

        $title = isset($_POST['title']) ? $_POST['title'] : null;
        $img   = isset($_POST['img'])   ? $_POST['img'] : null;
        $path  = isset($_POST['path'])  ? $_POST['path'] : null;
        $date  = isset($_POST['date'])  ? $_POST['date'] : time();
        $setimg = isset($_POST['setimg'])  ? $_POST['setimg'] : null;
        $pathtype = isset($_POST['pathtype']) ? $_POST['pathtype'] : null;
        $keywords = isset($_POST['keywords'])  ? $_POST['keywords'] : null;
        $description = isset($_POST['description'])  ? $_POST['description'] : null;
        
        $sortNum  = $db->result("SELECT count(`sortid`) FROM `#@_archives_sort` WHERE 1;");if ((int)$sortNum==0) { throwError(L('error/nosort')); }
        $sortid   = isset($_REQUEST['sortid']) ? (int)$_REQUEST['sortid'] : (int)Archives::getTopSortId();
        $model    = Archives::getModel($sortid);
        
        if (empty($aid)) {
            $menu  = $model['sortname'].'|'.url(C('CURRENT_MODULE'),'List','sortid='.$sortid).';'.$this->L('common/addpage').'|#|true';
        } else {
            $menu  = $model['sortname'].'|'.url(C('CURRENT_MODULE'),'List','sortid='.$sortid).';'.$this->L('common/editpage').'|#|true;'.$this->L('common/addpage').'|'.url(C('CURRENT_MODULE'),'Edit','sortid='.$sortid);
        }
        // 需要检查标题
        if ($checktitle) {
            if (empty($aid)) {
                $cktitle = $this->check("title|1|".$this->L('check/title')."|1-255;title|3|".$this->L('check/title1')."|SELECT COUNT(`id`) FROM `".$model['maintable']."` WHERE `title`='#pro#';");
            } else {
                $cktitle = $this->check("title|1|".$this->L('check/title')."|1-255;title|3|".$this->L('check/title1')."|SELECT COUNT(`id`) FROM `".$model['maintable']."` WHERE `title`='#pro#' AND `id`<>'{$aid}';");
            }
        } else {
            $cktitle = $this->check("title|1|".$this->L('check/title')."|1-255");
        }
        // 路径检查
        if ($this->method() && !empty($title)) {
			if (strpos($path,$this->L('common/pinyin'))!==false) {
				$path = str_replace($this->L('common/pinyin'),pinyin($title),$path);
			}
        }
        if (empty($aid)) {
            $checkpath = $this->check("path|1|".$this->L('check/path')."|1-255;path|6|".$this->L('check/path1').";path|3|".$this->L('check/path2')."|SELECT COUNT(`id`) FROM `".$model['maintable']."` WHERE `path`='{$path}';");
        } else {
            $checkpath = $this->check("path|1|".$this->L('check/path')."|1-255;path|6|".$this->L('check/path1').";path|3|".$this->L('check/path2')."|SELECT COUNT(`id`) FROM `".$model['maintable']."` WHERE `path`='{$path}' AND `id`<>'{$aid}';");
        }

        $this->validate(array(
            'title' => $cktitle,
            'path'  => $checkpath,
            'date'  => $this->check("date|0|".$this->L('check/date').";date|validate|".$this->L('check/date1')."|8"),
            'keywords' => !empty($keywords) ? $this->check("keywords|1|".$this->L('check/keywords')."|1-250") : null,
            'description' => !empty($description) ? $this->check("description|1|".$this->L('check/description')."|1-250") : null,
        ));
        
        $maxid = $db->max('id',$model['maintable']);

        $label = O('Label');
        $label->create("SELECT * FROM `#@_archives_fields` WHERE `modelid` = ? ORDER BY `fieldorder` ASC, `fieldid` ASC;",$model['modelid']);
        $formData  = array(); $fieldData = array(); $vsetimg = false; $downPic = null; $isEditor = true;
        while ($data = $label->result()) {
            $fieldData[$data['fieldename']] = $data;
            $formData[$data['fieldename']]  = isset($_POST[$data['fieldename']]) ? $_POST[$data['fieldename']] : null;
            if (is_array($formData[$data['fieldename']])) {
                $formData[$data['fieldename']] = implode(',',$formData[$data['fieldename']]);
            }
            // 只对第一个editor进行处理，其他的不进行处理
            if ($data['inputtype']=='editor' && $isEditor) {
                // 全部验证成功，进行抓图处理
                if ($this->method() && $this->validate()) {
                    // 远程抓图
                    if ($snapimg) {
                        $formData[$data['fieldename']] = snapImg($formData[$data['fieldename']]);
                    }
                    
                    // 抓取第一张图片，作为缩略图
                    if ($setimg) {
                        if (preg_match('/<img.[^>]*src="(.[^>]+?)".[^>]*\/>/i',$formData[$data['fieldename']],$imgInfo) && empty($downPic)) {
                            $downPic = replace('/'.preg_quote(C('SITE_BASE'),'/').'/i','',downPic($imgInfo[1]),1);
                        }
                    }
                    // 自动截取简述
                    $content = clearHTML($formData[$data['fieldename']]);
                    if (empty($description)) {
                        $description = lefte($content,200);
                    }
                }
                if (!$vsetimg) {
                    $vsetimg = true;
                }
                $isEditor = false;
            }
        }
        if ($this->method()) {
            if ($this->validate()) {
                if (!empty($downPic)) { $img = $downPic; }
                if (strpos($path,'[MD5]')!==false) {
                    $path = str_replace('[MD5]',md5(salt(10).$maxid),$path);
                }
                if (empty($keywords)) {
                    $keywords = $this->keys($title);
                } else {
                    $keywords = $this->keys(null,$keywords);
                }
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
                        'date'    => (int)strtotime($date),
                        'keywords'=> (string)$keywords,
                        'description' => (string)$description,
                    );
                    $db->insert($model['maintable'],$row);
                    $aid = $db->lastInsertId();
                    $addrows = $formData; $addrows['aid'] = $aid;
                    $db->insert($model['addtable'],$addrows);
                } else { // update
                    $set = array(
                        'sortid'  => (int)$sortid,
                        'title'   => (string)$title,
                        'show'    => (int)$show,
                        'commend' => (int)$commend,
                        'top'     => (int)$top,
                        'img'     => (string)$img,
                        'path'    => (string)$path,
                        'date'    => (int)strtotime($date),
                        'keywords'=> (string)$keywords,
                        'description' => (string)$description,
                    );
                    $where = $db->quoteInto('`id` = ?',$aid);
                    $db->update($model['maintable'],$set,$where);
                    if (!empty($formData)) {
                        $num = $db->count("SELECT * FROM `".$model['addtable']."` WHERE `aid` = '{$aid}';");
                        if ($num>0) {
                            $where = $db->quoteInto('`aid` = ?',$aid);
                            $db->update($model['addtable'],$formData,$where);    
                        } else {
                            $addrows = $formData; $addrows['aid'] = $aid;
                            $db->insert($model['addtable'],$addrows);    
                        }
                    }
                }
                // 生成页面
                Archives::viewArchive($sortid,$aid);
                // 生成RSS
                Archives::createRss();
                // 更新列表，自动添加一个更新loading到toolbar
                if ($upsort && !C('SITE_MODE')) { exeloading("createsort_{$sortid}",url(C('CURRENT_MODULE'),'loading',"submit=createsort&lists={$sortid}"),$model['sortname']); }
                // 自动更新网站首页
                if ($uphome && class_exists('Onepage') && !C('SITE_MODE')) { Onepage::updateIndex(); }
                // 进行blog ping
                if (M($CURRENT_MODULE,'ARCHIVES_BLOG_PING')) {
                    import("system.downloader"); $url = 'http://'.$_SERVER['HTTP_HOST'].C('SITE_BASE');
                    $d = new DownLoader('http://blogsearch.google.com/ping?url='.rawurlencode($url.M($CURRENT_MODULE,'ARCHIVES_RSS_FILE')).'&btnG=Submit+Blog&hl=en');
                    $d->send(); unset($d);
                }
                redirect(url(C('CURRENT_MODULE'),'List','sortid='.$sortid));return true;
            }
        } else {
            if (!empty($aid)) {
                $res   = $db->query("SELECT * FROM `".$model['maintable']."` WHERE `id` = ?;",$aid);
                if ($data = $db->fetch($res)) {
                    $sortid  = $data['sortid'];
                    $title   = $data['title'];
                    $show    = $data['show'];
                    $commend = $data['commend'];
                    $top     = $data['top'];
                    $img     = $data['img'];
                    $path    = $data['path'];
                    $date    = $data['date'];
                    $keywords = $data['keywords'];
                    $description = $data['description'];
                    $formData = Archives::getData($aid,$model['addtable']);
                } else {
                    throwError(L('error/invalid'));
                }
                $maxid = $aid;
            } else {
                $show    = M($CURRENT_MODULE,'ARCHIVES_ADD_SHOW');
                $commend = M($CURRENT_MODULE,'ARCHIVES_ADD_COMMEND');
                $top     = M($CURRENT_MODULE,'ARCHIVES_ADD_TOP');
            }
            $snapimg = M($CURRENT_MODULE,'ARCHIVES_ADD_SNAPIMG');
            $upsort  = M($CURRENT_MODULE,'ARCHIVES_ADD_UPSORT');
            $uphome  = M($CURRENT_MODULE,'ARCHIVES_ADD_UPHOME');
            $checktitle = M($CURRENT_MODULE,'ARCHIVES_ADD_CHECKTITLE');
        }

        while (list($name,$data) = each($fieldData)) {
            $label->p = '<p><label>'.$data['fieldname'].'</label>'.$label->tag($data,$formData[$name]).'</p>';
        }
        $this->outHTML = $label->fetch;
        
        $defaultPath = $create_path;

        switch (M($CURRENT_MODULE,'ARCHIVES_CREATE_FILEMODE')) {
            case 'ID': 
                $defaultPath.= $maxid.C('HTML_URL_SUFFIX');
                break;
            case 'DateID': 
                $defaultPath.= date('Y/m/d/',$date).$maxid;
                break;
            case 'PinYin': 
                $defaultPath.= $this->L('common/pinyin').C('HTML_URL_SUFFIX');
                break;
            case 'MD5': 
                $defaultPath.= '[MD5]'.C('HTML_URL_SUFFIX');
                break;
            default:
                $defaultPath.= M($CURRENT_MODULE,'ARCHIVES_CREATE_FILE_CUSTOM');
                $defaultPath = str_replace('{id}',$maxid,strtolower($defaultPath));
                $defaultPath = str_replace('{y}',date('Y',$date),$defaultPath);
                $defaultPath = str_replace('{m}',date('m',$date),$defaultPath);
                $defaultPath = str_replace('{d}',date('d',$date),$defaultPath);
                $defaultPath = str_replace('{pinyin}',$this->L('common/pinyin'),$defaultPath);
                $defaultPath = str_replace('{md5}','[MD5]',$defaultPath);
                break;
        }

        $this->assign(array(
            'aid'    => $aid,
            'sortid' => $sortid,
            'title'  => htmlencode($title),
            'img'    => $img,
            'setimg' => $vsetimg,
            'path'   => empty($aid) && empty($path) ? $defaultPath :$path,
            'date'   => date('Y-m-d H:i:s',(empty($date) ? now() : (!is_numeric($date) ? strtotime($date) : $date))),
            'show'   => !empty($show) ? ' checked="checked"' : null,
            'top'    => !empty($top) ? ' checked="checked"' : null,
            'snapimg' => !empty($snapimg) ? ' checked="checked"' : null,
            'upsort'  => !empty($upsort) ? ' checked="checked"' : null,
            'uphome'  => !empty($uphome) ? ' checked="checked"' : null,
            'commend' => !empty($commend) ? ' checked="checked"' : null,
			'upath'   => C('UPFILE_PATH'),
            'isEditor'=> empty($aid)? false : true,
            'menu'    => $menu,
            'keywords'=> htmlencode($keywords),
            'description' => htmlencode($description),
            'checktitle'  => !empty($checktitle) ? ' checked="checked"' : null,
            'pathtype_id' => $maxid.C('HTML_URL_SUFFIX'),
			'create_path' => $create_path,
			'pathtype_date' => date('Y/m/d/',$date).$maxid,
        ));
        $this->display('edit.php');
    }
    // _insert *** *** www.LazyCMS.net *** ***
    function _insert(){
        // 插入10W篇文章进行测试
        set_time_limit(0);
        $db = getConn();
        $keywords = "分析师,微软,收购";
        $description = '北京时间6月14日消息，据国外媒体报道，随着雅虎宣布同谷歌在搜索广告领域建立合作伙伴关系，微软为收购雅虎而付出的长达数月的努力彻底以失败告终。分析师预计，微软可能会转而收购时代华纳旗下互联网部门AOL。';
        $content = '<p>北京时间6月14日消息，据国外媒体报道，随着雅虎宣布同谷歌在搜索广告领域建立合作伙伴关系，微软为收购雅虎而付出的长达数月的努力彻底以失败告终。分析师预计，微软可能会转而收购时代华纳旗下互联网部门AOL。<br />
<br />
&nbsp;</p>
<p>雅虎周四宣布终止与微软之间的所有谈判，并同谷歌签署了一份非排他性搜索广告合作协议。根据协议，雅虎将在主站及美国和加拿大合作网站的搜索结果中投放谷歌广告，雅虎有权决定谷歌广告的投放位置和方式。从一定程度上讲，雅虎选择同谷歌合作，意味着微软为收购雅虎所付出的努力彻底以失败告终。<br />
<br />
　　微软现在陷入了进退两难的境地。一方面，正是因为微软的推动，雅虎投向了谷歌一方；另一方面，微软斥巨资收购雅虎，从一个侧面承认了自己的互联网战略失败，只能通过收购保持竞争力。那么，在失去雅虎之后，微软又应当采取何种策略呢？TechTraderDaily分析师科里斯托弗&middot;马拉吉(Christopher Marangi)认为，微软应当转而收购AOL。<br />
<br />
　　马拉吉在投资者报告中预计，微软可能会转而收购AOL，因为只有AOL具有与雅虎相当的规模和价值。随着时代华纳CEO杰夫&middot;比克斯(Jeff Bewkes)宣布可能出售AOL，这家老牌互联网巨头再度成为了业界关注的焦点。马拉吉表示：&ldquo;AOL也面临着挑战，但再也没有其它互联网资产可以达到AOL的规模。通过收购AOL，微软可以提升搜索份额，增加页面访问量，以及获得平台 A的第三方广告网络。&rdquo;不计入互联网接入业务，他预计AOL价值120亿美元。<br />
<br />
　　放弃收购雅虎之后，微软拥有500亿美元的可处置资金，一笔大规模收购呼之欲出。此前有消息称，微软可能会收购美国第二大社交网站Facebook，但考虑到这家公司还没有可支撑的商业模式，微软斥巨资全面收购的可能性并不大。综合各方面因素考虑，微软更有可能将AOL作为潜在收购目标。<br />
&nbsp;</p>';
        for ($i=0; $i<10000; $i++) {
            $sortid = mt_rand(3,7);
            $title = "第{$i}条新闻，分析师预计微软将转而收购AOL";
            $maxid = $db->max('id','#@_archives');
            $path = md5(salt(10).$maxid).C('HTML_URL_SUFFIX');
            $row = array(
                'order'   => (int)$maxid,
                'sortid'  => (int)$sortid,
                'title'   => (string)$title,
                'show'    => 1,
                'path'    => (string)$path,
                'date'    => (int)time(),
                'keywords'=> (string)$keywords,
                'description' => (string)$description,
            );
            $db->insert('#@_archives',$row);
            $aid = $db->lastInsertId();
            $formData = array(
                'aid'=>$aid,
                'from'=>'互联网',
                'author'=>'未知',
                'content'=>$content,
            );
            $db->insert('#@_archives_model_article',$formData);
        }
        echo 'ok!!!';
    }
    // _sortset *** *** www.LazyCMS.net *** ***
    function _set(){
        clearCache();
        $this->checker(C('CURRENT_MODULE'),true);
        $db     = getConn();
        $submit = isset($_POST['submit']) ? $_POST['submit'] : null;
        $lists  = isset($_POST['lists']) ? $_POST['lists'] : null;
        $sortid = isset($_REQUEST['sortid']) ? (int)$_REQUEST['sortid'] : null;
        if (instr('delete,create',$submit) && empty($lists)) {
            $this->poping($this->L('pop/select'),0);
        }
        $model = Archives::getModel($sortid);
        switch($submit){
            case 'delete' :
                $res = $db->query("SELECT `path` FROM `".$model['maintable']."` WHERE `id` IN({$lists})");
                while ($data = $db->fetch($res,0)){
                    Archives::delArchive($data[0]);
                }
                $db->exec("DELETE FROM `".$model['maintable']."` WHERE `id` IN({$lists});");
                $db->exec("DELETE FROM `".$model['addtable']."` WHERE `aid` IN({$lists});");
                $this->poping($this->L('pop/deleteok'),1);
                break;
            case 'create' :
                $sortname = $db->result("SELECT `sortname` FROM `#@_archives_sort` WHERE `sortid`='{$sortid}'");
                $js = '<script type="text/javascript">';
                $js.= "loading('{$submit}_{$sortid}','".url(C('CURRENT_MODULE'),'loading',"submit={$submit}&lists={$lists}&sortid={$sortid}")."','{$sortname}');";
                $js.= '</script>';
                $this->poping($this->L('pop/loading').$js,0);
                break;
            case 'updown' :
                $updown = isset($_POST['updown']) ? (string)$_POST['updown'] : null;
                $num    = isset($_POST['num']) ? (int)$_POST['num'] : null;
                $upid   = isset($_POST['upid']) ? (int)$_POST['upid'] : null;
                $model  = Archives::getModel($upid);
                $this->order($model['maintable'].",id,order","{$lists},{$updown},{$num}","`sortid`='{$upid}'");
                break;
            default :
                $this->poping(L('error/invalid'),0);
                break;
        }
    }
    // _models *** *** www.LazyCMS.net *** ***
    function _models(){
        $this->checker(C('CURRENT_MODULE'));
        $db = getConn();
        $dp = O('Record');
        $dp->action = url(C('CURRENT_MODULE'),'ModelSet');
        $dp->result = $db->query("SELECT * FROM `#@_archives_model` WHERE 1 ORDER BY `modelid` ASC;");
        $dp->length = $db->count($dp->result);
        $dp->but = $dp->button();
        $dp->td  = "cklist(K[0]) + K[0] + ') <a href=\"".url(C('CURRENT_MODULE'),'ModelFields','modelid=$',"' + K[0] + '")."\">' + K[1] + '</a>'";
        $dp->td  = 'K[2]';
        $dp->td  = 'K[3]';
        $dp->td  = "state(K[4],'".url(C('CURRENT_MODULE'),'ModelState','modelid=$&state=1',"' + K[0] + '")."','".url(C('CURRENT_MODULE'),'ModelState','modelid=$&state=0',"' + K[0] + '")."')";
        $dp->td  = "ico('edit','".url(C('CURRENT_MODULE'),'ModelEdit','modelid=$',"' + K[0] + '")."') + ico('export','".url(C('CURRENT_MODULE'),'ModelExport','modelid=$',"' + K[0] + '")."') + ico('fields','".url(C('CURRENT_MODULE'),'ModelFields','modelid=$',"' + K[0] + '")."')";
        $dp->open();
        $dp->thead = '<tr><th>'.$this->L('models/list/id').') '.$this->L('models/list/name').'</th><th>'.$this->L('models/list/ename').'</th><th>'.$this->L('models/list/addtable').'</th><th>'.$this->L('models/list/state').'</th><th>'.$this->L('models/list/action').'</th></tr>';
        
        while ($data = $dp->result()) {
            $dp->tbody = "ll(".$data['modelid'].",'".t2js(htmlencode($data['modelname']))."','".t2js(htmlencode($data['modelename']))."','".htmlencode(str_replace('#@_',C('DSN_PREFIX'),$data['addtable']))."',".$data['modelstate'].");";
        }
        $dp->close();

        $this->outHTML = $dp->fetch;


        $this->assign('menu',$this->L('models/@title').'|#|true;'.$this->L('models/add').'|'.url(C('CURRENT_MODULE'),'ModelEdit').';'.$this->L('models/leadin').'|'.url(C('CURRENT_MODULE'),'ModelLeadIn'));
        $this->display('__public.php');
    }
    // _modelset *** *** www.LazyCMS.net *** ***
    function _modelset(){
        clearCache();
        $this->checker(C('CURRENT_MODULE'),true);
        $db     = getConn();
        $submit = isset($_POST['submit']) ? $_POST['submit'] : null;
        switch($submit){
            case 'delete' :
                $lists = isset($_POST['lists']) ? $_POST['lists'] : null;
                if (empty($lists)) {
                    $this->poping($this->L('models/pop/select'),0);
                }
                $res = $db->query("SELECT `modelid`,`maintable`,`addtable` FROM `#@_archives_model` WHERE `modelid` IN({$lists})");
                while ($data = $db->fetch($res,0)){
                    $db->exec("DELETE FROM `#@_archives_model` WHERE `modelid`=?;",$data[0]);
                    if (strtolower($data[1])!='#@_archives') {
                        $num = $db->count("SELECT * FROM `#@_archives_model` WHERE `maintable`=".$db->quote($data[1]));
                        if ($num==0) {
                            $db->exec("DROP TABLE IF EXISTS `".$data[1]."`;");
                        }
                    }
                    $db->exec("DROP TABLE IF EXISTS `".$data[2]."`;");
                }
                $db->exec("DELETE FROM `#@_archives_fields` WHERE `modelid` IN({$lists});");
                $this->poping($this->L('models/pop/deleteok'),1);
                break;
            default :
                $this->poping(L('error/invalid'),0);
                break;
        }
    }
    // _modelstate *** *** www.LazyCMS.net *** ***
    function _modelstate(){
        $this->checker(C('CURRENT_MODULE'));
        $modelid = isset($_GET['modelid']) ? (int)$_GET['modelid'] : null;
        $state   = isset($_GET['state']) ? (int)$_GET['state'] : null;
        $db  = getConn();
        $set = array(
            'modelstate' => $state,
        );
        $where = $db->quoteInto('`modelid` = ?',$modelid);
        $db->update('#@_archives_model',$set,$where);
        redirect($_SERVER['HTTP_REFERER']);
    }
    // _modelexport *** *** www.LazyCMS.net *** ***
    function _modelexport(){
        clearCache();
        $this->checker(C('CURRENT_MODULE'));
        $db = getConn();
        $modelid = isset($_GET['modelid']) ? (int)$_GET['modelid'] : null;
        
        $XML = array();
        $res = $db->query("SELECT * FROM `#@_archives_model` WHERE `modelid`= ? ;",$modelid);
        if ($data = $db->fetch($res)) {
            unset($data['modelid']);
            $modelName = $data['modelename'];
            $data['maintable'] = str_replace('#@_','',$data['maintable']);
            $data['addtable']  = str_replace('#@_','',$data['addtable']);
            $XML['model']      = $data;
        } else {
            $modelName = 'Error';
        }
        $fields = array();
        $res = $db->query("SELECT * FROM `#@_archives_fields` WHERE `modelid`= ? ORDER BY `fieldorder` ASC,`fieldid` ASC;",$modelid);
        while ($data = $db->fetch($res)){
            unset($data['fieldid'],$data['modelid'],$data['fieldorder']);
            $fields[] = $data;
        }
        $XML['fields'] = $fields;
        ob_start();
        header("Content-type: application/octet-stream; charset=utf-8");
        header("Content-Disposition: attachment; filename=LazyCMS_".C('CURRENT_MODULE').'_'.$modelName.".xml");
        header("Pragma: no-cache");
        header("Expires: 0");
        echo xmlcode($XML);
        ob_flush();
    }
    // _modelleadin *** *** www.LazyCMS.net *** ***
    function _modelleadin(){
        $this->checker(C('CURRENT_MODULE'));
        $field = 'model';
        if ($this->method()) {
            $upload = O('UpLoadFile');
            $upload->allowExts = "xml";
            $upload->maxSize   = 500*1024;//500K
            $folder = LAZY_PATH.C('UPFILE_PATH');mkdirs($folder);
            if ($file = $upload->save($field,$folder.'/'.basename($_FILES[$field]['name']))) {
                $modelCode = loadFile($file['path']); @unlink($file['path']);
                if (!empty($modelCode)) {
                    Archives::installModel($modelCode);
                }
                redirect(url(C('CURRENT_MODULE'),'Models'));
            } else {
                $this->validate(array(
                    $field => $upload->getError(),
                ));
            }
        }

        $this->display('modelleadin.php');
    }
    // _modeledit *** *** www.LazyCMS.net *** ***
    function _modeledit(){
        $this->checker(C('CURRENT_MODULE'));
        $db      = getConn();
        $modelid = isset($_REQUEST['modelid']) ? (int)$_REQUEST['modelid'] : null;
        $sql     = "modelname,modelename,maintable";//2
        foreach (explode(',',$sql) as $val) {
            $data[]= isset($_POST[$val]) ? $_POST[$val] : null;
        }
        if (empty($modelid)) {
            $menu = $this->L('models/add').'|#|true';
        } else {
            $menu = $this->L('models/add').'|'.url(C('CURRENT_MODULE'),'ModelEdit').';'.$this->L('models/edit').'|#|true';
        }
        // 验证表是否可以作为索引表
        $isValdate = 'true';
        if ($this->method() && strtolower($data[2])!='#@_archives') {
            // 表不存在的时候，自动拷贝 #@_archives 创建新表
            if (!$db->isTable($data[2])) {
                $db->copy('#@_archives',$data[2]);
            }
            $fields = array();
            $res = mysql_list_fields($db->getDataBase(),str_replace('#@_',C('DSN_PREFIX'),$data[2]),$db->getConnect());
            $col = mysql_num_fields($res);
            for ($i = 0; $i < $col; $i++) {
                $fields[0][] = mysql_field_name($res, $i);
            }
            $res = mysql_list_fields($db->getDataBase(),C('DSN_PREFIX').'archives',$db->getConnect());
            $col = mysql_num_fields($res);
            for ($i = 0; $i < $col; $i++) {
                $fields[1][] = mysql_field_name($res, $i);
            }
            if ($fields[1]!==$fields[0]) {
                $isValdate = 'false';
            }    
        }
        
        $this->validate(array(
            'modelname'  => $this->check('modelname|1|'.$this->L('models/check/name').'|1-50'),
            'maintable'  => $this->check('maintable|5|'.$this->L('models/check/table',array('table'=>str_replace('#@_',C('DSN_PREFIX'),$data[2]))).'|'.$isValdate),
            'modelename' => $this->check('modelename|1|'.$this->L('models/check/ename').'|1-50;modelename|validate|'.$this->L('models/check/ename1').'|^[A-Za-z0-9\_]+$'),
        ));

        if ($this->method()) {
            if ($this->validate()) {
                if(empty($modelid)){//insert
                    $row = array(
                        'modelname'  => $data[0],
                        'modelename' => $data[1],
                        'maintable'  => $data[2],
                        'addtable'   => '#@_archives_model_'.$data[1],
                    );
                    $db->insert('#@_archives_model',$row);
                    // 删除已存在的表
                    $db->exec("DROP TABLE IF EXISTS `#@_archives_model_".$data[1]."`;");
                    // 创建新表
                    $db->exec("CREATE TABLE IF NOT EXISTS `#@_archives_model_".$data[1]."` (
                                `aid` int(11) NOT NULL,
                                PRIMARY KEY (`aid`)
                               ) ENGINE=MyISAM DEFAULT CHARSET=#~lang~#;");
                } else {//update
                    $set = array(
                        'modelname'  => $data[0],
                        'maintable'  => $data[2],
                    );
                    $where = $db->quoteInto('`modelid` = ?',$modelid);
                    $db->update('#@_archives_model',$set,$where);
                }
                redirect(url(C('CURRENT_MODULE'),'Models'));
            }
        } else {
            if (!empty($modelid)) {
                $res   = $db->query("SELECT {$sql} FROM `#@_archives_model` WHERE `modelid` = ?;",$modelid);
                if (!$data = $db->fetch($res,0)) {
                    throwError(L('error/invalid'));
                }    
            }
        }


        $this->assign(array(
            'modelid'    => $modelid,
            'modelname'  => htmlencode($data[0]),
            'modelename' => htmlencode($data[1]),
            'maintable'  => !empty($data[2]) ? $data[2] : '#@_archives',
            'menu'       => $menu,
            'readonly'   => !empty($modelid) ? ' readonly="true"' : null,
        ));
        $this->display('modeledit.php');
    }
    // _modelfields *** *** www.LazyCMS.net *** ***
    function _modelfields(){
        $this->checker(C('CURRENT_MODULE'));
        $db      = getConn();
        $modelid = isset($_REQUEST['modelid']) ? (int)$_REQUEST['modelid'] : null;
        $dp = O('Record');
        $dp->action = url(C('CURRENT_MODULE'),'ModelFieldSet','modelid='.$modelid);
        $dp->result = $db->query("SELECT * FROM `#@_archives_fields` WHERE `modelid`= ? ORDER BY `fieldorder` ASC, `fieldid` ASC;",$modelid);
        $dp->length = $db->count($dp->result);
        $dp->but = $dp->button();
        $dp->td  = "cklist(K[0]) + '<a href=\"".url(C('CURRENT_MODULE'),'ModelFieldsEdit','modelid=:modelid&fieldid=:fieldid',array('modelid'=>"'+K[7]+'",'fieldid'=>"'+K[0]+'"))."\">' + K[0] + ') ' + K[1] + '</a>'";
        $dp->td  = "K[2]";
        $dp->td  = "K[3] + (K[9]=='input' ? '(' + K[4] + ')' : '')";
        $dp->td  = "(K[5]=='' ? 'NULL' : K[5])";
        $dp->td  = "index(K[8],K[6],'".url(C('CURRENT_MODULE'),'ModelFieldIndex','modelid=:modelid&fieldid=:fieldid&index=0',array('modelid'=>"'+K[7]+'",'fieldid'=>"'+K[0]+'"))."','".url(C('CURRENT_MODULE'),'ModelFieldIndex','modelid=:modelid&fieldid=:fieldid&index=1',array('modelid'=>"'+K[7]+'",'fieldid'=>"'+K[0]+'"))."')";
        $dp->td  = "ico('edit','".url(C('CURRENT_MODULE'),'ModelFieldsEdit','modelid=:modelid&fieldid=:fieldid',array('modelid'=>"'+K[7]+'",'fieldid'=>"'+K[0]+'"))."') + updown('up',K[0]) + updown('down',K[0])";
        $dp->open();
        $dp->thead = '<tr><th>'.$this->L('models/field/list/id').') '.$this->L('models/field/list/name').'</th><th>'.$this->L('models/field/list/ename').'</th><th>'.$this->L('models/field/list/type').'</th><th>'.$this->L('models/field/list/default').'</th><th>'.$this->L('models/field/list/key').'</th><th>'.$this->L('models/field/list/action').'</th></tr>';
        while ($data = $dp->result()) {
            $dp->tbody = "ll(".$data['fieldid'].",'".t2js(htmlencode($data['fieldname']))."','".t2js(htmlencode($data['fieldename']))."','".$this->L('models/field/type/'.$data['inputtype'])."','".t2js(htmlencode($data['fieldlength']))."','".t2js(htmlencode($data['fieldefault']))."',".$data['fieldindex'].",".$data['modelid'].",".(int)instr('text,mediumtext',$data['fieldtype']).",'".$data['inputtype']."');";
        }
        $dp->close();
        $this->outHTML = $dp->fetch;

        $this->assign('menu',$this->L('models/@title').'|'.url(C('CURRENT_MODULE'),'Models').';'.$this->L('models/add').'|'.url(C('CURRENT_MODULE'),'ModelEdit').';'.$this->L('models/field/@title').'|#|true;'.$this->L('models/field/add').'|'.url(C('CURRENT_MODULE'),'ModelFieldsEdit','modelid='.$modelid));
        $this->display('__public.php');
    }
    // _modelfieldindex *** *** www.LazyCMS.net *** ***
    function _modelfieldindex(){
        $this->checker(C('CURRENT_MODULE'));
        $fieldid = isset($_GET['fieldid']) ? (int)$_GET['fieldid'] : null;
        $modelid = isset($_GET['modelid']) ? (int)$_GET['modelid'] : null;
        $index   = isset($_GET['index']) ? (string)$_GET['index'] : null;
        $db    = getConn();
        try{
            $where     = $db->quoteInto('`modelid` = :modelid AND `fieldid`= :fieldid ',array('modelid'=>$modelid,'fieldid'=>$fieldid));
            $addtable  = $db->result("SELECT `addtable` FROM `#@_archives_model` WHERE `modelid`='{$modelid}';");
            $fieldname = $db->result("SELECT `fieldename` FROM `#@_archives_fields` WHERE {$where};");
            // 修改为不索引
            if (empty($index)){
                $db->exec("ALTER TABLE `{$addtable}` DROP INDEX `{$fieldname}`;");
            } else {
                $db->exec("ALTER TABLE `{$addtable}` ADD INDEX ( `{$fieldname}` ) ;");
            }
            $set = array(
                'fieldindex' => $index,
            );
            $db->update('#@_archives_fields',$set,$where);
        } catch(Error $err){}
        redirect($_SERVER['HTTP_REFERER']);
    }
    // _modelfieldset *** *** www.LazyCMS.net *** ***
    function _modelfieldset(){
        clearCache();
        $this->checker(C('CURRENT_MODULE'),true);
        $db      = getConn();
        $modelid = isset($_REQUEST['modelid']) ? (int)$_REQUEST['modelid'] : null;
        $submit  = isset($_POST['submit']) ? $_POST['submit'] : null;
        $lists   = isset($_POST['lists']) ? $_POST['lists'] : null;
        switch($submit){
            case 'delete' :
                if (empty($lists)) {
                    $this->poping($this->L('models/pop/select'),0);
                }
                // 取得附加表
                $addtable = $db->result("SELECT `addtable` FROM `#@_archives_model` WHERE `modelid`='{$modelid}';");
                // 组合删除数据库字段的SQL语句
                $DelSQL = "ALTER TABLE `{$addtable}` ";
                $res = $db->query("SELECT `fieldename` FROM `#@_archives_fields` WHERE `modelid`= ? AND `fieldid` IN({$lists});",$modelid);
                while ($data = $db->fetch($res,0)){
                    $DelSQL.= " DROP `".$data[0]."`,";
                }
                $DelSQL = rtrim($DelSQL,',').";";
                try { // 屏蔽所有错误
                    // 执行删除字段操作
                    $db->exec($DelSQL);
                    $db->exec("DELETE FROM `#@_archives_fields` WHERE `modelid`= ? AND `fieldid` IN({$lists});",$modelid);
                    $this->poping($this->L('models/pop/deletefieldok'),1);
                } catch (Error $err) {
                    $db->exec("DELETE FROM `#@_archives_fields` WHERE `modelid`= ? AND `fieldid` IN({$lists});",$modelid);
                    $this->poping($this->L('models/pop/deletefielderr'),1);
                }
                break;
            case 'updown' :
                $updown = isset($_POST['updown']) ? (string)$_POST['updown'] : null;
                $num    = isset($_POST['num']) ? (int)$_POST['num'] : null;
                $updown = $updown=='down' ? 'up' : 'down';
                $this->order("#@_archives_fields,fieldid,fieldorder","{$lists},{$updown},{$num}","`modelid`='{$modelid}'");
                break;
            default :
                $this->poping(L('error/invalid'),0);
                break;
        }
    }
    // _modelfieldsedit *** *** www.LazyCMS.net *** ***
    function _modelfieldsedit(){
        $this->checker(C('CURRENT_MODULE'));
        $db      = getConn();
        $modelid = isset($_REQUEST['modelid']) ? (int)$_REQUEST['modelid'] : null;
        $fieldid = isset($_REQUEST['fieldid']) ? (int)$_REQUEST['fieldid'] : null;
        $sql     = "fieldname,fieldename,fieldtype,fieldlength,fieldefault,fieldindex,inputtype,fieldvalue";//7
        foreach (explode(',',$sql) as $val) {
            $data[] = isset($_POST[$val]) ? $_POST[$val] : null;
        }
        $data[8] = isset($_POST['oldfieldename']) ? $_POST['oldfieldename'] : null;
        if (empty($fieldid)) {
            $menu = $this->L('models/field/add').'|#|true';
        } else {
            $menu = $this->L('models/field/add').'|'.url(C('CURRENT_MODULE'),'ModelFieldsEdit','modelid='.$modelid).';'.$this->L('models/field/edit').'|#|true';
        }
        $this->validate(array(
            'fieldname'  => $this->check('fieldname|1|'.$this->L('models/field/check/name').'|1-50'),
            'fieldename' => $this->check('fieldename|1|'.$this->L('models/field/check/ename').'|1-50;fieldename|validate|'.$this->L('models/field/check/ename1').'|^[A-Za-z0-9\_]+$'),
            'fieldlength'=> instr('input',$data[6]) ? $this->check('fieldlength|1|'.$this->L('models/field/check/length').'|1-255;fieldlength|validate|'.$this->L('models/field/check/length1').'|2') : null,
            'fieldvalue' => instr('radio,checkbox,select',$data[6]) ? $this->check('fieldvalue|0|'.$this->L('models/field/check/value')) : null,
        ));
        if ($this->method()) {
            if ($this->validate()) {
                // 取得附加表
                $addtable = $db->result("SELECT `addtable` FROM `#@_archives_model` WHERE `modelid`='{$modelid}';");
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
                        'fieldorder'  => $db->max('fieldid','#@_archives_fields'),
                        'modelid'     => $modelid,
                        'fieldname'   => $data[0],
                        'fieldename'  => $data[1],
                        'fieldtype'   => $data[2],
                        'fieldlength' => $data[3],
                        'fieldefault' => $data[4],
                        'fieldindex'  => $data[5],
                        'inputtype'   => $data[6],
                        'fieldvalue'  => $data[7],
                    );
                    $db->insert('#@_archives_fields',$row);
                    // 向附加表添加对应字段
                    $SQL     = "ALTER TABLE `{$addtable}` ADD ";
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
                    $where = $db->quoteInto('`modelid` = :modelid AND `fieldid`= :fieldid ',array('modelid'=>$modelid,'fieldid'=>$fieldid));
                    try{ // 删除索引，并修改字段为不索引
                        if (instr('text,mediumtext',$data[2])) {
                            $db->exec("ALTER TABLE `{$addtable}` DROP INDEX `".$data[1]."`;");
                            $set = array_merge($set,array(
                                'fieldindex' => '0'
                            ));
                        }
                    } catch(Error $err){}
                    $db->update('#@_archives_fields',$set,$where);
                    $db->exec("ALTER TABLE `{$addtable}` CHANGE `".$data[8]."` `".$data[1]."` ".$data[2].$length.$default.";");
                }
                redirect(url(C('CURRENT_MODULE'),'ModelFields',"modelid={$modelid}"));
            }
        } else {
            if (!empty($modelid) && !empty($fieldid)) {
                $res   = $db->query("SELECT {$sql} FROM `#@_archives_fields` WHERE `modelid` = :modelid AND `fieldid`= :fieldid;",array('modelid'=>$modelid,'fieldid'=>$fieldid));
                if (!$data = $db->fetch($res,0)) {
                    throwError(L('error/invalid'));
                }
            }
        }

        $this->assign(array(
            'fieldid'     => $fieldid,
            'modelid'     => $modelid,
            'fieldname'   => htmlencode($data[0]),
            'fieldename'  => htmlencode($data[1]),
            'fieldtype'   => htmlencode($data[2]),
            'fieldlength' => htmlencode($data[3]),
            'fieldefault' => htmlencode($data[4]),
            'inputtype'   => htmlencode($data[6]),
            'fieldvalue'  => htmlencode($data[7]),
            'menu'        => $menu,
            'fieldindex'  => !empty($data[5]) ? ' checked="true"' : null,
            'readonly'    => (!empty($modelid) && !empty($data[5])) ? ' readonly="true"' : null,
        ));
        $this->display('modelfieldsedit.php');
    }
    // _config *** *** www.LazyCMS.net *** ***
    function _config(){
        $this->checker(C('CURRENT_MODULE')); 
        $this->config(C('CURRENT_MODULE'));
        $this->display('config.php');
    }
    // _search *** *** www.LazyCMS.net *** ***
    function _search(){
        @set_time_limit(0);
		$db  = getConn(); $strSQL = null; 
		$tag = O('Tags'); $inSQL = null; $tmpList = null;
		$query  = isset($_REQUEST['query']) ? $_REQUEST['query'] : null;
		$tags   = isset($_REQUEST['tags']) ? $_REQUEST['tags'] : null;
		$sortid = isset($_REQUEST['sortid']) ? (int)$_REQUEST['sortid'] : 0;
		$page   = isset($_REQUEST['page']) ? (int)$_REQUEST['page'] : 1;
		if (!empty($query)) {
			if (is_utf8($query)==false) {
				if (function_exists('iconv')) {
					$query = iconv('gb2312','utf-8',$query);
				} elseif (function_exists('mb_convert_encoding')) {
					$query = mb_convert_encoding($query,'utf-8','gb2312');
				}
			}
			$query = rawurldecode($query);
		}
		if (!empty($tags)) {
			if (is_utf8($tags)==false) {
				if (function_exists('iconv')) {
					$tags = iconv('gb2312','utf-8',$tags);
				} elseif (function_exists('mb_convert_encoding')) {
					$tags = mb_convert_encoding($tags,'utf-8','gb2312');
				}
			}
			$tags = rawurldecode($tags);
		}
        $keyword = empty($tags) ? $query : $tags;
        // 缓存公用不变内容
        $cachePath = LAZY_PATH.C('HTML_CACHE_PATH').'/Archive_Search/'; mkdirs($cachePath);
        $cachePath.= md5($keyword.$sortid).".php";
        if (is_file($cachePath)) {
            extract(include($cachePath));
        } else {
            if (!empty($sortid)) { $sortids = Archives::getSubSortIds($sortid); $inSQL.= " And `a`.`sortid` IN({$sortids})"; }
            if (!empty($query)){ $inSQL.= " And (binary ucase(`a`.`title`) LIKE ucase('%{$query}%') OR binary ucase(`a`.`keywords`) LIKE ucase('%{$query}%') OR binary ucase(`a`.`description`) LIKE ucase('%{$query}%'))"; }
            if (!empty($tags)) { $inSQL.= " And (binary ucase(`a`.`keywords`) LIKE ucase('%{$tags}%'))"; }
            $res = $db->query("SELECT DISTINCT `maintable` FROM `#@_archives_model` GROUP BY `maintable`;");
            while ($data = $db->fetch($res,0)) {
                if (empty($strSQL)) {
                    $strSQL.= "SELECT `a`.*,`b`.`sortname`,`b`.`sortpath` FROM `".$data[0]."` AS `a` LEFT JOIN `#@_archives_sort` AS `b` ON `a`.`sortid`=`b`.`sortid` WHERE 1 {$inSQL}";
                } else {
                    $strSQL.= " UNION SELECT `a`.*,`b`.`sortname`,`b`.`sortpath` FROM `".$data[0]."` AS `a` LEFT JOIN `#@_archives_sort` AS `b` ON `a`.`sortid`=`b`.`sortid` WHERE 1 {$inSQL}";
                }
            }
            $strSQL.= " ORDER BY `a`.`date` DESC";
            $sotemplate1 = M(C('CURRENT_MODULE'),'ARCHIVES_TEMPLATE');
            $sotemplate2 = C('TEMPLATE_PATH').'/inside/search/'.C('TEMPLATE_DEF');
            $HTML = $tag->read($sotemplate1,$sotemplate2);
            $HTMList = $tag->getList($HTML,'search',1);
            $jsHTML  = $tag->getLabel($HTMList,0);
            $jsNumber= floor($tag->getLabel($HTMList,'number'));
            $zebra   = $tag->getLabel($HTMList,'zebra');
            $rand    = chr(3).salt(20).chr(2);//随机出来的替换参数
            $randpl  = chr(3).salt(16).chr(2);
            // 把 HTML 中的{lazy:...type=list/}标签替换为一个随机的标签；pagelist设置为一个随机标签
            $HTML = str_replace($HTMList,$rand,$HTML);
            // 替换模板中的标签
            $tag->clear();
            if (empty($keyword)) {
                $tag->value('title',encode(htmlencode($this->L('search/@title'))));
            } else {
                $tag->value('title',encode(htmlencode($this->L('search/title',array('keyword'=>$keyword)))));
                
            }
            $tag->value('query',encode(htmlencode($keyword)));
            $tag->value('pagelist',encode($randpl));
            $tag->value('sort',encode(Archives::__sort(0,0,0,$sortid)));
            $HTML = $tag->create($HTML,$tag->getValue());

            $totalRows  = $db->count($strSQL);
            $totalPages = ceil($totalRows/$jsNumber);
            $totalPages = ((int)$totalPages == 0) ? 1 : $totalPages;
            // 有记录，生成缓存文件
            if ((int)$totalRows > 0) {
                saveFile($cachePath,"<?php \nreturn ".var_export(array(
                    'strSQL'   => $strSQL,
                    'HTML'     => $HTML,
                    'HTMList'  => $HTMList,
                    'jsHTML'   => $jsHTML,
                    'jsNumber' => $jsNumber,
                    'zebra'    => $zebra,
                    'rand'     => $rand,
                    'randpl'     => $randpl,
                    'totalRows'  => $totalRows,
                    'totalPages' => $totalPages,
                    'sotemplate1'=> $sotemplate1,
                    'sotemplate2'=> $sotemplate2,
                ),true)."; \n?>");
            }
        }
        // 判断模板文件改变，就删除缓存
        if (is_file($cachePath)) {
            $template1 = filemtime(LAZY_PATH.$sotemplate1);
            $template2 = filemtime(LAZY_PATH.$sotemplate2);
            if ((int)filemtime($cachePath)<(int)$template1 || (int)filemtime($cachePath)<(int)$template2) {
                unlink($cachePath);
            }
        }
        if ((int)$page > (int)$totalPages) { $page = $totalPages; }
        $strSQL.= ' LIMIT '.$jsNumber.' OFFSET '.($page-1)*$jsNumber.';';
        // 有记录进行查询
        if ((int)$totalRows > 0 && !empty($keyword)) {
            $res = $db->query($strSQL);
            $i = 1;
            while ($data = $db->fetch($res)) {
                $_model = Archives::getModel($data['sortid']);
                $tag->clear();
                $tag->value('id',$data['id']);
                $tag->value('sortid',$data['sortid']);
                $tag->value('sortname',encode(htmlencode($_model['sortname'])));
                $tag->value('sortpath',encode(Archives::showSort($data['sortid'])));
                $tag->value('title',encode(htmlencode($data['title'])));
                $tag->value('path',encode(Archives::showArchive($data['id'],$_model)));
                $tag->value('image',encode($data['img']));
                $tag->value('date',$data['date']);
                $tag->value('hits',$data['hits']);
                $tag->value('keywords',encode(htmlencode($data['keywords'])));
                $tag->value('description',encode(htmlencode($data['description'])));
                $tag->value('zebra',($i % ($zebra+1)) ? 0 : 1);
                $tmpList.= $tag->createhtm($jsHTML,$tag->getValue());
                $i++;
            }
            if (!empty($query)) { $path = url('Archives','Search','query='.rawurlencode($query).'&page=$'); }
            if (!empty($tags)) { $path = url('Archives','Search','tags='.rawurlencode($tags).'&page=$'); }
            $outHTML = str_replace($rand,$tmpList,$HTML);
            $outHTML = str_replace($randpl,Archives::pagelist($path,$page,$totalPages,$totalRows,1),$outHTML);//'&page=$'
        } else {
            if ((int)$totalRows == 0) {
                $outHTML = str_replace($rand,$this->L('search/noresult',array('keyword'=>$keyword)),$HTML);
            } else {
                $outHTML = str_replace($rand,null,$HTML);
            }
            $outHTML = str_replace($randpl,null,$outHTML);
        }
        echo $outHTML;
    }
    // _hits *** *** www.LazyCMS.net *** ***
    function _hits(){
        clearCache();$db = getConn();
        $id = isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : null;
        $sortid = isset($_REQUEST['sortid']) ? (int)$_REQUEST['sortid'] : null;
        $model  = Archives::getModel($sortid);
        $db->exec("UPDATE `".$model['maintable']."` SET `hits` = `hits` + 1 WHERE `id` = ?;",$id);
        $res = $db->query("SELECT `hits` FROM `".$model['maintable']."` WHERE `id` = ?;",$id);
        if ($data = $db->fetch($res,0)) {
            echo $data[0];
        } else {
            echo '0';
        }
    }
    // _nextpage *** *** www.LazyCMS.net *** ***
    function _nextpage(){
        clearCache();$db = getConn();
        $id = isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : null;
        $sortid = isset($_REQUEST['sortid']) ? (int)$_REQUEST['sortid'] : null;
        $model  = Archives::getModel($sortid);
        $res = $db->query("SELECT * FROM `".$model['maintable']."` ".$db->quoteInto(' WHERE `id` = ?',$id));
        if (!$data = $db->fetch($res)) {
            echo L('error/invalid');
        }
        $res = $db->query("SELECT `title`,`path`,`id` FROM `".$model['maintable']."` WHERE `show`=1 AND `sortid`=:sortid AND `order`>:order ORDER BY `top` ASC,`order` ASC,`id` ASC LIMIT 0,1;",array('sortid'=>$model['sortid'],'order'=>$data['order']));
        if ($row = $db->fetch($res,0)) {
            if (!C('SITE_MODE')) { Archives::viewArchive($model['sortid'],$row[2]); }
            $I1 = '<a href="'.Archives::showArchive($row[2],$model).'">'.htmlencode($row[0]).'</a>';
        } else {
            $I1 = '<a href="'.Archives::showSort($model['sortid']).'">['.htmlencode($model['sortname']).']</a>';
        }
        echo $I1;
    }
    // _rss *** *** www.LazyCMS.net *** ***
    function _rss(){
        echo Archives::createRss(true);
    }
}