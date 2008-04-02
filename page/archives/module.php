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
    // getTopSortId *** *** www.LazyCMS.net *** ***
    static function getTopSortId(){
        $db  = getConn();
        $res = $db->query("SELECT `sortid`,`sortname` FROM `#@_sort` WHERE `sortid1` = '0' ORDER BY `sortorder` DESC,`sortid` DESC;");
        if ($data = $db->fetch($res,0)) {
            return $data[0];
        } else {
            return 0;
        }
    }
    // getModel *** *** www.LazyCMS.net *** ***
    static function getModel($l1){
        $db  = getConn();
        $res = $db->query("SELECT * FROM `#@_sort` AS `s` LEFT JOIN `#@_model` AS `m` ON `s`.`modelid` = `m`.`modelid` WHERE `s`.`sortid` = '{$l1}';");
        if ($data = $db->fetch($res)) {
            return $data;
        } else {
            return false;
        }
    }
    // getFields *** *** www.LazyCMS.net *** ***
    static function getFields($l1){
        $modeid = $l1;
        $fields = array();
        $db    = getConn();
        $where = $db->quoteInto('WHERE `modelid` = ?',$modeid);
        $res   = $db->query("SELECT * FROM `#@_fields` {$where};");
        while ($data = $db->fetch($res)) {
            $fields[] = $data['fieldename'];
        }
        return $fields;
    }
    // __sort *** *** www.LazyCMS.net *** ***
    static function __sort($l1,$l2,$l3=0,$l4=null){
        // $l1:sortid, $l2:current sortid, $l3:Space, $l4:selected
        $nbsp = null; $I1 = null;
        for ($i=0; $i<$l3; $i++) {
            $nbsp .= "&nbsp; &nbsp;";
        }
        $db  = getConn();
        $res = $db->query("SELECT `sortid`,`sortname` FROM `#@_sort` WHERE `sortid1` = '{$l1}' ORDER BY `sortorder` DESC,`sortid` DESC;");
        while ($data = $db->fetch($res,0)) {
            if ($l2 != $data[0]) {
                $selected = ((int)$l4 == (int)$data[0]) ? ' selected="selected"' : null;
                $I1 .= '<option value="'.$data[0].'"'.$selected.'>'.$nbsp.'├ '.$data[1].'</option>';
                if ($db->result("SELECT count(`sortid`) FROM `#@_sort` WHERE `sortid1`='{$data[0]}';") > 0) {
                    $I1 .= self::__sort($data[0],$l2,$l3+1,$l4);
                }
            }
        }
        return $I1;
    }
    // getData *** *** www.LazyCMS.net *** ***
    static function getData($l1,$l2){
        $db    = getConn(); $I1 = array();
        $where = $db->quoteInto('WHERE `aid` = ?',$l1);
        $res   = $db->query("SELECT * FROM `{$l2}` {$where};");
        if (!$data = $db->fetch($res)) {
            return false;
        }
        return $data;
    }
    // __model *** *** www.LazyCMS.net *** ***
    static function __model($l1){
        $db  = getConn(); $I1 = null;
        $res = $db->query("SELECT `modelid`,`modelname`,`modelename` FROM `#@_model` WHERE 1 ORDER BY `modelid` ASC;");
        while ($data = $db->fetch($res,0)) {
            $selected = ($l1 == $data[0]) ? ' selected="selected"' : null;
            $I1 .= '<option value="'.$data[0].'"'.$selected.' name="'.$data[2].'">'.$data[1].'['.$data[2].']</option>';
        }
        return $I1;
    }
    // showSort *** *** www.LazyCMS.net *** ***
    static function showSort($l1){
        $sortid = $l1;
        $db     = getConn();       
        $where  = $db->quoteInto("WHERE `sortid` = ?",$sortid);
        $res    = $db->query("SELECT `sortpath` FROM `#@_sort` {$where}");
        if ($data = $db->fetch($res,0)) {
            if (C('SITE_MODE')) {
                return url('Archives','ShowSort','sortid='.$sortid);
            } else {
                return C('SITE_BASE').$data[0];
            }
        }
    }
    // viewSort *** *** www.LazyCMS.net *** ***
    static function viewSort($l1,$page=1,$type=false,$isCreatePage=false){
        $sortid = $l1; $tmpList = null;
		$page   = !empty($page) ? (int)$page : 1;
        $db     = getConn();
        $model  = self::getModel($sortid);
        $fields = self::getFields($model['modelid']);
        
        $path = self::showSort($sortid);
        $tag  = O('Tags');
        $HTML = $tag->read($model['sorttemplate1'],$model['sorttemplate2']);
        $HTMList = $tag->getList($HTML,$model['modelename'],1);
        $jsHTML  = $tag->getLabel($HTMList,0);
        $jsOrder = $tag->getLabel($HTMList,'order');
        $jsOrder = strtoupper($jsOrder)=='ASC' ? 'ASC' : 'DESC';
        $jsNumber= floor($tag->getLabel($HTMList,'number'));
        $zebra   = $tag->getLabel($HTMList,'zebra');
        $rand    = chr(3).salt(20).chr(2);//随机出来的替换参数
        $randpl  = chr(3).salt(16).chr(2);
		
        // 把 HTML 中的{lazy:...type=list/}标签替换为一个随机的标签；pagelist设置为一个随机标签
        $HTML = str_replace($HTMList,$rand,$HTML);

        // 替换模板中的标签
        $tag->clear();
        $tag->value('title',encode(htmlencode($model['sortname'])));
        $tag->value('sortname',encode(htmlencode($model['sortname'])));
        $tag->value('sortpath',encode($path));
        $tag->value('path',encode($path));
        $tag->value('keywords',encode(htmlencode($model['keywords'])));
        $tag->value('description',encode(htmlencode($model['description'])));
        $tag->value('pagelist',encode($randpl));
        
        $HTML = $tag->create($HTML);

		$strSQL = "SELECT * FROM `".$model['maintable']."` AS `a` LEFT JOIN `".$model['addtable']."` AS `b` ON `a`.`id` = `b`.`aid` WHERE `a`.`sortid` = '{$sortid}' ORDER BY `a`.`order` {$jsOrder},`a`.`sortid` {$jsOrder}";
		$totalRows  = $db->count($strSQL); if ($totalRows==0) { return L('error/rsnot'); }
		$totalPages = ceil($totalRows/$jsNumber);
        $totalPages = ((int)$totalPages == 0) ? 1 : $totalPages;
        if ((int)$page > (int)$totalPages) {
            $page = $totalPages;
        }
		$percent = round($page/$totalPages*100,2);
        $strSQL .= ' LIMIT '.$jsNumber.' OFFSET '.($page-1)*$jsNumber.';';
        $res = $db->query($strSQL);
        $i = 1;
        while ($data = $db->fetch($res)) {
            $tag->clear();
            $tag->value('id',encode($data['id']));
            $tag->value('sortid',encode($data['sortid']));
            $tag->value('sortname',encode(htmlencode($model['sortname'])));
            $tag->value('sortpath',encode($path));
            $tag->value('title',encode(htmlencode($data['title'])));
            $tag->value('path',encode(self::showArchive($data['id'],$model)));
			if (is_file(LAZY_PATH.$data['img'])) {
				$data['img'] = C('SITE_BASE').$data['img'];
			} else {
				$data['img'] = C('SITE_BASE').C('PAGES_PATH').'/system/images/notpic.gif';
			}
			$tag->value('image',encode($data['img']));
            $tag->value('date',encode($data['date']));
            $tag->value('zebra',encode(fmod($zebra,$i) ? 1 : 0));
            foreach ($fields as $k) {
                $tag->value($k,encode($data[$k]));
            }
            $tmpList.= $tag->createhtm($jsHTML);
            $i++;
            if ($isCreatePage && !C('SITE_MODE')) {
                self::viewArchive($sortid,$data['id']);
            }
        }
        $outHTML = str_replace($rand,$tmpList,$HTML);
		$pageExt = C('SITE_MODE') ? '&page=$' : '/index$'.C('HTML_URL_SUFFIX');
		$outHTML = str_replace($randpl,self::pagelist($path.$pageExt,$page,$totalPages,$totalRows),$outHTML);
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
			return $percent;
		} else {
			return $outHTML;
		}
    }
	// pagelist *** *** www.LazyCMS.net *** ***
	static function pagelist($l1,$l2,$l3,$l4){
		// url,page,总页数,记录总数
		// 修要修改分页风格，直接修改此函数即可
		$I1 = null;
		if (strpos($l1,'%24')!==false) { $l1 = str_replace('%24','$',$l1); }
		if (strpos($l1,'$')==0 || $l4==0) { return ; }
		$l7 = C('SITE_MODE') ? 1 : null;
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
        $aid   = $l1;
        $model = $l2;
        $db    = getConn();       
        $where = $db->quoteInto("WHERE `b`.`id` = ?",$aid);
        $res   = $db->query("SELECT `a`.`sortpath`,`b`.`path` FROM `#@_sort` AS `a` LEFT JOIN `".$model['maintable']."` AS `b` ON `a`.`sortid` = `b`.`sortid` {$where}");
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
                return C('SITE_BASE').$data[0].'/'.$l4;
            }
        }
    }
	// viewArchive *** *** www.LazyCMS.net *** ***
	static function viewArchive($l1,$l2,$l3=1){
		$sortid = $l1; $aid = $l2; $page = $l3; $db = getConn();
		$model  = self::getModel($sortid);
		$where = $db->quoteInto('WHERE `id` = ?',$aid);
        $res   = $db->query("SELECT * FROM `".$model['maintable']."` AS `a` LEFT JOIN `".$model['addtable']."` AS `b` ON `a`.`id` = `b`.`aid` {$where};");
        if ($data = $db->fetch($res)) {
			$tag  = O('Tags');
			$HTML = $tag->read($model['pagetemplate1'],$model['pagetemplate2']);
			// 替换模板中的标签
			$tag->clear();
			$tag->value('id',encode($data['id']));
            $tag->value('sortid',encode($data['sortid']));
			$tag->value('title',encode(htmlencode($data['title'])));
			$tag->value('sortname',encode(htmlencode($model['sortname'])));
			$tag->value('sortpath',encode(self::showSort($sortid)));
			if (is_file(LAZY_PATH.$data['img'])) {
				$data['img'] = C('SITE_BASE').$data['img'];
			} else {
				$data['img'] = C('SITE_BASE').C('PAGES_PATH').'/system/images/notpic.gif';
			}
			$tag->value('image',encode($data['img']));
			$tag->value('date',encode($data['date']));
			
			$fields = self::getFields($model['modelid']);
            // 有编辑器，动态模式，分页
			$result = $db->query("SELECT * FROM `#@_fields` WHERE `modelid` ='".$model['modelid']."' AND `inputtype`='editor';");
			if ($field = $db->fetch($result)){
				$contents = explode(C('WEB_BREAK'),$data[$field['fieldename']]);
				$length   = count($contents);
				// 动态模式，只浏览不生成
				if (C('SITE_MODE')) {
					$page = (int)$page > (int)$length ? $length : $page;
					$tag->value('path',encode(self::showArchive($data['id'],$model,$page)));
					$tag->value('pagelist',encode(self::pagelists(self::showArchive($data['id'],$model,'$'),$length,$page)));
					$tag->value($field['fieldename'],encode($contents[$page-1]));
					return $tag->create($HTML);
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
                    if ((int)$page > 1) {
                        $tag->value($field['fieldename'],encode($contents[$page-1]));
                    } else {
                        $tag->value($field['fieldename'],encode($contents[$i]));
                    }
                    $outHTML = $tag->create($HTML);
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
            $outHTML = $tag->create($HTML);

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
            if (strpos($l2,'/')!==false){
                $path = substr($l2,0,strlen($l2)-strlen($paths[$count-1]));
                mkdirs(LAZY_PATH.$l1.'/'.$path);
            }
            mkdirs(LAZY_PATH.$l1);
            saveFile(LAZY_PATH.$l1.'/'.$l2,$l3);
        } else { //目录
            mkdirs(LAZY_PATH.$l1.'/'.$l2);
            saveFile(LAZY_PATH.$l1.'/'.$l2.'/'.C('SITE_INDEX'),$l3);
        }
    }
    // isOpen *** *** www.LazyCMS.net *** ***
    static function isOpen($l1){
        $db    = getConn();
        $state = $db->result("SELECT `sortopen` FROM `#@_sort` WHERE `sortid` = '{$l1}';");
        $state = (string)$state == "1" ? 'true' : 'false';
        return $state;
    }
    // isSub *** *** www.LazyCMS.net *** ***
    static function isSub($l1){
        $db    = getConn();
        $state = $db->result("SELECT count(*) FROM `#@_sort` WHERE `sortid1` = '{$l1}';") > 0 ? '1' : '2';
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
    // tags *** *** www.LazyCMS.net *** ***
    static function tags($tags,$inValue){ 
		$inSQL = null; $tmpList = null; $db = getConn();
		$tagName = sect($tags,"(lazy\:)","( |\/|\}|\))","");
		// 根据tagName 取得modelid
		$where = $db->quoteInto("WHERE `modelename` = ?",$tagName);
		$res   = $db->query("SELECT * FROM `#@_model` {$where};");
		if ($model = $db->fetch($res)) {
			$fields  = self::getFields($model['modelid']);
			$HTMList = $tags; $tag = O('Tags');
			$jsHTML  = $tag->getLabel($HTMList,0);
			$jsType  = $tag->getLabel($HTMList,'type');
			$jsNumber= floor($tag->getLabel($HTMList,'number'));
			$sortid  = $tag->getLabel($HTMList,'sortid');
			$zebra   = $tag->getLabel($HTMList,'zebra');
			if (validate($sortid,6)) {
				$inSQL = " `m`.`sortid` IN({$sortid})";
			} else {
				$sortname = $tag->getLabel($HTMList,'sortname');
				if (strlen($sortname) > 0) {
					$inSQL = $db->quoteInto(" `s`.`sortname` = ?",$sortname);
				}
			}
			$select = "SELECT * FROM `".$model['maintable']."` AS `m`
						LEFT JOIN `".$model['addtable']."` AS `a` ON `m`.`id` = `a`.`aid`
						LEFT JOIN `#@_sort` AS `s` ON `s`.`sortid` = `m`.`sortid`";

			if (strlen($inSQL) > 0) { $inSQL = " WHERE ".$inSQL; }
			switch (strtolower($jsType)) {
				case 'hot':// 热门文章
					$strSQL = $select." {$inSQL} ORDER BY `m`.`hits` DESC, `m`.`order` DESC,`m`.`id` DESC";
					break;
				case 'chill':// 冷门文章
					$strSQL = $select." {$inSQL} ORDER BY `m`.`hits` ASC, `m`.`order` DESC,`m`.`id` DESC";
					break;
				default : // 最新文章
                    $strSQL = $select." {$inSQL} ORDER BY `m`.`order` DESC,`m`.`id` DESC";
					break;
			}
			$strSQL.= " LIMIT 0,{$jsNumber};";
			$rs = $db->query($strSQL);
			$i  = 1;
			while ($data = $db->fetch($rs)) {
				$tag->clear();
				$tag->value('id',encode($data['id']));
				$tag->value('sortid',encode($data['sortid']));
				$tag->value('sortname',encode(htmlencode($data['sortname'])));
				$tag->value('sortpath',encode(self::showSort($data['sortid'])));
				$tag->value('title',encode(htmlencode($data['title'])));
				$tag->value('path',encode(self::showArchive($data['id'],self::getModel($data['sortid']))));
				if (is_file(LAZY_PATH.$data['img'])) {
					$data['img'] = C('SITE_BASE').$data['img'];
				} else {
					$data['img'] = C('SITE_BASE').C('PAGES_PATH').'/system/images/notpic.gif';
				}
				$tag->value('image',encode($data['img']));
				$tag->value('date',encode($data['date']));
				$tag->value('zebra',encode(fmod($zebra,$i) ? 1 : 0));
				$tag->value('++',$i);
				foreach ($fields as $k) {
					$tag->value($k,encode($data[$k]));
				}
				$tmpList.= $tag->createhtm($jsHTML);
				$i++;
			}
		}
		return $tmpList;
    }
    // instsql *** *** www.LazyCMS.net *** ***
    static function instSQL(){
        return ;
    }
}