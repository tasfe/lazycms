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
define('CACHE_RUNTIME',false);
define('CORE_PATH', dirname(__FILE__).'/core');
require CORE_PATH."/LazyCMS.php";
/**
 * LazyCMS 系统引导安装程序
 *
 * @copyright   Copyright (c) 2007-2008 lazycms.net All rights reserved.
 * @author      Lukin <mylukin@gmail.com>
 */
// 先清除缓存文件
if (is_file(RUNTIME_PATH.'/~app.php')) {
    unlink(RUNTIME_PATH.'/~app.php');
}
if (is_file(RUNTIME_PATH.'/~runtime.php')) {
    unlink(RUNTIME_PATH.'/~runtime.php');
}
// 加载惯例配置文件
C(include CORE_PATH.'/common/convention.php');
// 加载用户自定义配置
if (is_file(CORE_PATH.'/custom/config.php')) {
    C(include CORE_PATH.'/custom/config.php');
}

// labelError *** *** www.LazyCMS.net *** ***
function labelError($l1=null,$l2=null){
    static $checkerr = array();
    if (!empty($l2)) {
        $labelError = '<label class="error" for="'.$l1.'">'.$l2.'</label>'; 
        $checkerr[] = $labelError;
        return $labelError;
    } elseif (empty($l1)) {
        return empty($checkerr) ? true : false;
    }
}

$agree   = isset($_POST['agree']) ? $_POST['agree'] : null;
$install = isset($_POST['install']) ? $_POST['install'] : null;

// 获取安装目录
$sitebase = dirname(getUriBase());
$sitebase = ($sitebase=="\\" || $sitebase=="/") ?
            '/' : substr($sitebase.'/',0,strrpos($sitebase.'/','/')+1);
$sitebase  = isset($_POST['sitebase']) ? $_POST['sitebase'] : $sitebase;
$sitename  = isset($_POST['sitename']) ? $_POST['sitename'] : null;
$sitemail  = isset($_POST['sitemail']) ? $_POST['sitemail'] : null;
$keywords  = isset($_POST['keywords']) ? $_POST['keywords'] : null;
$sitemode  = isset($_POST['sitemode']) ? $_POST['sitemode'] : C('SITE_MODE');
$sitemode  = $sitemode=='true' ? true : false;
$dsnPrefix = isset($_POST['dsn_prefix']) ? $_POST['dsn_prefix'] : C('DSN_PREFIX');
$dsnConfig = isset($_POST['dsn_config']) ? $_POST['dsn_config'] : C('DSN_CONFIG');
$modules   = isset($_POST['modules']) ? $_POST['modules'] : null;

$leadinData    = isset($_POST['leadinData']) ? $_POST['leadinData'] : true;
$adminname     = isset($_POST['adminname']) ? $_POST['adminname'] : null;
$adminpass     = isset($_POST['adminpass']) ? $_POST['adminpass'] : null;
$adminlanguage = isset($_POST['adminlanguage']) ? $_POST['adminlanguage'] : null;
$admineditor   = isset($_POST['admineditor']) ? $_POST['admineditor'] : null;

// 验证网站名称
$sitename_err  = $install ? labelError('sitename',check('sitename|1|网站名称不能为空，长度≤50|1-50')) : null;
// 验证数据库连接字符串
$dsnConfig_err = $install ? labelError('dsn_config',check('dsn_config|0|数据库连接字符串不能为空')) : null;
if (!preg_match('/^(.+):\/\/(.[^:]+)(:(.[^@]+)?)?@([a-z0-9\-\.]+)(:(\d+))?\/(\w+)/i',trim($dsnConfig)) && empty($dsnConfig_err)) {
    $dsnConfig_err = labelError('dsn_config','格式不正确，请参考[注解]格式。');
}
// 验证管理员名称
$adminname_err = $install ? labelError('adminname',check('adminname|1|管理员名称不能为空，长度为2-12个字符|2-12')) : null;
// 验证管理员密码
$adminpass_err = $install ? labelError('adminpass',check('adminpass|1|管理员密码不能为空，长度为6-30个字符|6-30')) : null;
if (empty($adminpass_err)) {
    $adminpass_err = $install ? labelError('adminpass',check('adminpass|2|两次输入的密码不一致|adminpass1')) : null;
}
// install
if ($install && labelError()) {
    $config = array(
        'SITE_BASE'  => $sitebase,
        'SITE_MODE'  => $sitemode,
        'DSN_CONFIG' => $dsnConfig,
        'DSN_PREFIX' => $dsnPrefix,
        'DEBUG_MODE' => true,
    );
    // 动态模式，删除page目录的index.php 生成一个空的index.html
    if ((bool)$sitemode) {
        @unlink(LAZY_PATH.C('PAGES_PATH').'/index.php');
        saveFile(LAZY_PATH.C('PAGES_PATH').'/index.html');
    } else { // 静态模式，根目录下的index.php自动删除。
        @unlink(LAZY_PATH.C('PAGES_PATH').'/index.html');
        @unlink(LAZY_PATH.'index.php');
    }
    C($config);
    try {
        // 取得db对象
        $db = DB::factory($dsnConfig);
        try {
            $db->select();
        } catch (Error $err) {
            if ($db->errno() != 0) {
                // 创建数据库
                $db->exec("CREATE DATABASE `".$db->getDataBase()."` DEFAULT CHARSET=utf8");
                if ($db->errno()==1044) {
                    throwError('指定的数据库不存在，系统也无法自动建立。');
                } else {
                    $db->select();
                }
            }
        }
        import("@.system.module");
        $instSQL = System::instSQL();
        // 安装用户选择的模块
        $mMenu   = null;
        if (!empty($modules)) {
            foreach ($modules as $module) {
                import("@.{$module}.module");
                $obj = new $module();
                if (method_exists($obj,'uninstSQL')) {
                    $db->batQuery($obj->uninstSQL());
                }
                if (method_exists($obj,'instSQL')) {
                    $db->batQuery($obj->instSQL());
                } unset($obj);
                $mMenu .= L('title',null,$module)."|".L('manage',null,$module)."\r\n";
            }
        }

        // 批量执行sql
        $db->batQuery($instSQL);

        // 添加自定义菜单
        $diyMenu = array(
            'diymenulang' => C('LANGUAGE'),
            'diymenu'     => $mMenu.defmenu(),
        );
        $db->insert('#@_diymenu',$diyMenu);

        // 添加站点设置
        $system = array(
            'systemname'    => 'LazyCMS',
            'systemver'     => LAZY_VERSION,
            'dbversion'     => '1.1.0',
            'sitename'      => $sitename,
            'sitemail'      => $sitemail,
            'sitekeywords'  => $keywords,
            'modules'       => implode(',',$modules),
            'instdate'      => now(),
        );
        $db->insert('#@_system',$system);
        
        // 添加管理员
        $adminkey = salt();
        $admin = array(
            'adminname'     => $adminname,
            'adminpass'     => md5($adminpass.$adminkey),
            'adminkey'      => $adminkey,
            'adminlevel'    => 'admin',
            'adminlanguage' => $adminlanguage,
            'admineditor'   => $admineditor,
            'admindate'     => now(),
        );
        $db->insert('#@_admin',$admin);
        // 安装示例数据
        if ($leadinData) {
            $instSQL = loadFile(LAZY_PATH.C('TEMPLATE_PATH').'/install.sql');
            $db->batQuery($instSQL);
            @unlink(LAZY_PATH.C('TEMPLATE_PATH').'/install.sql');
        } else {
            // 添加首页
            $onepage = array(
                'oneorder'     => $db->max('oneid','#@_onepage'),
                'onetitle'     => L('common/home'),
                'onepath'      => C('SITE_INDEX'),
                'onename'      => L('common/home'),
                'onecontent'   => L('common/home'),
                'onetemplate1' => C('TEMPLATE_PATH').'/'.C('TEMPLATE_DEF'),
                'onetemplate2' => C('TEMPLATE_PATH').'/inside/onepage/'.C('TEMPLATE_DEF'),
                'ishome'       => '1',
            );
            $db->insert('#@_onepage',$onepage);
            // 导入默认模型
            import("@.archives.module");
            $models = getArrDir('@.archives.models','xml');
            foreach ($models as $model){
                Archives::installModel(loadFile(LAZY_PATH.C('PAGES_PATH')."/archives/models/{$model}"),true);
            }
        }
        saveFile(CORE_PATH.'/custom/config.php',"<?php\n".createNote('用户自定义配置文件')."\nreturn ".var_export($config,true).";\n?>");
        // 删除安装文件
        @unlink(LAZY_PATH.'install.php');
        redirect('admin/index.php');
    } catch (Error $err) {
        $dsnConfig_err = labelError('dsn_config',$err->getMessage());
    }
}
clearstatcache();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>LazyCMS 系统安装程序</title>
<script type="text/javascript" src="./page/system/js/jquery.js"></script>
<script type="text/javascript" src="./page/system/js/jquery.lazycms.js"></script>
<style type="text/css">
body{ width:750px; margin:5px auto 10px auto; background:#FBFBFB;}
body,th,td,p{ line-height:150%; font-family:Verdana; font-size:12px; color:#333333;}

.lz_form h2{ color:#193441; font-size:130%; padding:0px 0px 5px 0px; margin:10px 0px 10px 0px; border-bottom: 1px dashed #E0E0E0;}
.lz_form .table{ width:100%; border-collapse:collapse; margin:3px auto; }
    .lz_form .table th ,
    .lz_form .table td{ padding:3px; padding-bottom:2px;}
    .lz_form .table th{ background:#F5F5F5; border-style:solid; border-width:1px; border-color:#CECECE #A8A8A8 #A8A8A8 #CECECE; color:#193441; letter-spacing:1px;}
    .lz_form .table td{ border:solid 1px #CECECE;}
    
.lz_form .license h4{ margin:0;padding:0; font-size:14px;}
    .lz_form .license p{ margin:5px 0px;}
    .lz_form .license blockquote{margin-left:10px;}

.lz_form .say{ width:97%; border:solid 1px #712704; padding:3px 1%; margin:10px auto; background:#FCFC8A; text-align:center; color:#0000FF;}
.lz_form label.error{ display:block; color:#993300; clear:both; cursor:pointer; padding-top:2px;}
.lz_form .button{ width:100%; text-align:center; margin:10px auto; display:table;}
    .lz_form .button button{ margin-right:6px; letter-spacing:3px; padding:1px 2px; border:1px solid; border-color:#EEE #777 #777 #EEE; background:#D4D0C8; font-size:12px; line-height:100%; vertical-align:middle;}

/* 表单长度设置 */
.in0{ width:50px;border:solid 1px #7F9DB9;}
.in1{ width:100px;border:solid 1px #7F9DB9;}
.in2{ width:200px;border:solid 1px #7F9DB9;}
.in3{ width:300px;border:solid 1px #7F9DB9;}
.in4{ width:400px;border:solid 1px #7F9DB9;}
.in5{ width:500px;border:solid 1px #7F9DB9;}
.in6{ width:600px;border:solid 1px #7F9DB9;}
</style>
</head>
<body>
<form id="form1" name="form1" method="post" action="install.php" class="lz_form">
  <?php if ($agree) :?>
  <h2>系统设置</h2>
  <table class="table">
    <tr>
      <th width="30%">网站名称</th>
      <td><input class="in2" name="sitename" type="text" id="sitename" maxlength="50" value="<?php echo $sitename;?>" /> (1-50)<?php echo $sitename_err;?></td>
    </tr>
    <tr>
      <th>管理员信箱</th>
      <td><input class="in3" type="text" id="sitemail" name="sitemail" maxlength="100" value="<?php echo $sitemail;?>" /></td>
    </tr>
    <tr>
      <th>网站模式</th>
      <td><input type="radio" name="sitemode" id="sitemode_true" value="1"<?php echo $sitemode ? ' checked="checked"' : null;?>/><label for="sitemode_true">全站动态</label>
          <input name="sitemode" id="sitemode_false" type="radio" value="0"<?php echo !$sitemode ? ' checked="checked"' : null;?> /><label for="sitemode_false">全站静态</label>
      </td>
    </tr>
    <tr>
      <th>数据表前缀</th>
      <td><input class="in1" type="text" id="dsn_prefix" name="dsn_prefix" value="<?php echo $dsnPrefix;?>" /></td>
    </tr>
    <tr>
      <th>程序安装目录</th>
      <td><input class="in2" type="text" id="sitebase" name="sitebase" value="<?php echo $sitebase;?>" /> 一般安装时无需修改。</td>
    </tr>
    <tr>
      <th>数据库连接字符串</th>
      <td>注解：数据库类型://用户名:密码[可选]@主机名:端口[可选]/数据库名称<br/><input class="in4" type="text" id="dsn_config" name="dsn_config" value="<?php echo $dsnConfig;?>" /><?php echo $dsnConfig_err;?></td>
    </tr>
    <tr>
      <th>模块安装</th>
      <td>
        <?php 
        $_modules = getArrDir(C('PAGES_PATH'),'dir');
        $selected = 'mytags,feedback,archives,onepage';
        foreach ($_modules as $m) {
            if (strtolower($m) != 'system') {
                $checked = instr($modules,$m) ? ' checked="checked"' : null;
                if (instr($selected,$m)) { 
                    $checked = instr($selected,$m) ? ' checked="checked"' : null;
                    $disabled = ' disabled="disabled"';
                    echo '<input name="modules[]" type="hidden" value="'.$m.'" />';
                } else {
                    $disabled = null;
                }
                echo '<input type="checkbox" name="modules[]" id="m_'.$m.'" value="'.$m.'"'.$checked.$disabled.'/><label for="m_'.$m.'">'.L('title',null,$m).'</label>'.chr(10);
            }
        }
        ?>
      </td>
    </tr>
    <tr>
      <th>示例数据</th>
      <td>
          <input type="radio" name="leadinData" id="leadinData_true" value="1"<?php echo $leadinData ? ' checked="checked"' : null;?>/><label for="leadinData_true">[推荐新手]导入数据</label>
          <input name="leadinData" id="leadinData_false" type="radio" value="0"<?php echo !$leadinData ? ' checked="checked"' : null;?> /><label for="leadinData_false">不导入数据</label>
      </td>
    </tr>
    <tr>
      <th>权限检测</th>
      <td>
        <table class="table">
            <tr>
                <th>路径</th>
                <th>可读</th>
                <th>可写</th>
            </tr>
            <?php 
                $CORE_PATH = './'.str_replace(dirname(__FILE__).'/','',CORE_PATH);
                $paths = array('./','./'.C('PAGES_PATH'),$CORE_PATH,$CORE_PATH.'/custom','./'.C('TEMPLATE_PATH'),'./'.C('UPFILE_PATH'));
                while (list(,$path) = each($paths)) :
                mkdirs($path);
            ?>
            <tr>
                <td><?php echo $path;?></td>
                <td><?php echo isOK(is_readable($path));?></td>
                <td><?php echo isOK(is_writable($path));?></td>
            </tr>
            <?php endwhile;?>
        </table>
      </td>
    </tr>
  </table>
  <h2>管理员设置</h2>
  <table class="table">
    <tr>
      <th width="30%">管理员名称</th>
      <td><input class="in2" name="adminname" type="text" id="adminname" maxlength="12" value="<?php echo $adminname;?>" /> (2-12)<?php echo $adminname_err;?></td>
    </tr>
    <tr>
      <th>管理员密码</th>
      <td><input class="in2" type="password" id="adminpass" name="adminpass" maxlength="30" /> (6-30)<?php echo $adminpass_err;?></td>
    </tr>
    <tr>
      <th>确认密码</th>
      <td><input class="in2" type="password" id="adminpass1" name="adminpass1" maxlength="30" /></td>
    </tr>
    <tr>
      <th>界面语言</th>
      <td><select name="adminlanguage" id="adminlanguage">
          <?php echo formOpts('@.system.language','xml','<option value="#value#"#selected#>#name#</option>',$adminlanguage);?>
        </select>
      </td>
    </tr>
    <tr>
      <th>编辑器</th>
      <td><select name="admineditor" id="admineditor">
          <?php echo formOpts('@.system.editor','dir','<option value="#value#"#selected#>#name#</option>',$admineditor);?>
        </select>
      </td>
    </tr>
  </table>
  <div class="button">
    <input name="agree" type="hidden" id="agree" value="<?php echo $agree;?>" />
    <input name="install" type="hidden" id="install" value="1" />
    <button type="submit"> 安 装 </button>
    <button type="button" onclick="javascript:self.location.href='install.php';"> 返 回 </button>
  </div>
  <?php else:?>
  <table class="table">
    <tr>
      <th>〓 许可协议 〓</th>
    </tr>
    <tr>
      <td><div class="license">
          <ul>
            <li>本软件是自由软件，遵循 Apache License 2.0 许可协议 &lt;<a target="_blank" href="http://www.apache.org/licenses/LICENSE-2.0">http://www.apache.org/licenses/LICENSE-2.0</a>&gt;</li>
            <li>本软件的版权归 LazyCMS官方 所有，且受《中华人民共和国计算机软件保护条例》等知识产权法律及国际条约与惯例的保护。</li>
            <li>本协议适用且仅适用于 LazyCMS 1.x 版本，LazyCMS官方 拥有对本协议的最终解释权。</li>
            <li><u>无论个人或组织、盈利与否、用途如何（包括以学习和研究为目的），均需仔细阅读本协议，在理解、同意、并遵守本协议的全部条款后，方可开始使用本软件。</u></li>
          </ul>
          <blockquote>
            <h4><strong>I.协议许可和限制</strong></h4>
            <ol>
              <li><u>未经作者书面许可，不得衍生出私有软件。</u></li>
              <li>使用者所生成的网站，首页要包含软件的版权信息；不得对后台版权进行修改。</li>
              <li><u>您拥有使用本软件构建的网站全部内容所有权，并独立承担与这些内容的相关法律义务。</u></li>
              <li>您可以对源码进行修改及优化，但要保证源码的完整性；修改后的代码版权归开发者所有，未经开发者许可，不得私自发布。</li>
              <li><u>您将本软件应用在商业用途时，需遵守以下几条：</u>
                <ol>
                  <li><u>使用本软件建设网站时，无需支付使用费用，但需保留LazyCMS支持链接信息。</u></li>
                  <li>本源码可以用在商业用途，但不可以更名销售，若有OEM需求，请和作者联系。</li>
                  <li>若网站性质等因素所限，不适合保留支持信息，请与作者联系取得书面授权。</li>
                </ol>
              </li>
            </ol>
            <h4><strong>II.有限担保和免责声明</strong></h4>
            <ol>
              <li>本软件及所附带的文件是作为不提供任何明确的或隐含的赔偿或担保的形式提供的。</li>
              <li>用户出于自愿而使用本软件，您必须了解使用本软件的风险，在尚未购买产品技术服务之前，我们不承诺提供任何形式的技术支持、使用担保，也不承担任何因使用本软件而产生问题的相关责任。</li>
              <li>LazyCMS官方不对使用本软件构建的网站中的文章或信息承担责任。</li>
            </ol>
            <p>本协议保留作者的版权信息在许可协议文本之内，不得擅自修改其信息。</p>
            <p>2007-11，第1.0版 (保留对此许可协议的更新及解释权力)<br />
              协议著作权所有 &copy; LazyCMS.net&nbsp;&nbsp;&nbsp; &nbsp; &nbsp; 软件版权所有 &copy; LazyCMS.net</p>
            <p>作者：Lukin&nbsp;&nbsp;&nbsp; 邮箱：<a href="mailto:mylukin@gmail.com">mylukin@gmail.com</a></p>
          </blockquote>
        </div></td>
    </tr>
  </table>
  <div class="say">如果你接受协议中的条款，单击 [我同意] 继续安装。如果你选定 [取消] ，安装程序将会关闭。必须接受协议才能安装 LazyCMS。</div>
  <div class="button">
    <input name="agree" type="hidden" id="agree" value="1" />
    <button type="submit">我同意</button>
    <button type="button" onclick="javascript:self.window.close();">取 消</button>
  </div>
  <?php endif;?>
</form>
</body>
</html>
