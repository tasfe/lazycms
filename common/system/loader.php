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
defined('COM_PATH') or die('Restricted access!');
/**
 * 后台静态文件加载类
 * 
 * @author  Lukin <my@lukin.cn>
 * @version $Id$
 */
class LazyLoader {
    var $version  = 0;
    var $language = null;
    var $_groups  = array();
    /**
     * 设置语言
     *
     * @param string $language
     * @return void
     */
    function set_language($language = ''){
        if ($language) {
            $this->language = $language;
        }
	}

    /**
     * CSS样式依赖关系
     *
     * @return array
     */
    function styles() {
        $styles = array(
            'reset'  => array(COM_PATH.'/css/reset.css'),
            'icons'  => array(COM_PATH.'/css/icons.css'),
            'common' => array(COM_PATH.'/css/common.css'),
            'style'  => array(ADMIN_PATH.'/css/style.css',array('reset','icons','common')),
            'admin'  => array(ADMIN_PATH.'/css/admin.css',array('style')),
            'login'  => array(ADMIN_PATH.'/css/login.css',array('style')),
            'install' => array(ADMIN_PATH.'/css/install.css',array('style')),

            'user'   => array(ADMIN_PATH.'/css/user.css'),
            'model'  => array(ADMIN_PATH.'/css/model.css'),
            'post'   => array(ADMIN_PATH.'/css/post.css',array('xheditor.plugins')),
            'publish' => array(ADMIN_PATH.'/css/publish.css'),
            'categories' => array(ADMIN_PATH.'/css/categories.css'),
            'xheditor.plugins' => array(COM_PATH.'/css/xheditor.plugins.css'),
        );
        // 追加语言相关的CSS
        if ($this->language) {
            $styles[$this->language] = array(sprintf('%s/css/%s.css',ADMIN_PATH,$this->language));
        }
        return $styles;
    }
    /**
     * Js依赖关系
     *
     * @return array
     */
    function scripts(){
        $scripts = array(
            'jquery'        => array(COM_PATH.'/js/jquery.js'),
            'jquery.extend' => array(COM_PATH.'/js/jquery.extend.js'),
            'lazycms'       => array(COM_PATH.'/js/lazycms.js'),
            'common'        => array(ADMIN_PATH.'/js/common.js',array('jquery','jquery.extend','lazycms')),
            'login'         => array(ADMIN_PATH.'/js/login.js'),
            'install'       => array(ADMIN_PATH.'/js/install.js'),
            
            'user'          => array(ADMIN_PATH.'/js/user.js'),
            'model'         => array(ADMIN_PATH.'/js/model.js'),
            'categories'    => array(ADMIN_PATH.'/js/categories.js'),
            'post'          => array(ADMIN_PATH.'/js/post.js'),
            'options'       => array(ADMIN_PATH.'/js/options.js'),
            'publish'       => array(ADMIN_PATH.'/js/publish.js'),
            'message'       => array(ADMIN_PATH.'/js/message.js'),
            'xheditor'      => array(COM_PATH.'/editor/xheditor.js',array('xheditor.plugins')),
            'xheditor.plugins' => array(COM_PATH.'/js/xheditor.plugins.js'),
        );
        // 追加语言相关的JS
        if ($this->language) {
            $scripts[$this->language] = array(sprintf('%s/js/%s.js',ADMIN_PATH,$this->language));
        }
        return $scripts;
    }
    /**
     * 取得版本号
     *
     * @param string $files
     * @return int
     */
    function get_version($file){
        $version = 0;
        $files   = $this->get_dependence_files($file); unset($files['LazyCMS.L10N']);
        foreach ($files as $srcs) {
        	foreach ($srcs as $src) {
        		$version = max($version,filemtime($src));
        	}
        }
        if ($version) {
        	$version = date('YmdHis',$version);
        } else {
            $version = LAZY_VERSION;
        }
        return $version;
    }
    /**
     * 取得依赖的文件
     *
     * @param string $group
     * @return array
     */
    function get_dependence_files($file){
        $result = array(); $files = array(); $jsL10n = array();
        if (is_array($file) || strpos($file,',')!==false) {
        	$loads = !is_array($file)?explode(',',$file):$file;
        	foreach ($loads as $file) {
        	    $dependence_file = $this->_get_dependence_files($file,$jsL10n);
        	    if (!empty($dependence_file)) {
        	    	$files[$file] = $dependence_file;
        	    }
        	}
        } else {
            $files[$file] = $this->_get_dependence_files($file,$jsL10n);
        }

        $is_exist = array();
        foreach ($files as $group => $src) {
            $arr_src = array();
            foreach ($src as $k=>$v) {
                if (empty($v)) continue;
                if (!isset($is_exist[$k])) {
                	$arr_src[]    = $k;
                	$is_exist[$k] = 1;
                }
            }
            $result[$group] = $arr_src;
        }
        $result['LazyCMS.L10N'] = $jsL10n;
        return $result;
    }
    function _get_dependence_files($group,& $jsL10n){
        $files = array();
        if (isset($this->_groups[$group])) {
            $rule = $this->_groups[$group];
        	// 存在依赖
        	if (isset($rule[1]) && !empty($rule[1])) {
        	    foreach ($rule[1] as $tode_file) {
        	    	$dependence_files = $this->_get_dependence_files($tode_file,$jsL10n);
        	    	foreach ($dependence_files as $file=>$v) {
        	    	    if (!isset($files[$file])) {
        	    	    	$files[$file] = 1;
        	    	    }
        	        }
        	    }
        	}
        	$file = $rule[0];
        	if (!isset($files[$file]) && is_file($file)) {
                // 读取相关语言文字
                if (isset($rule[2]) && !empty($rule[2])) {
                    $jsL10n[$group] = $rule[2];
                }
                $files[$file] = 1;
            }
        }
        return $files;
    }
    function loads($files){
        return $this->get_dependence_files($files);
    }
    function get_files() {
        return $this->_groups;
    }
}

class StylesLoader extends LazyLoader {

    function __construct($language = ''){
        $this->set_language($language);
		$this->_groups = $this->styles();
	}

    function StylesLoader() {
        $args = func_get_args();
		call_user_func_array( array(&$this, '__construct'), $args );
    }
}

class ScriptsLoader extends LazyLoader {

    function __construct($language = ''){
        $this->set_language($language);
		$this->_groups = $this->scripts();
	}

    function ScriptsLoader() {
        $args = func_get_args();
		call_user_func_array( array(&$this, '__construct'), $args );
    }
}
