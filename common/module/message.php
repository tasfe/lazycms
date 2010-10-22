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
 * 发送消息
 *
 * @param string $content
 * @param string $authcode  不填则发给所有人
 * @return int
 */
function message_add($content,$authcode='Unknown') {
    global $_USER; $db = get_conn();
    $sender = isset($_USER['name']) ? $_USER['name'] : __('Unknown');
    return $db->insert('#@_message',array(
        'sender'   => $sender,
        'authcode' => $authcode,
        'content'  => esc_html($content),
        'datetime' => time(),
    ));
}
/**
 * 检查是否有新消息
 *
 * @return int
 */
function message_check_new() {
    global $_USER; $db = get_conn();
    if (!isset($_USER)) return 0;
    $ckey  = sprintf('message.%s',$_USER['authcode']);
    $reads = fcache_get($ckey);
    $insql = empty($reads) ? '' : ' AND `msgid` NOT IN('.implode(',', (array)$reads).')'; header('X-SQL: SQL-'.$insql);
    return $db->result(sprintf("SELECT COUNT(`msgid`) FROM `#@_message` WHERE (`authcode`='Unknown' OR `authcode`='%s'){$insql};",esc_sql($_USER['authcode'])));
}
/**
 * 读取消息
 *
 * @return array
 */
function message_get() {
    global $_USER; $result = array(); $db = get_conn();
    $ckey  = sprintf('message.%s',$_USER['authcode']);
    $reads = fcache_get($ckey);
    $insql = empty($reads) ? '' : ' AND `msgid` NOT IN('.implode(',', (array)$reads).')';
    $rs    = $db->query("SELECT * FROM `#@_message` WHERE (`authcode`='Unknown' OR `authcode`='%s'){$insql} ORDER BY `msgid` ASC;",$_USER['authcode']);
    while ($data = $db->fetch($rs)) {
        $data['datetime'] = date('H:i:s',$data['datetime']);
        $result[] = $data; $reads[] = $data['msgid'];
    }
    fcache_set($ckey,$reads,40);
    return $result;
}
/**
 * 删除信息
 *
 * 删除1分钟之前的消息
 *
 * @return bool
 */
function message_delete() {
    $db = get_conn(); $timeout = time() - 30;
    return $db->query("DELETE FROM `#@_message` WHERE `datetime`<%d;",$timeout);
}

/**
 * 查询消息
 *
 * @return array
 */
function message_poll() {
    $result = array();
    // 进入长轮询
    $start_time = micro_time(true);
    while (true) {
        $now_time = micro_time(true);
        // 超时退出
        if ($now_time-$start_time >= 20) {
            break;
        }
        // 检查新消息
        if (0 < message_check_new()) {
            $result = message_get();
            // 删除1分钟之前的消息
            message_delete();
            break;
        }
        // 休眠 0.1秒
        usleep(0.1 * 1000000);
    }
    return $result;
}