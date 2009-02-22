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
 * 系统公用入口
 *
 */
// *** *** www.LazyCMS.net *** *** //
function lazy_help(){
    no_cache(); System::purview();
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
    no_cache(); System::purview();
    $result = null;
    $title  = isset($_POST['title']) ? $_POST['title'] : null;
    if (!empty($title)) {
        $keywords = System::getKeywords($title);
        $result = implode(',',$keywords);
    }
    exit($result);
}
// *** *** www.LazyCMS.net *** *** //
function lazy_explorer_delete(){
    no_cache(); System::purview();
    $file = isset($_POST['file']) ? $_POST['file'] : null;
    if (!empty($file)) {
        if (is_file(LAZY_PATH.$file)) {
            unlink(LAZY_PATH.$file);
        }
    }
    ajax_result(true);
}
// *** *** www.LazyCMS.net *** *** //
function lazy_explorer_create(){
    no_cache(); System::purview();
    $path   = isset($_REQUEST['path']) ? $_REQUEST['path'] : '/';
    $folder = isset($_POST['folder']) ? $_POST['folder'] : null;
    $rPath  = LAZY_PATH.$path.'/'.$folder;
    $val = new Validate();
    if ($val->method()) {
        $val->check('folder|0|'.t('files/check/folder').';folder|5|'.t('files/check/folder1').';folder|3|'.t('files/check/folder2').'|'.!file_exists($rPath));
        if ($val->isVal()) {
            $val->out();
        } else {
            mkdirs($rPath);
            ajax_result(true);
        }
    }
    $hl = '<form id="CreateFolder" name="CreateFolder" method="post" action="'.PHP_FILE.'?action=explorer_create">';
    $hl.= '<p><label>'.t('files/folder').':</label><input class="in w200" help="system::help/files/folder" type="text" name="folder" id="folder" value="" /></p>';
    $hl.= '<input name="path" type="hidden" value="'.$path.'" />';
    $hl.= '<div class="tr"><button type="submit">'.t('system::save').'</button><button type="button" rel="cancel">'.t('system::cancel').'</button></div>';
    $hl.= '</form>';
    ajax_result(array(
        'TITLE' => t('files/create/folder'),
        'BODY'  => $hl
    ));
}
// *** *** www.LazyCMS.net *** *** //
function lazy_explorer_uploadfile(){
    no_cache(); System::purview(); $success = true;
    $path  = isset($_POST['path']) ? $_POST['path'] : '/';
    $field = isset($_POST['field']) ? $_POST['field'] : null;
    $type  = isset($_POST['exts']) ? $_POST['exts'] : '*';
    $exts  = $type=='*' ? '*' : c($type);
    import('system.uploadfile');
    $upload = new UpLoadFile();
    $upload->allowExts = $exts;
    $upload->maxSize   = c('UPLOAD_MAX_SIZE') * 1024;// 单位KB
    $upload->savePath  = $path;
    $result = $upload->saves();
    header('Content-Type:text/html; charset="utf-8";');
    $error = null;
    foreach ($result as $k=>$v) {
        if (!is_array($v)) {
            $success = false;
            $error.= '<p><label>'.t('files/file').' '.substr($k,-1).':</label><div class="in fl">'.$v.'</div></p>';
        }
    }
    $back = 'parent.$(\''.$field.'\').Explorer(\''.$path.'\',\''.$type.'\');';
    if ($success) {
        $msg = $back;
    } else {
        $msg = 'parent.$.dialogUI({';
        $msg.= '    style:{width:"400px"},close:false,name:"upresult",';
        $msg.= '    title:"'.t('upload').'",body:"'.t2js($error).'",buttons:[{';
        $msg.= '        text:"'.t('ok').'",';
        $msg.= '        handler:function(){';
        $msg.= '            this.remove();'.$back;
        $msg.= '        }';
        $msg.= '    }]';
        $msg.= '});';
    }
    echo '<script type="text/javascript" charset="utf-8">'.$msg.'</script>';
}
// *** *** www.LazyCMS.net *** *** //
function lazy_explorer_image(){
    $file = isset($_REQUEST['file']) ? $_REQUEST['file'] : null;
    if (!empty($file)) {
        $file = LAZY_PATH.utf2ansi($file);
        // 判断文件类型是否合法
        if (!instr(c('UPLOAD_IMAGE_EXT'),pathinfo($file,PATHINFO_EXTENSION))) {
            header('Cache-Control: max-age='.(30*365*24*60*60));
            header("Content-type: image/png");
            $img = imagecreatetruecolor(70,60);
            $col = imagecolorallocate($img,128,0,0);
            imagefill($img,0,0,imagecolorallocate($img,255,255,255));
            imagestring($img, 5, (imagesx($img)-8*2)/2, 15, "No", $col);
            imagestring($img, 5, (imagesx($img)-8*7)/2, 30, "Access!", $col);
            imagepng($img); imagedestroy($img);
            return ;
        }
        import('system.images');
        $Info = Images::getImageInfo($file);
        header('Content-Type: '.$Info['mime']);
        $thumb = dirname($file).'/.Thumbs/'.pathinfo($file,PATHINFO_BASENAME);
        if (!is_file($thumb) || (filemtime($file) != filemtime($thumb))) {
            if ((int)$Info['width'] > 70 || (int)$Info['height'] > 60) {
                Images::thumb($file,$thumb,70,60);
            } else {
                mkdirs(dirname($thumb)); copy($file,$thumb);
            }
            $time = filemtime($file); touch($file,$time); touch($thumb,$time);
        }
        
        // 判断缩略图是否存在
        if (is_file($thumb)) {
            no_cache(); readfile($thumb);
        } else {
            header('Cache-Control: max-age='.(24*60*60));
            readfile($file);
        }
    }
}
// *** *** www.LazyCMS.net *** *** //
function lazy_explorer(){
    no_cache(); System::purview();
    $path  = isset($_POST['path']) ? $_POST['path'] : '/';
    $field = isset($_POST['field']) ? $_POST['field'] : null;
    $type  = isset($_POST['exts']) ? $_POST['exts'] : '*';
    $exts  = $type=='*' ? '*' : c($type);
    $CMD   = isset($_POST['CMD']) ? $_POST['CMD'] : null;

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
    $hl.= '<div>'.t('files/folder').'</div>';
    $pt = null;
    foreach ($paths as $i=>$v) {
        $pt.= $v.'/';
        $vt = rtrim($pt,'/');
        $hl.= '<p style="padding-left:'.($i*10).'px;">';
        if ($i==0 && empty($v)) {
            $hl.= '<img class="c1 os" src="../system/images/white.gif" /><a href="javascript:;" onclick="$(\''.$field.'\').Explorer(\'/\',\''.$type.'\');">'.($path=='/'?'<strong>ROOT</strong>':'ROOT').'</a>';
        } elseif ($length == $i) {
            $hl.= '<img class="c3 os" src="../system/images/white.gif" /><a href="javascript:;" onclick="$(\''.$field.'\').Explorer(\''.$vt.'\',\''.$type.'\');"><strong>'.$v.'</strong></a>';
        } else {
            $hl.= '<img class="c2 os" src="../system/images/white.gif" /><a href="javascript:;" onclick="$(\''.$field.'\').Explorer(\''.$vt.'\',\''.$type.'\');">'.$v.'</a>';
        }
        $hl.= '</p>';
    }
    foreach ($dirs as $v) {
        $spt = $vt.'/'.$v;
        $hl.= '<p style="padding-left:'.(($i+1)*10).'px;"><img class="c2 os" src="../system/images/white.gif" /><a href="javascript:;" onclick="$(\''.$field.'\').Explorer(\''.$spt.'\',\''.$type.'\');">'.$v.'</a></p>';
    }
    $hl.= '</div>';
    $hl.= '<div class="right fr">';
    $hl.= '<table class="table" cellspacing="0">';
    if ($CMD == 'upload') {
        $hl.= '<thead><tr><th>'.t('upload').'['.$exts.']</th></tr></thead>';
    } else {
        $hl.= '<thead><tr><td>'.t('files/name').'</td><td>'.t('files/size').'</td><td class="tr"><a href="javascript:;" onclick="$(\''.$field.'\').UpLoadFile(\''.$path.'\',\''.$type.'\');"><img class="e5 os" src="../system/images/white.gif" /></a><a href="javascript:;" onclick="$(\''.$field.'\').CreateFolder(\''.$path.'\',\''.$type.'\');"><img class="e4 os" src="../system/images/white.gif" /></a></td></tr></thead>';
    }
    $hl.= '<tbody>';
    if (empty($CMD)) {
        if (!empty($files)) {
            $folder = LAZY_PATH.($path=='/'?'':$path).'/';
            if ($exts == c('UPLOAD_IMAGE_EXT')) {
                $hl.= '<tr><td colspan="3"><ul class="thumb">';
                foreach ($files as $k=>$v) {
                    $uf = ansi2utf($v);
                    $fz = file_size(filesize($folder.$v));
                    $thumb = LAZY_PATH.$path.'/.Thumbs/'.$uf;
                    $src= (is_file(utf2ansi($thumb)) && filemtime(LAZY_PATH.$path.'/'.$uf) == filemtime($thumb)) ? $path.'/.Thumbs/'.$uf : PHP_FILE.'?action=explorer_image&file='.rawurlencode($path.'/'.$uf);
                    $hl.= '<li><table border="0" cellpadding="0" cellspacing="0" title="'.$uf.'">';
                    $hl.= '<tr><td class="picture" rel="preview" src="'.$path.'/'.$uf.'"><img src="'.$src.'" onload="$(this).bbimg(70,60);" alt="'.$uf.'" /></td></tr>';
                    $hl.= '<tr><td><div class="name"><a href="javascript:;" src="'.$path.'/'.$uf.'" rel="insert"><img class="e3 os" src="../system/images/white.gif" /></a>'.$uf.'</div></td></tr>';
                    $hl.= '</table></li>';
                }
                $hl.= '</ul></td></tr>';
            } else {
                foreach ($files as $k=>$v) {
                    $uf = ansi2utf($v);
                    $fz = file_size(filesize($folder.$v));
                    $hl.= '<tr><td rel="preview" src="'.$path.'/'.$uf.'" title="'.$uf.'"><div class="filename">'.icon($v).$uf.'</div></td><td>'.$fz.'</td>';
                    $hl.= '<td><a href="javascript:;" src="'.$path.'/'.$uf.'" rel="insert"><img class="e3 os" src="../system/images/white.gif" /></a>';
                    $hl.= '<a href="javascript:;" src="'.$path.'/'.$uf.'" rel="delete"><img class="e7 os" src="../system/images/white.gif" /></a></td></tr>';
                }    
            }
        }
    } else {
        $hl.= '<tr><td><form action="'.PHP_FILE.'?action=explorer_uploadfile" method="post" enctype="multipart/form-data" name="form1" target="UpLoadFile" id="form1">';
        for ($i=1; $i<=5; $i++) {
            $hl.= '<p><label>'.t('files/file').' '.$i.':</label><input class="in w250" type="file" name="files_'.$i.'" /></p>';
        }
        $hl.= '<p class="buttons"><button type="submit"> '.t('system::upload').' </button><button type="button" onclick="$(\''.$field.'\').Explorer(\''.$path.'\',\''.$type.'\');"> '.t('system::back').' </button></p>';
        $hl.= '<iframe src="about:blank" name="UpLoadFile" width="0" height="0" marginwidth="0" marginheight="0" align="middle" scrolling="no" frameborder="0"></iframe>';
        $hl.= '<input name="field" type="hidden" value="'.$field.'" /><input name="path" type="hidden" value="'.$path.'" /><input name="exts" type="hidden" value="'.$type.'" />';
        $hl.= '</form></td></tr>';
    }
    $hl.= '</tbody></table>';
    $hl.= '</div>';
    $hl.= '</div>';
    ajax_result(array(
        'TITLE' => t('files'),
        'BODY'  => $hl
    ));
}