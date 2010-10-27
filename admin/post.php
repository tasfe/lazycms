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
// 文件名
$php_file = isset($php_file) ? $php_file : 'post.php';
// 加载公共文件
require dirname(__FILE__).'/admin.php';
// 查询管理员信息
$_USER = user_current();
// 标题
if ('page.php' == $php_file) {
    admin_head('title',  __('Pages'));
} else {
    admin_head('title',  __('Posts'));
}
admin_head('styles', array('css/post'));
admin_head('scripts',array('js/post'));
// 方法
$method = isset($_REQUEST['method'])?$_REQUEST['method']:null;

switch ($method) {
    // 强力插入
    case 'new':
        admin_head('scripts',array('js/xheditor','js/post'));
        if ('page.php' == $php_file) {
            current_user_can('page-new');
            admin_head('title',__('Add New Page'));
        } else {
            current_user_can('post-new');
            admin_head('title',__('Add New Post'));
        }
	    admin_head('loadevents','post_manage_init');
	    include ADMIN_PATH.'/admin-header.php';
	    post_manage_page('add');	    
	    include ADMIN_PATH.'/admin-footer.php';
	    break;
	// 编辑
	case 'edit':
        admin_head('scripts',array('js/xheditor','js/post'));
        if ('page.php' == $php_file) {
            // 所属
            $parent_file = 'page.php';
            // 权限检查
            current_user_can('page-edit');
            // 重置标题
            admin_head('title',__('Edit Page'));
        } else {
            // 所属
            $parent_file = 'post.php';
            // 权限检查
            current_user_can('post-edit');
            // 重置标题
            admin_head('title',__('Edit Post'));
        }

	    admin_head('loadevents','post_manage_init');
	    include ADMIN_PATH.'/admin-header.php';
	    post_manage_page('edit');	    
	    include ADMIN_PATH.'/admin-footer.php';
	    break;
    // 批量动作
	case 'bulk':
	    $action  = isset($_POST['action'])?$_POST['action']:null;
	    $listids = isset($_POST['listids'])?$_POST['listids']:null;
	    if (empty($listids)) {
	    	admin_error(__('Did not select any item.'));
	    }
	    switch ($action) {
	        case 'create':
	            foreach ($listids as $postid) {
	            	post_create($postid);
	            }
	            admin_success(__('Posts created.'),"LazyCMS.redirect('".referer()."');");
	            break;
            case 'delete':
	            current_user_can('post-delete');
	            foreach ($listids as $postid) {
	            	post_delete($postid);
	            }
	            admin_success(__('Posts deleted.'),"LazyCMS.redirect('".referer()."');");
	            break;
            default:
                admin_alert(__('Parameter is invalid.'));
                break;
	    }
	    break;
	// 保存
	case 'save':
        $postid = isset($_POST['postid'])?$_POST['postid']:0;
	    current_user_can($postid?'post-edit':'post-new');
	    
        if (validate_is_post()) {
            $mcode    = isset($_POST['model'])?$_POST['model']:null;
            $model    = model_get_bycode($mcode);
            $sortid   = isset($_POST['sortid'])?$_POST['sortid']:0;
            $category = isset($_POST['category'])?$_POST['category']:array();
            $title    = isset($_POST['title'])?$_POST['title']:null;
            $autokeys = isset($_POST['autokeys'])?$_POST['autokeys']:null;
            $path     = isset($_POST['path'])?$_POST['path']:null;
            $content  = isset($_POST['content'])?$_POST['content']:null;
            $template = isset($_POST['template'])?$_POST['template']:null;
            $keywords = isset($_POST['keywords'])?$_POST['keywords']:null;
            $description = isset($_POST['description'])?$_POST['description']:null;
            $createlists = isset($_POST['createlists'])?$_POST['createlists']:null;


            validate_check(array(
                array('title',VALIDATE_EMPTY,_x('The title field is empty.','post')),
                array('title',VALIDATE_LENGTH,_x('The title field length must be %d-%d characters.','post'),1,255),
            ));
            // 验证路径
            $path_exists = post_path_exists($postid,path_format($path,array('PY'=>$title)));
            validate_check(array(
                array('path',VALIDATE_EMPTY,_x('The path field is empty.','post')),
                array('path',VALIDATE_IS_PATH,sprintf(_x('The path can not contain any of the following characters %s','post'),'* : < > | \\')),
                array('path',(!$path_exists),_x('The path already exists.','post')),
            ));
            // 自动截取简述
            if (empty($description)) {
                $description = mb_substr(strip_tags($content),0,255,'UTF-8');
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
                // 处理路径
                $path = esc_html(rtrim($path,'/'));
                // 自动获取关键词
                if ($autokeys && empty($keywords)) {
                    $keywords = term_gets($title);
                }
                // 添加主分类
                if ($sortid > 0) {
                    array_unshift($category,$sortid);
                }
                // 获取数据
                $data = array(
                    'sortid'   => $sortid,
                    'category' => $category,
                    'model'    => esc_html($mcode),
                    'template' => esc_html($template),
                    'keywords' => $keywords,
                    'description' => esc_html($description),
                );
                // 获取模型字段值
                if ($model['fields']) {
                    foreach($model['fields'] as $field) {
                        $data['meta'][$field['n']] = isset($_POST[$field['_n']])?$_POST[$field['_n']]:null;
                    }
                }

                // 下载远程图片

                // 删除站外连接
                //$content = preg_replace('/<a([^>]*)href=["\']*(?!'.preg_quote(HTTP_HOST,'/').')([^>]*)>(.*)<\/a>/isU','$3',$content);
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
                    $data['author'] = $_USER['userid'];
                    $data['passed'] = 0;
                    $data['datetime'] = time();
                    if ($post = post_add($title,$content,$path,$data)) {
                        $postid = $post['postid'];
                    }
                    $result = __('Post created.');
                }
                // 更新分类
                if ($createlists) {
                    publish_add(sprintf(__('Create Lists:%s'),taxonomy_get_names($category)),'publish_lists',array($category,false));
                }
                // 生成文章
                if (post_create($postid)) {
                    admin_success($result,"LazyCMS.redirect('".PHP_FILE."');");
                } else {
                    admin_alert($result.__('File create failed.'),"LazyCMS.redirect('".PHP_FILE."');");
                }
            }
        }
	    break;
	// 获取扩展字段
	case 'extend-attr':
        $model  = null; $hl = '';
	    $mcode  = isset($_REQUEST['model'])?$_REQUEST['model']:null;
	    $postid = isset($_REQUEST['postid'])?$_REQUEST['postid']:0;
        $suffix = C('HTMLFileSuffix');
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
                    $hl.=    '<span class="description">'.$field['h'].'</span>';
                }
                $hl.=        '</label>';
                $hl.=    '</th>';
                $hl.=    '<td>';
                switch ($field['t']) {
                    case 'input':
                        $hl.= '<input class="text" id="'.$field['_n'].'" name="'.$field['_n'].'" type="text" style="width:'.$field['w'].'" maxlength="'.$field['c'].'" value="'.$field['d'].'" />';
                        break;
                    case 'textarea':
                        $hl.= '<textarea class="text" name="'.$field['_n'].'" id="'.$field['_n'].'" style="width:'.$field['w'].'" rows="8">'.$field['d'].'</textarea>';
                        break;
                    case 'select':
                        $values = explode("\n",$field['s']);
                        $hl.= '<select name="'.$field['_n'].'" id="'.$field['_n'].'" edit="true" style="width:'.$field['w'].'">';
                        foreach ($values as $k=>$v) {
                            $v = trim($v);
                            if ($v!='') {
                                $vs = explode(':',$v);
                                $vs = array_map('esc_html',$vs); $vs[1] = isset($vs[1])?$vs[1]:$vs[0];
                                $selected = !empty($field['d']) ? (strval($vs[0])==strval($field['d']) ? ' selected="selected"' : null) : null;
                                $hl.= '<option value="'.$vs[0].'"'.$selected.'>'.$vs[1].'</option>';
                            }
                        }
                        $hl.= '</select>';
                        break;
                    case 'radio': case 'checkbox':
                        $values = explode("\n",$field['s']);
                        $hl.= '<div id="'.$field['_n'].'" style="width:'.$field['w'].'">';
                        foreach ($values as $k=>$v) {
                            $v = trim($v);
                            if ($v!='') {
                                $vs = explode(':',$v);
                                $vs = array_map('esc_html',$vs); $vs[1] = isset($vs[1])?$vs[1]:$vs[0];
                                $checked = !empty($field['d']) ? (instr($vs[0],$field['d']) ? ' checked="checked"' : null) : null;
                                $hl.= '<label><input name="'.$field['_n'].($field['t']=='checkbox'?'['.$k.']':null).'" type="'.$field['t'].'" value="'.$vs[0].'"'.$checked.' />'.$vs[1].'</label>';
                            }
                        }
                        $hl.= '</div>';
                        break;
                    case 'basic': case 'editor':
                        $options = array();
                        $options['width'] = $field['w'];
                        if ($field['t']=='basic') {
                            $options['toobar'] = 'simple';
                            $options['height'] = '120';
                        }
                        $hl.= editor($field['_n'],$field['d'],$options);
                        break;
                    case 'upfile':
                        $hl.= '<input class="text" id="'.$field['_n'].'" name="'.$field['_n'].'" type="text" style="width:'.$field['w'].'" />&nbsp;<button type="button">'.__('Browse...').'</button>';
                        break;
                }
                
                $hl.=     '</td>';
                $hl.= '</tr>';
	    	}
	    }
        admin_return($hl);
	    break;
    default:
        if ('page.php' == $php_file) {
            current_user_can('page-list');
            $add_new = _x('Add New','page');
        } else {
            current_user_can('post-list');
            $add_new = _x('Add New','post');
        }
        admin_head('loadevents','post_list_init');
	    $page  = isset($_REQUEST['page'])?$_REQUEST['page']:1;
        $size  = isset($_REQUEST['size'])?$_REQUEST['size']:10;
        $model = isset($_REQUEST['model'])?$_REQUEST['model']:'';
        $category = isset($_REQUEST['category'])?$_REQUEST['category']:0;
        $query = array(
            'page' => '$',
            'size' => $size,
        );
        // 分页地址
        $page_url = PHP_FILE.'?'.http_build_query($query);
        // 排序方式
        $order = 'page.php'==$php_file ? 'ASC' : 'DESC';

        $conditions = array();
        if ('page.php' == $php_file) {
            $conditions[] = "`sortid`='-1'";
        } else {
            $conditions[] = "`sortid`<>'-1'";
        }
        // 根据分类筛选
        if ($category) {
            $sql = sprintf("SELECT `objectid` AS `postid` FROM `#@_term_relation` WHERE `taxonomyid`=%d ORDER BY `objectid` {$order}",esc_sql($category));
        } else {
            // 根据模型筛选
            if ($model) $conditions[] = sprintf("`model` = '%s'",esc_sql($model));
            // 没有任何筛选条件
            $where = ' WHERE '.implode(' AND ' , $conditions);
            $sql = "SELECT `postid` FROM `#@_post` {$where} ORDER BY `postid` {$order}";
        }
        $result = post_gets($sql, $page, $size);
        include ADMIN_PATH.'/admin-header.php';
        echo '<div class="wrap">';
        echo   '<h2>'.admin_head('title').'<a class="button" href="'.PHP_FILE.'?method=new">'.$add_new.'</a></h2>';
        echo   '<form action="'.PHP_FILE.'?method=bulk" method="post" name="postlist" id="postlist">';
        table_nav($page_url,$result);
        echo       '<table class="data-table" cellspacing="0">';
        echo           '<thead>';
        table_thead();
        echo           '</thead>';
        echo           '<tfoot>';
        table_thead();
        echo           '</tfoot>';
        echo           '<tbody>';
        if (0 < $result['length']) {
            foreach ($result['posts'] as $post) {
                $edit_url   = PHP_FILE.'?method=edit&postid='.$post['postid'];
                $categories = array();
                foreach($post['category'] as $category) {
                    $categories[] = '<a href="'.PHP_FILE.'?category='.$category['taxonomyid'].'">'.$category['name'].'</a>';
                }
                $actions = '<span class="edit"><a href="'.$edit_url.'">'.__('Edit').'</a> | </span>';
                $actions.= '<span class="create"><a href="javascript:;" onclick="post_create('.$post['postid'].')">'.__('Create').'</a> | </span>';
                $actions.= '<span class="delete"><a href="javascript:;" onclick="post_delete('.$post['postid'].')">'.__('Delete').'</a></span>';

                echo '<tr>';
                echo    '<td class="check-column"><input type="checkbox" name="listids[]" value="'.$post['postid'].'" /></td>';
                echo    '<td><strong><a href="'.$edit_url.'">'.$post['title'].'</a></strong><br/><div class="row-actions">'.$actions.'</div></td>';
                if (empty($post['model'])) {
                    echo '<td><a href="javascript:;">'.__('None').'</a></td>';
                } else {
                    echo '<td><a href="'.PHP_FILE.'?model='.$post['model']['langcode'].'">'.$post['model']['name'].'</a></td>';
                }

                // 检查文件是否已生成
                if (is_file(ABS_PATH.'/'.$post['path'])) {
                    echo '<td><img class="b6 os" src="'.ADMIN_ROOT.'images/t.gif" /><a href="'.WEB_ROOT.$post['path'].'" target="_blank">'.WEB_ROOT.$post['path'].'</a></td>';
                } else {
                    echo '<td><img class="b7 os" src="'.ADMIN_ROOT.'images/t.gif" /><a href="javascript:;" onclick="post_create('.$post['postid'].')">'.WEB_ROOT.$post['path'].'</a></td>';
                }

                if ('page.php' != $php_file) {
                    if (empty($categories)) {
                        echo '<td><a href="'.PHP_FILE.'?category=0">'.__('None').'</a></td>';
                    } else {
                        echo '<td>'.implode(',' , $categories).'</td>';
                    }

                }
                echo    '<td>'.date('Y-m-d H:i:s',$post['datetime']).'</td>';
                echo    '<td><img class="b'.($post['passed']+8).' os" src="'.ADMIN_ROOT.'images/t.gif" /></td>';
                echo '</tr>';
            }
        } else {
            echo           '<tr><td colspan="7" class="tc">'.__('No record!').'</td></tr>';
        }
        echo           '</tbody>';
        echo       '</table>';
        table_nav($page_url,$result);
        echo   '</form>';
        echo '</div>';
        include ADMIN_PATH.'/admin-footer.php';
        break;
}

/**
 * 批量操作
 *
 */
function table_nav($url,$result) {
    echo '<div class="table-nav">';
    echo     '<select name="actions">';
    echo         '<option value="">'.__('Bulk Actions').'</option>';
    echo         '<option value="create">'.__('Create').'</option>';
    echo         '<option value="delete">'.__('Delete').'</option>';
    echo     '</select>';
    echo     '<button type="button">'.__('Apply').'</button>';
    echo     page_list($url,$result['page'],$result['pages'],$result['total']);
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
    echo     '<th>'._x('Model','post').'</th>';
    echo     '<th>'._x('Path','post').'</th>';
    if ('page.php' != $php_file) {
        echo '<th>'._x('Categories','post').'</th>';
    }
    echo     '<th>'._x('Date','post').'</th>';
    echo     '<th>'._x('State','post').'</th>';
    echo '</tr>';
}

/**
 * 管理页面
 *
 * @param string $action
 */
function post_manage_page($action) {
    global $php_file;
    $referer = referer(PHP_FILE);
    $postid  = isset($_GET['postid'])?$_GET['postid']:0;
    $models  = model_gets(0);
    $suffix  = C('HTMLFileSuffix');
    if ($action=='add') {
        $mcode = isset($_GET['model'])?$_GET['model']:null;
    } else {
        $_DATA = post_get($postid);
        post_process($_DATA,'keywords');
        $mcode = $_DATA['model'];
    }

    $model    = $mcode ? model_get_bycode($mcode) : array_pop(array_slice($models,0,1));
    $sortid   = isset($_DATA['sortid'])?$_DATA['sortid']:null;
    $title    = isset($_DATA['title'])?$_DATA['title']:null;
    $path     = isset($_DATA['path'])?$_DATA['path']:$model['path'];
    $content  = isset($_DATA['content'])?$_DATA['content']:null;
    $template = isset($_DATA['template'])?$_DATA['template']:null;
    $keywords = isset($_DATA['keywords'])?$_DATA['keywords']:null;
    $categories  = isset($_DATA['category'])?$_DATA['category']:array();
    $description = isset($_DATA['description'])?$_DATA['description']:null;

    echo '<div class="wrap">';
    echo   '<h2>'.admin_head('title').'</h2>';
    echo   '<form action="'.PHP_FILE.'?method=save" method="post" name="postmanage" id="postmanage">';
    echo   '<fieldset>';
    echo       '<table class="form-table">';
    echo           '<tbody class="fixed-attr">';
    if ($models) {
        echo           '<tr>';
        echo               '<th><label for="model">'._x('Model','post').'</label></th>';
        echo               '<td><select name="model" id="model">';
        foreach ($models as $m) {
            $selected = isset($model['langcode']) && $m['langcode']==$model['langcode']?'selected="selected"':'';
        	echo               '<option value="'.$m['langcode'].'"'.$selected.'>'.$m['name'].'</option>';
        }
        echo               '</select></td>';
        echo           '</tr>';
    }
    $hidden = '';
    if ('page.php' == $php_file) {
        $hidden = '<input type="hidden" name="sortid" value="-1" />';
    } else {
        echo           '<tr class="taxonomyid">';
        echo               '<th><label for="taxonomyid">'._x('Categories','post').'</label></th>';
        echo               '<td>';
        echo                   categories_tree($sortid,$categories);
        echo               '</td>';
        echo           '</tr>';
    }
    
    echo               '<tr>';
    echo                   '<th><label for="title">'._x('Title','post').'<span class="description">'.__('(required)').'</span></label></th>';
    echo                   '<td>';
    echo                       '<input class="text" id="title" name="title" type="text" size="70" value="'.$title.'" />';
    echo                       '&nbsp;<label for="autokeys"><input type="checkbox" value="1" id="autokeys" name="autokeys" checked="checked">'.__('Auto get keywords').'</label>';
    echo                   '</td>';
    echo               '</tr>';
    echo               '<tr>';
    echo                   '<th><label for="content">'._x('Content','post').'</label></th>';
    echo                   '<td>'.editor('content',$content).'</td>';
    echo               '</tr>';
    echo               '<tr>';
    echo                   '<th><label for="path">'._x('Path','post').'<span class="description">'.__('(required)').'</span></label></th>';
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
    echo           '<tbody class="extend-attr"></tbody>';
    echo       '</table>';
    echo   '</fieldset>';
    echo   '<fieldset>';
    echo       '<a href="javascript:;" class="toggle" title="'.__('Click to toggle').'"><br/></a>';
    echo       '<h3>'.__('More attribute').'</h3>';
    echo       '<table class="form-table">';
    echo           '<tbody class="more-attr">';
    echo               '<tr>';
    echo                   '<th><label for="template">'._x('Page Template','post').'</label></th>';
    echo                   '<td>';
    echo                       '<select id="template" name="template">';
    echo                           $models?'<option value="">'.__('Use the model set').'</option>':null;
    echo                           options(system_themes_path(),C('TemplateSuffixs'),'<option value="#value#"#selected#>#name#</option>',$template);
    echo                       '</select>';
    echo                   '</td>';
    echo               '</tr>';
    echo               '<tr>';
    echo                   '<th><label for="keywords">'._x('Keywords','post').'</label></th>';
    echo                   '<td><input class="text" type="text" size="70" name="keywords" id="keywords" value="'.$keywords.'" />&nbsp;<button type="button" rel="keywords">'.__('Get').'</button></td>';
    echo               '</tr>';
    echo               '<tr>';
    echo                   '<th><label for="description">'._x('Description','post').'<br /><span class="description">'.__('(Maximum of 250)').'</span></label></th>';
    echo                   '<td><textarea class="text" cols="70" rows="5" id="description" name="description">'.$description.'</textarea></td>';
    echo               '</tr>';
    if ('page.php' != $php_file) {
        echo           '<tr>';
        echo               '<th><label>'._x('Other','post').'</label></th>';
        echo               '<td><label for="createlists"><input type="checkbox" name="createlists" value="1" id="createlists" />'.__('Update Category Lists').'</label></td>';
        echo           '</tr>';
    }
    echo           '</tbody>';
    echo       '</table>';
    echo   '</fieldset>';
    echo   '<p class="submit">';
    if ($action=='add') {
        echo   '<button type="submit">'.__('Add Post').'</button>'.$hidden;
    } else {
        echo   '<button type="submit">'.__('Update Post').'</button><input type="hidden" name="postid" value="'.$postid.'" />'.$hidden;
    }
    echo       '<button type="button" onclick="LazyCMS.redirect(\''.$referer.'\')">'.__('Back').'</button>';
    echo   '</p>';
    echo  '</form>';
    echo '</div>';
}

/**
 * 显示分类数
 *
 * @param int $sortid
 * @param array $categories
 * @param array $trees
 * @return string
 */
function categories_tree($sortid,$categories=array(),$trees=null) {
    static $func = null; if (!$func) $func = __FUNCTION__;
    $hl = sprintf('<ul class="%s">',is_null($trees) ? 'categories' : 'children');
    if ($trees === null) {
        $trees = taxonomy_get_trees();
        $checked = (empty($sortid) || empty($trees)) ? ' checked="checked"' : '';
        $hl.= sprintf('<li><input type="radio" name="sortid" id="sortid" value="0"%s /><label for="sortid">'.__('Uncategorized').'</label></li>',$checked);
    }
    foreach ($trees as $i=>$tree) {
        $checked = instr($tree['taxonomyid'],$categories) && $sortid!=$tree['taxonomyid'] ? ' checked="checked"' : '';
        $main_checked = $tree['taxonomyid']==$sortid?' checked="checked"':'';
        $hl.= sprintf('<li><input type="radio" name="sortid" value="%d"%s />',$tree['taxonomyid'],$main_checked);
        $hl.= sprintf('<label class="selectit" for="category-%d">',$tree['taxonomyid']);
        $hl.= sprintf('<input type="checkbox" id="category-%d" name="category[]" value="%d"%s />%s</label>',$tree['taxonomyid'],$tree['taxonomyid'],$checked,$tree['name']);
    	if (isset($tree['subs'])) {
    		$hl.= $func($sortid,$categories,$tree['subs']);
    	}
        $hl.= '</li>';
    }
    $hl.= '</ul>';
    return $hl;
}
