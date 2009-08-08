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
 * 文章管理公共模块
 */
class Content_Article{
    /**
     * 格式化路径
     *
     * @param  int      $p1     MaxID
     * @param  string   $p2     用户输入的字符串（未格式化）
     * @param  string   $p3     文章标题，需要用这个来格式化成为标题路径
     * @param  integer  $p4     时间戳，用来格式化日期变量
     * @return string
     */
    function formatPath($p1,$p2,$p3,$p4=null){
        $p4 = empty($p4) ? now() : $p4; $p5 = null;
        if (strpos($p2,'%P')!==false) {
            $p5 = pinyin($p3);
            $p5 = empty($p5)?$p1:$p5;
        }
        $R = str_replace(array('%I','%M','%P'),array($p1,md5($p1.salt(10)),$p5),$p2);
        $R = strftime($R,$p4);
        return $R;
    }

    /**
     * 统计分类和指定模型下的文档数量
     *
     * @param  integer  $p1     分类ID
     * @param  string   $p2     模型标识，用英文逗号分隔
     * @return integer
     */
    function count($p1,$p2){
        $db = get_conn(); $R = 0;
        if (empty($p2)) { return $R; }
        $p3 = explode(',',$p2);
        foreach ($p3 as $v) {
            $table = Content_Model::getDataTableName($v);
            $R = $R + $db->result("SELECT COUNT(*) FROM `{$table}` WHERE `sortid`=".DB::quote($p1).";");
        }
        return $R;
    }
    /**
     * 取得模板文件
     *
     * 根据分类id取得模板文件
     *
     * @param int $p1
     * @param int $p2   取得值类型：all,sort,page
     * @return array
     */
    function getTemplateBySortId($p1,$p2='all'){
        $db  = get_conn(); $R = array();
        $res = $db->query("SELECT `sortemplate`,`pagetemplate` FROM `#@_content_sort` WHERE `sortid`=?;",$p1);
        if ($rs = $db->fetch($res,0)) {
            $R['sort'] = $rs[0];
            $R['page'] = $rs[1];
        }
        // 使用模型设置的模板
        if (empty($R['sort']) && instr('all,sort',$p2)) {
            $sort = $db->result("SELECT `b`.`sortemplate` FROM `#@_content_sort_join` AS `a` LEFT JOIN `#@_content_model` AS `b` ON `a`.`modelid`=`b`.`modelid` WHERE `a`.`sortid`=".DB::quote($p1)." LIMIT 0,1;");
            $R['sort'] = empty($sort)?c('TEMPLATE_DEFAULT'):$sort;
        }
        if (empty($R['page']) && instr('all,page',$p2)) {
            $page = $db->result("SELECT `b`.`pagetemplate` FROM `#@_content_sort_join` AS `a` LEFT JOIN `#@_content_model` AS `b` ON `a`.`modelid`=`b`.`modelid` WHERE `a`.`sortid`=".DB::quote($p1)." LIMIT 0,1;");
            $R['page'] = empty($page)?c('TEMPLATE_DEFAULT'):$page;
        }
        return $p2=='all' ? $R : (string)$R[$p2];
    }
    /**
     * 生成文章
     *
     * @param string $modelename
     * @param string $ids
     * @return bool
     */
    function createPage($modelename,$ids){
        import('system.keywords');  $key = new Keywords($modelename);
        import('system.parsetags'); $tag = new ParseTags();
        $template = c('TEMPLATE');  $db  = get_conn();
        $table  = Content_Model::getDataTableName($modelename);
        $model  = Content_Model::getModelByEname($modelename);
        $fields = json_decode($model['modelfields']);
        $result = $db->query("SELECT * FROM `{$table}` WHERE `id` IN({$ids});");
        while ($rs = $db->fetch($result)) {
            // 优先使用文章设置的模板
            if ($rs['template']) {
            	$tplfile = $rs['template'];
            } else {
                $tplfile = Content_Article::getTemplateBySortId($rs['sortid'],'page');
            }
            // 加载模板
            $tag->loadHTML(LAZY_PATH.'/'.$template.'/'.$tplfile);
            // 清除标签值
            $tag->clear();
            // 定义内部变量
            $tag->V(array(
                'id'        => $rs['id'],
                'sortid'    => $rs['sortid'],
                'date'      => $rs['date'],
                'hits'      => $rs['hits'],
                'digg'      => $rs['digg'],
                'path'      => SITE_BASE.$rs['path'],
                'keywords'    => $key->get($rs['id']),
                'description' => $rs['description'],
            ));
            // 添加自定义字段变量
            foreach ($fields as $field) {
                $tag->V($field->ename,$rs[$field->ename]);
            }
            // 解析模板
            $outHTML = $tag->parse();
            $outFile = LAZY_PATH.'/'.$rs['path'];
            mkdirs(dirname($outFile)); save_file($outFile,$outHTML);
        }
        return true;
    }
    /**
     * 生成文档
     *
     * @param object $pro           Process对象
     * @return bool
     */
    function create(&$pro){
        $execTime = 5;
        // 第一次需要insert数据
        if ($pro->insert) {
            $models = array();
            foreach ($pro->data('sortids') as $sortid) {
                // 计算数据
                if ($ms  = Content_Model::getModelsBySortId($sortid,array('modelename'))) {
                    $len = Content_Article::count($sortid,implode(',',$ms));
                    if ((int)$len > 0) {
                        foreach ($ms as $m) {
                            $models[$m][] = $sortid;
                        }
                        $pro->total = $pro->total + $len;
                    }
                }
            }
            // 设置数据
            $pro->data('models',$models);
            // 第一次强力插入
            return true;
        }
        // 生成文章
        do {
            $isDo = true;
            // 遍历模型
            foreach ($pro->data('models') as $model=>$sorts){
                // 循环分类ID
                foreach ($sorts as $sortid) {
                    // 取得当前分类下文档
                    $result = $pro->query($sortid,$model);
                    while ($rs = $pro->fetch($result)) {
                        // 检查页面是否超时，如果超时则跳出循环；
                        if (isOverMaxTime($execTime)) {
                            break 4;
                        }
                        // 生成成功
                        if (Content_Article::createPage($model,$rs['id'])) {
                            // 记录已经生成的文件
                            $pro->insertLogs(array(
                                'dataid'    => $rs['id'],
                                'model'     => $model,
                                'createid'  => $pro->id,
                            ));
                            // 更新已生成的文章数，防止意外关闭无法更新
                            $pro->update();
                            // 睡眠
                            usleep(0.05 * 1000000);
                        }
                    }
                }
            }
            // 页面超时退出
            if ($isDo || isOverMaxTime($execTime)) {
                $isDo = false;
            }
        } while ($isDo);
        // 更新已用时间
        $pro->updateUseTime();
        // 更新当前任务进程信息
        $pro->update();
        // 文章都生成完毕
        if ($pro->isOver()) {
            $pro->delete();
        }
        return true;
    }
    // *** *** www.LazyCMS.net *** *** //
    function createList(&$pro){
        $db  = get_conn(); $execTime = 5;
        // 第一次需要insert数据
        if ($pro->insert) {
            import('system.parsetags'); $tag = new ParseTags(); $template = c('TEMPLATE');
            $listdata = array();
            // 计算分类文档数
            foreach ($pro->data('sortids') as $sortid) {
                // 取得模板地址，并载入模板
                $tag->loadHTML(LAZY_PATH.'/'.$template.'/'.Content_Article::getTemplateBySortId($sortid,'sort'));
                // 取得标签
                $listtag  = $tag->getLabel('content');
                // 取得每页显示条数
                $number   = $tag->getTagAttr($listtag,'number');
                // 取得当前分类下的所有模型
                $models   = Content_Model::getModelsBySortId($sortid,array('modelename'));
                // 计算需要生成的文档数
                $length   = floor(Content_Article::count($sortid,implode(',',$models)) / $number + 1);
                // 计算需要生成的文档总数
                $pro->total = $pro->total + $length;
                // 查询分类信息
                $result = $db->query("SELECT * FROM `#@_content_sort` WHERE `sortid`=".DB::quote($sortid));
                if (!($sortrs = $db->fetch($result))) {
                	$sortrs = array();
                }
                // 记录分类需要生成的文档数
                $listdata[$sortid] = array(
                    'sortrs' => $sortrs,
                    'models' => $models,
                    'total'  => $length,
                    'number' => $number,
                    'page'   => 1,
                    'outhtm' => $tag->HTML,
                );
                // 关闭对象
                $tag->close();
            }
            // 设置数据
            $pro->data('listdata',$listdata);
            // 第一次插入数据
            return true;
        }
        // 生成列表
        do {
            $isDo = true;
            // 遍历所有分类
            $listdata = $pro->data('listdata');
            foreach ($listdata as $sortid => $data) {
                for ($i = $data['page']; $i <= $data['total']; $i++) {
                    // 检查页面是否超时，如果超时则跳出循环；
                    if (isOverMaxTime($execTime)) {
                        break 3;
                    }
                    // 生成成功
                    if (Content_Article::createListPage($listdata[$sortid])) {
                        // 生成一个加一
                        $listdata[$sortid]['page']++; $pro->create++;
                        if ((int)$listdata[$sortid]['page'] == (int)$listdata[$sortid]['total']) {
                            unset($listdata[$sortid]);
                        }
                        $pro->data('listdata',$listdata);
                        // 更新已生成的文章数，防止意外关闭无法更新
                        $pro->update(array(
                            'data' => serialize($pro->data())
                        ));
                    }
                    // 睡眠
                    usleep(0.05 * 1000000);
                }
            }
            // 页面超时退出
            if ($isDo || isOverMaxTime($execTime)) {
                $isDo = false;
            }
        } while ($isDo);
        // 更新已用时间
        $pro->updateUseTime();
        // 更新当前任务进程信息
        $pro->update();
        // 文章都生成完啦，该生成文章列表啦！
        if ($pro->isOver()) {
            //$pro->delete();
        }
        return true;
    }
    /**
     * 生成列表页面
     *
     * @param int $sortid
     * @param int $total
     * @param int $number
     * @param int $page
     */
    function createListPage($data){
        return true;
        if (!$data) { return true; } import('system.keywords');
        $db = get_conn(); $template = c('TEMPLATE');
        $sortrs = $data['sortrs']; $sortid = $sortrs['sortid'];
        $models = $data['models']; $total  = $data['total'];
        $number  = $data['number']; $page  = $data['page'];
        import('system.parsetags'); $tag = new ParseTags();
        // 取得模板地址
        $tmplpath = LAZY_PATH.'/'.$template.'/'.Content_Article::getTemplateBySortId($sortid,'sort');
        // 加载模板
        $tag->loadHTML($tmplpath);
        
        // 分类下如果有多个模块，则进行链表查询
        if (count($models) > 1) {
            $inSQL = null;
            foreach ($models as $model) {
                $table = Content_Model::getDataTableName($model);
                $inSQL.= ($inSQL?" UNION ":null)."SELECT `id`,`order`,'{$model}' AS `model` FROM `{$table}` WHERE `passed`=0 AND `sortid`={$sortid}";
            }
            // 查询的数据可以放在临时表里进行缓存
            $SQL = "SELECT * FROM ({$inSQL}) AS `article`";
        } else {
            // 只有一个模型，进行简单的查询
            $model = array_pop($models);
            $table = Content_Model::getDataTableName($model);
            $SQL = "SELECT `id`,`order`,'{$model}' AS `model` FROM `{$table}` WHERE `passed`=0 AND `sortid`={$sortid}";
        }
        // 查询
        $result = $db->query("{$SQL} ORDER BY `order` LIMIT {$number} OFFSET ".(($page-1)*$number));
        while ($rs = $db->fetch($result)) {
        	// 得到存放数据的表名
            $table  = Content_Model::getDataTableName($rs['model']);
            // 取得一篇文章的所有信息
            $res = $db->query("SELECT * FROM `{$table}` WHERE `id`=".DB::quote($rs['id']));
            if ($data = $db->fetch($res)) {
                $key = new Keywords($rs['model']);
                // 清除标签值
                $tag->clear();
                // 取得模型数据
                $model  = Content_Model::getModelByEname($rs['model']);
                // 取得自定义字段
                $fields = json_decode($model['modelfields']);
                // 设置标签值
                $tag->value(array(
                    'id'        => $data['id'],
                    'sortid'    => $data['sortid'],
                    'order'     => $data['order'],
                    'date'      => $data['date'],
                    'hits'      => $data['hits'],
                    'digg'      => $data['digg'],
                    'path'      => SITE_BASE.$data['path'],
                    'keywords'  => $key->get($data['id']),
                    'description'   => $data['description'],
                ));
                // 替换自定义字段标签
                foreach ($fields as $field) {
                    $tag->value($field->ename,$data[$field->ename]);
                }
                unset($key);
                // 解析模板
                $outHTML = $tag->parse();
                $tag->close();
            }
            // 生成列表文件
            $outFile = LAZY_PATH.'/'.$sortrs['sortpath'].'/index'.($page>1?$page:'').'.html';
            mkdirs(dirname($outFile)); save_file($outFile,$outHTML);
        }
        return true;
    }
}