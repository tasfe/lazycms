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
 * | Author: Lukin <mylukin@gmail.com>                                         |
 * +---------------------------------------------------------------------------+
 */
defined('CORE_PATH') or die('Restricted access!');
/**
 * 系统后台 Control 层
 *
 * @copyright   Copyright (c) 2007-2008 lazycms.net All rights reserved.
 * @author      Lukin <mylukin@gmail.com>
 */
// Control 类名称必须 以Lazy开头，且继承 LazyCMS基础类
class LazySystem extends LazyCMS{
    // _index *** *** www.LazyCMS.net *** ***
    function _index(){
        $tpl = getTpl($this);
        $tpl->display('index.php');
    }
    // _default *** *** www.LazyCMS.net *** ***
    function _default(){
        try {
            $db = getConn();
        } catch (Error $e) {
            if (is_file(LAZY_PATH.'install.php')) {
                redirect(LAZY_PATH.'install.php');exit;
            } else {
                throwError($e->getMessage(),$e->getCode());
            }
        }
        // 读取首页
        if (class_exists('Onepage')) {
            Onepage::index();
        } else {
            throwError($this->L('error/onepage'));
        }
    }
    // _main *** *** www.LazyCMS.net *** ***
    function _main(){
        // 权限检查
        $this->checker();
        $db = getConn();
        if(function_exists('gd_info')!==false){
            $gdInfo = gd_info();
            $gdInfo = ' <span class="gray">'.$gdInfo['GD Version'].'</span>';
        }
        // 取得模板对象
        $tpl = getTpl($this);
        $tpl->assign(array(
            'gdInfo' => $gdInfo,
            'mysql'  => $db->version(),
        ));
        $tpl->display('main.php');
    }
    // _log *** *** www.LazyCMS.net *** ***
    function _log(){
        $this->checker('log');
        // 创建记录对象
        $dp = O('Record');
        // 查询记录
        $dp->create('SELECT * FROM `#@_log` WHERE 1 ORDER BY `logid` DESC');
        // 设置提交的url
        $dp->action = url('System','LogSet');
        // 设置翻页url
        $dp->url = url('System','Log','page=$');
        // 设置按钮，button方法输出指定的按钮，plist方法输出分页
        $dp->but = $dp->button('logdelete:'.$this->L('log/delete').'|clear:'.$this->L('log/clear')).$dp->plist();
        // 设置第1个td的内容
        $dp->td  = "cklist(K[0]) + K[0] + ') ' + K[1]";
        // 设置第2个td的内容
        $dp->td  = 'K[3]';
        // 设置第3个td的内容
        $dp->td  = 'K[2]';
        // 设置第4个td的内容
        $dp->td  = 'K[4]';
        // 打开查询
        $dp->open();
        // 设置thead
        $dp->thead  = '<tr><th>'.$this->L('log/list/id').') '.$this->L('log/list/name').'</th><th>'.$this->L('log/list/num').'</th><th>'.$this->L('log/list/ip').'</th><th>'.$this->L('log/list/date').'</th></tr>';
        
        // 循环设置tbody
        while ($data = $dp->result()) {
            $dp->tbody = "ll(".$data['logid'].",'".t2js($data['adminname'])."','".$data['ip']."','".$this->L('log/l'.$data['lognum'])."','".date('Y-m-d H:i:s',$data['logdate'])."');";
        }
        // 关闭对象
        $dp->close();
        // 将生成的html扔给this->record()
        $this->outHTML = $dp->fetch;

        // 取得模板对象
        $tpl = getTpl($this);
        $tpl->assign('menu',$this->L('admin/title').'|'.url('System','Main').';'.$this->L('log/@title').'|#|true');
        // 显示模板
        $tpl->display('__public.php');
    }
    // _logset *** *** www.LazyCMS.net *** ***
    function _logset(){
        clearCache();
        $this->checker('log',true);
        $db     = getConn();
        $submit = isset($_POST['submit']) ? $_POST['submit'] : null;
        switch($submit){
            case 'clear' :
                $db->exec("TRUNCATE TABLE `#@_log`;");
                $this->poping($this->L('log/pop/clearok'),1);
                break;
            case 'delete' :
                $lists = isset($_POST['lists']) ? $_POST['lists'] : null;
                if (empty($lists)) {
                    $this->poping($this->L('log/pop/select'),0);
                }
                $db->exec("DELETE FROM `#@_log` WHERE `logid` IN({$lists});");
                $this->poping($this->L('log/pop/deleteok'),1);
                break;
            case 'logdelete' ://删除过期日志 7天
                $db->exec("DELETE FROM `#@_log` WHERE `logdate`<".(now()-(7*24*3600)).";");
                $this->poping($this->L('log/pop/logdeleteok'),1);
                break;
            default :
                $this->poping(L('error/invalid'),0);
                break;
        }
    }
    // _admin *** *** www.LazyCMS.net *** ***
    function _admin(){
        $this->checker('admin');
        $dp = O('Record');
        $dp->create('SELECT * FROM `#@_admin` WHERE 1 ORDER BY `adminid` DESC');
        $dp->action = url('System','AdminSet');
        $dp->url = url('System','Admin','page=$');
        $dp->but = $dp->button().$dp->plist();
        $dp->td  = "cklist(K[0]) + K[0] + ') <a href=\"".url('System','AdminEdit','adminid=$',"' + K[0] + '")."\">' + K[1] + '</a>'";
        $dp->td  = 'K[2]';
        $dp->td  = 'K[3]';
        $dp->td  = 'K[4]';
        $dp->td  = 'K[5]';
        $dp->open();
        $dp->thead  = '<tr><th>'.$this->L('admin/list/name').'</th><th>'.$this->L('admin/list/level').'</th><th>'.$this->L('admin/list/language').'</th><th>'.$this->L('admin/list/editor').'</th><th>'.$this->L('admin/list/regdate').'</th></tr>';
        while ($data = $dp->result()) {
            $dp->tbody = "ll(".$data['adminid'].",'".t2js(htmlencode($data['adminname']))."','".($data['adminlevel']=='admin' ? $this->L('admin/level/super') : $this->L('admin/level/editor'))."','".langbox($data['adminlanguage'])."','".$data['admineditor']."','".date('Y-m-d H:i:s',$data['admindate'])."');";
        }
        $dp->close();

        $this->outHTML = $dp->fetch;

        $tpl = getTpl($this);
        $tpl->assign('menu',$this->L('admin/list').'|#|true;'.$this->L('admin/add').'|'.url('System','AdminEdit'));
        $tpl->display('__public.php');
    }
    // _logset *** *** www.LazyCMS.net *** ***
    function _adminset(){
        clearCache();
        $this->checker('admin',true);
        $db     = getConn();
        $submit = isset($_POST['submit']) ? $_POST['submit'] : null;
        switch($submit){
            case 'delete' :
                $lists = isset($_POST['lists']) ? $_POST['lists'] : null;
                if (empty($lists)) {
                    $this->poping($this->L('admin/pop/select'),0);
                }
                $where = $db->quoteInto('`adminid` = ?',$this->admin['adminid']);
                $db->exec("DELETE FROM `#@_admin` WHERE `adminid` IN({$lists}) AND NOT({$where});");
                $this->poping($this->L('admin/pop/deleteok'),1);
                break;
            default :
                $this->poping(L('error/invalid'),0);
                break;
        }
    }
    // _adminedit *** *** www.LazyCMS.net *** ***
    function _adminedit(){
        $this->checker('admin');
        $tpl = getTpl($this);
        $db  = getConn();
        $sql = "adminname,adminpass,adminlevel,adminlanguage,admineditor,diymenu";//5
        $adminid = isset($_REQUEST['adminid']) ? $_REQUEST['adminid']:0;
        if (empty($adminid)) {
            $menu = $this->L('admin/add').'|#|true';
        } else {
            $menu = $this->L('admin/add').'|'.url('System','AdminEdit').';'.$this->L('admin/edit').'|#|true';
        }
        // 循环取得各POST值
        foreach (explode(',',$sql) as $val) {
            $data[]= isset($_POST[$val]) ? $_POST[$val] : null;
        }
        $data[6] = isset($_POST['adminpass1']) ? $_POST['adminpass1'] : null;
        if (empty($data[2])) {
            $data[2] = isset($_POST['level']) ? $_POST['level'] : null;
        }
        // 验证
        $this->validate(array(
            'adminname' => empty($adminid) ? $this->check("adminname|1|".$this->L('admin/check/name')."|2-12;adminname|3|".$this->L('admin/check/name1')."|SELECT COUNT(`adminid`) FROM `#@_admin` WHERE `adminname`='#pro#'") : null,
            'adminpass' => (!empty($data[1]) || !empty($data[6]) || empty($adminid)) ? $this->check('adminpass|2|'.$this->L('admin/check/contrast').'|adminpass1;adminpass|1|'.$this->L('account/check/pwdsize').'|6-30') : null,
        ));
        // 验证通过
        if ($this->method()) {
            if ($this->validate()) {
                if (is_array($data[2])) {
                    $data[2] = '0,'.implode(',',$data[2]);
                }
                if(!empty($data[1])){
                    $newkey  = salt();
                    $data[1] = md5($data[1].$newkey);
                }
                if(empty($adminid)){//insert
                    $row = array(
                        'adminname'     => $data[0],
                        'adminpass'     => $data[1],
                        'adminkey'      => $newkey,
                        'adminlevel'    => $data[2],
                        'adminlanguage' => $data[3],
                        'admineditor'   => $data[4],
                        'admindate'     => now(),
                        'diymenu'       => $data[5],
                    );
                    $db->insert('#@_admin',$row);
                } else {//update
                    // 更新用户资料
                    $set = array(
                        'adminlevel'    => $data[2],
                        'adminlanguage' => $data[3],
                        'admineditor'   => $data[4],
                        'diymenu'       => $data[5],
                    );
                    $where = $db->quoteInto('`adminid` = ?',$adminid);
                    $db->update('#@_admin',$set,$where);
                    // 更新密码
                    if(!empty($data[1])){
                        $set = array(
                            'adminpass' => $data[1],
                            'adminkey'  => $newkey,
                        );
                        $where = $db->quoteInto('`adminid` = ?',$adminid);
                        $db->update('#@_admin',$set,$where);
                    }
                }
                redirect(url('System','Admin'));
            }
        } else {
            if (!empty($adminid)) {
                $where = $db->quoteInto('WHERE `adminid` = ?',$adminid);
                $res   = $db->query("SELECT {$sql} FROM `#@_admin` {$where};");
                if ($data = $db->fetch($res,0)) {
                    if ($data[0]==Cookie::get('adminname')) {
                        redirect(url('System','MyAccount')); exit(0);
                    }
                } else {
                    throwError($this->L('error/invalid'));
                }
            }
        }
        $modules = empty($this->system['modules']) ? array() : explode(',',$this->system['modules']);
        $tpl->assign(array(
            'adminid'       => $adminid,
            'adminname'     => htmlencode($data[0]),
            'adminlanguage' => htmlencode($data[3]),
            'admineditor'   => htmlencode($data[4]),
            'adminlevel'    => htmlencode($data[2]),
            'diymenu'       => htmlencode($data[5]),
            'levels'        => array('config','log','diymenu','module','models','filemanage'),
            'modules'       => $modules,
            'menu'          => $menu,
        ));
        $tpl->display('adminedit.php');
    }
    // _myaccount *** *** www.LazyCMS.net *** ***
    function _myaccount(){
        $this->checker();
        $tpl = getTpl($this);
        $db  = getConn();
        $adminid       = $this->admin['adminid'];
        $adminpass     = isset($_POST['adminpass']) ? $_POST['adminpass'] : null;
        $adminpass1    = isset($_POST['adminpass1']) ? $_POST['adminpass1'] : null;
        $adminlanguage = isset($_POST['adminlanguage']) ? $_POST['adminlanguage'] : null;
        $admineditor   = isset($_POST['admineditor']) ? $_POST['admineditor'] : null;
        $diymenu       = isset($_POST['diymenu']) ? $_POST['diymenu'] : null;
        // 验证
        $this->validate(array(
            'adminpass' => (!empty($adminpass) || !empty($adminpass1)) ? $this->check('adminpass|2|'.$this->L('admin/check/contrast').'|adminpass1;adminpass|1|'.$this->L('account/check/pwdsize').'|6-30') : null,
        ));
        if ($this->method()) {
            if ($this->validate()) {
                // 更新用户资料
                $set = array(
                    'adminlanguage' => $adminlanguage,
                    'admineditor'   => $admineditor,
                    'diymenu'       => $diymenu,
                );
                $where = $db->quoteInto('`adminid` = ?',$adminid);
                $db->update('#@_admin',$set,$where);
                // 更新密码
                if(!empty($adminpass)){
                    $newkey  = substr($this->admin['adminpass'],0,6);
                    $newpass = md5($adminpass.$newkey);
                    $set = array(
                        'adminpass' => $newpass,
                        'adminkey'  => $newkey,
                    );
                    $where = $db->quoteInto('`adminid` = ?',$adminid);
                    $db->update('#@_admin',$set,$where);
                    // 重置Cookie密码
                    Cookie::set('adminpass',$newpass);
                }
                Cookie::set('language',$adminlanguage);
                $this->succeed($this->L('common/upok'));
            }
        } else {
            $adminlanguage = $this->admin['adminlanguage'];
            $admineditor   = $this->admin['admineditor'];
            $diymenu       = $this->admin['diymenu'];
        }
        $tpl->assign(array(
            'adminname'     => htmlencode($this->admin['adminname']),
            'adminlanguage' => htmlencode($adminlanguage),
            'admineditor'   => htmlencode($admineditor),
            'diymenu'       => htmlencode($diymenu),
        ));
        $tpl->display('myaccount.php');
    }
    // _config *** *** www.LazyCMS.net *** ***
    function _diymenu(){
        $this->checker('diymenu');
        $tpl = getTpl($this);
        $db  = getConn();
        $where = $db->quoteInto('`diymenulang` = ?',$this->admin['adminlanguage']);
        if ($this->method()) {
            $diyMenu = isset($_POST['diymenu']) ? $_POST['diymenu']:$this->admin['diymenu'];
            $num = $db->count("SELECT `diymenuid` FROM `#@_diymenu` WHERE {$where};");
            if ((int)$num > 0) { // update
                $set = array(
                    'diymenu' => $diyMenu,
                );
                $db->update('#@_diymenu',$set,$where);
            } else { // insert
                $row = array(
                    'diymenulang' => $this->admin['adminlanguage'],
                    'diymenu'     => $diyMenu,
                );
                $db->insert('#@_diymenu',$row);
            }
            $this->succeed($this->L('common/upok'));
        } else {
            $res = $db->query("SELECT `diymenu` FROM `#@_diymenu` WHERE {$where};");
            if ($data = $db->fetch($res,0)) {
                $diyMenu = $data[0];
            }
            if (empty($diyMenu)) {
                $diyMenu = defmenu();
            }
        }
        $tpl->assign('diyMenu',htmlencode($diyMenu));
        $tpl->display('diymenu.php');
    }
    // _config *** *** www.LazyCMS.net *** ***
    function _config(){
        $this->checker('config');
        $tpl = getTpl($this);
        $db  = getConn();
        $sitename = isset($_POST['sitename']) ? $_POST['sitename'] : null;
        $sitemail = isset($_POST['sitemail']) ? $_POST['sitemail'] : null;
        $keywords = isset($_POST['keywords']) ? $_POST['keywords'] : null;
        $lockip   = isset($_POST['lockip']) ? $_POST['lockip'] : null;
        $this->validate(array(
            'sitename' => $this->check('sitename|1|'.$this->L('config/check/sitename').'|1-50'),
            'sitemail' => !empty($sitemail) ? $this->check('sitemail|validate|'.$this->L('config/check/sitemail').'|4') : null,
        ));
        if ($this->method()) {
            if ($this->validate()) {
                $set = array(
                    'sitename'     => $sitename,
                    'sitemail'     => $sitemail,
                    'sitekeywords' => $keywords,
                    'lockip'       => $lockip,
                );
                $where = $db->quoteInto('`systemname` = ?','LazyCMS');
                $db->update('#@_system',$set,$where);
                $this->succeed($this->L('common/upok'));
            }
        } else {
            $sitename = $this->system['sitename'];
            $sitemail = $this->system['sitemail'];
            $keywords = $this->system['sitekeywords'];
            $lockip   = $this->system['lockip'];
        }
        $tpl->assign(array(
            'sitename' => htmlencode($sitename),
            'sitemail' => htmlencode($sitemail),
            'keywords' => htmlencode($keywords),
            'lockip'   => htmlencode($lockip),
        ));
        $tpl->display('config.php');
    }
    // _module *** *** www.LazyCMS.net *** ***
    function _module(){
        $this->checker('module');
        $dir = getArrDir(C('PAGES_PATH'),'dir');
        $_html = '<script type="text/javascript">var lz_delete = \''.L('confirm/delete').'\';</script>';
        $_html.= '<table class="lz_table">';
        $_html.= '<thead><tr>';
        $_html.= '<th>'.$this->L('module/list/name').'</th>';
        $_html.= '<th>'.$this->L('module/list/version').'</th>';
        $_html.= '<th>'.$this->L('module/list/folder').'</th>';
        $_html.= '<th>'.$this->L('module/list/author').'</th>';
        $_html.= '<th>'.$this->L('module/list/source').'</th>';
        $_html.= '<th>'.$this->L('module/list/mail').'</th>';
        $_html.= '<th>'.$this->L('module/list/is').'</th>';
        $_html.= '</tr></thead>';

        $_html.= '<tbody>';
        foreach ($dir as $k=>$v) {
            if (strtolower($v) != 'system') {
                if (instr($this->system['modules'],$v)) {
                    $isInstall = $this->L('module/is/true');
                    $isAction  = 'delete';
                    $isExists  = null;
                    $module    = '<a href="'.eval('return '.L('manage',null,$v).';').'">'.L('title',null,$v).'</a>';
                    $config    = is_file(LAZY_PATH.C('PAGES_PATH').'/'.$v.'/config.php') ? " [<a href=\"javascript:void(0);\" onclick=\"$(this).gm('config',{module:'".$v."'},{width:'600px','margin-left':'-300px',height:'300px'});\">".$this->L('common/config')."</a>]" : null;
                } else {
                    $isInstall = $this->L('module/is/false');
                    $isAction  = 'install';
                    $isExists  = ' class="red"';
                    $module    = L('title',null,$v);
                    $config    = null;
                }
                $help  = is_file(LAZY_PATH.C('PAGES_PATH').'/'.$v.'/help/help.html') ? " [<a href=\"javascript:void(0);\" onclick=\"$(this).gm('help',{module:'".$v."'},{width:'600px','margin-left':'-300px',height:'300px'});\">".$this->L('common/help')."</a>]" : null;
                $about = is_file(LAZY_PATH.C('PAGES_PATH').'/'.$v.'/help/about.html') ? " [<a href=\"javascript:void(0);\" onclick=\"$(this).gm('about',{module:'".$v."'});\">".$this->L('common/about')."</a>]" : null;
                $_html.= '<tr'.$isExists.'><td>'.($k+1).') '.$module.'</td>';
                $_html.= '<td>'.L('version',null,$v).'</td>';
                $_html.= '<td>'.$v.'</td>';
                $_html.= '<td>'.L('author',null,$v).'</td>';
                $_html.= '<td>'.L('source',null,$v).'</td>';
                $_html.= '<td>'.L('email',null,$v).'</td>';
                $_html.= '<td>'.$isInstall.$config.' [<a href="javascript:void(0);" onclick="$(this).gm(\''.$isAction.'\',{module:\''.$v.'\'});">'.$this->L('common/'.$isAction).'</a>]'.$about.$help.'</td></tr>';
            }
        }
        $_html.= '</tbody></table>';
        $this->outHTML = $_html;

        $tpl = getTpl($this);
        $tpl->display('module.php');
    }
    // _moduleset *** *** www.LazyCMS.net *** ***
    function _moduleset(){
        clearCache();
        $this->checker('module',true);
        $db     = getConn();
        $submit = isset($_POST['submit']) ? $_POST['submit'] : null;
        $module = isset($_POST['module']) ? $_POST['module'] : null;
        $modules = $this->system['modules'];
        $path    = LAZY_PATH.C('PAGES_PATH').'/';
        $where   = $db->quoteInto('`diymenulang` = ?',$this->admin['adminlanguage']);
        switch($submit){
            case 'delete' :
                if (instr($modules,$module)) {
                    $tmpModules = array();
                    $_modules   = explode(',',$modules);
                    foreach ($_modules as $m) {
                        if ($m!=$module) {
                            if (file_exists($path.$m)) {
                                $tmpModules[] = $m;
                            }
                        }
                    }
                    $modules = implode(',',$tmpModules);
                    //删除插件菜单
                    $mMenu   = L('title',null,$module)."|".L('manage',null,$module)."\r\n";
                    $replace = $db->quoteInto("REPLACE(`diymenu`,?,'')",$mMenu);
                    $db->exec("UPDATE `#@_diymenu` SET `diymenu`={$replace} WHERE {$where};");
                }
                break;
            case 'install' :
                if (!instr($modules,$module)) {
                    if (file_exists($path.$module)) {       
                        $_module = ucfirst($module);
                        import("@.{$module}.module");
                        eval('$instSQL = '.$_module.'::instSQL();');
                        $db->batQuery($instSQL);
                        if (empty($modules)) {
                            $modules = $module;
                        } else {
                            $modules .= ','.$module;
                        }
                        //写入自定义菜单
                        $mMenu  = L('title',null,$module)."|".L('manage',null,$module)."\r\n";
                        $result = $db->query("SELECT `diymenu` FROM `#@_diymenu` WHERE {$where};");
                        if ($data = $db->fetch($result,0)) {
                            if (empty($data[0])) {
                                $data[0] = defmenu();
                            }
                            $set = array(
                                'diymenu' => $mMenu.str_replace($mMenu,'',$data[0]),
                            );
                            $db->update('#@_diymenu',$set,$where);
                        } else {
                            $row = array(
                                'diymenulang' => $this->admin['adminlanguage'],
                                'diymenu'     => $mMenu.defmenu(),
                            );
                            $db->insert('#@_diymenu',$row);
                        }
                    }
                }
                break;
            case 'about' :
                $this->poping(array(
                    'title' => $this->L('common/about').' - '.L('title',null,$module),
                    'main'  => ubbencode(loadFile(LAZY_PATH.C('PAGES_PATH').'/'.$module.'/help/about.html')),
                ),0);
                break;
            case 'help' :
                $this->poping(array(
                    'title' => $this->L('common/help').' - '.L('title',null,$module),
                    'main'  => ubbencode(loadFile(LAZY_PATH.C('PAGES_PATH').'/'.$module.'/help/help.html')),
                ),0);
                break;
            case 'config' :
                $config = M($module);
                $strCfg = loadFile(LAZY_PATH.C('PAGES_PATH').'/'.$module.'/config.php');
                $_html = '<style type="text/css">.lz_form .button{ margin:10px auto; text-align:center; }</style>';
                $_html.= '<form action="'.url('System','ModuleConfig').'" class="lz_form">';
                $_html.= '<table class="lz_table" style="width:97%;">';
                $_html.= '<thead><tr>';
                $_html.= '<th>Key</th>';
                $_html.= '<th>Value</th>';
                $_html.= '</tr></thead>';
                $_html.= '<tbody>';
                $input = null;
                foreach ($config as $k=>$v) {
                    if (is_numeric($v)) {
                        $v = (int)$v;
                    } elseif (is_bool($v)) {
                        $v = $v ? 'true' : 'false';
                    }
                    preg_match("/\'".preg_quote($k,'/')."\'( *)\=\>( *)(\'?)(.+)(\'?)( *)\,( *)\/\/( *)(.+)?/i",$strCfg,$info);
                    $noteValue = isset($info[9]) && !empty($info[9]) ? htmlencode($info[9]) : null;
                    $noteLabel = null;
                    if (!empty($noteValue)) {
                        $noteLabel = '<input name="'.$k.'__note" type="hidden" value="'.$noteValue.'" /><label class="error" for="'.$k.'">'.$noteValue.'</label>';
                        $input    .= $k.'__note,';
                    }
                    $_html.= '<tr>';
                    $_html.= '<td><strong>'.strtoupper($k).'</strong></td>';
                    $_html.= '<td><input id="'.$k.'" name="'.$k.'" type="text" class="in3" value="'.$v.'"/>'.$noteLabel.'</td>';
                    $_html.= '</tr>';
                    $input.= $k.',';
                }
                $_html.= '</tbody></table>';
                $_html.= '<div class="button">';
                $_html.= '<button type="button" onclick="javascript:$(this).gm(\'submit\',inputValue({module:\''.$module.'\'},\''.rtrim($input,',').'\'));">'.L("common/submit").'</button>';
                $_html.= '<button type="reset" onclick="javascript:return confirm(\''.L('confirm/reset').'\')">'.L('common/reset').'</button>';
                $_html.= '<button type="button" class="close">'.L('common/close').'</button>';
                $_html.= '</div>';
                $_html.= '</form>';
                
                $this->poping(array(
                    'title' => $this->L('config/@title').' - '.L('title',null,$module),
                    'main'  => $_html,
                ));
                break;
            default :
                $this->poping(L('error/invalid'),0);
                break;
        }
        // 重置模块安装字符串
        $set = array(
            'modules' => $modules,
        );
        $where = $db->quoteInto('`systemname` = ?','LazyCMS');
        $db->update('#@_system',$set,$where);

        if ($submit=='install') {
            $this->poping(array(
                'title' => L('title',null,$module).' - '.$this->L('common/memo'),
                'main'  => $this->L('module/tip/install').'<br/><a href="'.eval('return '.L('manage',null,$module).';').'">【'.$this->L("module/tip/welcome").'】</a>',
            ),1);
        } else {
            $this->poping(array(
                'title' => L('title',null,$module).' - '.$this->L('common/memo'),
                'main'  => $this->L('module/tip/delete'),
            ),1);
        }
    }
    // _moduleConfig *** *** www.LazyCMS.net *** ***
    function _moduleConfig(){
        clearCache();
        $this->checker('module',true); 
        $submit = isset($_POST['submit']) ? $_POST['submit'] : null; unset($_POST['submit']);
        $module = isset($_POST['module']) ? $_POST['module'] : null; unset($_POST['module']);
        M($module);
        switch($submit){
            case 'submit';
                unset($_POST['lists']);
                $array = array();
                foreach ($_POST as $k=>$v) {
                    if (is_numeric(trim($v))) {
                        $v = (int)$v;
                    } elseif (trim($v)=='true' || trim($v)=='false') {
                        if (trim($v)=='false') {
                            $v = false;
                        } else {
                            $v = true;
                        }
                    }
                    if (substr($k,-6)!='__note') {
                        M($module,$k,$v);
                    } else {
                        $array[substr($k,0,strlen($k)-6)] = $v;
                    }
                }
                $config = var_export(array_change_key_case(M($module),CASE_UPPER),true);
                foreach ($array as $k=>$v) {
                    $config = replace("/\'".preg_quote($k,'/')."\'( *)\=\>( *)(\'?)(.+)(\'?)( *)\,/i","\${0} // ".htmldecode($v),$config);
                }
                saveFile(LAZY_PATH.C('PAGES_PATH').'/'.$module.'/config.php',"<?php\n".createNote(ucfirst($module).' module configuration files')."\nreturn ".$config.";\n?>");
                break;
            default :
                $this->poping(L('error/invalid'),0);
                break;
        }
        $this->poping($this->L('module/pop/ok'),0);
    }
    // _models *** *** www.LazyCMS.net *** ***
    function _models(){
        $this->checker('models');
        $db = getConn();
        $dp = O('Record');
        $dp->action = url('System','ModelSet');
        $dp->result = $db->query("SELECT * FROM `#@_model` WHERE 1 ORDER BY `modelid` ASC;");
        $dp->length = $db->count($dp->result);
        $dp->but = $dp->button();
        $dp->td  = "cklist(K[0]) + K[0] + ') <a href=\"".url('System','ModelFields','modelid=$',"' + K[0] + '")."\">' + K[1] + '</a>'";
        $dp->td  = 'K[2]';
        $dp->td  = 'K[3]';
        $dp->td  = "state(K[4],'".url('System','ModelState','modelid=$&state=1',"' + K[0] + '")."','".url('System','ModelState','modelid=$&state=0',"' + K[0] + '")."')";
        $dp->td  = "ico('edit','".url('System','ModelEdit','modelid=$',"' + K[0] + '")."') + ico('export','".url('System','ModelExport','modelid=$',"' + K[0] + '")."') + ico('fields','".url('System','ModelFields','modelid=$',"' + K[0] + '")."')";
        $dp->open();
        $dp->thead = '<tr><th>'.$this->L('models/list/id').') '.$this->L('models/list/name').'</th><th>'.$this->L('models/list/ename').'</th><th>'.$this->L('models/list/addtable').'</th><th>'.$this->L('models/list/state').'</th><th>'.$this->L('models/list/action').'</th></tr>';
        
        while ($data = $dp->result()) {
            $dp->tbody = "ll(".$data['modelid'].",'".t2js(htmlencode($data['modelname']))."','".t2js(htmlencode($data['modelename']))."','".htmlencode($data['addtable'])."',".$data['modelstate'].");";
        }
        $dp->close();

        $this->outHTML = $dp->fetch;

        $tpl = getTpl($this);
        $tpl->assign('menu',$this->L('models/@title').'|#|true;'.$this->L('models/add').'|'.url('System','ModelEdit').';'.$this->L('models/leadin').'|'.url('System','ModelLeadIn'));
        $tpl->display('__public.php');
    }
    // _modelset *** *** www.LazyCMS.net *** ***
    function _modelset(){
        clearCache();
        $this->checker('models',true);
        $db     = getConn();
        $submit = isset($_POST['submit']) ? $_POST['submit'] : null;
        switch($submit){
            case 'delete' :
                $lists = isset($_POST['lists']) ? $_POST['lists'] : null;
                if (empty($lists)) {
                    $this->poping($this->L('models/pop/select'),0);
                }
                $res = $db->query("SELECT `addtable` FROM `#@_model` WHERE `modelid` IN({$lists})");
                while ($data = $db->fetch($res,0)){
                    $db->exec("DROP TABLE IF EXISTS `".$data[0]."`;");
                }
                $db->exec("DELETE FROM `#@_fields` WHERE `modelid` IN({$lists});");
                $db->exec("DELETE FROM `#@_model` WHERE `modelid` IN({$lists});");
                $this->poping($this->L('models/pop/deleteok'),1);
                break;
            default :
                $this->poping(L('error/invalid'),0);
                break;
        }
    }
    // _modelstate *** *** www.LazyCMS.net *** ***
    function _modelstate(){
        $this->checker('models');
        $modelid = isset($_GET['modelid']) ? (int)$_GET['modelid'] : null;
        $state   = isset($_GET['state']) ? (string)$_GET['state'] : null;
        $db  = getConn();
        $set = array(
            'modelstate' => $state,
        );
        $where = $db->quoteInto('`modelid` = ?',$modelid);
        $db->update('#@_model',$set,$where);
        redirect($_SERVER['HTTP_REFERER']);
    }
    // _modelexport *** *** www.LazyCMS.net *** ***
    function _modelexport(){
        clearCache();
        $this->checker('models');
        $db = getConn();
        $modelid = isset($_GET['modelid']) ? (int)$_GET['modelid'] : null;
        
        $XML = array();
        $res = $db->query("SELECT * FROM `#@_model` WHERE `modelid`='{$modelid}';");
        if ($data = $db->fetch($res)) {
            unset($data['modelid']);
            $modelName = $data['modelename'];
            $XML['model'] = $data;
        } else {
            $modelName = 'Error';
        }
        $res = $db->query("SELECT * FROM `#@_fields` WHERE `modelid`='{$modelid}' ORDER BY `fieldorder` ASC,`fieldid` ASC;");
        while ($data = $db->fetch($res)){
            unset($data['fieldid'],$data['modelid'],$data['fieldorder']);
            $XML['fields'][] = $data;
        }
        ob_start();
        header("Content-type: application/octet-stream; charset=utf-8");
        header("Content-Disposition: attachment; filename=LazyCMS_".$modelName.".mod");
        header("Pragma: no-cache");
        header("Expires: 0");
        echo chunk_split(base64_encode(xmlcode($XML)));
        ob_flush();
    }
    // _modelleadin *** *** www.LazyCMS.net *** ***
    function _modelleadin(){
        $this->checker('models');
        $field = 'model';
        if ($this->method()) {
            $upload = O('UpLoadFile');
            $upload->allowExts = "mod";
            $upload->maxSize   = 500*1024;//500K
            if ($file = $upload->save($field,LAZY_PATH.basename($_FILES[$field]['name']))) {
                $modelCode = loadFile($file['path']); @unlink($file['path']);
                if (!empty($modelCode)) {
                    System::installModel($modelCode);
                }
                redirect(url('System','Models'));
            } else {
                $this->validate(array(
                    $field => $upload->getError(),
                ));
            }
        }
        $tpl = getTpl($this);
        $tpl->display('modelleadin.php');
    }
    // _modeledit *** *** www.LazyCMS.net *** ***
    function _modeledit(){
        $this->checker('models');
        $db      = getConn();
        $modelid = isset($_REQUEST['modelid']) ? (int)$_REQUEST['modelid'] : null;
        $sql     = "modelname,modelename,maintable";//3
        foreach (explode(',',$sql) as $val) {
            $data[]= isset($_POST[$val]) ? $_POST[$val] : null;
        }
        if (empty($modelid)) {
            $menu = $this->L('models/add').'|#|true';
        } else {
            $menu = $this->L('models/add').'|'.url('System','ModelEdit').';'.$this->L('models/edit').'|#|true';
        }

        $this->validate(array(
            'modelname'  => $this->check('modelname|1|'.$this->L('models/check/name').'|1-50'),
            'modelename' => $this->check('modelename|1|'.$this->L('models/check/ename').'|1-50;modelename|validate|'.$this->L('models/check/ename1').'|^[A-Za-z0-9\_]+$'),
        ));
        if ($this->method()) {
            if ($this->validate()) {
                if(empty($modelid)){//insert
                    $row = array(
                        'modelname'  => $data[0],
                        'modelename' => $data[1],
                        'maintable'  => $data[2],
                        'addtable'   => '#@_'.C('MODEL_PREFIX').$data[1],
                    );
                    $db->insert('#@_model',$row);
                    // 删除已存在的表
                    $db->exec("DROP TABLE IF EXISTS `#@_".C('MODEL_PREFIX').$data[1]."`;");
                    // 创建新表
                    $db->exec("CREATE TABLE IF NOT EXISTS `#@_".C('MODEL_PREFIX').$data[1]."` (
                                `aid` int(11) NOT NULL,
                                PRIMARY KEY (`aid`)
                               ) ENGINE=MyISAM DEFAULT CHARSET=#~lang~#;");
                } else {//update
                    $set = array(
                        'modelname'  => $data[0],
                        'maintable'  => $data[2],
                    );
                    $where = $db->quoteInto('`modelid` = ?',$modelid);
                    $db->update('#@_model',$set,$where);
                }
                redirect(url('System','Models'));
            }
        } else {
            if (!empty($modelid)) {
                $where = $db->quoteInto('WHERE `modelid` = ?',$modelid);
                $res   = $db->query("SELECT {$sql} FROM `#@_model` {$where};");
                if (!$data = $db->fetch($res,0)) {
                    throwError(L('error/invalid'));
                }    
            }
        }

        $tpl = getTpl($this);
        $tpl->assign(array(
            'modelid'    => $modelid,
            'modelname'  => htmlencode($data[0]),
            'modelename' => htmlencode($data[1]),
            'maintable'  => htmlencode($data[2]),
            'menu'       => $menu,
            'readonly'   => !empty($modelid) ? ' readonly="true"' : null,
        ));
        $tpl->display('modeledit.php');
    }
    // _modelfields *** *** www.LazyCMS.net *** ***
    function _modelfields(){
        $this->checker('models');
        $db      = getConn();
        $modelid = isset($_REQUEST['modelid']) ? (int)$_REQUEST['modelid'] : null;
        $dp = O('Record');
        $dp->action = url('System','ModelFieldSet','modelid='.$modelid);
        $dp->result = $db->query("SELECT * FROM `#@_fields` WHERE `modelid`='{$modelid}' ORDER BY `fieldorder` ASC, `fieldid` ASC;");
        $dp->length = $db->count($dp->result);
        $dp->but = $dp->button();
        $dp->td  = "cklist(K[0]) + '<a href=\"".url('System','ModelFieldsEdit','modelid=:modelid&fieldid=:fieldid',array('modelid'=>"'+K[7]+'",'fieldid'=>"'+K[0]+'"))."\">' + K[0] + ') ' + K[1] + '</a>'";
        $dp->td  = "K[2]";
        $dp->td  = "K[3] + (K[9]=='input' ? '(' + K[4] + ')' : '')";
        $dp->td  = "(K[5]=='' ? 'NULL' : K[5])";
        $dp->td  = "index(K[8],K[6],'".url('System','ModelFieldIndex','modelid=:modelid&fieldid=:fieldid&index=0',array('modelid'=>"'+K[7]+'",'fieldid'=>"'+K[0]+'"))."','".url('System','ModelFieldIndex','modelid=:modelid&fieldid=:fieldid&index=1',array('modelid'=>"'+K[7]+'",'fieldid'=>"'+K[0]+'"))."')";
        $dp->td  = "ico('edit','".url('System','ModelFieldsEdit','modelid=:modelid&fieldid=:fieldid',array('modelid'=>"'+K[7]+'",'fieldid'=>"'+K[0]+'"))."') + updown('up',K[0]) + updown('down',K[0])";
        $dp->open();
        $dp->thead = '<tr><th>'.$this->L('models/field/list/id').') '.$this->L('models/field/list/name').'</th><th>'.$this->L('models/field/list/ename').'</th><th>'.$this->L('models/field/list/type').'</th><th>'.$this->L('models/field/list/default').'</th><th>'.$this->L('models/field/list/key').'</th><th>'.$this->L('models/field/list/action').'</th></tr>';
        while ($data = $dp->result()) {
            $dp->tbody = "ll(".$data['fieldid'].",'".t2js(htmlencode($data['fieldname']))."','".t2js(htmlencode($data['fieldename']))."','".$this->L('models/field/type/'.$data['inputtype'])."','".t2js(htmlencode($data['fieldlength']))."','".t2js(htmlencode($data['fieldefault']))."',".$data['fieldindex'].",".$data['modelid'].",".(int)instr('text,mediumtext',$data['fieldtype']).",'".$data['inputtype']."');";
        }
        $dp->close();
        $this->outHTML = $dp->fetch;
        $tpl = getTpl($this);
        $tpl->assign('menu',$this->L('models/@title').'|'.url('System','Models').';'.$this->L('models/add').'|'.url('System','ModelEdit').';'.$this->L('models/field/@title').'|#|true;'.$this->L('models/field/add').'|'.url('System','ModelFieldsEdit','modelid='.$modelid));
        $tpl->display('__public.php');
    }
    // _modelfieldindex *** *** www.LazyCMS.net *** ***
    function _modelfieldindex(){
        $this->checker('models');
        $fieldid = isset($_GET['fieldid']) ? (int)$_GET['fieldid'] : null;
        $modelid = isset($_GET['modelid']) ? (int)$_GET['modelid'] : null;
        $index   = isset($_GET['index']) ? (string)$_GET['index'] : null;
        $db    = getConn();
        try{
            $where     = $db->quoteInto('`modelid` = :modelid AND `fieldid`= :fieldid ',array('modelid'=>$modelid,'fieldid'=>$fieldid));
            $addtable  = $db->result("SELECT `addtable` FROM `#@_model` WHERE `modelid`='{$modelid}';");
            $fieldname = $db->result("SELECT `fieldename` FROM `#@_fields` WHERE {$where};");
            // 修改为不索引
            if (empty($index)){
                $db->exec("ALTER TABLE `{$addtable}` DROP INDEX `{$fieldname}`;");
            } else {
                $db->exec("ALTER TABLE `{$addtable}` ADD INDEX ( `{$fieldname}` ) ;");
            }
            $set = array(
                'fieldindex' => $index,
            );
            $db->update('#@_fields',$set,$where);
        } catch(Error $err){}
        redirect($_SERVER['HTTP_REFERER']);
    }
    // _modelfieldset *** *** www.LazyCMS.net *** ***
    function _modelfieldset(){
        clearCache();
        $this->checker('models',true);
        $db      = getConn();
        $modelid = isset($_REQUEST['modelid']) ? (int)$_REQUEST['modelid'] : null;
        $submit  = isset($_POST['submit']) ? $_POST['submit'] : null;
        $lists   = isset($_POST['lists']) ? $_POST['lists'] : null;
        switch($submit){
            case 'delete' :
                if (empty($lists)) {
                    $this->poping($this->L('models/pop/select'),0);
                }
                // 取得附加表
                $addtable = $db->result("SELECT `addtable` FROM `#@_model` WHERE `modelid`='{$modelid}';");
                // 组合删除数据库字段的SQL语句
                $DelSQL = "ALTER TABLE `{$addtable}` ";
                $res = $db->query("SELECT `fieldename` FROM `#@_fields` WHERE `modelid`='{$modelid}' AND `fieldid` IN({$lists});");
                while ($data = $db->fetch($res,0)){
                    $DelSQL.= " DROP `".$data[0]."`,";
                }
                $DelSQL = rtrim($DelSQL,',').";";
                try { // 屏蔽所有错误
                    // 执行删除字段操作
                    $db->exec($DelSQL);
                    $db->exec("DELETE FROM `#@_fields` WHERE `modelid`='{$modelid}' AND `fieldid` IN({$lists});");
                    $this->poping($this->L('models/pop/deletefieldok'),1);
                } catch (Error $err) {
                    $db->exec("DELETE FROM `#@_fields` WHERE `modelid`='{$modelid}' AND `fieldid` IN({$lists});");
                    $this->poping($this->L('models/pop/deletefielderr'),1);
                }
                break;
            case 'updown' :
                $updown = isset($_POST['updown']) ? (string)$_POST['updown'] : null;
                $num    = isset($_POST['num']) ? (int)$_POST['num'] : null;
                $updown = $updown=='down' ? 'up' : 'down';
                $this->order("fields,fieldid,fieldorder","{$lists},{$updown},{$num}","`modelid`='{$modelid}'");
                break;
            default :
                $this->poping(L('error/invalid'),0);
                break;
        }
    }
    // _modelfieldsedit *** *** www.LazyCMS.net *** ***
    function _modelfieldsedit(){
        $this->checker('models');
        $db      = getConn();
        $modelid = isset($_REQUEST['modelid']) ? (int)$_REQUEST['modelid'] : null;
        $fieldid = isset($_REQUEST['fieldid']) ? (int)$_REQUEST['fieldid'] : null;
        $sql     = "fieldname,fieldename,fieldtype,fieldlength,fieldefault,fieldindex,inputtype,fieldvalue";//7
        foreach (explode(',',$sql) as $val) {
            $data[] = isset($_POST[$val]) ? $_POST[$val] : null;
        }
        $data[8] = isset($_POST['oldfieldename']) ? $_POST['oldfieldename'] : null;
        if (empty($fieldid)) {
            $menu = $this->L('models/field/add').'|#|true';
        } else {
            $menu = $this->L('models/field/add').'|'.url('System','ModelFieldsEdit','modelid='.$modelid).';'.$this->L('models/field/edit').'|#|true';
        }
        $this->validate(array(
            'fieldname'  => $this->check('fieldname|1|'.$this->L('models/field/check/name').'|1-50'),
            'fieldename' => $this->check('fieldename|1|'.$this->L('models/field/check/ename').'|1-50;fieldename|validate|'.$this->L('models/field/check/ename1').'|^[A-Za-z0-9\_]+$'),
            'fieldlength'=> instr('input',$data[6]) ? $this->check('fieldlength|1|'.$this->L('models/field/check/length').'|1-255;fieldlength|validate|'.$this->L('models/field/check/length1').'|2') : null,
            'fieldvalue' => instr('radio,checkbox,select',$data[6]) ? $this->check('fieldvalue|0|'.$this->L('models/field/check/value')) : null,
        ));
        if ($this->method()) {
            if ($this->validate()) {
                // 取得附加表
                $addtable = $db->result("SELECT `addtable` FROM `#@_model` WHERE `modelid`='{$modelid}';");
                if (instr('text,mediumtext,datetime',$data[2])) {
                    $data[3] = null;
                } else {
                    $data[3] = !empty($data[3]) ? $data[3] : 255;
                }
                $length  = !empty($data[3]) ? "( ".$data[3]." ) " : null;
                if ((string)$data[2]!='datetime') {
                    $default = (string)$data[4] ? " DEFAULT '".t2js($data[4])."' " : null;
                } else {
                    $default = null;
                }
                if(empty($fieldid)){//insert
                    $row = array(
                        'fieldorder'  => $db->max('fieldid','#@_fields'),
                        'modelid'     => $modelid,
                        'fieldname'   => $data[0],
                        'fieldename'  => $data[1],
                        'fieldtype'   => $data[2],
                        'fieldlength' => $data[3],
                        'fieldefault' => $data[4],
                        'fieldindex'  => !empty($data[5]) ? (string)$data[5] : '0',
                        'inputtype'   => $data[6],
                        'fieldvalue'  => $data[7],
                    );
                    $db->insert('#@_fields',$row);
                    // 向附加表添加对应字段
                    $SQL     = "ALTER TABLE `{$addtable}` ADD ";
                    $db->exec($SQL."`".$data[1]."` ".$data[2].$length.$default.";");
                    // 添加为索引字段
                    if (!empty($data[5])){ $db->exec($SQL."INDEX ( `".$data[1]."` ) ;"); }
                } else {//update
                    // 修改字段
                    $set = array(
                        'fieldname'   => $data[0],
                        'fieldename'  => $data[1],
                        'fieldtype'   => $data[2],
                        'fieldlength' => $data[3],
                        'fieldefault' => $data[4],
                        'inputtype'   => $data[6],
                        'fieldvalue'  => $data[7],
                    );
                    $where = $db->quoteInto('`modelid` = :modelid AND `fieldid`= :fieldid ',array('modelid'=>$modelid,'fieldid'=>$fieldid));
                    try{ // 删除索引，并修改字段为不索引
                        if (instr('text,mediumtext',$data[2])) {
                            $db->exec("ALTER TABLE `{$addtable}` DROP INDEX `".$data[1]."`;");
                            $set = array_merge($set,array(
                                'fieldindex' => '0'
                            ));
                        }
                    } catch(Error $err){}
                    $db->update('#@_fields',$set,$where);
                    $db->exec("ALTER TABLE `{$addtable}` CHANGE `".$data[8]."` `".$data[1]."` ".$data[2].$length.$default.";");
                }
                redirect(url('System','ModelFields',"modelid={$modelid}"));
            }
        } else {
            if (!empty($modelid) && !empty($fieldid)) {
                $where = $db->quoteInto('WHERE `modelid` = :modelid AND `fieldid`= :fieldid ',array('modelid'=>$modelid,'fieldid'=>$fieldid));
                $res   = $db->query("SELECT {$sql} FROM `#@_fields` {$where};");
                if (!$data = $db->fetch($res,0)) {
                    throwError(L('error/invalid'));
                }
            }
        }
        $tpl = getTpl($this);
        $tpl->assign(array(
            'fieldid'     => $fieldid,
            'modelid'     => $modelid,
            'fieldname'   => htmlencode($data[0]),
            'fieldename'  => htmlencode($data[1]),
            'fieldtype'   => htmlencode($data[2]),
            'fieldlength' => htmlencode($data[3]),
            'fieldefault' => htmlencode($data[4]),
            'inputtype'   => htmlencode($data[6]),
            'fieldvalue'  => htmlencode($data[7]),
            'menu'        => $menu,
            'fieldindex'  => !empty($data[5]) ? ' checked="true"' : null,
            'readonly'    => (!empty($modelid) && !empty($data[5])) ? ' readonly="true"' : null,
        ));
        $tpl->display('modelfieldsedit.php');
    }
    // _browsefiles *** *** www.LazyCMS.net *** ***
    function _browsefiles(){
        clearCache();
        $this->checker('filemanage',true);
        $action= isset($_POST['action']) ? (string)$_POST['action'] : null;
        $path  = isset($_POST['path']) ? (string)$_POST['path'] : null; $path = ltrim($path,'/');
        $from  = isset($_POST['from']) ? (string)$_POST['from'] : null;
        $FolderHTML = null;$FileHTML = null;
        $cPath = LAZY_PATH.$path;
        $uPath = C('SITE_BASE').$path;
        switch (strtolower($action)) {
            case 'delete': 
                $file = isset($_POST['file']) ? (string)$_POST['file'] : null;
                @unlink($file);
                break;
            case 'createfolder' :
                $folder = isset($_POST['folder']) ? (string)$_POST['folder'] : null;
                if (!empty($folder)) {
                    mkdirs($cPath.'/'.$folder);
                }
                break;
            case 'getfolder' :
                $onclick = "\$('#{$from}').browseFiles('".url('System','browseFiles')."',{folder:$('#folder').val(),action:'createfolder',from:'{$from}',path:'{$path}'});";
                $I1 = '<div class="pop">';
                $I1.= '<div class="pop-title"><span>'.$this->system['sitename'].'</span><a href="javascript:void(0);" onclick="$(\'#poping .pop\').remove();">×</a></div>';
                $I1.= '<div class="pop-main">';
                $I1.= '<p><label>'.L('filemanage/createfolder').'</label><input class="in3" name="folder" type="text" id="folder" /></p>';
                $I1.= '<button type="button" onclick="'.$onclick.'">'.L('common/submit').'</button></div></div>';
                echo $I1; return ;
                break;
            case 'uploadfile' :
                $I1 = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
                $I1.= '<html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><title>UpLoadFile</title></head><body>';
                $I1.= '<script type="text/javascript" charset="utf-8">';
                $upload = O('UpLoadFile');
                $upload->allowExts = C('UPFILE_SUFFIX').',zip,rar';
                $upload->maxSize   = 2*1024*1024;//2M
                $filePath = str_replace('//','/',$cPath.'/'.basename($_FILES['upfile']['name']));
                if ($file = $upload->save('upfile',$filePath)) {
                    if (instr('jpg,gif,png,bmp',fileICON($_FILES['upfile']['name']))) {
                        $filePath = str_replace(LAZY_PATH,'',$filePath);
                        $I1.= "window.parent.\$('#{$from}').browseFiles('".url('System','browseFiles')."',{from:'{$from}',path:'{$filePath}'});";
                    } else {
                        $I1.= "window.parent.\$('#{$from}').browseFiles('".url('System','browseFiles')."',{from:'{$from}',path:'{$path}'});";
                    }
                } else {
                    $I1.= "window.parent.alert('".$upload->getError()."');";
                }
                $I1.= '</script></body></html>';
                echo $I1; return ;
                break;
            case 'getloadfile' :
                $I1 = '<div class="pop">';
                $I1.= '<div class="pop-title"><span>'.$this->system['sitename'].'</span><a href="javascript:void(0);" onclick="$(\'#poping .pop\').remove();">×</a></div>';
                $I1.= '<div class="pop-main"><form action="'.url('System','browseFiles').'" method="post" enctype="multipart/form-data" target="___UpLoadFile">';
                $I1.= '<p><label>'.L('filemanage/createfolder').'</label><input class="in3" name="upfile" type="file" id="upfile" /></p>';
                $I1.= '<input name="action" type="hidden" value="uploadfile" />';
                $I1.= '<input name="from" type="hidden" value="'.$from.'" />';
                $I1.= '<input name="path" type="hidden" value="'.$path.'" />';
                $I1.= '<button type="submit">'.L('common/submit').'</button></form>';
                $I1.= '<iframe src="about:blank" name="___UpLoadFile" width="0" height="0" scrolling="no" frameborder="0"></iframe></div></div>';
                echo $I1; return ;
                break;
            case 'deletefolder':
                $folder = isset($_POST['folder']) ? (string)$_POST['folder'] : null;
                if (!empty($folder)) {
                    rmdirs(LAZY_PATH.$folder,true);
                }
                break;
        }
        mkdirs($cPath);
        $DelFolder    = null;
        $UpLoadFile   = null;
        $CreateFolder = null;
        if (is_dir($cPath)) {
            if ($path!='') {
                $bPath = str_replace('\\','/',dirname('/'.$path));
                $bPath = $bPath == '/' ? $bPath : ltrim($bPath,'/');
                $DelFolder    = "<a href=\"javascript:DelFolder('{$path}');\" onclick=\"if(confirm('".L('confirm/delete')."')){\$('#{$from}').browseFiles('".url('System','browseFiles')."',{action:'deletefolder',folder:'{$path}',from:'{$from}',path:'{$bPath}'});};return false;\"><img src=\"".LAZY_PATH.C('PAGES_PATH')."/system/images/os/del.gif\" alt=\"Delete Folder...\" class=\"os\"/></a>";
            }
            $UpLoadFile   = "<a href=\"javascript:UpLoadFile('{$path}');\" onclick=\"\$(this).getPoping('.toolbar','".url('System','browseFiles')."',{action:'getloadfile',path:'{$path}',from:'{$from}'});return false;\"><img src=\"".LAZY_PATH.C('PAGES_PATH')."/system/images/os/upfile.gif\" alt=\"UpLoad File...\" class=\"os\"/></a>";
            $CreateFolder = "<a href=\"javascript:CreateFolder('{$path}');\" onclick=\"\$(this).getPoping('.toolbar','".url('System','browseFiles')."',{action:'getfolder',path:'{$path}',from:'{$from}'});return false;\"><img src=\"".LAZY_PATH.C('PAGES_PATH')."/system/images/os/crtdir.gif\" alt=\"Create Folder...\" class=\"os\"/></a>";
        }
        $HTML = '<form class="lz_form"><div class="toolbar"><span class="in">'.System::filesPath($path,$from).'</span><div>'.$DelFolder.$CreateFolder.$UpLoadFile.'</div></div>';
        $HTML.= '<table class="lz_table" style="width:565px;margin:3px 0;clear:both;">';
        if (is_dir($cPath)) {
            $HTML.= '<tr><th>'.L('filemanage/template/filename').'</th><th class="wp5">'.L('filemanage/template/filemtime').'</th></tr>';
            $dh = opendir($cPath);
            $imgPath = LAZY_PATH.C('PAGES_PATH')."/system/images/os/";
            while (false !== ($file=readdir($dh))) {
                if ($file != ".") {
                    if ($file == "..") {
                        $FolderUp = str_replace('\\','/',dirname('/'.$path));
                        $FolderUp = $FolderUp == '/' ? $FolderUp : ltrim($FolderUp,'/');
                        $TR = "<tr><td colspan=\"2\"><a href=\"javascript:UpFolder();\" onclick=\"\$('#{$from}').browseFiles('".url('System','browseFiles')."','{$FolderUp}');return false;\">";
                        $TR.= "<img src=\"".$imgPath."folderup.gif\" class=\"os\"/> {$file}</a></td></tr>";
                        $FolderHTML.= $TR;
                    } else {
                        $cFile = ($path != '') ? $path.'/'.$file : $file ;
                        $uFile = rtrim($cPath,'/').'/'.$file;
                        if ((string)strtolower($file) == "thumbs.db") { unlink($uFile); continue;}
                        if (is_dir($uFile)) {
                            $TR = "<tr><td><a href=\"javascript:OpenFolder('{$file}');\" onclick=\"\$('#{$from}').browseFiles('".url('System','browseFiles')."','{$cFile}');return false;\">";
                            $TR.= "<img src=\"".$imgPath."folder.gif\" class=\"os\"/> {$file}</a></td>";
                            $TR.= "<td>".date('Y-m-d H:i:s',filemtime($uFile))."</td>";
                            $TR.= "</tr>";
                            $FolderHTML.= $TR;
                        } else {
                            $fileType = fileICON($file);
                            if (instr('jpg,gif,png,bmp',$fileType)) {
                                $a = "<a href=\"javascript:Preview('{$file}');\" onclick=\"\$('#{$from}').browseFiles('".url('System','browseFiles')."','{$cFile}');return false;\">";
                            } else {
                                $a = "<a href=\"javascript:Insert('{$file}');\" onclick=\"\$('#{$from}').val('{$cFile}');return false;\" class=\"close\">";
                            }
                            $TR = "<tr><td>{$a}";
                            $TR.= "<img src=\"".$imgPath."file/{$fileType}.gif\" class=\"os\"/> {$file}</a></td>";
                            $TR.= "<td>".date('Y-m-d H:i:s',filemtime($uFile))."</td>";
                            $TR.= "</tr>";
                            $FileHTML  .= $TR;
                        }
                    }
                }
            }
            closedir($dh);
            $HTML.= $FolderHTML.$FileHTML;
        } else {
            $bPath  = str_replace('\\','/',dirname('/'.$path));
            $bPath  = $bPath == '/' ? $bPath : ltrim($bPath,'/');
            $editor = "<a href=\"javascript:insertEditor();\" onclick=\"insertEditor('<img src=\'{$uPath}\' />');return false;\" class=\"close\">[".L('common/inserteditor')."]</a> ";
            $insert = "<a href=\"javascript:Insert();\" onclick=\"\$('#{$from}').val('{$path}');return false;\" class=\"close\">[".L('common/insert')."]</a> ";
            $delete = "<a href=\"javascript:Delete();\" onclick=\"if(confirm('".L('confirm/delete')."')){\$('#{$from}').browseFiles('".url('System','browseFiles')."',{action:'delete',path:'{$bPath}',file:'{$cPath}'});}return false;\">[".L('common/delete')."]</a> ";
            $back = "<a href=\"javascript:Back();\" onclick=\"\$('#{$from}').browseFiles('".url('System','browseFiles')."','{$bPath}');return false;\">[".L('common/back')."]</a> ";
            $HTML.= '<tr><td>'.$editor.$insert.$delete.$back.' <a href="javascript:Close();" class="close">['.L('common/close').']</a></td></tr>';
            $HTML.= '<tr><td style="text-align:center;"><a href="'.$cPath.'" target="_blank"><img src="'.$cPath.'" onload="if(this.width>558){this.width=558;}"/></a></td></tr>';
        }
        $HTML.= '</table></form>';

        $this->poping(array(
            'title' => L('filemanage/@title'),
            'main'  => $HTML,
        ));

    }
    // _version *** *** www.LazyCMS.net *** ***
    function _version(){
        import("system.downloader");
        $d = new DownLoader("http://www.lazycms.net/ver/index.php?".$_SERVER['HTTP_HOST'].C('SITE_BASE'));
        $d->send();
        if ($d->status() == 200) {
            $l1 = $d->body();
        }
        if (validate($l1,6)) {
            $I1 = $l1;
            if (version_compare($this->system['systemver'], $l1, '<' )) {
                $I1 .= ' <a href="http://www.lazycms.net/download" target="_blank">【'.$this->L('parameters/downnew').'】</a>';
                $I1 .= ' <a href="'.url('System','Update').'" onclick="$.posts(this.href,{version:\''.$l1.'\'});">【'.$this->L('parameters/update').'】</a>';
            }
        } else {
            $I1 = $this->L('parameters/newversionerr');
        }
        echo $I1;
    }
    // _update *** *** www.LazyCMS.net *** ***
    function _update(){
        clearCache();
        $this->checker('admin',true);
        $version = isset($_POST['version']) ? $_POST['version'] : null;
        $this->poping($this->L('parameters/upok'),1);
    }
    // _login *** *** www.LazyCMS.net *** ***
    function _login(){
        try {
            $db = getConn();
			$adminname = Cookie::get('adminname');
			$adminpass = Cookie::get('adminpass');
			if (!empty($adminname) && !empty($adminpass)) {
				$where = $db->quoteInto('WHERE `adminname` = ?',$adminname);
				$res   = $db->query("SELECT * FROM `#@_admin` {$where};");
				if ($data = $db->fetch($res)) {
					if ($adminpass==$data['adminpass']) {
						// 登录成功，写登录记录
						$row = array(
							'adminname' => $adminname,
							'ip'        => ip(),
							'lognum'    => 1,
							'logdate'   => now(),
						);
						$db->insert('#@_log',$row);
						redirect(url('System'));
					}
				}
			}
        } catch (Error $e) {
            if (is_file(LAZY_PATH.'install.php')) {
                redirect(LAZY_PATH.'install.php');exit;
            } else {
                throwError($e->getMessage(),$e->getCode());
            }
        }
        // 取得模板对象
        $tpl = getTpl($this);
        $adminname = isset($_POST['adminname']) ? $_POST['adminname'] : null;
        $adminpass = isset($_POST['adminpass']) ? $_POST['adminpass'] : null;
        $save      = isset($_POST['save']) ? $_POST['save'] : null;
        // 验证
        $this->validate(array(
            'adminname' => $this->check('adminname|1|'.$this->L('login/check/name').'|2-30'),
            'adminpass' => $this->check('adminpass|1|'.$this->L('login/check/pass').'|6-30'),
        ));
        // 验证通过
        if ($this->method() && $this->validate()) {
            $validity  = $save ? (now()+3600*24*7) : null;
            $where = $db->quoteInto('WHERE `adminname` = ?',$adminname);
            $res   = $db->query("SELECT * FROM `#@_admin` {$where};");
            if ($data = $db->fetch($res)) {
                $md5pass = md5($adminpass.$data['adminkey']);
                if ($md5pass==$data['adminpass']) {
                    $newkey  = substr($md5pass,0,6);
                    $newpass = md5($adminpass.$newkey);
                    // 更新数据
                    $set = array(
                        'adminpass' => $newpass,
                        'adminkey'  => $newkey,
                    );
                    $where = $db->quoteInto('`adminname` = ?',$adminname);
                    $db->update('#@_admin',$set,$where);
                    // 写登录记录
                    $row = array(
                        'adminname' => $adminname,
                        'ip'        => ip(),
                        'lognum'    => 1,
                        'logdate'   => now(),
                    );
                    $db->insert('#@_log',$row);
                    // 设置登陆信息
                    Cookie::set('adminname',$adminname,$validity);
                    Cookie::set('adminpass',$newpass,$validity);
                    Cookie::set('language',$data['adminlanguage'],$validity);
                    redirect(url('System'));
                } else {
                    // 写登录记录
                    $row = array(
                        'adminname' => $adminname,
                        'ip'        => ip(),
                        'lognum'    => 2,
                        'logdate'   => now(),
                    );
                    $db->insert('#@_log',$row);
                    // 密码不正确，登录失败
                    $this->validate(array(
                        'adminpass' => $this->L('login/check/error2'),
                    ));
                }
            } else {
                // 写登录记录
                $row = array(
                    'adminname' => $adminname,
                    'ip'        => ip(),
                    'lognum'    => 2,
                    'logdate'   => now(),
                );
                $db->insert('#@_log',$row);
                // 用户名不存在，登录失败
                $this->validate(array(
                    'adminname' => $this->L('login/check/error1'),
                ));
            }
        }
        $tpl->assign(array(
            'adminname' => htmlencode($adminname),
            'adminpass' => $adminpass,
            'save'      => $save,
        ));
        $tpl->display('login.php');
    }
    // _logout *** *** www.LazyCMS.net *** ***
    function _logout(){
        $adminname = Cookie::get('adminname');
        // 清空cookie
        Cookie::delete('adminname');
        Cookie::delete('adminpass');
        Cookie::delete('language');
        if (!empty($adminname)) {
            // 记录退出
            $db = getConn();
            $row = array(
                'adminname' => $adminname,
                'ip'        => ip(),
                'lognum'    => 3,
                'logdate'   => now(),
            );
            $db->insert('#@_log',$row);
            unset($db);
        }
        // 跳转到登录页
        redirect(url('System','Login'));
    }
    // _noaccess *** *** www.LazyCMS.net *** ***
    function _noaccess(){
        throwError(L('error/jurisdiction'));
    }
}