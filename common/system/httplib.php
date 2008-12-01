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
defined('COM_PATH') or die('Restricted access!');
/**
 * http下载类库
 */
class Httplib{
    // 目标网站无法打开时返回的错误代码
    var $_ERROR_CONNECT_FAILURE = 600;
    // 自定义 UserAgent 字符串
    var $_SEND_USER_AGENT = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; LazyCMS.net::DownLoader)';
    var $_url,$_method,$_timeout;
    var $_scheme,$_host,$_port,$_path,$_query,$_referer;
    var $_header;
    var $_response;
    /**
     * 兼容PHP5模式
     * 
     * @param 同下面的参数
     */
    function __construct($url=null,$method='GET',$timeout=60){
        @set_time_limit(0);
        if (!empty($url)) {
            $this->connect($url,$method,$timeout);
        }
        return $this;
    }
    /**
     * 初始化对象
     *
     * @param string    $url
     * @param string    $method
     * @param int       $timeout
     * @return object
     */
    function Httplib($url=null,$method='GET',$timeout=60){
        return $this->__construct($url,$method,$timeout);
    }
    /**
     * 改变连接url
     *
     * @param string    $url
     * @param string    $method
     * @param int       $timeout
     * @return object
     */
    function connect($url=null,$method='GET',$timeout=60){
        $this->_header   = null;
        $this->_response = null;
        $this->_url      = $url;
        $this->_method   = strtoupper(empty($method) ? 'GET' : $method);
        $this->_timeout  = empty($timeout) ? 30 : $timeout;
        if (!empty($url)) {
            $this->_parseURL($url);
        }
        return $this;
    }
    /**
     * 发送请求
     * 
     * @param array $params
     * @return bool
     */ 
    function send($params=array()) {
        $header = null; $response = null; $QueryStr = null;
        if (!empty($params)) { $this->_method = 'POST'; }
        if (function_exists('curl_exec')) {
            $ch = curl_init($this->_url);
            curl_setopt_array($ch,array(
                CURLOPT_TIMEOUT => $this->_timeout,
                CURLOPT_HEADER => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_USERAGENT => $this->_SEND_USER_AGENT,
                CURLOPT_REFERER => $this->_referer,
            ));
            if ($this->method=='GET') {
                curl_setopt($ch,CURLOPT_HTTPGET,true);
            } else {
                if (is_array($params)) {
                    $QueryStr = http_build_query($params);
                } else {
                    $QueryStr = $params;
                }
                curl_setopt($ch,CURLOPT_POST,true);
                curl_setopt($ch,CURLOPT_POSTFIELDS,$QueryStr);
            }
            $fp = curl_exec($ch);
            curl_close($ch);
            if (!$fp) { return false; }
            $i = 0; $length = strlen($fp);
            // 读取 header
            do{ $header.= substr($fp,$i,1); $i++; } while (!preg_match("/\r\n\r\n$/",$header));
            // 遇到跳转，执行跟踪跳转
            if ($this->_redirect($header)) { return true; }
            // 读取内容
            do {
                $response.= substr($fp,$i,4096);
                $i = $i + 4096;
            } while ($length>=$i);
            unset($fp,$length,$i);
        } elseif (function_exists('fsockopen')) {
            $fp = @fsockopen($this->_host,$this->_port,$errno,$errstr,$this->_timeout);
            if (!$fp) { return false; }
            $_port   = ((int)$this->_port!==80) ? ':'.$this->_port : null;
            $SendStr = "{$this->_method} {$this->_path}{$this->_query} HTTP/1.0\r\n";
            $SendStr.= "Host:{$this->_host}{$_port}\r\n";
            $SendStr.= "Accept: */*\r\n";
            $SendStr.= "Referer:{$this->_referer}\r\n";
            $SendStr.= "User-Agent: ".$this->_SEND_USER_AGENT."\r\n";
            $SendStr.= "Pragma: no-cache\r\n";
            $SendStr.= "Cache-Control: no-cache\r\n";
            //如果是POST方法，分析参数
            if ($this->_method=='POST') {
                //判断参数是否是数组，循环出查询字符串
                if (is_array($params)) {
                    $QueryStr = http_build_query($params);
                } else {
                    $QueryStr = $params;
                }
                $length = strlen($QueryStr);
                $SendStr.= "Content-Type: application/x-www-form-urlencoded\r\n";
                $SendStr.= "Content-Length: {$length}\r\n";
            }
            $SendStr.= "Connection: Close\r\n\r\n";
            if(strlen($QueryStr) > 0){
                $SendStr.= $QueryStr."\r\n";
            }
            fputs($fp,$SendStr);
            // 读取 header
            do{ $header.= fread($fp,1); } while (!preg_match("/\r\n\r\n$/",$header));
            // 遇到跳转，执行跟踪跳转
            if ($this->_redirect($header)) { return true; }
            // 读取内容
            while(!feof($fp)) {
                $response.= fread($fp,4096);
            }
            fclose($fp);
        }
        $this->_header   = $header;
        $this->_response = $response;
        return true;
    }
    /**
     * 跟踪跳转
     *
     * @param string $header
     * @return bool
     */
    function _redirect($header){
        if (in_array($this->status($header),array(301,302))) {
            if(preg_match("/Location\:(.+)\r\n/i",$header,$regs)){
                $this->connect(trim($regs[1]),$this->_method,$this->_timeout);
                $this->send();
                return true;
            }
        } else {
            return false;
        }
    }
    /**
     * 取得请求的header
     *
     * @return string
     */
    function header(){
        return $this->_header;
    }
    /**
     * 请求返回的html
     *
     * @return string
     */
    function response(){
        return $this->_response;
    }
    /**
     * 返回状态
     *
     * @param string $header
     * @return int
     */
    function status($header=null){
        if (empty($header)) {
            $header = $this->_header;
        }
        if(preg_match("/(.+) (\d+) (.+)\r\n/i",$header,$status)){
            return $status[2];
        } else {
            return $this->_ERROR_CONNECT_FAILURE;
        }
    }
    /**
     * 解析url
     *
     * @param string $url
     */
    function _parseURL($url){
        $aUrl = parse_url($url);
        $aUrl['query'] = isset($aUrl['query']) ? $aUrl['query'] : null;
        $scheme = isset($_SERVER['HTTPS']) ? $_SERVER['HTTPS'] : null;
        $this->_scheme = ($scheme=='off' || empty($scheme)) ? 'http' : 'https';
        $this->_host  = isset($aUrl['host']) ? $aUrl['host'] : null;
        $this->_port  = empty($aUrl['port']) ? 80 : (int)$aUrl['host'];
        $this->_path  = empty($aUrl['path']) ? '/' : (string)$aUrl['path'];
        $this->_query = strlen($aUrl['query']) > 0 ? '?'.$aUrl['query'] : null;
        $this->_referer = $this->_scheme.'://'.$aUrl['host'];
    }
}