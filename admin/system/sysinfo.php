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
    System::tabs(
        l('Settings').':settings.php;'.
        l('System info').':sysinfo.php;'.
        l('System config').':sysinfo.php?action=config;'.
        l('Directory').':sysinfo.php?action=directory;'.
        l('PHP Settings').':sysinfo.php?action=phpinfo'
    );
}
// *** *** www.LazyCMS.net *** *** //
function lazy_main(){
    $db = get_conn();
    $gdInfo = function_exists('gd_info') ? gd_info() : array('GD Version'=>'none');
    
    System::header(l('System info'));
    
    /* System settings */
    echo '<fieldset><legend><a class="collapsed" rel=".table">'.l('System info').'</a></legend>';
    echo '<table class="table" cellspacing="0">';
    echo '<tbody>';
    echo '<tr><td class="width-30">'.l('Server OS').'</td><td>'.php_uname().'</td></tr>';
    echo '<tr><td>'.l('GD version').'</td><td>'.$gdInfo['GD Version'].'</td></tr>';
    echo '<tr><td>'.l('PHP version').'</td><td>'.PHP_VERSION.'</td></tr>';
    echo '<tr><td>'.l('PHP SAPI name').'</td><td>'.php_sapi_name().'</td></tr>';
    echo '<tr><td>'.l('Database version').'</td><td>'.$db->config('scheme').' '.$db->version().'</td></tr>';
    echo '<tr><td>'.l('System version').'</td><td>'.LAZY_VERSION.'</td></tr>';
    echo '<tr><td>'.l('WebServer').'</td><td>'.$_SERVER['SERVER_SOFTWARE'].'</td></tr>';
    echo '<tr><td>'.l('User agent').'</td><td>'.$_SERVER['HTTP_USER_AGENT'].'</td></tr>';
    echo '</tbody></table></fieldset>';
    
    /* PHP settings */
    echo '<fieldset><legend><a class="collapsed" rel=".table">'.l('PHP Settings').'</a></legend>';
    echo '<table class="table" cellspacing="0"><tbody>';
    echo '<tr><td class="width-30">'.l('PHP safe mode').'</td><td>'.get_php_setting('safe_mode').'</td></tr>';
    echo '<tr><td>'.l('PHP display errors').'</td><td>'.get_php_setting('display_errors').'</td></tr>';
    echo '<tr><td>'.l('PHP file uploads').'</td><td>'.get_php_setting('file_uploads').'</td></tr>';
    echo '<tr><td>'.l('PHP magic quotes gpc').'</td><td>'.get_php_setting('magic_quotes_gpc').'</td></tr>';
    echo '<tr><td>'.l('PHP zlib').'</td><td>'.isok(extension_loaded('zlib')).'</td></tr>';
    echo '<tr><td>'.l('PHP iconv').'</td><td>'.isok(function_exists('iconv')).'</td></tr>';
    echo '<tr><td>'.l('PHP allow url fopen').'</td><td>'.isok((function_exists('fsockopen') || function_exists('curl_exec'))).'</td></tr>';
    echo '<tr><td>'.l('PHP mbstring').'</td><td>'.isok(extension_loaded('mbstring')).'</td></tr>';
    echo '<tr><td>'.l('PHP open base dir').'</td><td>'.(($ob = ini_get('open_basedir')) ? $ob : 'none').'</td></tr>';
    echo '<tr><td>'.l('PHP disable functions').'</td><td>'.(($df = ini_get('disable_functions')) ? $df : 'none').'</td></tr>';
    echo '<tr><td>'.l('PHP upload max file size').'</td><td>'.get_cfg_var('upload_max_filesize').'</td></tr>';
    echo '<tr><td>'.l('PHP post max size').'</td><td>'.get_cfg_var('post_max_size').'</td></tr>';
    echo '</tbody></table></fieldset>';
}
// *** *** www.LazyCMS.net *** *** //
function lazy_config(){
    System::header(l('Settings'));
    echo '<fieldset><legend>'.l('Settings').'</legend>';
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
    System::header(l('Directory'));
    $paths = array(
        '/',
        '/common/js/',
        '/common/dicts/',
        '/common/modules/',
        '/common/language/',
        '/common/config.php',
        '/common/images/icons.css',
    );
    echo '<fieldset><legend>'.l('Directory').'</legend>';
    echo '<table class="table" cellspacing="0"><tbody>';
    echo '<tr><th>'.l('Directory path').'</th><th>'.l('Directory read').'</th><th>'.l('Directory write').'</th></tr>';
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
    
    System::header(l('PHP Settings'));
    
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