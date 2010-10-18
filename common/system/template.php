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
        if (preg_match_all('/\{include[^\}]*file=([^\}]*)\/\}/isU',$html,$r)) {
            $tags = $r[1];
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
     * @param  $tag_name
     * @param  $tag
     * @return mixed
     */
    function apply_plugins($tag_name,$tag) {
        $result = null;
        foreach ($this->_plugins as $func) {
            $result = call_user_func($func,$tag_name,$tag);
            if (null !== $result) break;
        }
        return $result;
    }
    /**
     * 清空内部数组
     *
     * @return void
     */
    function clean(){
        $this->_vars = array();
    }
    /**
     * 变量赋值
     *
     * @param  $key
     * @param  $val
     * @return array
     */
    function value($key=null,$val=null) {
        // 获取全部变量
        if (func_num_args()==0) return $this->_vars;
        // 批量赋值
    	if (is_array($key)) {
    		foreach ($key as $k=>$v) {
                $this->_vars[strtolower($k)] = $v;
    		}
            return $this->_vars;
        }
        $key = strtolower($key);
        // 取值
        if ($key && func_num_args()==1) {
            return isset($this->_vars[$key])?$this->_vars[$key]:null;
        }
        // 单个赋值
        else {
            $this->_vars[$key] = $val;
            return $val;
        }
    }
    /**
     * 解析标签块
     *
     * @param  $html
     * @return array
     */
    function parse_blocks($html){
        $result = array();
        // 匹配出所有的块标签
        if (preg_match_all('/\{[\w+\:\-]+\b[^\}]*(?<!\/)\}|\{\/[\w+\:\-]+\}/isU',$html,$r)) {
            $content  = $html;
            $position = $tag_len = $i = $j = 0;
            $matches  = $r[0];
            $tmp_stack  = array();
            $tmp_result = array();
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
                        $j++;
                        $tmp_stack[] = array(
                            'id'    => 0,
                            'tag'   => $tag[1],
                            'outer_start' => $position,
                            'inner_start' => $position + $tag_len,
                        );
                    }
                    // 出栈
                    else {
                        $j--;
                        $data              = array_pop($tmp_stack);
                        $data['inner_end'] = $position;
                        $data['outer_end'] = $position + $tag_len;
                        $data['parent'] = $j;

                        $tmp_result[$i][$j+1] = $data;
                        if ($j == 0) {
                            ksort($tmp_result[$i]);
                            $i++;
                        }
                    }
                    // 截取剩余代码
                    $content = substr($content,$index + $tag_len);
                }
            }
            // 合并处理节点
            $i = 0; unset($content);
            foreach($tmp_result as $key=>$val) {
                foreach($val as $k=>$v) {
                    $n = $k + $i;
                    if ($v['parent'] > 0) {
                        $v['parent']+= $i;
                    }
                    $v['id']    = $n;
                    $result[$n] = $v;
                }
                $i = $k;
            }
            // 将数组转变成树，因为使用了引用，所以不会占用太多的内存
            $un = array();
            foreach ($result as $id => $item) {
                if ($item['parent']) {
                    $result[$item['parent']]['sub'][$id] = &$result[$id];
                    $un[] = $id;
                }
            }
            foreach($un as $v) unset($result[$v]);
        }
        return $result;
    }
    /**
     * 处理标签块
     *
     * @param  $html
     * @return
     */
    function process_blocks($html) {
        $blocks = $this->parse_blocks($html); //print_r($blocks);
        foreach ($blocks as $block) {
            $html = $this->process_block($html,$block);
        }
        return $html;
    }
    function process_block($html, $block) {
        $tag = substr($html, $block['inner_start'], $block['inner_end']-$block['inner_start']);
        if (isset($block['sub'])) {
            foreach ($block['sub'] as $sub) {
                $tag = $this->process_block($tag,$sub);
            }
        }
        $html = substr($html, 0, $block['outer_start']).$tag.substr($html, $block['outer_end']);
        return $html;
    }
    /**
     * 解析变量
     *
     * @param  $html
     * @return mixed
     */
    function process_vars($html){
        if (preg_match_all('/\{\$([\w\.]++)\b[^\}]*+\}/',$html,$r)) {
            $tags = $r[1];
            foreach ($r[0] as $k => $tag) {
                // 不区分大小写
                $tags[$k] = strtolower($tags[$k]);

                // 解析变量
                $value = $this->value($tags[$k]);
                // 应用插件解析
                if (null === $value) {
                    $value = $this->apply_plugins($tags[$k], $tag);
                }
                // 处理变量属性
                $value = $this->process_attr($value, $tag);
                $html  = str_replace($tag, $value, $html);
            }
        }
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
        $tag    = strtolower($tag);
        $result = $value;
        // size
        if (stripos($tag,'size=') !== false) {
            $size = mid($tag,'size="','"');
            if (!$size)
                $size = mid($tag,"size='","'");
            if (validate_is($size,VALIDATE_IS_NUMERIC)) {
                if (intval(mb_strlen($value,'UTF-8')) > intval($size)) {
                    $result = mb_substr($value, 0, $size, 'UTF-8') . '...';
                } else{
                    $result = $value;
                }
            }
        }
        // datemode
        if (is_numeric($value) && stripos($tag,'mode=')!==false) {
            $date = mid($tag,'mode="','"');
            if (!$date)
                $date = mid($tag,"mode='","'");
            if (strlen($date) > 0) {
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
        }
        // code
        if (stripos($tag,'code=') !== false) {
            $code = mid($tag,'code="','"');
            if (!$code)
                $code = mid($tag,"code='","'");
            if (strlen($result) > 0) {
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
        }
        // apply
        if (stripos($tag,'func=') !== false) {
            $func = mid($tag,'func="','"');
            if (!$func)
                $func = mid($tag,"func='","'");
            if (strlen($func) > 0) {
                if (stripos($func,'@me') !== false) {
                    $func = preg_replace("/'@me'|\"@me\"|@me/isU",'$result',$func);
                }
                $result = eval('return '.$func.';');
            }
        }

        return $result;
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
 * @param  $key
 * @param  $val
 * @return array
 */
function tpl_value($key=null,$val=null) {
    $tpl = _tpl_get_object();
    if (func_num_args()==0)
        return $tpl->value();
    return $tpl->value($key, $val);
}
/**
 * 解析模版
 *
 * @param  $html
 * @return mixed
 */
function tpl_parse($html) {
    $tpl  = _tpl_get_object();
    //$html = $tpl->process_blocks($html);
    $html = $tpl->process_vars($html);
    return $html;
}
