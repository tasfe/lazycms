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
    check_login('onepage');
    // 设置公共菜单
    G('TABS',
        L('title').':index.php;'.
        L('add/@title').':index.php?action=edit'
    );
}
// lazy_default *** *** www.LazyCMS.net *** ***
function lazy_default(){ 
    $db = get_conn();
    $ds = new Recordset();
    $ds->create("SELECT * FROM `#@_onepage` WHERE `oneid1`=0 ORDER BY `oneorder` DESC");
    $ds->action = PHP_FILE."?action=set";
    $ds->but = $ds->button();
    $ds->td  = "cklist(K[0]) + K[0] + ') <a href=\"users.php?action=user_list&groupid=' + K[0] + '\">' + K[1] + '</a>'";
    $ds->td  = "K[2]";
    $ds->td  = "icon('add','users.php?action=user_edit&groupid=' + K[0]) + icon('edit','users.php?action=group_edit&groupid=' + K[0])";
    $ds->open();
    $ds->thead = '<tr><th>ID) '.L('list/name').'</th><th>'.L('list/path').'</th><th>'.L('common/action','system').'</th></tr>';
    while ($rs = $ds->result()) {
        $ds->tbody = "ll(".$rs['oneid'].",'".t2js(h2encode($rs['onename']))."','".t2js(h2encode($rs['onepath']))."',".$rs['oneid1'].",".$rs['oneopen'].");";
    }
    $ds->close();

    print_x(L('title'),$ds->fetch());
}
// lazy_edit *** *** www.LazyCMS.net *** ***
function lazy_edit(){
    $db = get_conn(); require_file('common.php');
    $oneid    = isset($_REQUEST['oneid']) ? $_REQUEST['oneid'] : 0;
    $title    = empty($oneid) ? L('add/@title') : L('edit/@title');
    $oneid1   = isset($_REQUEST['oneid1']) ? $_REQUEST['oneid1'] : 0;
    $onename  = isset($_POST['onename']) ? $_POST['onename'] : null;
    $onetitle = isset($_POST['onetitle']) ? $_POST['onetitle'] : null;
    $onepath  = isset($_POST['onepath']) ? $_POST['onepath'] : null;
    $onecontent  = isset($_POST['onecontent']) ? $_POST['onecontent'] : null;
    $keywords    = isset($_POST['keywords']) ? $_POST['keywords'] : null;
    $description = isset($_POST['description']) ? $_POST['description'] : null;
    $onetemplate = isset($_POST['onetemplate']) ? $_POST['onetemplate'] : null;
    
    $val = new Validate();
    if ($val->method()) {
        $val->check('onename|0|页面名称不能为空');
        $val->check('onetitle|0|页面标题不能为空');
        $val->check('onepath|0|路径不能为空;onepath|5|路径错误');
        if ($val->isVal()) {
            $val->out();
        } else {
            if (empty($oneid)) {
                $db->insert('#@_onepage',array(
                    'oneid1'  => $oneid1,
                    'oneorder'=> $db->max('oneid','#@_onepage'),
                    'onetitle'=> $onetitle,
                    'onepath' => $onepath,
                    'onename' => $onename,
                    'onecontent'  => $onecontent,
                    'onetemplate' => $onetemplate,
                    'description' => $description,
                ));
                $text = L('add/pop/addok');
            }
            echo_json(array(
                'text' => $text,
                'url'  => 'index.php',
            ),1);
        }
    } else {
    
    }

    $hl = '<form id="form1" name="form1" method="post" action="">';
    $hl.= '<fieldset><legend rel="tab"><a href="#" onclick="toggleFieldset(this,\'.more-attrd\')" class="collapsed">'.$title.'</a></legend>';
    $hl.= '<div class="more-attrd">';
    $hl.= '<p><label>'.L('add/sort').'：</label>';
    $hl.= '<select name="oneid1" id="oneid1">';
    $hl.= '<option value="0">--- '.L('add/topsort').' ---</option>';
    $hl.= Onepage::__sort(0,0,$oneid,$oneid1);
    $hl.= '</select></p>';
    $hl.= '<p><label>'.L('add/name').'：</label><input tip="'.L('add/name').'::'.L('add/name/@tip').'" class="in3" type="text" name="onename" id="onename" value="'.$onename.'" /></p>';
    $hl.= '<p><label>'.L('add/title').'：</label><input tip="'.L('add/title').'::'.h2encode(L('add/title/@tip')).'" class="in4" type="text" name="onetitle" id="onetitle" value="'.$onetitle.'" /></p>';
    $hl.= '<p><label>'.L('add/path').'：</label><input tip="'.L('add/path').'::300::'.h2encode(L('add/path/@tip')).'" class="in5" type="text" name="onepath" id="onepath" value="'.$onepath.'" /></p>';
    $hl.= '<p><label>'.L('add/content').'：</label><div class="box">'.editor('onecontent',array('value'=>$onecontent,'editor'=>'fckeditor')).'</div></p>';
    $hl.= '</div></fieldset>';
    $hl.= '<fieldset><legend><a href="#" onclick="toggleFieldset(this,\'.more-attr\')" class="collapse">'.L('common/attr').'</a></legend>';
    $hl.= '<div class="more-attr">';
    $hl.= '<p><label>'.L('add/keywords').'：</label><input tip="'.L('add/keywords').'::250::'.L('add/keywords/@tip').'" class="in4" type="text" name="keywords" id="keywords" value="'.$keywords.'" />&nbsp;<button type="button" onclick="$(\'#keywords\').getKeywords(\'#onetitle\',\'#onecontent\')" tip="'.L('common/get/@tip','system').'">'.L('common/get','system').'</button></p>';
    $hl.= '<p><label>'.L('add/description').'：</label><textarea name="description" id="description" rows="5" class="in4">'.$description.'</textarea></p>';
    $hl.= '<p><label>'.L('add/template').'：</label>';
    $hl.= '<select name="onetemplate" id="onetemplate">';
    $hl.= form_opts('themes/'.C('TEMPLATE'),'htm,html,xml,php','<option value="#value#"#selected#>#name#</option>','default.html');
    $hl.= '</select></p>';
    $hl.= '</div></fieldset>';
    $hl.= but('save').'<input name="oneid" type="hidden" value="'.$oneid.'" /></form>';
    print_x($title,$hl);
}