<?php
/**
 * Created by PhpStorm.
 * User: chimero
 * Date: 14-6-7
 * Time: 20:49
 */
use \Slim\Load as load;

// 接口详情页面
$app->get(
    '/api:id',
    function ($id) use ($app) {
        $_active =  $app->request->get('api_type');
        $where = $id ? "where id='" . ltrim($id, '/') . "'" : "where 1=1";
        $where .= $_active ? " and api_type='{$_active}'" : '';

        $db = load::db('api');
        $sql = "select * from api  {$where} order by id desc limit 1";
        $current = $db->query($sql, 'array');

        if(empty($current)) {
            echo 'not found';
            $app->halt(404);
        } else {

            $sql = "select * from api_param where api_id = '{$current['id']}'";
            $db->query($sql);
            $current['params'] = $db->retArray();
        }

        // 获取所有接口
        $sql = "select id, zh_name,en_name from api";
        $db->query($sql);
        $apis = $db->retArray();

        // 获取所有分类
        $sql = "select * from api_cat";
        $db->query($sql);
        $cats = $db->retArray();

        foreach($apis as $api) {

        }



        $app->render('api/info.php', array('_title' => 'api', '_active' => $current['api_type'],'current' => $current));

    }
)->conditions(array('id' => '(\/[1-9]{1}[0-9]*)|(\s{0})'));

// 接口列表页面
$app->get(
    '/api/list',
    function () use ($app) {
        $keywords =  $app->request->get('keyword', '');
        $api_type =  $app->request->get('api_type', 'inside');
        $where = $keywords ? "and (zh_name like '%{$keywords}%' or en_name like '%{$keywords}%')" : '';
        $db = load::db('api');
        $sql = "select * from api where api_type='{$api_type}' {$where} order by id desc ";
        if($api_type == 'all') {
            $sql = "select * from api  {$where} order by id desc ";
        }

        $db->query($sql);
        $list = $db->retArray();
        echo json_encode($list);
        exit;
    }
)->conditions(array('id' => '(\/[1-9]{1}[0-9]*)|(\s{0})'));


// 撰写文档
$app->get(
    '/api/add',
    function () use ($app) {
        $app->render('api/add.php', array('_title' => '撰写文档', '_active' => 'add', '_formid' => md5(microtime())));
    }
);

$app->post(
    '/api/add',
    function () use ($app) {
        $pdata = $app->request->post();

        // 简单过滤数据
        $needFields = array('zh_name', 'en_name', 'api_type', 'request_type', 'url');
        foreach($needFields as $val) {
            if(!isset($pdata[$val]) || $pdata[$val] == '') {
                echo '必填参数不能为空';
                exit;
            }
        }

        // 写入主表
        $db = load::db('api');
        $now = time();
        $sql = "insert into api(`zh_name`, `en_name`, `api_type`, `request_type`, `url`, `desc`, `create_at`, `update_at`)values
        ('{$pdata['zh_name']}', '{$pdata['en_name']}', '{$pdata['api_type']}', '{$pdata['request_type']}', '{$pdata['url']}'
        ,'{$pdata['desc']}', '{$now}', '{$now}')";
        $ret = $db->query($sql);

        if($ret && !empty($pdata['param'])) {
            $id = $db->lastID();
            $arr = array();
            $params = $pdata['param'];
            //myprint($pdata);
            $params = array_filter($params);
            foreach($params as $key => $val) {
                $is_need = $pdata['is_need'][$key];
                $param_type = $pdata['param_type'][$key];
                $param_desc = $pdata['param_desc'][$key];
                $arr[] = "('{$id}', '{$is_need}', '{$val}', '{$param_type}', '{$param_desc}')";
            }
            $str = implode(',', $arr);
            $sql = "insert into api_param(`api_id`, `is_need`, `param`, `param_type`, `param_desc`)values {$str}";
            //myprint($sql);
            $db->query($sql);
            $app->redirect('/api/' . $id);
        }


        exit('未知错误');
    }
);


// 编辑页面
$app->get(
    '/api/edit/:id',
    function ($id) use ($app) {
        $db = load::db('api');
        $sql = "select * from api where id='{$id}'";
        $api = $db->query($sql, 'array');

        if($api) {
            $api_id = $api['id'];
            $sql = "select * from api_param where api_id='{$api_id}'";
            $db->query($sql);
            $api['params'] = $db->retArray();
            $app->render('api/edit.php', array('_title' => '编辑', '_active' => 'edit', 'api' => $api));
        } else {
            $app->redirect('api/list');
        }
    }
)->conditions(array('id' => '[1-9]{1}[0-9]*'));

// 编辑操作
$app->post(
    '/api/edit/:id',
    function ($id) use ($app) {
        $pdata = $app->request->post();
        // 简单过滤数据
        $needFields = array('zh_name', 'en_name', 'api_type', 'request_type', 'url');

        foreach($needFields as $val) {
            if(!isset($pdata[$val]) || $pdata[$val] == '') {
                echo '必填参数不能为空';
                exit;
            }
        }

        // 写入主表
        $db = load::db('api');
        $now = time();
        $sql = "update api set
        `zh_name` = '{$pdata['zh_name']}',
        `en_name` = '{$pdata['en_name']}',
        `api_type` = '{$pdata['api_type']}',
        `request_type` = '{$pdata['request_type']}',
        `url` = '{$pdata['url']}',
        `desc` = '{$pdata['desc']}',
        `update_at` = '{$now}'
        where id = {$id}";

        $ret = $db->query($sql);

        if($ret && !empty($pdata['param'])) {
            // 删除旧数据
            $sql = "delete from api_param where api_id={$id}";
            $db->query($sql);

            // 添加数据
            $arr = array();
            $params = $pdata['param'];
            $params = array_filter($params);
            foreach($params as $key => $val) {
                $is_need = $pdata['is_need'][$key];
                $param_type = $pdata['param_type'][$key];
                $param_desc = $pdata['param_desc'][$key];
                $arr[] = "('{$id}', '{$is_need}', '{$val}', '{$param_type}', '{$param_desc}')";
            }
            $str = implode(',', $arr);
            $sql = "insert into api_param(`api_id`, `is_need`, `param`, `param_type`, `param_desc`)values {$str}";

            $db->query($sql);
            $app->redirect('/api/' . $id);
        }


        exit('未知错误');
    }
)->conditions(array('id' => '[1-9]{1}[0-9]*'));

// 接口调试
$app->get(
    '/api/tool:id',
    function ($id) use ($app) {
        $_active =  $app->request->get('api_type');
        $where = $id ? "where id='" . ltrim($id, '/') . "'" : "where 1=1";
        $where .= $_active ? " and api_type='{$_active}'" : '';

        $db = load::db('api');
        $sql = "select * from api  {$where} order by id desc limit 1";
        $current = $db->query($sql, 'array');

        if(empty($current)) {
            echo 'not found';
            $app->halt(404);
        } else {

            $sql = "select * from api_param where api_id = '{$current['id']}'";
            $db->query($sql);
            $current['params'] = $db->retArray();
        }
        $app->render('api/tool.php', array('_title' => '调试工具', '_active' => 'tool', 'current' => $current));
    }
)->conditions(array('id' => '(\/[1-9]{1}[0-9]*)|(\s{0})'));


$app->post(
    '/api/tool/send',
    function () use ($app) {
        $form = $app->request->post('form');
        parse_str($form, $pdata);
        $request_type = $pdata['request_type'];
        if($request_type == 'rest') {
            $url = $pdata['url'];
            $sp = preg_match('/[?]/', $url) ? '&' : '?';
            $param = array();
            foreach($pdata['param'] as $key => $val) {
                if($val && isset($pdata['param_val'][$key]) && $pdata['param_val'][$key]) {
                    $param[$val] = $pdata['param_val'][$key];
                }
            }
            $param_str = http_build_query($param);
            $url = $url . $sp . $param_str;
            $res = file_get_contents($url);

        } else {
            $url = $pdata['url'];

            $header = array();
            foreach($pdata['header'] as $key => $val) {
                if($val && isset($pdata['header_val'][$key]) && $pdata['header_val'][$key]) {
                    $header[$val] = $pdata['header_val'][$key];
                }
            }



            $body = array();
            $pdata['param'] = isset($pdata['param']) ? $pdata['param'] : array();
            foreach($pdata['param'] as $key => $val) {
                if($val && isset($pdata['param_val'][$key]) && $pdata['param_val'][$key]) {
                    $body[$val] = $pdata['param_val'][$key];
                }
            }

            $request['request']['header'] = array_merge($header, array('format' => 'json'));
            $request['request']['body'] = array_merge($body, array('request_from' => 'wiki'));
            $request = '<?xml version="1.0" encoding="UTF-8"?>'.toXml($request);
            $client = new SoapClient("http://dts.kuaidihelp.com/webService/dts.php");
            $res =  $client->exec($request);
            $url = htmlentities($request, ENT_QUOTES, 'utf-8', FALSE);

        }

        // 输出数据
        $result = array(
            'request' => $url,
            'response' => prettyOut(json_decode($res, true), $pdata['out_type'])
        );
        echo json_encode($result);
        die;

    }
);