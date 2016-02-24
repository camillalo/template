<?php
// 引入核心文件
require_once('index.php');

// 引入管理类
$api = core::Singleton('api.api');

$content = $_POST['content'];//json格式
$token   = $_POST['token'];

$result =  $api->exec($content, $token);

echo $result;//是json格式的

