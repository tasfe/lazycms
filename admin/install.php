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
require ADMIN_PATH.'/admin.php';
// 检查系统是否已经安装
if (installed()) redirect(ADMIN);
// 系统需要安装
$config_exist = is_file(COM_PATH.'/config.php');

$setup = isset($_POST['setup']) ? $_POST['setup'] : 'default';

switch($setup) {
    case 'install':
        if (validate_is_post()) {
            validate_check('license',VALIDATE_EMPTY,__('You must accept the license before they can continue the installation!'));

            if (!$config_exist) {
                validate_check('prefix', '/^[\w]+$/i', __('"Table Prefix" can only contain numbers, letters, and underscores.'));
            }

            validate_check('adminname',VALIDATE_EMPTY,__('You must provide a valid username.'));

            validate_check('password1',VALIDATE_EQUAL,__('Your passwords do not match. Please try again.'),'password2');

            validate_check(array(
                array('email',VALIDATE_EMPTY,__('Please enter an e-mail address.')),
                array('email',VALIDATE_IS_EMAIL,__('You must provide an e-mail address.'))
            ));

            if (validate_is_ok()) {
                fcache_flush();
                $writable = true;
                if (!$config_exist) {
                    $dbname = isset($_POST['dbname'])?$_POST['dbname']:null;
                    $uname  = isset($_POST['uname'])?$_POST['uname']:null;
                    $pwd    = isset($_POST['pwd'])?$_POST['pwd']:null;
                    $dbhost = isset($_POST['dbhost'])?$_POST['dbhost']:null;
                    $prefix = isset($_POST['prefix'])?$_POST['prefix']:null;
                    $db = new Mysql();
                    $db->config(array(
                        'name'   => $dbname,
                        'host'   => $dbhost,
                        'user'   => $uname,
                        'pwd'    => $pwd,
                        'prefix' => $prefix,
                    ));
                    // 链接成功
                    if (@$db->connect()) {
                        // 检查数据库版本
                        if (!version_compare($db->version(), '4.1', '>=')) {
                            ajax_error(__('MySQL database version lower than 4.1, please upgrade MySQL!'));
                        }
                        // 数据库没有创建，自动创建数据库
                        if (!$db->is_database($dbname)) {
                            $db->query("CREATE DATABASE `{$dbname}` CHARACTER SET utf8 COLLATE utf8_general_ci;");
                        }
                        @$db->select_db();
                    }
                    // 数据库链接错误
                    if ($db->errno()==1045) {
                        header('X-Dialog-title: '.json_encode(__('Error establishing a database connection')));
                        echo '<div style="padding:0 20px;">';
                        echo '<h2>'.__('Error establishing a database connection').'</h2>';
                        echo '<p>'.sprintf(__('This either means that the username and password information in your <code>common/config.php</code> file is incorrect or we can\'t contact the database server at <code>%s</code>.'),$dbhost).' '.__('This could mean your host\'s database server is down.').'</p>';
                        echo '<ul>';
                        echo    '<li>'.__('Are you sure you have the correct username and password?').'</li>';
                        echo    '<li>'.__('Are you sure that you have typed the correct hostname?').'</li>';
                        echo    '<li>'.__('Are you sure that the database server is running?').'</li>';
                        echo '</ul>';
                        echo '<p>'.__('If you\'re unsure what these terms mean you should probably contact your host.').' '.__('If you still need help you can always visit the <a href="http://lazycms.com/support/">LazyCMS Support Forums</a>.').'</p>';
                        echo '</div>';
                        exit();
                    }
                    // 无法选择数据库
                    elseif ($db->errno()==1049) {
                        header('X-Dialog-title: '.json_encode(__('Can\'t select database')));
                        echo '<div style="padding:0 20px;">';
                        echo '<h2>'.__('Can\'t select database').'</h2>';
                        echo '<p>'.sprintf(__('We were able to connect to the database server (which means your username and password is okay) but not able to select the <code>%s</code> database.'),$dbname).'</p>';
                        echo '<ul>';
                        echo    '<li>'.__('Are you sure it exists?').'</li>';
                        echo    '<li>'.sprintf(__('Does the user <code>%s</code> have permission to use the <code>%s</code> database?'),$uname,$dbname).'</li>';
                        echo    '<li>'.sprintf(__('On some systems the name of your database is prefixed with your username, so it would be like <code>%s_%s</code>. Could that be the problem?'),$uname,$dbname).'</li>';
                        echo '</ul>';
                        echo '<p>'.__('If you don\'t know how to set up a database you should <strong>contact your host</strong>.').' '.__('If all else fails you may find help at the <a href="http://lazycms.com/support/">LazyCMS Support Forums</a>.').'</p>';
                        echo '</div>';
                        exit();
                    }
                    // 检查 config.sample.php 文件是否存在
                    if (!is_file(COM_PATH.'/config.sample.php')) {
                        ajax_error(__('Sorry, I need a common/config.sample.php file to work from. Please re-upload this file from your LazyCMS installation.'));
                    }
                    // 替换变量
                    $configs = file(COM_PATH.'/config.sample.php');
                    foreach ($configs as $num => $line) {
                        switch(substr($line,0,16)) {
                            case "define('DB_NAME'":
                                $configs[$num] = str_replace("database_name_here", $dbname, $line);
                                break;
                            case "define('DB_HOST'":
                                $configs[$num] = str_replace("localhost", $dbhost, $line);
                                break;
                            case "define('DB_USER'":
                                $configs[$num] = str_replace("username_here", $uname, $line);
                                break;
                            case "define('DB_PWD',":
                                $configs[$num] = str_replace("password_here", $pwd, $line);
                                break;
                            case "define('DB_PREFI":
                                $configs[$num] = str_replace("lazy_", $prefix, $line);
                                break;
                        }
                    }
                    // 检查是否具有写入权限
                    if ($writable = is_writable(COM_PATH.'/')) {
                        $config = implode('', $configs);
                        file_put_contents(COM_PATH.'/config.php', $config);
                    }
                    // 定义数据库链接
                    define('DB_NAME',$dbname);
                    define('DB_HOST',$dbhost);
                    define('DB_USER',$uname);
                    define('DB_PWD',$pwd);
                    define('DB_PREFIX',$prefix);
                } else {
                    $db = get_conn();
                }
                $query = install_schema();
                // 创建数据表
                $db->delta($query);
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
                        'pass' => md5($password.$admin['authcode']),
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
            $html.=             '<tr><th class="w150"><label for="dbname">'.__('Database Name').'</label></th><td><input class="text" type="text" name="dbname" id="dbname" value="test" /></td><td>'.__('The name of the database you want to run LazyCMS in.').'</td></tr>';
            $html.=             '<tr><th><label for="uname">'.__('UserName').'</label></th><td><input class="text" type="text" name="uname" id="uname" value="username" /></td><td>'.__('Your MySQL username').'</td></tr>';
            $html.=             '<tr><th><label for="pwd">'.__('Password').'</label></th><td><input class="text" type="text" name="pwd" id="pwd" value="password" /></td><td>'.__('...and MySQL password.').'</td></tr>';
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
        require COM_PATH.'/system/httplib.php';
        $http_test = httplib_test();
        $html.=     '<table class="data-table">';
        $html.=         '<thead><tr><th colspan="3">'.__('Required Settings').'</th></tr></thead>';
        $html.=         '<tbody>';
        $html.=             '<tr class="thead"><th>'.__('Test').'</th><th class="w100">'.__('Require').'</th><th class="w150">'.__('Current').'</th></tr>';
        $html.=             '<tr><td>'.__('PHP Version').'</td><td>4.3.3+</td><td>'.test_result(version_compare(PHP_VERSION,'4.3.3','>')).'&nbsp; '.PHP_VERSION.'</td></tr>';
        $html.=             '<tr><td>'.__('MySQL Client Version').'</td><td>4.1.0+</td><td>'.test_result(function_exists('mysql_get_client_info')).'&nbsp; '.(function_exists('mysql_get_client_info') ? mysql_get_client_info() : __('Not Supported')).'</td></tr>';
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
            '/common/.cache/',
            '/common/config.php',
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