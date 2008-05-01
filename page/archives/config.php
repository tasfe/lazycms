<?php
/**
 * +---------------------------------------------------------------------------+
 * | LL                             LLLL   LL     L   LLLL                 LL  |
 * | LL                            LL   L  LLL   LL  LL   L               LLL  |
 * | LL      LLLL   LLLLL  LL  LL  LL      LLLL LLL  LL          LL  LL    LL  |
 * | LL         LL     LL  LL  LL  LL      L LLL LL  LLLLL       LL  LL    LL  |
 * | LL      LLLLL    LL    LLLL   LL      L  L  LL   LLLLL      LL  LL    LL  |
 * | LL     LL  LL   LL     LLLL   LL      L     LL      LL       LLLL     LL  |
 * | LL     LL  LL  LL       LL    LL   L  L     LL  L   LL       LLLL     LL  |
 * | LLLLLL  LLLLL  LLLLL    LL     LLLL   L     LL   LLLL         LL     LLLL |
 * |                        LL                                                 |
 * |                        LL                                                 |
 * +---------------------------------------------------------------------------+
 * | Copyright (c) 2007-2008 LazyCMS.net All rights reserved.                  |
 * +---------------------------------------------------------------------------+
 * | 许可协议，请查看源代码中附带的 LICENSE.txt 文件，                         |
 * | 或者访问 http://www.lazycms.net/ 获得详细信息。                           |
 * +---------------------------------------------------------------------------+
 * | Author: Lukin <mylukin@gmail.com>                                         |
 * +---------------------------------------------------------------------------+
 */defined('CORE_PATH') or die('Restricted access!');
/**
 * Archives module configuration files
 */
return array (
  /**
   * 添加文档->文档属性->显示
   *
   * @inputtype:radio
   * @fieldvalue:默认打勾:true|默认不打勾:false
   */
  'ARCHIVES_ADD_SHOW' => true,
  /**
   * 添加文档->文档属性->推荐
   *
   * @inputtype:radio
   * @fieldvalue:默认打勾:true|默认不打勾:false
   */
  'ARCHIVES_ADD_COMMEND' => false,
  /**
   * 添加文档->文档属性->置顶
   *
   * @inputtype:radio
   * @fieldvalue:默认打勾:true|默认不打勾:false
   */
  'ARCHIVES_ADD_TOP' => false,
  /**
   * 添加文档->文档属性->下载远程图片
   *
   * @inputtype:radio
   * @fieldvalue:默认打勾:true|默认不打勾:false
   */
  'ARCHIVES_ADD_SNAPIMG' => true,
  /**
   * 添加文档->文档属性->更新列表
   *
   * @inputtype:radio
   * @fieldvalue:默认打勾:true|默认不打勾:false
   */
  'ARCHIVES_ADD_UPSORT' => true,
  /**
   * 添加文档->文档属性->更新首页
   *
   * @inputtype:radio
   * @fieldvalue:默认打勾:true|默认不打勾:false
   */
  'ARCHIVES_ADD_UPHOME' => true,
  /**
   * 添加文档->文档属性->检查重复标题
   *
   * @inputtype:radio
   * @fieldvalue:默认打勾:true|默认不打勾:false
   */
  'ARCHIVES_ADD_CHECKTITLE' => true,
);
?>