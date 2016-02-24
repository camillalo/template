<?php
ini_set('date.timezone','PRC');
require_once("weixin.class.php"); 
session_start();


if(!isset($_POST['type'])){ 
	$type = "";
}else{ 
	$type = $_POST['type'];
}

//接收code
if(!isset($_SESSION['openid']) && isset($_POST['code']) && !empty($_POST['code'])){ 
	$code = $_POST['code'];
	$weixin =  file_get_contents("https://api.weixin.qq.com/sns/oauth2/access_token?appid=wx5fdc8233e9678fdf&secret=ecfbc6a335c7856e3e331330ba15cd57&code=".$code."&grant_type=authorization_code");//通过code换取网页授权access_token
	$jsondecode = json_decode($weixin); //对JSON格式的字符串进行编码
	$array = get_object_vars($jsondecode);//转换成数组
	$openid = $array['openid'];//输出openid
	$_SESSION['openid'] = $openid;		
}

if($type =="start"){ 
	
	if(!isset($_SESSION['href']) && empty($_SESSION['href'])){ 
		$_SESSION['href'] = $_POST['start_href'];
		$_SESSION['start_time'] = time();
	}else{ 
		$start_href = $_SESSION['href'];
		$href = $_POST['start_href'];
		$start_time = $_SESSION['start_time'];

		if($start_href != $href){ 
			$end_time = time();

			$weixin = new class_weixin_adv("wx5fdc8233e9678fdf", "ecfbc6a335c7856e3e331330ba15cd57");
			$openid = $_SESSION['openid'];

			$member_info = $weixin->get_user_info($openid);
   			$openid = $member_info['nickname'];
			$time_long = $end_time - $start_time;
			$ip = $_SERVER['REMOTE_ADDR'];
			$time = date("Y-m-d H:i:s");
			$link = $start_href;

			$_SESSION['href'] = $href;
			$_SESSION['start_time'] = $end_time;

			relate_db("localhost","wozan_woz","mBcQmeF6","wozan_woz");
			$sql = "INSERT INTO shop_weixin(openid,time_len,link,ip,time)values('$openid','$time_long','$link','$ip','$time')";
			mysql_query($sql);
				
		}
	}

}

/**
**链接数据库
**/
function relate_db($host,$user,$password,$db){ 
    $conn = mysql_connect($host,$user,$password) or die(mysql_error());
    mysql_select_db($db) or die(mysql_error());
    mysql_query("set names 'utf8'");
    return "success";
}
?>