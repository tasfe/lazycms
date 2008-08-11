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

    $hl.= '<fieldset><legend><a class="collapsed" rel=".fields">'.L('model/add/fields').'</a></legend>';
    $hl.= '<div class="fields">';
    $hl.= '<table class="table" cellspacing="0">';
    $hl.= '<thead><tr><th>ID) 表单文字</th><th>字段名</th><th>输入类型</th><th>默认值</th><th>验证规则</th><th>操作</th></tr></thead><tbody>';
    for ($i=1;$i<11;$i++) {
        $hl.= '<tr><td><input type="checkbox" name="list_'.$i.'" />'.$i.') 标题</td><td>title</td><td>输入框(20)</td><td>NULL</td><td>Email</td><td><a href="#"><img src="'.SITE_BASE.'common/images/icon/edit.png" class="os"/></a></td></tr>';
    }
    $hl.= '</tbody></table><div class="but"><button onclick="checkALL(this,\'all\');" type="button">全选</button><button onclick="checkALL(this);" type="button">反选</button><button>删除</button><button>添加字段</button></div>';
    $hl.= '</div>';
    $hl.= '</fieldset>';

    $hl.= but('save').'<input name="modelid" type="hidden" value="'.$modelid.'" /></form>';
    print_x($title,$hl);
}