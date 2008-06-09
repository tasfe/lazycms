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
 * LazyCMS 抽象类
 *
 * @copyright   Copyright (c) 2007-2008 lazycms.net All rights reserved.
 * @author      Lukin <mylukin@gmail.com>
 */
// LazyCMS *** *** www.LazyCMS.net *** ***
abstract class LazyCMS extends Lazy{
    public $admin   = array();
    public $system  = array();
    public $outHTML = null;
    protected $PRIVATE_OBJECT_TEMPLATE;
    // 默认动作 *** *** www.LazyCMS.net *** ***
    abstract function _index();
    // run *** *** www.LazyCMS.net *** ***
    final public function run($l1=null,$l2=null){ // $l1:module, $l2:action
        try {
            self::init();
            self::exec($l1,$l2);
        } catch (Error $err) {
            self::error($err);
        }
    }
    // error *** *** www.LazyCMS.net *** ***
    final public function error($err){
        if (C('DEBUG_MODE')) {
            $e = $err->getError();
            include CORE_PATH.'/template/error.php';
        } else {
            $tpl = O('Template');
            $tpl->path = LAZY_PATH.C('PAGES_PATH').'/system/template';
            $tpl->assign(array(
                'title' => L('error/system'),
                'Message' => $err->getMessage(),
            ));
            $tpl->display('error.php');
        }
    }
    // init *** *** www.LazyCMS.net *** ***
    final public function init(){
        static $I1 = true;
        if (!$I1) {return ;} $I1 = false;
        if (!defined('E_STRICT')) { define('E_STRICT', 2048); }
        /**
         * 检查项目是否编译过
         * 在部署模式下会自动在第一次执行的时候编译项目
         */
        if (is_file(RUNTIME_PATH.'/~app.php')) {
            // 直接读取编译后的项目文件
            C(include RUNTIME_PATH.'/~app.php');
        } else {
            // 预编译项目
            self::build();
        }
        
        // 设置错误级别
        if (C('DEBUG_MODE')) {
            ini_set('display_errors',true);
            //error_reporting(E_ALL & ~E_NOTICE);
            error_reporting(E_ALL);
        } else {
            ini_set('display_errors',false);
            error_reporting(0);
        }

        // 取得真实的 PATH_INFO
        if (!empty($_SERVER['PATH_INFO']) || !empty($_SERVER['QUERY_STRING'])) {
            if(C('HTML_URL_SUFFIX')) {
                $suffix = substr(C('HTML_URL_SUFFIX'),1);
                if (!empty($_SERVER['PATH_INFO'])) { $_SERVER['PATH_INFO'] = preg_replace('/\.'.$suffix.'$/','',$_SERVER['PATH_INFO']); }
                if (!empty($_SERVER['QUERY_STRING'])) { $_SERVER['QUERY_STRING'] = preg_replace('/\.'.$suffix.'$/','',$_SERVER['QUERY_STRING']); }
            }
        }
        
        // 替换特殊字符
        $I2 = array(& $_SERVER['PATH_INFO'], & $_SERVER['REQUEST_URI'], & $_SERVER['QUERY_STRING'], & $_SERVER['PHP_SELF']);
        while (list($k,$v) = each($I2)) {
            $I2[$k] = parent::urlencode($I2[$k]);
        }
        unset($I2,$k,$v);

        // 解析魔术引号
        set_magic_quotes_runtime(0);
        if (get_magic_quotes_gpc()) {
            $I2 = array(& $_GET, & $_POST, & $_COOKIE, & $_REQUEST);
            while (list($k,$v) = each($I2)) {
                $I2[$k] = stripslashes_deep($I2[$k]);
            }
            unset($I2,$k,$v);
        }
        unset($_ENV,$HTTP_ENV_VARS,$HTTP_POST_VARS,$HTTP_GET_VARS,$HTTP_POST_FILES,$HTTP_COOKIE_VARS);
        // 设置系统时区 PHP5支持
        if(function_exists('date_default_timezone_set')) {
            date_default_timezone_set(C('TIME_ZONE'));
        }
        // URL映射到控制器对象
        Dispatcher::dispatch();
    }

    // build *** *** www.LazyCMS.net *** ***
    final private function build(){
        // 加载惯例配置文件
        C(include CORE_PATH.'/common/convention.php');
        // 加载用户自定义配置
        if (is_file(CORE_PATH.'/custom/config.php')) {
            C(include CORE_PATH.'/custom/config.php');
        }
        
        // 部署模式下面生成编译文件
        // 下次直接加载项目编译文件
        if (!C('DEBUG_MODE')) {
            $I1 = "<?php\nreturn ".var_export(C(),true).";\n?>";
            saveFile(RUNTIME_PATH.'/~app.php',$I1);
        }
    }

    // exec *** *** www.LazyCMS.net *** ***
    final private function exec($l1=null,$l2=null){ // $l1:module, $l2:action
        // 取得模块名称
        $I1 = Dispatcher::getModule();
        $I2 = Dispatcher::getAction();
        if (empty($l1) || strtolower($I1) != strtolower(C('DEFAULT_MODULE'))) {
            $l1 = $I1;
            $l2 = $I2;
        }
        // 设置当前的模块名称
        C('CURRENT_MODULE',$l1);
        // 设置当前的操作名称
        C('CURRENT_ACTION',$l2);
        // 组合出新的模块名称
        $l3 = 'Lazy'.ucfirst($l1);
        // 必须要加载system.module
        import("@.system.module");
        // 循环加载所有已安装模块的module
        try {// 没有错误，说明数据库连接成功，加载模块
            $db = DB::factory(C('DSN_CONFIG'));
            $db->select();
            $res = $db->query("SELECT * FROM `#@_system` WHERE `systemname`='LazyCMS';");
            if ($system = $db->fetch($res)) {
                $modules = $system['modules'];
            } else {
                $modules = null;
            }
            unset($db);
            if (!empty($modules)) {
                $_modules = explode(',',$modules);
                foreach ($_modules as $v) {
                    import("@.{$v}.common");
                    import("@.{$v}.module");
                }
                unset($_modules);
            }
        } catch (Error $err) {
            if (is_file(LAZY_PATH.'install.php')) {
                $UriBase = getUriBase();
                if (strpos(LAZY_PATH,'../')!==false) {
                    $path = substr(LAZY_PATH,strpos(LAZY_PATH,'../'));
                    if ($path=='../../') {
                        $UriBase = dirname($UriBase);
                        $path = ($UriBase=='\\' || $UriBase=='/') ? '/' : $UriBase.'/';
                    } else {
                        $UriBase = dirname(substr($UriBase,0,strrpos($UriBase,'/')));
                        $path = ($UriBase=='\\' || $UriBase=='/') ? '/' : $UriBase.'/';
                    }
                } else {
                    $UriBase = dirname($UriBase);
                    $path = ($UriBase=='\\' || $UriBase=='/') ? '/' : $UriBase.'/';
                }
                redirect($path.'install.php');exit;
            }
            // 这里数据库连接出错不进行错误提示。
            $modules = null;
            $system  = null;
            
        }
        // 判断模块是否安装
        if (!instr($modules,strtolower($l1)) && strtolower($l1)!='system') {
            throwError(L('error/nomodule'));
        }
        // 加载当前执行模块的control
        import("@.".strtolower($l1).".control");
        if(class_exists($l3)) {
            $module = new $l3();
            $module->system = $system;
            C('PRIVATE_OBJECT',$module);
        } else {
            $module = null;
        }
        if (!$module) {
            // 模块不存在，抛出异常
            throwError($l3.L('error/nocontrol'));
        }
        // 组合出新的操作名称
        $l4 = '_'.strtolower($l2);
        if (!method_exists($module,$l4)) {
            // 检查操作名是否存在，抛出异常
            throwError($l2.L('error/noaction'));
        }
        // 执行模板类对象创建
        $tpl = O('Template');
        $tpl->assign('module',$module);
        $module->PRIVATE_OBJECT_TEMPLATE = $tpl;unset($tpl);
        // 执行子类构造函数
        if (method_exists($module, '_initialize')) {
            $module->_initialize();
        }
        // 执行操作
        $module->{$l4}();
        // 执行子类析构函数
        if (method_exists($module, '_terminate')) {
            $module->_terminate();
        }
    }
    // assign *** *** www.LazyCMS.net *** ***
    final public function assign($l1,$l2=null){
        $this->PRIVATE_OBJECT_TEMPLATE->assign($l1,$l2);
    }
    // assign *** *** www.LazyCMS.net *** ***
    final public function fetch($l1,$l2=null){
        return $this->PRIVATE_OBJECT_TEMPLATE->fetch($l1,$l2);
    }
    // assign *** *** www.LazyCMS.net *** ***
    final public function display($l1,$l2=null){
        $this->PRIVATE_OBJECT_TEMPLATE->display($l1,$l2);
    }
    // method *** *** www.LazyCMS.net *** ***
    final public function method(){
        return $_SERVER['REQUEST_METHOD']=='POST' ? true : false;
    }
    // checker *** *** www.LazyCMS.net *** ***
    final public function checker($l1=0,$l2=null){
        $isLogin   = false;
        $db        = getConn();
        $adminname = Cookie::get('adminname');
        $adminpass = Cookie::get('adminpass');
        if ((empty($adminname) || empty($adminpass)) && empty($l2)) {
            redirect(url('System','Logout')); exit;
        } elseif ((empty($adminname) || empty($adminpass)) && !empty($l2)) {
            $this->poping(L('error/nologin'),1); exit;
        }
        $res   = $db->query("SELECT * FROM `#@_admin` WHERE `adminname` = ?;",$adminname);
        if ($data = $db->fetch($res)) {
            if ($adminpass==$data['adminpass']) {
                // 登录成功，不做操作
                $isLogin     = true;
                $this->admin = $data;
            }
        }
        // 验证不成功，退出登录
        if ($isLogin==false && empty($l2)) {
            redirect(url('System','Logout'));exit;
        } elseif ($isLogin==false && !empty($l2)){
            $this->poping(L('error/nologin'),1);exit;
        }
        if ($this->admin['adminlevel']=='admin' || instr($this->admin['adminlevel'],strtolower($l1))) {
        }else{
            // 权限不足，提示！
            if (empty($l2)) {
                throwError(L("error/jurisdiction"));
            } else {
                $this->poping(L('error/jurisdiction'),0);
            }
        }
    }
    // validate *** *** www.LazyCMS.net *** ***
    final public function validate($l1=null){
        static $js = null;
        static $I1 = false;
        if ($I1 && empty($l1)) { return true; }
        // 如果js变量不为空，返回false
        if (is_array($l1)) {
            $l2 = array_unset_empty($l1);
            if (empty($l2)) {
                $I1 = true;
            } else {
                // 返回false
                $js = "<script type=\"text/javascript\">\n\$(function(){\n\t";
                foreach ($l2 as $k=>$v) {
                    $js.= "labelError('{$k}','".t2js($v)."');\n\t";
                }
                $js.= "});\n</script>\n";
                $I1 = false;
            }
        } elseif (!empty($l1)) {
            // 输出JavaScript错误
            echo $js;
            $I1 = false;
        } else {
            $I1 = false;
        }
        return $I1;
    }
    // check *** *** www.LazyCMS.net *** ***
    final public function check($l1){
        if (!$this->method()) { return ; }
        $I2 = explode(";",$l1);
        foreach ($I2 as $v) {
            $check = check($v);
            if (!empty($check)) {
                $l2 = $check;
                break;
            }
        }
        if(!empty($l2)){
            return $l2;
        }
    }
    // succeed *** *** www.LazyCMS.net *** ***
    final public function succeed($l1=null){
        static $html = null;
        if (!empty($html) || empty($l1)) {
            return $html;
        } else {
            if (is_array($l1)) {
                $html = '<ol>';
                foreach ($l1 as $v) {
                    if (strpos($v,'|')!==false) {
                        $a = explode('|',$v);
                        $a = '<a href="'.$a[1].'">'.$a[0].'</a>';
                    } else {
                        $a = $v;
                    }
                    $html.= '<li>'.$a.'</li>';
                }
                if (isset($_SERVER['HTTP_REFERER'])) {
                    $html.= '<li><a href="'.$_SERVER['HTTP_REFERER'].'">'.$_SERVER['HTTP_REFERER'].'</a></li>';
                }
                $html.= '</ol>';
            } else {
                $html = '<p class="succeed">'.$l1.'</p>';
            }
        }
        return $html;
    }
    // poping *** *** www.LazyCMS.net *** ***
    final public function poping($l1,$l2=''){
        // $l1: array or string
        $title = $this->system['sitename'];
        if (is_array($l1)) {
            $title = $l1['title'].' - '.$title;
            $main  = isset($l1['main']) ? $l1['main'] : '';
        } else{
            $main  = $l1;
        }
        $inst = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : C('SITE_BASE');
        
        switch ((string)$l2) {
            case '0': 
                $main .= '<br/>[close]';
                break;
            case '1':
                $main .= '<br/>[redraw]';
                break;
        }
        if (strpos($main,'[redraw]')!==false) {
            $main = str_replace('[redraw]','<a href="'.str_replace('"',urlencode('"'),$inst).'">['.L('common/redraw').']</a>',$main);
        }
        if (strpos($main,'[close]')!==false) {
            $main = str_replace('[close]','<a href="javascript:void(0);" class="close">['.L('common/close').']</a>',$main);
        }
        $I1 = array(
            'title' => $title,
            'main'  => $main,
        );
        echo json_encode($I1);exit(0);
    }
    // L *** *** www.LazyCMS.net *** ***
    final public function L($l1,$l2=null){
        return L($l1,$l2,C('CURRENT_MODULE'));
    }
    // but *** *** www.LazyCMS.net *** ***
    final public function but($l1){
        $I1 = '<div class="button">';
        $I1.= '<button type="submit">'.L("common/{$l1}").'</button>';
        $I1.= '<button type="reset" onclick="javascript:return confirm(\''.L('confirm/reset').'\')">'.L('common/reset').'</button>';
        $I1.= '<button type="button" onclick="javascript:history.back();">'.L('common/back').'</button>';
        $I1.= '</div>';
        return $I1;
    }
    // diymenu *** *** www.LazyCMS.net *** ***
    final public function diymenu(){
        $db = getConn();
        $res   = $db->query("SELECT `diymenu` FROM `#@_admin` WHERE `adminname` = ?;",$this->admin['adminname']);
        if ($data = $db->fetch($res,0)) {
            $I1 = $data[0];
        }
        if (empty($I1)) {
            $res   = $db->query("SELECT `diymenu` FROM `#@_diymenu` WHERE `diymenulang` = ?;",$this->admin['adminlanguage']);
            if ($data = $db->fetch($res,0)) {
                $I1 = $data[0];
            }
            if (empty($I1)) {
                $I1 = defmenu();
            }
        }
        $I1 = formatMenu($I1);
        unset($db);
        return $I1;
    }
    // editor *** *** www.LazyCMS.net *** ***
    final public function editor($l1,$l2,$l3=array()){
        //$l1:字段名  $l2:值   $l3:编辑器设置
        $I2 = $l3;
        if (isset($I2['editor'])) {
            $l4 = $I2['editor'];
        } else {
            $l4 = $this->admin['admineditor'];
        }
        $l4 = strtolower($l4);
        switch ((string)$l4) {
            case 'fckeditor':
                vendor('fckeditor');
                $I3 = new FCKeditor($l1);
                $I3->BasePath = C('SITE_BASE').C('PAGES_PATH').'/system/editor/fckeditor/';
                if (isset($I2['toolbar'])) {
                    $I3->ToolbarSet = $I2['toolbar'];
                }
                if (isset($I2['width'])) {
                    $I3->Width = $I2['width'];
                }
                if (isset($I2['height'])) {
                    $I3->Height = $I2['height'];
                }
                if (isset($I2['config'])) {
                    $I3->Config = $I2['config'];
                }
                $I3->Value = $l2;
                if (empty($I2['print'])) {
                    return $I3->CreateHtml();
                } else {
                    $I3->Create();
                }
                unset($I3);
                break;
            case 'html': default:
                $cols  = isset($I2['cols'])?' cols="'.$I2['cols'].'"':null;
                $rows  = isset($I2['rows'])?' rows="'.$I2['rows'].'"':' rows="20"';
                $class = isset($I2['class'])?' class="'.$I2['class'].'"':' class="'.$l1.'__wp700"';
                return '<style type="text/css">.'.$l1.'__wp700{width:700px;border:solid 1px #7F9DB9;}</style><textarea name="'.$l1.'" id="'.$l1.'"'.$cols.$rows.$class.'>'.$l2.'</textarea>';
                break;
        }
    }
    // order *** *** www.LazyCMS.net *** ***
    final public function order($l1,$l2,$l3=null){
        // $l1:table,key,field
        // $l2:id,up or down ,number
        // $l3:where
        $I1 = array();
        $I2 = array();
        $I3 = explode(',',$l1);
        $I4 = explode(',',$l2);
        $db = getConn();
        $I4[1] = ($I4[1] == "down") ? " DESC" : " ASC";
        $where = !empty($l3) ? "WHERE {$l3}" : null;
        $res   = $db->query("SELECT `{$I3[1]}`,`{$I3[2]}` FROM `{$I3[0]}` {$where} ORDER BY `{$I3[2]}`{$I4[1]},`{$I3[1]}`{$I4[1]};");
        while ($data = $db->fetch($res,0)) {
            if ((int)$data[0] === (int)$I4[0]) {
                $i = 0;
                $I1[0] = $data[0];
                $I2[0] = $data[1];
                continue;
            }
            if (isset($i)) {
                $i++;
                if ((int)$i === (int)$I4[2]) {
                    $I1[1] = $data[0];
                    $I2[1] = $data[1];
                    $db->exec("UPDATE `{$I3[0]}` SET `{$I3[2]}`='{$I2[1]}' WHERE `{$I3[1]}`='{$I1[0]}';");
                    $db->exec("UPDATE `{$I3[0]}` SET `{$I3[2]}`='{$I2[0]}' WHERE `{$I3[1]}`='{$I1[1]}';");
                    break;
                }
            }
        }
        unset($db);
    }
    // keys *** *** www.LazyCMS.net *** ***
    final public function keys($l1,$l2=null){
        $I1 = null; $i = 0;
        $db = getConn();
        $rs = $db->query("SELECT `sitekeywords` FROM `#@_system` WHERE `systemname` = 'LazyCMS';");
        if ($data = $db->fetch($rs,0)) {
            $I2 = $data[0];
        }
        if (!empty($l2)) {
            $I1 = $l2;
            $I3 = explode(',',$l2);
            foreach ($I3 as $keyword){
                if (!instr($I2,$keyword)) {
                    $I2.= ','.trim($keyword);
                }
            }
            $I2 = ltrim($I2,',');
            $db->exec("UPDATE `#@_system` SET `sitekeywords`= ? WHERE `systemname`='LazyCMS';",$I2);
        } else {
            if (strlen($I2) > 0) {
                $I3 = explode(',',$I2);
                foreach ($I3 as $keyword){
                    if (strpos(strtolower($l1),strtolower($keyword))!==false) {
                        if (empty($I1)) {
                            $I1 = $keyword;
                        } else {
                            $I1.= ','.$keyword;
                        }
                        $i++; if ((int)$i > 11) {break;}    
                    }
                }
            }
        }
        return $I1;
    }
    // config *** *** www.LazyCMS.net *** ***
    final public function config($module){
        $module = strtolower($module);
        $config = loadFile(LAZY_PATH.C('PAGES_PATH').'/'.$module.'/config.php');
        $config = preg_match('/return array( *)\(((.|\n)+)\)/i',$config,$info) ? $info[2] : $config;
        $config = str_replace("\r\n","\n",$config); $config = explode("\n",$config);
        $count  = count($config); $label = O('Label'); $data = array(); $comments = array();
        $comment= null;
        if ($this->method()) {
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
                M($module,$k,$v);
            }
        }
        for ($i=0; $i<$count; $i++) {
            $v = ltrim($config[$i]);
            if (preg_match('/\,$/',$v)) {
                preg_match("/'(.+)'( *)\=\>( *)([^']+|'(.+)?')\,/i",$v,$info);
                $data['fieldename']  = $info[1];
                if ($this->method()) {
                    $value = M($module,strtolower($data['fieldename']));
                    if (is_numeric($value)) {
                        $value = (int)$value;
                    } elseif (is_bool($value)) {
                        $value = $value ? 'true' : 'false';
                    }
                    $data['fieldefault'] = $value;
                } else {
                    $data['fieldefault'] = isset($info[5]) ? $info[5] : $info[4];
                }
                $data['fieldefault'] = str_replace('\r',"\r",$data['fieldefault']);
                $data['fieldefault'] = str_replace('\n',"\n",$data['fieldefault']);
                $data['fieldefault'] = stripslashes($data['fieldefault']);
                $data['fieldtype']   = true;
                $label->p = '<p><label>'.$info[1].' <span><label class="error" for="'.$data['fieldename'].'">('.$data['fieldname'].')</label></span></label>'.$label->tag($data).'</p>';
                $comments[$data['fieldename']] = $comment;
                $data = array(); $comment= null;
            } elseif (preg_match('/^((\/\*\*)|(\*))/',$v)) {
                $ltv = ltrim($v,' *');
                $comment.= $config[$i].chr(10);
                if (trim($v)=='/**') {
                    $data['fieldname'] = ltrim($config[$i+1],' *');
                } elseif ($ltv!='' && strncmp($ltv,'@',1)===0) {
                    preg_replace('/@([^\:]+)\:(.+)/e','$data[\'\\1\'] = "\\2"',$ltv);
                }
            }
        }
        $this->outHTML = $label->fetch;
        if ($this->method()) {
            $array  = array_change_key_case(M($module),CASE_UPPER);
            $config = var_export($array,true);
            foreach ($array as $k=>$v) {
                $config = preg_replace("/ {2}'".preg_quote($k,'/')."' \=\> ([^']+|'(.+)?')\,/i",$comments[$k].'\\0',$config);
            }
            saveFile(LAZY_PATH.C('PAGES_PATH').'/'.$module.'/config.php',"<?php\n".createNote(ucfirst($module).' module configuration files')."\nreturn ".$config.";\n?>");
            $this->succeed(L('common/upok'));
        }
    }
}
?>