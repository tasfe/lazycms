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
 * | Copyright (C) 2007-2008 LazyCMS.net All rights reserved.                  |
 * +---------------------------------------------------------------------------+
 * | LazyCMS is free software. This version use Apache License 2.0             |
 * | See LICENSE.txt for copyright notices and details.                        |
 * +---------------------------------------------------------------------------+
 */
defined('COM_PATH') or die('Restricted access!');
/**
 * 内容模块语言包
 */
return array(
    'name'  => '内容管理',

    'nomodel' => '请先创建并启用一个模型',
    /* 标签中心 */
    'label' => array(
        'title' => '标签中心',
    ),

    /* 生成中心 */
    'create' => array(
        'title' => '生成中心',
    ),

    /* 模型管理 */
    'model' => array(
        'title' => '模型管理',
        'import'    => array(
            'title' => '模型导入',
            'file'  => '模型文件',
            'name'  => '模型名称',
            'ename' => '模型标识',
            'code'  => '模型代码',
            'submit'=> '导入',
        ),
        'addlist'   => '添加列表模型',
        'addpage'   => '添加单页模型',
        'editlist'  => '编辑列表模型',
        'editpage'  => '编辑单页模型',
        'name'  => '模型名称',
        'ename' => '模型标识',
        'table' => '数据表名',
        'state' => '状态',
        'path'  => '生成规则',
        'template'  => array(
            'sort'  => '列表模板',
            'page'  => '内容模板',
        ),
        'fields'    => array(
            'title' => '字段管理',
            'add'   => '添加字段',
            'text'  => '表单文字',
            'help'  => '帮助信息',
            'ename' => '字段名',
            'input' => '输入类型',
            'width' => '宽',
            'rules' => '验证规则',
            'value' => '序列值',
            'length'    => '最大长度',
            'default'   => '默认值',
            'option'    => '更多属性',
            'ishelp'    => '需要帮助',
            'validate'  => '需要验证',
            'iskeyword' => '点亮自动获取关键词',
            'description'=> '点亮自动获取简述',
            'type'  => array(
                'input'     => '输入框',
                'textarea'	=> '文本框',
                'select'	=> '下拉列表框',
                'radio'	    => '单选框',
                'checkbox'	=> '复选框',
                'basic'	    => '简易编辑器',
                'editor'	=> '内容编辑器',
                'date'      => '日期选择器',
                'upfile'	=> '文件上传框',
            ),
            'check' => array(
                'label' => '表单文字不能为空',
                'ename' => '此项不能为空',
                'ename1'=> '此项只能使用：字母、数字、下划线、中杠',
                'restrict'  => '此字段名已被系统使用',
                'length'    => '最大长度不能为空',
                'length1'   => '最大长度必须为数字',
            )
        ),
        'check' => array(
            'name'  => '请输入模型名称',
            'ename' => '请输入模型标识',
            'code'  => '模型代码不能为空',
            'exist' => '模型标识重复，请修改模型标识',
        ),
        'alert' => array(
            'add'       => '添加模型成功',
            'edit'      => '编辑模型成功',
            'noselect'  => '请选择一个模型',
            'delete'    => '删除模型成功',
            'lock'      => '锁定模型成功',
            'unlock'    => '启用模型成功',
            'import'    => '导入模型成功',
        )
    ),

    /* 分类管理 */
    'sort'  => array(
        'title' => '分类管理',
        'add'   => '添加分类',
        'edit'  => '编辑分类',
        'belong'    => '所属分类',
        'topsort'   => '顶级分类',
        'name'      => '分类名称',
        'count'     => '文档数',
        'path'      => '列表目录',
        'model'     => '内容类型',
        'defaultemplate'    => '使用模型设置',
        'sortemplate'       => '列表模板',
        'pagetemplate'      => '内容模板',
        'check' => array(
            'name'  => '分类名称不能为空',
            'path'  => '分类目录不能为空',
            'path1' => '路径错误',
            'path2' => '路径重复',
        ),
        'alert' => array(
            'add'   => '添加分类成功',
            'edit'  => '编辑分类成功',
            'delete'=> '删除分类成功',
            'noselect'  => '请选择一个分类',
        )
    ),
    
    /* 文档管理 */
    'article'   => array(
        'title' => '文档管理',
        'model' => '模型',
        'sort'  => '分类',
        'path'  => '路径',
        'hits'  => '点击量',
        'digg'  => 'Digg',
        'date'  => '日期',
        'size'  => '每页条数',
        'nosort'    => '不属于任何分类',
        'sortall'   => '所有分类',
        'keyword'   => '关键词',
        'description'   => 'Meta简述',
        'fields'    => '列表字段',
        'view'      => '显示',
        'submit'    => '提交',
        'cancel'    => '取消',
        'select'    => '请选择分类...',
        'check'     => array(
            'path'  => '路径不能为空',
            'path1' => '路径错误',
            'path2' => '路径重复',
            'description'   => '简述内容不要超过250个字符',
        ),
        'alert'     => array(
            'noselect'  => '请选择一个文档',
            'create'    => '生成文档成功',
            'delete'    => '删除文档成功',
            'add'       => '添加文档成功',
            'edit'      => '编辑文档成功',
        ),
    ),

    /* 单页管理 */
    'onepage'   => array(
        'title' => '单页管理',
        'model' => '模型',
        'path'  => '路径',
        'hits'  => '点击量',
        'digg'  => 'Digg',
        'date'  => '日期',
        'keyword'   => '关键词',
        'description'   => 'Meta简述',
        'fields'    => '列表字段',
        'view'      => '显示',
        'check'     => array(
            'path'  => '路径不能为空',
            'path1' => '路径错误',
            'path2' => '路径重复',
            'description'   => '简述内容不要超过250个字符',
        ),
        'alert'     => array(
            'noselect'  => '请选择一个文档',
			'create'    => '生成文档成功',
            'delete'    => '删除文档成功',
            'add'       => '添加文档成功',
            'edit'      => '编辑文档成功',
        ),
    ),

    /* 验证信息 */
    'validate'  => array(
        'url'   => '匹配网址',
        'empty' => '不能为空',
        'limit' => '限制长度',
        'equal' => '对比两个值相等',
        'email' => '电子邮箱',
        'letter'    => '英文字母',
        'number'    => '必须是数字',
        'custom'    => '自定义验证',
        'error'     => array(
            'url'   => '输入的网址格式不正确',
            'empty' => '必填项，不能为空',
            'limit' => '内容长度必须是1-100个字符',
            'equal' => '两个值的内容不相等',
            'email' => '邮箱格式不正确',
            'letter'    => '必须是英文字母',
            'number'    => '必须是数字',
            'custom'    => '这里是错误提示',
            'regular'   => '这里是正则表达式',
        )
    ),
    /* 帮助信息 */
    'help'  => array(
        'title'     => '帮助',
        'model'     => array(
            'ename' => '模型英文名称，用来作为模板标签的一部分',
            'path'  => '
                |[b]生成文件的命名规则：[/b]
                |%I=ID编号
                |%M=MD5值
                |%P=标题拼音
                |%Y=4位数字表示的年份：1999 或 2008
                |%y=2位数字表示的年份：99 或 08
                |%m=数字表示的月份，01 到 12
                |%d=月份中的第几天，01 到 31
            ',
            'fields'    => array(
                'help'  => '帮助信息，可以使用UBB标签',
                'ename' => '
                    |必填，用处很多了，以下字段名已被系统使用：
                    |(id,sortid,order,date,hits,digg,passed,userid,path,
                    |keywords,description,isdel)
                ',
                'rules' => '
                    |可以有多个规则，使用“分号”分割。
                    |规则：字段名|类型|错误提示|其它
                ',
                'value' => '
                    |[pre]例：value:name
                    |    value1:name1
                    |    value2:name2[/pre]
                ',
            ),
        ),
        'sort'  => array(
            'path'  => '不支持 <\:*?"|,> 字符，不能以/开始或结束',
            'model' => '选择哪些内容类型使用此分类',
        ),
        'article'   => array(
            'path'  => '
                |[b]生成文件的命名规则：[/b]
                |%I=ID编号
                |%M=MD5值
                |%P=标题拼音
                |%Y=4位数字表示的年份：1999 或 2008
                |%y=2位数字表示的年份：99 或 08
                |%m=数字表示的月份，01 到 12
                |%d=月份中的第几天，01 到 31
            '
        ),
        'onepage'   => array(
            'path'  => '
                |[b]生成文件的命名规则：[/b]
                |%I=ID编号
                |%M=MD5值
                |%P=标题拼音
                |%Y=4位数字表示的年份：1999 或 2008
                |%y=2位数字表示的年份：99 或 08
                |%m=数字表示的月份，01 到 12
                |%d=月份中的第几天，01 到 31
            '
        )
    )
);