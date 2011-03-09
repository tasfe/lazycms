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
 * | Copyright (C) 2007-2010 LazyCMS.com All rights reserved.                  |
 * +---------------------------------------------------------------------------+
 * | LazyCMS is free software. See LICENSE for copyright notices and details.  |
 * +---------------------------------------------------------------------------+
 */
// 加载公共文件
include dirname(__FILE__).'/admin.php';
// 查询管理员信息
$_USER = user_current();
// 动作
$method = isset($_REQUEST['method']) ? $_REQUEST['method'] : null;

switch ($method) {
    // explorer
    case 'explorer':
        $action  = isset($_POST['action'])?$_POST['action']:null;
        if ($action) {
            $listids = isset($_POST['listids'])?$_POST['listids']:null;
            if (empty($listids)) {
                ajax_error(__('Did not select any item.'));
            }
            switch ($action) {
                case 'delete':
                    foreach ($listids as $mediaid) {
                        media_delete($mediaid);
                    }
                    break;
                default:
                    ajax_alert(__('Parameter is invalid.'));
                    break;
            }
        }
        $db     = get_conn();
        $page   = empty($_GET['page']) ? (empty($_POST['page']) ? 1 : $_POST['page']) : $_GET['page'];
        $type   = isset($_REQUEST['type']) ? $_REQUEST['type'] : null;
        $date   = isset($_REQUEST['date']) ? $_REQUEST['date'] : date('Y-m');
        $suffix = isset($_REQUEST['suffix']) ? $_REQUEST['suffix'] : null;
        // 文件夹
        $folders = array();
        $rs = $db->query("SELECT `folder` FROM `#@_media` GROUP BY `folder`;");
        while ($data = $db->fetch($rs)) {
            $folders[$data['folder']] = $data;
        }
        // 日期
        $dates = array();
        $rs = $db->query("SELECT FROM_UNIXTIME(`addtime`,'%Y-%m') AS `date`,SUM(`size`) AS `size`,COUNT(`mediaid`) AS `count` FROM `#@_media` GROUP BY `date`;");
        while ($data = $db->fetch($rs)) {
            $dates[$data['date']] = $data;
        }
        // 后缀
        $suffixs = array();
        $rs = $db->query("SELECT `suffix`,SUM(`size`) AS `size`,COUNT(`mediaid`) AS `count` FROM `#@_media` GROUP BY `suffix`;");
        while ($data = $db->fetch($rs)) {
            $suffixs[$data['suffix']] = $data;
        }

        $hl = '<div class="explorer">';
        $hl.=   '<form method="post" name="explorer" action="'.PHP_FILE.'?method=explorer">';
        $hl.=   '<div class="toobar">';
        $hl.=       '<label for="explor_type">'._x('Type:', 'explor').'</label>';
        $hl.=       '<select id="explor_type" name="type" rel="submit">';
        $hl.=           '<option value="">&mdash; &mdash; &mdash;</option>';
        foreach ($folders as $k=>$v) {
            $selected = $type==$k ? ' selected="selected"' : '';
            $hl.=       '<option value="'.$k.'"'.$selected.'>'.ucfirst($k).'</option>';
        }
        $hl.=       '</select>';
        $hl.=       '<label for="explor_date">'._x('Date:', 'explor').'</label>';
        $hl.=       '<select id="explor_date" name="date" rel="submit">';
        $hl.=           '<option value="">&mdash; &mdash; &mdash;</option>';
        foreach ($dates as $k=>$v) {
            $selected = $date==$k ? ' selected="selected"' : '';
            $hl.=       '<option value="'.$k.'"'.$selected.'>'.$k.'</option>';
        }
        $hl.=       '</select>';
        $hl.=       '<label for="explor_suffix">'._x('Suffix:', 'explor').'</label>';
        $hl.=       '<select id="explor_suffix" name="suffix" rel="submit">';
        $hl.=           '<option value="">&mdash; &mdash;</option>';
        foreach ($suffixs as $k=>$v) {
            $selected = $suffix==$k ? ' selected="selected"' : '';
            $hl.=       '<option value="'.$k.'"'.$selected.'>'.$k.'</option>';
        }
        $hl.=       '</select>';
        $hl.=       '<button type="button" rel="submit">'.__('Refresh').'</button>';
        $hl.=       '<button type="button" rel="delete">'.__('Delete').'</button>';
        $hl.=       '<span class="upload"><button type="button" rel="upload">'.__('Upload').'</button>';
        $hl.=       '<div class="infile"><input class="file" type="file" name="filedata" multiple="true"></div></span>';
        $hl.=   '</div>';
        pages_init($type == 'images' ? 28 : 10, $page);
        $where  = $type ? sprintf(" AND `folder`='%s'", esc_sql($type)) : '';
        $where .= $date ? sprintf(" AND FROM_UNIXTIME(`addtime`,'%%Y-%%m')='%s'", esc_sql($date)) : '';
        $where .= $suffix ? sprintf(" AND `suffix`='%s'", esc_sql($suffix)) : '';
        $size   = get_conn()->result("SELECT SUM(`size`) FROM `#@_media` WHERE 1=1 {$where}");
        $result = pages_query("SELECT * FROM `#@_media` WHERE 1=1 {$where} ORDER BY `mediaid` DESC", true);
        if ($result) {
            $view = $type=='images' ? 'icons' : 'list';
            if ($view == 'list') {
                $hl.= '<table class="data-table" cellspacing="0">';
                $hl.=   '<thead><tr>';
                $hl.=       '<th class="w20 tc"><input type="checkbox" name="select" value="all" /></th>';
                $hl.=       '<th>'._x('Name', 'explor').'</th>';
                $hl.=       '<th class="w100">'._x('Size', 'explor').'</th>';
                $hl.=       '<th class="w50">'._x('Type', 'explor').'</th>';
                $hl.=       '<th class="w150">'._x('Date', 'explor').'</th>';
                $hl.=   '</tr></thead>';
                $hl.=   '<tbody>';
                while ($data = pages_fetch($result)) {
                    $path = media_file($data);
                    $json = array(
                        'id'        => $data['mediaid'],
                        'name'      => $data['name'],
                        'suffix'    => $data['suffix'],
                        'size'      => $data['size'],
                        'url'       => ROOT.$path,
                    );
                    // 计算文件的高宽
                    if (instr($data['suffix'], C('UPIMG-Exts')) || $data['suffix'] == 'swf') {
                        list($width, $height) = getimagesize(ABS_PATH.'/'.$path);
                        if ($width && $height) { $json['width']  = $width; $json['height'] = $height; }
                    }
                    $hl.=   '<tr class="unit">';
                    $hl.=       '<td class="tc"><input type="checkbox" name="listids[]" value="'.$data['mediaid'].'" /><textarea class="hide">'.json_encode($json).'</textarea></td>';
                    $hl.=       '<td class="name"><a href="javascript:;" insert="true" rel="close">'.$data['name'].'</a>';
                    $hl.=       '<a href="'.ADMIN.'media.php?method=down&id='.$data['mediaid'].'">'.get_icon('d2',__('Download')).'</a></td>';
                    $hl.=       '<td>'.format_size($data['size']).'</td>';
                    $hl.=       '<td>'.$data['suffix'].'</td>';
                    $hl.=       '<td>'.date('Y-m-d H:i:s',$data['addtime']).'</td>';
                    $hl.=   '</tr>';
                }
                $hl.=   '</tbody>';
                $hl.= '</table>';
            } elseif ($view == 'icons') {
                $hl.= '<ul class="icons">';
                while ($data = pages_fetch($result)) {
                    $path = media_file($data);
                    $json = array(
                        'id'        => $data['mediaid'],
                        'name'      => $data['name'],
                        'suffix'    => $data['suffix'],
                        'size'      => $data['size'],
                        'url'       => ROOT.$path,
                    );
                    // 计算文件的高宽
                    if (instr($data['suffix'], C('UPIMG-Exts')) || $data['suffix'] == 'swf') {
                        list($width, $height) = getimagesize(ABS_PATH.'/'.$path);
                        if ($width && $height) { $json['width']  = $width; $json['height'] = $height; }
                    }
                    $hl .= '<li class="unit"><a href="javascript:;" insert="true" rel="close"><img src="'.ADMIN.'media.php?method=thumb&id='.$data['mediaid'].'&size=70x60" alt="'.$data['name'].'" /></a>';
                    $hl .= '<div class="mask">&nbsp;</div><div class="actions"><input type="checkbox" name="listids[]" value="'.$data['mediaid'].'" />';
                    $hl .= '<textarea class="hide">'.json_encode($json).'</textarea><a href="'.$json['url'].'" target="_blank">'.get_icon('d4', __('Zoom')).'</a></div></li>';
                }
                $hl.= '<br class="clear" /></ul>';
            }
        }
        $info = pages_info();
        $hl.=   '<div class="botbar">';
        $hl.=       '<div class="info">'.sprintf(__('%d items, totalling %s'), $info['total'], format_size($size)).'</div>';
        $hl.=       pages_list(PHP_FILE.'?method=explorer&page=$');
        $hl.=   '<br class="clear" /></div>';
        $hl.=   '<input type="hidden" name="page" value="'.$page.'">';
        $hl.=   '</form>';
        $hl.= '</div>';
        ajax_return($hl);
        break;
    // 文件上传
    case 'upload':
        // 加载文件上传类
        include_file(COM_PATH.'/system/uploadfile.php');
        $type   = isset($_GET['type']) ? $_GET['type'] : null;
        $result = array('err' => '');
        $upload = new UpLoadFile();
        switch ($type) {
            case 'file':
                $folder = 'files';
                $upload->allow_exts = C('UPFILE-Exts');
                break;
            case 'image':
                $folder = 'images';
                $upload->allow_exts = C('UPIMG-Exts');
                break;
            case 'flash':
                $folder = 'flash';
                $upload->allow_exts = 'swf';
                break;
            case 'video':
                $folder = 'videos';
                $upload->allow_exts = 'flv,mp4';
                break;
            default:
                $result['err'] = __('The uploaded file type is not allowed.');
                break;
        }
        if ($result['err'] == '') {
            $upload->save_path = MEDIA_PATH . '/'.$folder.'/';
            if ($info = $upload->save('filedata')) {
                $error_level = error_reporting(0);
                $result['msg'] = array(
                    'id'     => 0,
                    'size'   => $info['size'],
                    'suffix' => $info['ext'],
                );
                // 文件改名，保存到数据库
                $sha1sum = sha1_file($info['path']);
                // 文件不需要上传
                if ($file = media_no_add($sha1sum)) {
                    // 删除已上传的文件
                    unlink($info['path']);
                    // 修改为新地址
                    $info['path'] = ABS_PATH . '/' . $file;
                    $info['url']  = ROOT . $file;
                    $info['name'] = pathinfo($file, PATHINFO_BASENAME);
                }
                // 文件已存在
                elseif ($media = media_get($sha1sum)) {
                    if (is_file($media['path'])) {
                        // 删除已上传的文件
                        unlink($info['path']);
                    } else {
                        // 修改为新地址
                        mkdirs(dirname($media['path']));
                        rename($info['path'], $media['path']);
                    }
                    // 修改为新地址
                    $info['path'] = $media['path'];
                    $info['url']  = $media['url'];
                    $info['name'] = $media['name'];
                    $result['msg']['id'] = $media['mediaid'];
                }
                // 文件不存在，添加
                elseif ($mediaid = media_add($folder, $sha1sum, $info['name'], $info['size'], $info['ext'])) {
                    $media = media_get($mediaid);
                    // 修改为新地址
                    mkdirs(dirname($media['path']));
                    rename($info['path'], $media['path']);
                    $info['path'] = $media['path'];
                    $info['url']  = $media['url'];
                    $info['name'] = $media['name'];
                    $result['msg']['id'] = $media['mediaid'];
                }
                $result['msg']['name'] = $info['name'];
                switch ($type) {
                    case 'file':
                        $result['msg']['url'] = '!'.$info['url'].'||'.str_replace(chr(32), '%20', $info['name']);
                        break;
                    case 'flash':
                        list($width, $height) = getimagesize($info['path']);
                        $result['msg']['url'] = '!'.$info['url'].'||'.$width.'||'.$height;
                        break;
                    default:
                        $result['msg']['url'] = '!'.$info['url'];
                        break;
                }
                error_reporting($error_level);
            } else {
                $result['err'] = $upload->error();
            }
        }
        ajax_return($result);
        break;
    // 缩略图
    case 'thumb':
        $id   = isset($_GET['id']) ? $_GET['id'] : null;
        $size = isset($_GET['size']) ? $_GET['size'] : null;
        $size = explode('x', $size);
        if ($media = media_get($id)) {
            image_thumb($media['path'], $size[0], $size[1]);
        } else {
            header('Content-type: image/png');
            $img_res   = imagecreatetruecolor($size[0], $size[1]);
            $fontcolor = imagecolorallocate($img_res,128,0,0);
            $bgcolor   = imagecolorallocate($img_res,255,255,255);
            imagefill($img_res,0,0,$bgcolor);
            imagestring($img_res, 5, (imagesx($img_res)-8*3)/2, 15, 'Not', $fontcolor);
            imagestring($img_res, 5, (imagesx($img_res)-8*7)/2, 30, 'Support', $fontcolor);
            imagepng($img_res); imagedestroy($img_res);
        }
        break;
    // 文件下载
    case 'down':
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        if ($media = media_get($id)) {
            $useragent = $_SERVER['HTTP_USER_AGENT'];
            if (is_file($media['path'])) {
                header('Content-type: ' . mime_content_type($media['path']));
                header('Accept-Range : byte ');
                header('Accept-Length: ' . $media['size']);
                if (strpos($useragent, 'MSIE') !== false) {
                    header('Content-Disposition: attachment; filename="'.rawurlencode($media['name']).'"');
                } elseif (strpos($useragent, 'Firefox') !== false) {
                    header('Content-Disposition: attachment; filename*="utf8\' \''.rawurlencode($media['name']).'"');
                } else {
                    header('Content-Disposition: attachment; filename="'.$media['name'].'"');
                }
                readfile($media['path']);
            } else {
                header('Content-type: application/octet-stream');
            }
        }
        break;
}