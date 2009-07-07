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
defined('COM_PATH') or die('Restricted access!');
/**
 * 系统模块的公共函数
 */
class System{
    // 生成语言包文件
    function makeLang(){
        // 查找共支持多少语言包
        $languages = get_dir_array('@.language','dir');
        foreach ($languages as $language) {
            // 语言文件
            $langFiles = array(); $langMaxTime  = 0;
            $langFile  = COM_PATH."/js/lang.{$language}.js";
            $moduleLangJs = get_dir_array('@.language.'.$language,'js');
            foreach ($moduleLangJs as $moduleLang) {
                $langFiles[$moduleLang] = COM_PATH."/language/{$language}/{$moduleLang}";
                $langMaxTime= max($langMaxTime,filemtime($langFiles[$moduleLang]));
            }
            // 取得文件的最后修改日期
            $langLastTime = is_file($langFile)?filemtime($langFile):0;
            // 合并文件
            if ($langLastTime <= $langMaxTime) {
                $langContent = null;
                foreach ($langFiles as $file=>$lang) {
                    $langContent.= "// {$file}".preg_replace('#/\*\*(.+)\*/#isU','',read_file($lang))."\n";
                }
                save_file($langFile,$langContent);
            }
        }
    }
    /**
     * 输出后台header头
     *
     * @param string $title
     */
    function header($title=null,$selected=null){
        $_USER     = System::getAdmin(); System::makeLang();
        $selected  = !empty($selected) ? $selected.'|' : null;
        $title     = empty($title) ? t('system::system') : $title.' - '.t('system::system');
        // 生成语言js文件
        $hl = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
        $hl.= '<html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
        $hl.= '<title>'.$title.'</title>';
        $hl.= '<link href="../system/images/style.css" rel="stylesheet" type="text/css" />';
        $hl.= '<link rel="shortcut icon" href="'.SITE_BASE.'favicon.ico" />';
        $hl.= '<script type="text/javascript">window.MODULE = \''.MODULE.'\';window.ACTION = \''.ACTION.'\';</script>';
        $hl.= '<script type="text/javascript" src="../../common/js/jquery.js?ver=1.3"></script>';
        $hl.= '<script type="text/javascript" src="../../common/js/lazycms.library.js"></script>';
        $hl.= '<script type="text/javascript" src="../system/images/system.js"></script>';
        if ($script = g('SCRIPT')) {
            $hl.= '<script type="text/javascript">'.$script.'</script>';
        }        
        if ($style = g('STYLE')) {
            $hl.= '<style type="text/css">'.$style.'</style>';
        }
        $hl.= '</head><body>';
        $hl.= '<div id="top">';
        $hl.= '<div class="logo"><a href="../system/index.php"><img src="../system/images/logo.png" alt="LazyCMS '.LAZY_VERSION.'" /></a></div>';
        $hl.= '<div id="version" version="'.LAZY_VERSION.'"><strong>Hi,'.$_USER['adminname'].'</strong>&nbsp; '.t('system::system/lastversion').': <span>Loading...</span></div>';
        $hl.= '<ul id="menu">';
        // 生成菜单，TODO:需要增加权限判断
        $modules = System::getModules();
        foreach ($modules as $module) {
            $hl.= '<li><div>'.t($module.'::name').'<img class="a2 os" src="../system/images/white.gif" /></div>';
            $vl = include_file(COM_PATH.'/modules/'.$module.'/config.php');
            if (isset($vl['menus'])) {
                $hl.= '<ul>';
                $hr = false;
                foreach ($vl['menus'] as $menu) {
                    if (is_array($menu)) {
                        foreach ($menu as $k=>$v) {
                            $url = $v; $clik = null;
                            if (is_array($v)) {
                                if (instr($_USER['purview'],$module.'::'.$v['purview'])) {
                                    $pv  = true;
                                    $url = $v['href'];
                                } else {
                                    $pv  = false;
                                }
                            } else {
                                $pv = true;
                            }
                            // 有权限
                            if ($pv) {
                                $hr = true;
                                if (pathinfo($url,PATHINFO_BASENAME)=='logout.php') {
                                    $clik = ' onclick="$.confirm(\''.t('system::confirm/logout').'\',function(r){ r ? $.redirect(\''.$url.'\') : false; })"';
                                    $url  = 'javascript:;';
                                }
                                $hl.= '<li><a href="'.$url.'"'.$clik.'>'.t($module.'::'.$k).'</a></li>';
                            }
                        }
                    } else {
                        // 是否该有 hr
                        if ($hr) {
                            $hl.= '<li class="hr"></li>'; $hr = false;
                        }
                    }
                }
                $hl.= '</ul>';
            }
            $hl.= '</li>';
        }
        $hl.= '<li><div>'.t('system::help').'<img class="a2 os" src="../system/images/white.gif" /></div><ul>';
        $hl.= '    <li><a href="http://www.lazycms.net/" target="_blank">'.t('system::official/site').'</a></li>';
        $hl.= '    <li><a href="http://forums.lazycms.net/" target="_blank">'.t('system::official/forums').'</a></li>';
        $hl.= '    <li class="hr"></li>';
        $hl.= '    <li><a href="../system/sysinfo.php">'.t('system::sysinfo').'</a></li>';
        $hl.= '    </ul>';
        $hl.= '</li></ul>';
        $hl.= '<ul class="menu"><li><a href="'.SITE_BASE.'" target="_blank">'.t('system::system/preview').'</a></li><li><a href="../system/myaccount.php">'.t('system::myaccount').'</a></li><li><a href="javascript:;" onclick="$.confirm(\''.t('system::confirm/logout').'\',function(r){ r ? $.redirect(\'../system/logout.php\') : false; })">'.t('system::system/logout').'</a></li></ul>';
        $hl.= '</div><div id="main">';
        if ($tabs = g('TABS')) { 
            $hl.= menu($selected.$tabs); 
            $hl.= '<div id="box">';
            $help = basename(PHP_FILE,'.php').(ACTION==''?'':'/'.ACTION);
            $path = 'help/'.$help;
            if (!is_array(t($path)) && $path != t($path)) {
                $hl.= '<div id="help"><a href="javascript:;" onclick="$(this).help(\''.$help.'\');"><img class="h5 os" src="../system/images/white.gif" /></a></div>';
            }
        }
        echo $hl;
    }
    /**
     * 设置tabs菜单
     *
     * @param string $p1
     */
    function tabs($p1){
        g('TABS',$p1);
    }
    /**
     * 设置script
     *
     * @param string $p1
     */
    function script($p1){
        static $R = null;
        if (empty($R)) {
            $R = $p1;
        } else {
            $R.= $p1;
        }
        g('SCRIPT',$R);
    }
    /**
     * 加载脚本
     *
     * @param string $p1
     */
    function loadScript($p1){
        System::script('LoadScript("'.$p1.'");');
    }
    /**
     * 设置style
     *
     * @param string $p1
     */
    function style($p1){
        static $R = null;
        if (empty($R)) {
            $R = $p1;
        } else {
            $R.= $p1;
        }
        g('STYLE',$R);
    }
    /**
     * 取得已安装的模块
     */
    function getModules(){
        $modules = get_dir_array('@.modules','dir'); $index = array_search('system',$modules);
        $system  = $modules[$index]; unset($modules[$index]); array_unshift($modules,$system);
        return $modules;
    }
    /**
     * 管理员登录验证
     *
     * @param string $p1    用户名
     * @param string $p2    密码
     */
    function checkAdmin($p1,$p2){
        return System::getAdmin($p1,$p2);
    }
    /**
     * 验证管理员权限
     *
     * @param string $p1    用户名
     * @param string $p2    权限不正确，退出地址
     */
    function purview($p1=null,$p2='../system/logout.php'){
        $_USER = System::getAdmin($p1);
        if ($_USER===0) {
            // 登录超时
            if ($_SERVER['HTTP_AJAX_SUBMIT']) {
                ajax_alert(t('system::error/overtime'),'../system/logout.php');
            } else {
                redirect('../system/logout.php');
            }
        } elseif (!$_USER) {
            // TODO: 没有权限，或没有登录，提示
            if ($_SERVER['HTTP_AJAX_SUBMIT']) {
                ajax_alert(t('system::error/permission'));
            } else {
                if (!headers_sent()) { header("Content-Type:text/html; charset=utf-8"); }
                echo '<script type="text/javascript" charset="utf-8">alert("'.t2js(t('system::error/permission')).'");self.history.back();</script>';
            }
        } else {
            // 验证管理员是否被锁定
            if ($_USER['islocked']) {
                if ($_SERVER['HTTP_AJAX_SUBMIT']) {
                    alert(t('system::login/check/locked'));
                } else {
                    if (!headers_sent()) { header("Content-Type:text/html; charset=utf-8"); }
                    echo '<script type="text/javascript" charset="utf-8">alert("'.t2js(t('system::login/check/locked')).'");self.location.href="../system/logout.php";</script>';
                }
            }
        }
        return $_USER;
    }
    /**
     * 验证后台用户的登录和权限
     * 
     * @example 
     *  get_admin() 取得已登录管理员的信息
     *  get_admin('purview') 验证已登录管理员的权限
     *  get_admin('adminname','adminpass') 进行管理员登录验证
     */
    function getAdmin(){
        $db = get_conn(); 
        $funcNum = func_num_args();
        $params  = func_get_args();
        if ((int)$funcNum <= 1) {
            $params[2] = $params[0];
            $params[0] = Cookie::get('adminname');
            $params[1] = Cookie::get('adminpass');
        }
        // 登录超时
        if (empty($params[0]) || empty($params[0])) { return 0; }
        // 开始验证
        $res = $db->query("SELECT * FROM `#@_system_admin` WHERE `adminname`=? LIMIT 0,1;",$params[0]);
        if ($rs = $db->fetch($res)) {
            // 验证用户名密码
            if ((int)$funcNum > 1 && !empty($params[0]) && !empty($params[1])) {
                $md5pass = md5($params[1].$rs['adminkey']);
                if ($md5pass == $rs['adminpass']) {
                    $newkey  = substr($md5pass,0,6);
                    $newpass = md5($params[1].$newkey);
                    // 更新数据
                    $db->update('#@_system_admin',array(
                        'adminpass' => $newpass,
                        'adminkey'  => $newkey,
                    ),DB::quoteInto('`adminname` = ?',$params[0]));
                    // 合并新密码和key
                    $rs = array_merge($rs,array(
                        'adminpass' => $newpass,
                        'adminkey'  => $newkey,
                    ));
                    return $rs;
                }
            } elseif ((int)$funcNum == 1 && !empty($params[2])) {
                // 验证权限正确，则返回管理员信息
                if ((string)$params[1] == (string)$rs['adminpass']) {
                    // 输入权限则进行验证，不输入权限则只返回管理员信息
                    if (isset($params[2])) {
                        $params[2] = strtolower($params[2]);
                        // 先查找是否有此权限
                        if (instr($rs['purview'],$params[2])) {
                            return $rs;
                        }
                    }
                } else {
                    // 登录超时
                    return 0;
                }
            } else {
                // 验证是否登录
                if ((string)$params[1] == (string)$rs['adminpass']) {
                    return $rs;
                } else {
                    // 登录超时
                    return 0;
                }
            }
        }
        return false;
    }
    /**
     * 输出后台尾部
     *
     */
    function footer(){
        $hl = null;
        if (g('TABS')) {
            $hl.= '</div>';
        }
        $GLOBALS['_endTime'] = microtime_float();
        $execTime = ($GLOBALS['_endTime']-$GLOBALS['_beginTime']);
        $hl.= sprintf('</div><div id="footer"><a href="http://www.lazycms.net" target="_blank">Copyright &copy; LazyCMS.net All Rights Reserved.</a><br/>Processed in %f second(s)</div>',$execTime);
        /*
        $hl.= '<div id="process" class="panel">';
        $hl.= '<div class="head" style="width:195px"><strong>进程列表</strong><a href="javascript:;" onclick="$().remove();">&nbsp;</a></div>';
        $hl.= '<div class="body">dddddd</div>';
        $hl.= '</div>';
        */
        $hl.= '</body></html>';
        echo $hl;
    }
    /**
     * 中文分词
     *
     * @param string $p1
     * @return array
     */
    function getKeywords($p1){
        $keywords = $RemoteKeywords = array();
        import('system.httplib');
        import('system.splitword');
        // 先使用本地词库分词
        $sw = new SplitWord();
        $keywords = $sw->getWord($p1);
        // 使用远程分词
        $d = new Httplib("http://keyword.lazycms.net/related_kw.php","POST",10);
        $d->send(array('title'=>rawurlencode($p1)));
        // 请求成功
        if ($d->status() == 200) {
            $XML = $d->response();
            // 取出关键词为数组
            if (preg_match_all('/\<kw\>\<\!\[CDATA\[(.+)\]\]\>\<\/kw\>/i',$XML,$Regs)) {
                $RemoteKeywords = $Regs[1];
            }
        }
        // 合并两次分词的结果
        if (!empty($RemoteKeywords)) {
            foreach ($RemoteKeywords as $keyword) {
                if (array_search_value($keyword,$keywords)===false) {
                    $keywords[] = $keyword;
                }
            }
        }
        return $keywords;
    }
}
