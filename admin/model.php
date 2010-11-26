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
// 加载公共文件
require dirname(__FILE__).'/admin.php';
// 查询管理员信息
$_USER = user_current();
// 标题
system_head('title',  __('Models'));
system_head('styles', array('css/model'));
system_head('scripts',array('js/model'));
system_head('jslang',array(
    'Add New' => _x('Add New','field'),
    'Edit'    => _x('Edit','field'),
    'The label field is empty.' => _x('The label field is empty.','field'),
    'The name field is empty.'  => _x('The name field is empty.','field'),
    'The name already exists.'  => _x('The name already exists.','field'),
));
// 动作
$method = isset($_REQUEST['method'])?$_REQUEST['method']:null;

switch ($method) {
    // 强力插入
	case 'new':
	    // 权限检查
	    current_user_can('model-new');
	    // 重置标题
	    system_head('title',__('Add New Model'));
	    // 添加JS事件
	    system_head('loadevents','model_manage_init');
	    include ADMIN_PATH.'/admin-header.php';
        // 显示页面
	    model_manage_page('add');
        include ADMIN_PATH.'/admin-footer.php';
	    break;
	// 活塞式运动，你懂得。。。
	case 'edit':
	    // 所属
        $parent_file = 'model.php';
	    // 权限检查
	    current_user_can('model-edit');
	    // 重置标题
	    system_head('title',__('Edit Model'));
	    // 添加JS事件
	    system_head('loadevents','model_manage_init');
	    include ADMIN_PATH.'/admin-header.php';
	    model_manage_page('edit');
        include ADMIN_PATH.'/admin-footer.php';
	    break;
	// 保存
	case 'save':
        $modelid = isset($_POST['modelid'])?$_POST['modelid']:null;
	    $purview = $modelid?'model-edit':'model-new';
	    current_user_can($purview);

        if (validate_is_post()) {
            $name     = isset($_POST['name'])?$_POST['name']:null;
            $code     = isset($_POST['code'])?$_POST['code']:null;
            $path     = isset($_POST['path'])?$_POST['path']:null;
            $page     = isset($_POST['page'])?$_POST['page']:null;
            $fields   = isset($_POST['field'])?$_POST['field']:null;
            $language = isset($_POST['language'])?$_POST['language']:language();
            $langcode = sprintf('%s:%s',$language,$code);

            validate_check(array(
                // 模型名不能为空
                array('name',VALIDATE_EMPTY,_x('The name field is empty.','model')),
                // 模型名长度必须是2-30个字符
                array('name',VALIDATE_LENGTH,_x('The name field length must be %d-%d characters.','model'),1,30),
            ));


            if ($modelid) {
                // 模型存在
                if ($model = model_get_bycode($langcode)) {
                    if ($model['modelid']==$modelid) {
                        $is_exist = false;
                    } else {
                        $is_exist = true;
                    }
                } else {
                    $is_exist = false;
                }
            } else {
                $is_exist = model_get_bycode($langcode) ? true : false;
            }

            validate_check(array(
                // 模型标识不能为空
                array('code',VALIDATE_EMPTY,_x('The code field is empty.','model')),
                // 模型标识长度必须是2-30个字符
                array('code',VALIDATE_LENGTH,_x('The code field length must be %d-%d characters.','model'),1,30),
                // 模型标识已存在
                array('code',!$is_exist,_x('The code already exists.','model')),
            ));

            validate_check(array(
                array('path',VALIDATE_EMPTY,_x('The path field is empty.','model')),
            ));

            // 安全有保证，做爱做的事吧！
            if (validate_is_ok()) {
                $info = array(
                    'code'     => esc_html($code),
                    'name'     => esc_html($name),
                    'path'     => esc_html($path),
                    'page'     => esc_html($page),
                    'language' => esc_html($language),
                );
                if (current_user_can('model-fields',false)) {
                    $info['fields'] = serialize($fields);
                }
                // 编辑
                if ($modelid) {
                    model_edit($modelid,$info);
                    ajax_success(__('Model updated.'),"LazyCMS.redirect('".PHP_FILE."');");
                } 
                // 强力插入了
                else {
                    model_add($info);
                    ajax_success(__('Model created.'),"LazyCMS.redirect('".PHP_FILE."');");
                }
            }
        }
	    break;
	// 批量动作
	case 'bulk':
	    $action  = isset($_POST['action'])?$_POST['action']:null;
	    $listids = isset($_POST['listids'])?$_POST['listids']:null;
	    if (empty($listids)) {
	    	ajax_error(__('Did not select any item.'));
	    }
	    switch ($action) {
	        // 删除
	        case 'delete':
	            current_user_can('model-delete');
	            foreach ($listids as $modelid) {
	            	model_delete($modelid);
	            }
	            ajax_success(__('Models deleted.'),"LazyCMS.redirect('".PHP_FILE."');");
	            break;
	        // 启用
	        case 'enabled':
	            foreach ($listids as $modelid) {
	            	model_edit($modelid,array(
	            	  'state' => 0
	            	));
	            }
	            ajax_success(__('Models enabled.'),"LazyCMS.redirect('".PHP_FILE."');");
	            break;
	        // 禁用
	        case 'disabled':
	            foreach ($listids as $modelid) {
	            	model_edit($modelid,array(
	            	  'state' => 1
	            	));
	            }
	            ajax_success(__('Models disabled.'),"LazyCMS.redirect('".PHP_FILE."');");
	            break;
	        // 导出
	        case 'export':
	            // 批量导出，打包成zip
	            break;
            default:
                ajax_alert(__('Parameter is invalid.'));
                break;
	    }
	    break;
	// 导出
	case 'export':
	    current_user_can('model-export');
	    break;
	// 导入
	case 'import':
	    current_user_can('model-import');
	    include ADMIN_PATH.'/admin-header.php';
        
        include ADMIN_PATH.'/admin-footer.php';
	    break;
	// 字段管理
	case 'field':
	    current_user_can('model-fields');
	    $id = isset($_POST['id'])?$_POST['id']:null;
	    $l  = isset($_POST['l'])?$_POST['l']:null;
	    $h  = isset($_POST['h'])?$_POST['h']:null;
	    $n  = isset($_POST['n'])?$_POST['n']:null;
	    $so = isset($_POST['so'])?$_POST['so']:null;
	    $t  = isset($_POST['t'])?$_POST['t']:null;
	    $w  = isset($_POST['w'])?$_POST['w']:'200px';
	    $v  = isset($_POST['v'])?$_POST['v']:null;
	    $s  = isset($_POST['s'])?$_POST['s']:null;
	    $a  = isset($_POST['a'])?$_POST['a']:null;
	    $c  = isset($_POST['c'])?$_POST['c']:255;
	    $d  = isset($_POST['d'])?$_POST['d']:null;
	    $verify = array(
	       __("Can't be empty")             => 'IS_EMPTY|'.__('The field value is empty.'),
	       __('Fixed-length')               => 'LENGTH_LIMIT|'.__('The field value length must be %d-%d characters.').'|1-100',
	       __('Value1 = Value2')            => 'IS_EQUAL|'.__('Same the two fields.').'|[field]',
	       __('E-mail')                     => 'IS_EMAIL|'.__('The e-mail address isn\'t correct.'),
	       __('Case-Insensitive letters')   => 'IS_LETTERS|'.__('This field value is not a letter.'),
	       __('Numeric:[0-9]')              => 'IS_NUMERIC|'.__('This field value is not a number.'),
	       _x('URL','model')               => 'IS_URL|'.__('This field value is not a URL.'),
	       __('Custom validation')          => 'CUSTOM|'.__('Error Message'),
	    );
	    $hl = '<div class="wrapper">';
	    $hl.= '<a href="javascript:;" class="help">'.get_icon('f1').'</a>';
	    $hl.= '<form id="model-field-table">';
	    $hl.= '<table class="model-field-table">';
	    $hl.=    '<tr><th><label for="field_l">'._x('Label','model').'</label></th><td><input class="text" id="field_l" name="l" type="text" size="35" value="'.$l.'" />';
	    $hl.=    '<label for="field_is_help"><input type="checkbox" id="field_is_help"'.($h?' checked="checked"':null).' />'.__('Need help').'</label></td></tr>';
	    $hl.=    '<tr id="field_help" class="hide"><th class="vt"><label for="field_h">'._x('Help','model').'</label></th><td><textarea class="text" name="h" id="field_h" rows="2" cols="40">'.$h.'</textarea></td></tr>';
	    $hl.=    '<tr><th><label for="field_n">'._x('Field','model').'</label></th><td><input class="text" id="field_n" name="n" type="text" size="30" value="'.$n.'" />';
	    $hl.=    '<label for="can_search"><input type="checkbox" id="can_search" name="so" value="1"'.($so?' checked="checked"':null).' />'.__('Can search').'</label></td></tr>';
	    $hl.=    '<tr><th><label for="field_t">'._x('Type','model').'</label></th><td>';
	    $hl.=        '<select id="field_t" name="t">'; $types = model_get_types();
	    foreach ($types as $type=>$text) {
	        $selected = $type==$t?' selected="selected"':null;
	    	$hl.=      '<option value="'.$type.'"'.$selected.'>'.$text.'</option>';
	    }
	    $hl.=        '</select>';
	    $hl.=        '<label for="field_w">'.__('Width').'</label><select name="w" id="field_w" edit="true" default="'.$w.'">';
        $hl.=          '<option value="auto">'.__('Auto').'</option>';
	    for($i=1;$i<=16;$i++){
            $hl.=      '<option value="'.($i*50).'px">'.($i*50).'px</option>';
        }
	    $hl.=        '</select>';
	    $hl.=        '<label for="field_is_verify"><input type="checkbox" id="field_is_verify"'.($v?' checked="checked"':null).' />'.__('Need to verify').'</label>';
	    $hl.=    '</td></tr>';
	    $hl.=    '<tr id="field_verify" class="hide">';
	    $hl.=        '<th class="vt"><label for="field_sv">'.__('Verify rule').'</label></th>';
	    $hl.=        '<td><select name="sv" id="field_sv">';
	    foreach ($verify as $text=>$val) {
            $hl.=       '<option value="'.$val.'">'.$text.'</option>';
        }
	    $hl.=        '</select>&nbsp;<a href="javascript:;" rule="+">'.get_icon('b3').'</a><a href="javascript:;" rule="-">'.get_icon('b4').'</a>';
	    $hl.=        '<br/><textarea class="text" name="v" id="field_v" rows="3" cols="40">'.$v.'</textarea></td>';
	    $hl.=    '</tr>';
	    $hl.=    '<tr id="field_serialize" class="hide"><th class="vt"><label for="field_s">'._x('Serialize','model').'</label></th><td><textarea class="text" name="s" id="field_s" rows="3" cols="40">'.$s.'</textarea></td></tr>';
	    $hl.=    '<tr id="field_toolbar" class="hide"><th class="vt"><label>'.__('Toolbar').'</label></th><td class="toolbar">';
	    $hl.=        '<label><input type="checkbox" name="a[]"'.(instr('Img', $a)?' checked="checked"':null).' value="Img" /> '.__('Insert Image').'</label>';
	    $hl.=        '<label><input type="checkbox" name="a[]"'.(instr('Flash', $a)?' checked="checked"':null).' value="Flash" /> '.__('Insert Flash').'</label>';
	    $hl.=        '<label><input type="checkbox" name="a[]"'.(instr('Flv', $a)?' checked="checked"':null).' value="Flv" /> '.__('Insert Flv').'</label>';
	    $hl.=        '<label><input type="checkbox" name="a[]"'.(instr('Emot', $a)?' checked="checked"':null).' value="Emot" /> '.__('Insert Emote').'</label>';
	    $hl.=        '<label><input type="checkbox" name="a[]"'.(instr('Table', $a)?' checked="checked"':null).' value="Table" /> '.__('Insert Table').'</label>';
        $hl.=        '<label><input type="checkbox" name="a[]"'.(instr('GoogleMap', $a)?' checked="checked"':null).' value="GoogleMap" /> '.__('Insert Google Map').'</label>';
	    $hl.=        '<label><input type="checkbox" name="a[]"'.(instr('Pagebreak', $a)?' checked="checked"':null).' value="Pagebreak" /> '.__('Insert Pagebreak').'</label>';
        $hl.=        '<label><input type="checkbox" name="a[]"'.(instr('Removelink', $a)?' checked="checked"':null).' value="Removelink" /> '.__('Remove external links').'</label>';
        $hl.=    '</td></tr>';
	    $hl.=    '<tr id="field_length" class="hide">';
	    $hl.=        '<th><label for="field_c">'._x('Length','model').'</label></th>';
	    $hl.=        '<td><select name="c" id="field_c" edit="true" default="'.$c.'">';
	    foreach (array(10,20,30,50,100,255) as $v) {
            $hl.=       '<option value="'.$v.'">'.$v.'</option>';
        }
	    $hl.=        '</select></td>';
	    $hl.=    '</tr>';
	    $hl.=    '<tr id="field_default" class="hide"><th><label for="field_d">'._x('Default','model').'</label></th><td><input class="text" id="field_d" name="d" type="text" size="40" value="'.$d.'" /></td></tr>';
	    $hl.= '</table>';
	    $hl.= '<div class="buttons"><button type="button" rel="save">'.__('Save').'</button><button type="button" rel="close">'.__('Cancel').'</button></div>';
	    $hl.= '<input type="hidden" name="id" value="'.$id.'" />';
	    $hl.= '</form></div>';
	    ajax_return($hl);
	    break;
	default:
	    current_user_can('model-list');
	    system_head('loadevents','model_list_init');
	    $models = model_gets();
        include ADMIN_PATH.'/admin-header.php';
        echo '<div class="wrap">';
        echo   '<h2>'.system_head('title').'<a class="button" href="'.PHP_FILE.'?method=new">'._x('Add New','model').'</a></h2>';
        echo   '<form action="'.PHP_FILE.'?method=bulk" method="post" name="modellist" id="modellist">';
        table_nav();
        echo       '<table class="data-table" cellspacing="0">';
        echo           '<thead>';
        table_thead();
        echo           '</thead>';
        echo           '<tfoot>';
        table_thead();
        echo           '</tfoot>';
        echo           '<tbody>';
        if ($models) {
            foreach ($models as $model) {
                $href = PHP_FILE.'?method=edit&modelid='.$model['modelid'];
                $actions = '<span class="edit"><a href="'.$href.'">'.__('Edit').'</a> | </span>';
                //$actions.= '<span class="export"><a href="'.PHP_FILE.'?method=export&modelid='.$model['modelid'].'">'.__('Export').'</a> | </span>';
                $actions.= '<span class="enabled"><a href="javascript:;" onclick="model_state(\'enabled\','.$model['modelid'].')">'.__('Enabled').'</a> | </span>';
                $actions.= '<span class="disabled"><a href="javascript:;" onclick="model_state(\'disabled\','.$model['modelid'].')">'.__('Disabled').'</a> | </span>';
                $actions.= '<span class="delete"><a href="javascript:;" onclick="model_delete('.$model['modelid'].')">'.__('Delete').'</a></span>';
                echo       '<tr>';
                echo           '<td class="check-column"><input type="checkbox" name="listids[]" value="'.$model['modelid'].'" /></td>';
                echo           '<td><strong><a href="'.$href.'">'.$model['name'].'</a></strong><br/><div class="row-actions">'.$actions.'</div></td>';
                echo           '<td>'.$model['code'].'</td>';
                echo           '<td>'.code2lang($model['language']).'</td>';
                echo           '<td>'.get_icon('c'.($model['state']+3)).'</td>';
                echo       '</tr>';
            }
        } else {
            echo           '<tr><td colspan="4" class="tc">'.__('No record!').'</td></tr>';
        }
        echo           '</tbody>';
        echo       '</table>';
        table_nav();
        echo   '</form>';
        echo '</div>';
        include ADMIN_PATH.'/admin-footer.php';
        break;
}

/**
 * 批量操作
 *
 */
function table_nav() {
    echo '<div class="table-nav">';
    echo     '<select name="actions">';
    echo         '<option value="">'.__('Bulk Actions').'</option>';
    echo         '<option value="export">'.__('Export').'</option>';
    echo         '<option value="enabled">'.__('Enabled').'</option>';
    echo         '<option value="disabled">'.__('Disabled').'</option>';
    echo         '<option value="delete">'.__('Delete').'</option>';
    echo     '</select>';
    echo     '<button type="button">'.__('Apply').'</button>';
    echo '</div>';
}
/**
 * 表头
 *
 */
function table_thead() {
    echo '<tr>';
    echo     '<th class="check-column"><input type="checkbox" name="select" value="all" /></th>';
    echo     '<th>'._x('Name','model').'</th>';
    echo     '<th>'._x('Code','model').'</th>';
    echo     '<th>'._x('Language','model').'</th>';
    echo     '<th>'._x('State','model').'</th>';
    echo '</tr>';
}

/**
 * 管理页面
 *
 * @param string $action
 */
function model_manage_page($action) {
    $referer = referer(PHP_FILE);
    $modelid  = isset($_GET['modelid'])?$_GET['modelid']:0;
    if ($action!='add') {
    	$_MODEL = model_get_byid($modelid);
    }
    $suffix   = C('HTMLFileSuffix');
    $default  = sprintf('%%Y%%m%%d/%%ID%s',$suffix);
    $language = isset($_MODEL['language'])?$_MODEL['language']:language();
    $name     = isset($_MODEL['name'])?$_MODEL['name']:null;
    $code     = isset($_MODEL['code'])?$_MODEL['code']:null;
    $path     = isset($_MODEL['path'])?$_MODEL['path']:$default;
    $list     = isset($_MODEL['list'])?$_MODEL['list']:null;
    $page     = isset($_MODEL['page'])?$_MODEL['page']:null;
    $fields   = isset($_MODEL['fields'])?$_MODEL['fields']:null;
    echo '<div class="wrap">';
    echo   '<h2>'.system_head('title').'</h2>';
    echo   '<form action="'.PHP_FILE.'?method=save" method="post" name="modelmanage" id="modelmanage">';
    echo     '<fieldset>';
    echo       '<table class="form-table">';
    echo           '<tr>';
    echo               '<th><label for="language">'._x('Language','model').'</label></th>';
    echo               '<td><select name="language" id="language">';
    echo                  '<option value="en"'.($language=='en'?' selected="selected"':null).'>'.__('English').'</option>';
    echo                   options('@.locale','lang','<option value="#value#"#selected#>#name#</option>',$language);
    echo               '</select></td>';
    echo           '</tr>';
    echo           '<tr>';
    echo               '<th><label for="name">'._x('Name','model').'<span class="resume">'.__('(required)').'</span></label></th>';
    echo               '<td><input class="text" id="name" name="name" type="text" size="20" value="'.$name.'" /></td>';
    echo           '</tr>';
    echo           '<tr>';
    echo               '<th><label for="code">'._x('Code','model').'<br /><span class="resume">'.__('(required,only)').'</span></label></th>';
    echo               '<td><input class="text" id="code" name="code" type="text" size="20" value="'.$code.'" /></td>';
    echo           '</tr>';
    echo           '<tr>';
    echo               '<th><label for="path">'._x('Path','model').'<span class="resume">'.__('(required)').'</span></label></th>';
    echo               '<td><input class="text" id="path" name="path" type="text" size="60" value="'.$path.'" /><div class="rules">';
    echo                   '<a href="#%ID'.$suffix.'" title="%ID">['.__('Post ID').']</a>';
    echo                   '<a href="#%MD5'.$suffix.'" title="%MD5">['.__('MD5 Value').']</a>';
    echo                   '<a href="#%PY'.$suffix.'" title="%PY">['.__('Pinyin').']</a>';
    echo                   '<a href="#%Y" title="%Y">['.strftime('%Y').']</a>';
    echo                   '<a href="#%m" title="%m">['.strftime('%m').']</a>';
    echo                   '<a href="#%d" title="%d">['.strftime('%d').']</a>';
    echo                   '<a href="#%a" title="%a">['.strftime('%a').']</a>';
    echo               '</div></td>';
    echo           '</tr>';
    echo           '<tr>';
    echo               '<th><label for="pagetemplate">'.__('Page Template').'</label></th>';
    echo               '<td>';
    echo                   '<select id="pagetemplate" name="page">';
    echo                        options(system_themes_path(),C('TemplateSuffixs'),'<option value="#value#"#selected#>#name#</option>',$page);
    echo                   '</select>';
    echo               '</td>';
    echo           '</tr>';
    if (current_user_can('model-fields',false)) {
        echo        '<tr>';
        echo           '<td colspan="2">';
        echo               '<fieldset id="fields">';
        echo               '<a href="javascript:;" class="toggle" title="'.__('Click to toggle').'"><br/></a>';
        echo               '<h3>'._x('Fields','model').'</h3>';
        echo               '<div class="fields-data">';
        fields_actions();
        echo                   '<div class="fields-table">';
        echo                      '<table id="table-fields" class="data-table" cellspacing="0"">';
        echo                         '<thead>';
        fields_table_thead();
        echo                         '</thead>';
        echo                         '<tfoot>';
        fields_table_thead();
        echo                         '</tfoot>';
        echo                         '<tbody>';
        if ($fields) {
            foreach ($fields as $i=>$field) {
                $actions  = '<span class="edit"><a href="#'.$i.'">'.__('Edit').'</a> | </span>';
                $actions .= '<span class="delete"><a href="#'.$i.'">'.__('Delete').'</a></span>';
                $textarea = '<textarea class="hide" name="field[]">'.http_build_query($field).'</textarea>';
                echo                    '<tr id="field-index-'.$i.'" index="'.$i.'">';
                echo                       '<td class="check-column"><input type="checkbox" name="listids[]" value="'.$i.'" /></td>';
                echo                       '<td><strong class="edit"><a href="#'.$i.'">'.$field['l'].'</a></strong><br/><div class="row-actions">'.$actions.'</div>'.$textarea.'</td>';
                echo                       '<td>'.$field['n'].'</td>';
                echo                       '<td>'.model_get_types($field['t']).'</td>';
                echo                       '<td>'.(empty($field['d'])?'NULL':$field['d']).'</td>';
                echo                    '</tr>';
            }
        } else {
            echo                        '<tr class="empty"><td colspan="5" class="tc">'.__('No record!').'</td></tr>';
        }
        echo                         '</tbody>';
        echo                      '</table>';
        echo                   '</div>';
        fields_actions();
        echo                '</div>';
        echo             '</div>';
        echo          '</td>';
        echo       '</tr>';
    }
    echo       '</table>';
    echo     '</fieldset>';
    echo     '<p class="submit">';
    if ($action=='add') {
        echo   '<button type="submit">'.__('Add Model').'</button>';
    } else {
        echo   '<button type="submit">'.__('Update Model').'</button><input type="hidden" name="modelid" value="'.$modelid.'" />';
    }
    echo       '<button type="button" onclick="LazyCMS.redirect(\''.$referer.'\')">'.__('Back').'</button>';
    echo     '</p>';
    echo   '</form>';
    echo '</div>';
}
/**
 * 表头
 *
 */
function fields_table_thead() {
    echo '<tr class="nodrop">';
    echo     '<th class="check-column"><input type="checkbox" name="select" value="all" /></th>';
    echo     '<th>'._x('Label','model').'</th>';
    echo     '<th>'._x('Field','model').'</th>';
    echo     '<th>'._x('Type','model').'</th>';
    echo     '<th>'._x('Default','model').'</th>';
    echo '</tr>';
}
/**
 * 批量操作
 *
 */
function fields_actions() {
    echo '<div class="actions">';
    echo     '<button type="button" class="delete">'._x('Delete','field').'</button>';
    echo     '<button type="button" class="addnew">'._x('Add New','field').'</button>';
    echo '</div>';
}