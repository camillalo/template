<?php
	header('Content-Type:text/html;charset=UTF-8');
	ini_set('date.timezone','PRC'); //时区设置
	ini_set("display_errors", "On");
	require_once ('smtp.class.php');
	require_once ('config.php');
	require_once ('pop3.php');
	relate_db($config['db_host'],$config['db_user'],$config['db_password'],$config['db_name']);

	$subject_title = $_POST['subject_title'];
	$subject_content = $_POST['subject_content'];

	$email = $config['accept_email'];//接受邮件账号
	$_smtp_server = $config['smtp_server'];//SMTP服务器
	$_smtp_server_port =$config['smtp_server_port'];//SMTP服务器端口

	$_to = $email;//收件人
	$_smtp_user = $config['smtp_user'];//SMTP服务器的用户帐号
	$_smtp_password = $config['smtp_password'];//SMTP服务器的用户密码
	$_from = $config['smtp_user'];//发件人邮箱
	$_from_name = $config['from_name'];//发件人姓名

	$_subject = $subject_title;//邮件主题

	//body内文字必须要有12个以上
	$_body = $subject_content;//邮件内容

	$_smtp_class = new smtp($_smtp_server,$_smtp_server_port);
	$_smtp_class->login($_smtp_user,$_smtp_password);

	$result = $_smtp_class->send($_from,$_from_name,$_to,$_subject,$_body);
	if($result=="success"){ 
		$pop3obj = new SocketPOP3Client($config['email'], $config['password'], $config['host'], $config['port']);
		$isLogin = $pop3obj->popLogin();

		//邮件数量、字节
		$ml = $pop3obj->getMailSum();
		//echo "<pre>";
		//print_r($ml);
		$mltot = $ml[0];  


		//下载全部的eml数据 
		 for($i=0;$i<$mltot;$i++){
		 	$content = $pop3obj->getMailMessage(($i+1),2);

		 	//判断是否是smtp服务器发送过来
		 	if(preg_match("/smtp\.(.*)?\.com/", $content[0])){
		 		if(preg_match("/发稿人/", $content[6])){ 
			 	$con = "";
			 	for ($j=19; $j < count($content); $j++) { 
			 		$con .=$content[$j];
			 	}

			 	$title = $content[7];
			 	$content = $con;

			 	$title = explode(":",$title);
			 	$title = $title[1];

			 	//查看数据表中是否已经存在
			 	$sql = "select title from email_content where title like '%$title%'";

			 	$row = mysql_query($sql);
			 	$rs = mysql_fetch_array($row);
			 	if(!$rs){ 
			 		$ip = $_SERVER['REMOTE_ADDR'];
			 		$time = date("Y-m-d H:i:s");
			 		$sql = "insert into email_content (title,content,ip,time) values ('$title','$content','$ip','$time')";
			 		mysql_query($sql);
			 	}
			 	$pop3obj->delMail($i+1);
			 	$mltot--;
			 }
			}
		   }	
		 $pop3obj->popLogout();
		 $pop3obj->closeHost();

		echo $result;
	}
?>