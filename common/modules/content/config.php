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
 * 模块配置文件
 */
return array(
    /* 权限列表 */
    'purview' => array(
        'label',
        'create',
        'onepage',
        'article',
        'fragment',
        'sort',
        'model',
    ),
    /* 菜单列表 */
    'menus'   => array(
        /*array('label' => array(
            'purview' => 'label',
            'href'    => '../content/label.php',
        )),*/
        array('create' => array(
            'purview' => 'create',
            'href'    => '../content/create.php',
        )),
        '-',
        array('onepage' => array(
            'purview' => 'onepage',
            'href'    => '../content/onepage.php',
        )),
        array('article' => array(
            'purview' => 'article',
            'href'    => '../content/article.php',
        )),
        array('fragment' => array(
            'purview' => 'fragment',
            'href'    => '../content/fragment.php',
        )),
        '-',
        array('sort' => array(
            'purview' => 'sort',
            'href'    => '../content/sort.php',
        )),
        array('model' => array(
            'purview' => 'model',
            'href'    => '../content/model.php',
        )),
    )
);
