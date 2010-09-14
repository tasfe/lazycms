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
     * get
     *
     * @param  $url
     * @param array $args
     * @return array|bool|mixed|void
     */
    function get($url,$args=array()) {
        $defaults = array('method' => 'GET');
        $args     = Httplib::parse_args($args,$defaults);
        return $this->request($url,$args);
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
        $args     = Httplib::parse_args($args,$defaults);
        return $this->request($url,$args);
    }
    /**
     * head
     *
     * @param  $url
     * @param array $args
     * @return array|bool|mixed|void
     */
    function head($url,$args=array()) {
        $defaults = array('method' => 'HEAD');
        $args     = Httplib::parse_args($args,$defaults);
        return $this->request($url,$args);
    }
    /**
     * 测试支持的类型
     *
     * @param  $args
     * @param  $send_data   是否发送数据
     * @return array
     */
    function transports($args, $send_data='YES') {
        static $result;
        if ( is_null($result) ) {
            if (true != $this->disable_fsockopen && function_exists('fsockopen')) {
                $result['fsockopen'] = 'fsockopen';
            }
            if (true != $this->disable_fopen && function_exists('fopen')
                    && (function_exists('ini_get') && true == ini_get('allow_url_fopen'))
                    && (isset($args['method']) && 'HEAD' != $args['method']) ) {
                $result['fopen'] = 'fopen';
            }
            if (true != $this->disable_streams && function_exists('fopen')
                    && (function_exists('ini_get') && true == ini_get('allow_url_fopen'))
                    && !version_compare(PHP_VERSION, '5.0', '<') ) {
                $result['streams'] = 'streams';
            }
            if (true != $this->disable_curl && function_exists('curl_init') && function_exists('curl_exec')) {
                $result['curl'] = 'curl';
            }
            // fopen不支持post提交
            if ($send_data == 'YES' && isset($result['fopen'])) {
                unset($result['fopen']);
            }
        }
        return $result;
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
        $r = Httplib::parse_args($args,$defaults);
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
        if ( !isset($r['headers']['Accept']) && !isset($r['headers']['accept']) )
            $r['headers']['Accept'] = '*/*';
        
        // Construct Cookie: header if any cookies are set
		Httplib::build_cookie_header( $r );
        // 判断是否支持gzip
        if ( Httplib::is_compress() )
			$r['headers']['Accept-Encoding'] = Httplib::accept_encoding();

        // 判断数据处理类型
        if (empty($r['body'])) {
			if(($r['method'] == 'POST') && !isset($r['headers']['Content-Length'])) {
                $r['headers']['Content-Length'] = 0;
            }
            $transports = $this->transports($r,'NO');
        } else {
            if (is_array($r['body'])) {
				$r['body'] = http_build_query($r['body'],null,'&');
				$r['headers']['Content-Type'] = 'application/x-www-form-urlencoded';
				$r['headers']['Content-Length'] = strlen($r['body']);
			}
            if (!isset($r['headers']['Content-Length']) && !isset($r['headers']['content-length'])) {
                $r['headers']['Content-Length'] = strlen($r['body']);
            }
            $transports = $this->transports($r,'YES');
        } 

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
     * @param  $agrs
     * @return array|bool|mixed|void
     */
    function request_fsockopen($url,$agrs) {
        $r = $agrs;
        // 解析url
        $aurl = Httplib::parse_url($url); $host = $aurl['host']; if ('localhost' == strtolower($host)) $host = '127.0.0.1';
        // 连接服务器
        $start_delay = time();
        $handle      = @fsockopen($host, $aurl['port'], $errno, $errstr, $r['timeout']);
        $end_delay   = time();
        // 连接错误
        if (false === $handle) return throw_error(sprintf('%s: %s',$errno,$errstr),E_LAZY_WARNING);
        // 连接时间超过超时时间，暂时禁用当前方法
        $elapse_delay = ($end_delay-$start_delay) > $r['timeout'];
		if (true === $elapse_delay) $this->disable_fsockopen = true;
        // 设置超时时间
        $timeout = (int) floor( $r['timeout'] );
		$utimeout = $timeout == $r['timeout'] ? 0 : 1000000 * $r['timeout'] % 1000000;
		stream_set_timeout( $handle, $timeout, $utimeout );
        // 拼装headers
        $str_headers = sprintf("%s %s HTTP/%s\r\n",strtoupper($r['method']),$aurl['path'].$aurl['query'],$r['httpversion']);
        $str_headers.= sprintf("Host: %s\r\n",$aurl['host']);
        // user-agent
        if (isset($r['user-agent']))
            $str_headers.= sprintf("User-Agent: %s\r\n",$r['user-agent']);
        // 其他字段
        if (is_array($r['headers'])) {
			foreach ( (array) $r['headers'] as $header => $headerValue )
				$str_headers.= sprintf("%s: %s\r\n",$header,$headerValue);
		} else {
			$str_headers.= $r['headers'];
		}
        // referer
        if (!isset($r['headers']['referer']))
            $str_headers.= sprintf("Referer: %s\r\n",$aurl['referer']);

        // connection
        if (!isset($r['headers']['connection']))
            $str_headers.= "Connection: Close\r\n";
        
        $str_headers.= "\r\n";

        if (!is_null($r['body'])) $str_headers.= $r['body'];
        
        // 提交
		fwrite($handle, $str_headers);
        // 非阻塞模式
        if (!$r['blocking']) {
			fclose($handle);
            return array('headers'=>array(),'body'=>'','response'=>array('code'=>false,'message'=>false),'cookies'=>array());
        }

        // 读取服务器返回数据
        $str_response = '';
		while (!feof($handle)) {
            $str_response.= fread($handle, 4096);
        }
        
		fclose($handle);

        // 处理服务器返回的结果
        $process = Httplib::process_response($str_response);
        // 处理headers
        $headers = Httplib::process_headers($process['headers']);
        // 响应代码是400范围内？
		if ((int)$headers['response']['code'] >= 400 && (int)$headers['response']['code'] < 500)
            return throw_error($headers['response']['code'].': '.$headers['response']['message'],E_LAZY_WARNING);

        // 重定向到新的位置
		if ('HEAD' != $r['method'] && isset($headers['headers']['location'])) {
			if ($r['redirection']-- > 0) {
				return $this->request($headers['headers']['location'], $r);
			} else {
                return throw_error(__('Too many redirects.'),E_LAZY_WARNING);
			}
		}
        // If the body was chunk encoded, then decode it.
		if (!empty($process['body']) && isset($headers['headers']['transfer-encoding']) && 'chunked' == $headers['headers']['transfer-encoding'])
			$process['body'] = Httplib::decode_chunked($process['body']);

        if ( true === $r['decompress'] && true === Httplib::should_decode($headers['headers']) )
			$process['body'] = Httplib::decompress( $process['body'] );

        return array(
            'headers'   => $headers['headers'],
            'body'      => $process['body'],
            'response'  => $headers['response'],
            'cookies'   => $headers['cookies'],
        );
    }
    /**
     * curl
     *
     * @param  $url
     * @param  $agrs
     * @return array|bool|mixed|void
     */
    function request_curl($url,$agrs) {
        $r = $agrs;
        // 解析url
        $aurl = Httplib::parse_url($url); $host = $aurl['host']; if ('localhost' == strtolower($host)) $host = '127.0.0.1';

        $handle = curl_init();

        // CURLOPT_TIMEOUT and CURLOPT_CONNECTTIMEOUT expect integers.  Have to use ceil since
		// a value of 0 will allow an ulimited timeout.
		$timeout = (int) ceil( $r['timeout'] );
		curl_setopt( $handle, CURLOPT_CONNECTTIMEOUT, $timeout );
		curl_setopt( $handle, CURLOPT_TIMEOUT, $timeout );

		curl_setopt( $handle, CURLOPT_URL, $url);
		curl_setopt( $handle, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $handle, CURLOPT_USERAGENT, $r['user-agent'] );
		curl_setopt( $handle, CURLOPT_MAXREDIRS, $r['redirection'] );

        switch ( $r['method'] ) {
			case 'HEAD':
				curl_setopt( $handle, CURLOPT_NOBODY, true );
				break;
			case 'POST':
				curl_setopt( $handle, CURLOPT_POST, true );
				curl_setopt( $handle, CURLOPT_POSTFIELDS, $r['body'] );
				break;
		}

		if ( true === $r['blocking'] )
			curl_setopt( $handle, CURLOPT_HEADER, true );
		else
			curl_setopt( $handle, CURLOPT_HEADER, false );

		// The option doesn't work with safe mode or when open_basedir is set.
		// Disable HEAD when making HEAD requests.
		if ( !ini_get('safe_mode') && !ini_get('open_basedir') && 'HEAD' != $r['method'] )
			curl_setopt( $handle, CURLOPT_FOLLOWLOCATION, true );

		if ( !empty( $r['headers'] ) ) {
			// cURL expects full header strings in each element
			$headers = array();
			foreach ( $r['headers'] as $name => $value ) {
				$headers[] = "{$name}: $value";
			}
			curl_setopt( $handle, CURLOPT_HTTPHEADER, $headers );
		}

		if ( $r['httpversion'] == '1.0' )
			curl_setopt( $handle, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0 );
		else
			curl_setopt( $handle, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1 );

        // We don't need to return the body, so don't. Just execute request and return.
		if ( ! $r['blocking'] ) {
			curl_exec( $handle );
			curl_close( $handle );
			return array('headers'=>array(),'body'=>'','response'=>array('code'=>false,'message'=>false),'cookies'=>array());
		}

		$str_response = curl_exec( $handle );

        if ( !empty($str_response) ) {
			$length = curl_getinfo($handle, CURLINFO_HEADER_SIZE);
			$headers = trim( substr($str_response, 0, $length) );
			if ( strlen($str_response) > $length )
				$the_body = substr( $str_response, $length );
			else
				$the_body = '';
            
			if ( false !== strrpos($headers, "\r\n\r\n") ) {
				$header_parts = explode("\r\n\r\n", $headers);
				$headers = $header_parts[ count($header_parts) -1 ];
			}
			$headers = Httplib::process_headers($headers);
		} else {
			if ( $curl_error = curl_error($handle) )
                return throw_error($curl_error,E_LAZY_WARNING);
			if ( in_array( curl_getinfo( $handle, CURLINFO_HTTP_CODE ), array(301, 302) ) )
				return throw_error(__('Too many redirects.'), E_LAZY_WARNING);

			$headers = array( 'headers' => array(), 'cookies' => array() );
			$the_body = '';
		}
        $response = array();
		$response['code']    = curl_getinfo( $handle, CURLINFO_HTTP_CODE );
		$response['message'] = get_status_header_desc($response['code']);

		curl_close( $handle );

		// See #11305 - When running under safe mode, redirection is disabled above. Handle it manually.
		if ( !empty($headers['headers']['location']) && (ini_get('safe_mode') || ini_get('open_basedir')) ) {
			if ( $r['redirection']-- > 0 ) {
				return $this->request($headers['headers']['location'], $r);
			} else {
                return throw_error(__('Too many redirects.'),E_LAZY_WARNING);
			}
		}

		if ( true === $r['decompress'] && true === Httplib::should_decode($headers['headers']) )
			$the_body = Httplib::decompress( $the_body );

        return array(
            'headers'   => $headers['headers'],
            'body'      => $the_body,
            'response'  => $response,
            'cookies'   => $headers['cookies'],
        );
    }
    /**
     * fopen
     *
     * @param  $url
     * @param  $agrs
     * @return bool
     */
    function request_fopen($url,$agrs) {
        $r = $agrs;
        // 解析url
        $aurl = Httplib::parse_url($url); $host = $aurl['host']; if ('localhost' == strtolower($host)) $host = '127.0.0.1';
        
        if ( 'http' != $aurl['scheme'] && 'https' != $aurl['scheme'] )
			$url = str_replace($aurl['scheme'], 'http', $url);
        
        if ( is_null( $r['headers'] ) )
			$r['headers'] = array();

		if ( is_string($r['headers']) ) {
			$headers = Httplib::process_headers($r['headers']);
			$r['headers'] = $headers['headers'];
		}

		$initial_user_agent = ini_get('user_agent');

        if ( !empty($r['headers']) && is_array($r['headers']) ) {
			$user_agent_extra_headers = '';
			foreach ( $r['headers'] as $header => $value )
				$user_agent_extra_headers .= "\r\n$header: $value";
            
            // referer
            if (!isset($r['headers']['referer']))
                $user_agent_extra_headers.= sprintf("\r\nReferer: %s",$aurl['referer']);

            // connection
            if (!isset($r['headers']['connection']))
                $user_agent_extra_headers.= "\r\nConnection: Close";
            
			@ini_set('user_agent', $r['user-agent'] . $user_agent_extra_headers);
		} else {
			@ini_set('user_agent', $r['user-agent']);
		}

        $handle = fopen($url, 'r');

        if (! $handle)
			return throw_error(sprintf(__('Could not open handle for fopen() to %s'), $url),E_LAZY_WARNING);

        $timeout = (int) floor( $r['timeout'] );
		$utimeout = $timeout == $r['timeout'] ? 0 : 1000000 * $r['timeout'] % 1000000;
		stream_set_timeout( $handle, $timeout, $utimeout );

		if ( ! $r['blocking'] ) {
			fclose($handle);
			@ini_set('user_agent', $initial_user_agent); //Clean up any extra headers added
			return array('headers'=>array(),'body'=>'','response'=>array('code'=>false,'message'=>false),'cookies'=>array());
		}

        $str_response = '';
		while ( ! feof($handle) )
			$str_response .= fread($handle, 4096);

		if ( function_exists('stream_get_meta_data') ) {
			$meta = stream_get_meta_data($handle);

			$the_headers = $meta['wrapper_data'];
			if ( isset( $meta['wrapper_data']['headers'] ) )
				$the_headers = $meta['wrapper_data']['headers'];
		} else {
			//$http_response_header is a PHP reserved variable which is set in the current-scope when using the HTTP Wrapper
			//see http://php.oregonstate.edu/manual/en/reserved.variables.httpresponseheader.php
			$the_headers = $http_response_header;
		}

		fclose($handle);

		@ini_set('user_agent', $initial_user_agent); //Clean up any extra headers added

		$headers = Httplib::process_headers($the_headers);

		if ( ! empty( $str_response ) && isset( $headers['headers']['transfer-encoding'] ) && 'chunked' == $headers['headers']['transfer-encoding'] )
			$str_response = Httplib::decode_chunked($str_response);

		if ( true === $r['decompress'] && true === Httplib::should_decode($headers['headers']) )
			$str_response = Httplib::decompress( $str_response );

		return array(
            'headers'   => $headers['headers'],
            'body'      => $str_response,
            'response'  => $headers['response'],
            'cookies'   => $headers['cookies']
        );
    }
    /**
     * streams
     *
     * @param  $url
     * @param  $agrs
     * @return bool
     */
    function request_streams($url,$agrs) {
        $r = $agrs;
        // 解析url
        $aurl = Httplib::parse_url($url); $host = $aurl['host']; if ('localhost' == strtolower($host)) $host = '127.0.0.1';

        // Convert Header array to string.
		$str_headers = '';
		if ( is_array( $r['headers'] ) )
			foreach ( $r['headers'] as $name => $value )
				$str_headers .= "{$name}: $value\r\n";
		else if ( is_string( $r['headers'] ) )
			$str_headers = $r['headers'];

        // referer
        if (!isset($r['headers']['referer']))
            $str_headers.= sprintf("Referer: %s\r\n",$aurl['referer']);

        // connection
        if (!isset($r['headers']['connection']))
            $str_headers.= "Connection: Close\r\n";

        $arr_context = array('http' =>
			array(
				'method'            => strtoupper($r['method']),
				'user_agent'        => $r['user-agent'],
				'max_redirects'     => $r['redirection'] + 1, // See #11557
				'protocol_version'  => (float) $r['httpversion'],
				'header'            => $str_headers,
				'ignore_errors'     => true, // Return non-200 requests.
				'timeout'           => $r['timeout'],
			)
		);
        
        if ( 'HEAD' == $r['method'] ) // Disable redirects for HEAD requests
            $arr_context['http']['max_redirects'] = 1;

        if ( ! empty($r['body'] ) )
            $arr_context['http']['content'] = $r['body'];
        //print_r($arr_context);exit;
        $context = stream_context_create($arr_context);

        $handle = fopen($url, 'r', false, $context);

        if ( ! $handle )
            return throw_error(sprintf(__('Could not open handle for fopen() to %s'), $url),E_LAZY_WARNING);

        $timeout = (int) floor( $r['timeout'] );
		$utimeout = $timeout == $r['timeout'] ? 0 : 1000000 * $r['timeout'] % 1000000;
		stream_set_timeout( $handle, $timeout, $utimeout );

		if ( ! $r['blocking'] ) {
			stream_set_blocking($handle, 0);
			fclose($handle);
			return array('headers'=>array(),'body'=>'','response'=>array('code'=>false,'message'=>false),'cookies'=>array());
		}

        $str_response = stream_get_contents($handle);
		$meta = stream_get_meta_data($handle);

		fclose($handle);

		$headers = array();
		if ( isset( $meta['wrapper_data']['headers'] ) )
			$headers = Httplib::process_headers($meta['wrapper_data']['headers']);
		else
			$headers = Httplib::process_headers($meta['wrapper_data']);

		if ( ! empty( $str_response ) && isset( $headers['headers']['transfer-encoding'] ) && 'chunked' == $headers['headers']['transfer-encoding'] )
			$str_response = Httplib::decode_chunked($str_response);

		if ( true === $r['decompress'] && true === Httplib::should_decode($headers['headers']) )
			$str_response = Httplib::decompress( $str_response );

        return array(
            'headers'   => $headers['headers'],
            'body'      => $str_response,
            'response'  => $headers['response'],
            'cookies'   => $headers['cookies'],
        );
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
        
		if ( false !== ( $decompressed = @gzdecode( $compressed ) ) )
		    return $decompressed;

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
    function is_compress() {
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
    /**
     * Merge user defined arguments into defaults array.
     *
     * This function is used throughout WordPress to allow for both string or array
     * to be merged into another array.
     * 
     * @param  $args
     * @param string $defaults
     * @return array|mixed
     */
    function parse_args($args, $defaults = '') {
        if ( is_object( $args ) )
            $r = get_object_vars( $args );
        elseif ( is_array( $args ) )
            $r =& $args;
        else {
            parse_str( $args, $r );
            if ( get_magic_quotes_gpc() )
                $r = stripslashes_deep( $r );
        }

        if ( is_array( $defaults ) )
            return array_merge( $defaults, $r );
        
        return $r;
    }
}