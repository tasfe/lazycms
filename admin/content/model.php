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
 */
require '../../global.php';
/**
 * 模型管理
 *
 * @copyright   Copyright (c) 2007-2008 lazycms.net All rights reserved.
 * @author      Lukin <mylukin@gmail.com>
 * @date        2008-7-10
 */

// lazy_before *** *** www.LazyCMS.net *** ***
function lazy_before(){
    check_login('model');
    // 设置公共菜单
    G('TABS',
        L('model/@title').':model.php;'.
        L('model/add/@title').':model.php?action=edit'
    );
    G('SCRIPT','LoadScript("content.model");');
}
// lazy_default *** *** www.LazyCMS.net *** ***
function lazy_default(){ 
    $db = get_conn();
    $ds = new Recordset();
    $ds->create("SELECT * FROM `#@_content_model` WHERE 1=1 ORDER BY `modelid` ASC");
    $ds->action = PHP_FILE."?action=set";
    $ds->but = $ds->button('unlock:'.L('common/unlock').'|lock:'.L('common/lock').'');
    $ds->td  = "cklist(K[0]) + K[0] + ') <a href=\"".PHP_FILE."?action=edit&modelid=' + K[0] + '\">' + K[1] + '</a>'";
    $ds->td  = "K[2]";
    $ds->td  = "K[3]";
    $ds->td  = "(K[4]?icon('tick'):icon('stop'))";
    $ds->td  = "icon('edit','".PHP_FILE."?action=edit&modelid=' + K[0])";
    $ds->open();
    $ds->thead = '<tr><th>ID) '.L('model/list/name').'</th><th>'.L('model/list/ename').'</th><th>'.L('model/list/table').'</th><th>'.L('model/list/state').'</th><th>'.L('common/action','system').'</th></tr>';
    while ($rs = $ds->result()) {
        $ds->tbody = "E(".$rs['modelid'].",'".t2js(h2encode($rs['modelname']))."','".t2js(h2encode($rs['modelename']))."','".t2js(h2encode(Model::getDBName($rs['modelename'])))."',".$rs['modelstate'].");";
    }
    $ds->close();

    print_x(L('model/@title'),$ds->fetch());
}
// lazy_set *** *** www.LazyCMS.net *** ***
function lazy_set(){
    $db = get_conn();
    $submit = isset($_POST['submit']) ? strtolower($_POST['submit']) : null;
    $lists  = isset($_POST['lists']) ? $_POST['lists'] : null;
    empty($lists) ? echo_json(L('model/pop/select'),0) : null ;
    switch($submit){
        case 'delete':
            $res = $db->query("SELECT `modelename` FROM `#@_content_model` WHERE `modelid` IN({$lists});");
            while ($rs = $db->fetch($res,0)) {
                $db->exec("DROP TABLE IF EXISTS `".Model::getDBName($rs[0])."`;");
            }
            $db->exec("DELETE FROM `#@_content_model` WHERE `modelid` IN({$lists});");
            echo_json(array(
                'text' => L('model/pop/deleteok'),
                'url'  => $_SERVER["HTTP_REFERER"],
            ),1);
            break;
        case 'lock': case 'unlock':
            $state = ($submit=='lock') ? 0 : 1;
            $db->update('#@_content_model',array('modelstate' => $state),"`modelid` IN({$lists})");
            echo_json(array(
                'text' => L('model/pop/success'),
                'url'  => PHP_FILE
            ));
            break;
        default :
            echo_json(L('error/invalid','system'));
            break;
    }
}
// lazy_edit *** *** www.LazyCMS.net *** ***
function lazy_edit(){
    G('HEAD','
        <script type="text/javascript">
        $(document).ready(function() {
            tableDnD("#Fields");
        });
        </script>
        <style type="text/css">
        #Fields thead tr,#Fields td input{ cursor:default !important; }
        #toggleFields{width:400px; left:28%; top:10px; z-index:100;}
        #toggleFields .head{ width:395px;}
        #toggleFields .body p label{ width:80px;}
        </style>
    ');
    $db = get_conn();
    $modelid = isset($_REQUEST['modelid']) ? $_REQUEST['modelid'] : 0;
    $title   = empty($modelid) ? L('model/add/@title') : L('model/edit/@title');
    $modelname   = isset($_POST['modelname']) ? $_POST['modelname'] : null;
    $modelename  = isset($_POST['modelename']) ? $_POST['modelename'] : null;
    $modelpath   = isset($_POST['modelpath']) ? $_POST['modelpath'] : null;
    $modelfields = isset($_POST['modelfields']) ? $_POST['modelfields'] : array();
    $oldename    = isset($_POST['oldename']) ? $_POST['oldename'] : null;
    $delFields   = isset($_POST['delFields']) ? $_POST['delFields'] : null;
    $setKeyword  = isset($_POST['setKeyword']) ? $_POST['setKeyword'] : null;
    $description = isset($_POST['description']) ? $_POST['description'] : null;
    if (is_array($modelfields)) {
        $modelfields = '['.implode(',',$modelfields).']';
    }
    $val = new Validate();
    if ($val->method()) {
        $val->check('modelname|1|'.L('model/check/name').'|1-50')
            ->check('modelename|1|'.L('model/check/ename').'|1-50;modelename|validate|'.L('model/check/ename1').'|3;modelename|4|'.L('model/check/ename2')."|SELECT COUNT(`modelid`) FROM `#@_content_model` WHERE `modelename`='#pro#'".(empty($modelid)?null:" AND `modelid` <> {$modelid}"));
        if ($val->isVal()) {
            $val->out();
        } else {
            $table  = Model::getDBName($modelename);
            $fields = json_decode($modelfields);
            if (empty($modelid)) {
                $structure   = null;
                foreach ($fields as $v) {
                    $data = (array) $v;
                    $len  = empty($data['length'])?null:'('.$data['length'].')';
                    $type = Model::getType($data['intype']);
                    $type = strpos($type,')')===false ? $type.$len : $type;
                    $def  = empty($data['default'])?null:" DEFAULT '".$data['default']."'";
                    $structure.= ",\n`".$data['ename']."` {$type} {$def}";
                }
                // 先删除表
                $db->exec("DROP TABLE IF EXISTS `{$table}`;");
                // 创建表
                $db->exec("
                CREATE TABLE IF NOT EXISTS `{$table}` (
                    `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                    `order` INT(11) DEFAULT '0',
                    `date` INT(11) DEFAULT '0',
                    `hits` INT(11) DEFAULT '0',
                    `digg` INT(11) DEFAULT '0',
                    `path` VARCHAR(255),
                    `img` VARCHAR(255),
                    `description` VARCHAR(255),
                    `isdel` TINYINT(1) DEFAULT '1'{$structure}
                ) ENGINE=MyISAM DEFAULT CHARSET=#~lang~#;");
                $db->insert('#@_content_model',array(
                    'modelname'  => $modelname,
                    'modelename' => $modelename,
                    'modelpath'  => $modelpath,
                    'modelfields'=> $modelfields,
                    'setkeyword' => $setKeyword,
                    'description'=> $description,
                ));
                $modelid = $db->lastId();
                $text = L('model/pop/addok');
            } else {
                if ($oldename!=$modelename) {
                    $otable = Model::getDBName($oldename);
                    $db->exec("RENAME TABLE `{$otable}` TO `{$table}`;");
                }
                $modelfields = array();
                $structure   = null;
                foreach ($fields as $v) {
                    $data = (array) $v;
                    if ($data['ename']!==$data['oname']) {
                        $len  = empty($data['length'])?null:'('.$data['length'].')';
                        $type = Model::getType($data['intype']);
                        $type = strpos($type,')')===false ? $type.$len : $type;
                        $def  = empty($data['default'])?null:" DEFAULT '".$data['default']."'";
                        $structure.= " CHANGE `".$data['oname']."` `".$data['ename']."` {$type} {$def},";
                        $data['oname'] = $data['ename'];
                    }
                    $modelfields[] = $data;
                }
                if (!empty($structure)) {
                    $structure = rtrim($structure,',');
                    $db->exec("ALTER TABLE `{$table}` {$structure};");    
                }
                if (!empty($delFields)) {
                    $arrFields = explode(',',$delFields); $structure = null;
                    foreach ($arrFields as $field) {
                        $structure.= " DROP `{$field}`,";
                    }
                    $structure = rtrim($structure,',');
                    $db->exec("ALTER TABLE `{$table}` {$structure};");
                }
                $db->update('#@_content_model',array(
                    'modelname'  => $modelname,
                    'modelename' => $modelename,
                    'modelpath'  => $modelpath,
                    'modelfields'=> json_encode($modelfields),
                    'setkeyword' => $setKeyword,
                    'description'=> $description,
                ),DB::quoteInto('`modelid` = ?',$modelid));
                $text = L('model/pop/editok');
            }
            // 输出执行结果
            echo_json(array(
                'text' => $text,
                'url'  => PHP_FILE,
            ),1);
        }
    } else {
        if (!empty($modelid)) {
            $res = $db->query("SELECT * FROM `#@_content_model` WHERE `modelid`=?",$modelid);
            if ($rs = $db->fetch($res)) {
                $modelname   = h2encode($rs['modelname']);
                $modelename  = h2encode($rs['modelename']);
                $modelpath   = $rs['modelpath'];
                $modelfields = empty($rs['modelfields'])?array():json_decode($rs['modelfields']);
                $setKeyword  = $rs['setkeyword'];
                $description = $rs['description'];
            }
        }
    }
    $hl = '<form id="form1" name="form1" method="post" action="">';
    $hl.= '<fieldset><legend rel="tab"><a class="collapsed" rel=".show" cookie="false">'.$title.'</a></legend>';
    $hl.= '<div class="show">';
    $hl.= '<p><label>'.L('model/add/name').'：</label><input class="in2" type="text" name="modelname" id="modelname" value="'.$modelname.'" /></p>';
    $hl.= '<p><label>'.L('model/add/ename').'：</label><input tip="'.L('model/add/ename').'::'.L('model/add/ename/@tip').'" class="in3" type="text" name="modelename" id="modelename" value="'.$modelename.'" /></p>';
    $hl.= '<p><label>'.L('model/add/path').'：</label><input tip="::250::'.ubbencode(L('model/add/path/@tip')).'" class="in3" type="text" name="modelpath" id="modelpath" value="'.(empty($modelpath)?'%Y%m%d/%I.htm':$modelpath).'" />';
    $hl.= '</p>';
    $hl.= '</div></fieldset>';

    $hl.= '<fieldset><legend><a class="collapsed" rel=".fields">'.L('model/add/fields/@title').'</a></legend>';
    $hl.= '<div class="fields">';
    $hl.= '<table id="Fields" action="'.PHP_FILE.'?action=fields" class="table" cellspacing="0">';
    $hl.= '<thead><tr class="nodrop"><th>'.L('model/add/fields/text').'</th><th>'.L('model/add/fields/ename').'</th><th>'.L('model/add/fields/input').'</th><th>'.L('model/add/fields/default').'</th><th>'.L('common/action','system').'</th></tr></thead><tbody>';
    foreach ($modelfields as $v) {
        $data = (array) $v; $i = $data['id'];
        $tip = empty($data['tip'])?null:' tip="'.$data['label'].'::'.ubbencode(h2encode($data['tip'])).'"';
        $len = empty($data['length'])?null:'('.$data['length'].')';
        $hl.= '<tr id="TR_'.$i.'"><td'.$tip.'><input type="checkbox" name="list_'.$i.'" value="'.$data['oname'].'" /> '.$data['label'].'</td>';
        $hl.= '<td>'.$data['ename'].'</td><td>'.L('model/type/'.$data['intype']).$len.'</td><td>'.(empty($data['default'])?'NULL':$data['default']).'</td>';
        $hl.= '<td><a href="javascript:;" onclick="$(this).getFields(\'#Fields\',$(\'#TR_Field_'.$i.'\').val());"><img src="'.SITE_BASE.'common/images/icon/edit.png" class="os"/></a>';
        if ($data['intype']=='input') {
            $selected = ($setKeyword==$data['ename']) ? null : '-off';
            $hl.= '<a href="javascript:;" rel="autoKeywords" tip="::120::'.L('model/add/fields/autokeywords').'" onclick="$(this).autoKeywords(\'#setKeyword\',\''.$data['ename'].'\');"><img src="'.SITE_BASE.'common/images/icon/lightbulb'.$selected.'.png" class="os" /></a>';
        }
        if (instr('basic,editor',$data['intype'])) {
            $selected = ($description==$data['ename']) ? null : '-off';
            $hl.= '<a href="javascript:;" rel="autoDescription" tip="::120::'.L('model/add/fields/autodescription').'" onclick="$(this).autoKeywords(\'#description\',\''.$data['ename'].'\');"><img src="'.SITE_BASE.'common/images/icon/cut'.$selected.'.png" class="os" /></a>';
        }
        $hl.= '<textarea class="hide" name="modelfields['.$i.']" id="TR_Field_'.$i.'">'.json_encode($data).'</textarea></td></tr>';
    }
    $hl.= '</tbody></table>';
    $hl.= '<div class="but"><button onclick="checkALL(\'#Fields\',\'all\');" type="button">'.L('common/selectall','system').'</button>';
    $hl.= '<button onclick="checkALL(\'#Fields\');" type="button">'.L('common/reselect','system').'</button>';
    $hl.= '<button type="button" onclick="$(\'#Fields\').delFields(\'#delFields\',\''.L('confirm/delete','system').'\');">'.L('common/delete','system').'</button>';
    $hl.= '<button type="button" onclick="$(this).getFields(\'#Fields\',{\'method\':\'get\'});">'.L('model/add/fields/add').'</button></div>';
    $hl.= '</div>';
    $hl.= '</fieldset>';

    $hl.= but('save').'<input name="modelid" type="hidden" value="'.$modelid.'" /><input id="oldename" name="oldename" type="hidden" value="'.$modelename.'" /><input id="delFields" name="delFields" type="hidden" value="" /><input id="setKeyword" name="setKeyword" type="hidden" value="'.$setKeyword.'" /><input id="description" name="description" type="hidden" value="'.$description.'" /></form>';
    print_x($title,$hl);
}
// lazy_fields *** *** www.LazyCMS.net *** ***
function lazy_fields(){
    $data   = array();
    $method = isset($_POST['method']) ? $_POST['method'] : null;
    $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : null;
    $description = isset($_POST['description']) ? $_POST['description'] : null;
    $fields = "id,label,tip,ename,intype,width,validate,value,length,default,oname,option";//11
    $eField = explode(',',$fields);
    if ($method=='POST') {
        foreach ($eField as $field) {
            $data[] = isset($_POST['field'.$field]) ? $_POST['field'.$field] : null;
        }
        $isField = !instr('id,order,date,hits,digg,path,description,isdel',$data[3]);
        $val = new Validate();
        $val->check('fieldlabel|0|'.L('model/check/label'));
        $val->check('fieldename|0|'.L('model/check/ename').';fieldename|validate|'.L('model/check/ename1').'|3;fieldename|3|'.L('model/check/restrict').'|'.$isField);
        if ($data[4]=='input') {
            $val->check('fieldlength|0|'.L('model/check/length').';fieldlength|validate|'.L('model/check/length1').'|2');
        }
        if ($val->isVal()) {
            $val->out();
        } else {
            $R = array();
            foreach ($eField as $k=>$field){
                $R[$field] = $data[$k];
            }
            $R['oname'] = empty($R['oname']) ? $R['ename'] : $R['oname'];
            $tip = empty($data[2])?null:' tip="'.$data[1].'::'.ubbencode(h2encode($data[2])).'"';
            $len = empty($data[8])?null:'('.$data[8].')';
            $hl = '<tr id="TR_'.$data[0].'">';
            $hl.= '<td'.$tip.'><input type="checkbox" name="list_'.$data[0].'" value="'.$data[10].'" /> '.$data[1].'</td>';
            $hl.= '<td>'.$data[3].'</td>';
            $hl.= '<td>'.L('model/type/'.$data[4]).$len.'</td>';
            $hl.= '<td>'.(empty($data[9])?'NULL':$data[9]).'</td>';
            $hl.= '<td><a href="javascript:;" onclick="$(this).getFields(\'#Fields\',$(\'#TR_Field_'.$data[0].'\').val());"><img src="'.SITE_BASE.'common/images/icon/edit.png" class="os"/></a>';
            if ($data[4]=='input') {
                $selected = ($keyword==$data[3]) ? null : '-off';
                $hl.= '<a href="javascript:;" rel="autoKeywords" tip="::120::'.L('model/add/fields/autokeywords').'" onclick="$(this).autoKeywords(\'#setKeyword\',\''.$data[3].'\');"><img src="'.SITE_BASE.'common/images/icon/lightbulb'.$selected.'.png" class="os" /></a>';
            }
            if (instr('basic,editor',$data[4])) {
                $selected = ($description==$data[3]) ? null : '-off';
                $hl.= '<a href="javascript:;" rel="autoDescription" tip="::120::'.L('model/add/fields/autodescription').'" onclick="$(this).autoKeywords(\'#description\',\''.$data[3].'\');"><img src="'.SITE_BASE.'common/images/icon/cut'.$selected.'.png" class="os" /></a>';
            }
            $hl.= '<textarea class="hide" name="modelfields['.$data[0].']" id="TR_Field_'.$data[0].'">'.json_encode($R).'</textarea></td></tr>';
            echo_json(array(
                'id' => $data[0],
                'tr' => $hl
            ),1);
        }
    } else {
        $_JSON = isset($_POST['JSON']) ? object_deep(json_decode($_POST['JSON'])) : null;
        foreach ($eField as $field) {
            $data[] = isset($_JSON[$field]) ? $_JSON[$field] : null;
        }
    }
    $hl = '<form id="formFields" name="formFields" method="post" action="'.PHP_FILE.'?action=fields">';
    $hl.= '<div id="toggleFields" class="panel">';
    $hl.= '<div class="head"><strong>'.L('model/add/fields/'.(empty($data[0])?'add':'edit')).'</strong><a href="javascript:;" onclick="$(\'#formFields\').remove()">×</a></div><div class="body">';
    $hl.= '<p><label>'.L('model/add/fields/text').'：</label><input tip="'.L('model/add/fields/text').'::'.L('model/add/fields/text/@tip').'" class="in2" type="text" name="fieldlabel" id="fieldlabel" value="'.$data[1].'" /><span><input type="checkbox" name="needTip" id="needTip"'.(empty($data[2])?null:' checked="checked"').(empty($data[0])?' cookie="true"':null).'/><label for="needTip">'.L('model/add/fields/needtip').'</label></span></p>';
    $hl.= '<p class="hide"><label>'.L('model/add/fields/tiptext').'：</label><textarea tip="'.L('model/add/fields/tiptext').'::'.L('model/add/fields/tiptext/@tip').'" name="fieldtip" id="fieldtip" rows="3" class="in3">'.$data[2].'</textarea></p>';
    $hl.= '<p><label>'.L('model/add/fields/ename').'：</label><input tip="'.L('model/add/fields/ename').'::300::'.L('model/add/fields/ename/@tip').'" class="in2" type="text" name="fieldename" id="fieldename" value="'.$data[3].'" /></p>';
    $hl.= '<p><label>'.L('model/add/fields/input').'：</label><select name="fieldintype" id="fieldintype">';
    foreach (Model::getType() as $k=>$v) {
        $selected = $data[4]==$k?' selected="selected"':null;
        $hl.= '<option value="'.$k.'"'.$selected.'>'.L('model/type/'.$k).'</option>';
    }
    $hl.= '</select>';
    
    $hl.= '<select name="fieldwidth" id="fieldwidth">';
    $hl.= '<option value="auto">width:auto</option>';
    $style = COM_PATH.'/images/style.css';
    if (is_file($style)) {
        if (preg_match_all('/\.(in(\d+)) *\{.*(width\:.*)\;.*\}/iU',read_file($style),$ins)) {
            foreach ($ins[1] as $k=>$v) {
                $selected = $data[5]==$v?' selected="selected"':null;
                $hl.= '<option value="'.$v.'"'.$selected.'>'.$ins[3][$k].'</option>';
            }
        }
    }
    $hl.= '</select><span><input type="checkbox" name="isValidate" id="isValidate"'.(empty($data[6])?null:' checked="checked"').(empty($data[0])?' cookie="true"':null).' /><label for="isValidate">'.L('model/add/fields/validate').'</label></span></p>';
    
    $hl.= '<p class="'.(empty($data[7])?'hide':'show').'"><label>'.L('model/add/fields/value').'：</label><textarea tip="'.L('model/add/fields/value').'::'.L('model/add/fields/value/@tip').'" name="fieldvalue" id="fieldvalue" rows="5" class="in3">'.$data[7].'</textarea></p>';
    $hl.= '<p class="hide"><label>'.L('model/add/fields/rules').'：</label><select name="setValidate" id="setValidate">';
    foreach (Model::getValidate() as $k=>$v) {
        $hl.= '<option value="'.$v.'">'.L('model/validate/'.$k).'</option>';
    }
    $hl.= '</select>&nbsp;<a href="javascript:;" onclick="$(\'#setValidate\').setValidate(\'#fieldvalidate\',1);"><img src="'.SITE_BASE.'common/images/icon/add.png" class="os" /></a>&nbsp;<a href="javascript:;" onclick="$(\'#setValidate\').setValidate(\'#fieldvalidate\',0);"><img src="'.SITE_BASE.'common/images/icon/reduce.png" class="os" /></a>';
    $hl.= '<textarea tip="'.L('model/add/fields/rules').'::250::'.ubbencode(L('model/add/fields/rules/@tip')).'" name="fieldvalidate" id="fieldvalidate" rows="3" class="in3">'.$data[6].'</textarea></p>';
    $hl.= '<p class="'.(empty($data[8]) && !empty($data[0])?'hide':'show').'"><label>'.L('model/add/fields/length').'：</label><input tip="'.L('model/add/fields/length/@tip').'" class="in1" type="text" name="fieldlength" id="fieldlength" value="'.$data[8].'" /></p>';
    $hl.= '<p class="'.(instr('basic,editor',$data[4])?'show':'hide').'"><label>'.L('common/attr').'：</label><span id="fieldoption">';
    $hl.= '<input type="checkbox" name="fieldoption[upimg]" id="upimg" value="1"'.($data[11]['upimg']?' checked="checked"':null).' /><label for="upimg">'.L('fckeditor/upimg','system').'</label>';
    $hl.= '<input type="checkbox" name="fieldoption[upfile]" id="upfile" value="1"'.($data[11]['upfile']?' checked="checked"':null).' /><label for="upfile">'.L('fckeditor/upfile','system').'</label>';
    $hl.= '<input type="checkbox" name="fieldoption[break]" id="pagebreak" value="1"'.($data[11]['break']?' checked="checked"':null).' /><label for="pagebreak">'.L('fckeditor/pagebreak','system').'</label>';
    $hl.= '<input type="checkbox" name="fieldoption[snapimg]" id="snapimg" value="1"'.($data[11]['snapimg']?' checked="checked"':null).' /><label for="snapimg">'.L('fckeditor/snapimg','system').'</label>';
    $hl.= '<input type="checkbox" name="fieldoption[dellink]" id="dellink" value="1"'.($data[11]['dellink']?' checked="checked"':null).' /><label for="dellink">'.L('fckeditor/dellink','system').'</label>';
    $hl.= '<input type="checkbox" name="fieldoption[setimg]" id="setimg" value="1"'.($data[11]['setimg']?' checked="checked"':null).' /><label for="setimg">'.L('fckeditor/setimg','system').'</label>';
    $hl.= '<input type="checkbox" name="fieldoption[resize]" id="resize" value="1"'.($data[11]['resize']?' checked="checked"':null).' /><label for="resize">'.L('fckeditor/resize','system').'</label>';
    $hl.= '</span></p>';
    $hl.= '<p><label>'.L('model/add/fields/default').'：</label><input tip="'.L('model/add/fields/default').'::250::'.ubbencode(L('model/add/fields/default/@tip')).'" class="in3" type="text" name="fielddefault" id="fielddefault" value="'.$data[9].'" /></p>';
    $hl.= '<p class="tr"><button type="button" onclick="$(this).submitFields();">'.L('common/save').'</button>&nbsp;<button type="button" onclick="$(\'#formFields\').remove()">'.L('common/cancel').'</button></p>';
    $hl.= '</div></div><input id="fieldid" name="fieldid" type="hidden" value="'.$data[0].'" /><input name="description" id="fieldescription" type="hidden" value="'.$description.'" /><input id="fieldkeyword" name="keyword" type="hidden" value="'.$keyword.'" /><input name="fieldoname" type="hidden" value="'.$data[10].'" /><input name="method" type="hidden" value="POST" /></form>'; echo $hl;
}