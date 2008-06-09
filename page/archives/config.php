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
  /**
   * 搜索页面模板文件
   *
   * @inputtype:upfile
   */
  'ARCHIVES_TEMPLATE' => 'template/default.html',
  /**
   * Rss生成地址
   *
   * @inputtype:input
   * @fieldclass:in2
   */
  'ARCHIVES_RSS_FILE' => 'rss.xml',
  /**
   * Rss生成记录数
   *
   * @inputtype:input
   * @fieldclass:in0
   */
  'ARCHIVES_RSS_NUMBER' => 20,
  /**
   * 是否将文章直接在根目录生成
   *
   * @inputtype:radio
   * @fieldvalue:生成在根目录:true|生成到子目录:false
   */
  'ARCHIVES_CREATE_ROOTFILE' => false,
  /**
   * 是否进行Blog Ping
   *
   * @inputtype:radio
   * @fieldvalue:是:true|否:false
   */
  'ARCHIVES_BLOG_PING' => false,
  /**
   * 生成文件的默认组合方式
   *
   * @inputtype:radio
   * @fieldvalue:ID+后缀:ID|年/月/日/ID:DateID|拼音+后缀:PinYin|MD5+后缀:MD5|启用自定义格式:CUSTOM
   */
  'ARCHIVES_CREATE_FILEMODE' => 'PinYin',
  /**
   * <br/>&nbsp; 自定义文件生成格式：<br/>&nbsp; &nbsp;{ID}:文章ID<br/>&nbsp; &nbsp;{Y}:当前年份<br/>&nbsp; &nbsp;{M}:当前月份<br/>&nbsp; &nbsp;{D}:当前天数<br/>&nbsp; &nbsp;{PinYin}:标题拼音<br/>&nbsp; &nbsp;{MD5}:自动生成MD5串<br/>
   *
   * @inputtype:input
   */
  'ARCHIVES_CREATE_FILE_CUSTOM' => '{Y}-{M}-{D}/{PinYin}.html',
);
?>