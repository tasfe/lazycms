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
// 文件名
$php_file = isset($php_file) ? $php_file : 'post.php';
// 加载公共文件
include dirname(__FILE__).'/admin.php';
// 查询管理员信息
$_USER = user_current();
// 方法
$method = isset($_REQUEST['method'])?$_REQUEST['method']:null;

switch ($method) {
    // 强力插入
    case 'new':
        if ('page.php' == $php_file) {
            system_head('title',__('Add New Page'));
            current_user_can('page-new');
        } else {
            system_head('title',__('Add New Post'));
            current_user_can('post-new');
        }
        system_head('styles', array('css/post','css/xheditor','css/datePicker'));
        system_head('scripts',array('js/post','js/xheditor','js/datePicker'));
        system_head('jslang',system_editor_lang());
        system_head('jslang',array(
            'Please enter the title or content!' => __('Please enter the title or content!'),
        ));
	    system_head('loadevents', 'post_manage_init');
	    include ADMIN_PATH.'/admin-header.php';
	    post_manage_page('add');	    
	    include ADMIN_PATH.'/admin-footer.php';
	    break;
	// 编辑
	case 'edit':
        if ('page.php' == $php_file) {
            // 所属
            $parent_file = 'page.php';
            // 重置标题
            system_head('title',__('Edit Page'));
            // 权限检查
            current_user_can('page-edit');
        } else {
            // 所属
            $parent_file = 'post.php';
            // 重置标题
            system_head('title',__('Edit Post'));
            // 权限检查
            current_user_can('post-edit');
        }
        system_head('styles', array('css/post','css/xheditor','css/datePicker'));
        system_head('scripts',array('js/post','js/xheditor','js/datePicker'));
        system_head('jslang',system_editor_lang());
        system_head('jslang',array(
            'Please enter the title or content!' => __('Please enter the title or content!'),
        ));
	    system_head('loadevents', 'post_manage_init');
	    include ADMIN_PATH.'/admin-header.php';
	    post_manage_page('edit');	    
	    include ADMIN_PATH.'/admin-footer.php';
	    break;
    // 批量动作
	case 'bulk':
	    $action  = isset($_POST['action'])?$_POST['action']:null;
	    $listids = isset($_POST['listids'])?$_POST['listids']:null;
	    if (empty($listids)) {
	    	ajax_error(__('Did not select any item.'));
	    }
	    switch ($action) {
	        case 'create':
	            foreach ($listids as $postid) {
	            	post_create($postid);
	            }
	            ajax_success(__('Posts created.'),"LazyCMS.redirect('".referer()."');");
	            break;
            case 'delete':
	            current_user_can('post-delete');
	            foreach ($listids as $postid) {
	            	post_delete($postid);
	            }
	            ajax_success(__('Posts deleted.'),"LazyCMS.redirect('".referer()."');");
	            break;
            default:
                ajax_alert(__('Parameter is invalid.'));
                break;
	    }
	    break;
	// 保存
	case 'save':
        $postid = isset($_POST['postid'])?$_POST['postid']:0;
	    current_user_can($postid?'post-edit':'post-new');
	    
        if (validate_is_post()) {
            $referer  = referer(PHP_FILE,false);
            $mcode    = isset($_POST['model'])?$_POST['model']:null;
            $model    = model_get_bycode($mcode);
            $create   = isset($_POST['create'])?$_POST['create']:array();
            $comments = isset($_POST['comments'])?$_POST['comments']:'No';
            $listid   = isset($_POST['listid'])?$_POST['listid']:0;
            $type     = isset($_POST['type'])?$_POST['type']:'page';
            $category = isset($_POST['category'])?$_POST['category']:array();
            $title    = isset($_POST['title'])?$_POST['title']:null;
            $autokeys = isset($_POST['autokeys'])?$_POST['autokeys']:null;
            $path     = isset($_POST['path'])?$_POST['path']:null;
            $content  = isset($_POST['content'])?$_POST['content']:null;
            $template = isset($_POST['template'])?$_POST['template']:null;
            $keywords = isset($_POST['keywords'])?$_POST['keywords']:null;
            $description = isset($_POST['description'])?$_POST['description']:null;


            if ('post.php' == $php_file) {
                validate_check('listid',VALIDATE_EMPTY,_x('Please select a main category.','post'));
            }

            validate_check(array(
                array('title',VALIDATE_EMPTY,_x('The title field is empty.','post')),
                array('title',VALIDATE_LENGTH,_x('The title field length must be %d-%d characters.','post'),1,255),
            ));
            // 验证路径
            $path_exists = post_path_exists($postid,path_format($path,array('PY'=>$title)));
            validate_check(array(
                array('path',VALIDATE_EMPTY,_x('The path field is empty.','post')),
                array('path',VALIDATE_IS_PATH,sprintf(_x('The path can not contain any of the following characters %s','post'),esc_html('* : < > | \\'))),
                array('path',(!$path_exists),_x('The path already exists.','post')),
            ));
            // 自动截取简述
            if (empty($description)) {
                $description = mb_substr(clear_space(strip_tags($content)),0,250,'UTF-8');
            } else {
                validate_check(array(
                    array('description',VALIDATE_LENGTH,__('Description the field up to 255 characters.'),0,255),
                ));
            }           

            // 验证自定义的字段
            if ($model['fields']) {
                foreach($model['fields'] as $field) {
                    if (empty($field['v'])) continue;
                    $last_rules = array();
                    $rules = explode("\n",$field['v']);
                    foreach($rules as $rule) {
                        if (strpos($rule,'|')===false) continue;
                        $VRS = explode('|',rtrim($rule,';')); array_unshift($VRS,$field['_n']);
                        $last_rules[] = $VRS;
                    }
                    validate_check($last_rules);
                }
            }

            // 安全有保证，做爱做的事吧！
            if (validate_is_ok()) {
                // 下载远程图片
                if (isset($_POST['LocalizedImages'])) {
                    // 下载主编辑器图片
                    if (isset($_POST['LocalizedImages']['content']) && $_POST['LocalizedImages']['content']) {
                        $content = media_localized_images($content); unset($_POST['LocalizedImages']['content']);
                    }
                    // 下载其他编辑器图片
                    foreach((array)$_POST['LocalizedImages'] as $field=>$v) {
                        if ($v) {
                            $_POST[$field] = media_localized_images($_POST[$field]);
                        }
                    }
                }
                // 处理路径
                $path = esc_html(rtrim($path,'/'));
                // 自动获取关键词
                if ($autokeys && empty($keywords)) {
                    $keywords = term_gets($title, $content);
                }
                // 添加主分类
                if ($listid > 0) {
                    array_unshift($category,$listid);
                }
                // 获取数据
                $data = array(
                    'listid'   => $listid,
                    'type'     => $type,
                    'category' => $category,
                    'model'    => esc_html($mcode),
                    'template' => esc_html($template),
                    'keywords' => $keywords,
                    'comments' => $comments,
                    'description' => esc_html($description),
                );

                // 获取模型字段值
                if ($model['fields']) {
                    foreach($model['fields'] as $field) {
                        $data['meta'][$field['n']] = isset($_POST[$field['_n']])?$_POST[$field['_n']]:null;
                    }
                }

                // 更新
                if ($postid) {
                    $data['path']    = $path;
                    $data['title']   = $title;
                    $data['content'] = $content;
                    post_edit($postid,$data);
                    $result = __('Post updated.');
                }
                // 强力插入
                else {
                    $data['author'] = $_USER['name'];
                    $data['userid'] = $_USER['userid'];
                    if ($post = post_add($title,$content,$path,$data)) {
                        $postid = $post['postid'];
                    }
                    $result = __('Post created.');
                }
                // 更新分类
                if (instr('lists',$create)) {
                    publish_add(sprintf(__('Create Lists:%s'),taxonomy_get_names($category)),'publish_lists',array($category,false));
                }
                // 更新所有单页面
                if (instr('pages',$create)) {
                    publish_add(__('Create all Pages'),'publish_posts',array('pages'));
                }
                // 生成文章
                if (post_create($postid,$preid)) {
                    // 重新生成上一篇
                    if ($preid) post_create($preid);
                    // 生成单页 sitemaps
                    if ('page.php' == $php_file) {
                        publish_page_sitemaps();
                    }
                    $result = sprintf('<p>%s</p><p>%s</p>', $result, _x('[Submit] to Add New<br />[Cancel] to Back list','post'));
                    ajax_confirm($result, "LazyCMS.redirect('".PHP_FILE."?method=new&category={$listid}');", "LazyCMS.redirect('".$referer."');");
                } else {
                    ajax_alert($result.__('File create failed.'),"LazyCMS.redirect('".$referer."');");
                }
            }
        }
	    break;
	// 获取扩展字段
	case 'extend-attr':
        $model  = null; $hl = '';
	    $mcode  = isset($_REQUEST['model'])?$_REQUEST['model']:null;
	    $postid = isset($_REQUEST['postid'])?$_REQUEST['postid']:0;
        $suffix = C('HTML-Ext');
        if ($postid) {
            $post = post_get($postid);
        }
        if ($mcode) {
            $model = model_get_bycode($mcode);
            $path  = isset($post['path'])?$post['path']:$model['path'];
        } else {
            $path  = isset($post['path'])?$post['path']:'%PY'.$suffix;
        }
        header('X-LazyCMS-Path: '.$path);
	    if ($model) {
	    	foreach ($model['fields'] as $field) {
                if (isset($post['meta'][$field['n']])) {
                    $field['d'] = $post['meta'][$field['n']];
                }
	    		$hl.= '<tr>';
                $hl.=    '<th><label for="'.$field['_n'].'">'.$field['l'];
                if (!empty($field['h'])) {
                    $hl.=    '<span class="resume">'.$field['h'].'</span>';
                }
                $hl.=        '</label>';
                $hl.=    '</th>';
                $hl.=    '<td>'.model_field2html($field).'</td>';
                $hl.= '</tr>';
	    	}
	    }
        ajax_return($hl);
	    break;
    default:
        if ('page.php' == $php_file) {
            system_head('title',  __('Pages'));
            current_user_can('page-list');
            $add_new = _x('Add New','page');
        } else {
            system_head('title',  __('Posts'));
            current_user_can('post-list');
            $add_new = _x('Add New','post');
        }
        system_head('styles', array('css/post'));
        system_head('scripts',array('js/post'));
        system_head('loadevents','post_list_init');
	    $model    = isset($_REQUEST['model'])?$_REQUEST['model']:'';
        $search   = isset($_REQUEST['query'])?$_REQUEST['query']:'';
        $category = isset($_REQUEST['category'])?$_REQUEST['category']:null;
        $query    = array('page' => '$');
        $add_args = array('method' => 'new');
        // 排序方式
        $order = 'page.php'==$php_file ? 'ASC' : 'DESC';

        $conditions = array();
        // 根据分类筛选
        if ($search || $category) {
            if ('page.php' == $php_file) {
                $where = "WHERE `p`.`type`='page'";
            } else {
                $where = "WHERE `p`.`type`='post'";
            }
            if ($category) {
                $query['category'] = $category; $add_args['category'] = $category;
                $where.= sprintf(" AND (`tr`.`taxonomyid`=%d)", esc_sql($category));
            }
            if ($search) {
                $query['query'] = $search;
                $fields = array('title','content','description');
                foreach($fields as $field) {
                    $conditions[] = sprintf("BINARY UCASE(`p`.`%s`) LIKE UCASE('%%%s%%')",$field,esc_sql($search));
                }
                $where.= ' AND ('.implode(' OR ', $conditions).')';
            }
            $sql = "SELECT DISTINCT(`p`.`postid`) FROM `#@_post` AS `p` LEFT JOIN `#@_term_relation` AS `tr` ON `p`.`postid`=`tr`.`objectid` {$where} ORDER BY `p`.`postid` {$order}";
        } else {
            if ('page.php' == $php_file) {
                $conditions[] = "`type`='page'";
            } else {
                $conditions[] = "`type`='post'";
            }
            // 根据模型筛选
            if ($model) {
                $query['model'] = $model;
                $conditions[] = sprintf("`model` = '%s'",esc_sql($model));
            }
            // 没有任何筛选条件
            $where = ' WHERE '.implode(' AND ' , $conditions);
            $sql = "SELECT `postid` FROM `#@_post` {$where} ORDER BY `postid` {$order}";
        }
        $result = pages_query($sql);
        // 分页地址
        $page_url   = PHP_FILE.'?'.http_build_query($query);

        include ADMIN_PATH.'/admin-header.php';
        echo '<div class="wrap">';
        echo   '<h2>'.system_head('title').'<a class="button" href="'.PHP_FILE.'?'.http_build_query($add_args).'">'.$add_new.'</a></h2>';
        echo   '<form header="POST '.PHP_FILE.'?method=bulk" action="'.PHP_FILE.'" method="get" name="postlist" id="postlist">';
        table_nav('top',$page_url);
        echo       '<table class="data-table" cellspacing="0">';
        echo           '<thead>';
        table_thead();
        echo           '</thead>';
        echo           '<tfoot>';
        table_thead();
        echo           '</tfoot>';
        echo           '<tbody>';
        if ($result) {
            while ($data = pages_fetch($result)) {
                $post     = post_get($data['postid']);
                $edit_url = PHP_FILE.'?method=edit&postid='.$post['postid'];
                $post['count'] = comment_count($post['postid']);
                // 检查文件是否已生成
                $post['path'] = post_get_path($post['listid'],$post['path']);
                if (is_file(ABS_PATH.'/'.$post['path'])) {
                    $browse   = get_icon('b6').'<a href="'.ROOT.$post['path'].'" target="_blank">'.ROOT.$post['path'].'</a>';
                } else {
                    $browse   = get_icon('b7').'<a href="javascript:;" onclick="post_create('.$post['postid'].')">'.ROOT.$post['path'].'</a>';
                }
                $actions = '<span class="edit"><a href="'.$edit_url.'">'.__('Edit').'</a> | </span>';
                $actions.= '<span class="create"><a href="javascript:;" onclick="post_create('.$post['postid'].')">'.__('Create').'</a> | </span>';
                $actions.= '<span class="delete"><a href="javascript:;" onclick="post_delete('.$post['postid'].')">'.__('Delete').'</a> | </span>';
                $actions.= '<span class="browse">'.$browse.'</span>';

                echo '<tr>';
                echo    '<td class="check-column"><input type="checkbox" name="listids[]" value="'.$post['postid'].'" /></td>';
                echo    '<td><strong><a href="'.$edit_url.'">'.$post['title'].'</a></strong><br/><div class="row-actions">'.$actions.'</div></td>';
                if (empty($post['model'])) {
                    echo '<td>'.__('None').'</td>';
                } else {
                    $post['model'] = model_get_bycode($post['model']);
                    echo '<td><a href="'.PHP_FILE.'?model='.$post['model']['langcode'].'">'.$post['model']['name'].'</a></td>';
                }

                if ('post.php' == $php_file) {
                    $categories = array();
                    foreach(post_get_taxonomy($post['category']) as $taxonomyid=>$category) {
                        $categories[] = '<a href="'.PHP_FILE.'?category='.$taxonomyid.'">'.$category.'</a>';
                    }
                    echo empty($categories) ? '<td>'.__('None').'</td>' : '<td>'.implode(',' , $categories).'</td>';
                }
                $tags = array();
                foreach(post_get_taxonomy($post['keywords']) as $keyid=>$keyword) {
                    $tags[] = '<a href="'.PHP_FILE.'?category='.$keyid.'">'.$keyword.'</a>';
                }
                echo empty($tags) ? '<td>'.__('None').'</td>' : '<td>'.implode(',' , $tags).'</td>';
                
                echo    '<td><a class="comment-count'.($post['count']?' exist':'').'" href="'.ADMIN.'comment.php?p='.$post['postid'].'"><span>'.$post['count'].'</span></a></td>';
                echo    '<td><div title="'.date('Y-m-d H:i:s',$post['datetime']).'">'.date('Y-m-d',$post['datetime']).'</div></td>';//'.get_icon($post['approved']).'
                echo '</tr>';
            }
        } else {
            echo           '<tr><td colspan="7" class="tc">'.__('No record!').'</td></tr>';
        }
        echo           '</tbody>';
        echo       '</table>';
        table_nav('bottom',$page_url);
        echo   '</form>';
        echo '</div>';
        include ADMIN_PATH.'/admin-footer.php';
        break;
}

/**
 * 批量操作
 *
 * @param  $side    top|bottom
 * @param  $url
 * @return void
 */
function table_nav($side,$url) {
    global $php_file, $category, $search;
    echo '<div class="table-nav">';
    echo     '<select name="actions">';
    echo         '<option value="">'.__('Bulk Actions').'</option>';
    echo         '<option value="create">'.__('Create').'</option>';
    echo         '<option value="delete">'.__('Delete').'</option>';
    echo     '</select>';
    echo     '<button type="button">'.__('Apply').'</button>';
    if ($side == 'top') {
        echo '<span class="filter">';
        if ('post.php' == $php_file) {
            echo '<select name="category">';
            echo     '<option value="">'.__('View all categories').'</option>';
            echo     dropdown_categories($category);
            echo '</select>';
        }
        echo    '<input class="text" type="text" size="20" name="query" value="'.esc_html($search).'" />';
        echo    '<button type="submit">'.__('Filter').'</button>';
        echo '</span>';
    }
    if ($side == 'bottom') {
        echo pages_list($url);
    }
    echo '</div>';
}
/**
 * 表头
 *
 */
function table_thead() {
    global $php_file;
    echo '<tr>';
    echo     '<th class="check-column"><input type="checkbox" name="select" value="all" /></th>';
    echo     '<th>'._x('Title','post').'</th>';
    echo     '<th class="w100">'._x('Model','post').'</th>';
    if ('post.php' == $php_file) {
        echo '<th class="wp15">'._x('Categories','post').'</th>';
    }
    echo     '<th class="wp15">'._x('Tags','post').'</th>';
    echo     '<th class="w50">'.get_icon('c9').'</th>';
    echo     '<th class="w100">'._x('Date','post').'</th>';
    echo '</tr>';
}

/**
 * 管理页面
 *
 * @param string $action
 */
function post_manage_page($action) {
    global $php_file; $trees = null;
    $referer = referer(PHP_FILE);
    if ('post.php' == $php_file) {
        $trees = taxonomy_get_trees();
        if (empty($trees)) {
            echo '<div class="wrap">';
            echo   '<h2>'.system_head('title').'</h2>';
            echo   '<fieldset>';
            echo       '<table class="form-table">';
            echo           '<tbody>';
            echo               '<tr><td>'.__('Please Add Category!').'</td></tr>';
            echo               '<tr><td class="buttons"><button type="button" onclick="LazyCMS.redirect(\''.ADMIN.'categories.php?method=new\')">'._x('Add New','sort').'</button><button type="button" onclick="LazyCMS.redirect(\''.$referer.'\')">'.__('Back').'</button></td></tr>';
            echo           '</tbody>';
            echo       '</table>';
            echo   '</fieldset>';
            echo '</div>';
            return true;
        }
    }
    
    $postid  = isset($_GET['postid'])?$_GET['postid']:0;
    $models  = model_gets('Post', 'enabled');
    $suffix  = C('HTML-Ext');
    if ($action=='add') {
        $mcode  = isset($_GET['model'])?$_GET['model']:null;
        $listid = isset($_GET['category'])?$_GET['category']:null;
    } else {
        $_DATA  = post_get($postid);
        $mcode  = $_DATA['model'];
        $listid = isset($_DATA['listid'])?$_DATA['listid']:null;
    }

    $model    = $mcode ? model_get_bycode($mcode) : array_pop(array_slice($models,0,1));
    $title    = isset($_DATA['title'])?$_DATA['title']:null;
    $path     = isset($_DATA['path'])?$_DATA['path']:$model['path'];
    $content  = isset($_DATA['content'])?$_DATA['content']:null;
    $comments = isset($_DATA['comments'])?$_DATA['comments']:'Yes';
    $template = isset($_DATA['template'])?$_DATA['template']:null;
    $keywords = isset($_DATA['keywords'])?post_get_taxonomy($_DATA['keywords'], true):null;
    $categories  = isset($_DATA['category'])?$_DATA['category']:array();
    $description = isset($_DATA['description'])?$_DATA['description']:null;

    echo '<div class="wrap">';
    echo   '<h2>'.system_head('title').'</h2>';
    echo   '<form action="'.PHP_FILE.'?method=save" method="post" name="postmanage" id="postmanage">';
    echo   '<fieldset>';
    echo       '<table class="form-table">';
    echo           '<tbody>';
    if ($models) {
        echo           '<tr>';
        echo               '<th><label for="model">'._x('Model','post').'</label></th>';
        echo               '<td><select name="model" id="model"'.($action=='add' ? ' cookie="true"' : '').'>';
        foreach ($models as $m) {
            $selected = isset($model['langcode']) && $m['langcode']==$model['langcode']?' selected="selected"':'';
        	echo               '<option value="'.$m['langcode'].'"'.$selected.'>'.$m['name'].'</option>';
        }
        echo               '</select></td>';
        echo           '</tr>';
    }
    $hidden = '';
    if ('page.php' == $php_file) {
        $hidden = '<input type="hidden" name="type" value="page" />';
    } else {
        $hidden = '<input type="hidden" name="type" value="post" />';
        echo           '<tr class="taxonomyid">';
        echo               '<th><label for="taxonomyid">'._x('Categories','post').'</label></th>';
        echo               '<td>';
        echo                   display_ul_categories($listid,$categories,$trees);
        echo               '</td>';
        echo           '</tr>';
    }
    
    echo               '<tr>';
    echo                   '<th><label for="title">'._x('Title','post').'<span class="resume">'.__('(required)').'</span></label></th>';
    echo                   '<td>';
    echo                       '<input class="text" id="title" name="title" type="text" size="70" value="'.$title.'" />';
    echo                       '&nbsp;<label for="autokeys"><input type="checkbox" value="1" id="autokeys" name="autokeys" checked="checked" cookie="true">'.__('Auto get keywords').'</label>';
    echo                   '</td>';
    echo               '</tr>';
    echo               '<tr>';
    echo                   '<th><label for="content">'._x('Content','post').'</label></th>';
    echo                   '<td>'.editor('content', $content).'</td>';
    echo               '</tr>';
    echo           '</tbody>';
    echo           '<tbody class="extend-attr"></tbody>';
    echo           '<tbody>';
    echo               '<tr>';
    echo                   '<th><label for="path">'._x('Path','post').'<span class="resume">'.__('(required)').'</span></label></th>';
    echo                   '<td><input class="text" id="path" name="path" type="text" size="80" value="'.$path.'" /><div class="rules">';
    echo                       '<a href="#%ID'.$suffix.'">['.__('Post ID').']</a>';
    echo                       '<a href="#%MD5'.$suffix.'">['.__('MD5 Value').']</a>';
    echo                       '<a href="#%PY'.$suffix.'">['.__('Pinyin').']</a>';
    echo                       '<a href="#%Y" title="%Y">['.strftime('%Y').']</a>';
    echo                       '<a href="#%m" title="%m">['.strftime('%m').']</a>';
    echo                       '<a href="#%d" title="%d">['.strftime('%d').']</a>';
    echo                       '<a href="#%a" title="%a">['.strftime('%a').']</a>';
    echo                   '</div></td>';
    echo               '</tr>';
    echo           '</tbody>';
    echo       '</table>';
    echo   '</fieldset>';
    echo   '<fieldset cookie="true">';
    echo       '<a href="javascript:;" class="toggle" title="'.__('Click to toggle').'"><br/></a>';
    echo       '<h3>'.__('More attribute').'</h3>';
    echo       '<table class="form-table">';
    echo           '<tbody>';
    echo               '<tr>';
    echo                   '<th><label for="template">'._x('Page Template','post').'</label></th>';
    echo                   '<td>';
    echo                       '<select id="template" name="template">';
    echo                           $trees  ? '<option value="">'.__('Use the category set').'</option>' : '';
    echo                           $models && 'page.php'==$php_file ? '<option value="">'.__('Use the model set').'</option>' : '';
    echo                           options(system_themes_path(),C('TPL-Exts'),'<option value="#value#"#selected#>#name#</option>',$template);
    echo                       '</select>';
    echo                   '</td>';
    echo               '</tr>';
    echo               '<tr>';
    echo                   '<th><label for="keywords">'._x('Keywords','post').'</label></th>';
    echo                   '<td><input class="text" type="text" size="70" name="keywords" id="keywords" value="'.$keywords.'" />&nbsp;<button type="button" rel="keywords">'.__('Get').'</button></td>';
    echo               '</tr>';
    echo               '<tr>';
    echo                   '<th><label for="description">'._x('Description','post').'<br /><span class="resume">'.__('(Maximum of 250)').'</span></label></th>';
    echo                   '<td><textarea class="text" cols="70" rows="5" id="description" name="description">'.$description.'</textarea></td>';
    echo               '</tr>';

        echo           '<tr>';
        echo               '<th><label>'._x('Other','post').'</label></th>';
        echo               '<td>';
        echo                   '<label for="comments"><input type="checkbox" name="comments" value="Yes" id="comments"'.($comments=='Yes'?' checked="checked"':'').' />'.__('Allow Comments').'</label>';
        if ('post.php' == $php_file) {
            echo               '<label for="createpages"><input type="checkbox" name="create[]" value="pages" id="createpages" cookie="true" />'.__('Update all Pages').'</label>';
            echo               '<label for="createlists"><input type="checkbox" name="create[]" value="lists" id="createlists" cookie="true" />'.__('Update Category Lists').'</label>';
        }
        echo               '</td>';
        echo           '</tr>';

    echo           '</tbody>';
    echo       '</table>';
    echo   '</fieldset>';
    echo   '<p class="submit">';
    if ($action=='add') {
        echo   '<button type="submit">'.__('Add Post').'</button>'.$hidden;
    } else {
        $hidden.= '<input type="hidden" name="postid" value="'.$postid.'" />';
        $hidden.= '<input type="hidden" name="referer" value="'.referer().'" />';
        echo   '<button type="submit">'.__('Update Post').'</button>'.$hidden;
    }
    echo       '<button type="button" onclick="LazyCMS.redirect(\''.$referer.'\')">'.__('Back').'</button>';
    echo   '</p>';
    echo  '</form>';
    echo '</div>';
}

/**
 * 显示分类数
 *
 * @param int $listid
 * @param array $categories
 * @param array $trees
 * @return string
 */
function display_ul_categories($listid,$categories=array(),$trees=null) {
    static $func = null;
    $hl = sprintf('<ul %s>',is_null($func) ? 'id="listid" class="categories"' : 'class="children"');
    if (!$func) $func = __FUNCTION__;
    if ($trees === null) $trees = taxonomy_get_trees();
    foreach ($trees as $i=>$tree) {
        $checked = instr($tree['taxonomyid'],$categories) && $listid!=$tree['taxonomyid'] ? ' checked="checked"' : '';
        $main_checked = $tree['taxonomyid']==$listid?' checked="checked"':'';
        $hl.= sprintf('<li><input type="radio" name="listid" value="%d"%s />',$tree['taxonomyid'],$main_checked);
        $hl.= sprintf('<label class="selectit" for="category-%d">',$tree['taxonomyid']);
        $hl.= sprintf('<input type="checkbox" id="category-%1$d" name="category[]" value="%1$d"%3$s />%2$s</label>',$tree['taxonomyid'],$tree['name'],$checked);
    	if (isset($tree['subs'])) {
    		$hl.= $func($listid,$categories,$tree['subs']);
    	}
        $hl.= '</li>';
    }
    $hl.= '</ul>';
    return $hl;
}

/**
 * 显示分类树
 *
 * @param int $taxonomyid   当前分类ID
 * @param int $selected 被选择的分类ID
 * @param int $n
 * @param array $trees
 * @return string
 */
function dropdown_categories($selected=0, $trees=null) {
    static $n = 0; $func = __FUNCTION__; 
    if ($trees===null) $trees = taxonomy_get_trees();
    $hl = ''; $space = str_repeat('&nbsp; &nbsp; ',$n); $n++;
    foreach ($trees as $tree) {
        $sel  = $selected==$tree['taxonomyid']?' selected="selected"':null;
        $hl.= '<option value="'.$tree['taxonomyid'].'"'.$sel.' path="'.$tree['path'].'/">'.$space.'├ '.$tree['name'].'</option>';
    	if (isset($tree['subs'])) {
    		$hl.= $func($selected,$tree['subs']);
    	}
    }
    return $hl;
}