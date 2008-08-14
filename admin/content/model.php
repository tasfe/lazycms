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
 * 系统设置
 *
 * @copyright   Copyright (c) 2007-2008 lazycms.net All rights reserved.
 * @author      Lukin <mylukin@gmail.com>
 * @date        2008-6-26
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
    $ds->create("SELECT * FROM `#@_content_model` WHERE 1=1 ORDER BY `modelid` DESC");
    $ds->action = PHP_FILE."?action=set";
    $ds->but = $ds->button();
    $ds->td  = "cklist(K[0]) + K[0] + ') <a href=\"".PHP_FILE."?action=edit&modelid=' + K[0] + '\">' + K[1] + '</a>'";
    $ds->td  = "K[2]";
    $ds->td  = "K[3]";
    $ds->td  = "(K[4]?icon('stop'):icon('tick'))";
    $ds->td  = "icon('edit','".PHP_FILE."?action=edit&modelid=' + K[0])";
    $ds->open();
    $ds->thead = '<tr><th>ID) '.L('model/list/name').'</th><th>'.L('model/list/ename').'</th><th>'.L('model/list/table').'</th><th>'.L('model/list/state').'</th><th>'.L('common/action','system').'</th></tr>';
    while ($rs = $ds->result()) {
        $ds->tbody = "E(".$rs['modelid'].",'".t2js(h2encode($rs['modelname']))."','".t2js(h2encode($rs['modelename']))."','".t2js(h2encode(Model::getDBName($rs['modelename'])))."',".$rs['modelstate'].");";
    }
    $ds->close();

    print_x(L('model/@title'),$ds->fetch());
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
    $modelname  = isset($_POST['modelname']) ? $_POST['modelname'] : null;
    $modelename = isset($_POST['modelename']) ? $_POST['modelename'] : null;

    $hl = '<form id="form1" name="form1" method="post" action="">';
    $hl.= '<fieldset><legend rel="tab"><a class="collapsed" rel=".show" cookie="false">'.$title.'</a></legend>';
    $hl.= '<div class="show">';
    $hl.= '<p><label>'.L('model/add/name').'：</label><input class="in2" type="text" name="modelname" id="modelname" value="'.$modelname.'" /></p>';
    $hl.= '<p><label>'.L('model/add/ename').'：</label><input tip="'.L('model/add/ename').'::'.L('model/add/ename/@tip').'" class="in3" type="text" name="modelename" id="modelename" value="'.$modelename.'" /></p>';
    $hl.= '</div></fieldset>';

    $hl.= '<fieldset><legend><a class="collapsed" rel=".fields">'.L('model/add/fields/@title').'</a></legend>';
    $hl.= '<div class="fields">';
    $hl.= '<table id="Fields" action="'.PHP_FILE.'?action=fields" class="table" cellspacing="0">';
    $hl.= '<thead><tr><th>'.L('model/add/fields/text').'</th><th>'.L('model/add/fields/ename').'</th><th>'.L('model/add/fields/input').'</th><th>'.L('model/add/fields/default').'</th><th>'.L('common/action','system').'</th></tr></thead><tbody>';
    for ($i=1;$i<1;$i++) {
        $data = array(
            'id'      => $i,
            'label'   => $i.'.标题',
            'tip'     => '提示"内容',
            'ename'   => 'tit\'le',
            'intype'  => 'checkbox',
            'validate'=> 'ddddd',
            'value'   => 'sfasdf',
            'length'  => '',
            'default' => '',
        );
        $hl.= '<tr id="TR_'.$i.'"><td tip="标题::标题介绍"><input type="checkbox" name="list_'.$i.'" value="'.$i.'" /> '.$i.'.标题</td><td>title</td><td>输入框(20)</td><td>NULL</td><td><a href="javascript:;" onclick="$(this).getFields(\'#Fields\',$(\'#TR_HIDE_'.$i.'\').val());"><img src="'.SITE_BASE.'common/images/icon/edit.png" class="os"/></a><textarea class="hide" name="TR_HIDE_'.$i.'" id="TR_HIDE_'.$i.'">'.json_encode($data).'</textarea></td></tr>';
    }
    $hl.= '</tbody></table>';
    $hl.= '<div class="but"><button onclick="checkALL(\'#Fields\',\'all\');" type="button">'.L('common/selectall','system').'</button>';
    $hl.= '<button onclick="checkALL(\'#Fields\');" type="button">'.L('common/reselect','system').'</button>';
    $hl.= '<button type="button" onclick="$(\'#Fields\').delFields(\''.L('confirm/delete','system').'\');">'.L('common/delete','system').'</button>';
    $hl.= '<button type="button" onclick="$(this).getFields(\'#Fields\',{\'method\':\'get\'});">'.L('model/add/fields/add').'</button></div>';
    $hl.= '</div>';
    $hl.= '</fieldset>';

    $hl.= but('save').'<input name="modelid" type="hidden" value="'.$modelid.'" /></form>';
    print_x($title,$hl);
}
// lazy_fields *** *** www.LazyCMS.net *** ***
function lazy_fields(){
    $data   = array();
    $method = isset($_POST['method']) ? $_POST['method'] : null;
    $fields = "id,label,tip,ename,intype,width,validate,value,length,default";//9
    $eField = explode(',',$fields);
    if ($method=='POST') {
        foreach ($eField as $field) {
            $data[] = isset($_POST['field'.$field]) ? $_POST['field'.$field] : null;
        }
        $val = new Validate();
        $val->check('fieldlabel|0|'.L('model/check/label'));
        $val->check('fieldename|0|'.L('model/check/ename').';fieldename|validate|'.L('model/check/ename1').'|3');
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
            $tip = empty($data[2])?null:' tip="'.$data[1].'::'.$data[2].'"';
            $len = empty($data[8])?null:'('.$data[8].')';
            $hl = '<tr id="TR_'.$data[0].'">';
            $hl.= '<td'.$tip.'><input type="checkbox" name="list_'.$data[0].'" value="'.$data[0].'" /> '.$data[1].'</td>';
            $hl.= '<td>'.$data[3].'</td>';
            $hl.= '<td>'.L('model/type/'.$data[4]).$len.'</td>';
            $hl.= '<td>'.(empty($data[9])?'NULL':$data[9]).'</td>';
            $hl.= '<td><a href="javascript:;" onclick="$(this).getFields(\'#Fields\',$(\'#TR_HIDE_'.$data[0].'\').val());"><img src="'.SITE_BASE.'common/images/icon/edit.png" class="os"/></a>';
            $hl.= '<textarea class="hide" name="TR_HIDE_'.$data[0].'" id="TR_HIDE_'.$data[0].'">'.json_encode($R).'</textarea></td></tr>';
            echo_json(array(
                'id' => $data[0],
                'tr' => $hl
            ),1);
        }
    } else {
        foreach ($eField as $field) {
            $data[] = isset($_POST[$field]) ? $_POST[$field] : null;
        }
    }

    $hl = '<form id="formFields" name="formFields" method="post" action="'.PHP_FILE.'?action=fields">';
    $hl.= '<div id="toggleFields" class="panel">';
    $hl.= '<div class="head"><strong>'.L('model/add/fields/'.(empty($data[0])?'add':'edit')).'</strong><a href="javascript:;" onclick="$(\'#formFields\').remove()">×</a></div><div class="body">';
    $hl.= '<p><label>'.L('model/add/fields/text').'：</label><input tip="'.L('model/add/fields/text').'::'.L('model/add/fields/text/@tip').'" class="in2" type="text" name="fieldlabel" id="fieldlabel" value="'.$data[1].'" /><span><input type="checkbox" name="needTip" id="needTip"'.(empty($data[2])?null:' checked="checked"').(empty($data[0])?' cookie="true"':null).'><label for="needTip">'.L('model/add/fields/needtip').'</label></span></p>';
    $hl.= '<p class="hide"><label>'.L('model/add/fields/tiptext').'：</label><textarea tip="'.L('model/add/fields/tiptext').'::'.L('model/add/fields/tiptext/@tip').'" name="fieldtip" id="fieldtip" rows="3" class="in3">'.$data[2].'</textarea></p>';
    $hl.= '<p><label>'.L('model/add/fields/ename').'：</label><input tip="'.L('model/add/fields/ename').'::'.L('model/add/fields/ename/@tip').'" class="in2" type="text" name="fieldename" id="fieldename" value="'.$data[3].'" /></p>';
    $hl.= '<p><label>'.L('model/add/fields/input').'：</label><select name="fieldintype" id="fieldintype">';
    foreach (Model::getType() as $k=>$v) {
        $selected = $data[4]==$k?' selected="selected"':null;
        $hl.= '<option value="'.$k.'" type="'.$v.'"'.$selected.'>'.L('model/type/'.$k).'</option>';
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
    $hl.= '</select><span><input type="checkbox" name="isValidate" id="isValidate"'.(empty($data[6])?null:' checked="checked"').(empty($data[0])?' cookie="true"':null).'><label for="isValidate">'.L('model/add/fields/validate').'</label></span></p>';
    
    $hl.= '<p class="'.(empty($data[7])?'hide':'show').'"><label>'.L('model/add/fields/value').'：</label><textarea tip="'.L('model/add/fields/value').'::'.L('model/add/fields/value/@tip').'" name="fieldvalue" id="fieldvalue" rows="5" class="in3">'.$data[7].'</textarea></p>';
    $hl.= '<p class="hide"><label>'.L('model/add/fields/rules').'：</label><select name="setValidate" id="setValidate">';
    foreach (Model::getValidate() as $k=>$v) {
        $hl.= '<option value="'.$v.'">'.L('model/validate/'.$k).'</option>';
    }
    $hl.= '</select>&nbsp;<a href="javascript:;" onclick="$(\'#setValidate\').setValidate(\'#fieldvalidate\',1);"><img src="'.SITE_BASE.'common/images/icon/add.png" class="os" /></a>&nbsp;<a href="javascript:;" onclick="$(\'#setValidate\').setValidate(\'#fieldvalidate\',0);"><img src="'.SITE_BASE.'common/images/icon/cut.png" class="os" /></a>';
    $hl.= '<textarea tip="'.L('model/add/fields/rules').'::250::'.ubbencode(L('model/add/fields/rules/@tip')).'" name="fieldvalidate" id="fieldvalidate" rows="3" class="in3">'.$data[6].'</textarea></p>';
    $hl.= '<p class="'.(empty($data[8]) && !empty($data[0])?'hide':'show').'"><label>'.L('model/add/fields/length').'：</label><input tip="'.L('model/add/fields/length/@tip').'" class="in1" type="text" name="fieldlength" id="fieldlength" value="'.$data[8].'" /></p>';
    $hl.= '<p><label>'.L('model/add/fields/default').'：</label><input tip="'.L('model/add/fields/default').'::250::'.ubbencode(L('model/add/fields/default/@tip')).'" class="in3" type="text" name="fielddefault" id="fielddefault" value="'.$data[9].'" /></p>';
    $hl.= '<p class="tr"><button type="button" onclick="$(this).submitFields();">'.L('common/save').'</button>&nbsp;<button type="button" onclick="$(\'#formFields\').remove()">'.L('common/cancel').'</button></p>';
    $hl.= '</div></div><input id="fieldid" name="fieldid" type="hidden" value="'.$data[0].'" /><input name="method" type="hidden" value="POST" /></form>'; echo $hl;
}