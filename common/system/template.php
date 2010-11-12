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

class Template {
    var $_vars    = array();
    var $_plugins = array();
    function __construct(){ }

    function Template() {
        $args = func_get_args();
		call_user_func_array( array(&$this, '__construct'), $args );
    }
    /**
     * 载入html代码
     *
     * @param  $html
     * @return mixed
     */
    function load($html) {
        // 处理 include 标签
        $tags = array(); $base = ABS_PATH.'/'.system_themes_path().'/';
        if (preg_match_all('/\{(inc|include)[^\}]*file=([^\}]*)\/\}/isU',$html,$r)) {
            $tags = $r[2];
            foreach ($tags as $i=>$tag) {
                $file = trim($tag,'"\' ');
                $html = str_replace($r[0][$i],$this->load_file($base.$file),$html);
            }
        }
        $html = preg_replace('/<title>/isU',"<meta name=\"generator\" content=\"LazyCMS ".LAZY_VERSION."\"/>\n\${0}",$html);
        // 格式化图片、css、js路径
        $html = preg_replace('/(<(((script|link|img|input|embed|object|base|area|map|table|td|th|tr).+?(src|href|background))|((param).+?(src|value)))=([^\/]+?))((images|scripts)\/.{0,}?\>)/i','${1}'.WEB_ROOT.system_themes_path().'/${10}',$html);
        return $html;
    }
    /**
     * 加载文件
     *
     * @param  $file
     * @return mixed
     */
    function load_file($file) {
        return $this->load(file_get_contents($file));
    }
    /**
     * 添加插件
     *
     * @param  $func
     * @return void
     */
    function add_plugin($func) {
        if (!in_array($func,$this->_plugins)) {
            $this->_plugins[] = $func;
            return true;
        }
        return false;
    }
    /**
     * 执行插件
     *
     * @param string $tag_name
     * @param string $tag
     * @param array $block 标签信息，嵌套标签需要传
     * @return mixed
     */
    function apply_plugins($tag_name,$tag,$block=null) {
        $result = null; $tag_name = strtolower($tag_name);
        foreach ($this->_plugins as $func) {
            $result = call_user_func($func,$tag_name,$tag,$block);
            if (null !== $result) break;
        }
        return $result;
    }
    /**
     * 清空内部数组
     *
     * @param string $type
     * @return void
     */
    function clean(){
        $this->_vars = array();
    }
    /**
     * 设置变量
     *
     * @param string|array $key
     * @param mixed $val
     * @return bool
     */
    function set_var($key,$val=null) {
        // 空key不赋值
        if (empty($key)) return true;
        // 批量赋值
    	if (is_array($key)) {
    		foreach ($key as $k=>$v) {
                if (empty($k)) continue;
                $this->_vars[strtolower($k)] = $this->encode($v);
    		}
        }
        // 单个赋值
        else {
            $this->_vars[strtolower($key)] = $this->encode($val);
        }
        return true;
    }
    /**
     * 取得变量
     *
     * @param string $key
     * @return array
     */
    function get_var($key) {
        $key = strtolower($key);
        return isset($this->_vars[$key]) ? $this->_vars[$key] : null;
    }
    /**
     * 解析多层嵌套标签
     *
     * @param  $html
     * @return array
     */
    function get_blocks($html){
        $result = array();
        // 匹配出所有的块标签
        if (preg_match_all('/\{[\w+\:\-]+\b[^\}]*(?<!\/)\}|\{\/[\w+\:\-]+\}/isU',$html,$r)) {
            $content  = $html;
            $position = $tag_len = $id = 0;
            $matches  = $r[0];
            $stacks   = array();
            $result   = array();
            // 遍历标签
            foreach($matches as $match) {
                // 查找标签是否存在
                if ($index = strpos($content,$match)) {
                    // 计算标签所在文档的位置
                    $position += $index + $tag_len;
                    // 标签长度
                    $tag_len = strlen($match);
                    // 入栈
                    if (preg_match('/\{([\w+\:\-]+)\b[^\}]*\}/isU',$match,$tag)) {
                        $stacks[] = array(
                            'id'    => ++$id,
                            'name'  => $tag[1],
                            'start' => $position,
                            'inner_start' => $position + $tag_len,
                        );
                    }
                    // 出栈
                    else {
                        $data           = array_pop($stacks);
                        $parent         = array_pop(array_slice($stacks,count($stacks)-1,1));
                        $data['offset'] = $data['inner_start'] - $data['start'];
                        $data['length'] = $position - $data['inner_start'];
                        $data['end']    = $position + $tag_len;
                        $data['pid']    = isset($parent['id']) ? $parent['id'] : 0;
                        $data['tag']    = substr($html,$data['start'],$data['end']-$data['start']);

                        unset($data['start'],$data['inner_start'],$data['end']);


                        $result[$data['id']] = $data;
                        
                    }
                    // 截取剩余代码
                    $content = substr($content,$index + $tag_len);
                }
            }
            // 将数组转变成树，因为使用了引用，所以不会占用太多的内存
            $un = array();
            foreach ($result as $id=>$item) {
                if ($item['pid']) {
                    $result[$item['pid']]['sub'][$id] = &$result[$id];
                    $un[] = $id;
                }
            }
            foreach($un as $v) unset($result[$v]);
        }
        return $result;
    }

    /**
     * 取得单个标签块
     *
     * @param string $html
     * @param string $tag_name
     * @param string $type
     * @return array|null
     */
    function get_block($html,$tag_name,$type='list') {
        $result = null;
        // 取得所有块标签
        $blocks = $this->get_blocks($html);
        // 处理所有标签
        foreach ($blocks as $block) {
            // 取得指定的标签块
            if (instr(strtolower($block['name']),$tag_name) && $this->get_attr($block['tag'],'type')==$type) {
                $result = $block; break;
            }
        }
        return $result;
    }
    /**
     * 取得标签块的内容
     *
     * @param  $block
     * @return string
     */
    function get_block_inner($block) {
        return substr($block['tag'], $block['offset'], $block['length']);
    }
    /**
     * 处理标签块
     *
     * @param  $html
     * @return mixed
     */
    function process_blocks($html) {
        // 取得所有块标签
        $blocks = $this->get_blocks($html);
        // 处理所有标签
        foreach ($blocks as $block) {
            $html = str_replace($block['tag'],$this->apply_plugins($block['name'],$block['tag'],$block),$html);
        }
        return $html;
    }
    /**
     * 解析变量
     *
     * @param string $html
     * @return mixed
     */
    function process_vars($html){
        if (preg_match_all('/\{\$([\w\.\-]++)\b[^\}]*+\}/',$html,$r)) {
            $tags = $r[1];
            foreach ($r[0] as $k => $tag) {
                // 应用插件解析
                $value = $this->apply_plugins('$'.$tags[$k], $tag);
                // 解析变量
                if (null === $value) {
                    $value = $this->get_var($tags[$k]);
                }
                // 处理变量属性
                $value = $this->process_attr($value, $tag);
                $html  = str_replace($tag, $value, $html);
            }
        }
        return $this->decode($html);
    }
    /**
     * 解析模版
     *
     * @param string $html
     * @return mixed
     */
    function parse($html) {
        $html = $this->process_blocks($html);
        $html = $this->process_vars($html);
        return $html;
    }
    /**
     * 解析标签属性
     *
     * @param  $value    标签名称
     * @param  $tag      整个标签字符串
     * @return array|mixed|string
     */
    function process_attr($value,$tag) {
        $result = $value;
        // size
        $size = $this->get_attr($tag,'size');
        if ($size && validate_is($size,VALIDATE_IS_NUMERIC)) {
            if (intval(mb_strlen($value,'UTF-8')) > intval($size)) {
                $result = mb_substr($value, 0, $size, 'UTF-8') . '...';
            } else{
                $result = $value;
            }
        }
        // datemode
        $date = $this->get_attr($tag,'mode');
        if (strlen($date) > 0 && is_numeric($value)) {
            switch (strval($date)) {
                case '0':
                    $result = date('Y-n-j G:i:s',$value);
                    break;
                case '1':
                    $result = date('Y-m-d H:i:s',$value);
                    break;
                default:
                    $result = date($date,$value);
                    break;
            }
        }
        // code
        $code = $this->get_attr($tag,'code');
        if (strlen($result) > 0 && $code) {
            $code = strtolower($code);
            switch ($code) {
                case 'javascript': case 'js':
                    $result = sprintf('document.writeln("%s");',str_replace(array("\r", "\n"), array('', '\n'), addslashes($result)));
                    break;
                case 'xmlencode': case 'xml':
                    $result = xmlencode($result);
                    break;
                case 'urlencode': case 'url':
                    $result = rawurlencode($result);
                    break;
                case 'htmlencode':
                    $result = esc_html($result);
                    break;
            }
        }
        // apply
        $func = $this->get_attr($tag,'func');
        if (strlen($func) > 0 && $func) {
            if (stripos($func,'@me') !== false) {
                $func = preg_replace("/'@me'|\"@me\"|@me/isU",'$result',$func);
            }
            $result = eval('return '.$func.';');
        }
        return $result;
    }
    /**
     * 取得一个属性值
     *
     * @param  $tag
     * @param  $attr
     * @return string
     */
    function get_attr($tag,$attr) {
        $value = mid($tag, $attr.'="','"');
        if (!$value)
            $value = mid($tag, $attr."='","'");
        return $value;
    }
    /**
     * 转义标签
     *
     * @param  $str
     * @return mixed
     */
    function encode($str) {
        return str_replace(array(chr(36),chr(123),chr(125)), array('&#36;','&#123;','&#125;'), $str);
    }
    /**
     * 反转标签
     *
     * @param  $str
     * @return mixed
     */
    function decode($str) {
        return str_replace(array('&#36;','&#123;','&#125;'), array(chr(36),chr(123),chr(125)), $str);
    }
}
/**
 * 取得模版实例
 *
 * @return Template
 */
function &_tpl_get_object() {
    static $template;
	if ( is_null($template) )
		$template = new Template();
	return $template;
}
/**
 * 加载文件 
 *
 * @param  $file
 * @return mixed
 */
function tpl_loadfile($file) {
    $tpl = _tpl_get_object();
    return $tpl->load_file($file);
}
/**
 * 添加插件
 *
 * @param  $func
 * @return void
 */
function tpl_add_plugin($func) {
    $tpl = _tpl_get_object();
    return $tpl->add_plugin($func);
}
/**
 * 使用插件
 * 
 * @param  $tag_name
 * @param  $tag
 * @return mixed
 */
function tpl_apply_plugins($tag_name, $tag) {
    $tpl = _tpl_get_object();
    return $tpl->apply_plugins($tag_name, $tag);
}
/**
 * 清理内部数组
 *
 * @return void
 */
function tpl_clean() {
    $tpl = _tpl_get_object();
    return $tpl->clean();
}
/**
 * 变量赋值
 *
 * @param mixed $key
 * @param mixed $val
 * @return array
 */
function tpl_set_var($key,$val=null) {
    $tpl = _tpl_get_object();
    return $tpl->set_var($key,$val);   
}
/**
 * 查询变量
 *
 * @param string $key
 * @return array
 */
function tpl_get_var($key) {
    $tpl = _tpl_get_object();
    return $tpl->get_var($key);   
}
/**
 * 取得单个标签块
 * 
 * @param string $html
 * @param string $tag_name
 * @param string $type
 * @return array|null
 */
function tpl_get_block($html,$tag_name,$type='list') {
    $tpl = _tpl_get_object();
    return $tpl->get_block($html,$tag_name,$type);
}
/**
 * 取得标签块内容
 *
 * @param  $block
 * @return string
 */
function tpl_get_block_inner($block) {
    $tpl = _tpl_get_object();
    return $tpl->get_block_inner($block);
}
/**
 * 取得属性
 *
 * @param  $tag
 * @param  $attr
 * @return string
 */
function tpl_get_attr($tag, $attr) {
    $tpl = _tpl_get_object();
    return $tpl->get_attr($tag, $attr);
}
/**
 * 解析模版变量
 *
 * @param  $html
 * @return mixed
 */
function tpl_parse($html) {
    $tpl = _tpl_get_object();
    return $tpl->parse($html);
}