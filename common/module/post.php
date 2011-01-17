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
/**
 * 添加文章
 *
 * @param  $title
 * @param  $content
 * @param  $path
 * @param  $data
 * @return array
 */
function post_add($title,$content,$path,$data=null) {
    $db = get_conn(); 
    $postid = $db->insert('#@_post',array(
        'title'    => $title,
        'content'  => $content,
        'path'     => $path,
        'type'     => 'post',
        'approved' => 'passed',
        'datetime' => time(),
        'edittime' => time(),
    ));
    $data['path'] = $path;
    return post_edit($postid,$data);
}
/**
 * 更新文章信息
 *
 * @param int $postid
 * @param array $data
 * @return array
 */
function post_edit($postid,$data) {
    $db = get_conn();
    $postid = intval($postid);
    $post_rows = $meta_rows = array();
    if ($post = post_get($postid)) {
        $tpl  = tpl_init('page-404');
        $data = is_array($data) ? $data : array();
        // 格式化路径
        if (isset($data['path'])) {
            $data['path'] = path_format($data['path'],array(
                'ID'  => $postid,
                'PY'  => $post['title'],
                'MD5' => $postid,
            ));
            // 删除旧文件
            if ($data['path'] != $post['path']) {
                $post['path'] = post_get_path($post['listid'],$post['path']);
                $post['edittime'] = $post['edittime'] ? $post['edittime'] : time();
                // 文章添加大于24小时
                if (time()-$post['edittime'] > 86400) {
                    // 生成临时转向文件
                    if (strncmp($data['path'],'/',1) === 0) {
                        $path = ltrim($data['path'], '/');
                    } elseif ($post['listid'] > 0) {
                        $taxonomy = taxonomy_get($post['listid']);
                        $path = $taxonomy['path'].'/'.$data['path'];
                    }
                    $html = tpl_loadfile(ABS_PATH.'/'.system_themes_path().'/'.esc_html(C('TPL-404')));
                    tpl_clean($tpl);
                    tpl_set_var(array(
                        'path'  => ROOT.$post['path'],
                        'url'   => ROOT.$path,
                        'title' => $post['title'],
                        'keywords' => post_get_taxonomy($post['keywords']),
                        'description' => $post['description'],
                    ), $tpl);
                    $html = tpl_parse($html, $tpl);
                    $file = ABS_PATH.'/'.$post['path'];
                    mkdirs(dirname($file));
                    file_put_contents($file,$html);
                    // 变更添加时间
                    $data['edittime'] = time();
                }
                // 删除旧文件
                elseif (is_file(ABS_PATH.'/'.$post['path'])){
                    unlink(ABS_PATH.'/'.$post['path']);
                }
            }
        }
        $category = isset($data['category']) ? $data['category'] : null;
        $keywords = isset($data['keywords']) ? $data['keywords'] : null;
        unset($data['category'],$data['keywords']);
        $meta_rows = empty($data['meta']) ? array() : $data['meta']; unset($data['meta']);
        $post_rows = $data; $data['meta'] = $meta_rows; $data['category'] = $category;

        // 更新数据
        if (!empty($post_rows)) {
            $db->update('#@_post',$post_rows,array('postid' => $postid));
        }
        if (!empty($meta_rows)) {
            post_edit_meta($postid,$meta_rows);
        }
        // 更新分类关系
        if ($data['category']) {
            taxonomy_make_relation('category',$postid,$data['category']);
        }
        // 分析关键词
        if ($keywords) {
            $taxonomies = array();
            if (!is_array($keywords)) {
                // 替换掉全角逗号和全角空格
                $keywords = str_replace(array('，','　'),array(',',' '),$keywords);
                // 先用,分隔关键词
                $keywords = explode(',',$keywords);
                // 分隔失败，使用空格分隔关键词
                if (count($keywords)==1) $keywords = explode(' ',$keywords[0]);
            }
            // 移除重复的关键词
            $keywords = array_unique($keywords);
            // 去除关键词两边的空格，转义HTML
            array_walk($keywords,create_function('&$s','$s=esc_html(trim($s));'));
            // 强力插入关键词
            foreach($keywords as $key) {
                $taxonomies[] = taxonomy_add_tag($key, 'post_tag');
            }
            $data['keywords'] = implode(',',$keywords);
            // 创建关系
            taxonomy_make_relation('post_tag',$postid,$taxonomies);
        }
        // 清理缓存
        post_clean_cache($postid);
        return array_merge($post,$data);
    }
    return null;
}
/**
 * 判断路径是否存在
 *
 * @param  $postid
 * @param  $path    必须是format_path()格式化过的路径
 * @return bool
 */
function post_path_exists($postid,$path) {
    if (strpos($path,'%ID')!==false && strpos($path,'%MD5')!==false) return false;
    $db = get_conn();
    if ($postid) {
        $sql = sprintf("SELECT COUNT(`postid`) FROM `#@_post` WHERE `path`='%s' AND `postid`<>'%d';", esc_sql($path), esc_sql($postid));
    } else {
        $sql = sprintf("SELECT COUNT(`postid`) FROM `#@_post` WHERE `path`='%s';",esc_sql($path));
    }
    return !($db->result($sql) == 0);
}
/**
 * 统计文章数量
 *
 * @param string $type
 * @return int
 */
function post_count($type) {
    $db = get_conn(); return $db->result(sprintf("SELECT COUNT(`postid`) FROM `#@_post` WHERE `type`='%s' AND `approved`='passed';", $type));
}
/**
 * 查找指定的文章
 *
 * @param int $postid
 * @return array
 */
function post_get($postid) {
    $db   = get_conn();
    $ckey = sprintf('post.%d',$postid);
    $post = fcache_get($ckey);
    if (fcache_not_null($post)) return $post;

    $rs = $db->query("SELECT * FROM `#@_post` WHERE `postid`=%d LIMIT 1 OFFSET 0;",$postid);
    // 判断文章是否存在
    if ($post = $db->fetch($rs)) {
        // 取得分类关系
        $post['category'] = taxonomy_get_relation('category',$postid);
        $post['keywords'] = taxonomy_get_relation('post_tag',$postid);
        if ($meta = post_get_meta($post['postid'])) {
            $post['meta'] = $meta;
        }
        // 保存到缓存
        fcache_set($ckey,$post);
        return $post;
    }
    return null;
}
/**
 * 查询文章路径
 *
 * @param int $listid
 * @param string $path
 * @param string $prefix
 * @return string
 */
function post_get_path($listid,$path,$prefix='') {
    if ($prefix) {
        $prefix = !substr_compare($prefix,'/',strlen($prefix)-1,1) ? $prefix : $prefix.'/';
        if (strncmp($prefix,'/',1) === 0) {
            return ltrim($prefix,'/').ltrim($path, '/');
        }
    }
    if (strncmp($path,'/',1) === 0) {
        $path = ltrim($prefix,'/').ltrim($path, '/');
    } elseif ($listid > 0) {
        $taxonomy = taxonomy_get($listid);
        if (isset($taxonomy['path'])) {
            $path  = $taxonomy['path'].'/'.$prefix.$path;
        }
    } else {
        $path = $prefix.$path;
    }
    return $path;
}
/**
 * 获取关键词
 *
 * @param array $keywords
 * @param bool $isjoin
 * @return array|string
 */
function post_get_taxonomy($keywords, $isjoin=false) {
    $result = array();
    foreach((array)$keywords as $taxonomyid) {
        $taxonomy = taxonomy_get($taxonomyid);
        $result[$taxonomyid] = str_replace(chr(44), '&#44;', $taxonomy['name']);
    }
    return $isjoin ? implode(',', $result) : $result;
}
/**
 * 获取文章的详细信息
 *
 * @param  $postid
 * @return array
 */
function post_get_meta($postid) {
    $db = get_conn(); $result = array(); $postid = intval($postid);
    $rs = $db->query("SELECT * FROM `#@_post_meta` WHERE `postid`=%d;",$postid);
    while ($row = $db->fetch($rs)) {
        $result[$row['key']] = is_serialized($row['value']) ? unserialize($row['value']) : $row['value'];
    }
    return $result;
}
/**
 * 填写文章的详细信息
 *
 * @param  $postid
 * @param  $data
 * @return bool
 */
function post_edit_meta($postid,$data) {
    $db = get_conn(); $postid = intval($postid);
    if (!is_array($data)) return false;
    foreach ($data as $key=>$value) {
        // 查询数据库里是否已经存在
        $length = (int) $db->result(vsprintf("SELECT COUNT(*) FROM `#@_post_meta` WHERE `postid`=%d AND `key`='%s';",array($postid,esc_sql($key))));
        // update
        if ($length > 0) {
            $db->update('#@_post_meta',array(
                'value' => $value,
            ),array(
                'postid' => $postid,
                'key'    => $key,
            ));
        }
        // insert
        else {
            // 保存到数据库里
            $db->insert('#@_post_meta',array(
                'postid' => $postid,
                'key'    => $key,
                'value'  => $value,
            ));
        }
    }
    return true;
}
/**
 * 清理文章缓存
 *
 * @param  $postid
 * @return bool
 */
function post_clean_cache($postid) {
    return fcache_delete('post.'.$postid);
}
/**
 * 删除一片文章
 *
 * @param  $postid
 * @return bool
 */
function post_delete($postid) {
    $db = get_conn();
    $postid = intval($postid);
    if (!$postid) return false;
    if ($post = post_get($postid)) {
        // 查询路径
        $post['path'] = post_get_path($post['listid'],$post['path']);
        // 删除文件
        if (is_file(ABS_PATH.'/'.$post['path'])) {
            if (!unlink(ABS_PATH.'/'.$post['path'])) {
                return false;
            }
        }
        // 删除分类关系
        foreach($post['category'] as $taxonomyid) {
            taxonomy_delete_relation($postid,$taxonomyid);
        }
        // 删除关键词关系
        foreach($post['keywords'] as $taxonomyid) {
            taxonomy_delete_relation($postid,$taxonomyid);
        }
        $db->delete('#@_post_meta',array('postid' => $postid));
        $db->delete('#@_post',array('postid' => $postid));
        // 清理缓存
        post_clean_cache($postid);
        return true;
    }
    return false;
}
/**
 * 生成页面
 *
 * @param  $postid
 * @return bool
 */
function post_create($postid,&$preid=0,&$nextid=0) {
    $postid = intval($postid);
    if (!$postid) return false;
    if ($post = post_get($postid)) {
        $tpl  = tpl_init('post');
        $b_guid = $inner = ''; comment_create($post['postid']); // 生成评论
        // 处理文章
        $post['list']     = taxonomy_get($post['listid']);
        $post['cmt_path'] = post_get_path($post['listid'],$post['path'], C('Comments-Path'));
        $post['path']     = post_get_path($post['listid'],$post['path']);
        // 模版设置优先级：页面设置>分类设置>模型设置
        if (empty($post['template'])) {
            if ($post['listid'] > 0) {
                $taxonomy = taxonomy_get($post['listid']);
                $post['template'] = $taxonomy['page'];
            }
            // 使用模型设置
            if (empty($post['template'])) {
                $model = model_get_bycode($post['model']);
                $post['template'] = $model['page'];
            }
        }
        // 加载模版
        $html = tpl_loadfile(ABS_PATH.'/'.system_themes_path().'/'.esc_html($post['template']));
        
        $vars = array(
            'postid'   => $post['postid'],
            'userid'   => $post['userid'],
            'author'   => $post['author'],
            'views'    => '<script type="text/javascript" src="'.ROOT.'common/gateway.php?func=post_views&postid='.$post['postid'].'&updated=true"></script>',
            'comment'  => '<script type="text/javascript" src="'.ROOT.'common/gateway.php?func=post_comment&postid='.$post['postid'].'"></script>',
            'people'   => '<script type="text/javascript" src="'.ROOT.'common/gateway.php?func=post_comment_people&postid='.$post['postid'].'"></script>',
            'digg'     => $post['digg'],
            'date'     => $post['datetime'],
            'edittime' => $post['edittime'],
            'keywords' => post_get_taxonomy($post['keywords']),
            'prepage'  => post_prepage($post['listid'],$post['postid'],$preid),
            'nextpage' => post_nextpage($post['listid'],$post['postid'],$nextid),
            'cmt_state'    => $post['comments'],
            'cmt_ajaxinfo' => ROOT.'common/gateway.php?func=post_ajax_comment&postid='.$post['postid'],
            'cmt_replyurl' => ROOT.'common/gateway.php?func=post_send_comment&postid='.$post['postid'],
            'cmt_listsurl' => ROOT.$post['cmt_path'],
            'description'  => $post['description'],
        );
        // 设置分类变量
        if (isset($post['list'])) {
            $vars['listid']     = $post['list']['taxonomyid'];
            $vars['listname']   = $post['list']['name'];
            $vars['listpath']   = ROOT.$post['list']['path'].'/';
            $vars['listcount']  = '<script type="text/javascript" src="'.ROOT.'common/gateway.php?func=taxonomy_count&listid='.$post['list']['taxonomyid'].'"></script>';
            if (isset($post['list']['meta'])) {
                foreach((array)$post['list']['meta'] as $k=>$v) {
                    $vars['list.'.$k] = $v;
                }
            }
        }
        // 清理数据
        tpl_clean($tpl);
        tpl_set_var($b_guid, $inner, $tpl);
        tpl_set_var($vars, $tpl);
        // 设置自定义字段
        if (isset($post['meta'])) {
            foreach((array)$post['meta'] as $k=>$v) {
                tpl_set_var('post.'.$k, $v, $tpl);
            }
        }
        // 文章导航
        $guide = system_category_guide($post['listid']);

        // 文章分页
        if ($post['content'] && strpos($post['content'],'<!--pagebreak-->')!==false) {
            $contents = explode('<!--pagebreak-->',$post['content']);
            // 总页数
            $pages = count($contents);
            if (($pos=strrpos($post['path'],'.')) !== false) {
                $basename = substr($post['path'],0,$pos);
                $suffix   = substr($post['path'],$pos);
            } else {
                $basename = $post['path'];
                $suffix   = '';
            }
            foreach($contents as $i=>$content) {
                $page = $i + 1;
                if ($page == 1) {
                    $path  = $basename.$suffix;
                    $title = $post['title'];
                } else {
                    $path  = $basename.'_'.$page.$suffix;
                    $title = $post['title'].' ('.$page.')';
                    tpl_set_var('views', '<script type="text/javascript" src="'.ROOT.'common/gateway.php?func=post_views&postid='.$post['postid'].'"></script>', $tpl);
                }

                tpl_set_var(array(
                    'guide'   => $guide ? $guide.' &gt;&gt; '.$title : $title,
                    'title'   => $title,
                    'content' => $content,
                    'path'    => ROOT.$path,
                ), $tpl);
                $pagehtml = tpl_parse($html, $tpl);
                // 解析分页标签
                if (stripos($pagehtml,'{pagelist') !== false) {
                    $pagehtml = preg_replace('/\{(pagelist)[^\}]*\/\}/isU',
                        pages_list(ROOT.$basename.'_$'.$suffix, '!_$', $page, $pages, 1),
                        $pagehtml
                    );
                }
                // 生成的文件路径
                $file = ABS_PATH.'/'.$path;
                // 创建目录
                mkdirs(dirname($file));
                // 保存文件
                file_put_contents($file,$pagehtml);
            }
        }
        // 没有分页
        else {
            tpl_set_var(array(
                'guide'   => $guide ? $guide.' &gt;&gt; '.$post['title'] : $post['title'],
                'title'   => $post['title'],
                'content' => $post['content'],
                'path'    => ROOT.$post['path'],
            ), $tpl);
            // 解析分页标签
            if (stripos($html,'{pagelist') !== false) {
                $html = preg_replace('/\{(pagelist)[^\}]*\/\}/isU','',$html);
            }
            $html = tpl_parse($html, $tpl);
            // 生成的文件路径
            $file = ABS_PATH.'/'.$post['path'];
            // 创建目录
            mkdirs(dirname($file));
            // 保存文件
            return file_put_contents($file,$html);
        }
    }
    return true;
}
/**
 * 上一页
 *
 * @param int $listid
 * @param int $postid
 * @param int &$preid
 * @return string
 */
function post_prepage($listid,$postid,&$preid=0) {
    $db    = get_conn();
    $preid = $db->result(sprintf("SELECT `objectid` FROM `#@_term_relation` WHERE `taxonomyid`=%d AND `objectid`<%d ORDER BY `objectid` DESC LIMIT 1 OFFSET 0;", esc_sql($listid), esc_sql($postid)));
    if ($preid) {
        $post = post_get($preid);
        $post['path'] = post_get_path($post['listid'],$post['path']);
        $result = '<a href="'.ROOT.$post['path'].'">'.$post['title'].'</a>';
    } elseif($listid) {
        $post = post_get($postid);
        $post['list'] = taxonomy_get($post['listid']);
        $result = '<a href="'.ROOT.$post['list']['path'].'/">['.$post['list']['name'].']</a>';
    } else {
        $result = '['.__('Not Supported').']';
    }
    return $result;
}
/**
 * 下一页
 *
 * @param int $listid
 * @param int $postid
 * @param int &$nextid
 * @return string
 */
function post_nextpage($listid,$postid,&$nextid=0) {
    $db     = get_conn();
    $nextid = $db->result(sprintf("SELECT `objectid` FROM `#@_term_relation` WHERE `taxonomyid`=%d AND `objectid`>%d ORDER BY `objectid` ASC LIMIT 1 OFFSET 0;", esc_sql($listid), esc_sql($postid)));
    if ($nextid) {
        $post = post_get($nextid);
        $post['path'] = post_get_path($post['listid'],$post['path']);
        $result = '<a href="'.ROOT.$post['path'].'">'.$post['title'].'</a>';
    } elseif($listid) {
        $post = post_get($postid);
        $post['list'] = taxonomy_get($post['listid']);
        $result = '<a href="'.ROOT.$post['list']['path'].'/">['.$post['list']['name'].']</a>';
    } else {
        $result = '['.__('Not Supported').']';
    }
    return $result;
}
/**
 * 显示评论信息
 *
 * @return
 */
function post_gateway_ajax_comment() {
    $postid = isset($_GET['postid'])  ? $_GET['postid']  : 0;
    $comment_count  = comment_count($postid);
    $comment_people = comment_people($postid);
    return array($comment_count,$comment_people);
}
/**
 * 文章浏览数
 *
 * @return string
 */
function post_gateway_views() {
    $postid  = isset($_GET['postid'])  ? $_GET['postid']  : 0;
    $updated = isset($_GET['updated']) ? $_GET['updated'] : null;
    if (post_get($postid)) {
        $db = get_conn();
        $views = $db->result(sprintf("SELECT `views` FROM `#@_post` WHERE `postid`=%d", esc_sql($postid)));
        if ($updated=='true' || $updated=='1') {
            $views++; no_cache();
            $db->update('#@_post',array('views' => $views),array( 'postid' => $postid));
        }
    } else {
        $views = 0;
    }
    return 'document.write('.$views.');';
}
/**
 * 评论数量
 *
 * @return string
 */
function post_gateway_comment() {
    $postid = isset($_GET['postid']) ? $_GET['postid'] : 0;
    return 'document.write('.comment_count($postid).');';
}
/**
 * 评论人数量
 *
 * @return string
 */
function post_gateway_comment_people() {
    $postid = isset($_GET['postid']) ? $_GET['postid'] : 0;
    return 'document.write('.comment_people($postid).');';
}
/**
 * 发表评论
 *
 * @return void
 */
function post_gateway_send_comment() {
    $postid  = isset($_REQUEST['postid'])  ? $_REQUEST['postid']  : 0;
    // 检查文章是否存在
    if (!($post = post_get($postid))) {
        return ajax_error(__('The post doesn\'t exist or has been deleted!'));
    }
    // 检查是否可以评论
    if ($post['comments'] != 'Yes') {
        return ajax_error(__('The post doesn\'t comment!'));
    }
    $parent  = isset($_REQUEST['parent'])  ? $_REQUEST['parent']  : 0;
    $content = isset($_REQUEST['content']) ? $_REQUEST['content']  : '';
    $content = esc_html(trim($content));
    // 限制内容长度
    if (!$content) return ajax_alert(__('Please enter a comment on the contents!'));
    if (mb_strlen($content,'UTF-8') > 500) return ajax_alert(sprintf(__('Maximum %d characters of Comment content!'), 500));
    global $_USER;
    if (!isset($_USER)) {
        $email = isset($_REQUEST['mail']) ? esc_html(trim($_REQUEST['mail'])) : '';
        if (!validate_is($email,VALIDATE_IS_EMAIL)) return ajax_alert(__('You must provide an e-mail address.'));
        $_USER = array(
            'mail' => $email,
            'name' => isset($_REQUEST['author'])  ? esc_html(trim($_REQUEST['author']))  : '',
            'url'  => isset($_REQUEST['url'])     ? esc_html(trim($_REQUEST['url']))     : '',
        );
    }

    // 获取用户唯一标识
    $authcode = authcode();
    $cachekey = sprintf('comment.send.%s', $authcode);
    $session  = fcache_get($cachekey);
    if (fcache_not_null($session)) {
        // 说话太快，歇歇吧！
        if (time()-$session['time'] <= 3) {
            return ajax_error(__('You speak too fast, rest!'));
        }
        // 不能发送重复的评论
        if ($session['content'] == $content) {
            return ajax_error(__('You can not send duplicate comment!'));
        }
        // 没有违规，删除缓存
        fcache_delete($cachekey);
    }
    // 添加评论
    if (comment_add($postid,$content,$parent,$_USER)) {
        // 保存用户信息
        fcache_set($cachekey, array(
            'time' => time(),
            'content' => $content
        ), 86400);
        comment_create($postid);
        return ajax_success(__('Comment on the success!'));
    } else {
        return ajax_error(__('Comment failed!'));
    }
}