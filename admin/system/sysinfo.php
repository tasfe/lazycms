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
 */
require '../../global.php';
/**
 * 系统信息
 *
 * @copyright   Copyright (c) 2007-2008 lazycms.net All rights reserved.
 * @author      Lukin <mylukin@gmail.com>
 * @date        2008-6-18
 */

// lazy_before *** *** www.LazyCMS.net *** ***
function lazy_before(){
    check_login('system');
    // 设置公共菜单
    G('TABS',
        L('sysinfo/@title').':sysinfo.php;'
        .L('sysinfo/settings').':sysinfo.php?action=settings;'
        .L('sysinfo/directory/@title').':sysinfo.php?action=directory;'
        .L('sysinfo/phpinfo').':sysinfo.php?action=phpinfo'
    );
}

// lazy_def *** *** www.LazyCMS.net *** ***
function lazy_Default(){
    $db = get_conn();
    $gdInfo = function_exists('gd_info') ? gd_info() : array('GD Version'=>'none');

    /* System settings */
    $hl = '<fieldset><legend><a class="collapsed" rel=".table">'.L('sysinfo/@title').'</a></legend>';
    $hl.= '<table class="table">';
    $hl.= '<tbody>';
    $hl.= '<tr><td class="width-30">'.L('sysinfo/server_os').'</td><td>'.php_uname().'</td></tr>';
    $hl.= '<tr><td>'.L('sysinfo/gdversion').'</td><td>'.$gdInfo['GD Version'].'</td></tr>';
    $hl.= '<tr><td>'.L('sysinfo/phpversion').'</td><td>'.PHP_VERSION.'</td></tr>';
    $hl.= '<tr><td>'.L('sysinfo/php_sapi_name').'</td><td>'.php_sapi_name().'</td></tr>';
    $hl.= '<tr><td>'.L('sysinfo/dbversion').'</td><td>'.$db->config('scheme').' '.$db->version().'</td></tr>';
    $hl.= '<tr><td>'.L('sysinfo/system_version').'</td><td>'.LAZY_VERSION.'</td></tr>';
    $hl.= '<tr><td>'.L('sysinfo/software').'</td><td>'.$_SERVER['SERVER_SOFTWARE'].'</td></tr>';
    $hl.= '<tr><td>'.L('sysinfo/useragent').'</td><td>'.$_SERVER['HTTP_USER_AGENT'].'</td></tr>';
    $hl.= '</tbody></table></fieldset>';
    
    /* PHP settings */
    $hl.= '<fieldset><legend><a class="collapsed" rel=".table">'.L('sysinfo/phpsettings/@title').'</a></legend>';
    $hl.= '<table class="table"><tbody>';
    $hl.= '<tr><td class="width-30">'.L('sysinfo/phpsettings/safe_mode').'</td><td>'.get_php_setting('safe_mode').'</td></tr>';
    $hl.= '<tr><td>'.L('sysinfo/phpsettings/display_errors').'</td><td>'.get_php_setting('display_errors').'</td></tr>';
    $hl.= '<tr><td>'.L('sysinfo/phpsettings/file_uploads').'</td><td>'.get_php_setting('file_uploads').'</td></tr>';
    $hl.= '<tr><td>'.L('sysinfo/phpsettings/magic_quotes_gpc').'</td><td>'.get_php_setting('magic_quotes_gpc').'</td></tr>';
    $hl.= '<tr><td>'.L('sysinfo/phpsettings/xml').'</td><td>'.isok(extension_loaded('xml')).'</td></tr>';
    $hl.= '<tr><td>'.L('sysinfo/phpsettings/zlib').'</td><td>'.isok(extension_loaded('zlib')).'</td></tr>';
    $hl.= '<tr><td>'.L('sysinfo/phpsettings/iconv').'</td><td>'.isok(function_exists('iconv')).'</td></tr>';
    $hl.= '<tr><td>'.L('sysinfo/phpsettings/allow_url_fopen').'</td><td>'.isok((function_exists('fsockopen') || function_exists('curl_exec'))).'</td></tr>';
    $hl.= '<tr><td>'.L('sysinfo/phpsettings/mbstring').'</td><td>'.isok(extension_loaded('mbstring')).'</td></tr>';
    $hl.= '<tr><td>'.L('sysinfo/phpsettings/open_basedir').'</td><td>'.(($ob = ini_get('open_basedir')) ? $ob : 'none').'</td></tr>';
    $hl.= '<tr><td>'.L('sysinfo/phpsettings/disable_functions').'</td><td>'.(($df = ini_get('disable_functions')) ? $df : 'none').'</td></tr>';
    $hl.= '<tr><td>'.L('sysinfo/phpsettings/upload_max_filesize').'</td><td>'.get_cfg_var('upload_max_filesize').'</td></tr>';
    $hl.= '<tr><td>'.L('sysinfo/phpsettings/post_max_size').'</td><td>'.get_cfg_var('post_max_size').'</td></tr>';
    $hl.= '</tbody></table></fieldset>';

    /* Output html */
    print_x(L('sysinfo/@title'),$hl);
}

// lazy_Settings *** *** www.LazyCMS.net *** ***
function lazy_Settings(){
    $hl = '<fieldset><legend>'.L('sysinfo/settings').'</legend>';
    $hl.= '<table class="table"><tbody>';
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
    $hl.= $config;
    $hl.= '</tbody></table></fieldset>';
    print_x(L('sysinfo/settings'),$hl);
}

// lazy_Directory *** *** www.LazyCMS.net *** ***
function lazy_Directory(){
    $paths = array(
        '/',
        '/common/config.php',
        '/common/data/dict/',
        '/common/data/module.php',
        '/common/images/icons.css',
    );
    $hl = '<fieldset><legend>'.L('sysinfo/directory/@title').'</legend>';
    $hl.= '<table class="table"><tbody>';
    $hl.= '<tr><th>'.L('sysinfo/directory/path').'</th><th>'.L('sysinfo/directory/read').'</th><th>'.L('sysinfo/directory/write').'</th></tr>';
    foreach ($paths as $path) {
        $hl.= '<tr><td>'.$path.'</td><td>'.isok(is_readable(LAZY_PATH.$path)).'</td><td>'.isok(is_writable(LAZY_PATH.$path)).'</td></tr>';
    }
    $hl.= '</tbody></table></fieldset>';
    print_x(L('sysinfo/directory/@title'),$hl);
}

// lazy_phpinfo *** *** www.LazyCMS.net *** ***
function lazy_PHPInfo(){
    ob_start();
    phpinfo(INFO_GENERAL | INFO_CONFIGURATION | INFO_MODULES);
    $phpinfo = ob_get_contents();
    ob_end_clean();

    preg_match_all('#<body[^>]*>(.*)</body>#siU', $phpinfo, $output);
    $output = preg_replace('#<table#', '<table class="table" align="center"', $output[1][0]);
    $output = preg_replace('#(\w),(\w)#', '\1, \2', $output);
    $output = preg_replace('#border="0" cellpadding="3" width="600"#', 'border="0" cellspacing="1" cellpadding="4" width="95%"', $output);
    $output = preg_replace('#<hr />#', '', $output);
    $output = str_replace('<div class="center">', '', $output);
    $output = str_replace('</div>', '', $output);
    print_x(L('sysinfo/phpinfo'),$output);
}