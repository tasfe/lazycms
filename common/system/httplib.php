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
    var $is_response, $disable_curl, $disable_fopen, $disable_streams, $disable_fsockopen;

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
    /**
     * post
     * 
     * @param  $url
     * @param array $args
     * @return array|bool|mixed|void
     */
    function post($url,$args=array()) {
        $defaults = array('method' => 'POST');
        $args     = array_merge($args,$defaults);
        return $this->request($url,$args);
    }
    /**
     * 执行请求
     *
     * @param  $url         路径
     * @param array $args   参数
     * @return array|bool|mixed|void
     */
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
			'decompress'  => true,
        );
        $r = array_merge($defaults,$args);
        if (empty($url)) return throw_error(__('A valid URL was not provided.'),E_LAZY_ERROR);
        if (is_null($r['headers'])) $r['headers'] = array();
        // headers 不是数组时需要处理
        if (!is_array($r['headers'])) {
			$headers = Httplib::process_headers($r['headers']);
			$r['headers'] = $headers['headers'];
		}
        // 处理user-agent
        if ( isset($r['headers']['User-Agent']) ) {
			$r['user-agent'] = $r['headers']['User-Agent'];
			unset($args['headers']['User-Agent']);
		} else if( isset($r['headers']['user-agent']) ) {
			$r['user-agent'] = $r['headers']['user-agent'];
			unset($r['headers']['user-agent']);
		}
        // Construct Cookie: header if any cookies are set
		Httplib::build_cookie_header( $r );
        // 判断是否支持gzip
        if ( Httplib::is_available() )
			$r['headers']['Accept-Encoding'] = Httplib::accept_encoding();

        // 判断数据处理类型
        if (empty($r['body'])) {
			if(($r['method'] == 'POST') && !isset($r['headers']['Content-Length']))
                $r['headers']['Content-Length'] = 0;
        } else {
            if (is_array($r['body'])) {
				$r['body'] = http_build_query($r['body'],null,'&');
				$r['headers']['Content-Type'] = 'application/x-www-form-urlencoded';
				$r['headers']['Content-Length'] = strlen($r['body']);
			}
            if (!isset($r['headers']['Content-Length']) && !isset($r['headers']['content-length']))
                $r['headers']['Content-Length'] = strlen($r['body']);
        } 
        $transports = $this->transports($r);
        $response   = array();
        foreach ((array)$transports as $transport) {
            if (method_exists($this,'request_'.$transport)) {
                $response = call_user_func(array(&$this,'request_'.$transport),$url,$r);
                if (is_array($response)) return $response;
            }
        }
        return $response;
    }
    /**
     * fsockopen
     *
     * @param  $url
     * @param  $args
     * @return array|bool|mixed|void
     */
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
        // referer
        if (!isset($args['headers']['referer']))
            $str_headers.= sprintf("Referer: %s\r\n",$aurl['referer']);

        // connection
        if (!isset($args['headers']['connection']))
            $str_headers.= "Connection: Close\r\n";
        
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
		while (!feof($handle)) {
            $str_response.= fread($handle, 4096);
        }
        
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
			$process['body'] = Httplib::decode_chunked($process['body']);

        if ( true === $args['decompress'] && true === $this->should_decode($headers['headers']) )
			$process['body'] = Httplib::decompress( $process['body'] );

        $response['headers']  = $headers['headers'];
        $response['body']     = $process['body'];
        $response['response'] = $headers['response'];
        $response['cookies']  = $headers['cookies'];
        
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
     * 创建cookie header
     *
     * @param  $r
     * @return void
     */
    function build_cookie_header( &$r ) {
		if ( ! empty($r['cookies']) ) {
			$cookies_header = '';
			foreach ( (array) $r['cookies'] as $cookie ) {
                if (!empty($cookie['name']) && empty($cookie['value'])) {
                    $cookies_header .= $cookie['name'] . '=' . urlencode( $cookie['value'] ) . '; ';
                }
			}
			$cookies_header = substr( $cookies_header, 0, -2 );
			$r['headers']['cookie'] = $cookies_header;
		}
	}
    /**
     * 判断是否需要解码
     *
     * @param  $headers
     * @return bool
     */
    function should_decode($headers) {
		if ( is_array( $headers ) ) {
			if ( array_key_exists('content-encoding', $headers) && ! empty( $headers['content-encoding'] ) )
				return true;
		} else if ( is_string( $headers ) ) {
			return ( stripos($headers, 'content-encoding:') !== false );
		}

		return false;
	}
    /**
     * 处理http头
     *
     * @param  $headers
     * @return
     */
    function process_headers($headers) {
        // split headers, one per array element
		if ( is_string($headers) ) {
			// tolerate line terminator: CRLF = LF (RFC 2616 19.3)
			$headers = str_replace("\r\n", "\n", $headers);
			// unfold folded header fields. LWS = [CRLF] 1*( SP | HT ) <US-ASCII SP, space (32)>, <US-ASCII HT, horizontal-tab (9)> (RFC 2616 2.2)
			$headers = preg_replace('/\n[ \t]/', ' ', $headers);
			// create the headers array
			$headers = explode("\n", $headers);
		}

		$response = array('code' => 0, 'message' => '');

		// If a redirection has taken place, The headers for each page request may have been passed.
		// In this case, determine the final HTTP header and parse from there.
		for ( $i = count($headers)-1; $i >= 0; $i-- ) {
			if ( !empty($headers[$i]) && false === strpos($headers[$i], ':') ) {
				$headers = array_splice($headers, $i);
				break;
			}
		}
        
		$cookies = array();
		$new_headers = array();
		foreach ( $headers as $temp_header ) {
			if ( empty($temp_header) )
				continue;

			if ( false === strpos($temp_header, ':') ) {
				list( , $response['code'], $response['message']) = explode(' ', $temp_header, 3);
				continue;
			}

			list($key, $value) = explode(':', $temp_header, 2);

			if ( !empty( $value ) ) {
				$key = strtolower( $key );
				if ( isset( $new_headers[$key] ) ) {
					if ( !is_array($new_headers[$key]) )
						$new_headers[$key] = array($new_headers[$key]);
					$new_headers[$key][] = trim( $value );
				} else {
					$new_headers[$key] = trim( $value );
				}
				if ( 'set-cookie' == strtolower( $key ) )
					$cookies[] = Httplib::process_cookie( $value );
			}
		}

		return array('response' => $response, 'headers' => $new_headers, 'cookies' => $cookies);
    }
    /**
     * 处理cookie
     *
     * @param  $data
     * @return array|bool
     */
    function process_cookie($data) {
        $result = array();
        if ( is_string( $data ) ) {
			// Assume it's a header string direct from a previous request
			$pairs = explode( ';', $data );

			// Special handling for first pair; name=value. Also be careful of "=" in value
			$name  = trim( substr( $pairs[0], 0, strpos( $pairs[0], '=' ) ) );
			$value = substr( $pairs[0], strpos( $pairs[0], '=' ) + 1 );
			$result['name']  = $name;
			$result['value'] = urldecode( $value );
			array_shift( $pairs ); //Removes name=value from items.

			// Set everything else as a property
			foreach ( $pairs as $pair ) {
				$pair = rtrim($pair);
				if ( empty($pair) ) //Handles the cookie ending in ; which results in a empty final pair
					continue;

				list( $key, $val ) = strpos( $pair, '=' ) ? explode( '=', $pair ) : array( $pair, '' );
				$key = strtolower( trim( $key ) );
				if ( 'expires' == $key )
					$val = strtotime( $val );
				$result[$key] = $val;
			}
		} else {
			if ( !isset( $data['name'] ) )
				return false;

			// Set properties based directly on parameters
			$result['name']   = $data['name'];
			$result['value']  = isset( $data['value'] ) ? $data['value'] : '';
			$result['path']   = isset( $data['path'] ) ? $data['path'] : '';
			$result['domain'] = isset( $data['domain'] ) ? $data['domain'] : '';

			if ( isset( $data['expires'] ) )
				$result['expires'] = is_int( $data['expires'] ) ? $data['expires'] : strtotime( $data['expires'] );
			else
				$result['expires'] = null;
		}
        return $result;
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
    /**
     * Decompression of deflated string.
	 *
	 * Will attempt to decompress using the RFC 1950 standard, and if that fails
	 * then the RFC 1951 standard deflate will be attempted. Finally, the RFC
	 * 1952 standard gzip decode will be attempted. If all fail, then the
	 * original compressed string will be returned.
     *
     * @param  $compressed
     * @param  $length
     * @return bool|string
     */
    function decompress( $compressed, $length = null ) {

		if ( empty($compressed) )
			return $compressed;

		if ( false !== ( $decompressed = @gzinflate( $compressed ) ) )
			return $decompressed;

		if ( false !== ( $decompressed = Httplib::gzinflate( $compressed ) ) )
			return $decompressed;

		if ( false !== ( $decompressed = @gzuncompress( $compressed ) ) )
			return $decompressed;

		if ( function_exists('gzdecode') ) {
			$decompressed = @gzdecode( $compressed );

			if ( false !== $decompressed )
				return $decompressed;
		}

		return $compressed;
	}
    /**
     * Decompression of deflated string while staying compatible with the majority of servers.
	 *
	 * Certain Servers will return deflated data with headers which PHP's gziniflate()
	 * function cannot handle out of the box. The following function lifted from
	 * http://au2.php.net/manual/en/function.gzinflate.php#77336 will attempt to deflate
	 * the various return forms used.
     *
     * @param  $gz_data
     * @return bool|string
     */
    function gzinflate($gz_data) {
		if ( substr($gz_data, 0, 3) == "\x1f\x8b\x08" ) {
			$i = 10;
			$flg = ord( substr($gz_data, 3, 1) );
			if ( $flg > 0 ) {
				if ( $flg & 4 ) {
					list($xlen) = unpack('v', substr($gz_data, $i, 2) );
					$i = $i + 2 + $xlen;
				}
				if ( $flg & 8 )
					$i = strpos($gz_data, "\0", $i) + 1;
				if ( $flg & 16 )
					$i = strpos($gz_data, "\0", $i) + 1;
				if ( $flg & 2 )
					$i = $i + 2;
			}
			return gzinflate( substr($gz_data, $i, -8) );
		} else {
			return false;
		}
	}
    /**
     * Whether decompression and compression are supported by the PHP version.
	 *
	 * Each function is tested instead of checking for the zlib extension, to
	 * ensure that the functions all exist in the PHP version and aren't
	 * disabled.
     *
     * @return bool
     */
    function is_available() {
		return ( function_exists('gzuncompress') || function_exists('gzdeflate') || function_exists('gzinflate') );
	}
    /**
	 * What encoding types to accept and their priority values.
	 *
	 * @return string Types of encoding to accept.
	 */
	function accept_encoding() {
		$type = array();
		if ( function_exists( 'gzinflate' ) )
			$type[] = 'deflate;q=1.0';

		if ( function_exists( 'gzuncompress' ) )
			$type[] = 'compress;q=0.5';

		if ( function_exists( 'gzdecode' ) )
			$type[] = 'gzip;q=0.5';

		return implode(', ', $type);
	}
}