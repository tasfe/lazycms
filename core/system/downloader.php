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
 * | Author: Lukin <mylukin@gmail.com>                                         |
 * +---------------------------------------------------------------------------+
 */
defined('CORE_PATH') or die('Restricted access!');
/**
 * 下载类库
 *
 * @copyright   Copyright (c) 2007-2008 lazycms.net All rights reserved.
 * @author      Lukin <mylukin@gmail.com>
 */
// DownLoader *** *** www.LazyCMS.net *** ***
class DownLoader extends Lazy{
    // 目标网站无法打开时返回的错误代码
    const ERROR_CONNECT_FAILURE = 600;
    // 自定义 UserAgent 字符串
    const SEND_USER_AGENT = 'LazyCMS::DownLoader';
    public $url,$method,$timeout;
    private $host,$port,$path,$query,$referer;
    private $header;
    private $body;

    // __construct *** *** www.LazyCMS.net *** ***
    public function __construct($url=null,$method='GET',$timeout=30){
        set_time_limit(0);
        if (!empty($url)) {
            $this->connect($url,$method,$timeout);
        }
    }
    // connect *** *** www.LazyCMS.net *** ***
    public function connect($url=null,$method='GET',$timeout=30){
        $this->header  = null;
        $this->body    = null;
        $this->url     = $url;
        $this->method  = strtoupper(empty($method) ? 'GET' : $method);
        $this->timeout = empty($timeout) ? 30 : $timeout;
        if (!empty($url)) {
            $this->parseURL($url);
        }
        return $this;
    }
    // send *** *** www.LazyCMS.net *** ***
    public function send($params=array()){
        $header = null; $body = null; $QueryStr = null;
        if (function_exists('fsockopen')) {
            $fp = @fsockopen($this->host,$this->port,$errno,$errstr,$this->timeout);
            if (!$fp) { return false; }
            $SendStr = "{$this->method} {$this->path}{$this->query} HTTP/1.0\r\n";
            $SendStr.= "Host:{$this->host}:{$this->port}\r\n";
            $SendStr.= "Accept: */*\r\n";
            $SendStr.= "Referer:{$this->referer}\r\n";
            $SendStr.= "User-Agent: ".self::SEND_USER_AGENT."\r\n";
            $SendStr.= "Pragma: no-cache\r\n";
            $SendStr.= "Cache-Control: no-cache\r\n";
            //如果是POST方法，分析参数
            if ($this->method=='POST') {
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
            if ($this->redirect($header)) { return true; }
            // 读取内容
            while(!feof($fp)) {
                $body.= fread($fp,4096);
            }
            fclose($fp);
        } elseif (function_exists('curl_exec')) {
            $ch = curl_init($this->url);
            curl_setopt_array($ch,array(
                CURLOPT_TIMEOUT => $this->timeout,
                CURLOPT_HEADER => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_USERAGENT => self::SEND_USER_AGENT,
                CURLOPT_REFERER => $this->referer,
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
            if ($this->redirect($header)) { return true; }
            // 读取内容
            do {
                $body.= substr($fp,$i,4096);
                $i = $i + 4096;
            } while ($length>=$i);
            unset($fp,$length,$i);
        }
        $this->header = $header;
        $this->body   = $body;
        return true;
    }
    // redirect *** *** www.LazyCMS.net *** ***
    private function redirect($header){
        if (in_array($this->status($header),array(301,302))) {
            if(preg_match("/Location\:(.+)\r\n/i",$header,$regs)){
                $this->connect(trim($regs[1]),$this->method,$this->timeout);
                $this->send();
                return true;
            }
        } else {
            return false;
        }
    }
    // header *** *** www.LazyCMS.net *** ***
    public function header(){
        return $this->header;
    }
    // body *** *** www.LazyCMS.net *** ***
    public function body(){
        return $this->body;
    }
    // status *** *** www.LazyCMS.net *** ***
    public function status($header=null){
        if (empty($header)) {
            $header = $this->header;
        }
        if(preg_match("/(.+) (\d+) (.+)\r\n/i",$header,$status)){
            return $status[2];
        } else {
            return self::ERROR_CONNECT_FAILURE;
        }
    }
    // parseURL *** *** www.LazyCMS.net *** ***
    private function parseURL($url){
        $aUrl = parse_url($url);
        $aUrl['query'] = isset($aUrl['query']) ? $aUrl['query'] : null;
        $this->host  = $aUrl['host'];
        $this->port  = empty($aUrl['port']) ? 80 : (int)$aUrl['host'];
        $this->path  = empty($aUrl['path']) ? '/' : (string)$aUrl['path'];
        $this->query = strlen($aUrl['query']) > 0 ? '?'.$aUrl['query'] : null;
        $this->referer = 'http://'.$aUrl['host'];
    }
}