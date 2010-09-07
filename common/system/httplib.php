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

// 目标网站无法打开时返回的错误代码
define('HTTPLIB_CONNECT_FAILURE',600);
// User agent
define('HTTPLIB_USER_AGENT','LazyCMS/'.LAZY_VERSION.' (compatible; Httplib/0.1; +http://www.lazycms.com/httplib.html)');

class Httplib {
    /*var $header,$response;
    var $url,$method,$timeout;
    var $host,$port,$path,$query,$referer;*/
    var $is_response;
    var $disable_curl, $disable_fopen, $disable_streams, $disable_fsockopen;


    function __construct(){ }

    function Httplib() {
        $args = func_get_args();
		return call_user_func_array( array(&$this, '__construct'), $args );
    }
    /**
     * 测试支持的类型
     *
     * @param  $opts
     * @return array
     */
    function transports($opts) {
        $result = array();
        if (true != $this->disable_fsockopen && function_exists('fsockopen')) {
            $result[] = 'fsockopen';
        }
        if (true != $this->disable_fopen && function_exists('fopen')
                && (function_exists('ini_get') && true == ini_get('allow_url_fopen'))
                && (isset($opts['method']) && 'HEAD' != $opts['method']) ) {
            $result[] = 'fopen';
        }
        if (true != $this->disable_streams && function_exists('fopen')
                && (function_exists('ini_get') && true == ini_get('allow_url_fopen'))
                && !version_compare(PHP_VERSION, '5.0', '<') ) {
            $result[] = 'streams';
        }
        if (true != $this->disable_curl && function_exists('curl_init') && function_exists('curl_exec')) {
            $result[] = 'curl';
        }
        return $result;
    }
    
    function request($url=null,$args=array()) {
        $defaults = array(
            'method'      => 'GET',
            'timeout'     => 5,
            'redirection' => 3,
            'user-agent'  => HTTPLIB_USER_AGENT,
            'blocking'    => true,
            'headers'     => array(),
            'body'        => null,
            'httpversion' => '1.0',
        );
        $opts = array_merge($defaults,$args);
        if (empty($url)) return throw_error(__('A valid URL was not provided.'),E_LAZY_ERROR);
        if (is_null($opts['headers'])) $opts['headers'] = array();
        // TODO headers 不是数组时需要处理

        // 处理user-agent
        if ( isset($opts['headers']['User-Agent']) ) {
			$opts['user-agent'] = $opts['headers']['User-Agent'];
			unset($args['headers']['User-Agent']);
		} else if( isset($opts['headers']['user-agent']) ) {
			$opts['user-agent'] = $opts['headers']['user-agent'];
			unset($opts['headers']['user-agent']);
		}
        // 判断数据处理类型
        if (empty($opts['body'])) {
			if(($opts['method'] == 'POST') && !isset($opts['headers']['Content-Length']))
                $opts['headers']['Content-Length'] = 0;
        } else {
            if (is_array($opts['body'])) {
				$opts['body'] = http_build_query($opts['body'],null,'&');
				$opts['headers']['Content-Type'] = 'application/x-www-form-urlencoded';
				$opts['headers']['Content-Length'] = strlen($opts['body']);
			}
            if (!isset($opts['headers']['Content-Length']) && !isset($opts['headers']['content-length']))
                $opts['headers']['Content-Length'] = strlen($opts['body']);
        } 
        $transports = $this->transports($opts);
        $response   = array();
        foreach ((array)$transports as $transport) {
            $response = call_user_func(array(&$this,'request_'.$transport),$url,$opts);
            if ($this->is_response) break;
        }
        return $response;
    }
    
    function request_fsockopen($url,$args) {
        $response = array(
            'headers'  => array(),
            'body'     => '',
            'response' => array(
                'code'    => false,
                'message' => false
            ),
            'cookies'  => array()
        );
        // 解析url
        $aurl = $this->parse_url($url); $host = $aurl['host']; if ('localhost' == strtolower($host)) $host = '127.0.0.1';
        // 连接服务器
        $start_delay = time();
        $handle      = @fsockopen($host, $aurl['port'], $errno, $errstr, $args['timeout']);
        $end_delay   = time();
        // 连接错误
        if (false === $handle) return throw_error(sprintf('%s: %s',$errno,$errstr),E_LAZY_WARNING);
        // 连接时间超过超时时间，暂时禁用当前方法
        $elapse_delay = ($end_delay-$start_delay) > $args['timeout'];
		if (true === $elapse_delay) $this->disable_fsockopen = true;
        // 设置超时时间
        $timeout = (int) floor( $args['timeout'] );
		$utimeout = $timeout == $args['timeout'] ? 0 : 1000000 * $args['timeout'] % 1000000;
		stream_set_timeout( $handle, $timeout, $utimeout );
        // 拼装headers
        $str_headers = sprintf("%s %s HTTP/%s\r\n",strtoupper($args['method']),$aurl['path'].$aurl['query'],$args['httpversion']);
        $str_headers.= sprintf("Host: %s\r\n",$aurl['host']);
        // user-agent
        if (isset($args['user-agent']))
            $str_headers.= sprintf("User-agent: %s\r\n",$args['user-agent']);
        // 其他字段
        if (is_array($args['headers'])) {
			foreach ( (array) $args['headers'] as $header => $headerValue )
				$str_headers.= sprintf("%s: %s\r\n",$header,$headerValue);
		} else {
			$str_headers.= $args['headers'];
		}
        $str_headers.= "\r\n";

        if (!is_null($args['body'])) $str_headers.= $args['body'];
        // 提交
		fwrite($handle, $str_headers);
        // 非阻塞模式
        if (!$args['blocking']) {
			fclose($handle);
            return $response;
        }
        // 读取服务器返回数据
        $str_response = '';
		while (!feof($handle)) $str_response.= fread($handle, 4096);
		fclose($handle);
        // 处理服务器返回的结果
        $process = $this->process_response($str_response);
        // 处理headers
        $headers = $this->process_headers($process['headers']);
        // 响应代码是400范围内？
		if ((int)$headers['response']['code'] >= 400 && (int)$headers['response']['code'] < 500)
            return throw_error($headers['response']['code'].': '.$headers['response']['message'],E_LAZY_WARNING);

        // 重定向到新的位置
		if ('HEAD' != $args['method'] && isset($headers['headers']['location'])) {
			if ($args['redirection']-- > 0) {
				return $this->request($headers['headers']['location'], $args);
			} else {
                return throw_error(__('Too many redirects.'),E_LAZY_WARNING);
			}
		}
        // If the body was chunk encoded, then decode it.
		if (!empty($process['body']) && isset($headers['headers']['transfer-encoding']) && 'chunked' == $headers['headers']['transfer-encoding'])
			$process['body'] = $this->decode_chunked($process['body']);

        $response['headers']  = $headers['headers'];
        $response['body']     = $process['body'];
        $response['response'] = $headers['response'];
        $response['cookies']  = $headers['cookies'];

        $this->is_response = true;
        return $response;
    }
    /**
     * 解析URL
     *
     * @param  $url
     * @return mixed
     */
    function parse_url($url){
        $referer = array();
        $aurl    = parse_url($url);
        $aurl['scheme']= isset($aurl['scheme']) ? $aurl['scheme'] : 'http';
        $aurl['host']  = isset($aurl['host']) ? $aurl['host'] : '';
        $aurl['port']  = isset($aurl['port']) ? intval($aurl['port']) : 80;
        $aurl['path']  = isset($aurl['path']) ? $aurl['path'] : '/';
        $aurl['query'] = isset($aurl['query']) ? '?'.$aurl['query'] : '';
        foreach(array('scheme','host','port','path','query') as $k) {
            if ($k=='port' && $aurl[$k]==80) {
                continue;
            } elseif ($k=='scheme') {
                $referer[$k] = $aurl[$k].'://';
            } elseif($k=='port') {
                $referer[$k] = ':'.$aurl[$k];
            } else {
                $referer[$k] = $aurl[$k];
            }
        }
        $aurl['referer'] = implode('',$referer);
        return $aurl;
    }
    /**
     * 处理返回的内容
     * 
     * @param  $response
     * @return
     */
    function process_response($response) {
		$res = explode("\r\n\r\n", $response, 2);
		return array('headers' => isset($res[0]) ? $res[0] : array(), 'body' => isset($res[1]) ? $res[1] : '');
	}

    /**
     * decode a string that is encoded w/ "chunked' transfer encoding
 	 * as defined in RFC2068 19.4.6
     *
     * @param string $buffer
     * @return string
     */
    function decode_chunked($buffer) {
    	$length = 0;
    	$newstr = '';
    	// read chunk-size, chunk-extension (if any) and CRLF
    	// get the position of the linebreak
    	$chunkend   = strpos($buffer,"\r\n") + 2;
		$chunk_size = hexdec(trim(substr($buffer,0,$chunkend)));
		$chunkstart = $chunkend;
		// while (chunk-size > 0) {
		while ($chunk_size > 0) {

			$chunkend = strpos( $buffer, "\r\n", $chunkstart + $chunk_size);

			// Just in case we got a broken connection
		  	if ($chunkend == false) {
		  	    $chunk = substr($buffer,$chunkstart);
				// append chunk-data to entity-body
		    	$new .= $chunk;
		  	    $length += strlen($chunk);
		  	    break;
			}

		  	// read chunk-data and CRLF
		  	$chunk  = substr($buffer,$chunkstart,$chunkend-$chunkstart);
		  	// append chunk-data to entity-body
		  	$newstr.= $chunk;
		  	// length := length + chunk-size
		  	$length += strlen($chunk);
		  	// read chunk-size and CRLF
		  	$chunkstart = $chunkend + 2;

		  	$chunkend = strpos($buffer,"\r\n",$chunkstart)+2;
			if ($chunkend == false) {
				break; //Just in case we got a broken connection
			}
			$chunk_size = hexdec(trim(substr($buffer,$chunkstart,$chunkend-$chunkstart)));
			$chunkstart = $chunkend;
		}
		return $newstr;
    }
}