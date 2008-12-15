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
require '../../global.php';
/**
 * 系统信息
 * 
 */
// *** *** www.LazyCMS.net *** *** //
function lazy_before(){
    System::purview();
    System::tabs(
        t('settings').':settings.php;'.
        t('sysinfo').':sysinfo.php;'.
        t('sysinfo/config').':sysinfo.php?action=config;'.
        t('sysinfo/directory').':sysinfo.php?action=directory;'.
        t('sysinfo/phpinfo').':sysinfo.php?action=phpinfo'
    );
}
// *** *** www.LazyCMS.net *** *** //
function lazy_main(){
    $db = get_conn();
    $gdInfo = function_exists('gd_info') ? gd_info() : array('GD Version'=>'none');
    
    System::header(t('sysinfo'));
    
    /* System settings */
    echo '<fieldset><legend><a rel=".table"><img class="a2 os" src="../system/images/white.gif" />'.t('sysinfo').'</a></legend>';
    echo '<table class="table" cellspacing="0">';
    echo '<tbody>';
    echo '<tr><td class="w4">'.t('sysinfo/server_OS').'</td><td>'.php_uname().'</td></tr>';
    echo '<tr><td>'.t('sysinfo/GD_version').'</td><td>'.$gdInfo['GD Version'].'</td></tr>';
    echo '<tr><td>'.t('sysinfo/PHP_version').'</td><td>'.PHP_VERSION.'</td></tr>';
    echo '<tr><td>'.t('sysinfo/PHP_SAPI_name').'</td><td>'.php_sapi_name().'</td></tr>';
    echo '<tr><td>'.t('sysinfo/DB_version').'</td><td>'.$db->config('scheme').' '.$db->version().'</td></tr>';
    echo '<tr><td>'.t('sysinfo/version').'</td><td>'.LAZY_VERSION.'</td></tr>';
    echo '<tr><td>'.t('sysinfo/software').'</td><td>'.$_SERVER['SERVER_SOFTWARE'].'</td></tr>';
    echo '<tr><td>'.t('sysinfo/useragent').'</td><td>'.$_SERVER['HTTP_USER_AGENT'].'</td></tr>';
    echo '</tbody></table></fieldset>';
    
    /* PHP settings */
    echo '<fieldset><legend><a rel=".table"><img class="a2 os" src="../system/images/white.gif" />'.t('sysinfo/phpinfo').'</a></legend>';
    echo '<table class="table" cellspacing="0"><tbody>';
    echo '<tr><td class="w4">'.t('sysinfo/safe_mode').'</td><td>'.get_php_setting('safe_mode').'</td></tr>';
    echo '<tr><td>'.t('sysinfo/display_errors').'</td><td>'.get_php_setting('display_errors').'</td></tr>';
    echo '<tr><td>'.t('sysinfo/file_uploads').'</td><td>'.get_php_setting('file_uploads').'</td></tr>';
    echo '<tr><td>'.t('sysinfo/magic_quotes_gpc').'</td><td>'.get_php_setting('magic_quotes_gpc').'</td></tr>';
    echo '<tr><td>'.t('sysinfo/zlib').'</td><td>'.isok(extension_loaded('zlib')).'</td></tr>';
    echo '<tr><td>'.t('sysinfo/iconv').'</td><td>'.isok(function_exists('iconv')).'</td></tr>';
    echo '<tr><td>'.t('sysinfo/allow_url_fopen').'</td><td>'.isok((function_exists('fsockopen') || function_exists('curl_exec'))).'</td></tr>';
    echo '<tr><td>'.t('sysinfo/mbstring').'</td><td>'.isok(extension_loaded('mbstring')).'</td></tr>';
    echo '<tr><td>'.t('sysinfo/open_base_dir').'</td><td>'.(($ob = ini_get('open_basedir')) ? $ob : 'none').'</td></tr>';
    echo '<tr><td>'.t('sysinfo/disable_functions').'</td><td>'.(($df = ini_get('disable_functions')) ? $df : 'none').'</td></tr>';
    echo '<tr><td>'.t('sysinfo/upload_max_filesize').'</td><td>'.get_cfg_var('upload_max_filesize').'</td></tr>';
    echo '<tr><td>'.t('sysinfo/post_max_size').'</td><td>'.get_cfg_var('post_max_size').'</td></tr>';
    echo '</tbody></table></fieldset>';
}
// *** *** www.LazyCMS.net *** *** //
function lazy_config(){
    System::header(t('Settings'));
    echo '<fieldset><legend>'.t('settings').'</legend>';
    echo '<table class="table" cellspacing="0"><tbody>';
    $fp = file(COM_PATH.'/config.php');
    $config = null;
    $isFlag = false;
    foreach ($fp as $k => $v) {
        $v = rtrim(trim($v),','); if ($v=='') { continue; }
        if (preg_match('/\?>/',$v) || preg_match('/\)\;/',$v)) {
            $isFlag = false;
        }
        if ($isFlag) {
            if (preg_match("/(.+)'(.+):\/\/(.[^:]+)(:(.[^@]+)?)?@([a-z0-9\-\.]+)(:(\d+))?\/(\w+)\/(\w+)'/",$v,$r)) {
                $r[5] = empty($r[5]) ? null : ':******';
                $v = sprintf("%s'%s://%s%s@%s%s/%s/%s'",$r[1],$r[2],$r[3],$r[5],$r[6],$r[7],$r[9],$r[10]);
                unset($r);
            }
            if (substr($v,0,2)=='/*') {
                $config.= '<tr><th colspan="2">'.$v.'</th></tr>';
            } else {
                $v = str_replace('=>','</td><td>',$v);
                $config.= '<tr><td class="width-30">'.$v.'</td></tr>';
            }
        }
        if (preg_match('/^return array\(/i',$v)) {
            $isFlag = true;
        }
    }
    echo $config;
    echo '</tbody></table></fieldset>';
}
// *** *** www.LazyCMS.net *** *** //
function lazy_directory(){
    System::header(t('sysinfo/directory'));
    $paths = array(
        '/',
        '/common/js/',
        '/common/dicts/',
        '/common/modules/',
        '/common/language/',
        '/common/config.php',
        '/common/images/icons.css',
    );
    echo '<fieldset><legend>'.t('sysinfo/directory').'</legend>';
    echo '<table class="table" cellspacing="0"><tbody>';
    echo '<tr><th>'.t('sysinfo/directory/path').'</th><th>'.t('sysinfo/directory/read').'</th><th>'.t('sysinfo/directory/write').'</th></tr>';
    foreach ($paths as $path) {
        echo '<tr><td>'.$path.'</td><td>'.isok(is_readable(LAZY_PATH.$path)).'</td><td>'.isok(is_writable(LAZY_PATH.$path)).'</td></tr>';
    }
    echo '</tbody></table></fieldset>';
}
// *** *** www.LazyCMS.net *** *** //
function lazy_PHPInfo(){
    ob_start();
    phpinfo(INFO_GENERAL | INFO_CONFIGURATION | INFO_MODULES);
    $phpinfo = ob_get_contents();
    ob_end_clean();
    
    System::header(t('sysinfo/phpinfo'));
    
    preg_match_all('#<body[^>]*>(.*)</body>#siU', $phpinfo, $output);
    $output = preg_replace('#<table#', '<table class="table" align="center"', $output[1][0]);
    $output = preg_replace('#(\w),(\w)#', '\1, \2', $output);
    $output = preg_replace('#border="0" cellpadding="3" width="600"#', 'border="0" cellspacing="1" cellpadding="4" width="95%"', $output);
    $output = preg_replace('#<hr />#', '', $output);
    $output = str_replace('<div class="center">', '', $output);
    $output = str_replace('</div>', '', $output);
    echo $output;
}
// *** *** www.LazyCMS.net *** *** //
function lazy_after(){
    System::footer();
}