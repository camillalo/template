<?php
/**
 * API 文档wiki
 * @author llq
 */

/*
 ***************************************************
 * config the app
 ***************************************************
 */
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control:no-cache, must-revalidate");
header("Pragma:no-cache");
date_default_timezone_set("Asia/Shanghai");
require '../vender/helper/common.php';
require '../vender/Slim/Slim.php';
require '../vender/Slim/Load.php';
include_once(dirname(__FILE__) . '/http.php');
\Slim\Slim::registerAutoloader();

//myprint(dirname(__FILE__) . '/view');
$app = new \Slim\Slim(array(
    'mode' => 'development',
    'debug' => true,
    'templates.path' => dirname(__FILE__) . '/view',
    'cookies.secure' => true,
    'cookies.secret_key' => '123456',
));


/*
 ***************************************************
 * dispatch route to handle
 ***************************************************
 */
use \Slim\Load as load;

// 组织数据
function get_data() {
    $db = load::db('api');

    // 获取分类
    $sql = "select * from api_cat order by id desc ";
    $db->query($sql);
    $cats = $db->retArray();



    // 获取apis
    $sql = "select * from api";
    $db->query($sql);
    $rets = $db->retArray();

    // 组织多维数据
    $apis = array();
    $cids = array();
    foreach($cats as $cat) {
        array_push($cids, $cat['id']);
    }

    foreach($rets as $ret) {
        if(in_array($ret['cid'], $cids)) {
            $apis[$ret['cid']][] = $ret;
        }
    }

    return array('apis' => $apis, 'cats' => $cats);
}

// 首页
$app->get('/', function () use ($app) {
        $data = array_merge(
            array(
                '_active' => 'index',
                '_title' => '首页'
            ),
            get_data()
        );
        $app->render('api/index.php', $data);
    }
);

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
            $app->redirect('/');
        } else {

            $sql = "select * from api_param where api_id = '{$current['id']}' order by id asc";
            $db->query($sql);
            $current['params'] = $db->retArray();
        }

        $data = array(
            '_title' => 'api',
            '_active' => 'index',
            'current' => $current
        );
        $data = array_merge($data, get_data());
        $app->render('api/info.php', $data);

    }
)->conditions(array('id' => '(\/[1-9]{1}[0-9]*)|(\s{0})'));

// 添加get
$app->get('/add', function () use ($app) {
        if(!getUserInfo()) {
            $app->redirect('/login?ref=' . $_SERVER['REQUEST_URI'], 303);
        }
        $db = load::db('api');
        $sql = "select * from api_cat order by id desc ";
        $db->query($sql);
        $cats = $db->retArray();

        $app->render(
            'api/add.php',
            array(
                '_active' => 'add',
                '_title' => '撰写文档',
                'cats' => $cats
            )
        );
    }
);

// 撰写接口文档
$app->post('/add', function () use ($app) {
        $pdata = $app->request->post();
        // 过滤数据
        $needFields = array(
            'cat',
            'en_name'
        );
        foreach($needFields as $val) {
            if(!isset($pdata[$val]) || $pdata[$val] == '') {
                echo '接口类型和名称不能为空';
                exit;
            }
        }

        // 写入主表
        $db = load::db('api');
        $now = time();
        $uid = getUserInfo('uid') ? getUserInfo('uid') : 0;
        $sql = "insert into api(
            `cid`,
            `uid`,
            `zh_name`,
            `en_name`,
            `desc`,
            `create_at`,
            `update_at`
        )values
        (
        '{$pdata['cat']}',
        '{$uid}',
        '{$pdata['zh_name']}',
        '{$pdata['en_name']}',
        '{$pdata['desc']}',
        '{$now}',
        '{$now}'
        )";
        $ret = $db->query($sql);

        if($ret && !empty($pdata['param'])) {
            $id = $db->lastID();
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
            //myprint($sql);
            $db->query($sql);
            $app->redirect('/api/' . $id, 303);
        }


        exit('未知错误');
    }
);


// 编辑
$app->get(
    '/edit/:id',
    function ($id) use ($app) {
        if(!getUserInfo()) {
            $app->redirect('/login?ref=' . $_SERVER['REQUEST_URI'], 303);
        }
        $db = load::db('api');
        $sql = "select * from api where id='{$id}'";
        $api = $db->query($sql, 'array');

        if($api) {
            $api_id = $api['id'];
            $sql = "select * from api_param where api_id='{$api_id}'";
            $db->query($sql);
            $api['params'] = $db->retArray();

            // 获取分类
            $sql = "select * from api_cat order by id desc ";
            $db->query($sql);
            $cats = $db->retArray();

            $app->render('api/edit.php', array('_title' => '编辑', '_active' => 'edit', 'api' => $api, 'cats' => $cats));
        } else {
            $app->redirect('/');
        }
    }
)->conditions(array('id' => '[1-9]{1}[0-9]*'));

// 编辑操作
$app->post(
    '/edit/:id',
    function ($id) use ($app) {

        $pdata = $app->request->post();
        // 简单过滤数据
        $needFields = array('en_name', 'cat');

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
        `cid` = '{$pdata['cat']}',
        `zh_name` = '{$pdata['zh_name']}',
        `en_name` = '{$pdata['en_name']}',
        `desc` = '{$pdata['desc']}',
        `update_at` = '{$now}'
        where id = {$id}";

        $ret = $db->query($sql);

        if($ret ) {
            if(!empty($pdata['param'])) {
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

            }
            $app->redirect('/api/' . $id, 303);
        }


        exit('未知错误');
    }
)->conditions(array('id' => '[1-9]{1}[0-9]*'));



// 接口调试
$app->get(
    '/debug:id',
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

            $sql = "select * from api_param where api_id = '{$current['id']}' order by id asc";
            $db->query($sql);
            $current['params'] = $db->retArray();
        }
        $data = array('_title' => '调试工具', '_active' => 'debug', 'current' => $current);
        $data = array_merge($data, get_data());

//        myprint($data);
        $app->render('api/debug.php', $data);
    }
)->conditions(array('id' => '(\/[1-9]{1}[0-9]*)|(\s{0})'));


$app->post(
    '/debug/send',
    function () use ($app) {
        $form = $app->request->post('form');
        parse_str($form, $pdata);
        // 参数处理
        $pdata['param'] =  isset($pdata['param']) ? $pdata['param'] : array();
        $param = array();
        foreach($pdata['param'] as $key => $val) {
            if($val && isset($pdata['param_val'][$key]) && $pdata['param_val'][$key]) {
                $param[$val] = $pdata['param_val'][$key];
            }
        }

        $keys = array(
            'wiki_test' => '9f6860c953bcd0222f2a71b45c5bc4a6',
        );

        // 密钥
        $session = '';
        // 密钥
        $key = $keys['wiki_test'];

        /*
        // 获取测试密钥
        if($pdata['platform'] == 'androids' || $pdata['platform'] == 'ioss') {
            $db = load::db('kd_shop');
            $sql = "select * from tbl_wd_user_login  WHERE  user_name ='13661964640' order by update_datetime desc limit 1";
            $current = $db->query($sql, 'array');
            $session = 's' . $current['login_session'];
        } else {
            $db = load::db('user');
            $sql = "select * from tbl_user_login  WHERE  user_name ='13661964640' order by update_datetime desc limit 1";
            $current = $db->query($sql, 'array');
            $session = 'c' . $current['login_session'];
        }
		*/


        // 请求地址
        $url = 'http://dts.huijunet.com:2080/api.php';
        $data = array(
            // 使用平台
            'pname' => 'wiki_test',
            // 接口名称
            'sname' => $pdata['api_name']
        );
        // 请求数据
        $data = array_merge($data, $param);
		
        $request = array(
            'content' => jsonEncode($data),//请求内容
            'token'   => md5(jsonEncode($data).$key),//token值
        );
		
		
        $res = send_post($url, $request);

        // 输出数据
        $result = array(
            'request' => prettyOut($request, 'json'),
            'response' => prettyOut(json_decode($res, true) ? json_decode($res, true) : $res, $pdata['out_type'])
        );
        echo json_encode($result);
        die;

    }
);

// 小工具
$app->get('/tools', function () use ($app) {
    $data = array_merge(
        array(
            '_active' => 'tools',
            '_title' => '小工具'
        ),
        get_data()
    );
    $app->render('api/tools.php', $data);
}
);
$app->post('/tools/debug',
    function () use ($app) {
        $form = $app->request->post('form');
        parse_str($form, $pdata);
        // 参数处理
        $pdata['param'] =  isset($pdata['param']) ? $pdata['param'] : array();
        $param = array();
        foreach($pdata['param'] as $key => $val) {
            if($val && isset($pdata['param_val'][$key]) && $pdata['param_val'][$key]) {
                $param[trim($val)] = $pdata['param_val'][$key];
            }
        }

        $keys = array(
            'androids' => array(
                'app_id' => 10002,
                'app_key' => '4accd1296e8f514627a411e4e2fbfc5f',
            ),
            'androidc' => array(
                'app_id' => 10001,
                'app_key' => '55541f60566fa71dcdb19d4188aba6c3',
            ),
            'androidcsto' => array(
                'app_id' => 10005,
                'app_key' => '4056222b7a22c66b0643194bbb2777e1',
            ),
            'ioss' => array(
                'app_id' => 10004,
                'app_key' => '86d8a8d5a0abf9683e9998cc5b24493a',
            ),
            'iosc' => array(
                'app_id' => 10003,
                'app_key' => '727493f913ba0cce122892217c8f7948',
            ),
        );

        // 密钥
        $session = '';
        // 密钥
        $app_key = $keys[$pdata['platform']]['app_key'];
        $app_id = $keys[$pdata['platform']]['app_id'];
        $ts = time();
        $api = '/' . trim(trim($pdata['api']), '/');
        $sign = md5($ts . $app_key . $api . $app_id);

        // 获取测试密钥
        if($app_id == 10002 || $app_id == 10004) {
            $db = load::db('kd_shop');
            $sql = "select * from tbl_wd_user_login  WHERE  user_name ='13661964640' order by update_datetime desc limit 1";
            $current = $db->query($sql, 'array');
            if($current) {
                $session = 's' . $current['login_session'];
            }

        } else {
            $db = load::db('user');
            $sql = "select * from tbl_user_login  WHERE  user_name ='13661964640' order by update_datetime desc limit 1";
            $current = $db->query($sql, 'array');
            if($current) {
                $session = 'c' . $current['login_session'];
            }

        }

        $data = array(
            'sign' => $sign,
            'ts' => $ts,
            'app_id' => $app_id,
            'data' => json_encode($param)
        );



        // 请求地址
        $url = 'http://api.kuaidihelp.com/' . $api;
        $opt = empty($session) ? array() : array('cookie' => 'session_id=' . $session);
        $rets = http::post($url, $data, $opt);
        $res = json_decode($rets, true);
        if(empty($rets) || json_last_error() != 0) {
            $res = array(
                'code' => 1000,
                'msg' => '未知错误'
            );
        }

        $request = array(
            'url' => $url,
            'http' => $data,
            'session_id' => $session
        );
        // 输出数据
        $result = array(
            'request' => prettyOut($request, 'json'),
            'response' => prettyOut($res, $pdata['out_type'])
        );
       
        echo json_encode($result);
        die;
    }
);

// 状态码
$app->get(
    '/code',
    function () use ($app) {
        $ret = http::get('http://core.kuaidihelp.com/v1/code/index');
        $res = json_decode($ret, true);
        $data = array_merge(
            array(
                '_active' => 'code',
                '_title' => '状态码'
            ),
            array(
                'codes' => $res['data']
            )
        );
        $app->render('api/code.php', $data);
    }
);




// 登录
$app->get(
    '/login',
    function () use ($app) {
        $app->render('user/login.php', array('_title' => '登录WiKi'), null , false);
    }
);
$app->post(
    '/login',
    function () use ($app) {
        $pdata = $app->request->post();

        if(empty($pdata['password']) || empty($pdata['username'])) {
            echo json_encode(array('ret' => 1, 'msg' => '用户不存在'));
            exit;
        }
        $db = load::db('api');
        $sql = "select * from user where username='{$pdata['username']}' and password='{$pdata['password']}'";
        $current = $db->query($sql, 'array');
        if($current) {
            setcookie('userinfo', serialize($current), time()+3600000);
            echo json_encode(array('ret' => 0, 'msg' => '登录成功'));
            exit;
        }

        echo json_encode(array('ret' => 1, 'msg' => '用户不存在'));
        exit;
    }
);

$app->get(
    '/logout',
    function () use ($app) {
        setcookie('userinfo', '', -36000000);
        $app->redirect($_SERVER['HTTP_REFERER'], 303);
    }
);



/*
 ***************************************************
 * run it now
 ***************************************************
 */
$app->run();
