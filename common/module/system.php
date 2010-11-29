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

// 注册模版变量处理函数
func_add_callback('tpl_add_plugin', 'system_tpl_plugin');
func_add_callback('tpl_add_plugin', 'system_tpl_list_plugin');
func_add_callback('tpl_add_plugin', 'system_tpl_comments_plugin');
// 添加 CSS
func_add_callback('loader_add_css', array(
    'reset'             => array('/common/css/reset.css'),
    'icons'             => array('/common/css/icons.css'),
    'common'            => array('/common/css/common.css'),
    'style'             => array('/admin/css/style.css', array('reset','icons','common')),
    'admin'             => array('/admin/css/admin.css', array('style')),
    'login'             => array('/admin/css/login.css', array('style')),
    'install'           => array('/admin/css/install.css', array('style')),
    'cpanel'            => array('/admin/css/cpanel.css'),
    'user'              => array('/admin/css/user.css'),
    'model'             => array('/admin/css/model.css'),
    'post'              => array('/admin/css/post.css', array('xheditor.plugins')),
    'publish'           => array('/admin/css/publish.css'),
    'options'           => array('/admin/css/options.css'),
    'comment'           => array('/admin/css/comment.css'),
    'xheditor.plugins'  => array('/common/css/xheditor.plugins.css'),
));
// 添加js
func_add_callback('loader_add_js', array(
    'jquery'            => array('/common/js/jquery.js'),
    'jquery.extend'     => array('/common/js/jquery.extend.js'),
    'lazycms'           => array('/common/js/lazycms.js'),
    'common'            => array('/admin/js/common.js', array('jquery','jquery.extend','lazycms')),
    'login'             => array('/admin/js/login.js'),
    'install'           => array('/admin/js/install.js'),
    'cpanel'            => array('/admin/js/cpanel.js'),
    'user'              => array('/admin/js/user.js'),
    'model'             => array('/admin/js/model.js'),
    'categories'        => array('/admin/js/categories.js'),
    'post'              => array('/admin/js/post.js'),
    'options'           => array('/admin/js/options.js'),
    'publish'           => array('/admin/js/publish.js'),
    'comment'           => array('/admin/js/comment.js'),
    'xheditor'          => array('/common/editor/xheditor.js', array('xheditor.plugins')),
    'xheditor.plugins'  => array('/common/js/xheditor.plugins.js'),
));
// 系统权限
func_add_callback('system_purview_add', array(
    'cpanel' => array(
        '_LABEL_'           => __('Control Panel'),
        'publish'           => __('Publish Posts'),
        'upgrade'           => __('Upgrade'),
    ),
    'posts' => array(
        '_LABEL_'           => __('Posts'),
        'categories'        => __('Categories'),
        'post-new'          => _x('Add New','post'),
        'post-list'         => _x('List','post'),
        'post-edit'         => _x('Edit','post'),
        'post-delete'       => _x('Delete','post'),
    ),
    'pages' => array(
        '_LABEL_'           => __('Pages'),
        'page-list'         => _x('List','page'),
        'page-new'          => _x('Add New','page'),
        'page-edit'         => _x('Edit','page'),
        'page-delete'       => _x('Delete','page'),
    ),
    'models' => array(
        '_LABEL_'           => __('Models'),
        'model-list'        => _x('List','model'),
        'model-new'         => _x('Add New','model'),
        'model-edit'        => _x('Edit','model'),
        'model-delete'      => _x('Delete','model'),
        'model-export'      => _x('Export','model'),
        'model-fields'      => _x('Fields','model'),
    ),
    'comments' => array(
        '_LABEL_'           => __('Comments'),
        'comment-list'      => _x('List','comment'),
        'comment-state'     => _x('Change State','comment'),
        'comment-reply'     => _x('Reply comment','comment'),
        'comment-edit'      => _x('Edit','comment'),
        'comment-delete'    => _x('Delete','comment'),
    ),
    'users' => array(
        '_LABEL_'           => __('Users'),
        'user-list'         => _x('List','user'),
        'user-new'          => _x('Add New','user'),
        'user-edit'         => _x('Edit','user'),
        'user-delete'       => _x('Delete','user'),
    ),
    /*'plugins' => array(
        '_LABEL_'           => __('Plugins'),
        'plugin-list'       => _x('List','plugin'),
        'plugin-new'        => _x('Add New','plugin'),
        'plugin-delete'     => _x('Delete','plugin'),
    ),*/
    'settings' => array(
        '_LABEL_'           => __('Settings'),
        'option-general'    => _x('General','setting'),
        'option-posts'      => _x('Posts','setting'),
    )
));

/**
 * 处理模版变量
 *
 * @param  $tag_name
 * @param  $tag
 * @return mixed    null 说明没有解析成功，会继续
 */
function system_tpl_plugin($tag_name,$tag) {
    switch ($tag_name) {
        case '$sitename':
            $result = C('SiteTitle');
            break;
        case '$inst': case '$webroot':
            $result = ROOT;
            break;
        case '$host': case '$domain':
            $result = HTTP_HOST;
            break;
        case '$ver': case '$version':
            $result = LAZY_VERSION;
            break;
        case '$theme': case '$templet': case '$template':
            $result = ROOT.system_themes_path();
            break;
        case '$lang': case '$language':
            $result = C('Language');
            break;
        case '$cms': case '$lazycms':
            $result = '<span id="lazycms">Powered by: <a href="http://lazycms.com/" style="font-weight:bold" target="_blank" title="LazyCMS">LazyCMS</a> '.LAZY_VERSION.'</span>';
            break;
        case '$guide':
            $name  = tpl_get_attr($tag,'name');
            $guide = tpl_get_var('guide');
            if (!$name) $name = __('Home');
            $name = esc_html($name);
            if ($guide) {
                $result = '<a href="'.ROOT.'" title="'.$name.'">'.$name.'</a> &gt;&gt; '.$guide;
            } else {
                $result = '<a href="'.ROOT.'" title="'.$name.'">'.$name.'</a> &gt;&gt; '.tpl_get_var('title');
            }
            break;
        case '$jquery':
            $version = tpl_get_attr($tag,'ver');
            $version = $version ? $version : '1.4.2';
            $result  = 'http://ajax.googleapis.com/ajax/libs/jquery/'.$version.'/jquery.min.js';
            break;
        case '$keywords': case '$keyword':
            $result = tpl_get_var('keywords');
            break;
        case '$description':
            $result = tpl_get_var('description');
            if (!$result) $result = tpl_get_var('title');
            break;
        case '$content':
            // 关键词链接地址
            $link = tpl_get_attr($tag,'link');
            if ($link) {
                $link = str_replace(array('[$inst]','[$webroot]'),ROOT,$link);
                $link = str_replace(array('[$host]','[$domain]'),HTTP_HOST,$link);
                $link = str_replace(array('[$theme]','[$templet]','[$template]'),ROOT.system_themes_path(),$link);
                // 关键词匹配最大数
                $number = tpl_get_attr($tag,'tags');
                if (strpos($number,'-') !== false) {
                    $range = explode('-', trim($number,'-')); sort($range);
                    $tag_min = $range[0]; $tag_max = $range[1];
                } else {
                    $tag_min = $tag_max = $number;
                }
                $tag_min = validate_is($tag_min,VALIDATE_IS_NUMERIC) ? $tag_min : 10;
                $tag_max = validate_is($tag_max,VALIDATE_IS_NUMERIC) ? $tag_max : 10;
                // 文章内容
                $content = tpl_get_var('content');
                if ($content) {
                    $dicts = term_gets();
                    if (!empty($dicts)) {
                        require_file(COM_PATH.'/system/keyword.php');
                        $splitword = new keyword($dicts);
                        $content   = $splitword->tags($content, $link, mt_rand($tag_min,$tag_max));
                    }
                }
                $result = $content;
            } else {
                $result = null;
            }
            break;
        default:
            $result = null;
            break;
    }
    return $result;
}
/**
 * 处理列表
 *
 * @param  $tag_name
 * @param  $tag
 * @return
 */
function system_tpl_list_plugin($tag_name,$tag,$block) {
    if (!instr($tag_name,'post,list')) return null;
    // 实例化模版对象
    $tpl = new Template();
    // 列表类型
    $type = $tpl->get_attr($tag,'type');
    // 类型为必填
    if (!$type) return null;
    // 分类ID
    $sortid = $tpl->get_attr($tag,'sortid');
    // 显示条数
    $number = $tpl->get_attr($tag,'number');
    // 斑马线实现
    $zebra  = $tpl->get_attr($tag,'zebra');
    // 校验数据
    $sortid = validate_is($sortid,VALIDATE_IS_LIST) ? $sortid : null;
    $number = validate_is($number,VALIDATE_IS_NUMERIC) ? $number : 10;
    $zebra  = validate_is($zebra,VALIDATE_IS_NUMERIC) ? $zebra : 0;
    // 处理
    switch ($type) {
        case 'new': case 'hot': case 'chill':
            $sortid = $sortid===null ? tpl_get_var('sortid') : $sortid;
            $where  = $sortid ? " AND `tr`.`taxonomyid` IN({$sortid})" : '';
            switch ($type) {
                case 'new'  : $order = 'ORDER BY `p`.`postid` DESC'; break;
                case 'hot'  : $order = 'ORDER BY `p`.`views` DESC, `p`.`postid` DESC'; break;
                case 'chill': $order = 'ORDER BY `p`.`views` ASC, `p`.`postid` DESC'; break;
            }
            $sql = sprintf("SELECT DISTINCT(`p`.`postid`) FROM `#@_post` AS `p` RIGHT JOIN `#@_term_relation` AS `tr` ON `p`.`postid`=`tr`.`objectid` WHERE `p`.`type`='post' %s %s LIMIT %d;",$where,$order,$number);
            break;
        case 'related':
            $_keywords = tpl_get_var('_keywords');
            if ($_keywords) {
                $postid = tpl_get_var('postid');
                $ids = implode(',', $_keywords);
                $sql = sprintf("SELECT DISTINCT(`p`.`postid`) FROM `#@_post` AS `p` RIGHT JOIN `#@_term_relation` AS `tr` ON `p`.`postid`=`tr`.`objectid` WHERE `p`.`type`='post' AND `tr`.`taxonomyid` IN(%s) AND `p`.`postid`<>%d LIMIT %d;",$ids,$postid,$number);
            } else {
                $sql = null;
            }
            break;
        default:
            $sql = null;
    }
    
    $result = null;
    if ($sql) {
        $db = get_conn(); $i = 0;
        $rs = $db->query($sql);
        $inner = $tpl->get_block_inner($block);
        while ($data = $db->fetch($rs)) {
            $post = post_get($data['postid']);
            $post['sort'] = taxonomy_get($post['sortid']);
            $post['path'] = post_get_path($post['sortid'],$post['path']);
            $post['keywords'] = post_get_keywords($post['keywords']);
            // 文章内容
            if ($post['content'] && strpos($post['content'],'<!--pagebreak-->')!==false) {
                $contents = explode('<!--pagebreak-->', $post['content']);
                $content  = array_shift($contents);
            } else {
                $content  = $post['content'];
            }
            // 设置文章变量
            $vars = array(
                'zebra'    => ($i % ($zebra + 1)) ? '0' : '1',
                'postid'   => $post['postid'],
                'sortid'   => $post['sortid'],
                'userid'   => $post['userid'],
                'author'   => $post['author'],
                'views'    => '<script type="text/javascript" src="'.ROOT.'common/gateway.php?func=post_views&postid='.$post['postid'].'"></script>',
                'digg'     => $post['digg'],
                'title'    => $post['title'],
                'path'     => ROOT.$post['path'],
                'content'  => $content,
                'date'     => $post['datetime'],
                'edittime' => $post['edittime'],
                'keywords' => $post['keywords'],
                'description' => $post['description'],
            );
            // 设置分类变量
            if (isset($post['sort'])) {
                $vars['sortname'] = $post['sort']['name'];
                $vars['sortpath'] = ROOT.$post['sort']['path'].'/';
            }
            $tpl->clean();
            $tpl->set_var($vars);
            // 设置自定义字段
            if (isset($post['meta'])) {
                foreach((array)$post['meta'] as $k=>$v) {
                    $tpl->set_var('model.'.$k, $v);
                }
            }
            // 解析二级内嵌标签
            if (isset($block['sub'])) {
                foreach ($block['sub'] as $sblock) {
                    $sblock['name'] = strtolower($sblock['name']);
                    switch($sblock['name']) {
                        // TODO 解析图片标签
                        case 'images':
                            $inner = str_replace($sblock['tag'],'',$inner);
                            break;
                    }
                }
            }
            $result.= $tpl->parse($inner);
            $i++;
        }
    }
    return $result;
}

function system_tpl_comments_plugin($tag_name,$tag,$block) {
    if (!instr($tag_name,'comment,comments')) return null;
    // 实例化模版对象
    $tpl = new Template();
    // 列表类型
    $type = $tpl->get_attr($tag,'type');
    // 类型为必填
    if (!$type) return null;
    // 显示条数
    $number = $tpl->get_attr($tag,'number');
    // 斑马线实现
    $zebra  = $tpl->get_attr($tag,'zebra');
    $number = validate_is($number,VALIDATE_IS_NUMERIC) ? $number : 10;
    $zebra  = validate_is($zebra,VALIDATE_IS_NUMERIC) ? $zebra : 0;
    // 处理类型
    switch($type) {
        case 'new':
            $postid = $tpl->get_var('postid');
            $insql  = $postid ? sprintf(" AND `postid`=%d", esc_sql($postid)) : '';
            $sql    = "SELECT * FROM `#@_comments` WHERE `approved`='1' {$insql} ORDER BY `cmtid` DESC LIMIT {$number};";

            break;
        default:
            $sql = null;
    }

    $result = null;
    if ($sql) {
        $db = get_conn(); $i = 0;
        $rs = $db->query($sql);
        $inner = $tpl->get_block_inner($block);
        while ($data = $db->fetch($rs)) {
            $data['ip']      = long2ip($data['ip']);
            $data['ipaddr']  = ip2addr($data['ip']);
            if ($data['ip'] == $data['ipaddr']) {
                $data['ip'] = substr_replace($data['ip'], '*', strrpos($data['ip'], '.')+1);
                $data['ipaddr'] = $data['ip'];
            } else {
                $data['ip'] = substr_replace($data['ip'], '*', strrpos($data['ip'], '.')+1);
            }
            $tpl->clean();
            if ($post = post_get($data['postid'])) {
                $tpl->set_var('path', ROOT.post_get_path($post['sortid'], $post['path']));
            }
            $tpl->set_var(array(
                'zebra'   => ($i % ($zebra + 1)) ? '0' : '1',
                'cmtid'   => $data['cmtid'],
                'avatar'  => get_avatar($data['mail'], 16, 'mystery'),
                'author'  => $data['author'] ? $data['author'] : __('Anonymous'),
                'email'   => $data['mail'],
                'url'     => !strncmp($data['url'],'http://',7) ? $data['url'] : 'http://'.$data['url'],
                'ip'      => $data['ip'],
                'address' => $data['ipaddr'],
                'content' => nl2br($data['content']),
                'agent'   => $data['agent'],
                'date'    => $data['date'],
            ));
            $result.= $tpl->parse($inner);
            $i++;
        }
    }
    
    return $result;
}
/**
 * 标签
 *
 * @param string $tag
 * @return mixed
 */
function system_tags($tag) {
    $db = get_conn();
    $inner  = $b_guid = '';
    // 载入模版
    $html   = tpl_loadfile(ABS_PATH.'/'.system_themes_path().'/'.esc_html(C('Template-Tags')));
    // 标签块信息
    $block  = tpl_get_block($html,'post,list','list');
    if ($tag && $block) {
        // 每页条数
        $number = tpl_get_attr($block['tag'],'number');
        // 排序方式
        $order  = tpl_get_attr($block['tag'],'order');
        // 斑马线实现
        $zebra  = tpl_get_attr($block['tag'],'zebra');
        // 校验数据
        $zebra  = validate_is($zebra,VALIDATE_IS_NUMERIC) ? $zebra : 0;
        $number = validate_is($number,VALIDATE_IS_NUMERIC) ? $number : 10;
        $order  = instr(strtoupper($order),'ASC,DESC') ? $order : 'DESC';
        // 设置每页显示数
        pages_init($number);
        // 显示Tags相关的文章列表
        $term   = term_get_byname($tag);
        $tid    = $db->result(sprintf("SELECT `taxonomyid` FROM `#@_term_taxonomy` WHERE `type`='post_tag' AND `termid`=%d", esc_sql($term['termid'])));
        $sql    = sprintf("SELECT DISTINCT(`p`.`postid`) FROM `#@_post` AS `p` RIGHT JOIN `#@_term_relation` AS `tr` ON `p`.`postid`=`tr`.`objectid` WHERE `p`.`type`='post' AND `tr`.`taxonomyid`=%d ORDER BY `p`.`postid` %s", $tid, $order);
        $result = pages_query($sql);
        // 解析分页标签
        if (stripos($html,'{pagelist') !== false) {
            $html = preg_replace('/\{(pagelist)[^\}]*\/\}/isU',
                pages_list(PHP_FILE.'?q='.rawurlencode($tag).'&page=$'),
                $html
            );
        }
        // 数据存在
        if ($result) {
            $i = 0;
            // 取得标签块内容
            $block['inner'] = tpl_get_block_inner($block);
            while ($data = pages_fetch($result)) {
                $post = post_get($data['postid']);
                if (empty($post)) continue;
                $post['sort'] = taxonomy_get($post['sortid']);
                $post['path'] = post_get_path($post['sortid'],$post['path']);
                // 文章内容
                if ($post['content'] && strpos($post['content'],'<!--pagebreak-->')!==false) {
                    $contents = explode('<!--pagebreak-->', $post['content']);
                    $content  = array_shift($contents);
                } else {
                    $content  = $post['content'];
                }
                // 设置文章变量
                $vars = array(
                    'zebra'    => ($i % ($zebra + 1)) ? '0' : '1',
                    'postid'   => $post['postid'],
                    'sortid'   => $post['sortid'],
                    'userid'   => $post['userid'],
                    'author'   => $post['author'],
                    'title'    => $post['title'],
                    'views'    => '<script type="text/javascript" src="'.ROOT.'common/gateway.php?func=post_views&postid='.$post['postid'].'"></script>',
                    'digg'     => $post['digg'],
                    'path'     => ROOT.$post['path'],
                    'content'  => $content,
                    'datetime' => $post['datetime'],
                    'edittime' => $post['edittime'],
                    'keywords' => $post['keywords'],
                    'description' => $post['description'],
                );
                // 设置分类变量
                if (isset($post['sort'])) {
                    $vars['sortname'] = $post['sort']['name'];
                    $vars['sortpath'] = ROOT.$post['sort']['path'].'/';
                }
                // 清理数据
                tpl_clean();
                tpl_set_var($vars);
                // 设置自定义字段
                if (isset($post['meta'])) {
                    foreach((array)$post['meta'] as $k=>$v) {
                        tpl_set_var('model.'.$k, $v);
                    }
                }
                // 解析二级内嵌标签
                if (isset($block['sub'])) {
                    foreach ($block['sub'] as $sblock) {
                        $sblock['name'] = strtolower($sblock['name']);
                        switch($sblock['name']) {
                            // TODO 解析图片标签
                            case 'images':
                                $block['inner'] = str_replace($sblock['tag'],'',$block['inner']);
                                break;
                        }
                    }
                }

                // 解析变量
                $inner.= tpl_parse($block['inner']); $i++;
            }
        } else {
            $inner = __('No record!');
        }
        // 生成标签块的唯一ID
        $b_guid = guid($block['tag']);
        // 把标签块替换成变量标签
        $html   = str_replace($block['tag'], '{$'.$b_guid.'}', $html);
        // 清理模版内部变量
        tpl_clean();
        tpl_set_var($b_guid,$inner);
        tpl_set_var(array(
            'guide'    => 'Tags &gt;&gt; '.$tag,
            'title'    => $tag,
            'keywords' => $tag,
        ));
    } else {
        if (stripos($html,'{pagelist') !== false) {
            $html = preg_replace('/\{(pagelist)[^\}]*\/\}/isU', '', $html);
        }
        tpl_clean();
        tpl_set_var(array(
            'title'    => 'Tags',
            'keywords' => 'Tags',
        ));
    }
    return tpl_parse($html);    
}

if (!function_exists('system_category_guide')) :
/**
 * 生成导航
 *
 * @param int $sortid
 * @return string
 */
function system_category_guide($sortid) {
    if (empty($sortid)) return ; $result = '';
    if ($taxonomy = taxonomy_get($sortid)) {
        $result = '<a href="'.ROOT.$taxonomy['path'].'/" title="'.esc_html($taxonomy['name']).'">'.esc_html($taxonomy['name']).'</a>';
        if ($taxonomy['parent']) {
            $result = system_category_guide($taxonomy['parent'])." &gt;&gt; ".$result;
        }
    }
    return $result;
}
endif;
/**
 * 查询模版路径
 *
 * @return string
 */
function system_themes_path() {
    return TEMPLATE.'/'.C('Template');
}


/**
 * 设置head变量
 *
 * @param string $key
 * @param mixed $value
 * @return mixed
 */
function system_head($key,$value=null) {
    static $head = array();
    // 赋值
    if (!is_null($value)) {
    	$head[$key] = $value;
    }
    return isset($head[$key])?$head[$key]:array();
}
/**
 * 添加权限
 *
 * @param array $purview
 * @return array
 */
function system_purview_add($purview) {
    global $LC_Purview;
    
    if (!$LC_Purview)
        $LC_Purview = array();

    if (is_array($purview)) {
        foreach ($purview as $key=>$val) {
            $LC_Purview[$key] = $val;
        }
    } else {
        $args = func_get_args();
        $key  = $args[0];
        $val  = $args[1];
        $LC_Purview[$key] = $val;

    }
    return $LC_Purview;
}
/**
 * 权限列表
 *
 * @return array
 */
function system_purview($data=null) {
    global $LC_Purview;
    $hl = '<div class="role-list">';
    foreach ((array) $LC_Purview as $k=>$pv) {
        $title = $pv['_LABEL_']; unset($pv['_LABEL_']);
        $roles = null; $parent_checked = ' checked="checked"';
        foreach ($pv as $sk=>$spv) {
            if ($data == 'ALL') {
                $checked = ' checked="checked"';
            } else {
                $checked = instr($sk, $data)?' checked="checked"':null;
            }
            $parent_checked = empty($checked)?'':$parent_checked;
        	$roles.= '<label><input type="checkbox" name="roles[]" rel="'.$k.'" value="'.$sk.'"'.$checked.' /> '.$spv.'</label>';
        }
        $hl.= '<p><label><input type="checkbox" name="parent[]" class="parent-'.$k.'" value="'.$k.'"'.$parent_checked.' /> <strong>'.$title.'</strong></label><br/>'.$roles.'</p>';
    }
    $hl.= '</div>';
    return $hl;
}
/**
 * 添加菜单
 *
 * @param string|array $menus
 * @return array
 */
function system_menu_add($menus) {
    global $LC_system_menus;

    if (!$LC_system_menus)
        $LC_system_menus = array();

    if (is_array($menus)) {
        foreach ($menus as $key=>$val) {
            $LC_system_menus[$key] = $val;
        }
    } else {
        $args = func_get_args();
        $key  = $args[0];
        $val  = $args[1];
        $LC_system_menus[$key] = $val;

    }
    return $LC_system_menus;
}
/**
 * 输出后台菜单
 *
 * @param  $menus
 * @return bool
 */
function system_menu($menus) {
    global $parent_file,$_USER,$LC_system_menus;
    // 获取管理员信息
    if (!isset($_USER)) $_USER = user_current(false);
    // 自动植入配置
    $is_first = true; $is_last = false;
    // 设置默认参数
    if (!empty($_SERVER['QUERY_STRING'])) {
        parse_str($_SERVER['QUERY_STRING'],$query);
        if (!isset($query['method'])) {
            $query = array_merge(array('method' => 'default'),$query);
        }
        $query = '?'.http_build_query($query);
    } else {
        $query = '?method=default';
    }
    if (!isset($parent_file)) {
    	$parent_file = PHP_FILE.$query;
    } else {
        $parent_file = ADMIN.(strpos($parent_file,'?')!==false ? $parent_file : $parent_file.'?method=default');
    }
    // 插入菜单
    if ($LC_system_menus) {
        $i = $j = 0;
        foreach($menus as $k=>$v) {
            if ($j >= 2) break; $i++;
            if (is_string($v)) $j++;
        }
        reset($menus);
        array_ksplice($menus, $i-1, 0, '-');
        array_ksplice($menus, $i, 0, $LC_system_menus);
        unset($i, $j);
    }

    $menus_tree = array();
    // 预处理菜单
    while (list($k,$menu) = each($menus)) {
        if (is_array($menu)) {
            $submenus = array(); $is_expand = $has_submenu = false; $has_view = true;
            if (!empty($menu[3]) && is_array($menu[3])) {
                $has_submenu = true;
                foreach ($menu[3] as $href) {
                    // 文件不存在，菜单也不能出现
                    if (!is_file(ADMIN_PATH.'/'.parse_url($href[1],PHP_URL_PATH))) continue;
                    $href[1]   = ADMIN.$href[1];
                    $url_query = strpos($href[1],'?')!==false?$href[1]:$href[1].'?method=default';
                    $href[3]   = !strncasecmp($parent_file,$url_query,strlen($url_query))?true:false;
                    $is_expand = !strncasecmp($parent_file,$url_query,strlen($url_query))?true:$is_expand;

                    // 子菜单需要权限才能访问，且用户要有权限
                    if (isset($href[2]) && (instr($href[2],$_USER['roles']) || $_USER['roles']=='ALL')) {
                        $submenus[] = $href;
                    }
                    // 子菜单存在，不需要权限
                    elseif (empty($href[2])) {
                        $submenus[] = $href;
                    }
                }
            }
            // 没有子菜单
            else {
                // 文件存在
                if (!is_file(ADMIN_PATH.'/'.parse_url($menu[1],PHP_URL_PATH))
                    || (isset($menu[3]) && ($_USER['roles']!='ALL' && !instr($menu[3],$_USER['roles'])))) {
                    $has_view = false;
                }
            }

            // 存在子菜单，并且子菜单不为空，或者没有子菜单
            if ($has_submenu && !empty($submenus) || $has_view && !$has_submenu) {
                $menu[1] = ADMIN.$menu[1];
                $current = !strncasecmp($parent_file,$menu[1],strlen($menu[1])) || $is_expand ? ' current' : '';
                $expand  = empty($submenus) || empty($current) ? '' : ' expand';
                $menu = array(
                    'text' => $menu[0],
                    'link' => $menu[1],
                    'icon' => $menu[2],
                    'current'  => $current,
                    'expand'   => $expand,
                    'submenus' => $submenus,
                );
                $menus_tree[$k] = $menu;
            }
        } else {
            $menus_tree[] = $menu;
        }
    }

    // 循环所有的菜单
    while (list($k,$menu) = each($menus_tree)) {
        // 数组是菜单
        if (is_array($menu)) {
            $is_last = is_array(current($menus_tree)) ? $is_last : true; $class = '';
            if ($is_first) $class.= ' first';
            if ($is_last)  $class.= ' last';
            echo '<li id="menu-'.$k.'" class="head'.$class.$menu['current'].$menu['expand'].'">';
            echo '<a href="'.$menu['link'].'" class="image">'.get_icon($menu['icon']).'</a>';
            echo '<a href="'.$menu['link'].'" class="text'.$class.'">'.$menu['text'].'</a>';
            // 展示子菜单
            if (!empty($menu['submenus'])) {
                echo '<a href="javascript:;" class="toggle"><br/></a>';
                echo '<div class="sub">';
                echo '<dl>';
                echo '<dt>'.$menu['text'].'</dt>';
                foreach ($menu['submenus'] as $submenu) {
                    $current = $submenu[3]?' class="current"':null;
                    echo '<dd'.$current.'><a href="'.$submenu[1].'">'.$submenu[0].'</a></dd>';
                }
                echo '</dl>';
                echo '</div>';
            }
            echo '</li>';
            $is_first = false; $separator = true;
        }
        // 否则是分隔符
        elseif($separator) {
            echo '<li class="separator"><a href="javascript:;"><br/></a></li>';
            $is_first = true; $is_last = $separator = false;
        }
    }

    return true;
}
/**
 * js公共语言包
 *
 * @return string
 */
function system_jslang() {
    // js语言包
    $js_lang = array_merge(array(
        'System Error' => __('System Error'),
        'Alert'     => __('Alert'),
        'Submit'    => __('Submit'),
        'Confirm'   => __('Confirm'),
        'Cancel'    => __('Cancel'),
        'Save'      => __('Save'),
        'Close'     => __('Close'),
        'Edit'      => __('Edit'),
        'Delete'    => __('Delete'),
        'Search'    => __('Search'),
        'Address:'    => __('Address:'),
        'Insert Map'    => __('Insert Map'),
        'No record!'        => __('No record!'),
        'Confirm Logout?'   => __('Confirm Logout?'),
        'Confirm Delete?'   => __('Confirm Delete?'),
        'Latest Version:'   => __('Latest Version:'),
        'Did not select any action!' => __('Did not select any action!'),
        // 密码强度
        'Strength indicator' => __('Strength indicator'),
        'Very weak'          => __('Very weak'),
        'Weak'               => __('Weak'),
        'Medium'             => __('Medium'),
        'Strong'             => __('Strong'),
        'Mismatch'           => __('Mismatch'),

    ),system_head('jslang'));
    return sprintf('$.extend(LazyCMS.L10n,%s);',json_encode($js_lang));
}
/**
 * 编辑器语言
 *
 * @return array
 */
function system_editor_lang() {
    return array(
        'Cancel'    => __('Cancel'),
        'Paragraph' => __('Paragraph'),
        'Heading 1' => __('Heading 1'),
        'Heading 2' => __('Heading 2'),
        'Heading 3' => __('Heading 3'),
        'Heading 4' => __('Heading 4'),
        'Heading 5' => __('Heading 5'),
        'Heading 6' => __('Heading 6'),
        'Preformatted' => __('Preformatted'),
        'Address' => __('Address'),
        'xx-small' => __('xx-small'),
        'x-small' => __('x-small'),
        'small' => __('small'),
        'medium' => __('medium'),
        'large' => __('large'),
        'x-large' => __('x-large'),
        'xx-large' => __('xx-large'),
        'Align left' => __('Align left'),
        'Align center' => __('Align center'),
        'Align right' => __('Align right'),
        'Align full' => __('Align full'),
        'Ordered list' => __('Ordered list'),
        'Unordered list' => __('Unordered list'),
        'Use Ctrl+V on your keyboard to paste the text.' => __('Use Ctrl+V on your keyboard to paste the text.'),
        'Ok' => __('Ok'),
        'Flv URL:' => __('Flv URL:'),
        'Link URL:' => __('Link URL:'),
        'Target:&nbsp;&nbsp;' => __('Target:&nbsp;&nbsp;'),
        'Link Text:' => __('Link Text:'),
        'Img URL:&nbsp;' => __('Img URL:&nbsp;'),
        'Alt text:' => __('Alt text:'),
        'Alignment:' => __('Alignment:'),
        'Dimension:' => __('Dimension:'),
        'Border:&nbsp;&nbsp;&nbsp;' => __('Border:&nbsp;&nbsp;&nbsp;'),
        'Hspace:&nbsp;&nbsp;&nbsp;' => __('Hspace:&nbsp;&nbsp;&nbsp;'),
        'Vspace:' => __('Vspace:'),
        'Flash URL:' => __('Flash URL:'),
        'Media URL:' => __('Media URL:'),
        'Rows&Cols:&nbsp;&nbsp;' => __('Rows&Cols:&nbsp;&nbsp;'),
        'Headers:&nbsp;&nbsp;&nbsp;&nbsp;' => __('Headers:&nbsp;&nbsp;&nbsp;&nbsp;'),
        'CellSpacing:' => __('CellSpacing:'),
        'CellPadding:' => __('CellPadding:'),
        'Caption:&nbsp;&nbsp;&nbsp;&nbsp;' => __('Caption:&nbsp;&nbsp;&nbsp;&nbsp;'),
        'Border:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' => __('Border:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'),
        'Default' => __('Default'),
        'New window' => __('New window'),
        'Same window' => __('Same window'),
        'Parent window' => __('Parent window'),
        'Left' => _x('Left','xhe'),
        'Right' => _x('Right','xhe'),
        'Top' => _x('Top','xhe'),
        'Middle' => _x('Middle','xhe'),
        'Center' => _x('Center','xhe'),
        'Baseline' => _x('Baseline','xhe'),
        'Bottom' => _x('Bottom','xhe'),
        'None' => __('None'),
        'First row' => __('First row'),
        'First column' => __('First column'),
        'Both' => __('Both'),
        'xhEditor is a platform independent WYSWYG XHTML editor based by jQuery,released as Open Source under <a href="http://www.gnu.org/licenses/lgpl.html" target="_blank">LGPL</a>.' => __('xhEditor is a platform independent WYSWYG XHTML editor based by jQuery,released as Open Source under <a href="http://www.gnu.org/licenses/lgpl.html" target="_blank">LGPL</a>.'),
        'Smile' => __('Smile'),
        'Tongue' => __('Tongue'),
        'Titter' => __('Titter'),
        'Laugh' => __('Laugh'),
        'Sad' => __('Sad'),
        'Wronged' => __('Wronged'),
        'Fast cry' => __('Fast cry'),
        'Cry' => __('Cry'),
        'Wail' => __('Wail'),
        'Mad' => __('Mad'),
        'Knock' => __('Knock'),
        'Curse' => __('Curse'),
        'Crazy' => __('Crazy'),
        'Angry' => __('Angry'),
        'Oh my' => __('Oh my'),
        'Awkward' => __('Awkward'),
        'Panic' => __('Panic'),
        'Shy' => __('Shy'),
        'Cute' => __('Cute'),
        'Envy' => __('Envy'),
        'Proud' => __('Proud'),
        'Struggle' => __('Struggle'),
        'Quiet' => __('Quiet'),
        'Shut up' => __('Shut up'),
        'Doubt' => __('Doubt'),
        'Despise' => __('Despise'),
        'Sleep' => __('Sleep'),
        'Bye' => __('Bye'),
        'Cut (Ctrl+X)' => __('Cut (Ctrl+X)'),
        'Copy (Ctrl+C)' => __('Copy (Ctrl+C)'),
        'Paste (Ctrl+V)' => __('Paste (Ctrl+V)'),
        'Paste as plain text' => __('Paste as plain text'),
        'Block tag' => __('Block tag'),
        'Font family' => __('Font family'),
        'Font size' => __('Font size'),
        'Bold (Ctrl+B)' => __('Bold (Ctrl+B)'),
        'Italic (Ctrl+I)' => __('Italic (Ctrl+I)'),
        'Underline (Ctrl+U)' => __('Underline (Ctrl+U)'),
        'Strikethrough (Ctrl+S)' => __('Strikethrough (Ctrl+S)'),
        'Select text color' => __('Select text color'),
        'Select background color' => __('Select background color'),
        'SelectAll (Ctrl+A)' => __('SelectAll (Ctrl+A)'),
        'Remove formatting' => __('Remove formatting'),
        'Align' => __('Align'),
        'List' => __('List'),
        'Outdent (Shift+Tab)' => __('Outdent (Shift+Tab)'),
        'Indent (Tab)' => __('Indent (Tab)'),
        'Insert/edit link (Ctrl+K)' => __('Insert/edit link (Ctrl+K)'),
        'Unlink' => __('Unlink'),
        'Insert/edit image' => __('Insert/edit image'),
        'Insert/edit flash' => __('Insert/edit flash'),
        'Insert/edit media' => __('Insert/edit media'),
        'Insert Flv Video' => __('Insert Flv Video'),
        'Insert Pagebreak' => __('Insert Pagebreak'),
        'Insert Google map' => __('Insert Google map'),
        'Google Maps' => __('Google Maps'),
        'Remove external links' => __('Remove external links'),
        'Emotions' => __('Emotions'),
        'Insert a new table' => __('Insert a new table'),
        'Edit source code' => __('Edit source code'),
        'Preview' => __('Preview'),
        'Print (Ctrl+P)' => __('Print (Ctrl+P)'),
        'Toggle fullscreen (Esc)' => __('Toggle fullscreen (Esc)'),
        'About xhEditor' => __('About xhEditor'),
        'Click to open link' => __('Click to open link'),
        'Current textarea is hidden, please make it show before initialization xhEditor, or directly initialize the height.' => __('Current textarea is hidden, please make it show before initialization xhEditor, or directly initialize the height.'),
        'Upload file extension required for this: ' => __('Upload file extension required for this: '),
        'You can only drag and drop the same type of file.' => __('You can only drag and drop the same type of file.'),
        'File uploading,please wait...' => __('File uploading,please wait...'),
        'Please do not upload more then {$upMultiple} files.' => __('Please do not upload more then {$upMultiple} files.'),
        'File uploading(Esc cancel)' => __('File uploading(Esc cancel)'),
        ' upload interface error!' => __(' upload interface error!'),
        'return error:' => __('return error:'),
        'Close (Esc)' => __('Close (Esc)'),
        'Currently not supported by your browser, use keyboard shortcuts(Ctrl+X) instead.' => __('Currently not supported by your browser, use keyboard shortcuts(Ctrl+X) instead.'),
        'Currently not supported by your browser, use keyboard shortcuts(Ctrl+C) instead.' => __('Currently not supported by your browser, use keyboard shortcuts(Ctrl+C) instead.'),
        'Currently not supported by your browser, use keyboard shortcuts(Ctrl+V) instead.' => __('Currently not supported by your browser, use keyboard shortcuts(Ctrl+V) instead.'),
        'Upload file extension required for this: ' => __('Upload file extension required for this: '),
    );
}
/**
 * 取得PHPINFO
 *
 * @param int $info
 * @return mixed|string
 */
function system_phpinfo($info = INFO_ALL) {
    /**
     * callback function to eventually add an extra space in passed <td class="v">...</td>
     * after a ";" or "@" char to let the browser split long lines nicely
     */
    function _system_phpinfo_v_callback($matches) {
        $matches[2] = preg_replace('/(?<!\s)([;@])(?!\s)/', "$1 ", $matches[2]);
        return $matches[1] . $matches[2] . $matches[3];
    }
    ob_start();
    phpinfo($info);
    $output = preg_replace(array('/^.*<body[^>]*>/is', '/<\/body[^>]*>.*$/is'), '', ob_get_clean(), 1);

    $output = preg_replace('/width="[0-9]+"/i', 'width="100%"', $output);
    $output = str_replace('<table border="0" cellpadding="3" width="100%">', '<table class="phpinfo">', $output);
    $output = str_replace('<hr />', '', $output);
    $output = str_replace('<tr class="h">', '<tr>', $output);
    $output = str_replace('<a name=', '<a id=', $output);
    $output = str_replace('<font', '<span', $output);
    $output = str_replace('</font', '</span', $output);
    $output = str_replace(',', ', ', $output);
    // match class "v" td cells an pass them to callback function
    return preg_replace_callback('%(<td class="v">)(.*?)(</td>)%i', '_system_phpinfo_v_callback', $output);
}

/**
 * rewrite
 *
 * @return mixed|string
 */
function system_gateway_rewrite() {
    // 获取参数
    $path   = isset($_GET['path']) ? substr('/'.rtrim(trim($_GET['path']),'/'), strlen(ROOT)) : null;
    $paths  = parse_path($path);
    $type   = isset($paths[0]) ? strtolower($paths[0]) : null;
    $result = '';
    switch($type) {
        case 'tags':
            $result = system_tags($_GET[$type]);
            break;
        default:
            header('HTTP/1.1 404 Not Found', true, 404);
            redirect(ROOT, 3, __('Restricted access!'));
            break;
    }
    return $result;
}
