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
 * | Copyright (C) 2007-2010 LazyCMS.com All rights reserved.                  |
 * +---------------------------------------------------------------------------+
 * | LazyCMS is free software. See LICENSE for copyright notices and details.  |
 * +---------------------------------------------------------------------------+
 */
defined('COM_PATH') or die('Restricted access!');

/**
 * 添加发布进程
 *
 * @param string $name
 * @param string $func  callback function args is publish table row.
 * @param array $args
 * @return int
 */
function publish_add($name,$func,$args=array()) {
    $db = get_conn();
    return $db->insert('#@_publish',array(
       'name'  => $name,
       'func'  => $func,
       'args'  => $args,
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
    // 取出未执行进程，开始执行
    $rs = $db->query("SELECT * FROM `#@_publish` WHERE (`state`=0 OR `state`=1) ORDER BY `pubid` ASC LIMIT 1;");
    if ($data = $db->fetch($rs)) {
        if (!function_exists($data['func'])) {
            $sets = array('state' => 2);
            publish_edit($data['pubid'],$sets);
            return array_merge($data,$sets);
        }
        $args = unserialize($data['args']);
        unset($data['args']); array_unshift($args,$data);
        return call_user_func_array($data['func'], $args);
    }
    return false;
}
/**
 * 生成所有页面
 *
 * @param  $data
 * @return bool
 */
function publish_posts($data,$mode='all'){
    $db = get_conn(); $where = '';
    $sql = 'SELECT `postid` FROM `#@_post`';
    // 生成所有页面
    if ($mode == 'pages') {
        $where = "WHERE `type`='page'";
        $sql  .= $where;
    }
    // 生成所有文章
    elseif($mode == 'posts') {
        $where = "WHERE `type`='post'";
        $sql  .= $where;
    }
    // 指定分类文章生成
    elseif (is_array($mode) && !empty($mode)) {
        $sql = sprintf("SELECT DISTINCT(`p`.`postid`) FROM `#@_post` AS `p` LEFT JOIN `#@_term_relation` AS `tr` ON `p`.`postid`=`tr`.`objectid` WHERE `tr`.`taxonomyid` IN(%s)",implode(',',$mode));
    }

    // 总数小于等于0时，统计总数并保存
    if (isset($data['total']) && 0 >= $data['total'] && $data['state']==0) {
        if (is_array($mode) && !empty($mode)) {
            $count_sql = sprintf("SELECT COUNT(DISTINCT(`p`.`postid`)) FROM `#@_post` AS `p` LEFT JOIN `#@_term_relation` AS `tr` ON `p`.`postid`=`tr`.`objectid` WHERE `tr`.`taxonomyid` IN(%s)",implode(',',$mode));
        } else {
            $count_sql = "SELECT COUNT(`postid`) FROM `#@_post` {$where};";
        }
        $total = $db->result($count_sql);
        // 没有任何文章需要生成，直接结束
        if (0 >= $total) {
            $sets = array('state' => 2);
            publish_edit($data['pubid'],$sets);
            return array_merge($data,$sets);
        }
        // 更新需要生成的文档总数
        publish_edit($data['pubid'],array(
            'total'     => $total,
            'state'     => 1,
        ));
    }
    $length = 0;
    $rs = $db->query("{$sql} LIMIT 100 OFFSET %d;",$data['complete']);
    while ($row = $db->fetch($rs)) {
        post_create($row['postid']);
        $length++;
    }
    // 生成结束
    if ($length === 0) {
        $sets = array(
            'elapsetime' => $data['elapsetime'] + micro_time(true) - BEGIN_TIME,
            'state'      => 2,
        );
        publish_edit($data['pubid'],$sets);
    } else {
        // 更新进度
        $sets = array(
            'complete'   => $data['complete'] + $length,
            'elapsetime' => $data['elapsetime'] + micro_time(true) - BEGIN_TIME,
        );
        publish_edit($data['pubid'],$sets);
    }
    return array_merge($data,$sets);
}
/**
 * 生成列表
 *
 * @param array $data
 * @param array $sortids
 * @param bool $make_post 是否生成文章
 * @param int $sortid
 * @return array
 */
function publish_lists($data,$sortids=null,$make_post=false,$sortid=0) {
    if (isset($data['total']) && 0 >= $data['total'] && $data['state']==0) {
        $db = get_conn(); $lists = array();
        $taxonomy_list = $sortids===null ? taxonomy_get_list('category') : $sortids;
        // 计算要生成的总页数
        foreach($taxonomy_list as $taxonomyid) {
            if ($taxonomy = taxonomy_get($taxonomyid)) {
                // 载入模版
                $html = tpl_loadfile(ABS_PATH.'/'.system_themes_path().'/'.$taxonomy['list']);
                // 标签块信息
                $block  = tpl_get_block($html,'post,list','list');
                if ($block) {
                    // 每页条数
                    $number = tpl_get_attr($block['tag'],'number');
                    // 文章总数
                    $posts = $db->result(sprintf("SELECT COUNT(`objectid`) FROM `#@_term_relation` WHERE `taxonomyid`=%d;", esc_sql($taxonomyid)));
                    // 总页数
                    $pages = ceil($posts/$number); $pages = ((int)$pages == 0) ? 1 : $pages;
                    // 要生成的文档总数
                    $lists[$taxonomyid] = $pages;
                }
            }
        }
        $total = array_sum($lists);
        // 没有任何文章需要生成，直接结束
        if (0 >= $total) {
            $sets = array('state' => 2);
            publish_edit($data['pubid'],$sets);
            return array_merge($data,$sets);
        }
        $keys = array_keys($lists);
        // 更新需要生成的文档总数
        $sets = array(
            'total'      => $total,
            'state'      => 1,
            'args'       => array(
                'lists'  => $lists,
                'mpost'  => $make_post,
                'listid' => isset($keys[0]) ? $keys[0] : 0,
            )
        );
        $sortids = $sets['args']['lists'];
        $sortid  = $sets['args']['listid'];
        publish_edit($data['pubid'],$sets);
        $data = array_merge($data,$sets);
    }
    // 正在生成的分类ID
    $generated = 0;
    // 计算应该生成第几页
    foreach((array)$sortids as $id=>$v) {
        if ($id == $sortid) break;
        $generated+= $v;
    }
    $page = $data['complete'] - $generated + 1;
    // 生成成功
    if (taxonomy_create($sortid,$page,$make_post)) {
        // 更新进度
        $sets = array(
            'complete'   => ++$data['complete'],
            'elapsetime' => $data['elapsetime'] + micro_time(true) - BEGIN_TIME,
        );
        publish_edit($data['pubid'],$sets);
    }
    // 当前分类生成结束
    else {
        // 切换到下一个分类
        $keys = array_keys($sortids);
        $key  = array_search($sortid, $keys) + 1;
        if (isset($keys[$key])) {
            $sets = array(
                'elapsetime' => $data['elapsetime'] + micro_time(true) - BEGIN_TIME,
                'args'       => array(
                    'lists'  => $sortids,
                    'mpost'  => $make_post,
                    'listid' => $keys[$key],
                )
            );
            publish_edit($data['pubid'],$sets);
        }
        // 全部生成结束
        else {
            $sets = array(
                'elapsetime' => $data['elapsetime'] + micro_time(true) - BEGIN_TIME,
                'state'      => 2,
            );
            publish_edit($data['pubid'],$sets);
        }

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
