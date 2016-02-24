<?php
	header('Content-Type:text/html;charset=UTF-8');
	ini_set('date.timezone','PRC'); //时区设置
	require_once ('smtp.class.php');
	require_once ('config.php');

	$email = $_POST['email'];
	$subject_title = $_POST['subject_title'];
	$subject_content = $_POST['subject_content'];
	//print_r($_POST);
	//exit();

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

	echo $_smtp_class->send($_from,$_from_name,$_to,$_subject,$_body);
?>