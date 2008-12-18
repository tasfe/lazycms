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

    /* 模型管理 */
    'model' => array(
        'title' => '模型管理',
        'import'    => array(
            'title' => '模型导入',
            'file'  => '模型文件',
            'code'  => '模型代码',
            'submit'=> '导入',
        ),
        'addlist'   => '添加列表模型',
        'addpage'   => '添加单页模型',
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
            'ename' => '字段名',
            'input' => '输入类型',
            'width' => '宽',
            'value' => '序列值',
            'length'    => '最大长度',
            'default'   => '默认值',
            
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
            )
        ),
        'check' => array(
            'code'  => '模型代码不能为空',
            'exist' => '模型标识(modelename)重复，请修改模型标识',
        ),
        'alert' => array(
            'noselect'  => '请选择一个模型',
            'delete'    => '删除模型成功',
            'lock'      => '锁定模型成功',
            'unlock'    => '启用模型成功',
            'import'    => '导入模型成功',
        )
    ),
    
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
    )
);