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
        'alert' => array(
            'noselect'  => '请选择一个模型',
            'delete'    => '删除模型成功',
            'lock'      => '锁定模型成功',
            'unlock'    => '启用模型成功',
        )
    ),
    
    
    /* Model tip */
    'Model execute success' => '操作成功',
    'Model execute delete success'  => '执行删除成功',
    
    'Validate url'      => '匹配网址',    
    'Validate empty'    => '不能为空',
    'Validate limit'    => '限制长度',
    'Validate equal'    => '对比两个值相等',
    'Validate email'    => '电子邮箱',
    'Validate letter'   => '英文字母',
    'Validate number'   => '必须是数字',
    'Validate custom'   => '自定义验证',
    'Validate url error'      => '输入的网址格式不正确',
    'Validate empty error'    => '必填项，不能为空',
    'Validate limit error'    => '内容长度必须是1-100个字符',
    'Validate equal error'    => '两个值的内容不相等',
    'Validate email error'    => '邮箱格式不正确',
    'Validate letter error'   => '必须是英文字母',
    'Validate number error'   => '必须是数字',
    'Validate custom error'   => '这里是错误提示',
    'Validate custom regular' => '这里是正则表达式',
);