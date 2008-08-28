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
 */
defined('COM_PATH') or die('Restricted access!');
/**
 * Keywords 处理
 *
 * @copyright   Copyright (c) 2007-2008 lazycms.net All rights reserved.
 * @author      Lukin <mylukin@gmail.com>
 * @date        2008-8-3
 */
// Keywords *** *** www.LazyCMS.net *** ***
class Keywords {
    private $db,$dict,$module,$joinTable;
    private $dicts = array();
    // __construct *** *** www.LazyCMS.net *** ***
    function __construct($module=null){
        $this->db   = get_conn();
        $this->dict = COM_PATH.'/data/dict/LazyCMS_Private.dict';
        $this->module    = empty($module)?MODULE:$module;
        $this->joinTable = "#@_keywords_{$this->module}";
        $this->autoCreateTable();
    }
    // get *** *** www.LazyCMS.net *** ***
    public function get($id){
        $R = array();
        $result = $this->db->query("SELECT `keyword` FROM `{$this->joinTable}` AS `kj` LEFT JOIN `#@_keywords` AS `k` ON `kj`.`keyid`=`k`.`keyid` WHERE `kj`.`itemid`=? ORDER BY `kj`.`keyid` ASC;",$id);
        while ($keywords = $this->db->fetch($result,0)) {
            $R[] = $keywords[0];
        }
        return join(',',$R);
    }
    // loadDicts *** *** www.LazyCMS.net *** ***
    private function loadDicts(){
        if (!is_file($this->dict)) { return ;}
        $this->dicts = array();
        $fp = fopen($this->dict,'r');
        while($ws = trim(fgets($fp))){
            if (empty($ws)){ continue; }
            $this->dicts[strlen($ws)][$ws] = 1;
        }
        fclose($fp);
    }
    // isKeyword *** *** www.LazyCMS.net *** ***
    private function isKeyword($string){
        $len = strlen(trim($string));
        return isset($this->dicts[$len][$string]);
    }
    // save *** *** www.LazyCMS.net *** ***
    public function save($id,$keywords,$related=false){
        if (strlen($keywords)==0) { return ; }
        // 按优先级处理关键词
        $keywords = str_replace(array('，','　'),array(',',' '),$keywords);
        $splitWords = explode(',',$keywords);
        if (count($splitWords)==1) {
            $splitWords = explode(' ',$keywords);
        }
        // 移除重复的关键词
        $splitWords = array_unique($splitWords);
        // 去除关键词两边的空格
        array_walk($splitWords,create_function('&$p1,$p2','$p1=trim($p1);'));
        // 加载私有词库
        $this->loadDicts();
        // 删除关键词关联
        $this->rejoin($id,array_diff(explode(',',$this->get($id)),$splitWords));
        // 查询数据库中存在的关键词
        $result = $this->db->query("SELECT * FROM `#@_keywords` WHERE `keyword` IN(".DB::quote($splitWords).");");
        // 从数组移除
        while ($data = $this->db->fetch($result)) {
            // 数据库中已经存在，插入关联记录
            $this->join($id,$data['keyid']);
            // 更新关键词的记录数
            $this->keysum($data['keyid']);
            // 从数组移除
            $key = array_search($data['keyword'],$splitWords);
            unset($splitWords[$key]);
        }
        // 循环插入新关键词
        foreach ($splitWords as $v) {
            // 插入关键词
            $this->db->insert('#@_keywords',array('keyword' => $v));
            // 取得刚插入关键词的id
            $keyid = $this->db->lastId();
            // 插入关联记录
            $this->join($id,$keyid);
            // 更新关键词的记录数
            $this->keysum($keyid);
            // 将此关键词写入文本词库
            if (!$this->isKeyword($v)) {
                $this->dicts[strlen($v)][$v] = 1;
                save_file($this->dict,$v.chr(10),false);
            }
            // 获取相关的长尾关键词
            if (!$related) { continue; }
            if ($rKeys = $this->getRelated($v)) {
                $length = (($len = count($rKeys))>3) ? 3 : $len;
                for ($i=0;$i<$length;$i++) {
                    $rKey = $rKeys[$i];
                    if (!$this->isKeyword($rKey)) {
                        $this->dicts[strlen($rKey)][$rKey] = 1;
                        save_file($this->dict,$rKey.chr(10),false);
                    }
                }
            }
        }
        return true;
    }
    // rejoin *** *** www.LazyCMS.net *** ***
    private function rejoin($id,$keywords){
        if (empty($keywords) || !is_array($keywords)) { return ; }
        $this->db->exec("DELETE FROM `{$this->joinTable}` WHERE `itemid`=? AND `keyid` IN(SELECT `keyid` FROM `#@_keywords` WHERE `keyword` IN(".DB::quote($keywords)."));",$id);
        $this->keysum($keywords);
        return true;
    }
    // join *** *** www.LazyCMS.net *** ***
    private function join($id,$keyid){
        // 如果关联不存在，则插入
        $N = $this->db->count("SELECT * FROM `{$this->joinTable}` WHERE `itemid`=".DB::quote($id)." AND `keyid`=".$this->db->quote($keyid).";");
        return ((int)$N>0) ? true : $this->db->insert($this->joinTable,array(
            'itemid' => $id,
            'keyid'  => $keyid,
        ));
    }
    // keysum *** *** www.LazyCMS.net *** ***
    private function keysum($keywords){
        if (empty($keywords)) { return ; }
        if (is_array($keywords)) {
            $result = $this->db->query("SELECT `keyid` FROM `#@_keywords` WHERE `keyword` IN(".DB::quote($keywords).");");
            while ($data = $this->db->fetch($result,0)) {
                $this->keysum($data[0]);
            }
            return true;
        } else {
            $keyid = $keywords;
            return $this->db->update('#@_keywords',array(
                'keysum' => $this->db->count("SELECT * FROM `{$this->joinTable}` WHERE `keyid`=".DB::quote($keyid).";")
            ),DB::quoteInto('`keyid` = ?',$keyid));    
        }
    }
    // autoCreateTable *** *** www.LazyCMS.net *** ***
    private function autoCreateTable(){
        // 表不存在，创建表
        if (!$this->db->isTable($this->joinTable)) {
            // 创建表
            $this->db->exec("
            CREATE TABLE IF NOT EXISTS `{$this->joinTable}` (
                `kjid` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `itemid` INT(11) NOT NULL,
                `keyid` INT(11) NOT NULL,
                KEY `itemid` (`itemid`),
                KEY `keyid` (`keyid`)
            ) ENGINE=MyISAM DEFAULT CHARSET=#~lang~#;");
        }
        return true;
    }
    // getRelated *** *** www.LazyCMS.net *** ***
    function getRelated($keyword){
        // 获取相关的长尾关键词
        import('system.downloader');
        $d = new DownLoader('http://www.baidu.com/s?ie=utf-8&wd='.rawurlencode($keyword));
        $d->send();
        if ($d->status() == 200) {
            $R = array();
            $body = ansi2utf($d->body());
            $body = sect($body,'>相关搜索</td>','</table>','( href="(.+)")');
            if (preg_match_all('/<a>(.+)<\/a>/iU',$body,$Keywords)) {
                if (!isset($Keywords[1])) { return false; }
                foreach ($Keywords[1] as $key) {
                    $eKeyword = explode(' ',$key);
                    if (count($eKeyword)>1) {
                        foreach ($eKeyword as $eKey) {
                            $R[] = trim($eKey);
                        }
                    } else {
                        $R[] = trim($key);
                    }
                }
                $R = array_unique($R);
            }
            return vsort($R);
        } else {
            return false;
        }
    }
}