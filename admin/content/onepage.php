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
    G('MODULE','onepage');
    // 设置公共菜单
    G('TABS',
        L('title').':onepage.php;'.
        L('add/@title').':onepage.php?action=edit'
    );
}
// lazy_default *** *** www.LazyCMS.net *** ***
function lazy_default(){ 
    $db = get_conn(); check_login('onepage');
    $ds = new Recordset();
    $ds->create("SELECT * FROM `#@_onepage` WHERE `oneid1`=0 ORDER BY `oneorder` DESC");
    $ds->action = PHP_FILE."?action=set";
    $ds->but = $ds->button();
    $ds->td  = "cklist(K[0]) + icon('dir'+K[4]) + K[0] + ') <a href=\"".PHP_FILE."?action=edit&oneid=' + K[0] + '\">' + K[1] + '</a>'";
    $ds->td  = "(K[3]?icon('link',K[2]):icon('link-error',K[2])) + K[2]";
    $ds->td  = "icon('edit','".PHP_FILE."?action=edit&oneid=' + K[0])";
    $ds->open();
    $ds->thead = '<tr><th>ID) '.L('list/name').'</th><th>'.L('list/path').'</th><th>'.L('common/action','system').'</th></tr>';
    while ($rs = $ds->result()) {
        $isSub = Onepage::__sub($rs['oneid']);
        $ds->tbody = "E(".$rs['oneid'].",'".t2js(h2encode($rs['onename']))."','".t2js(h2encode(SITE_BASE.$rs['onepath']))."',".(is_file(LAZY_PATH.$rs['onepath'])?1:0).",{$isSub});addSub(".$rs['oneid'].",1,{$isSub});";
    }
    $ds->close();

    print_x(L('title'),$ds->fetch());
}
// lazy_set *** *** www.LazyCMS.net *** ***
function lazy_set(){
    check_login('onepage');
    $db = get_conn();
    $submit = isset($_POST['submit']) ? strtolower($_POST['submit']) : null;
    $lists  = isset($_POST['lists']) ? $_POST['lists'] : null;
    switch($submit){
        case 'delete':
            empty($lists) ? echo_json(L('list/pop/select'),0) : null ;
            $db->update('#@_onepage',array('oneid1'=>0),"`oneid1` IN({$lists})");
            $db->exec("DELETE FROM `#@_onepage` WHERE `oneid` IN({$lists});");
            echo_json(array(
                'text' => L('list/pop/deleteok'),
                'url'  => $_SERVER["HTTP_REFERER"],
            ),1);
            break;
        case 'getsub':
            $space  = isset($_POST['space']) ? $_POST['space'] : 1;
            $result = $db->query("SELECT * FROM `#@_onepage` WHERE `oneid1`=? ORDER BY `oneorder` DESC",$lists);
            $array  = array();
            while ($rs = $db->fetch($result)) {
                $isSub = Onepage::__sub($rs['oneid']);
                $array[] = array(
                    'id'    => $rs['oneid'],
                    'sub'   => $isSub,
                    'code'  => "R(".$rs['oneid'].",'".t2js(h2encode($rs['onename']))."','".t2js(h2encode(SITE_BASE.$rs['onepath']))."',".(is_file(LAZY_PATH.$rs['onepath'])?1:0).",{$isSub});",
                );
            }
            echo(json_encode($array));
            break;
    }
}
// lazy_edit *** *** www.LazyCMS.net *** ***
function lazy_edit(){
    $_USER    = check_login('onepage'); $db = get_conn();
    $oneid    = isset($_REQUEST['oneid']) ? $_REQUEST['oneid'] : 0;
    $title    = empty($oneid) ? L('add/@title') : L('edit/@title');
    $oneid1   = isset($_REQUEST['oneid1']) ? $_REQUEST['oneid1'] : 0;
    $onename  = isset($_POST['onename']) ? $_POST['onename'] : null;
    $onetitle = isset($_POST['onetitle']) ? $_POST['onetitle'] : null;
    $onepath  = isset($_POST['onepath']) ? $_POST['onepath'] : null;
    $snapimg  = isset($_POST['snapimg']) ? $_POST['snapimg'] : null;
    $dellink  = isset($_POST['dellink']) ? $_POST['dellink'] : null;
    $onecontent   = isset($_POST['onecontent']) ? $_POST['onecontent'] : null;
    $keywords     = isset($_POST['keywords']) ? $_POST['keywords'] : null;
    $description  = isset($_POST['description']) ? $_POST['description'] : null;
    $onetemplate  = isset($_POST['onetemplate']) ? $_POST['onetemplate'] : 'default.html';
    $autokeywords = isset($_POST['autokeywords']) ? $_POST['autokeywords'] : null;
    // 加载 Keywords 处理类
    import('system.keywords');
    $key = new Keywords();
    $val = new Validate();
    if ($val->method()) {
        $val->check('onename|1|'.L('add/check/name').'|1-50')
            ->check('onetitle|1|'.L('add/check/title').'|1-100')
            ->check('onepath|0|'.L('add/check/path').';onepath|5|'.L('add/check/path1').';onepath|4|'.L('add/check/path2')."|SELECT COUNT(`oneid`) FROM `#@_onepage` WHERE `onepath`='#pro#'".(empty($oneid)?null:" AND `oneid` <> {$oneid}"))
            ->check('description|1|'.L('add/check/description').'|0-250');
        if ($val->isVal()) {
            $val->out();
        } else {
            // 下载远程图片
            if ($snapimg) {
                $onecontent = snapImg($onecontent);
            }
            // 删除站外连接
            if ($dellink) {
                $onecontent = preg_replace('/<a([^>]*)href=["\']*(http|https)\:\/\/(?!'.preg_quote($_SERVER['HTTP_HOST'],'/').')([^>]*)>(.*)<\/a>/isU','$4',$onecontent);
            }
            // 自动截取简述
            $description = (strlen($description)==0) ? left(cls(preg_replace('/<[^>]*>/iU','',$onecontent)),180) : $description;
            if (empty($oneid)) {
                $db->insert('#@_onepage',array(
                    'oneid1'  => (int)$oneid1,
                    'oneorder'=> $db->max('oneid','#@_onepage'),
                    'onetitle'=> $onetitle,
                    'onepath' => $onepath,
                    'onename' => $onename,
                    'onecontent'  => $onecontent,
                    'onetemplate' => $onetemplate,
                    'description' => $description,
                ));
                $oneid = $db->lastId();
                $text = L('add/pop/addok');
            } else {
                $db->update('#@_onepage',array(
                    'oneid1'  => $oneid1,
                    'onetitle'=> $onetitle,
                    'onepath' => $onepath,
                    'onename' => $onename,
                    'onecontent'  => $onecontent,
                    'onetemplate' => $onetemplate,
                    'description' => $description,
                ),DB::quoteInto('`oneid` = ?',$oneid));
                $text = L('add/pop/editok');
            }
            // 自动获取关键词
            if ($autokeywords && empty($keywords)) {
                require_file('../system/common.php');
                $keywords = System::getKeywords($onetitle,$onecontent);
                $keywords = implode(',',$keywords);
            }
            $key->save($oneid,$keywords,C('GET_RELATED_KEY'));
            // 输出执行结果
            echo_json(array(
                'text' => $text,
                'url'  => PHP_FILE,
            ),1);
        }
    } else {
        if (!empty($oneid)) {
            $res = $db->query("SELECT * FROM `#@_onepage` WHERE `oneid`=?",$oneid);
            if ($rs = $db->fetch($res)) {
                $oneid1   = $rs['oneid1'];
                $onename  = h2encode($rs['onename']);
                $onetitle = h2encode($rs['onetitle']);
                $onepath  = h2encode($rs['onepath']);
                $onecontent  = $rs['onecontent'];
                $keywords    = $key->get($oneid);
                $description = $rs['description'];
                $onetemplate = h2encode($rs['onetemplate']);
            }
        }
    }

    $hl = '<form id="form1" name="form1" method="post" action="">';
    $hl.= '<fieldset><legend rel="tab"><a class="collapsed" rel=".more-attr" cookie="false">'.$title.'</a></legend>';
    $hl.= '<div class="more-attr">';
    $hl.= '<p><label>'.L('add/sort').'：</label>';
    $hl.= '<select name="oneid1" id="oneid1">';
    $hl.= '<option value="0">--- '.L('add/topsort').' ---</option>';
    $hl.= Onepage::__sort(0,0,$oneid,$oneid1);
    $hl.= '</select></p>';
    $hl.= '<p><label>'.L('add/name').'：</label><input tip="'.L('add/name').'::'.L('add/name/@tip').'" class="in3" type="text" name="onename" id="onename" value="'.$onename.'" /></p>';
    $hl.= '<p><label>'.L('add/title').'：</label><input tip="'.L('add/title').'::'.h2encode(L('add/title/@tip')).'" class="in4" type="text" name="onetitle" id="onetitle" value="'.$onetitle.'" />';
    $hl.= '<span tip="'.L('add/autokeywords/@tip').'"><input type="checkbox" name="autokeywords" id="autokeywords" value="1" checked="checked" cookie="true" /><label for="autokeywords">'.L('add/autokeywords').'</label></span></p>';
    $hl.= '<p><label>'.L('add/path').'：</label><input tip="'.L('add/path').'::300::'.h2encode(L('add/path/@tip')).'" class="in5" type="text" name="onepath" id="onepath" value="'.$onepath.'" /></p>';
    $hl.= '<p><label>'.L('add/content').'：</label><div class="box">';
    $hl.= editor('onecontent',array(
        'upimg'   => true,
        'upfile'  => true,
        'snapimg' => array(1,1),
        'dellink' => array(1,0),
        'resize'  => true,
        'value'   => $onecontent,
        'editor'  => $_USER['editor']
    ));
    $hl.= '</div></p></div></fieldset>';
    $hl.= '<fieldset><legend><a class="collapse" rel=".more-attr">'.L('common/attr').'</a></legend>';
    $hl.= '<div class="more-attr">';
    $hl.= '<p><label>'.L('add/keywords').'：</label><input tip="'.L('add/keywords').'::250::'.L('add/keywords/@tip').'" class="in4" type="text" name="keywords" id="keywords" value="'.$keywords.'" />&nbsp;<button type="button" onclick="$(\'#keywords\').getKeywords(\'#onetitle\',\'#onecontent\')" tip="'.L('common/get/@tip','system').'">'.L('common/get','system').'</button></p>';
    $hl.= '<p><label>'.L('add/description').'：</label><textarea tip="'.L('add/description').'::'.L('add/description/@tip').'" name="description" id="description" rows="5" class="in4">'.$description.'</textarea></p>';
    $hl.= '<p><label>'.L('add/template').'：</label>';
    $hl.= '<select name="onetemplate" id="onetemplate">';
    $hl.= form_opts('themes/'.C('TEMPLATE'),'*','<option value="#value#"#selected#>#name#</option>',$onetemplate);
    $hl.= '</select></p>';
    $hl.= '</div></fieldset>';
    $hl.= but('save').'<input name="oneid" type="hidden" value="'.$oneid.'" /></form>';
    print_x($title,$hl);
}