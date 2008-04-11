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
	// getSubSortIds *** *** www.LazyCMS.net *** ***
	static function getSubSortIds($l1){
		$I1  = $l1;
		$db  = getConn();
        $res = $db->query("SELECT `sortid` FROM `#@_sort` WHERE ".$db->quoteInto('`sortid1` = ?',$l1));
        while ($data = $db->fetch($res,0)) {
			if ($db->count("SELECT count(`sortid`) FROM `#@_sort` WHERE `sortid1`= '".$data[0]."';") > 0) {
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
        $res = $db->query("SELECT `modelid`,`modelname`,`modelename` FROM `#@_model` WHERE `modelstate`='0' ORDER BY `modelid` ASC;");
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
        $where  = $db->quoteInto("WHERE `sortid` = ?",$sortid);
        $res    = $db->query("SELECT `sortpath` FROM `#@_sort` {$where}");
        if ($data = $db->fetch($res,0)) {
            if (C('SITE_MODE')) {
                return url('Archives','ShowSort','sortid='.$sortid);
            } else {
                return C('SITE_BASE').$data[0];
            }
        } else {
            return C('SITE_BASE');
        }
    }
	// guide *** *** www.LazyCMS.net *** ***
	function guide($l1){
		if (empty($l1)) { return ;}
		$I1 = null; $db = getConn();
		$res = $db->query("SELECT `sortid1`,`sortname`,`sortpath` FROM `#@_sort` WHERE `sortid`='{$l1}';");
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
            $pageExt = C('SITE_MODE') ? '&page=$' : '/index$'.C('HTML_URL_SUFFIX');
            $outHTML = str_replace($randpl,self::pagelist($path.$pageExt,$page,$totalPages,$totalRows),$outHTML);
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
        if (is_numeric($l2)) {
            $model = self::getModel($l2);
        } else {
            $model = $l2;
        }
        $aid   = $l1;
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
			$tag->value('id',$data['id']);
            $tag->value('sortid',$data['sortid']);
			$tag->value('title',encode(htmlencode($data['title'])));
			$tag->value('sortname',encode(htmlencode($model['sortname'])));
			$tag->value('sortpath',encode(self::showSort($sortid)));
			$tag->value('image',encode($data['img']));
			$tag->value('date',$data['date']);
            $tag->value('keywords',encode(htmlencode($data['keywords'])));
            $tag->value('description',encode(htmlencode($data['description'])));
			$tag->value('guide',encode(self::guide($data['sortid'])." &gt;&gt; ".htmlencode($data['title'])));
			$tag->value('hits',encode("<span class=\"lz_hits\"><script type=\"text/javascript\">\$('.lz_hits').html(loadgif()).load('".url('Archives','hits','sortid='.$sortid.'&id='.$data['id'])."');</script></span>"));
            $tag->value('lastpage',encode(self::lastPage($data,$model,$HTML)));
            $tag->value('nextpage',encode(self::nextPage($data,$model,$HTML)));
			
			$fields = self::getFields($model['modelid']);
            
            // 有编辑器，动态模式，分页，只对第一个编辑器进行处理
			$result = $db->query("SELECT * FROM `#@_fields` WHERE `modelid` ='".$model['modelid']."' AND `inputtype`='editor' ORDER BY `fieldorder` ASC, `fieldid` ASC;");
			if ($field = $db->fetch($result)){
				$contents = explode(C('WEB_BREAK'),$data[$field['fieldename']]);
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
                    if ((int)$page > 1) {
                        $tag->value($field['fieldename'],encode($contents[$page-1]));
                    } else {
                        $tag->value($field['fieldename'],encode($contents[$i]));
                    }
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
    // lastPage *** *** www.LazyCMS.net *** ***
    static function lastPage($l1,$l2,$l3){
        if (strpos($l3,'{lazy:lastpage')!==false) {
            $data = $l1; $model = $l2; $HTML = $l3;
            $db  = getConn();
            $res = $db->query("SELECT `title`,`path`,`id` FROM `".$model['maintable']."` WHERE `show`=1 AND `sortid`='".$model['sortid']."' AND `order`<".$data['order']." ORDER BY `top` DESC,`order` DESC,`id` DESC LIMIT 0,1;");
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
            $res = $db->query("SELECT `title`,`path`,`id` FROM `".$model['maintable']."` WHERE `show`=1 AND `sortid`='".$model['sortid']."' AND `order`>".$data['order']." ORDER BY `top` ASC,`order` ASC,`id` ASC LIMIT 0,1;");
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
		$tagName = sect($tags,"(lazy\:)","( |\/|\}|\))");
        $HTMList = $tags; $tag = O('Tags');
        $jsHTML  = $tag->getLabel($HTMList,0);
        $sortid  = $tag->getLabel($HTMList,'sortid');
        $jsType  = strtolower($tag->getLabel($HTMList,'type'));
        // 如果是请求链接直接返回链接地址
        if ($jsType=='link') { return self::showSort($sortid); }

        $jsNumber= floor($tag->getLabel($HTMList,'number'));
        $remove  = $tag->getLabel($HTMList,'remove');
        $zebra   = $tag->getLabel($HTMList,'zebra');

		// 根据tagName 取得modelid
		$where = $db->quoteInto("WHERE `modelename` = ?",$tagName);
		$res   = $db->query("SELECT * FROM `#@_model` {$where};");
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
			$select = "SELECT * FROM `".$model['maintable']."` AS `m`
						LEFT JOIN `".$model['addtable']."` AS `a` ON `m`.`id` = `a`.`aid`
						LEFT JOIN `#@_sort` AS `s` ON `s`.`sortid` = `m`.`sortid` WHERE `s`.`modelid`='".$model['modelid']."' AND `m`.`show` = 1 ";

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
        $db       = getConn();
        $modelDom = DOMDocument::loadXML($modelCode);
        $XPath    = new DOMXPath($modelDom);
        // Model Value
        $data[0] = $XPath->evaluate("//lazycms/model/modelname")->item(0)->nodeValue;
        $data[1] = $XPath->evaluate("//lazycms/model/modelename")->item(0)->nodeValue;
        $data[2] = '#@_'.$XPath->evaluate("//lazycms/model/maintable")->item(0)->nodeValue;
        $data[3] = '#@_'.$XPath->evaluate("//lazycms/model/addtable")->item(0)->nodeValue;
        $data[4] = $XPath->evaluate("//lazycms/model/modelstate")->item(0)->nodeValue;
        if (!$isDeleteTable) {
            if ($db->isTable($data[3])) {
                $salt = salt(4);
				$data[1].= '_'.$salt;
				$data[3].= '_'.$salt;
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
        $db->insert('#@_model',$row);

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
                'fieldorder' => $db->max('fieldid','#@_fields'),
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
            $db->insert('#@_fields',$row);
        }
        $db->exec("DROP TABLE IF EXISTS `".$data[3]."`;");
        // 创建新表
        $db->exec("CREATE TABLE IF NOT EXISTS `".$data[3]."` (
                    `aid` int(11) NOT NULL,
                    {$inSQL}{$indexSQL}
                    PRIMARY KEY (`aid`)
                   ) ENGINE=MyISAM DEFAULT CHARSET=#~lang~#;");
    }
    // instsql *** *** www.LazyCMS.net *** ***
    static function instSQL(){
        return <<<SQL
            // 公共存档
            DROP TABLE IF EXISTS `#@_archives`;
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
			  `hits` int(11) NOT NULL default '0',			# 浏览次数
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
            DROP TABLE IF EXISTS `#@_model`;
            CREATE TABLE IF NOT EXISTS `#@_model` (
              `modelid` int(11) NOT NULL auto_increment,
              `modelname` varchar(50) NOT NULL,             # 模块名称
              `modelename` varchar(50) NOT NULL,            # 模块E名称
              `maintable` varchar(50) NOT NULL,             # 主索引表
              `addtable` varchar(50) NOT NULL,              # 附加表
              `modelstate` int(11) default '0',             # 状态 0:启用 1:禁用
              PRIMARY KEY  (`modelid`)
            ) ENGINE=MyISAM DEFAULT CHARSET=#~lang~#;
            // 模型字段
            DROP TABLE IF EXISTS `#@_fields`;
            CREATE TABLE IF NOT EXISTS `#@_fields` (
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
            DROP TABLE IF EXISTS `#@_sort`;
            CREATE TABLE IF NOT EXISTS `#@_sort` (
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