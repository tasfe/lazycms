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
    System::purview('content::model');
    System::tabs(
        t('model').':model.php;'.
        t('model/import').':model.php?action=import;'.
        t('model/addlist').':model.php?action=edit&type=list;'.
        t('model/addpage').':model.php?action=edit&type=page;'
    );
}
// *** *** www.LazyCMS.net *** *** //
function lazy_main(){
    System::header(t('model'));
    $db = get_conn();
    $ds = new Recordset();
    $ds->create("SELECT * FROM `#@_content_model` ORDER BY `modelid` ASC");
    $ds->action = PHP_FILE."?action=set";
    $ds->but = $ds->button('unlock:'.t('system::unlock').'|lock:'.t('system::lock').'');
    $ds->td("cklist(K[0]) + K[0] + ') <a href=\"".PHP_FILE."?action=edit&modelid=' + K[0] + '\">' + K[1] + '</a>'");
    $ds->td("K[2]");
    $ds->td("K[3]");
    $ds->td("(K[4]?icon('b2'):icon('b1'))");
    $ds->td("icon('a5','".PHP_FILE."?action=edit&modelid=' + K[0]) + icon('a8','".PHP_FILE."?action=export&model=' + K[2])");
    $ds->open();
    $ds->thead = '<tr><th>ID) '.t('model/name').'</th><th>'.t('model/ename').'</th><th>'.t('model/table').'</th><th>'.t('model/state').'</th><th>'.t('system::Manage').'</th></tr>';
    while ($rs = $ds->result()) {
        $ds->tbody("E(".$rs['modelid'].",'".t2js(h2c($rs['modelname']))."','".t2js(h2c($rs['modelename']))."','".t2js(h2c(Content_Model::getDataTableName($rs['modelename'])))."',".$rs['modelstate'].");");
    }
    $ds->close();
    $ds->display();
}
// *** *** www.LazyCMS.net *** *** //
function lazy_set(){
    $db = get_conn();
    $submit = isset($_POST['submit']) ? strtolower($_POST['submit']) : null;
    $lists  = isset($_POST['lists']) ? $_POST['lists'] : null;
    empty($lists) ? ajax_alert(t('model/alert/noselect')) : null ;
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
            ajax_success(t('model/alert/delete'),1);
            break;
        case 'lock': case 'unlock':
            $state = ($submit=='lock') ? 0 : 1;
            $db->update('#@_content_model',array('modelstate' => $state),"`modelid` IN({$lists})");
            ajax_success(t('model/alert/'.$submit),0);
            break;
        default :
            ajax_error(t('system::error/invalid'));
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
    exit();
}
// *** *** www.LazyCMS.net *** *** //
function lazy_import(){
    $modelcode = isset($_POST['modelcode']) ? $_POST['modelcode'] : null;
    $val = new Validate();
    if ($val->method()) {
        $val->check('modelcode|0|'.t('model/check/code'));
        if ($val->isVal()) {
            $val->out();
        } else {
            // 解析xml数据
            $db = get_conn();
            $data = (array) json_decode($modelcode);
            $number = $db->result("SELECT COUNT(*) FROM `#@_content_model` WHERE `modelename`=".DB::quote($data['modelename']).";");
            $isexist= $number > 0 ? false : true;
            $val->check('modelcode|3|'.t('model/check/exist').'|'.$isexist);
            if ($val->isVal()) {
                $val->out();
            } else {
                // 创建模型
                Content_Model::addModel($data);
                ajax_success(t('model/alert/import'),0);
            }
        }
    }
    System::loadScript('content.model');
    System::header(t('model/import'));
    echo '<fieldset><legend><a rel=".show" cookie="false"><img class="a2 os" src="../system/images/white.gif" />'.t('model/import').'</a></legend>';
    echo '<div class="show">';
    echo '<form id="form1" name="form1" method="post" ajax="false" enctype="multipart/form-data" action="'.PHP_FILE.'?action=upmodel" target="tempform">';
    echo '<p><label>'.t('model/import/file').':</label><input type="file" name="modelfile" id="modelfile" onchange="$(this).autoUpFile();" class="in w400" /></p>';
    echo '</form>';
    echo '<form id="form2" name="form2" method="post" action="'.PHP_FILE.'?action=import">';
    echo '<p><label>'.t('model/import/code').':</label><textarea name="modelcode" id="modelcode" rows="20" class="in w600"></textarea></p>';
    echo '<p><label>&nbsp;</label><button type="submit">'.t('model/import/submit').'</button></p>';
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
    echo 'parent.$(\'#modelfile\').replaceWith(\'<input type="file" name="modelfile" id="modelfile" onchange="$(this).autoUpFile();" class="in w400" />\');';
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
    $modeltype = isset($_REQUEST['type']) ? strtolower($_REQUEST['type']) : 'list';
    $post      = POST('modelname','modelename','modelpath','modeltype','modelfields','iskeyword','description','sortemplate','pagetemplate');
    $post[3]   = empty($post[3]) ? $modeltype : $post[3];
    if (is_array($post[4])) {
        $post[4] = '['.implode(',',$post[4]).']';
    }
    $val = new Validate();
    if ($val->method()) {
        $val->check('modelname|1|'.t('model/check/name').'|1-50');
        $val->check('modelename|1|'.t('model/check/ename').'|1-50;modelename|validate|'.t('model/check/ename1').'|3;modelename|4|'.t('model/check/ename2')."|SELECT COUNT(`modelid`) FROM `#@_content_model` WHERE `modelename`='#pro#'".(empty($modelid)?null:" AND `modelid` <> {$modelid}"));
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
                    'iskeyword'   => $post[5],
                    'description' => $post[6],
                    'sortemplate' => $post[7],
                    'pagetemplate'=> $post[8],
                ));
                $text = t('model/alert/add');
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
                // 组成修改数据结构的SQL
                $post[4] = $sute = array();
                $chang = $add = array();
                foreach ($fields as $v) {
                    $data = (array) $v; $k = $data['ename'];
                    $type = Content_Model::getType($data['intype']);
                    $sute[$k]['len']  = instr('input,radio,checkbox,select,upfile',$data['intype'])?'('.$data['length'].')':null;
                    $sute[$k]['type'] = strpos($type,')')===false ? $type.$sute[$k]['len'] : $type;
                    $sute[$k]['def']  = empty($data['default'])?null:" DEFAULT '".$data['default']."'";
                    // 只改变结构
                    $data['oname'] = empty($data['oname'])?$data['ename']:$data['oname'];
                    if ($data['ename'] == $data['oname'] && $db->isField($table,$data['oname'])) {
                        $chang[] = " CHANGE `".$data['ename']."` `".$data['ename']."` ".$sute[$k]['type']." ".$sute[$k]['def'];
                    } elseif ($db->isField($table,$data['oname'])) {
                        $chang[] = " CHANGE `".$data['oname']."` `".$data['ename']."` ".$sute[$k]['type']." ".$sute[$k]['def'];
                        $data['oname'] = $data['ename'];
                    } else {
                        $add[] = " ADD `".$data['ename']."` ".$sute[$k]['type']." ".$sute[$k]['def'];
                    }
                    $post[4][] = $data;
                }
                // 修改现有字段的结构
                if (!empty($chang)) {
                    $chang = implode(',',$chang);
                    $db->exec("ALTER TABLE `{$table}` {$chang};");
                }
                // 添加新增的字段
                if (!empty($add)) {
                    $add = implode(',',$add);
                    $db->exec("ALTER TABLE `{$table}` {$add};");
                }
                $delfields = Content_Model::diffFields($db->listFields($table),$fields);
                // 删除不需要的字段
                if (!empty($delfields)) {
                    $sute = null;
                    foreach ($delfields as $field) {
                        if ($db->isField($table,$field)) {
                            $sute.= " DROP `{$field}`,";
                        }
                    }
                    $sute = rtrim($sute,',');
                    $db->exec("ALTER TABLE `{$table}` {$sute};");
                }
                // 更新数据
                $db->update('#@_content_model',array(
                    'modelname'   => $post[0],
                    'modelename'  => $post[1],
                    'modelpath'   => $post[2],
                    'modeltype'   => $post[3],
                    'modelfields' => json_encode($post[4]),
                    'iskeyword'   => $post[5],
                    'description' => $post[6],
                    'sortemplate' => $post[7],
                    'pagetemplate'=> $post[8],
                ),DB::quoteInto('`modelid` = ?',$modelid));
                $text = t('model/alert/edit');
            }
            // 输出执行结果
            ajax_success($text,0);
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
                $post[5] = h2c($rs['iskeyword']);
                $post[6] = h2c($rs['description']);
                $post[7] = h2c($rs['sortemplate']);
                $post[8] = h2c($rs['pagetemplate']);
            }
        }
    }
    $title = empty($modelid) ? t('model/add'.$post[3]) : t('model/edit'.$post[3]);
    // 判断tab显示
    switch ($post[3]) {
        case 'page':
            $post[2] = empty($post[2])?'%P.html':$post[2];
            $n = 4; break;
        case 'list': default:
            $post[2] = empty($post[2])?'%Y%m%d/%I.html':$post[2];
            $n = 3; break;
    }

    System::loadScript('content.model');
    System::script('
        $(document).ready(function() {
            $("#tableFields").__tableDnD();
        });
    ');
    System::header($title,$n);

    echo '<style type="text/css">';
    echo '#tableFields thead tr,#tableFields td input{ cursor:default !important; }';
    echo '#tableFields tr.Drag td{ color:#0000FF !important; background-color:#FFFFCC; }';
    echo '#tableFields tr.Over td{ background-color:#FFFFCC; }';
    echo '</style>';

    echo '<form id="form1" name="form1" method="post" action="">';
    echo '<fieldset><legend rel="tab"><a rel=".show" cookie="false"><img class="a2 os" src="../system/images/white.gif" />'.$title.'</a></legend>';
    echo '<div class="show">';
    echo '<p><label>'.t('model/name').':</label><input class="in w200" type="text" name="modelname" id="modelname" value="'.$post[0].'" /></p>';
    echo '<p><label>'.t('model/ename').':</label><input help="model/ename" class="in w250" type="text" name="modelename" id="modelename" value="'.$post[1].'" /></p>';
    echo '<p><label>'.t('model/path').':</label><input help="model/path" class="in w300" type="text" name="modelpath" id="modelpath" value="'.$post[2].'" /></p>';
    
    if ($post[3]=='list') {
        echo '<p><label>'.t('model/template/sort').':</label>';
        echo '<select name="sortemplate" id="sortemplate">';
        echo form_opts(c('TEMPLATE'),'*','<option value="#value#"#selected#>#name#</option>',$post[7]);
        echo '</select></p>';
    }
    
    echo '<p><label>'.t('model/template/page').':</label>';
    echo '<select name="pagetemplate" id="pagetemplate">';
    echo form_opts(c('TEMPLATE'),'*','<option value="#value#"#selected#>#name#</option>',$post[8]);
    echo '</select></p>';
    echo '</div></fieldset>';

    echo '<fieldset><legend><a rel=".fields"><img class="a2 os" src="../system/images/white.gif" />'.t('model/fields').'</a></legend>';
    echo '<div class="fields">';

    echo '<table id="tableFields" action="'.PHP_FILE.'?action=fields" class="table" cellspacing="0">';
    echo '<thead><tr class="nodrop"><th>'.t('model/fields/text').'</th><th>'.t('model/fields/ename').'</th><th>'.t('model/fields/input').'</th><th>'.t('model/fields/default').'</th><th>'.t('system::manage').'</th></tr></thead><tbody>';
    
    foreach ($post[4] as $row) {
        $row = (array) $row;
        $len = instr('input,radio,checkbox,select,upfile',$row['intype'])?'('.$row['length'].')':null;
        echo '<tr n="'.$row['id'].'">';
        echo '<td><input type="checkbox" name="list['.$row['id'].']" value="'.$row['id'].'" /> '.$row['label'].'</td>';
        echo '<td>'.$row['ename'].'</td>';
        echo '<td>'.t('model/fields/type/'.$row['intype']).$len.'</td>';
        echo '<td>'.(empty($row['default'])?'NULL':$row['default']).'<textarea name="modelfields['.$row['id'].']" fieldid="'.$row['id'].'" class="hide">'.json_encode($row).'</textarea></td>';
        echo '<td><a href="javascript:;" onclick="$(\'#tableFields\').editFields('.$row['id'].');"><img class="a5 os" src="../system/images/white.gif" /></a>';
        if ($row['intype']=='input') {
            $selected = ($post[5]==$row['ename']) ? 'b5' : 'b6';
            echo '<a href="javascript:;" onclick="$(this).isKeyword('.$row['id'].');" title="'.t('model/fields/iskeyword').'"><img class="'.$selected.' os" src="../system/images/white.gif" /></a>';
        }
        if (instr('basic,editor',$row['intype'])) {
            $selected = ($post[6]==$row['ename']) ? 'b7' : 'b8';
            echo '<a href="javascript:;" onclick="$(this).Description('.$row['id'].');" title="'.t('model/fields/description').'"><img class="'.$selected.' os" src="../system/images/white.gif" /></a>';
        }
        echo '</td></tr>';
    }

    echo '</tbody></table>';

    echo '<div class="but">';
    echo '<button type="button" onclick="$(\'#tableFields\').checkALL(true);">'.t('system::selectall').'</button>';
    echo '<button type="button" onclick="$(\'#tableFields\').checkALL(false);">'.t('system::reselect').'</button>';
    echo '<button type="button" onclick="$.confirm(\''.t('system::confirm/delete').'\',function(r){r?$(\'#tableFields\').delFields():false;})">'.t('system::delete').'</button>';
    echo '<button type="button" onclick="$(\'#tableFields\').addFields();">'.t('model/fields/add').'</button>';
    echo '</div>';

    echo '</fieldset>';

    echo but('system::save');
    echo '<input name="modelid" type="hidden" value="'.$modelid.'" />';
    echo '<input name="modeltype" type="hidden" value="'.$post[3].'" />';
    echo '<input name="oldename" type="hidden" value="'.$post[1].'" />';
    echo '<input name="iskeyword" type="hidden" value="'.$post[5].'" />';
    echo '<input name="description" type="hidden" value="'.$post[6].'" />';
    echo '</form>';
}
// *** *** www.LazyCMS.net *** *** //
function lazy_fields(){
    $post   = array();
    $fields = "id,label,help,ename,intype,width,validate,value,length,default,option,oname";//11
    $eField = explode(',',$fields);
    $val = new Validate();
    if ($val->method() && !isset($_POST['JSON'])) {
        foreach ($eField as $field) {
            $rq[] = isset($_POST['field'.$field]) ? $_POST['field'.$field] : null;
        }
        $isField = !instr('id,order,date,hits,digg,passed,userid,path,description,isdel',$rq[3]);
        $val->check('fieldlabel|0|'.t('model/fields/check/label'));
        $val->check('fieldename|0|'.t('model/fields/check/ename').';fieldename|validate|'.t('model/fields/check/ename1').'|3;fieldename|3|'.t('model/fields/check/restrict').'|'.$isField);
        if (instr('input',$rq[4])) {
            $val->check('fieldlength|0|'.t('model/fields/check/length').';fieldlength|validate|'.t('model/fields/check/length1').'|2');
        }
        if ($val->isVal()) {
            $val->out();
        } else {
            $row = array();
            foreach ($eField as $k=>$field){
                $row[$field] = $rq[$k];
            }
            $len = instr('input,radio,checkbox,select,upfile',$row['intype'])?'('.$row['length'].')':null;
            $s = '<tr n="'.$row['id'].'">';
            $s.= '<td><input type="checkbox" name="list['.$row['id'].']" value="'.$row['id'].'" /> '.$row['label'].'</td>';
            $s.= '<td>'.$row['ename'].'</td>';
            $s.= '<td>'.t('model/fields/type/'.$row['intype']).$len.'</td>';
            $s.= '<td>'.(empty($row['default'])?'NULL':$row['default']).'<textarea name="modelfields['.$row['id'].']" fieldid="'.$row['id'].'" class="hide">'.json_encode($row).'</textarea></td>';
            $s.= '<td><a href="javascript:;" onclick="$(\'#tableFields\').editFields('.$row['id'].');"><img class="a5 os" src="../system/images/white.gif" /></a>';
            if ($row['intype']=='input') {
                $s.= '<a href="javascript:;" onclick="$(this).isKeyword('.$row['id'].');" title="'.t('model/fields/iskeyword').'"><img class="b6 os" src="../system/images/white.gif" /></a>';
            }
            if (instr('basic,editor',$row['intype'])) {
                $s.= '<a href="javascript:;" onclick="$(this).Description('.$row['id'].');" title="'.t('model/fields/description').'"><img class="b8 os" src="../system/images/white.gif" /></a>';
            }
            $s.= '</td></tr>';
            ajax_result($s);
        }
    } else {
        $_JSON = isset($_POST['JSON']) ? json_decode($_POST['JSON']) : array();
        if (empty($_JSON)) {
            $rq[0] = isset($_GET['fieldid']) ? $_GET['fieldid'] : 0;
        } else {
            foreach ($eField as $field) {
                $rq[] = isset($_JSON->$field) ? $_JSON->$field : null;
            }
        }
    }
    $hl = '<form id="formFields" name="formFields" method="post" action="'.PHP_FILE.'?action=fields">';
    $hl.= '<p><label>'.t('model/fields/text').':</label><input class="in w200" type="text" name="fieldlabel" id="fieldlabel" value="'.h2c($rq[1]).'" /><span><input type="checkbox" name="isHelp" id="isHelp"'.(empty($rq[2])?' cookie="true"':' checked="checked"').' /><label for="isHelp">'.t('model/fields/ishelp').'</label></span></p>';
    $hl.= '<p class="hide"><label>'.t('model/fields/help').':</label><textarea help="model/fields/help" name="fieldhelp" id="fieldhelp" rows="3" class="in w250">'.h2c($rq[2]).'</textarea></p>';
    $hl.= '<p><label>'.t('model/fields/ename').':</label><input help="model/fields/ename" class="in w200" type="text" name="fieldename" id="fieldename" value="'.h2c($rq[3]).'" /></p>';
    $hl.= '<p><label>'.t('model/fields/input').':</label><select name="fieldintype" id="fieldintype" rel="change">';
    foreach (Content_Model::getType() as $k=>$v) {
        $selected = $rq[4]==$k?' selected="selected"':'';
        $hl.= '<option value="'.$k.'"'.$selected.'>'.t('model/fields/type/'.$k).'</option>';
    }
    $hl.= '</select> <span>'.t('model/fields/width').':<select name="fieldwidth" id="fieldwidth" edit="true" default="'.h2c($rq[5]?$rq[5]:'150px').'">';
    for($i=1;$i<=16;$i++){
        $hl.= '<option value="'.($i*50).'px">'.($i*50).'px</option>';
    }
    $hl.= '</select><input type="checkbox" name="isValidate" id="isValidate"'.(empty($rq[6])?' cookie="true"':' checked="checked"').' /><label for="isValidate">'.t('model/fields/validate').'</label></span></p>';

    $hl.= '<p class="hide"><label>'.t('model/fields/rules').':</label><select name="fieldrules" id="fieldrules">';
    foreach (Content_Model::getValidate() as $k=>$v) {
        $hl.= '<option value="'.$v.'">'.t('validate/'.$k).'</option>';
    }
    $hl.= '</select>&nbsp;<a href="javascript:;" rule="+"><img class="a6 os" src="../system/images/white.gif" /></a><a href="javascript:;" rule="-"><img class="a7 os" src="../system/images/white.gif" /></a>';
    $hl.= '<textarea help="model/fields/rules" name="fieldvalidate" id="fieldvalidate" rows="3" class="in w250">'.h2c($rq[6]).'</textarea></p>';
    
    $hl.= '<p class="hide"><label>'.t('model/fields/value').':</label><textarea help="model/fields/value" name="fieldvalue" id="fieldvalue" rows="4" class="in w250">'.h2c($rq[7]).'</textarea></p>';
    
    $hl.= '<p class="hide"><label>'.t('model/fields/option').':</label><span id="fieldoption">';
    $hl.= '<input type="checkbox" name="fieldoption[upimg]" id="option_upimg" value="1"'.($rq[10]->upimg?' checked="checked"':null).' /><label for="option_upimg">'.t('system::editor/upimg').'</label>';
    $hl.= '<input type="checkbox" name="fieldoption[upfile]" id="option_upfile" value="1"'.($rq[10]->upfile?' checked="checked"':null).' /><label for="option_upfile">'.t('system::editor/upfile').'</label>';
    $hl.= '<input type="checkbox" name="fieldoption[break]" id="option_break" value="1"'.($rq[10]->break?' checked="checked"':null).' /><label for="option_break">'.t('system::editor/break').'</label>';
    $hl.= '<input type="checkbox" name="fieldoption[snapimg]" id="option_snapimg" value="1"'.($rq[10]->snapimg?' checked="checked"':null).' /><label for="option_snapimg">'.t('system::editor/snapimg').'</label>';
    $hl.= '<input type="checkbox" name="fieldoption[dellink]" id="option_dellink" value="1"'.($rq[10]->dellink?' checked="checked"':null).' /><label for="option_dellink">'.t('system::editor/dellink').'</label>';
    $hl.= '<input type="checkbox" name="fieldoption[resize]" id="option_resize" value="1"'.($rq[10]->resize?' checked="checked"':null).' /><label for="option_resize">'.t('system::editor/resize').'</label>';
    $hl.= '</span></p>';

    $hl.= '<p class="hide"><label>'.t('model/fields/length').':</label><select name="fieldlength" id="fieldlength" edit="true" default="'.h2c(empty($rq[8])?255:$rq[8]).'">';
    foreach (array(10,20,30,50,100,255) as $v) {
        $hl.= '<option value="'.$v.'">'.$v.'</option>';
    }
    $hl.= '</select></p>';
    $hl.= '<p><label>'.t('model/fields/default').':</label><input class="in w300" type="text" name="fielddefault" id="fielddefault" value="'.h2c($rq[9]).'" /></p>';
    $hl.= '<div class="tr"><button type="submit">'.t('system::save').'</button><button type="button" rel="cancel">'.t('system::ajax/cancel').'</button></div>';
    $hl.= '<input name="fieldid" type="hidden" value="'.$rq[0].'" />';
    $hl.= '<input name="fieldoname" type="hidden" value="'.(empty($rq[11])?$rq[3]:$rq[11]).'" />';
    $hl.= '</form>';
    ajax_result(array(
        'TITLE' => t('model/fields/add'),
        'BODY'  => $hl,
    ));
}
// *** *** www.LazyCMS.net *** *** //
function lazy_after(){
    System::footer();
}