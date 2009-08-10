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
 * Process
 */
class Process{
    // 进程ID
    var $id     = 0;
    // 已生成数
    var $create = 0;
    // 总文档数
    var $total  = 0;
    // 已使用时间
    var $useTime = 0;
    // insert数据
    var $insert = true;

    // 数据变量
    var $_data = array();
    // 数据库连接句柄
    var $_db  = null;
    // 开始时间
    var $_beginTime = 0;
    // 队列数
    var $_alone = 0;

    /**
     * 初始化
     *
     * 兼容PHP5模式
     *
     * @param string $ids
     */
    function __construct($ids){
        // 设置数据库连接句柄
        $this->_db = get_conn();
        // 设置开始时间
        $this->_beginTime = $GLOBALS['_beginTime'];
        // 进程唯一标识
        if (strlen($ids)==32 && !validate($ids,6)) {
            $this->id  = $ids;
            $this->insert = false;
        } else {
            // 按钮事件
            $submit = isset($_POST['submit']) ? strtolower($_POST['submit']) : null;
            // 设置默认的其他变量
            $this->_data['others'] = array(
                'phpurl' => PHP_FILE,
                'submit' => $submit,
                'action' => ACTION,
            );
            $this->id  = md5($ids.PHP_FILE.ACTION.$submit);
            $this->_data['sortids'] = explode(',',$ids);
            $this->_alone = $this->_db->result("SELECT COUNT(*) FROM `#@_system_create` WHERE `state`=1;");
        }
    }
    /**
     * 初始化
     */
    function Process($ids){
        $this->__construct($ids);
    }
    /**
     * 类数据存取方法
     *
     * 可以存取类的内部数据
     *
     * @param mixed  $p1
     * @param string $p2
     */
    function data($p1=null,$p2=null){
        // 取得所有data数据
        if (!$p1 && !$p2) {
            return $this->_data;
        }
        // 取值
        if (!is_array($p1) && empty($p2)) {
            return isset($this->_data[$p1]) ? $this->_data[$p1] : null;
        }
        // 赋值
        if (is_array($p1)) {
            $this->_data = array_merge($this->_data,$p1);
        } else {
            $this->_data = array_merge($this->_data,array($p1 => $p2));
        }
    }
    /**
     * 执行进程
     *
     * 进程执行，保证队列唯一，执行页面生成
     *
     * @param string $args[0]      回调函数
     * @return mixed
     */
    function exec(){
        $args = func_get_args();
        $func = explode('::',$args[0]); $args[0] = &$this;
        $res  = $this->_db->query("SELECT * FROM `#@_system_create` WHERE `id`=? LIMIT 0,1;",$this->id);
        if ($rs = $this->_db->fetch($res)) {
            // 取得属性变量
            $this->total   = $rs['total'];
            $this->create  = $rs['create'];
            $this->useTime = $rs['usetime'];
            $this->data(unserialize($rs['data']));
            // 更新当前进程的状态
            if (empty($rs['state'])) {
                $this->_db->update('#@_system_create',array('state' => 1),"`id`=".DB::quote($this->id));
            }
        } else {
            $this->updateUseTime();
            // 回调函数必须返回true
            if (call_user_func_array($func,$args)) {
                if ($this->total == 0 || !$this->insert) { return ; }
                // 插入数据
                return $this->_db->insert('#@_system_create',array(
                    'id'      => $this->id,
                    'total'   => $this->total,
                    'create'  => $this->create,
                    'usetime' => floatval($this->useTime),
                    'data'    => serialize($this->_data),
                ));
            }
        }
        // 进程数大于0，退出
        if ((int)$this->_alone > 0) { return ; }
        // 执行生成回调函数
        return call_user_func_array($func,$args);
    }
    /**
     * 更新当前进程信息
     *
     * @return bool
     */
    function update($p1=null){
        // 更新已用时间
        $this->updateuseTime();
        $update = array(
            'create'  => $this->count(),
            'usetime' => $this->useTime
        );
        if ($p1) {
            $update = array_merge($update,$p1);
        }
        $this->create = $update['create'];
        // 更新已生成数，已用时间
        $this->_db->update('#@_system_create',$update,"`id`=".DB::quote($this->id));
        return true;
    }
    /**
     * 删除当前进程信息
     *
     */
    function delete(){
        $this->_db->delete('#@_system_create',"`id`=".DB::quote($this->id));
        $this->_db->delete('#@_system_create_logs',"`createid`=".DB::quote($this->id));
    }
    // 统计数量
    function count(){
        return $this->_db->result("SELECT COUNT(*) FROM `#@_system_create_logs` WHERE `createid`='".$this->id."';");
    }
    /**
     * 更新已用时间
     *
     */
    function updateUseTime(){
        // 当前时间
        $this->_now = microtime_float();
        // 累加已用时间，单位：微秒
        $this->useTime = $this->useTime + ($this->_now - $this->_beginTime);
        // 重置开始时间
        $this->_beginTime = $this->_now;
    }
    /**
     * 查询记录
     *
     * @param string $sortid
     * @param string $model
     * @return resource
     */
    function query($sortid,$model){
        $table   = Content_Model::getDataTableName($model);
        $result  = $this->_db->query("SELECT * FROM `{$table}` AS `a` WHERE NOT EXISTS(SELECT `dataid` FROM `#@_system_create_logs` WHERE `dataid`=`a`.`id` AND `createid`=[id] AND `model`=[model]) AND `sortid`={$sortid};",array('id'=>$this->id,'model'=>$model));
        return $result;
    }
    /**
     * 取得记录
     *
     * @param resource $p1
     * @return array
     */
    function fetch($p1){
        return $this->_db->fetch($p1);
    }
    /**
     * 插入记录
     *
     * @param array $p1
     */
    function insertLogs($p1){
        @$this->_db->insert('#@_system_create_logs',(array) $p1);
    }
    /**
     * 判断记录是否生成完毕
     *
     * @return bool
     */
    function isOver(){
        return (int)$this->create>=(int)$this->total?true:false;
    }
    /**
     * 关闭对象
     *
     */
    function close(){
        $others = $this->data('others');
        echo_json('PROCESS',array(
            'ACTION' => $others['phpurl'],
            'DATAS'  => array(
                'ALONE' => (int) $this->_alone,  // 进程数
                'OVER'  => (int) $this->create,  // 已生成文档数
                'TOTAL' => (int) $this->total,   // 总文档数
                'USED'  => (float) number_format($this->useTime,2,'.',''), // 已用时间
            ),
            'PARAM'  => array(
                'action' => $others['action'],   // 动作
                'submit' => $others['submit'],   // 提交动作
                'lists'  => $this->id,           // ID
            )
        ));
    }
}
