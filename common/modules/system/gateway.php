<?php
/**
 * +---------------------------------------------------------------------------+
 * | LL                             LLLL   LL     L   LLLL               LLLL  |
 * | LL                            LL   L  LLL   LL  LL   L             LL  LL |
 * | LL      LLLL   LLLLL  LL  LL  LL      LLLL LLL  LL         LL  LL      LL |
 * | LL         LL     LL  LL  LL  LL      L LLL LL  LLLLL      LL  LL     LL  |
 * | LL      LLLLL    LL    LLLL   LL      L  L  LL   LLLLL     LL  LL    LL   |
 * | LL     LL  LL   LL     LLLL   LL      L     LL      LL      LLLL    LL    |
 * | LL     LL  LL  LL       LL    LL   L  L     LL  L   LL      LLLL   LL     |
 * | LLLLLL  LLLLL  LLLLL    LL     LLLL   L     LL   LLLL        LL    LLLLLL |
 * |                        LL                                                 |
 * |                        LL                                                 |
 * +---------------------------------------------------------------------------+
 * | Copyright (C) 2007-2008 LazyCMS.net All rights reserved.                  |
 * +---------------------------------------------------------------------------+
 * | LazyCMS is free software. This version use Apache License 2.0             |
 * | See LICENSE.txt for copyright notices and details.                        |
 * +---------------------------------------------------------------------------+
 */
require '../../../global.php';
/**
 * 帮助信息
 *
 */
// *** *** www.LazyCMS.net *** *** //
function lazy_help(){
    System::purview();
    $path = isset($_POST['path'])?$_POST['path']:null;
    if (!strncasecmp($path,'HTML::',6)) {
        $help = substr($path,6);
    } else {
        if (strpos($path,'::')===false) {
            $module = isset($_POST['module'])?$_POST['module']:MODULE;
            $path  = $module.'::help/'.$path;
        }
        $help = t($path);
    }
    ajax_result(array(
        'TITLE' => t('help'),
        'BODY'  => ubbencode($help)
    ));
}
// *** *** www.LazyCMS.net *** *** //
function lazy_keywords(){
    System::purview();
    $result = null;
    $title  = isset($_POST['title']) ? $_POST['title'] : null;
    if (!empty($title)) {
        $keywords = System::getKeywords($title);
        $result = implode(',',$keywords);
    }
    exit($result);
}
// *** *** www.LazyCMS.net *** *** //
function lazy_explorer(){
    System::purview();
    $path  = isset($_POST['path']) ? $_POST['path'] : '/';
    $field = isset($_POST['field']) ? $_POST['field'] : null;
    $exts  = isset($_POST['exts']) ? $_POST['exts'] : '*';
    // 文件夹不存在，则创建
    if (!file_exists(LAZY_PATH.$path)) {
        mkdirs(LAZY_PATH.$path);
    }
    $dirs   = get_dir_array($path,'dir');
    $files  = get_dir_array($path,$exts);
    $paths  = $path=='/'?array(0=>''):explode('/',$path);
    $length = count($paths)-1;
    $hl = '<div id="explorer">';
    $hl.= '<div class="left fl">';
    $hl.= '<div>文件夹</div>';
    $pt = null;
    foreach ($paths as $i=>$v) {
        $pt.= $v.'/';
        $vt = rtrim($pt,'/');
        $hl.= '<p style="padding-left:'.($i*10).'px;">';
        if ($i==0 && empty($v)) {
            $hl.= '<img class="c1 os" src="../system/images/white.gif" /><a href="javascript:;" onclick="$(\''.$field.'\').Explorer(\'/\',\''.$exts.'\');">'.($path=='/'?'<strong>ROOT</strong>':'ROOT').'</a>';
        } elseif ($length == $i) {
            $hl.= '<img class="c3 os" src="../system/images/white.gif" /><a href="javascript:;" onclick="$(\''.$field.'\').Explorer(\''.$vt.'\',\''.$exts.'\');"><strong>'.$v.'</strong></a>';
        } else {
            $hl.= '<img class="c2 os" src="../system/images/white.gif" /><a href="javascript:;" onclick="$(\''.$field.'\').Explorer(\''.$vt.'\',\''.$exts.'\');">'.$v.'</a>';
        }
        $hl.= '</p>';
    }
    
    foreach ($dirs as $v) {
        $spt = $vt.'/'.$v;
        $hl.= '<p style="padding-left:'.(($i+1)*10).'px;"><img class="c2 os" src="../system/images/white.gif" /><a href="javascript:;" onclick="$(\''.$field.'\').Explorer(\''.$spt.'\',\''.$exts.'\');">'.$v.'</a></p>';
    }
    $hl.= '</div>';
    $hl.= '<div class="right fr">';
    if (!empty($files)) {
        $hl.= '<table class="table" cellspacing="0"><thead><tr><td>文件名</td><td>大小</td><td>操作</td></tr></thead><tbody>';
        $folder = LAZY_PATH.($path=='/'?'':$path).'/';
        if ($exts == c('UPLOAD_IMAGE_EXT')) {
            $hl.= '<tr><td colspan="3"><ul class="thum">';
            foreach ($files as $k=>$v) {
                $uf = ansi2utf($v);
                $fz = file_size(filesize($folder.$v));
                $hl.= '<li><table border="0" cellpadding="0" cellspacing="0">';
                $hl.= '<tr><td class="picture"><img src="'.$path.'/'.$uf.'" alt="" onload="$(this).bbimg(70,60);" /></td></tr>';
                $hl.= '<tr><td><div class="name"><a href="javascript:;" src="'.$path.'/'.$uf.'"><img class="e3 os" src="../system/images/white.gif" /></a>'.$uf.'</div></td></tr>';
                $hl.= '</table></li>';
            }
            $hl.= '</ul></td></tr>';
        } else {
            foreach ($files as $k=>$v) {
                $uf = ansi2utf($v);
                $fz = file_size(filesize($folder.$v));
                $hl.= '<tr><td>'.icon($v).$uf.'</td><td>'.$fz.'</td><td><a href="'.$path.'/'.$uf.'" target="_blank">下载</a></td></tr>';
            }    
        }        
        $hl.= '</tbody></table>';
    }
    $hl.= '</div>';
    $hl.= '</div>';
    ajax_result(array(
        'TITLE' => '资源管理器',
        'BODY'  => $hl
    ));
}