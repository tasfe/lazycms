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
    /**
     * 输出后台header头
     *
     * @param string $title
     */
    function header($title=null,$selected=null){
        $_USER    = System::getAdmin();
        $selected = !empty($selected) ? $selected.'|' : null;
        $title = empty($title) ? t('system::system') : $title.' - '.t('system::system');
        $hl = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
        $hl.= '<html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
        $hl.= '<title>'.$title.'</title>';
        $hl.= '<link href="../system/images/style.css" rel="stylesheet" type="text/css" />';
        $hl.= '<link rel="shortcut icon" href="'.SITE_BASE.'favicon.ico" />';
        $hl.= '<script type="text/javascript">var MODULE = \''.MODULE.'\';var ACTION = \''.ACTION.'\';</script>';
        $hl.= '<script type="text/javascript" src="../../common/js/jquery.js?ver=1.2.6"></script>';
        $hl.= '<script type="text/javascript" src="../../common/js/lazycms.library.js?ver=1.0"></script>';
        $hl.= '<script type="text/javascript" src="../system/images/system.js?ver=1.0"></script>';
        $hl.= '<script type="text/javascript">'.js_lang();
        $hl.= g('SCRIPT');
        $hl.= '</script>';
        if ($style = g('STYLE')) {
            $hl.= '<style type="text/css">'.$style.'</style>';
        }
        $hl.= '</head><body>';
        $hl.= '<div id="top">';
        $hl.= '<div class="logo"><a href="../system/index.php"><img src="../system/images/logo.png" alt="LazyCMS '.LAZY_VERSION.'" /></a></div>';
        $hl.= '<div id="version" version="'.LAZY_VERSION.'"><strong>Hi,'.$_USER['adminname'].'</strong>&nbsp; '.t('system::system/lastversion').': <span>Loading...</span></div>';
        $hl.= '<ul id="menu">';
        $hl.= '<li><div>'.t('system::system/manage').'<img class="a2 os" src="../system/images/white.gif" /></div><ul>';
        $hl.= '    <li><a href="../system/index.php">'.t('system::system/cpanel').'</a></li>';
        $hl.= '    <li class="hr"></li>';
        $hl.= '    <li><a href="../system/admins.php">'.t('system::admins').'</a></li>';
        $hl.= '    <li><a href="../system/files.php">'.t('system::files').'</a></li>';
        $hl.= '    <li class="hr"></li>';
        $hl.= '    <li><a href="../system/modules.php">'.t('system::modules').'</a></li>';
        $hl.= '    <li><a href="../system/settings.php">'.t('system::settings').'</a></li>';
        $hl.= '    <li class="hr"></li>';
        $hl.= '    <li><a href="javascript:;" onclick="$.confirm(\''.t('system::confirm/logout').'\',function(r){ r ? $.redirect(\'../system/logout.php\') : false; })">'.t('system::system/logout').'</a></li>';
        $hl.= '</ul></li>';
        $hl.= '
            <li><div>内容管理<img class="a2 os" src="../system/images/white.gif" /></div>
                <ul>
                    <li><a href="../content/label.php">标签中心</a></li>
                    <li><a href="../content/create.php">生成中心</a></li>
                    <li class="hr"></li>
                    <li><a href="../content/onepage.php">单页管理</a></li>
                    <li><a href="../content/article.php">文档管理</a></li>
                    <li class="hr"></li>
                    <li><a href="../content/sort.php">分类管理</a></li>
                    <li><a href="../content/model.php">模型管理</a></li>
                </ul>
            </li>';
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
        $GLOBALS['_endTime'] = microtime(true);
        $execTime = ($GLOBALS['_endTime']-$GLOBALS['_beginTime']);
        $hl.= sprintf('</div><div id="footer"><a href="http://www.lazycms.net" target="_blank">Copyright &copy; LazyCMS.net All Rights Reserved.</a><br/>Processed in %f second(s)</div>',$execTime);
        $hl.= '</body></html>'; echo $hl;
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
