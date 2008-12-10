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
        $selected = !empty($selected) ? $selected.'|' : null;
        $title = empty($title) ? l('System manage') : $title.' - '.l('System manage');
        $hl = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
        $hl.= '<html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
        $hl.= '<title>'.$title.'</title>';
        $hl.= '<link href="../system/images/style.css" rel="stylesheet" type="text/css" />';
        $hl.= '<script type="text/javascript" src="../../common/js/jquery.js?ver=1.2.6"></script>';
        $hl.= '<script type="text/javascript" src="../../common/js/lazycms.library.js?ver=1.0"></script>';
        $hl.= '<script type="text/javascript" src="../system/images/system.js?ver=1.0"></script>';
        if ($script = g('SCRIPT')) { 
            $hl.= '<script type="text/javascript">'.$script.'</script>';
        }
        $hl.= '</head><body>';
        $hl.= '<div id="top">';
        $hl.= '<div class="logo"><a href="../system/index.php"><img src="../system/images/logo.png" alt="LazyCMS '.LAZY_VERSION.'" /></a></div>';
        $hl.= '<div id="version" version="'.LAZY_VERSION.'">'.l('Last Version').': <span>Loading...</span></div>';
        $hl.= '<ul id="menu">';
        $hl.= '<li><div>'.l('System manage').'<img class="a2 os" src="../system/images/white.gif" /></div><ul>';
        $hl.= '    <li><a href="../system/index.php" class="icon-16-cpanel">'.l('Cpanel').'</a></li>';
        $hl.= '    <li class="hr"></li>';
        $hl.= '    <li><a href="../system/admin.php" class="icon-16-admin">'.l('Admins').'</a></li>';
        $hl.= '    <li><a href="../system/files.php" class="icon-16-files">'.l('Webftp').'</a></li>';
        $hl.= '    <li class="hr"></li>';
        $hl.= '    <li><a href="../system/install.php" class="icon-16-install">'.l('Modules').'</a></li>';
        $hl.= '    <li><a href="../system/settings.php" class="icon-16-config">'.l('Settings').'</a></li>';
        $hl.= '    <li class="hr"></li>';
        $hl.= '    <li><a href="javascript:;" class="icon-16-logout" onclick="return $.confirm(\''.l('Confirm logout').'\',function(r){ r ? $.redirect(\'../system/logout.php\') : false; })">'.l('Logout').'</a></li>';
        $hl.= '</ul></li>';
        $hl.= '
            <li><div>内容管理<img class="a2 os" src="../system/images/white.gif" /></div>
                <ul>
                    <li><a href="../content/label.php" class="icon-16-label">标签中心</a></li>
                    <li><a href="../content/create.php" class="icon-16-create">生成中心</a></li>
                    <li class="hr"></li>
                    <li><a href="../content/onepage.php" class="icon-16-page">单页管理</a></li>
                    <li><a href="../content/article.php" class="icon-16-article">文档管理</a></li>
                    <li class="hr"></li>
                    <li><a href="../content/sort.php" class="icon-16-sort">分类管理</a></li>
                    <li><a href="../content/model.php" class="icon-16-model">模型管理</a></li>
                </ul>
            </li>';
        $hl.= '<li><div>'.l('Help').'<img class="a2 os" src="../system/images/white.gif" /></div><ul>';
        $hl.= '    <li><a href="http://www.lazycms.net/" class="icon-16-home" target="_blank">'.l('Official Website').'</a></li>';
        $hl.= '    <li><a href="http://forums.lazycms.net/" class="icon-16-help" target="_blank">'.l('Support Forums').'</a></li>';
        $hl.= '    <li class="hr"></li>';
        $hl.= '    <li><a href="../system/sysinfo.php" class="icon-16-info">'.l('System info').'</a></li>';
        $hl.= '    </ul>';
        $hl.= '</li></ul>';
        $hl.= '<ul class="menu"><li><a href="'.SITE_BASE.'" target="_blank">'.l('Preview').'</a></li><li><a href="#">'.l('修改密码').'</a></li><li><a href="javascript:;" onclick="return $.confirm(\''.l('Confirm logout').'\',function(r){ r ? $.redirect(\'../system/logout.php\') : false; })">'.l('Logout').'</a></li></ul>';
        $hl.= '</div><div id="main">';
        if ($tabs = g('TABS')) { 
            $hl.= menu($selected.$tabs); 
            $hl.= '<div id="box">';
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
    function purview($p1='System',$p2='../system/logout.php'){
        $_USER = System::getAdmin($p1);
        if (!$_USER) {
            // TODO: 没有权限，或没有登录，提示
            if ($_SERVER['HTTP_AJAX_SUBMIT']) {
                alert('你没有权限查看此页');
            } else {
                redirect($p2);
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
        if (empty($params[0]) || empty($params[0])) { return false; }
        // 开始验证
        $res = $db->query("SELECT * FROM `#@_system_admin` WHERE `adminname`=? LIMIT 0,1;",$params[0]);
        if ($rs = $db->fetch($res)) {
            // 验证用户名密码
            if ((int)$funcNum > 1) {
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
            } elseif ((int)$funcNum == 1) {
                // 验证权限正确，则返回管理员信息
                if ((string)$params[1] == (string)$rs['adminpass']) {
                    // 输入权限则进行验证，不输入权限则只返回管理员信息
                    if (isset($params[2])) {
                        $params[2] = strtolower($params[2]);
                        // 先查找是否有此权限
                        if (instr($rs['purview'],$params[2])) {
                            return $rs;
                        }
                    } else {
                        return $rs;
                    }
                }
            } else {
                // 验证是否登录
                if ((string)$params[1] == (string)$rs['adminpass']) {
                    return $rs;
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
        $hl.= '<div id="toolbar"></div>';
        $hl.= '</body></html>'; echo $hl;
    }
    /**
     * 取得用户组下拉列表
     *
     * @param int $p1   groupid
     * @param int $p2   selected
     * @return string
     */
    function getGroups($p1,$p2=null){
        $R = null; $db = get_conn();
        $res = $db->query("SELECT `groupid`,`groupname` FROM `#@_system_group` WHERE 1=1 ORDER BY `groupid` DESC;");
        while ($rs = $db->fetch($res,0)) {
            if ($p1 != $rs[0]) {
                $selected = ((int)$p2 == (int)$rs[0]) ? ' selected="selected"' : null;
                $R.= '<option value="'.$rs[0].'"'.$selected.'>'.$rs[1].'</option>';
            }
        }
        return $R;
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
