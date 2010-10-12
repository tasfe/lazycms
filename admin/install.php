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
 * | Copyright (C) 2007-2008 LazyCMS.com All rights reserved.                  |
 * +---------------------------------------------------------------------------+
 * | LazyCMS is free software. This version use Apache License 2.0             |
 * | See LICENSE.txt for copyright notices and details.                        |
 * +---------------------------------------------------------------------------+
 */
// 定义管理后台路径
defined('ADMIN_PATH') or define('ADMIN_PATH',dirname(__FILE__));
// 禁止重复跳转
define('NO_REDIRECT',true);
// 加载公共文件
require ADMIN_PATH.'/admin.php';
// 检查 config.php 是否存在
$config_exist = file_exists_case(COM_PATH.'/config.php');


if (validate_is_post()) {
    validate_check('license',VALIDATE_EMPTY,__('You must accept the license before they can continue the installation!'));

    validate_check('prefix', '/^[\w]+$/i', __('"Table Prefix" can only contain numbers, letters, and underscores.'));

    if (validate_is_ok()) {
        fcache_flush();
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
                    admin_error(__('MySQL database version lower than 4.1, please upgrade MySQL!'));
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
            if (!file_exists_case(COM_PATH.'/config.sample.php')) {
                admin_error(__('Sorry, I need a common/config.sample.php file to work from. Please re-upload this file from your LazyCMS installation.'));
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
            if (is_writable(COM_PATH.'/')) {
                $config = implode('', $configs);
                file_put_contents(COM_PATH.'/config.php', $config);
            }
        } else {
            $db = get_conn();
        }
        // 创建数据表

        // 安装默认设置

        // 保存用户填写的信息
        
        admin_success(__('All right sparky! You\'ve made it through of the installation. If you are ready, time now to&hellip;'), "LazyCMS.redirect('".ADMIN_ROOT."');");

    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php _e('LazyCMS Setup');?></title>
<?php admin_css('css/install'); admin_script('js/install');?>
<script type="text/javascript">
    LazyCMS.L10n.common = $.extend(LazyCMS.L10n.common,{
        'Strength indicator': "<?php _e('Strength indicator');?>",
        'Very weak': "<?php _e('Very weak');?>",
        'Weak': "<?php _e('Weak');?>",
        'Medium': "<?php _e('Medium');?>",
        'Strong': "<?php _e('Strong');?>",
        'Mismatch': "<?php _e('Mismatch');?>",
        'Rock it!': "<?php _e('Rock it!');?>"
    });
    LazyCMS.addLoadEvent(install_init);
</script>
</head>

<body>
<h1 id="logo"><?php _e('LazyCMS Setup');?></h1>
<form action="<?php echo PHP_FILE;?>" method="post" name="setup" id="setup">
    <?php if (!$config_exist) :?>
    <p><?php _e('Welcome to LazyCMS! Below you should enter your database connection details. If you\'re not sure about these, contact your host.');?></p>
    <table class="data-table">
        <thead><tr><th colspan="3"><?php _e('Database Configuration');?></th></tr></thead>
        <tbody>
            <tr><th class="w100"><label for="dbname"><?php _e('Database Name');?></label></th><td><input class="text" type="text" name="dbname" id="dbname" value="test" /></td><td><?php _e('The name of the database you want to run LazyCMS in.');?></td></tr>
            <tr><th><label for="uname"><?php _e('User Name');?></label></th><td><input class="text" type="text" name="uname" id="uname" value="username" /></td><td><?php _e('Your MySQL username');?></td></tr>
            <tr><th><label for="pwd"><?php _e('Password');?></label></th><td><input class="text" type="text" name="pwd" id="pwd" value="password" /></td><td><?php _e('...and MySQL password.');?></td></tr>
            <tr><th><label for="dbhost"><?php _e('Database Host');?></label></th><td><input class="text" type="text" name="dbhost" id="dbhost" value="localhost" /></td><td><?php _e('You should be able to get this info from your web host, if <code>localhost</code> does not work.');?></td></tr>
            <tr><th><label for="prefix"><?php _e('Table prefix');?></label></th><td><input class="text" type="text" name="prefix" id="prefix" value="lazy_" /></td><td><?php _e('If you want to run multiple LazyCMS installations in a single database, change this.');?></td></tr>
        </tbody>
    </table>
    <?php endif;?>
    <p><?php _e('Please provide the following information. Don’t worry, you can always change these settings later.');?></p>
    <table class="data-table">
        <thead><tr><th colspan="3"><?php _e('Information needed');?></th></tr></thead>
        <tbody>
            <tr><th class="w150"><label for="sitetitle"><?php _e('Site Title');?></label></th><td><input class="text" type="text" name="sitetitle" id="sitetitle" /></td><td>&nbsp;</td></tr>
            <tr><th><label for="adminname"><?php _e('UserName');?></label></th><td><input class="text" type="text" name="adminname" id="adminname" /></td><td><?php _e('Usernames can have only alphanumeric characters, spaces, underscores, hyphens, periods and the @ symbol.');?></td></tr>
            <tr>
                <th><label for="passowrd1"><?php _e('Password, twice');?></label><p><?php _e('A password will be automatically generated for you if you leave this blank.');?></p></th>
                <td><input class="text" type="password" name="passowrd" id="passowrd1" /><br /><input class="text" type="password" name="passowrd2" id="passowrd2" /><br /><div id="pass-strength-result"><?php _e('Strength indicator');?></div></td>
                <td><?php _e('Hint: The password should be at least seven characters long. To make it stronger, use upper and lower case letters, numbers and symbols like ! " ? $ % ^ & ).');?></td>
            </tr>
            <tr><th><label for="email"><?php _e('Your E-mail');?></label></th><td><input class="text" type="text" name="email" id="email" /></td><td><?php _e('Double-check your email address before continuing.');?></td></tr>
        </tbody>
    </table>
    <p>
        <input type="checkbox" name="license" id="license" value="agree" />
        <label for="license"><?php _e('I accept the <a href="../LICENSE.txt" target="_blank">License</a>');?></label> &nbsp;
        <button type="submit"><?php _e('Install LazyCMS');?></button>
    </p>
</form>
<script type="text/javascript">if(typeof LazyCMS!='undefined' && typeof LazyCMS.init=='function') LazyCMS.init();</script>
</body>
</html>