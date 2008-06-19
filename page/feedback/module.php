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
 */
defined('CORE_PATH') or die('Restricted access!');
/**
 * Module 层
 *
 * @copyright   Copyright (c) 2007-2008 lazycms.net All rights reserved.
 * @author      Lukin <mylukin@gmail.com>
 */
class FeedBack{
    public static $addTable = '#@_feedback_custom';
    // getTitle *** *** www.LazyCMS.net *** ***
    static function getTitle($l1,$l2=30){
        $I1 = null;
        $contents = str_replace("\r\n","\n",$l1);
        $contents = explode("\n",$contents);
        foreach ($contents as $v) {
            if (strlen($I1)>0 && $I1!='&nbsp;') {
                break;
            } else {
                $I1 = cls(lefte(clearHTML($v),$l2));
            }
        }
        return $I1;
    }
    // showTypes *** *** www.LazyCMS.net *** ***
    static function showTypes($l1=null){
        $I1 = null; $module = getObject();
        $l2 = array(
            'input'    => 'varchar',   // 输入框
            'textarea' => 'text',      // 文本框
            'radio'    => 'varchar',   // 单选框
            'checkbox' => 'varchar',   // 复选框
            'select'   => 'varchar',   // 下拉菜单
            'basic'    => 'text',      // 简易编辑器
            'editor'   => 'mediumtext',// 内容编辑器
        );
        foreach ($l2 as $k => $v){
            $selected = ((string)$l1 == (string)$k) ? ' selected="selected"' : null;
            $I1 .= '<option value="'.$k.'" type="'.$v.'"'.$selected.'>'.$module->L('list/field/type/'.$k).'</option>';
        }
        return $I1;
    }
    // tags *** *** www.LazyCMS.net *** ***
    static function tags($tags,$inValue){
        $tmpList = null; $db = getConn();
        $HTMList = $tags; import("system.tags"); $tag = new Tags();
        $jsHTML  = $tag->getLabel($HTMList,0);
        $jsType  = $tag->getLabel($HTMList,'type');
        $jsNumber= floor($tag->getLabel($HTMList,'number'));
        $zebra   = $tag->getLabel($HTMList,'zebra');

        $select = "SELECT * FROM `#@_feedback` AS `fb` LEFT JOIN `".self::$addTable."` AS `fbc` ON `fb`.`fbid`=`fbc`.`fbid` ";
        switch ($jsType) {
            case 'tag0': // 未加星的留言
                $strSQL = $select." WHERE `fb`.`istag`=0 ORDER BY `fb`.`fbid` DESC";
                break;
            case 'tag1': // 加星的留言
                $strSQL = $select." WHERE `fb`.`istag`=1 ORDER BY `fb`.`fbid` DESC";
                break;
            default:
                $strSQL = $select." ORDER BY `fb`.`fbid` DESC";
                break;
        }
        $strSQL.= " LIMIT 0,{$jsNumber};";
        $rs = $db->query($strSQL);
        $i  = 1;
        while ($data = $db->fetch($rs)) {
            $tag->clear();
            $tag->value('id',$data['fbid']);
            $tag->value('istag',$data['istag']);
            $tag->value('title',encode(htmlencode($data['fbtitle'])));
            $tag->value('content',encode($data['fbcontent']));
            $tag->value('ip',encode($data['fbip']));
            $tag->value('date',$data['fbdate']);
            $tag->value('zebra',($i % ($zebra+1)) ? 0 : 1);
            $tag->value('++',$i);
            $res = $db->query("SELECT `fieldename` FROM `#@_feedback_fields`;");
            while ($field = $db->fetch($res)) {
                $tag->value($field['fieldename'],encode(htmlencode($data[$field['fieldename']])));
            }
            $tmpList.= $tag->createhtm($jsHTML,$tag->getValue());
            $i++;
        }
        return $tmpList;
    }
    // uninstSQL *** *** www.LazyCMS.net *** ***
    static function uninstSQL(){
        $addTable = self::$addTable;
        return <<<SQL
            DROP TABLE IF EXISTS `#@_feedback`;
            DROP TABLE IF EXISTS `#@_feedback_fields`;
            DROP TABLE IF EXISTS `{$addTable}`;
SQL;
    }
    // instsql *** *** www.LazyCMS.net *** ***
    static function instSQL(){
        $addTable = self::$addTable;
        return <<<SQL
            // 留言反馈
            CREATE TABLE IF NOT EXISTS `#@_feedback` (
              `fbid` int(11) NOT NULL auto_increment,
              `istag` int(11) default '0',              # 是否加星
              `fbtitle` varchar(100),                   # 标题
              `fbcontent` text,                         # 内容
              `fbip` varchar(20),                       # IP地址
              `fbdate` int(11) default '0',             # 添加日期
              PRIMARY KEY  (`fbid`)
            ) ENGINE=MyISAM DEFAULT CHARSET=#~lang~#;
            // 留言自定义字段
            CREATE TABLE IF NOT EXISTS `#@_feedback_fields` (
              `fieldid` int(11) NOT NULL auto_increment,
              `fieldorder` int(11),                         # 字段排序
              `fieldname` varchar(50),                      # 表单文字
              `fieldename` varchar(50),                     # 字段名
              `fieldtype` varchar(20),                      # 类型
              `fieldlength` varchar(255),                   # 长度
              `fieldefault` varchar(255),                   # 默认值
              `inputtype` varchar(20),                      # 输入框类型
              `fieldvalue` varchar(255),                    # radio,checkbox,select 值
              PRIMARY KEY  (`fieldid`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=#~lang~#;
            // 附属表
            CREATE TABLE IF NOT EXISTS `{$addTable}` (
                `fbid` int(11) NOT NULL,
                PRIMARY KEY (`fbid`)
            ) ENGINE=MyISAM DEFAULT CHARSET=#~lang~#;
SQL;
    }
}