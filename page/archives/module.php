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
 * Module 层
 *
 * @copyright   Copyright (c) 2007-2008 lazycms.net All rights reserved.
 * @author      Lukin <mylukin@gmail.com>
 */
class Archives{
    // Count *** *** www.LazyCMS.net *** ***
    static function Count($l1){
        $db = getConn();
        $model = self::getModel($l1);
        return $db->count("SELECT * FROM `".$model['maintable']."` WHERE `sortid`='{$l1}';");
    }
    // getTopSortId *** *** www.LazyCMS.net *** ***
    static function getTopSortId(){
        $db  = getConn();
        $res = $db->query("SELECT `sortid` FROM `#@_archives_sort` WHERE `sortid1` = '0' ORDER BY `sortorder` DESC,`sortid` DESC;");
        if ($data = $db->fetch($res,0)) {
            return $data[0];
        } else {
            return 0;
        }
    }
    // getModel *** *** www.LazyCMS.net *** ***
    static function getModel($l1){
        $db  = getConn();
        $res = $db->query("SELECT * FROM `#@_archives_sort` AS `s` LEFT JOIN `#@_archives_model` AS `m` ON `s`.`modelid` = `m`.`modelid` WHERE `s`.`sortid` = ?;",$l1);
        if ($data = $db->fetch($res)) {
            return $data;
        } else {
            return false;
        }
    }
    // getSubSortIds *** *** www.LazyCMS.net *** ***
    static function getSubSortIds($l1){
        $I1  = $l1;
        $db  = getConn();
        $res = $db->query("SELECT `sortid` FROM `#@_archives_sort` WHERE `sortid1` = ?;",$l1);
        while ($data = $db->fetch($res,0)) {
            if ($db->count("SELECT count(`sortid`) FROM `#@_archives_sort` WHERE `sortid1`= '".$data[0]."';") > 0) {
                $I1.= ",".self::getSubSortIds($data[0]);
            } else {
                $I1.= ",".$data[0];
            }
        }
        return $I1;
    }
    // getFields *** *** www.LazyCMS.net *** ***
    static function getFields($l1){
        $modeid = $l1;
        $fields = array();
        $db    = getConn();
        $res   = $db->query("SELECT * FROM `#@_archives_fields` WHERE `modelid` = ?;",$modeid);
        while ($data = $db->fetch($res)) {
            $fields[] = $data['fieldename'];
        }
        return $fields;
    }
    // __sort *** *** www.LazyCMS.net *** ***
    static function __sort($l1,$l2,$l3=0,$l4=null,$l5=false){
        // $l1:father sortid, $l2:current sortid, $l3:Space, $l4:selected
        $nbsp = null; $I1 = null;
        for ($i=0; $i<$l3; $i++) {
            $nbsp .= "&nbsp; &nbsp;";
        }
        $db = getConn(); $inSQL = null;
        if ($l5) {
            $modelid  = @$db->result("SELECT `modelid` FROM `#@_archives_sort` WHERE `sortid` = '{$l4}'");
            if ($modelid!==false) {
                $inSQL = "AND `modelid`='{$modelid}'";
            }
        }
        $res = $db->query("SELECT `sortid`,`sortname` FROM `#@_archives_sort` WHERE `sortid1` = ? {$inSQL} ORDER BY `sortorder` DESC,`sortid` DESC;",$l1);
        while ($data = $db->fetch($res,0)) {
            if ($l2 != $data[0]) {
                $selected = ((int)$l4 == (int)$data[0]) ? ' selected="selected"' : null;
                $I1 .= '<option value="'.$data[0].'"'.$selected.'>'.$nbsp.'├ '.$data[1].'</option>';
                if ($db->result("SELECT count(`sortid`) FROM `#@_archives_sort` WHERE `sortid1`='{$data[0]}';") > 0) {
                    $I1 .= self::__sort($data[0],$l2,$l3+1,$l4);
                }
            }
        }
        return $I1;
    }
    // getData *** *** www.LazyCMS.net *** ***
    static function getData($l1,$l2){
        $db  = getConn(); $I1 = array();
        $res = $db->query("SELECT * FROM `{$l2}` WHERE `aid` = ?;",$l1);
        if (!$data = $db->fetch($res)) {
            return false;
        }
        return $data;
    }
    // __model *** *** www.LazyCMS.net *** ***
    static function __model($l1){
        $db  = getConn(); $I1 = null;
        $res = $db->query("SELECT `modelid`,`modelname`,`modelename` FROM `#@_archives_model` WHERE `modelstate`='0' ORDER BY `modelid` ASC;");
        while ($data = $db->fetch($res,0)) {
            $selected = ($l1 == $data[0]) ? ' selected="selected"' : null;
            $I1 .= '<option value="'.$data[0].'"'.$selected.' name="'.$data[2].'">'.$data[1].'['.$data[2].']</option>';
        }
        return $I1;
    }
    // delArchive *** *** www.LazyCMS.net *** ***
    static function delArchive($l1,$l2=false){
        $paths = explode('/',$l1);
        if (strpos($paths[count($paths)-1],'.')!==false){ //文件
            @unlink(LAZY_PATH.$l1);
            if (strpos($l1,'/')!==false){
                $path = substr($l1,0,strlen($l1)-strlen($paths[count($paths)-1]));
                rmdirs(LAZY_PATH.$path,$l2);
            }
        } else { //目录
            @unlink(LAZY_PATH.$l1.'/'.C('SITE_INDEX'));
            rmdirs(LAZY_PATH.$l1,$l2);
        }
    }
    // showSort *** *** www.LazyCMS.net *** ***
    static function showSort($l1){
        $sortid = $l1;
        $db     = getConn();       
        $res    = $db->query("SELECT `sortpath` FROM `#@_archives_sort` WHERE `sortid` = ?;",$sortid);
        if ($data = $db->fetch($res,0)) {
            if (C('SITE_MODE')) {
                return url('Archives','ShowSort','sortid='.$sortid);
            } else {
                return C('SITE_BASE').$data[0].'/';
            }
        } else {
            return C('SITE_BASE');
        }
    }
    // guide *** *** www.LazyCMS.net *** ***
    function guide($l1){
        if (empty($l1)) { return ;}
        $I1 = null; $db = getConn();
        $res = $db->query("SELECT `sortid1`,`sortname`,`sortpath` FROM `#@_archives_sort` WHERE `sortid`=?;",$l1);
        if ($data = $db->fetch($res,0)) {
            $I1 = '<a href="'.self::showSort($l1).'">'.htmlencode($data[1]).'</a>';
            if ((int)$data[0] !== 0) {
                $I1 = self::guide($data[0])." &gt;&gt; ".$I1;
            }
        }
        return $I1;
    }
    // viewSort *** *** www.LazyCMS.net *** ***
    static function viewSort($l1,$page=1,$type=false,$isCreatePage=false){
        @set_time_limit(0);
        import("system.tags"); $tag = new Tags();
        $sortid = $l1; $tmpList = null;
        $page   = !empty($page) ? (int)$page : 1;
        $db     = getConn();
        // 缓存公用不变内容
        $cacheDir = LAZY_PATH.C('HTML_CACHE_PATH').'/'; mkdirs($cacheDir);
        $cachePath = $cacheDir."Archive_CREATE_SORT_{$sortid}.php";
        if (is_file($cachePath)) {
            extract(include($cachePath));
        } else {
            $model  = self::getModel($sortid);
            $fields = self::getFields($model['modelid']);
            $path   = self::showSort($sortid);
            $HTML   = $tag->read($model['sorttemplate1'],$model['sorttemplate2']);
            $HTMList = $tag->getList($HTML,$model['modelename'],1);
            $jsHTML  = $tag->getLabel($HTMList,0);
            $jsType  = strtolower($tag->getLabel($HTMList,'type'));
            $jsOrder = $tag->getLabel($HTMList,'order');
            $jsOrder = strtoupper($jsOrder)=='ASC' ? 'ASC' : 'DESC';
            $jsNumber= floor($tag->getLabel($HTMList,'number'));
            $zebra   = $tag->getLabel($HTMList,'zebra');
            $rand    = chr(3).salt(20).chr(2);//随机出来的替换参数
            $randpl  = chr(3).salt(16).chr(2);
            if ($jsType=='sub') {
                $sortids = self::getSubSortIds($sortid);
            } else {
                $sortids = $sortid;
            }
            // 把 HTML 中的{lazy:...type=list/}标签替换为一个随机的标签；pagelist设置为一个随机标签
            $HTML = str_replace($HTMList,$rand,$HTML);

            // 替换模板中的标签
            $tag->clear();
            $tag->value('title',encode(htmlencode($model['sortname'])));
            $tag->value('sortid',$model['sortid']);
            $tag->value('sortname',encode(htmlencode($model['sortname'])));
            $tag->value('sortpath',encode($path));
            $tag->value('path',encode($path));
            $tag->value('keywords',encode(htmlencode($model['keywords'])));
            $tag->value('description',encode(htmlencode($model['description'])));
            $tag->value('pagelist',encode($randpl));
            $tag->value('guide',encode(self::guide($model['sortid'])));
            
            $HTML = $tag->create($HTML,$tag->getValue());

            $strSQL = "SELECT * FROM `".$model['maintable']."` AS `a` LEFT JOIN `".$model['addtable']."` AS `b` ON `a`.`id` = `b`.`aid` WHERE `a`.`sortid` IN({$sortids}) AND `a`.`show` = 1 ORDER BY `a`.`top` DESC,`a`.`order` {$jsOrder},`a`.`sortid` {$jsOrder}";
            $totalRows  = $db->count($strSQL);
            $totalPages = ceil($totalRows/$jsNumber);
            $totalPages = ((int)$totalPages == 0) ? 1 : $totalPages;
            // 有记录，生成缓存文件
            if ((int)$totalRows > 0) {
                saveFile($cachePath,"<?php \nreturn ".var_export(array(
                    'model'  => $model,
                    'fields' => $fields,
                    'path'   => $path,
                    'HTML'   => $HTML,
                    'HTMList' => $HTMList,
                    'jsHTML'  => $jsHTML,
                    'jsType'  => $jsType,
                    'jsOrder' => $jsOrder,
                    'jsNumber' => $jsNumber,
                    'zebra'    => $zebra,
                    'rand'     => $rand,
                    'randpl'   => $randpl,
                    'sortids'  => $sortids,
                    'strSQL'   => $strSQL,
                    'totalRows'  => $totalRows,
                    'totalPages' => $totalPages,
                ),true)."; \n?>");
            }
        }
        // 判断模板文件改变，就删除缓存
        if (is_file($cachePath)) {
            $template1 = filemtime(LAZY_PATH.$model['sorttemplate1']);
            $template2 = filemtime(LAZY_PATH.$model['sorttemplate2']);
            if ((int)filemtime($cachePath)<(int)$template1 || (int)filemtime($cachePath)<(int)$template2) {
                unlink($cachePath);
            }
        }
        if ((int)$page > (int)$totalPages) {
            $page = $totalPages;
        }
        $percent = round($page/$totalPages*100,2);
        $strSQL .= ' LIMIT '.$jsNumber.' OFFSET '.($page-1)*$jsNumber.';';
        if ((int)$totalRows > 0) {
            $res = $db->query($strSQL);
            $i = 1;
            while ($data = $db->fetch($res)) {
                $_model = self::getModel($data['sortid']);
                $tag->clear();
                $tag->value('id',$data['id']);
                $tag->value('sortid',$data['sortid']);
                $tag->value('sortname',encode(htmlencode($_model['sortname'])));
                $tag->value('sortpath',encode(self::showSort($data['sortid'])));
                $tag->value('title',encode(htmlencode($data['title'])));
                $tag->value('path',encode(self::showArchive($data['id'],$_model)));
                $tag->value('image',encode($data['img']));
                $tag->value('date',$data['date']);
                $tag->value('hits',$data['hits']);
                $tag->value('keywords',encode(htmlencode($data['keywords'])));
                $tag->value('description',encode(htmlencode($data['description'])));
                $tag->value('zebra',($i % ($zebra+1)) ? 0 : 1);

                foreach ($fields as $k) {
                    $tag->value($k,encode($data[$k]));
                }
                $tmpList.= $tag->createhtm($jsHTML,$tag->getValue());
                
                $i++;
                if ($isCreatePage && !C('SITE_MODE')) {
                    self::viewArchive($sortid,$data['id']);
                }
            }
            $outHTML = str_replace($rand,$tmpList,$HTML);
            if (C('SITE_MODE')) {
                $path = url('Archives','ShowSort','sortid='.$sortid.'&page=$');;
            } else {
                $path.= 'index$'.C('HTML_URL_SUFFIX');
            }
            $outHTML = str_replace($randpl,self::pagelist($path,$page,$totalPages,$totalRows),$outHTML);
        } else {
            $outHTML = str_replace($rand,L('error/rsnot'),$HTML);
            $outHTML = str_replace($randpl,null,$outHTML);
        }
        // 生成
        if (!C('SITE_MODE')) { 
            mkdirs(LAZY_PATH.$model['sortpath']);
            if ((int)$page == 1) {
                $arcPath = LAZY_PATH.$model['sortpath'].'/'.C('SITE_INDEX');
            } elseif ((int)$page <= (int)$totalPages) {
                $arcPath = LAZY_PATH.$model['sortpath'].'/index'.$page.C('HTML_URL_SUFFIX');
            }
            if (!empty($arcPath)) {
                saveFile($arcPath,$outHTML);
            }
        }
        if ($type) {
            if (!((int)$percent<100)) {
                @unlink($cachePath);@unlink($cacheDir."Archive_CREATE_PAGE_{$sortid}.php");
            }
            return $percent;
        } else {
            return $outHTML;
        }
    }
    // pagelist *** *** www.LazyCMS.net *** ***
    static function pagelist($l1,$l2,$l3,$l4,$l5=0){
        // url,page,总页数,记录总数
        // 修要修改分页风格，直接修改此函数即可
        $I1 = null;
        if (strpos($l1,'%24')!==false) { $l1 = str_replace('%24','$',$l1); }
        if (strpos($l1,'$')==0 || $l4==0) { return ; }
        $l7 = (C('SITE_MODE') || $l5) ? 1 : null;
        if ($l2 > 3) {
            $I1 = '<a href="'.str_replace('$',$l7,$l1).'">1 ...</a>';
        }
        if ($l2 > 2) {
            $I1 .= '<a href="'.str_replace('$',$l2-1,$l1).'">&lsaquo;&lsaquo;</a>';
        } elseif ($l2==2) {
            $I1 .= '<a href="'.str_replace('$',$l7,$l1).'">&lsaquo;&lsaquo;</a>';
        }
        $l5 = $l2-2;
        $l6 = $l2+7;
        for ($i=$l5; $i<=$l6; $i++) {
            if ($i>=1 && $i<=$l3) {
                if ((int)$i==(int)$l2) {
                    $I1 .= "<strong>$i</strong>";
                } else {
                    if ($i==1) {
                        $I1 .= '<a href="'.str_replace('$',$l7,$l1).'">'.$i.'</a>';
                    } else {
                        $I1 .= '<a href="'.str_replace('$',$i,$l1).'">'.$i.'</a>';
                    }
                }
            }
        }
        if ($l2 < $l3) {
            $I1 .= '<a href="'.str_replace('$',$l2+1,$l1).'">&rsaquo;&rsaquo;</a>';
        }
        if ($l2 < ($l3-7)) {
            $I1 .= '<a href="'.str_replace('$',$l3,$l1).'">... '.$l3.'</a>';
        }
        $I2 = explode('$',$l1);
        return '<div class="pagelist"><em>'.$l4.'</em>'.$I1.'</div>';
    }
    // showArchive *** *** www.LazyCMS.net *** ***
    static function showArchive($l1,$l2,$l3=null){
        if (is_numeric($l2)) {
            $model = self::getModel($l2);
        } else {
            $model = $l2;
        }
        $aid   = $l1;
        $db    = getConn();       
        $res   = $db->query("SELECT `a`.`sortpath`,`b`.`path` FROM `#@_archives_sort` AS `a` LEFT JOIN `".$model['maintable']."` AS `b` ON `a`.`sortid` = `b`.`sortid` WHERE `b`.`id` = ?;",$aid);
        if ($data = $db->fetch($res,0)) {
            if (C('SITE_MODE')) {
                if (!empty($l3)) {
                    $page = '&page='.$l3;
                } else {
                    $page = null;
                }
                return url('Archives','ShowArchive','sortid='.$model['sortid'].'&aid='.$aid.$page);
            } else {
                if (!empty($l3)) {
                    if (is_numeric($l3) && (int)$l3==1) {
                        $l3 = null;
                    }
                    $l4 = $data[1];
                    if (isfile($l4)) {
                        $l5 = strrpos($l4,'.');
                        $l4 = substr($l4,0,$l5).$l3.substr($l4,$l5,strlen($l4));
                    } else {
                        $l4.= $l3;
                    }
                } else {
                    $l4 = $data[1];
                }
                if (substr($l4,0,1)=='/') {
                    $l4 = ltrim($l4,'/');
                    $I1 = C('SITE_BASE').$l4;
                } else {
                    $I1 = C('SITE_BASE').$data[0].'/'.$l4;
                }
                if (!isfile($I1)) { $I1.= '/';}
                return $I1;
            }
        }
    }
    // viewArchive *** *** www.LazyCMS.net *** ***
    static function viewArchive($l1,$l2,$l3=1){
        @set_time_limit(0);
        import("system.tags"); $tag = new Tags();
        $sortid = $l1; $aid = $l2; $page = $l3; $db = getConn();
        // 缓存公用不变内容
        $cachePath = LAZY_PATH.C('HTML_CACHE_PATH').'/'; mkdirs($cachePath);
        $cachePath.= "Archive_CREATE_PAGE_{$sortid}.php";
        if (is_file($cachePath)) {
            extract(include($cachePath));
        } else {
            $model  = self::getModel($sortid);
            $fields = self::getFields($model['modelid']);
            $HTML   = $tag->read($model['pagetemplate1'],$model['pagetemplate2']);
            $sortpath = self::showSort($sortid);
            // 生成缓存文件
            saveFile($cachePath,"<?php \nreturn ".var_export(array(
                'model'  => $model,
                'fields' => $fields,
                'HTML'   => $HTML,
                'sortpath' => $sortpath,
            ),true)."; \n?>");
        }
        // 判断模板文件改变，就删除缓存
        if (is_file($cachePath)) {
            $template1 = filemtime(LAZY_PATH.$model['pagetemplate1']);
            $template2 = filemtime(LAZY_PATH.$model['pagetemplate2']);
            if ((int)filemtime($cachePath)<(int)$template1 || (int)filemtime($cachePath)<(int)$template2) {
                unlink($cachePath);
            }
        }
        $res   = $db->query("SELECT * FROM `".$model['maintable']."` AS `a` LEFT JOIN `".$model['addtable']."` AS `b` ON `a`.`id` = `b`.`aid` WHERE `id` = ?;",$aid);
        if ($data = $db->fetch($res)) {
            // 替换模板中的标签
            $tag->clear();
            $tag->value('id',$data['id']);
            $tag->value('sortid',$data['sortid']);
            $tag->value('title',encode(htmlencode($data['title'])));
            $tag->value('sortname',encode(htmlencode($model['sortname'])));
            $tag->value('sortpath',encode($sortpath));
            $tag->value('image',encode($data['img']));
            $tag->value('date',$data['date']);
            $tag->value('keywords',encode(htmlencode($data['keywords'])));
            $tag->value('description',encode(htmlencode($data['description'])));
            $tag->value('guide',encode(self::guide($data['sortid'])." &gt;&gt; ".htmlencode($data['title'])));
            $tag->value('hits',encode("<span class=\"lz_hits\"><script type=\"text/javascript\">\$('.lz_hits').html(loadgif()).load('".url('Archives','hits','sortid='.$sortid.'&id='.$data['id'])."');</script></span>"));
            $tag->value('lastpage',encode(self::lastPage($data,$model,$HTML)));
            $tag->value('nextpage',encode(self::nextPage($data,$model,$HTML)));

            // 有编辑器，动态模式，分页，只对第一个编辑器进行处理
            $result = $db->query("SELECT * FROM `#@_archives_fields` WHERE `modelid` = ? AND `inputtype`='editor' ORDER BY `fieldorder` ASC, `fieldid` ASC;",$model['modelid']);
            if ($field = $db->fetch($result)){
                $contents = preg_split(C('WEB_BREAK'),$data[$field['fieldename']],-1,PREG_SPLIT_NO_EMPTY);
                $length   = count($contents);
                // 动态模式，只浏览不生成
                if (C('SITE_MODE')) {
                    $page = (int)$page > (int)$length ? $length : $page;
                    foreach ($fields as $k) { $tag->value($k,encode($data[$k])); }
                    $tag->value('path',encode(self::showArchive($data['id'],$model,$page)));
                    $tag->value('pagelist',encode(self::pagelists(self::showArchive($data['id'],$model,'$'),$length,$page)));
                    $tag->value($field['fieldename'],encode($contents[$page-1]));
                    return $tag->create($HTML,$tag->getValue());
                }
            } else {
                $contents = null;
                $length   = -1;
            }

            // 静态模式，且有编辑器，分页生成 
            if (!C('SITE_MODE') && (int)$length > 0) { 
                for ($i=0;$i<$length;$i++) {
                    foreach ($fields as $k) { $tag->value($k,encode($data[$k])); }
                    $tag->value('path',encode(self::showArchive($data['id'],$model,($i+1))));
                    $tag->value('pagelist',encode(self::pagelists(self::showArchive($data['id'],$model,'$'),$length,($i+1))));
                    $tag->value($field['fieldename'],encode($contents[$i]));
                    $outHTML = $tag->create($HTML,$tag->getValue());
                    // 生成每一页
                    $path = $data['path'];
                    if ((int)$i>0) {
                        if (isfile($path)) {
                            $instr = strrpos($path,'.');
                            $path = substr($path,0,$instr).($i+1).substr($path,$instr,strlen($path));
                        } else {
                            $path.= $i+1;
                        }    
                    }
                    self::createHTML($model['sortpath'],$path,$outHTML);
                }
                return ;
            }

            // 没有编辑器，动态模式 AND 没有编辑器静态模式
            $tag->value('path',encode(self::showArchive($data['id'],$model)));
            if (!empty($fields)) {
                foreach ($fields as $k) {
                    $tag->value($k,encode($data[$k]));
                }
            }
            $outHTML = $tag->create($HTML,$tag->getValue());

            if (!C('SITE_MODE')) {
                // 静态模式，没有分页，只生成一页
                self::createHTML($model['sortpath'],$data['path'],$outHTML);
            }
        } else {
            $outHTML = null;
        }
        return $outHTML;
    }
    // pagelists *** *** www.LazyCMS.net *** ***
    static function pagelists($l1,$l2,$l3){
        // url,总页数,page
        $I1 = null;if ($l2<=1) { return ; }
        if (strpos($l1,'%24')!==false) { $l1 = str_replace('%24','$',$l1); }
        if (strpos($l1,'$')===false) { return ; }
        $l4 = C('SITE_MODE') ? 1 : null;
        for ($i=1; $i<=$l2; $i++) {
            if ((int)$i==(int)$l3) {
                $I1 .= "<strong>{$i}</strong>";
            } else {
                if ($i==1) {
                    $I1 .= '<a href="'.str_replace('$',$l4,$l1).'">'.$i.'</a>';
                } else {
                    $I1 .= '<a href="'.str_replace('$',$i,$l1).'">'.$i.'</a>';
                }
            }
        }
        return $I1;
    }
    // createHTML *** *** www.LazyCMS.net *** ***
    static function createHTML($l1,$l2,$l3){
        // $l1:目录路径, $l2:文件路径, $l3:需要保存的内容
        // 生成文件
        $paths = explode('/',$l2);
        $count = count($paths);
        if (strpos($paths[$count-1],'.')!==false){ //文件
            if (substr($l2,0,1)=='/') {
                $l2 = ltrim($l2,'/');
                if (strpos($l2,'/')!==false){
                    $path = substr($l2,0,strlen($l2)-strlen($paths[$count-1]));
                    mkdirs(LAZY_PATH.$path);
                }
                saveFile(LAZY_PATH.$l2,$l3);
                return true;
            }
            if (strpos($l2,'/')!==false){
                $path = substr($l2,0,strlen($l2)-strlen($paths[$count-1]));
                mkdirs(LAZY_PATH.$l1.'/'.$path);
            }
            mkdirs(LAZY_PATH.$l1);
            saveFile(LAZY_PATH.$l1.'/'.$l2,$l3);
        } else { //目录
            if (substr($l2,0,1)=='/') {
                $l2 = ltrim($l2,'/');
                mkdirs(LAZY_PATH.$l2);
                saveFile(LAZY_PATH.$l2.'/'.C('SITE_INDEX'),$l3);
                return true;
            }
            mkdirs(LAZY_PATH.$l1.'/'.$l2);
            saveFile(LAZY_PATH.$l1.'/'.$l2.'/'.C('SITE_INDEX'),$l3);
        }
    }
    // lastPage *** *** www.LazyCMS.net *** ***
    static function lastPage($l1,$l2,$l3){
        if (strpos($l3,'{lazy:lastpage')!==false) {
            $data = $l1; $model = $l2; $HTML = $l3;
            $db  = getConn();
            $res = $db->query("SELECT `title`,`path`,`id` FROM `".$model['maintable']."` WHERE `show`=1 AND `sortid`= :sortid AND `order`<:order ORDER BY `top` DESC,`order` DESC,`id` DESC LIMIT 0,1;",array('sortid'=>$model['sortid'],'order'=>$data['order']));
            if ($row = $db->fetch($res,0)) {
                $I1 = '<a href="'.self::showArchive($row[2],$model).'">'.htmlencode($row[0]).'</a>';
            } else {
                $I1 = '<a href="'.self::showSort($model['sortid']).'">['.htmlencode($model['sortname']).']</a>';
            }
            return "<span class=\"lz_lastpage\">{$I1}</span>";
        }
    }
    // nextPage *** *** www.LazyCMS.net *** ***
    static function nextPage($l1,$l2,$l3){
        if (strpos($l3,'{lazy:nextpage')!==false) {
            $data = $l1; $model = $l2; $HTML = $l3;
            $db  = getConn();
            $res = $db->query("SELECT `title`,`path`,`id` FROM `".$model['maintable']."` WHERE `show`=1 AND `sortid`= :sortid AND `order`>:order ORDER BY `top` ASC,`order` ASC,`id` ASC LIMIT 0,1;",array('sortid'=>$model['sortid'],'order'=>$data['order']));
            if ($row = $db->fetch($res,0)) {
                $I1 = '<a href="'.self::showArchive($row[2],$model).'">'.htmlencode($row[0]).'</a>';
            } else {
                $I1 = "<script type=\"text/javascript\">\$('.lz_nextpage').html(loadgif()).load('".url('Archives','nextpage','sortid='.$model['sortid'].'&id='.$data['id'])."');</script>";
            }
            return "<span class=\"lz_nextpage\">{$I1}</span>";
        }
    }
    // isOpen *** *** www.LazyCMS.net *** ***
    static function isOpen($l1){
        $db    = getConn();
        $state = $db->result("SELECT `sortopen` FROM `#@_archives_sort` WHERE `sortid` = '{$l1}';");
        $state = (string)$state == "1" ? 'true' : 'false';
        return $state;
    }
    // isSub *** *** www.LazyCMS.net *** ***
    static function isSub($l1){
        $db    = getConn();
        $state = $db->result("SELECT count(*) FROM `#@_archives_sort` WHERE `sortid1` = '{$l1}';") > 0 ? '1' : '2';
        return $state;
    }
    // subSort *** *** www.LazyCMS.net *** ***
    static function subSort($l1,$l2=1){
        $state = self::isSub($l1);
        $onclick = ((int)$state == 1) ?' onclick="$(this).addsub('.$l1.','.$l2.');"' : null;
        if (self::isOpen($l1)=='true') {
            $state = 'loading';
        } else {
            $state = 'os/dir'.$state;
        }
        return t2js('<a href="javascript:;"'.$onclick.' id="dir'.$l1.'"><img src="'.C('SITE_BASE').C('PAGES_PATH').'/system/images/'.$state.'.gif" class="os" /></a>');
    }
    // createRss *** *** www.LazyCMS.net *** ***
    static function createRss($isHeader=false){
        if ($isHeader) { header('content-type: text/xml'); }
        $db  = getConn(); $strSQL = null; $module = getObject();
        // 两个用户设置的变量
        $rss = M('Archives','ARCHIVES_RSS_FILE');
        $num = M('Archives','ARCHIVES_RSS_NUMBER');
        $url = 'http://'.$_SERVER['HTTP_HOST'].(C('SITE_BASE')!='/'?C('SITE_BASE'):null);
        $XML = '<?xml version="1.0" encoding="utf-8"?>';
        $XML.= '<rss version="2.0"><channel>';
        $XML.= '<title><![CDATA['.$module->system['sitename'].']]></title>';
        $XML.= '<link>'.$url.'</link>';
        $XML.= '<description><![CDATA[Powered by:LazyCMS v'.$module->system['systemver'].']]></description>';
        $XML.= '<language>'.C('LANGUAGE').'</language>';
        $XML.= '<generator><![CDATA['.$module->system['sitename'].']]></generator>';
        $XML.= '<ttl>5</ttl>';
        $XML.= '</channel></rss>';
        // 创建dom对象
        $dom = new DOMDocument();
        $dom->loadXML($XML);
        $xPath = new DOMXPath($dom);
        $res = $db->query("SELECT DISTINCT `maintable` FROM `#@_archives_model` GROUP BY `maintable`;");
        while ($data = $db->fetch($res,0)) {
            if (empty($strSQL)) {
                $strSQL.= "SELECT `a`.*,`b`.`sortname`,`b`.`sortpath` FROM `".$data[0]."` AS `a` LEFT JOIN `#@_archives_sort` AS `b` ON `a`.`sortid`=`b`.`sortid`";
            } else {
                $strSQL.= " UNION SELECT `a`.*,`b`.`sortname`,`b`.`sortpath` FROM `".$data[0]."` AS `a` LEFT JOIN `#@_archives_sort` AS `b` ON `a`.`sortid`=`b`.`sortid`";
            }
        }
        $strSQL.= " ORDER BY `a`.`id` DESC LIMIT 0,{$num}";
        $res = $db->query($strSQL);
        while ($data = $db->fetch($res)) {
            $model   = Archives::getModel($data['sortid']);
            $channel = $xPath->evaluate("//rss/channel")->item(0);
            $item    = $channel->appendChild($dom->createElement('item'));
            $title   = $item->appendChild($dom->createElement('title')); $title->appendChild($dom->createCDATASection($data['title']));
            $link    = $item->appendChild($dom->createElement('link')); $link->nodeValue = xmlencode($url.self::showArchive($data['id'],$model));
            $pubDate     = $item->appendChild($dom->createElement('pubDate')); $pubDate->nodeValue = date('Y-m-d H:i:s',$data['date']);
            $category    = $item->appendChild($dom->createElement('category')); $category->nodeValue = xmlencode($data['sortname']);
            $description = $item->appendChild($dom->createElement('description')); $description->appendChild($dom->createCDATASection($data['description']));
        }
        if (!C('SITE_MODE')) {
            $dom->save(LAZY_PATH.$rss);
        }
        return $dom->saveXML();
    }
    // createSiteMaps *** *** www.LazyCMS.net *** ***
    function createSortSiteMaps($l1){
        @set_time_limit(0);
        $sortids = $l1; $db = getConn();
        $arrSortids = explode(',',$sortids);
        $siteUrl = 'http://'.$_SERVER['HTTP_HOST'].(C('SITE_BASE')!='/'?C('SITE_BASE'):null);
        foreach ($arrSortids as $sortid) {
            $model  = self::getModel($sortid); $I1 = null;
            
            $tag  = O('Tags');
            $HTML = $tag->read($model['sorttemplate1'],$model['sorttemplate2']);
            $HTMList = $tag->getList($HTML,$model['modelename'],1);
            $jsNumber= floor($tag->getLabel($HTMList,'number'));

            $strSQL = "SELECT * FROM `".$model['maintable']."` WHERE `show` = 1 AND `sortid`='{$sortid}' ORDER BY `id` DESC";
            $totalRows = $db->count($strSQL);
            $totalPages = ceil($totalRows/$jsNumber);
            $totalPages = ((int)$totalPages == 0) ? 1 : $totalPages;
            // 列表页面地址
            for ($page=1; $page<=$totalPages; $page++) {
                if (C('SITE_MODE')) {
                    if ($page==1) { $query = null; } else { $query = '&page='.$page; }
                    $path = url('Archives','ShowSort','sortid='.$sortid.$query);;
                } else {
                    if ($page==1) { $num = null; } else { $num = $page; }
                    $path = self::showSort($sortid).'index'.$num.C('HTML_URL_SUFFIX');
                }
                $url = '<loc>'.xmlencode($siteUrl.$path).'</loc>';
                $url.= '<changefreq>always</changefreq>';
                $url.= '<priority>0.5</priority>';
                $I1.= '<url>'.$url.'</url>';
            }
            // 文章页面地址
            if ($totalRows>0) { 
                $maxRows = 50000 - $page;
                if ($totalRows>$maxRows) {
                    $strSQL.= " LIMIT 0,{$maxRows};";
                }
                $res = $db->query($strSQL);
                while ($data = $db->fetch($res)) {
                    if (!C('SITE_MODE')) { if (strncmp($data['path'],'/',1)===0) { continue; } }
                    $url = '<loc>'.xmlencode($siteUrl.self::showArchive($data['id'],$model)).'</loc>';
                    $url.= '<lastmod>'.date('c',$data['date']).'</lastmod>';
                    $url.= '<changefreq>weekly</changefreq>';
                    $url.= '<priority>0.8</priority>';
                    $I1.= '<url>'.$url.'</url>';
                }
            }
            $XML = '<?xml version="1.0" encoding="UTF-8"?>';
            $XML.= '<urlset xmlns="http://www.google.com/schemas/sitemap/0.84">';
            $XML.= $I1;unset($I1);
            $XML.= '</urlset>';
            mkdirs(LAZY_PATH.$model['sortpath']);
            saveFile(LAZY_PATH.$model['sortpath'].'/sitemap.xml',$XML);
        }
    }
    // createSiteMaps *** *** www.LazyCMS.net *** ***
    function createSiteMaps(){
        @set_time_limit(0);
        // 生成总SiteMaps索引，包括单页面，根目录生成的文件
        $db = getConn(); $strSQL = null; $I1 = null; $i = 0; $Index = null;
        $siteUrl = 'http://'.$_SERVER['HTTP_HOST'].(C('SITE_BASE')!='/'?C('SITE_BASE'):null);
        // 生成单页面地址
        if (class_exists('Onepage')) {
            $res = $db->query("SELECT * FROM `#@_onepage` WHERE `ishome` = '0';");
            while ($data = $db->fetch($res)) {
                $i++;
                $url = '<loc>'.xmlencode($siteUrl.Onepage::show($data['oneid'])).'</loc>';
                $url.= '<changefreq>always</changefreq>';
                $url.= '<priority>0.9</priority>';
                $I1.= '<url>'.$url.'</url>';
            }
        }
        // 静态模式生成根目录生成的文件地址
        if (!C('SITE_MODE')) {
            $res = $db->query("SELECT DISTINCT `maintable` FROM `#@_archives_model` GROUP BY `maintable`;");
            while ($data = $db->fetch($res,0)) {
                if (empty($strSQL)) {
                    $strSQL.= "SELECT * FROM `".$data[0]."` WHERE LEFT(`path`,1)='/'";
                } else {
                    $strSQL.= " UNION SELECT * FROM `".$data[0]."` WHERE LEFT(`path`,1)='/'";
                }
            }
            $strSQL.= " ORDER BY `id` DESC";
            $totalRows = $db->count($strSQL);
            $maxRows = 50000 - $i;
            if ($totalRows>$maxRows) {
                $strSQL.= " LIMIT 0,{$maxRows};";
            }
            $res = $db->query($strSQL);
            while ($data = $db->fetch($res)) {
                $url = '<loc>'.xmlencode($siteUrl.$data['path']).'</loc>';
                $url.= '<lastmod>'.date('Y-m-d H:i:s',$data['date']).'</lastmod>';
                $url.= '<changefreq>weekly</changefreq>';
                $url.= '<priority>0.8</priority>';
                $I1.= '<url>'.$url.'</url>';
            }
        }
        // 生成所有在根目录下载地址sitemaps文件
        $XML = '<?xml version="1.0" encoding="UTF-8"?>';
        $XML.= '<urlset xmlns="http://www.google.com/schemas/sitemap/0.84">';
        $XML.= '<url>';
        $XML.= '<loc>'.xmlencode('http://'.$_SERVER['HTTP_HOST'].C('SITE_BASE')).'</loc>';
        $XML.= '<changefreq>always</changefreq>';
        $XML.= '<priority>1</priority>';
        $XML.= '</url>';
        $XML.= $I1;
        $XML.= '</urlset>';
        saveFile(LAZY_PATH.'/sitemap.xml',$XML);
        // 生成总索引
        $siteUrl = 'http://'.$_SERVER['HTTP_HOST'].C('SITE_BASE');
        $res = $db->query("SELECT * FROM `#@_archives_sort` WHERE 1=1 ORDER BY `sortid` DESC;");
        while ($data = $db->fetch($res)) {
            $sitemap = LAZY_PATH.$data['sortpath'].'/sitemap.xml';
            if (is_file($sitemap)) {
                $Index.= '<sitemap><loc>'.xmlencode($siteUrl.$data['sortpath'].'/sitemap.xml').'</loc></sitemap>';
            }
        }
        $XML = '<?xml version="1.0" encoding="UTF-8"?>';
        $XML.= '<sitemapindex xmlns="http://www.google.com/schemas/sitemap/0.84">';
        $XML.= '<sitemap>';
        $XML.= '<loc>'.xmlencode($siteUrl.'sitemap.xml').'</loc>';
        $XML.= '</sitemap>';
        $XML.= $Index;
        $XML.= '</sitemapindex>';
        saveFile(LAZY_PATH.'/sitemaps.xml',$XML);
    }
    // tags *** *** www.LazyCMS.net *** ***
    static function tags($tags,$inValue){
        @set_time_limit(0);
        $inSQL = null; $tmpList = null; $db = getConn();
        $tagName = sect($tags,"(lazy\:)","( |\/|\}|\))");
        $HTMList = $tags; import("system.tags"); $tag = new Tags();
        $jsHTML  = $tag->getLabel($HTMList,0);
        $sortid  = $tag->getLabel($HTMList,'sortid');
        $jsType  = strtolower($tag->getLabel($HTMList,'type'));
        // 如果是请求链接直接返回链接地址
        if ($jsType=='link') { return self::showSort($sortid); }

        $jsNumber= floor($tag->getLabel($HTMList,'number'));
        $remove  = $tag->getLabel($HTMList,'remove');
        $zebra   = $tag->getLabel($HTMList,'zebra');

        // 根据tagName 取得modelid
        $res   = $db->query("SELECT * FROM `#@_archives_model` WHERE `modelename` = ?;",$tagName);
        if ($model = $db->fetch($res)) {
            $fields  = self::getFields($model['modelid']);
            if (is_numeric($remove)) {
                $inSQL.= " AND `m`.`sortid` NOT IN({$remove})";
            }
            if (preg_match('/\(lazy:image.{0,}?\/\)/i',$jsHTML,$regs)){
                $inSQL.= " AND `m`.`img` <> ''";
            }
            if (validate($sortid,6) || instr('sub,current',$sortid)) {
                switch (strtolower($sortid)){
                    case 'sub':
                        $sortid = $inValue['sortid'];
                        $sortid = self::getSubSortIds($sortid);
                        break;
                    case 'current':
                        $sortid = $inValue['sortid'];
                        break;
                }
                if (empty($sortid)) { $sortid = 0; }
                $inSQL.= " AND `m`.`sortid` IN({$sortid})";
            } else {
                $sortname = $tag->getLabel($HTMList,'sortname');
                if (strlen($sortname) > 0) {
                    $inSQL.= $db->quoteInto(" AND `s`.`sortname` = ?",$sortname);
                }
            }
            $select = "SELECT `m`.*,`a`.*,`s`.`sortname` FROM `".$model['maintable']."` AS `m`
                        LEFT JOIN `".$model['addtable']."` AS `a` ON `m`.`id` = `a`.`aid`
                        LEFT JOIN `#@_archives_sort` AS `s` ON `s`.`sortid` = `m`.`sortid` WHERE `s`.`modelid`='".$model['modelid']."' AND `m`.`show` = 1 ";

            switch ($jsType) {
                case 'related':// 相关文章
                    $key = $inValue['keywords'];
                    $aid = $inValue['id'];
                    $likey = likey("`m`.`keywords`",$key);
                    if (strlen($likey) > 0) {
                        $likey = " AND ({$likey})";
                        if (validate($aid,2)) {
                            $strSQL = $select.$inSQL.$likey." AND `m`.`id`<>{$aid} ORDER BY `m`.`order` DESC,`m`.`id` DESC";
                        } else {
                            $strSQL = $select.$inSQL.$likey." ORDER BY `m`.`order` DESC,`m`.`id` DESC";
                        }
                    } else {
                        return ;
                    }
                    break;
                case 'sql':// 自定义SQL
                    $jsSQL  = $tag->getLabel($HTMList,'sql');
                    $strSQL = $select.$jsSQL;
                    break;
                case 'commend':// 推荐文章
                    $strSQL = $select." AND `m`.`commend` = 1 {$inSQL} ORDER BY `m`.`order` DESC,`m`.`id` DESC";
                    break;
                case 'hot':// 热门文章
                    $strSQL = $select.$inSQL." ORDER BY `m`.`hits` DESC ,`m`.`id` DESC";
                    break;
                case 'chill': case 'cold':// 冷门文章
                    $strSQL = $select.$inSQL." ORDER BY `m`.`hits` ASC ,`m`.`id` ASC";
                    break;
                default : // 最新文章
                    $strSQL = $select.$inSQL." ORDER BY `m`.`order` DESC,`m`.`id` DESC";
                    break;
            }
            $strSQL.= " LIMIT 0,{$jsNumber};";
            
            $rs = $db->query($strSQL);
            $i  = 1;
            while ($data = $db->fetch($rs)) {
                $tag->clear();
                $tag->value('id',$data['id']);
                $tag->value('sortid',$data['sortid']);
                $tag->value('sortname',encode(htmlencode($data['sortname'])));
                $tag->value('sortpath',encode(self::showSort($data['sortid'])));
                $tag->value('title',encode(htmlencode($data['title'])));
                $tag->value('path',encode(self::showArchive($data['id'],self::getModel($data['sortid']))));
                $tag->value('image',encode($data['img']));
                $tag->value('date',$data['date']);
                $tag->value('keywords',encode(htmlencode($data['keywords'])));
                $tag->value('description',encode(htmlencode($data['description'])));
                $tag->value('zebra',($i % ($zebra+1)) ? 0 : 1);
                $tag->value('++',$i);
                foreach ($fields as $k) {
                    $tag->value($k,encode($data[$k]));
                }
                $tmpList.= $tag->createhtm($jsHTML,$tag->getValue());
                $i++;
            }
        }
        return $tmpList;
    }
    // showTypes *** *** www.LazyCMS.net *** ***
    static function showTypes($l1=null){
        $I1 = null; $module = getObject();
        $l2 = array(
            'input'    => 'varchar',   // 输入框
            'textarea' => 'text',      // 文本框
            'radio'    => 'varchar',   // 单选框
            'checkbox' => 'varchar',   // 复选框
            'select'   => 'varchar',   // 下拉菜单
            'basic'    => 'text',      // 简易编辑器
            'editor'   => 'mediumtext',// 内容编辑器
            'date'     => 'datetime',  // 日期选择器
            'upfile'   => 'varchar',   // 文件上传框
        );
        foreach ($l2 as $k => $v){
            $selected = ((string)$l1 == (string)$k) ? ' selected="selected"' : null;
            $I1 .= '<option value="'.$k.'" type="'.$v.'"'.$selected.'>'.$module->L('models/field/type/'.$k).'</option>';
        }
        return $I1;
    }
    // installModel *** *** www.LazyCMS.net *** ***
    static function installModel($modelCode,$isDeleteTable=false){
        $db  = getConn();
        $modelDom = DOMDocument::loadXML($modelCode);
        $XPath    = new DOMXPath($modelDom);

        // Model Value
        $data[] = $XPath->evaluate("//lazycms/model/modelname")->item(0)->nodeValue;
        $data[] = $XPath->evaluate("//lazycms/model/modelename")->item(0)->nodeValue;
        $data[] = '#@_'.$XPath->evaluate("//lazycms/model/maintable")->item(0)->nodeValue;
        $data[] = '#@_'.$XPath->evaluate("//lazycms/model/addtable")->item(0)->nodeValue;
        $data[] = $XPath->evaluate("//lazycms/model/modelstate")->item(0)->nodeValue;
        $salt   = salt(4);
        if (!$isDeleteTable) {
            if ($db->isTable($data[3])) {
                $data[1].= '_'.$salt;
                $data[3].= '_'.$salt;
            }
        }

        // 验证表是否可以作为索引表
        if (strtolower($data[2])!='#@_archives') {
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
                // 不是索引表类型，自动创建一个索引表
                $data[2].= '_'.$salt;
                $db->copy('#@_archives',$data[2]);
            }
        }

        // Insert model
        $row = array(
            'modelname'  => $data[0],
            'modelename' => $data[1],
            'maintable'  => $data[2],
            'addtable'   => $data[3],
            'modelstate' => $data[4],
        );
        $db->insert('#@_archives_model',$row);
        
        // Insert fields
        $inSQL      = null;
        $indexSQL   = null;
        $modelid    = $db->lastInsertId();
        $objFields  = $modelDom->getElementsByTagName('fields')->item(0)->childNodes;
        $fieldCount = $objFields->length;
        for ($i=0; $i<$fieldCount; $i++) {
            $row       = array();
            $objItem   = $objFields->item($i)->childNodes;
            $itemCount = $objItem->length;
            for ($j=0; $j<$itemCount; $j++) {
                $row[$objItem->item($j)->nodeName] = $objItem->item($j)->nodeValue;
            }
            $row = array_merge($row,array(
                'modelid'    => $modelid,
                'fieldorder' => $db->max('fieldid','#@_archives_fields'),
                'fieldindex' => (string)$row['fieldindex'],
            ));
            if (instr('text,mediumtext,datetime',$row['fieldtype'])) {
                $row['fieldlength'] = null;
            } else {
                $row['fieldlength'] = !empty($row['fieldlength']) ? $row['fieldlength'] : 255;
            }
            $length  = !empty($row['fieldlength']) ? "( ".$row['fieldlength']." ) " : null;
            if ((string)$row['fieldtype']!='datetime') {
                $default = (string)$row['fieldefault'] ? " default '".t2js($row['fieldefault'])."' " : null;
            } else {
                $default = null;
            }
            $inSQL.= "`".$row['fieldename']."` ".$row['fieldtype'].$length.$default.",";
            if (!empty($row['fieldindex'])){ 
                $indexSQL.= "KEY `".$row['fieldename']."` (`".$row['fieldename']."`),";
            }
            $db->insert('#@_archives_fields',$row);
        }
        $db->exec("DROP TABLE IF EXISTS `".$data[3]."`;");
        // 创建新表
        $db->exec("CREATE TABLE IF NOT EXISTS `".$data[3]."` (
                    `aid` int(11) NOT NULL,
                    {$inSQL}{$indexSQL}
                    PRIMARY KEY (`aid`)
                   ) ENGINE=MyISAM DEFAULT CHARSET=#~lang~#;");
    }
    // uninstSQL *** *** www.LazyCMS.net *** ***
    static function uninstSQL(){
        return <<<SQL
            DROP TABLE IF EXISTS `#@_archives`;
            DROP TABLE IF EXISTS `#@_archives_model`;
            DROP TABLE IF EXISTS `#@_archives_fields`;
            DROP TABLE IF EXISTS `#@_archives_sort`;
SQL;
    }
    // instsql *** *** www.LazyCMS.net *** ***
    static function instSQL(){
        return <<<SQL
            // 公共存档
            CREATE TABLE IF NOT EXISTS `#@_archives` (
              `id` int(11) NOT NULL auto_increment,
              `sortid` int(11) default '0',                 # 分类编号
              `order` int(11) default '0',                  # 排序编号
              `title` varchar(255) NOT NULL,                # 标题
              `show` tinyint(1) default '0',                # 显示
              `commend` tinyint(1) default '0',             # 推荐
              `top` tinyint(1) default '0',                 # 置顶
              `img` varchar(255),                           # 图片
              `path` varchar(255) NOT NULL,                 # 路径
              `date` int(11) NOT NULL,                      # 发布时间
              `hits` int(11) NOT NULL default '0',          # 浏览次数
              `keywords` varchar(255),                      # 关键词
              `description` varchar(255),                   # 简述
              PRIMARY KEY  (`id`),
              UNIQUE KEY `path` (`path`),
              KEY `sortid` (`sortid`),
              KEY `show` (`show`),
              KEY `commend` (`commend`),
              KEY `top` (`top`)
            ) ENGINE=MyISAM DEFAULT CHARSET=#~lang~#;
            // 自定义模型
            CREATE TABLE IF NOT EXISTS `#@_archives_model` (
              `modelid` int(11) NOT NULL auto_increment,
              `modelname` varchar(50) NOT NULL,             # 模块名称
              `modelename` varchar(50) NOT NULL,            # 模块E名称
              `maintable` varchar(50) NOT NULL,             # 主索引表
              `addtable` varchar(50) NOT NULL,              # 附加表
              `modelstate` int(11) default '0',             # 状态 0:启用 1:禁用
              PRIMARY KEY  (`modelid`)
            ) ENGINE=MyISAM DEFAULT CHARSET=#~lang~#;
            // 模型字段
            CREATE TABLE IF NOT EXISTS `#@_archives_fields` (
              `fieldid` int(11) NOT NULL auto_increment,
              `modelid` int(11) NOT NULL,                   # 所属模型
              `fieldorder` int(11),                         # 字段排序
              `fieldname` varchar(50),                      # 表单文字
              `fieldename` varchar(50),                     # 字段名
              `fieldtype` varchar(20),                      # 类型
              `fieldlength` varchar(255),                   # 长度
              `fieldefault` varchar(255),                   # 默认值
              `fieldindex` int(11) default '0',             # 是否索引 0:不索引 1:索引
              `inputtype` varchar(20),                      # 输入框类型
              `fieldvalue` varchar(255),                    # radio,checkbox,select 值
              PRIMARY KEY  (`fieldid`),
              KEY `modelid` (`modelid`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=#~lang~#;
            // 分类
            CREATE TABLE IF NOT EXISTS `#@_archives_sort` (
              `sortid` int(11) NOT NULL auto_increment,
              `sortid1` int(11) default '0',                # 所属分类
              `modelid` int(11) default '0',                # 模型编号
              `sortorder` int(11) NOT NULL,                 # 排序
              `sortname` varchar(50) NOT NULL,              # 分类名称
              `sortpath` varchar(255) NOT NULL,             # 路径
              `keywords` varchar(255),                      # meta 关键词
              `description` varchar(255),                   # meta 简述
              `sortopen` int(11) default '0',               # 是否展开 0:关闭 1:展开
              `sorttemplate1` varchar(255),                 # 分类页外模板
              `sorttemplate2` varchar(255),                 # 分类页内模板
              `pagetemplate1` varchar(255),                 # 内容页外模板
              `pagetemplate2` varchar(255),                 # 内容页内模板
              PRIMARY KEY  (`sortid`),
              UNIQUE KEY `sortpath` (`sortpath`),
              KEY `sortid1` (`sortid1`),
              KEY `sortname` (`sortname`),
              KEY `modelid` (`modelid`)
            ) ENGINE=MyISAM DEFAULT CHARSET=#~lang~#;
SQL;
    }
}