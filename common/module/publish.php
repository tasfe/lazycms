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
 * | Copyright (C) 2007-2008 LazyCMS.com All rights reserved.                  |
 * +---------------------------------------------------------------------------+
 * | LazyCMS is free software. This version use Apache License 2.0             |
 * | See LICENSE.txt for copyright notices and details.                        |
 * +---------------------------------------------------------------------------+
 */
defined('COM_PATH') or die('Restricted access!');

/**
 * 添加发布进程
 *
 * @param string $name
 * @param string $func  callback function args is publish table row.
 * @return int
 */
function publish_add($name,$func) {
    $db = get_conn();
    return $db->insert('#@_publish',array(
       'name'  => $name,
       'func'  => $func,
       'state' => 0,
    ));
}
/**
 * 更新发布进程
 *
 * @param int $pubid
 * @param array $data
 * @return int
 */
function publish_edit($pubid,$data) {
    $db = get_conn(); if (empty($data)) return false;
    return $db->update('#@_publish', (array)$data, array('pubid' => $pubid));
}
/**
 * 查询所有进程
 *
 * @return array
 */
function publish_gets() {
    $db = get_conn(); $result = array();
    $rs = $db->query("SELECT * FROM `#@_publish` ORDER BY `pubid` ASC;");
    while ($data = $db->fetch($rs)) {
        $result[$data['pubid']] = $data;
    }
    return $result;
}
/**
 * 检查是否有需要生成的进度
 *
 * @return int
 */
function publish_check_process() {
    $db = get_conn();
    return $db->result("SELECT COUNT(`pubid`) FROM `#@_publish` WHERE (`state`=0 OR `state`=1);");
}
/**
 * 执行发布
 *
 * @return bool|mixed
 */
function publish_exec() {
    $db = get_conn();
    // 已经有进程在执行，则退出
    if (1 < intval($db->result("SELECT COUNT(`pubid`) FROM `#@_publish` WHERE `state`=1;")))
        return false;
    // 取出未执行进程，开始执行
    $rs = $db->query("SELECT * FROM `#@_publish` WHERE (`state`=0 OR `state`=1) ORDER BY `pubid` ASC LIMIT 1;");
    if ($data = $db->fetch($rs)) {
        if (!function_exists($data['func'])) {
            $time = time();
            $sets = array(
                'begintime'  => $time,
                'elapsetime' => 0,
                'endtime'    => time(),
                'state'      => 2,
            );
            publish_edit($data['pubid'],$sets);
            return array_merge($data,$sets);
        }
        return call_user_func($data['func'], $data);
    }
    return false;
}
/**
 * 生成所有单页面
 *
 * @param  $data
 * @return bool
 */
function publish_pages($data){
    $db = get_conn();
    // 总数小于等于0时，统计总数并保存
    if (isset($data['total']) && 0 >= $data['total'] && $data['state']==0) {
        $total = $db->result("SELECT COUNT(`postid`) FROM `#@_post` WHERE `sortid`='-1';");
        // 没有任何文章需要生成，直接结束
        if (0 >= $total) {
            $time = time();
            $sets = array(
                'begintime'  => $time,
                'elapsetime' => 0,
                'endtime'    => $time,
                'state'      => 2,
            );
            publish_edit($data['pubid'],$sets);
            return array_merge($data,$sets);
        }
        // 更新需要生成的文档总数
        publish_edit($data['pubid'],array(
            'total'     => $total,
            'state'     => 1,
            'begintime' => time(),
        ));
    }
    $rs = $db->query("SELECT `postid` FROM `#@_post` WHERE `sortid`='-1' LIMIT 1 OFFSET %d;",$data['complete']);
    if ($row = $db->fetch($rs)) {
        if (post_create($row['postid'])) {
            // 更新进度
            $sets = array(
                'complete'   => ++$data['complete'],
                'elapsetime' => time() - $data['begintime'],
            );
            publish_edit($data['pubid'],$sets);
        }
    }
    // 生成结束
    else {
        $sets = array(
            'endtime' => time(),
            'state'   => 2,
        );
        publish_edit($data['pubid'],$sets);
    }
    return array_merge($data,$sets);
}
/**
 * 删除进程
 *
 * @param  $listids
 * @return bool
 */
function publish_delete($listids) {
    $db = get_conn(); if (empty($listids)) return false;
    $listids = is_array($listids) ? implode(',', $listids) : $listids;
    return $db->query("DELETE FROM `#@_publish` WHERE `pubid` IN({$listids})");
}