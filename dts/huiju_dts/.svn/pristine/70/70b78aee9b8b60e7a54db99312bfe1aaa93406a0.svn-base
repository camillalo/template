<?php 
header("Content-Type:text/html;charset=utf-8");
require_once(dirname(__FILE__).'/../index.php');
core::Singleton('comm.remote.remote');


//读取邮件
$data = array(
	"sname" => "get_content"
);
remote::$open_debug=1;
$result = remote::send($data);

print_r($result);
//var_dump(json_decode($result));



?>