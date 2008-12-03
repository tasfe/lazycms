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
require '../../global.php';
/**
 * 模型管理
 * 
 */
// *** *** www.LazyCMS.net *** *** //
function lazy_before(){
    System::tabs(
        t('Model manage').':model.php;'.
        t('Model import').':model.php?action=import;'.
        t('Model add list').':model.php?action=edit&type=list;'.
        t('Model add page').':model.php?action=edit&type=page;'
    );
    System::script('LoadScript("content.model");');
}
// *** *** www.LazyCMS.net *** *** //
function lazy_main(){
    System::header(t('Model manage'));
    $db = get_conn();
    $ds = new Recordset();
    $ds->create("SELECT * FROM `#@_content_model` ORDER BY `modelid` ASC");
    $ds->action = PHP_FILE."?action=set";
    $ds->but = $ds->button('unlock:'.t('Unlock').'|lock:'.t('Lock').'');
    $ds->td("cklist(K[0]) + K[0] + ') <a href=\"".PHP_FILE."?action=edit&modelid=' + K[0] + '\">' + K[1] + '</a>'");
    $ds->td("K[2]");
    $ds->td("K[3]");
    $ds->td("(K[4]?icon('tick'):icon('stop'))");
    $ds->td("icon('edit','".PHP_FILE."?action=edit&modelid=' + K[0]) + icon('export','".PHP_FILE."?action=export&model=' + K[2])");
    $ds->open();
    $ds->thead = '<tr><th>ID) '.t('Model name').'</th><th>'.t('Model ename').'</th><th>'.t('Model table').'</th><th>'.t('Model state').'</th><th>'.l('Manage').'</th></tr>';
    while ($rs = $ds->result()) {
        $ds->tbody = "E(".$rs['modelid'].",'".t2js(h2c($rs['modelname']))."','".t2js(h2c($rs['modelename']))."','".t2js(h2c(Content_Model::getDataTableName($rs['modelename'])))."',".$rs['modelstate'].");";
    }
    $ds->close();
    $ds->display();
}
// *** *** www.LazyCMS.net *** *** //
function lazy_set(){
    $db = get_conn();
    $submit = isset($_POST['submit']) ? strtolower($_POST['submit']) : null;
    $lists  = isset($_POST['lists']) ? $_POST['lists'] : null;
    empty($lists) ? echo_json('ALERT',t('model/pop/select')) : null ;
    switch($submit){
        case 'delete':
            $res = $db->query("SELECT `modelename` FROM `#@_content_model` WHERE `modelid` IN({$lists});");
            while ($rs = $db->fetch($res,0)) {
                $db->exec("DROP TABLE IF EXISTS `".Content_Model::getDataTableName($rs[0])."`;");
                $db->exec("DROP TABLE IF EXISTS `".Content_Model::getJoinTableName($rs[0])."`;");
            }
            // 删除模型
            $db->delete('#@_content_model',"`modelid` IN({$lists})");
            // 删除模型和分类的关联关系
            $db->delete('#@_content_sort_model',"`modelid` IN({$lists})");
            alert(t('Model execute delete success'),1);
            break;
        case 'lock': case 'unlock':
            $state = ($submit=='lock') ? 0 : 1;
            $db->update('#@_content_model',array('modelstate' => $state),"`modelid` IN({$lists})");
            alert(t('Model execute success'),0);
            break;
        default :
            alert(l('Error invalid'));
            break;
    }
}
// *** *** www.LazyCMS.net *** *** //
function lazy_export(){
    no_cache(); $db = get_conn();
    $model = isset($_GET['model'])?$_GET['model']:null;
    header("Content-type: application/octet-stream; charset=utf-8");
    header("Content-Disposition: attachment; filename=LazyCMS_{$model}.json");
    $res = $db->query("SELECT * FROM `#@_content_model` WHERE `modelename`=".DB::quote($model).";");
    if ($data = $db->fetch($res)) {
        unset($data['modelid'],$data['modelstate']);
        echo json_encode($data);
    }
}
// *** *** www.LazyCMS.net *** *** //
function lazy_import(){
    $modelcode = isset($_POST['modelcode']) ? $_POST['modelcode'] : null;
    $val = new Validate();
    if ($val->method()) {
        $val->check('modelcode|0|'.t('Model check code'));
        if ($val->isVal()) {
            $val->out();
        } else {
            // 解析xml数据
            $db = get_conn();
            $data = (array) json_decode($modelcode);
            $number = $db->result("SELECT COUNT(*) FROM `#@_content_model` WHERE `modelename`=".DB::quote($data['modelename']).";");
            $isexist= $number > 0 ? false : true;
            $val->check('modelcode|3|'.t('Model check is exist').'|'.$isexist);
            if ($val->isVal()) {
                $val->out();
            } else {
                // 创建模型
                Content_Model::addModel($data);
                alert(t('Model import success'),0);
            }
        }
    }
    System::header(t('Model import'));
    echo '<fieldset><legend><a class="collapsed" rel=".show" cookie="false">'.t('Model import').'</a></legend>';
    echo '<div class="show">';
    echo '<form id="form1" name="form1" method="post" ajax="false" enctype="multipart/form-data" action="'.PHP_FILE.'?action=upmodel" target="tempform">';
    echo '<p><label>'.t('Model import file').':</label><input type="file" name="modelfile" id="modelfile" onchange="$(this).autoUpFile();" class="in4" /></p>';
    echo '</form>';
    echo '<form id="form2" name="form2" method="post" action="'.PHP_FILE.'?action=import">';
    echo '<p><label>'.t('Model import code').':</label><textarea name="modelcode" id="modelcode" rows="20" class="in6"></textarea></p>';
    echo '<p><label>&nbsp;</label><button type="submit">'.t('Model import submit').'</button></p>';
    echo '</form>';
    echo '</div></fieldset>';
}
// *** *** www.LazyCMS.net *** *** //
function lazy_upmodel(){
    import('system.uploadfile');
    $upload = new UpLoadFile();
    $upload->allowExts = 'json';
    $upload->maxSize   = 500*1024;//500K
    $folder = LAZY_PATH.SEPARATOR.C('UPLOAD_FILE_PATH');mkdirs($folder);
    if ($file = $upload->save('modelfile',$folder.'/'.basename($_FILES['modelfile']['name']))) {
        $modelcode = read_file($file['path']); @unlink($file['path']);
        if (is_utf8($modelcode)) {
            $charset = ' charset="utf-8"';
        }
        $msg = 'parent.$(\'#modelcode\').val(\''.t2js($modelcode).'\');';
    } else {
        $charset = ' charset="utf-8"';
        $msg = 'alert(\''.t2js($upload->getError()).'\');';
    }
    header('Content-Type:text/html;'.str_replace('"','',$charset));
    echo '<script type="text/javascript"'.$charset.'>';
    echo 'parent.$(\'input.uploading\').remove();';
    echo 'parent.$(\'#modelfile\').replaceWith(\'<input type="file" name="modelfile" id="modelfile" onchange="$(this).autoUpFile();" class="in4" />\');';
    echo $msg;
    echo 'parent.$(\'iframe[@name=tempform]\').remove();';
    echo '</script>';
    exit();
}
// *** *** www.LazyCMS.net *** *** //
function lazy_after(){
    System::footer();
}