<?php
header("Content-Type:text/html;charset=utf-8"); 
require_once(dirname(__FILE__).'/../index.php');
core::Singleton('comm.remote.remote');


$labels = array("建筑","餐饮","行业");
$from = array("peter");

$object = array(
				array("emaiId"=>0,"from"=>"peter","title"=>"建筑行业"),
				array("emaiId"=>1,"from"=>"david","title"=>"餐饮业"),
				array("emaiId"=>2,"from"=>"seven","title"=>"建筑不行业")
	);

//发送邮件
$data = array(
	"sname"  => "choose_email",
	"object" => $object,
	"labels"  => $labels,
	"from"  => $from,
);
//remote::$open_debug=1;
$result = remote::send($data);

var_dump($result);



?>