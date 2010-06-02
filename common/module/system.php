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

class ModuleSystem {
    /**
     * 格式化路径
     *
     * @param string $path  %ID,%PY,%MD5 和 strftime() 支持的参数
     * @param array $data
     *          array(
     *              'ID'  => 1,
     *              'PY'  => '标题',
     *              'MD5' => '文章ID或者其他任何唯一的值',
     *          )
     * @return string
     */
    function format_path($path,$data=null) {
        $py = $id = null; $md5 = '%MD5';
        if (is_array($data)) {
        	foreach ($data as $k=>$v) {
        		if ($k=='PY') {
        		    $py = pinyin($v);
        		} elseif ($k=='ID') {
        		    $id = $v;
        		} elseif ($k=='MD5') {
        		    $md5 = strtoupper(md5($path.micro_time(true).$v));
        		}
        	}
        	$path = str_replace(array('%PY','%ID','%MD5'),array($py,$id,$md5),$path);
        }
        if ($md5=='%MD5') {
            $path = str_replace('%MD5',strtoupper(md5($path.micro_time(true))),$path);
    	}
        return strftime($path);
    }
}