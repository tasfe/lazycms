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
    $ds->but = $ds->button('unlock:'.l('Unlock').'|lock:'.l('Lock').'');
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
    empty($lists) ? alert(t('Model alert not select')) : null ;
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
function lazy_edit(){
    $db = get_conn();
    $modelid   = isset($_REQUEST['modelid']) ? $_REQUEST['modelid'] : 0;
    $oldename  = isset($_POST['oldename']) ? strtolower($_POST['oldename']) : null;
    $delFields = isset($_POST['delFields']) ? $_POST['delFields'] : null;
    $modeltype = isset($_REQUEST['type']) ? strtolower($_REQUEST['type']) : 'list';
    $post = POST('modelname','modelename','modelpath','modeltype','modelfields','setkeyword','description','sortemplate','pagetemplate');
    $modeltype = empty($post[3]) ? $modeltype : $post[3];
    if (is_array($post[4])) {
        $post[4] = '['.implode(',',$post[4]).']';
    }
    $val = new Validate();
    if ($val->method()) {
        $val->check('modelname|1|'.t('Model check name').'|1-50');
        $val->check('modelename|1|'.t('Model check ename').'|1-50;modelename|validate|'.t('Model check ename is Special characters').'|3;modelename|4|'.t('Model check ename is repeat')."|SELECT COUNT(`modelid`) FROM `#@_content_model` WHERE `modelename`='#pro#'".(empty($modelid)?null:" AND `modelid` <> {$modelid}"));
        if ($val->isVal()) {
            $val->out();
        } else {
            if (empty($modelid)) {
                $modelid = Content_Model::addModel(array(
                    'modelname'   => $post[0],
                    'modelename'  => $post[1],
                    'modelpath'   => $post[2],
                    'modeltype'   => $post[3],
                    'modelfields' => $post[4],
                    'setkeyword'  => $post[5],
                    'description' => $post[6],
                    'sortemplate' => $post[7],
                    'pagetemplate'=> $post[8],
                ));
                $text = t('Model alert add success');
            } else {
                // 数据表名
                $table  = Content_Model::getDataTableName($post[1]);
                // 关联表名
                $jtable = Content_Model::getJoinTableName($post[1]);
                // 解析字段
                $fields = json_decode($post[4]);
                // 模型名称被修改，修改数据结构
                if ($oldename!=$post[1]) {
                    $otable  = Content_Model::getDataTableName($oldename);
                    $ojtable = Content_Model::getJoinTableName($oldename);
                    $db->exec("RENAME TABLE `{$otable}` TO `{$table}`;");
                    $db->exec("RENAME TABLE `{$ojtable}` TO `{$jtable}`;");
                }
                // 删除不需要的字段
                if (!empty($delFields)) {
                    $arrFields = explode(',',$delFields); $sute = null;
                    foreach ($arrFields as $field) {
                        if ($db->isField($table,$field)) {
                            $sute.= " DROP `{$field}`,";
                        }
                    }
                    $sute = rtrim($sute,',');
                    $db->exec("ALTER TABLE `{$table}` {$sute};");
                }
                // 组成修改数据结构的SQL
                $post[4] = $sute = array();
                $chang = $add = array();
                foreach ($fields as $v) {
                    $data = (array) $v; $k = $data['ename'];
                    $type = Content_Model::getType($data['intype']);
                    $sute[$k]['len']  = empty($data['length'])?null:'('.$data['length'].')';
                    $sute[$k]['type'] = strpos($type,')')===false ? $type.$sute[$k]['len'] : $type;
                    $sute[$k]['def']  = empty($data['default'])?null:" DEFAULT '".$data['default']."'";
                    $sute[$k]['add'] = " ADD `".$data['ename']."` ".$sute[$k]['type']." ".$sute[$k]['def'];
                    if ($data['ename']!==$data['oname']) {
                        $chang[] = " CHANGE `".$data['oname']."` `".$data['ename']."` ".$sute[$k]['type']." ".$sute[$k]['def'];
                        $data['oname'] = $data['ename'];
                    }
                    $post[4][] = $data;
                }
                // 修改现有字段的结构
                if (!empty($chang)) {
                    $chang = implode(',',$chang);
                    $db->exec("ALTER TABLE `{$table}` {$chang};");
                }
                // 添加新增的字段
                foreach ($post[4] as $data) {
                    if (!$db->isField($table,$data['ename'])) {
                        $add[] = $sute[$data['ename']]['add'];
                    }
                }
                if (!empty($add)) {
                    $add = implode(',',$add);
                    $db->exec("ALTER TABLE `{$table}` {$add};");
                }
                // 更新数据
                $db->update('#@_content_model',array(
                    'modelname'   => $post[0],
                    'modelename'  => $post[1],
                    'modelpath'   => $post[2],
                    'modeltype'   => $post[3],
                    'modelfields' => json_encode($post[4]),
                    'setkeyword'  => $post[5],
                    'description' => $post[6],
                    'sortemplate' => $post[7],
                    'pagetemplate'=> $post[8],
                ),DB::quoteInto('`modelid` = ?',$modelid));
                $text = t('Model alert edit success');
            }
            // 输出执行结果
            alert($text,0);
        }
    } else {
        if (!empty($modelid)) {
            $res = $db->query("SELECT * FROM `#@_content_model` WHERE `modelid`=?",$modelid);
            if ($rs = $db->fetch($res)) {
                $post[0] = h2c($rs['modelname']);
                $post[1] = h2c($rs['modelename']);
                $post[2] = $rs['modelpath'];
                $post[3] = $rs['modeltype'];
                $post[4] = empty($rs['modelfields'])?array():json_decode($rs['modelfields']);
                $post[5] = h2c($rs['setkeyword']);
                $post[6] = h2c($rs['description']);
                $post[7] = h2c($rs['sortemplate']);
                $post[8] = h2c($rs['pagetemplate']);
            }
        }
    }
    $title = empty($modelid) ? t('Model add '.$modeltype) : t('Model edit '.$modeltype);
    // 判断tab显示
    switch ($modeltype) {
        case 'page':
            $post[2] = empty($post[2])?'%P.html':$post[2];
            $n = 4; break;
        case 'list': default:
            $post[2] = empty($post[2])?'%Y%m%d/%I.html':$post[2];
            $n = 3; break;
    }
    System::header($title,$n);
    $hl = '<form id="form1" name="form1" method="post" action="">';
    $hl.= '<fieldset><legend rel="tab"><a class="collapsed" rel=".show" cookie="false">'.$title.'</a></legend>';
    $hl.= '<div class="show">';
    $hl.= '<p><label>'.t('Model name').':</label><input class="in2" type="text" name="modelname" id="modelname" value="'.$post[0].'" /></p>';
    $hl.= '<p><label>'.t('Model ename').':</label><input class="in3" type="text" name="modelename" id="modelename" value="'.$post[1].'" /></p>';
    $hl.= '<p><label>'.t('Model path').':</label><input class="in3" type="text" name="modelpath" id="modelpath" value="'.$post[2].'" /></p>';
    
    if ($modeltype=='list') {
        $hl.= '<p><label>'.t('Model sort template').':</label>';
        $hl.= '<select name="sortemplate" id="sortemplate">';
        $hl.= form_opts(c('TEMPLATE'),'*','<option value="#value#"#selected#>#name#</option>',$post[7]);
        $hl.= '</select></p>';
    }
    
    $hl.= '<p><label>'.t('Model page template').':</label>';
    $hl.= '<select name="pagetemplate" id="pagetemplate">';
    $hl.= form_opts(c('TEMPLATE'),'*','<option value="#value#"#selected#>#name#</option>',$post[8]);
    $hl.= '</select></p>';
    $hl.= '</div></fieldset>';

    $hl.= '<fieldset><legend><a class="collapsed" rel=".fields">'.t('Model fields').'</a></legend>';
    $hl.= '<div class="fields">';
    $hl.= '<table id="Fields" action="'.PHP_FILE.'?action=fields" class="table" cellspacing="0">';
    $hl.= '<thead><tr class="nodrop"><th>'.t('Model fields text').'</th><th>'.t('Model fields ename').'</th><th>'.t('Model fields input').'</th><th>'.t('Model fields default').'</th><th>'.l('Manage').'</th></tr></thead><tbody>';
    foreach ($post[4] as $v) {
        $data = (array) $v; $i = $data['id'];
        $tip = empty($data['tip'])?null:' tip="'.$data['label'].'::'.ubbencode(h2encode($data['tip'])).'"';
        $len = empty($data['length'])?null:'('.$data['length'].')';
        $hl.= '<tr id="TR_'.$i.'"><td'.$tip.'><input type="checkbox" name="list_'.$i.'" value="'.$data['oname'].'" /> '.$data['label'].'</td>';
        $hl.= '<td>'.$data['ename'].'</td><td>'.t('model/type/'.$data['intype']).$len.'</td><td>'.(empty($data['default'])?'NULL':$data['default']).'</td>';
        $hl.= '<td><a href="javascript:;" onclick="$(this).getFields(\'#Fields\',$(\'#TR_Field_'.$i.'\').val());"><i class="os icon-16-edit"></i></a>';
        if ($data['intype']=='input') {
            $selected = ($setKeyword==$data['ename']) ? null : '-off';
            $hl.= '<a href="javascript:;" rel="light" tip="::120::'.t('model/add/fields/autokeywords').'" onclick="$(this).autoKeywords(\'#setKeyword\',\''.$data['ename'].'\');"><i class="os icon-16-light'.$selected.'"></i></a>';
        }
        if (instr('basic,editor',$data['intype'])) {
            $selected = ($description==$data['ename']) ? null : '-off';
            $hl.= '<a href="javascript:;" rel="cut" tip="::120::'.t('model/add/fields/autodescription').'" onclick="$(this).autoKeywords(\'#description\',\''.$data['ename'].'\');"><i class="os icon-16-cut'.$selected.'"></i></a>';
        }
        $hl.= '<textarea class="hide" name="modelfields['.$i.']" id="TR_Field_'.$i.'">'.json_encode($data).'</textarea></td></tr>';
    }
    $hl.= '</tbody></table>';
    $hl.= '<div class="but"><button onclick="checkALt(\'#Fields\',\'all\');" type="button">'.t('common/selectall','system').'</button>';
    $hl.= '<button onclick="checkALL(\'#Fields\');" type="button">'.t('common/reselect','system').'</button>';
    $hl.= '<button type="button" onclick="$(\'#Fields\').delFields(\'#delFields\',\''.t('confirm/delete','system').'\');">'.t('common/delete','system').'</button>';
    $hl.= '<button type="button" onclick="$(this).getFields(\'#Fields\',{\'method\':\'get\'});">'.t('model/add/fields/add').'</button></div>';
    $hl.= '</div>';
    $hl.= '</fieldset>';

    $hl.= but('save').'<input name="modelid" type="hidden" value="'.$modelid.'" /><input id="oldename" name="oldename" type="hidden" value="'.$modelename.'" />';
    $hl.= '<input id="delFields" name="delFields" type="hidden" value="" /><input id="setKeyword" name="setKeyword" type="hidden" value="'.$setKeyword.'" />';
    $hl.= '<input id="description" name="description" type="hidden" value="'.$description.'" /><input name="type" type="hidden" value="'.$modeltype.'" /></form>';
    print_x($title,$hl,$n);
}
// *** *** www.LazyCMS.net *** *** //
function lazy_after(){
    System::footer();
}