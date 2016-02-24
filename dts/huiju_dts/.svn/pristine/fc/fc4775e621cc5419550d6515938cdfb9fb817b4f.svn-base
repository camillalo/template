<?php
//header('Access-Control-Allow-Origin:*');
//header('Access-Control-Allow-Headers:Origin, X-Requested-With, Content-Type, Accept');
//file_put_contents('/tmp/re3',date('H:i:s').': '.print_r(file_get_contents('php://input', 'r'),true). ' '.print_r($_REQUEST,true) . ' '.print_r($_SERVER,true),FILE_APPEND);
//file_put_contents('/tmp/re2',date('H:i:s').': '.print_r($_REQUEST,true),FILE_APPEND);
//file_put_contents('/tmp/re2',date('H:i:s').': '.print_r($_SERVER,true),FILE_APPEND);

// 引入核心文件
require_once(dirname(__FILE__).'/../header.php');
// 引入管理类
$api = core::Singleton('api.api');

$content = $_REQUEST['content'];//json格式
$token   = $_REQUEST['token'];
/*

file_put_contents('D:\test.txt',date('H:i:s').': '.print_r($token,true),FILE_APPEND);
exit;

 
$content = '{"pname":"web","sname":"banner.get","latest_time":"2015-04-13 11:12"}';
$token = '36a332242972cadd5c0ee25180b2d4a1';
 *  * */
 
if(ini_get("magic_quotes_gpc")=="1"){
	$content = stripslashes($content);
}
$result  = $api->exec($content,$token);
echo $result;//是json格式的
?>