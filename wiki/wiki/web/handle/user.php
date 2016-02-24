<?php
/**
 * Created by PhpStorm.
 * User: chimero
 * Date: 14-6-7
 * Time: 20:51
 */
// 注册为开发者
// ajax
$app->post(
    '/user/register',
    function () use ($app) {
        if($app->request->isAjax()) {
            myprint(11);
        }

    }
);

// 登陆到wiki
// ajax
$app->post(
    '/user/login',
    function () use ($app) {
        if($app->request->isAjax()) {

        }
        myprint($app);
        echo '注册用户';
    }
);