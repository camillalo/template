<?php
/**
 * 读取邮件
 */
class get_content extends api_comm  {

	/**
	 * 设置响应的消息体  返回值必须是json
	 * 
	 */
	
	public function get_response() {
		header('Content-Type:text/html;charset=UTF-8');
		ini_set('date.timezone','PRC'); //时区设置
		require_once (dirname(__FILE__).'/../../comm/email/class.pop3.php');	
		require_once (substr(dirname(__FILE__),0,-13).'/config/config.php');		

		$pop3obj = new SocketPOP3Client($config['email'], $config['password'], $config['host'], $config['port'],$config['ssl_port']);
		$isLogin = $pop3obj->popLogin();

		$ml = $pop3obj->getMailSum();
		$mltot = $ml[0];  
		$array_content = array();
		//下载全部的eml数据 
		 for($i=0;$i<$mltot;$i++){
		 	$content = $pop3obj->getMailMessage(($i+1),2);
		 	//判断是否是smtp服务器发送过来
		 	if(preg_match("/smtp\.(.*)?\.com/", $content[0])){
			 	$con = "";
			 	for ($j=19; $j < count($content); $j++) { 
			 		$con .=$content[$j];
			 	}
			 	$title = $content[7];

			 	$title = explode(":",$title);
			 	$title = $title[1];

			 	$rs = $this->check_title($title);
			 	if(!count($rs)){$this->insert_data($title,$con)}

			 	$array_content[$i]['title'] = $title;
			 	$array_content[$i]['con'] = $con;
			}
		   }	
		 $pop3obj->popLogout();
		 $pop3obj->closeHost();

		return $array_content;

	}

	//查看数据表中是否已经存在
	public function check_title($title){ 
		$comm = core::Singleton('user.comm');
		$result = $comm->check_title($title);unset($comm);
        return $result;
	}

	//添加数据
	public function insert_data($title,$content){ 
		$comm = core::Singleton('user.comm');
		$result = $comm->insert_data($title,$content);unset($comm);
        return $result;
	}

}

?>