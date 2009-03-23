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
    var $id  = 0;
    // 已生成文档数
    var $make  = 0;
    // 总文档数
    var $total = -1;
    // 已使用时间
    var $useTime = 0;
    // 模型数组
    var $models  = array();
    // 当前时间
    var $_now;
    // 数据库连接句柄
    var $_db = null;
    // 队列数
    var $_alone = 0;
    // 开始时间
    var $_beginTime = 0;
    // 其他变量
    var $_others;
    /**
     * 初始化
     *
     * @param string $ids
     */
    function __construct($ids){
        // 设置数据库连接句柄
        $this->_db = get_conn();
        // 进程唯一标识
        if (strlen($ids)==32 && !validate($ids,6)) {
            $this->id = $ids;
        } else {
            $this->id = md5($ids);
            $this->_alone = $this->_db->count("SELECT * FROM `#@_system_create` WHERE `state`=1 LIMIT 0,1;");
        }
        // 设置开始时间
        $this->_beginTime = $GLOBALS['_beginTime'];
        // 设置默认的其他变量
        $this->_others = array(
            'phpurl' => PHP_FILE,
            'submit' => isset($_POST['submit']) ? strtolower($_POST['submit']) : null,
            'action' => ACTION,
        );
    }
    /**
     * 设置其他变量的值
     * 
     * 可以修改其他变量的属性值
     *
     * @param mixed  $p1
     * @param string $p2
     */
    function setAttr($p1,$p2=null){
        if (is_array($p1)) {
            $this->_others = array_merge($this->_others,$p1);
        } else {
            $this->_others = array_merge($this->_others,array($p1=>$p2));
        }
    }
    /**
     * 执行进程
     * 
     * 进程执行，保证队列唯一，执行页面生成
     *
     * @param string $callback      回调函数
     * @param bool   $isMakePage    是否生成页面
     * @param bool   $isReCreate    是否重新生成
     * @return mixed
     */
    function exec($callBack,$isMakePage=false,$isReCreate=false){
        $res = $this->_db->query("SELECT * FROM `#@_system_create` WHERE `id`=? LIMIT 0,1;",$this->id);
        if ($rs = $this->_db->fetch($res)) {
            // 取得属性变量
            $this->total = $rs['total'];
            $this->make  = $rs['make'];
            $this->useTime = $rs['usetime'];
            $this->models  = json_decode($rs['models'],true);
            $this->_others = json_decode($rs['others'],true);
            $isMakePage    = $rs['makepage'];
            $isReCreate    = $rs['recreate'];
            // 更新当前进程的状态
            if (empty($rs['state'])) {
                $this->_db->update('#@_system_create',array('state' => 1),"`id`=".DB::quote($this->id));
            }
        } else {
            $this->updateUseTime();
            // 创建进程
            return $this->_db->insert('#@_system_create',array(
                'id'     => $this->id,
                'total'  => $this->total,
                'make'   => $this->make,
                'models' => json_encode($this->models),
                'others' => json_encode($this->_others),
                'usetime' => floatval($this->useTime),
                'makepage'  => $isMakePage?1:0,
                'recreate'  => $isReCreate?1:0,
            ));
        }
        // 进程数大于0，退出
        if ((int)$this->_alone > 0) { return ; }
        // 执行生成回调函数
        return call_user_func(explode('::',$callBack),$this,$isMakePage,$isReCreate);
    }
    /**
     * 更新当前进程信息
     *
     * @param bool $p1  true:更新多想信息
     * @return bool
     */
    function update($p1=false){
        if ($p1) {
            // 更新多项信息
            $this->_db->update('#@_system_create',array(
                'make'    => $this->make,
                'models'  => json_encode($this->models),
                'usetime' => $this->useTime,
            ),"`id`=".DB::quote($this->id));
        } else {
            // 更新已用时间
            $this->updateuseTime();
            // 更新已生成数，已用时间
            $this->_db->update('#@_system_create',array(
                'make'    => $this->make,
                'usetime' => $this->useTime
            ),"`id`=".DB::quote($this->id));
        }
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
     * 取得记录
     *
     * @param string $sorts
     * @param string $model
     * @return resource
     */
    function fetchLogs($sorts,$model){
        $sortids = implode(',',$sorts);
        $table   = Content_Model::getDataTableName($model);
        $result  = $this->_db->query("SELECT * FROM `{$table}` WHERE `id` NOT IN(SELECT `dataid` FROM `#@_system_create_logs` WHERE `model`=[model] AND `createid`=[id]) AND `sortid` IN({$sortids});",array('id'=>$this->id,'model'=>$model));
        return $result;
    }
    /**
     * 插入记录
     *
     * @param array $p1
     */
    function insertLogs($p1){
        $this->_db->insert('#@_system_create_logs',(array) $p1);
    }
    /**
     * 判断记录是否生成完毕
     *
     * @return bool
     */
    function isOver(){
        return (int)$this->make==(int)$this->total?true:false;
    }
    /**
     * 关闭对象
     *
     */
    function close(){
        echo_json('PROCESS',array(
            'ACTION' => $this->_others['phpurl'],
            'DATAS'  => array(
                'ALONE' => (int) $this->_alone,  // 进程数
                'OVER'  => (int) $this->make,    // 已生成文档数
                'TOTAL' => (int) $this->total,   // 总文档数
                'USED'  => (float) number_format($this->useTime,2), // 已用时间
            ),
            'PARAM'  => array(
                'action' => $this->_others['action'],   // 动作
                'submit' => $this->_others['submit'],   // 提交动作
                'lists'  => $this->id,                  // ID
            )
        ));
    }
}