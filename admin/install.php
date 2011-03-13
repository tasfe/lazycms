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
 * | Copyright (C) 2007-2010 LazyCMS.com All rights reserved.                  |
 * +---------------------------------------------------------------------------+
 * | LazyCMS is free software. See LICENSE for copyright notices and details.  |
 * +---------------------------------------------------------------------------+
 */
// 定义管理后台路径
defined('ADMIN_PATH') or define('ADMIN_PATH',dirname(__FILE__));
// 禁止重复跳转
define('NO_REDIRECT',true);
// 加载公共文件
include ADMIN_PATH.'/admin.php';
// 检查系统是否已经安装
if (installed()) redirect(ADMIN);
// 系统需要安装
$config_exist = is_file(ABS_PATH.'/config.php');

$setup = isset($_POST['setup']) ? $_POST['setup'] : 'default';

switch($setup) {
    case 'install':
        if (validate_is_post()) {
            $dbtype = isset($_POST['dbtype']) ? $_POST['dbtype'] : null;

            validate_check('license',VALIDATE_EMPTY,__('You must accept the license before they can continue the installation!'));
            // 配置文件不存在，需要填写表前缀
            if (!$config_exist) {
                validate_check('prefix', '/^[\w]+$/i', __('"Table Prefix" can only contain numbers, letters, and underscores.'));
            }
            // 用户名
            validate_check('adminname',VALIDATE_EMPTY,__('You must provide a valid username.'));
            // 密码
            validate_check('password1',VALIDATE_EQUAL,__('Your passwords do not match. Please try again.'),'password2');
            // 验证EMAIL
            validate_check(array(
                array('email',VALIDATE_EMPTY,__('Please enter an e-mail address.')),
                array('email',VALIDATE_IS_EMAIL,__('You must provide an e-mail address.'))
            ));
            if (validate_is_ok()) {
                $writable = true;
                fcache_flush();
                // 需要设置数据库配置
                if (!$config_exist) {
                    $dbname = isset($_POST['dbname'])?$_POST['dbname']:null;
                    $uname  = isset($_POST['uname'])?$_POST['uname']:null;
                    $pwd    = isset($_POST['pwd'])?$_POST['pwd']:null;
                    $dbhost = isset($_POST['dbhost'])?$_POST['dbhost']:null;
                    $prefix = isset($_POST['prefix'])?$_POST['prefix']:null;
                    // mysql DSN
                    if (instr($dbtype,'mysql,mysqli')) {
                        $db_dsn = sprintf('%1$s:host=%2$s;name=%3$s;prefix=%4$s;', $dbtype, $dbhost, $dbname, $prefix);
                    }
                    // sqlite DSN
                    elseif (instr($dbtype,'sqlite2,sqlite3,pdo_sqlite2,pdo_sqlite')) {
                        $db_dsn = sprintf('%1$s:name=%2$s;prefix=%3$s;', $dbtype, $dbname, $prefix);
                    }
                    // 清理之前的错误
                    $err = last_error(null);
                    // 测试数据库链接信息
                    $db  = @DBQuery::factory($db_dsn, $uname, $pwd);
                    // 取得错误信息
                    $err = last_error();
                    // 有错误，提示
                    if ($err) ajax_alert($err['error']);

                    // 检查 config.sample.php 文件是否存在
                    if (!is_file(COM_PATH.'/config.sample.php')) {
                        ajax_error(__('Sorry, I need a common/config.sample.php file to work from. Please re-upload this file from your LazyCMS installation.'));
                    }
                    // 替换变量
                    $configs = file(COM_PATH.'/config.sample.php');
                    foreach ($configs as $num => $line) {
                        switch(substr($line,0,16)) {
                            case "define('DB_DSN',":
                                $configs[$num] = str_replace("database_dsn_here", $db_dsn, $line);
                                break;
                            case "define('DB_USER'":
                                $configs[$num] = str_replace("username_here", $uname, $line);
                                break;
                            case "define('DB_PWD',":
                                $configs[$num] = str_replace("password_here", $pwd, $line);
                                break;
                        }
                    }
                    // 检查是否具有写入权限
                    if ($writable = is_writable(COM_PATH.'/')) {
                        $config = implode('', $configs);
                        file_put_contents(ABS_PATH.'/config.php', $config);
                    }
                    // 定义数据库链接
                    define('DB_DSN',$db_dsn);
                    define('DB_USER',$uname);
                    define('DB_PWD',$pwd);
                }
                $db = @get_conn();
                $query = install_schema();
                // 创建数据表
                $db->batch($query);
                // 是否安装初始数据
                $initial = isset($_POST['initial'])?$_POST['initial']:null;
                // 安装默认设置
                install_defaults($initial);
                // 保存用户填写的信息
                $sitetitle = isset($_POST['sitetitle'])?$_POST['sitetitle']:'';
                $adminname = isset($_POST['adminname'])?$_POST['adminname']:'';
                $password  = isset($_POST['password1'])?$_POST['password1']:'';
                $email     = isset($_POST['email'])?$_POST['email']:'';

                if ($sitetitle) C('SiteTitle',$sitetitle);
                // 管理员存在，修改管理员信息
                if ($admin = user_get_byname($adminname)) {
                    user_edit($admin['userid'],array(
                        'pass' => sha1($password.$admin['authcode']),
                        'mail' => esc_html($email),
                        'roles' => 'ALL',
                        'Administrator' => 'Yes'
                    ));
                }
                // 添加管理员
                else {
                    user_add($adminname,$password,$email,array(
                        'url'  => esc_html(HTTP_HOST),
                        'nickname' => esc_html($adminname),
                        'roles' => 'ALL',
                        'Administrator' => 'Yes'
                    ));
                }
                if ($writable) {
                    ajax_success(__('All right sparky! You\'ve made it through of the installation. If you are ready, time now to&hellip;'), "LazyCMS.redirect('".ADMIN."');");
                } else {
                    ajax_alert(__('Create config.php failed, Please manually create.'), "LazyCMS.redirect('".ADMIN."');");
                }
            }
        }
        break;
    case 'config':
        $html = '<form action="'.PHP_FILE.'" method="post" name="setup_cfg" id="setup_cfg">';
        if (!$config_exist) {
            $html.=     '<p>'.__('Welcome to LazyCMS! Below you should enter your database connection details. If you\'re not sure about these, contact your host.').'</p>';
            $html.=     '<table class="data-table">';
            $html.=         '<thead><tr><th colspan="3">'.__('Database Configuration').'</th></tr></thead>';
            $html.=         '<tbody>';
            $html.=             '<tr><th class="w150"><label for="dbtype">'.__('Database Type').'</label></th><td><select name="dbtype" id="dbtype">';
            // sqlite3
            $phpinfo = parse_phpinfo();
            $sqlite  = isset($phpinfo['pdo_sqlite']) ? array_shift($phpinfo['pdo_sqlite']) == 'enabled' : false;
            if ($r = class_exists('SQLite3')) {
                $version = SQLite3::version();
                $html.=             sprintf('<option value="sqlite3">SQLite %s</option>', $version['versionString']);
            } elseif (extension_loaded('pdo_sqlite') && $sqlite) {
                $version = $phpinfo['pdo_sqlite']['SQLite Library'];
                $value   = version_compare($version, '3.0.0', '<') ? 'pdo_sqlite2' : 'pdo_sqlite';
                $html.=             sprintf('<option value="%s">PDO_SQLite %s</option>', $value, $version);
            }
            // sqlite2
            if ($r = function_exists('sqlite_libversion')) {
                $html.=             sprintf('<option value="sqlite2">SQLite %s</option>', sqlite_libversion());
            }
            // mysql
            if ($r = function_exists('mysqli_get_client_info')) {
                $html.=             sprintf('<option value="mysqli">MySQLi %s</option>', mysqli_get_client_info());
            }
            if ($r = function_exists('mysql_get_client_info')) {
                $html.=             sprintf('<option value="mysql">MySQL %s</option>', mysql_get_client_info());
            }
            $html.=             '</select></td><td>'.__('Recommended to use SQLite 3.x or MySQLi.').'</td></tr>';
            $html.=             '<tr><th><label for="dbname">'.__('Database Name').'</label></th><td><input class="text" type="text" name="dbname" id="dbname" value="test" rel="'.str_rand(10).'" /></td><td>'.__('The name of the database you want to run LazyCMS in.').'</td></tr>';
            $html.=             '<tr><th><label for="uname">'.__('UserName').'</label></th><td><input class="text" type="text" name="uname" id="uname" value="" /></td><td>'.__('Your MySQL username').'</td></tr>';
            $html.=             '<tr><th><label for="pwd">'.__('Password').'</label></th><td><input class="text" type="text" name="pwd" id="pwd" value="" /></td><td>'.__('...and MySQL password.').'</td></tr>';
            $html.=             '<tr><th><label for="dbhost">'.__('Database Host').'</label></th><td><input class="text" type="text" name="dbhost" id="dbhost" value="localhost" /></td><td>'.__('You should be able to get this info from your web host, if <code>localhost</code> does not work.').'</td></tr>';
            $html.=             '<tr><th><label for="prefix">'.__('Table prefix').'</label></th><td><input class="text" type="text" name="prefix" id="prefix" value="lazy_" /></td><td>'.__('If you want to run multiple LazyCMS installations in a single database, change this.').'</td></tr>';
            $html.=         '</tbody>';
            $html.=     '</table>';
        }
        $html.=     '<p>'.__('Please provide the following information. Don’t worry, you can always change these settings later.').'</p>';
        $html.=     '<table class="data-table">';
        $html.=         '<thead><tr><th colspan="3">'.__('Information needed').'</th></tr></thead>';
        $html.=         '<tbody>';
        $html.=             '<tr><th class="w150"><label for="sitetitle">'.__('Site Title').'</label></th><td><input class="text" type="text" name="sitetitle" id="sitetitle" /></td><td>&nbsp;</td></tr>';
        $html.=             '<tr><th><label for="adminname">'.__('UserName').'</label></th><td><input class="text" type="text" name="adminname" id="adminname" /></td><td>'.__('Administrator account.').'</td></tr>';
        $html.=             '<tr>';
        $html.=                 '<th><label for="password1">'.__('Password, twice').'</label></th>';
        $html.=                 '<td><input class="text" type="password" name="password1" id="password1" /><br /><input class="text" type="password" name="password2" id="password2" /><br /><div id="pass-strength-result" class="pass-strength">'.__('Strength indicator').'</div></td>';
        $html.=                 '<td>'.__('Hint: The password should be at least seven characters long. To make it stronger, use upper and lower case letters, numbers and symbols like ! " ? $ % ^ & ).').'</td>';
        $html.=             '</tr>';
        $html.=             '<tr><th><label for="email">'.__('Your E-mail').'</label></th><td><input class="text" type="text" name="email" id="email" /></td><td>'.__('Double-check your email address before continuing.').'</td></tr>';
        $html.=         '</tbody>';
        $html.=     '</table>';
        $html.=     '<p><label for="initial"><input type="checkbox" name="initial" id="initial" value="1" checked="checked" />'.__('Installing initial data').'</label></p>';
        $html.=     '<p><label for="license"><input type="checkbox" name="license" id="license" value="agree" />'.sprintf(__('I accept the <a href="%s" target="_blank">License</a>'), '../LICENSE').'</label></p>';
        $html.=     '<p class="buttons">';
        $html.=         '<input type="hidden" name="setup" value="install" />';
        $html.=         '<button type="submit">'.__('Install').'</button>';
        $html.=         '<button type="button" onclick="LazyCMS.redirect(\''.PHP_FILE.'\')">'.__('Back').'</button>';
        $html.=     '</p>';
        $html.= '</form>';
        install_wrapper($html);
        break;
    default:
        $error_level = error_reporting(0);
        $html = '<form action="'.PHP_FILE.'" method="post" name="setup" id="setup">';
        $html.=     '<table class="data-table">';
        $html.=         '<thead><tr><th colspan="3">'.__('System Information').'</th></tr></thead>';
        $html.=         '<tbody>';
        $html.=             '<tr><td>'.__('Server OS').'</td><td>'.PHP_OS .' '. php_uname('r') .' On '. php_uname('m').'</td></tr>';
        $html.=             '<tr><td>'.__('Server Software').'</td><td>'.$_SERVER['SERVER_SOFTWARE'].'</td></tr>';
        $html.=             '<tr><td>'.__('Server API').'</td><td>'.PHP_SAPI.'</td></tr>';
        $html.=         '</tbody>';
        $html.=     '</table>';
        // HTTPLIB
        include COM_PATH.'/system/httplib.php';
        $http_test = httplib_test();
        $html.=     '<table class="data-table">';
        $html.=         '<thead><tr><th colspan="3">'.__('Required Settings').'</th></tr></thead>';
        $html.=         '<tbody>';
        $html.=             '<tr class="thead"><th>'.__('Test').'</th><th class="w100">'.__('Require').'</th><th class="w150">'.__('Current').'</th></tr>';
        $html.=             '<tr><td>'.__('PHP Version').'</td><td>4.3.3+</td><td>'.test_result(version_compare(PHP_VERSION,'4.3.3','>')).'&nbsp; '.PHP_VERSION.'</td></tr>';
        $html.=             '<tr><td>'.__('DB Driver').'</td><td>SQLite 2.8.0+<br />MySQL 4.1.0+</td><td>';
        // sqlite
        $phpinfo = parse_phpinfo();
        $sqlite  = isset($phpinfo['pdo_sqlite']) ? array_shift($phpinfo['pdo_sqlite']) == 'enabled' : false;
        if ($r = class_exists('SQLite3')) {
            $version = SQLite3::version();
            $html.=             test_result($r).'&nbsp; SQLite '.$version['versionString'];
        } elseif (extension_loaded('pdo_sqlite') && $sqlite) {
            $version = $phpinfo['pdo_sqlite']['SQLite Library'];
            $html.=             test_result($sqlite).'&nbsp; SQLite '.$version;
        } elseif ($r = function_exists('sqlite_libversion')) {
            $html.=             test_result($r).'&nbsp; SQLite '.sqlite_libversion();
        } else {
            $html.=             test_result(false).'&nbsp; SQLite '.__('Not Supported');
        }
        $html.=                 '<br />';
        // mysql
        if ($r = function_exists('mysql_get_client_info')) {
            $html.=             test_result($r).'&nbsp; MySQL '.mysql_get_client_info();
        } elseif ($r = function_exists('mysqli_get_client_info')) {
            $html.=             test_result($r).'&nbsp; MySQL '.mysqli_get_client_info();
        } else {
            $html.=             test_result(false).'&nbsp; MySQL '.__('Not Supported');
        }
        $html.=             '</td></tr>';
        $html.=             '<tr><td>'.__('GD Library').'</td><td>2.0.0+</td><td>'.test_result(function_exists('gd_info')).'&nbsp; '.(function_exists('gd_info') ? GD_VERSION : __('Not Supported')).'</td></tr>';
        $html.=             '<tr><td>'.__('Iconv Support').'</td><td>2.0.0+</td><td>'.test_result(function_exists('iconv')).'&nbsp; '.(function_exists('iconv') ? ICONV_VERSION : __('Not Supported')).'</td></tr>';
        $html.=             '<tr><td>'.__('Multibyte Support').'</td><td>'.__('Support').'</td><td>'.test_result(extension_loaded('mbstring')).'&nbsp; '.(extension_loaded('mbstring') ? 'mbstring' : __('Not Supported')).'</td></tr>';
        $html.=             '<tr><td>'.__('Remote URL Open').'</td><td>'.__('Support').'</td><td>'.test_result($http_test).'&nbsp; '.($http_test ? array_shift($http_test) : __('Not Supported')).'</td></tr>';
        $html.=         '</tbody>';
        $html.=     '</table>';
        $html.=     '<table class="data-table">';
        $html.=         '<thead><tr><th colspan="3">'.__('Directory Permissions').'</th></tr></thead>';
        $html.=         '<tbody>';
        $html.=             '<tr class="thead"><th>'.__('Path').'</th><th class="w100">'.__('Read').'</th><th class="w100">'.__('Write').'</th></tr>';
        $paths = array(
            '/',
            '/error.log',
            '/config.php',
            '/common/.cache/',
        );
        foreach($paths as $path) {
            $is_read  = is_readable(ABS_PATH.$path);
            $is_write = is_writable(ABS_PATH.$path);
            // 检测文件
            if (!substr_compare($path,'/',strlen($path)-1,1)===false) {
                if (!is_file(ABS_PATH.$path)) {
                    mkdirs(dirname(ABS_PATH.$path));
                    file_put_contents(ABS_PATH.$path, "<?php\necho 'Testing...';");
                    $is_read  = is_readable(ABS_PATH.$path);
                    $is_write = is_writable(ABS_PATH.$path);
                    unlink(ABS_PATH.$path);
                }
            }
            $html.=         '<tr><td>'.ABS_PATH.$path.'</td><td>'.test_result($is_read).'</td><td>'.test_result($is_write).'</td></tr>';
        }
        $html.=         '</tbody>';
        $html.=     '</table>';
        $html.=     system_phpinfo(INFO_CONFIGURATION | INFO_MODULES);
        $html.=     '<p class="buttons">';
        $html.=         '<input type="hidden" name="setup" value="config" />';
        $html.=         '<button type="submit">'.__('Continue').'</button>';
        $html.=         '<button type="button" onclick="LazyCMS.redirect(\''.PHP_FILE.'\')">'.__('Try Again').'</button>';
        $html.=         '<button type="button" rel="phpinfo">'.__('Display PHP Information').'</button>';
        $html.=     '</p>';
        $html.= '</form>';
        error_reporting($error_level);
        install_wrapper($html);
        break;
}