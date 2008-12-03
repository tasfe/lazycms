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
 * Keywords 处理
 */
class Keywords{
    var $_db,$_dict,$_joinTable;
    var $_dicts = array();
    /**
     * 兼容PHP5模式
     *
     */
    function __construct($module=null){
        $this->_db   = get_conn();
        $this->_dict = COM_PATH.'/dicts/LazyCMS_Private.dict';
        $this->_joinTable = Content_Model::getJoinTableName(empty($module)?MODULE:$module);
    }
    /**
     * 初始化
     *
     * @return Keywords
     */
    function Keywords($module=null){
        $this->__construct($module);
    }
    /**
     * 根据id取得关键词列表
     *
     * @param int $id
     * @return string
     */
    function get($id){
        $R = array();
        $result = $this->_db->query("SELECT `k`.`keyword` FROM `{$this->_joinTable}` AS `j` LEFT JOIN `#@_keywords` AS `k` ON `j`.`sid`=`k`.`keyid` WHERE `j`.`tid`=? AND `j`.`type`=0 ORDER BY `j`.`sid` ASC;",$id);
        while ($rs = $this->_db->fetch($result,0)) {
            $R[] = $rs[0];
        }
        return join(',',$R);
    }
    /**
     * 加载词典
     *
     */
    function _loadDicts(){
        if (!is_file($this->_dict)) { return ;}
        $this->_dicts = array();
        $fp = fopen($this->_dict,'r');
        while($ws = trim(fgets($fp))){
            if (empty($ws)){ continue; }
            $this->_dicts[strlen($ws)][$ws] = 1;
        }
        fclose($fp);
    }
    /**
     * 判断是否已加入关键词库
     *
     * @param string $string
     * @return bool
     */
    function _isKeyword($string){
        $len = strlen(trim($string));
        return isset($this->_dicts[$len][$string]);
    }
    /**
     * 保存关键词
     *
     * @param int    $id
     * @param string $keywords
     * @param bool   $related
     * @return bool
     */
    function save($id,$keywords,$related=false){
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
        $this->_loadDicts();
        // 删除关键词关联
        $this->_rejoin($id,array_diff(explode(',',$this->get($id)),$splitWords));
        // 查询数据库中存在的关键词
        $result = $this->_db->query("SELECT * FROM `#@_keywords` WHERE `keyword` IN(".DB::quote($splitWords).");");
        // 从数组移除
        while ($data = $this->_db->fetch($result)) {
            // 数据库中已经存在，插入关联记录
            $this->_join($id,$data['keyid']);
            // 更新关键词的记录数
            $this->_keysum($data['keyid']);
            // 从数组移除
            $key = array_search($data['keyword'],$splitWords);
            unset($splitWords[$key]);
        }
        // 循环插入新关键词
        foreach ($splitWords as $v) {
            // 插入关键词
            $this->_db->insert('#@_keywords',array('keyword' => $v));
            // 取得刚插入关键词的id
            $keyid = $this->_db->lastId();
            // 插入关联记录
            $this->_join($id,$keyid);
            // 更新关键词的记录数
            $this->_keysum($keyid);
            // 将此关键词写入文本词库
            if (!$this->_isKeyword($v)) {
                $this->_dicts[strlen($v)][$v] = 1;
                save_file($this->_dict,$v.chr(10),false);
            }
            // 获取相关的长尾关键词
            if (!$related) { continue; }
            if ($rKeys = $this->_getRelated($v)) {
                $length = (($len = count($rKeys))>3) ? 3 : $len;
                for ($i=0;$i<$length;$i++) {
                    $rKey = $rKeys[$i];
                    if (!$this->_isKeyword($rKey)) {
                        $this->_dicts[strlen($rKey)][$rKey] = 1;
                        save_file($this->_dict,$rKey.chr(10),false);
                    }
                }
            }
        }
        return true;
    }
    /**
     * 删除关键词关联
     *
     * @param int    $id
     * @param string $keywords
     * @return bool
     */
    function _rejoin($id,$keywords){
        if (empty($keywords) || !is_array($keywords)) { return ; }
        $this->_db->exec("DELETE FROM `{$this->_joinTable}` WHERE `tid`=? AND `type`=0 AND `sid` IN(SELECT `keyid` FROM `#@_keywords` WHERE `keyword` IN(".DB::quote($keywords)."));",$id);
        $this->_keysum($keywords);
        return true;
    }
    /**
     * 创建关键词关联
     *
     * @param int    $id
     * @param int    $keyid
     * @return mixed
     */
    function _join($id,$keyid){
        // 如果关联不存在，则插入
        $N = $this->_db->count("SELECT * FROM `{$this->_joinTable}` WHERE `tid`=".DB::quote($id)." AND `type`=0 AND `sid`=".DB::quote($keyid).";");
        return ((int)$N>0) ? true : $this->_db->insert($this->_joinTable,array(
            'tid'  => $id,
            'sid'  => $keyid,
            'type' => 0,
        ));
    }
    /**
     * 统计关键词的使用次数
     *
     * @param string $keywords
     * @return bool
     */
    function _keysum($keywords){
        if (empty($keywords)) { return ; }
        if (is_array($keywords)) {
            $result = $this->_db->query("SELECT `keyid` FROM `#@_keywords` WHERE `keyword` IN(".DB::quote($keywords).");");
            while ($data = $this->_db->fetch($result,0)) {
                $this->_keysum($data[0]);
            }
            return true;
        } else {
            $keyid = $keywords;
            return $this->_db->update('#@_keywords',array(
                'keysum' => $this->_db->count("SELECT * FROM `{$this->_joinTable}` WHERE `sid`=".DB::quote($keyid)." AND `type`=0;")
            ),DB::quoteInto('`keyid` = ?',$keyid));    
        }
    }
    /**
     * 在百度上面活的相关的长尾关键词
     *
     * @param string $keyword
     * @return bool
     */
    function _getRelated($keyword){
        // 获取相关的长尾关键词
        import('system.httplib');
        $d = new Httplib('http://www.baidu.com/s?ie=utf-8&wd='.rawurlencode($keyword));
        $d->send();
        if ($d->status() == 200) {
            $R = array();
            $body = ansi2utf($d->response());
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