<?php
function send_post($url, $post_data=array()) {
	$postdata = http_build_query($post_data);
	$options = array(
			'http' => array(
					'method' => 'POST',
					'header' => 'Content-type:application/x-www-form-urlencoded',
					'content' => $postdata,
					'timeout' => 15 * 60 // 超时时间（单位:s）
			)
	);
	$context = stream_context_create($options);
	$result = file_get_contents($url, false, $context);
	return $result;
}
//发送邮件
$url = "http://dts.huijunet.com:2080/api.php";
$key = "161037ca2a75a04c5da0cd9bdede4824";
$data = array(	
	"pname"=>"huiju",
	"sname"=>"send_email"
);

$json_data = json_encode($data);		
$request = array(
		'content' => $json_data,//请求内容
		'token'   => md5($json_data.$key),//token值
);

$result = send_post($url,$request);

var_dump($result);