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
defined('COM_PATH') or die('Restricted access!');

// 默认的缓存目录
defined('CACHE_PATH') or define('CACHE_PATH',COM_PATH.'/.cache');
// 默认过期时间
define('DATACACHE_EXPIRE', 31536000);
/**
 * 数据缓存类
 *
 * @author  Lukin <my@lukin.cn>
 * @version $Id$
 */
class FCache {
    
    function __construct(){ }

    function FCache() {
        register_shutdown_function( array(&$this, '__destruct') );

        $args = func_get_args();
        call_user_func_array( array(&$this, '__construct'), $args );
    }
    /**
     * 取得缓存路径
     *
     * @param string $key
     * @return unknown
     */
    function file($key) {
        $sha1_key = sha1($key); $folders = array();
        for ($i=1;$i<=3;$i++) $folders[] = substr($sha1_key,0,$i);
        $file   = sprintf('%s/%s/%s.cache',CACHE_PATH,implode('/',$folders),$sha1_key);
        $floder = dirname($file); mkdirs($floder);
        return $file;
    }
    /**
     * 添加一个值，如果已经存在，则覆盖
     *
     * @param string $key
     * @param mixed $data
     * @param int $expire 单位秒
     * @return bool
     */
    function set($key, $data, $expire=0) {
        $result      = false;
        $hash_file   = $this->file($key);
        $error_level = error_reporting(0);
        $fp = fopen($hash_file, "wb");
    	if ($fp) {
            // 延长过期时间，防止高并发情况出现
            touch($hash_file, time() + 30);

            flock($fp, LOCK_EX);
    	    $mqr = get_magic_quotes_runtime();
            if ($mqr) set_magic_quotes_runtime(0);
            if ($data === null) $data = new LC_Null();
            // 判断是否需要序列化
            if (!is_scalar($data)) {
                $data = serialize($data);
            }
            fwrite($fp, $data);
            if ($mqr) set_magic_quotes_runtime($mqr);
            flock($fp, LOCK_UN);
            fclose($fp);
            // 默认永不过期
            $expire = $expire===0 ? DATACACHE_EXPIRE : $expire;
            // 写入过期时间
            touch($hash_file, time() + abs($expire), time());
            
            $result = true;
    	}
        error_reporting($error_level);
        return $result;
    }
    /**
     * 取得一个缓存结果
     *
     * @param array|string $keys
     * @return array|string
     */
    function get($key) {
        $data        = null;
        $hash_file   = $this->file($key);
        $error_level = error_reporting(0);
        if (is_file($hash_file)) {
        	$fp = fopen($hash_file, "rb");
        	flock($fp, LOCK_SH);
        	if ($fp) {
        	    clearstatcache();
                $length = filesize($hash_file);
                $mqr = get_magic_quotes_runtime();
                if ($mqr) set_magic_quotes_runtime(0);
                if ($length) {
                    $data = fread($fp, $length);
                } else {
                    $data = '';
                }
                if ($mqr) set_magic_quotes_runtime($mqr);
                flock($fp, LOCK_UN);
                fclose($fp);

                if (is_serialized($data)) {
                	$data = unserialize($data);
                }
                // 检查文件是否过期
                $last_time = filemtime($hash_file);
                if ($last_time < time()) {
                	unlink($hash_file);
                }
        	}
        }
        error_reporting($error_level);
        return $data;
    }
    /**
     * 删除一个key值
     *
     * @param string $key
     * @return bool
     */
    function delete($key) {
        $hash_file = $this->file($key);
        if (is_file($hash_file)) {
            $error_level = error_reporting(0);
        	unlink($hash_file);
            error_reporting($error_level);
        	return true;
        }
    }
    /**
     * 清除所有缓存的数据，但是不会削去使用的内存空间
     *
     * @return bool
     */
    function flush() {
        return rmdirs(CACHE_PATH);
    }
    /**
     * 执行过期文件清理
     *
     * @return bool
     */
    function gc($path) {
        $error_level = error_reporting(0);
        if ($dh = opendir($path)) {
            while (false !== ($file=readdir($dh))) {
                if ($file != '.' && $file != '..') {
                    $file_path = $path.'/'.$file;
                    // 文件夹
                    if (is_dir($file_path)) {
                        $this->gc($file_path);
                    }
                    // 文件
                    elseif (is_file($file_path)) {
                        $last_time = filemtime($file_path);
                        if ($last_time < time()) {
                            unlink($file_path);
                        }
                    }
                }
            }
            closedir($dh);
        }
        error_reporting($error_level);
        return true;
    }
    
    function __destruct() {
        return $this->gc(CACHE_PATH);
    }
}
class LC_Null { }
/**
 * 实例化对象
 *
 * @return FCache
 */
function &_fcache_get_object() {
    static $fcache;
	if ( is_null($fcache) )
		$fcache = new FCache();
	return $fcache;
}
/**
 * 判断cache结果
 *
 * @param mixed $data
 * @return bool
 */
function fcache_is_null($data) {
    if ($data === null)
        return true;
    if (is_object($data)) {
        return get_class($data) == 'LC_Null';
    }
    return false;
}
/**
 * 判断cache结果
 *
 * @param mixed $data
 * @return bool
 */
function fcache_not_null($data) {
    return fcache_is_null($data) === false;
}
function fcache_file($key) {
    $fcache = _fcache_get_object();
    return $fcache->file($key);
}
function fcache_set($key, $data, $expire=0) {
    $fcache = _fcache_get_object();
    return $fcache->set($key, $data, $expire);
}
function fcache_get($key) {
    $fcache = _fcache_get_object();
    return $fcache->get($key);
}
function fcache_delete($key) {
    $fcache = _fcache_get_object();
    return $fcache->delete($key);
}
function fcache_flush() {
    $fcache = _fcache_get_object();
    return $fcache->flush();
}