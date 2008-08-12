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
            $("div.fields table.table").tableDnD({
                onDragClass: "Drag",
                onDrop: function(table, row) {
                    //alert($.tableDnD.serialize());
                }
            }).find("tr").hover(function(){
                $(this).addClass("Over");
            },function(){
                $(this).removeClass("Over");
            });
        });
        </script>
        <style type="text/css">
        #Fields thead tr,#Fields td input{ cursor:default !important; }
        #toggleField{width:400px; left:28%; top:0px; z-index:100;}
        #toggleField .head{ width:395px;}
        #toggleField .body p label{ width:80px;}
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
    $hl.= '<table id="Fields" class="table" cellspacing="0">';
    $hl.= '<thead><tr><th>'.L('model/add/fields/text').'</th><th>'.L('model/add/fields/ename').'</th><th>'.L('model/add/fields/input').'</th><th>'.L('model/add/fields/default').'</th><th>'.L('model/add/fields/validate').'</th><th>'.L('common/action','system').'</th></tr></thead><tbody>';
    for ($i=1;$i<6;$i++) {
        $hl.= '<tr id="TR_'.$i.'"><td tip="标题::标题介绍"><input type="checkbox" name="list_'.$i.'" value="'.$i.'" /> 标题</td><td>title</td><td>输入框(20)</td><td>NULL</td><td>Email</td><td><a href="javascript:;" onclick="$(\'#Fields\').addFields(\''.PHP_FILE.'?action=fields\',{name:1});"><img src="'.SITE_BASE.'common/images/icon/edit.png" class="os"/></a></td></tr>';
    }
    $hl.= '</tbody></table>';
    $hl.= '<div class="but"><button onclick="checkALL(\'#Fields\',\'all\');" type="button">'.L('common/selectall','system').'</button>';
    $hl.= '<button onclick="checkALL(\'#Fields\');" type="button">'.L('common/reselect','system').'</button>';
    $hl.= '<button type="button" onclick="$(\'#Fields\').delFields(\''.L('confirm/delete','system').'\');">'.L('common/delete','system').'</button>';
    $hl.= '<button type="button" onclick="$(\'#Fields\').addFields(\''.PHP_FILE.'?action=fields\');">'.L('model/add/fields/add').'</button></div>';
    $hl.= '</div>';
    $hl.= '</fieldset>';

    $hl.= but('save').'<input name="modelid" type="hidden" value="'.$modelid.'" /></form>';
    print_x($title,$hl);
}
// lazy_fields *** *** www.LazyCMS.net *** ***
function lazy_fields(){
    $hl = '<div id="toggleField" class="panel">';
    $hl.= '<div class="head"><strong>添加字段</strong><a href="javascript:;" onclick="$(\'#toggleField\').remove()">×</a></div><div class="body">';
    $hl.= '<p><label>表单文字：</label><input class="in2" type="text" name="fieldlabel" id="fieldlabel" value="'.$fieldlabel.'" /><span><input type="checkbox" name="needTip" id="needTip" cookie="true"><label for="needTip">需要说明</label></span></p>';
    $hl.= '<p class="hide"><label>提示说明：</label><textarea name="fieldtip" id="fieldtip" rows="3" class="in3">'.$fieldtip.'</textarea></p>';
    $hl.= '<p><label>字段名：</label><input class="in2" type="text" name="fieldename" id="fieldename" value="'.$fieldename.'" /></p>';
    $hl.= '<p><label>输入类型：</label><select name="fieldintype" id="fieldintype">';
    foreach (Model::getType() as $k=>$v) {
        $hl.= '<option value="'.$k.'" type="'.$v.'">'.L('model/type/'.$k).'</option>';
    }
    $hl.= '</select><span><input type="checkbox" name="isValidate" id="isValidate" cookie="true"><label for="isValidate">需要验证</label></span></p>';
    $hl.= '<p class="hide"><label>序列值：</label><textarea name="fieldvalue" id="fieldvalue" rows="5" class="in3">'.$fieldvalue.'</textarea></p>';
    $hl.= '<p class="hide"><label>验证规则：</label><select name="setValidate" id="setValidate">';
    foreach (Model::getValidate() as $k=>$v) {
        $hl.= '<option value="'.$v.'">'.L('model/validate/'.$k).'</option>';
    }
    $hl.= '</select><textarea name="fieldvalidate" id="fieldvalidate" rows="3" class="in3">'.$fieldvalidate.'</textarea></p>';
    $hl.= '<p><label>最大长度：</label><input class="in1" type="text" name="fieldlength" id="fieldlength" value="'.$fieldlength.'" /></p>';
    $hl.= '<p><label>默认值：</label><input class="in3" type="text" name="fielddefault" id="fielddefault" value="'.$fielddefault.'" /></p>';
    $hl.= '<p class="tr"><button type="button">保存</button>&nbsp;<button type="button" onclick="$(\'#toggleField\').remove()">取消</button></p>';
    $hl.= '</div></div>'; echo $hl;
}