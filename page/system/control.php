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
        // 读取首页
        if (class_exists('Onepage')) {
            Onepage::index();
        } else {
            throwError(L('error/onepage'));
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
        $dp->but = $dp->button('logdelete:'.L('log/delete').'|clear:'.L('log/clear')).$dp->plist();
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
        $dp->thead  = '<tr><th>'.L('log/list/id').') '.L('log/list/name').'</th><th>'.L('log/list/num').'</th><th>'.L('log/list/ip').'</th><th>'.L('log/list/date').'</th></tr>';
        
        // 循环设置tbody
        while ($data = $dp->result()) {
            $dp->tbody = "ll(".$data['logid'].",'".t2js($data['adminname'])."','".$data['ip']."','".L('log/l'.$data['lognum'])."','".date('Y-m-d H:i:s',$data['logdate'])."');";
        }
        // 关闭对象
        $dp->close();
        // 将生成的html扔给this->record()
        $this->outHTML = $dp->fetch;

        // 取得模板对象
        $tpl = getTpl($this);
        $tpl->assign('menu',L('admin/title').'|'.url('System','Main').';'.L('log/@title').'|#|true');
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
                $this->poping(L('log/pop/clearok'),1);
                break;
            case 'delete' :
                $lists = isset($_POST['lists']) ? $_POST['lists'] : null;
                if (empty($lists)) {
                    $this->poping(L('log/pop/select'),0);
                }
                $db->exec("DELETE FROM `#@_log` WHERE `logid` IN({$lists});");
                $this->poping(L('log/pop/deleteok'),1);
                break;
            case 'logdelete' ://删除过期日志 7天
                $date = now()-(7*24*3600);
                $db->exec("DELETE FROM `#@_log` WHERE `logdate`< ? ;",$date);
                $this->poping(L('log/pop/logdeleteok'),1);
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
        $dp->thead  = '<tr><th>'.L('admin/list/name').'</th><th>'.L('admin/list/level').'</th><th>'.L('admin/list/language').'</th><th>'.L('admin/list/editor').'</th><th>'.L('admin/list/regdate').'</th></tr>';
        while ($data = $dp->result()) {
            $dp->tbody = "ll(".$data['adminid'].",'".t2js(htmlencode($data['adminname']))."','".($data['adminlevel']=='admin' ? L('admin/level/super') : L('admin/level/editor'))."','".langbox($data['adminlanguage'])."','".$data['admineditor']."','".date('Y-m-d H:i:s',$data['admindate'])."');";
        }
        $dp->close();

        $this->outHTML = $dp->fetch;

        $tpl = getTpl($this);
        $tpl->assign('menu',L('admin/list').'|#|true;'.L('admin/add').'|'.url('System','AdminEdit'));
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
                    $this->poping(L('admin/pop/select'),0);
                }
                $db->exec("DELETE FROM `#@_admin` WHERE `adminid` IN({$lists}) AND NOT(`adminid` = ?);",$this->admin['adminid']);
                $this->poping(L('admin/pop/deleteok'),1);
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
            $menu = L('admin/add').'|#|true';
        } else {
            $menu = L('admin/add').'|'.url('System','AdminEdit').';'.L('admin/edit').'|#|true';
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
            'adminname' => empty($adminid) ? $this->check("adminname|1|".L('admin/check/name')."|2-12;adminname|3|".L('admin/check/name1')."|SELECT COUNT(`adminid`) FROM `#@_admin` WHERE `adminname`='#pro#'") : null,
            'adminpass' => (!empty($data[1]) || !empty($data[6]) || empty($adminid)) ? $this->check('adminpass|2|'.L('admin/check/contrast').'|adminpass1;adminpass|1|'.L('account/check/pwdsize').'|6-30') : null,
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
                $res   = $db->query("SELECT {$sql} FROM `#@_admin` WHERE `adminid` = ?;",$adminid);
                if ($data = $db->fetch($res,0)) {
                    if ($data[0]==Cookie::get('adminname')) {
                        redirect(url('System','MyAccount')); exit(0);
                    }
                } else {
                    throwError(L('error/invalid'));
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
            'levels'        => array('config','log','diymenu','module','filemanage'),
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
            'adminpass' => (!empty($adminpass) || !empty($adminpass1)) ? $this->check('adminpass|2|'.L('admin/check/contrast').'|adminpass1;adminpass|1|'.L('account/check/pwdsize').'|6-30') : null,
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
                $this->succeed(L('common/upok'));
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
            $this->succeed(L('common/upok'));
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
        $sitemode = isset($_POST['sitemode']) ? (bool)($_POST['sitemode']=='true'?true:false) : C('SITE_MODE');
        $urlmode  = isset($_POST['urlmode']) ? (int)$_POST['urlmode'] : C('URL_MODEL');
        $keywords = isset($_POST['keywords']) ? $_POST['keywords'] : null;
        $lockip   = isset($_POST['lockip']) ? $_POST['lockip'] : null;
        // 判断服务器是否支持 rewrite
        ob_start();phpinfo(); if (strpos(strtolower(ob_get_contents()),'mod_rewrite')!==false) { $isReWrite = true; } else { $isReWrite = false; } ob_end_clean();
        $this->validate(array(
            'sitename' => $this->check('sitename|1|'.L('config/check/sitename').'|1-50'),
            'sitemail' => !empty($sitemail) ? $this->check('sitemail|validate|'.L('config/check/sitemail').'|4') : null,
        ));
        if ($this->method()) {
            if ($this->validate()) {
                // 不支持rewrite 还原 URL_MODEL 设置
                if (!$isReWrite) { $urlmode = C('URL_MODEL'); }
                $config = include CORE_PATH.'/custom/config.php';
                $config = array_merge($config,array('SITE_MODE'=>$sitemode,'URL_MODEL'=>$urlmode));
                // 全站动态模式，删除page/index.php 生成一个 page/index.html
                if ($sitemode) {
                    @unlink(LAZY_PATH.C('PAGES_PATH').'/index.php');
                    saveFile(LAZY_PATH.C('PAGES_PATH').'/index.html');
                    saveFile(LAZY_PATH.'/index.php',"<?php\n".createNote()."\ndefine('CORE_PATH', dirname(__FILE__).'/core');require CORE_PATH.'/LazyCMS.php';LazyCMS::run('System','Default');\n?>");
                } else {
                    // 全站静态模式，删除index.php 自动创建一个 page/index.php
                    @unlink(LAZY_PATH.C('PAGES_PATH').'/index.html');
                    @unlink(LAZY_PATH.'index.php');
                    saveFile(LAZY_PATH.C('PAGES_PATH').'/index.php',"<?php\n".createNote()."\ndefine('LAZY_PATH', dirname(__FILE__).'/../'); define('CORE_PATH', dirname(__FILE__).'/../core');require CORE_PATH.'/LazyCMS.php'; LazyCMS::run('System','Sysinfo');\n?>");
                }
                if ($urlmode==URL_REWRITE) {
                    if (is_file(LAZY_PATH.'/index.php')) {
                        $RewriteBase = C('SITE_BASE');
                    } else {
                        $RewriteBase = C('SITE_BASE').C('PAGES_PATH').'/';
                    }
                    saveFile(LAZY_PATH.'/.htaccess',reWrite($RewriteBase));
                } else {
                    @unlink(LAZY_PATH.'/.htaccess');
                }
                C($config); saveFile(CORE_PATH.'/custom/config.php',"<?php\n".createNote('User-defined configuration files')."\nreturn ".var_export($config,true).";\n?>");
                $set = array(
                    'sitename'     => $sitename,
                    'sitemail'     => $sitemail,
                    'sitekeywords' => $keywords,
                    'lockip'       => $lockip,
                );
                $where = $db->quoteInto('`systemname` = ?','LazyCMS');
                $db->update('#@_system',$set,$where);
                $this->succeed(L('common/upok'));
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
            'sitemode' => $sitemode,
            'urlmode'  => $urlmode,
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
        $_html.= '<th>'.L('module/list/name').'</th>';
        $_html.= '<th>'.L('module/list/version').'</th>';
        $_html.= '<th>'.L('module/list/folder').'</th>';
        $_html.= '<th>'.L('module/list/author').'</th>';
        $_html.= '<th>'.L('module/list/source').'</th>';
        $_html.= '<th>'.L('module/list/mail').'</th>';
        $_html.= '<th>'.L('module/list/is').'</th>';
        $_html.= '</tr></thead>';

        $_html.= '<tbody>';
        foreach ($dir as $k=>$v) {
            if (strtolower($v) != 'system') {
                if (instr($this->system['modules'],$v)) {
                    $isInstall = L('module/is/true');
                    $isAction  = 'delete';
                    $isExists  = null;
                    $module    = '<a href="'.eval('return '.L('manage',null,$v).';').'">'.L('title',null,$v).'</a>';
                    $config    = is_file(LAZY_PATH.C('PAGES_PATH').'/'.$v.'/config.php') ? " [<a href=\"javascript:void(0);\" onclick=\"$(this).gm('config',{module:'".$v."'},{width:'600px','margin-left':'-300px',height:'300px'});\">".L('common/config')."</a>]" : null;
                } else {
                    $isInstall = L('module/is/false');
                    $isAction  = 'install';
                    $isExists  = ' class="red"';
                    $module    = L('title',null,$v);
                    $config    = null;
                }
                $help  = is_file(LAZY_PATH.C('PAGES_PATH').'/'.$v.'/help/help.html') ? " [<a href=\"javascript:void(0);\" onclick=\"$(this).gm('help',{module:'".$v."'},{width:'600px','margin-left':'-300px',height:'300px'});\">".L('common/help')."</a>]" : null;
                $about = is_file(LAZY_PATH.C('PAGES_PATH').'/'.$v.'/help/about.html') ? " [<a href=\"javascript:void(0);\" onclick=\"$(this).gm('about',{module:'".$v."'});\">".L('common/about')."</a>]" : null;
                $_html.= '<tr'.$isExists.'><td>'.($k+1).') '.$module.'</td>';
                $_html.= '<td>'.L('version',null,$v).'</td>';
                $_html.= '<td>'.$v.'</td>';
                $_html.= '<td>'.L('author',null,$v).'</td>';
                $_html.= '<td>'.L('source',null,$v).'</td>';
                $_html.= '<td>'.L('email',null,$v).'</td>';
                $_html.= '<td>'.$isInstall.$config.' [<a href="javascript:void(0);" onclick="$(this).gm(\''.$isAction.'\',{module:\''.$v.'\'});">'.L('common/'.$isAction).'</a>]'.$about.$help.'</td></tr>';
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
                        $obj = new $_module();
                        if (method_exists($obj,'instSQL')) {
                            $instSQL = $obj->instSQL();
                            $db->batQuery($instSQL);
                        }
                        unset($obj);
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
                    'title' => L('common/about').' - '.L('title',null,$module),
                    'main'  => ubbencode(loadFile(LAZY_PATH.C('PAGES_PATH').'/'.$module.'/help/about.html')),
                ),0);
                break;
            case 'help' :
                $this->poping(array(
                    'title' => L('common/help').' - '.L('title',null,$module),
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
                    'title' => L('config/@title').' - '.L('title',null,$module),
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
                'title' => L('title',null,$module).' - '.L('common/memo'),
                'main'  => L('module/tip/install').'<br/><a href="'.eval('return '.L('manage',null,$module).';').'">【'.L("module/tip/welcome").'】</a>',
            ),1);
        } else {
            $this->poping(array(
                'title' => L('title',null,$module).' - '.L('common/memo'),
                'main'  => L('module/tip/delete'),
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
        $this->poping(L('module/pop/ok'),0);
    }
    
    // _browsefiles *** *** www.LazyCMS.net *** ***
    function _browsefiles(){
        clearCache();
        $this->checker('filemanage',true);
        if ((int)get_cfg_var('post_max_size') < (int)$_SERVER["CONTENT_LENGTH"]/1024/1024) {
            $I1 = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
            $I1.= '<html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><title>UpLoadFile</title></head><body>';
            $I1.= '<script type="text/javascript" charset="utf-8">window.parent.alert(\''.L('error/upload/err0',array('max' => get_cfg_var('post_max_size'))).'\');</script>';
            $I1.= '</body></html>';echo $I1;return ;
        }
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
                $filePath = str_replace('//','/',$cPath.'/'.basename($_FILES['upfile']['name']));
                if ($file = $upload->save('upfile',$filePath)) {
                    if (instr('jpg,gif,png,bmp',fileICON($_FILES['upfile']['name']))) {
                        $filePath = str_replace(LAZY_PATH,'',$file['path']);
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
                $DelFolder    = "<a href=\"javascript:DelFolder('{$path}');\" onclick=\"if(confirm('".L('confirm/delete')."')){\$('#{$from}').browseFiles('".url('System','browseFiles')."',{action:'deletefolder',folder:'{$path}',from:'{$from}',path:'{$bPath}'});};return false;\"><img src=\"".C('SITE_BASE').C('PAGES_PATH')."/system/images/os/del.gif\" alt=\"Delete Folder...\" class=\"os\"/></a>";
            }
            $UpLoadFile   = "<a href=\"javascript:UpLoadFile('{$path}');\" onclick=\"\$(this).getPoping('.toolbar','".url('System','browseFiles')."',{action:'getloadfile',path:'{$path}',from:'{$from}'});return false;\"><img src=\"".C('SITE_BASE').C('PAGES_PATH')."/system/images/os/upfile.gif\" alt=\"UpLoad File...\" class=\"os\"/></a>";
            $CreateFolder = "<a href=\"javascript:CreateFolder('{$path}');\" onclick=\"\$(this).getPoping('.toolbar','".url('System','browseFiles')."',{action:'getfolder',path:'{$path}',from:'{$from}'});return false;\"><img src=\"".C('SITE_BASE').C('PAGES_PATH')."/system/images/os/crtdir.gif\" alt=\"Create Folder...\" class=\"os\"/></a>";
        }
        $HTML = '<form class="lz_form"><div class="toolbar"><span class="in">'.System::filesPath($path,$from).'</span><div>'.$DelFolder.$CreateFolder.$UpLoadFile.'</div></div>';
        $HTML.= '<table class="lz_table" style="width:565px;margin:3px 0;clear:both;">';
        if (is_dir($cPath)) {
            $HTML.= '<tr><th>'.L('filemanage/template/filename').'</th><th class="wp5">'.L('filemanage/template/filemtime').'</th></tr>';
            $dh = opendir($cPath);
            $imgPath = C('SITE_BASE').C('PAGES_PATH')."/system/images/os/";
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
        } else {
            $l1 = $d->status();
        }
        if (validate($l1,6)) {
            $I1 = $l1;
            if (version_compare($this->system['systemver'], $l1, '<' )) {
                $I1 .= ' <a href="http://www.lazycms.net/download" target="_blank">【'.L('parameters/downnew').'】</a>';
                $I1 .= ' <a href="'.url('System','Update').'" onclick="$.posts(this.href,{version:\''.$l1.'\'});return false;">【'.L('parameters/update').'】</a>';
            }
        } else {
            $I1 = L('parameters/newversionerr');
        }
        echo $I1;
    }
    // _update *** *** www.LazyCMS.net *** ***
    function _update(){
        clearCache();
        $this->checker('admin',true);
        $version = isset($_POST['version']) ? $_POST['version'] : null;
        $this->poping(L('parameters/notup'),1);
    }
    // _login *** *** www.LazyCMS.net *** ***
    function _login(){
        $db = getConn();
        $adminname = Cookie::get('adminname');
        $adminpass = Cookie::get('adminpass');
        if (!empty($adminname) && !empty($adminpass)) {
            $res   = $db->query("SELECT * FROM `#@_admin` WHERE `adminname` = ?;",$adminname);
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
        // 取得模板对象
        $tpl = getTpl($this);
        $adminname = isset($_POST['adminname']) ? $_POST['adminname'] : null;
        $adminpass = isset($_POST['adminpass']) ? $_POST['adminpass'] : null;
        $save      = isset($_POST['save']) ? $_POST['save'] : null;
        // 验证
        $this->validate(array(
            'adminname' => $this->check('adminname|1|'.L('login/check/name').'|2-30'),
            'adminpass' => $this->check('adminpass|1|'.L('login/check/pass').'|6-30'),
        ));
        // 验证通过
        if ($this->method() && $this->validate()) {
            $validity  = $save ? (now()+3600*24*7) : null;
            $res   = $db->query("SELECT * FROM `#@_admin` WHERE `adminname` = ?;",$adminname);
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
                        'adminpass' => L('login/check/error2'),
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
                    'adminname' => L('login/check/error1'),
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
    // sysinfo *** *** www.LazyCMS.net *** ***
    function _sysinfo(){
        $tpl = getTpl($this); $Message = null;
        $space = "&nbsp; &nbsp; &nbsp; &nbsp; ";
        $modules = $this->system['modules'];
        $modules = explode(',',$modules);
        foreach ($modules as $module) {
            $Message.= $space.L('title',null,$module)."<br/>";
        }
        $HTML = "<strong>Version:</strong><br/>".$space.$this->system['systemver']."<br/>";
        $HTML.= "<strong>Modules:</strong><br/>{$Message}";
        $HTML.= "<strong>Official Website:</strong><br/>{$space}<a href=\"http://www.LazyCMS.net\" target=\"_blank\">http://www.LazyCMS.net</a><br/>";
        $tpl->assign(array(
            'title'   => $this->system['sitename'],
            'Message' => $HTML,
        ));
        $tpl->display('error.php');
    }
}