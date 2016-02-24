<?php
/**
大汉三通平台
 */
class dahanClient{

	/**
	 * url
	 */
	var $url = '‍';

	/**
	 * 用户名
	 */
	var $userName = '';
	
	/**
	 * 密码3mL?IW~*  
	 */
	var $userPass = '';
	
	/**
	 * webservice客户端
	 */
	var $soap;
	
	/**
	 * 往外发送的内容的编码,默认为 UTF-8
	 */
	var $outgoingEncoding = "UTF-8";
	
	/**
	 * 往外发送的内容的编码,默认为 UTF-8
	 */
	var $incomingEncoding = 'UTF-8';
	
	/**
	 * @param string $url 			接口地址
	 * @param string $serialNumber 	用户名
	 * @param string $password		密码
	 * @param string $timeout		连接超时时间，默认0，为不超时
	 * @param string $response_timeout		信息返回超时时间，默认30
	 * 
	 * 
	 */
	function Client($url='' ,$userName='',$userPass='',$timeout = 0, $response_timeout = 30)
	{
		$url = 'http://3tong.net/services/sms?wsdl';
		$userName = 'dh51111';
		$userPass = '3mL?IW~*';
		$this->userName = $userName;
		$this->userPass = md5($userPass);	
		$this->url = $url;	
		
		/**
		 * 初始化 webservice 客户端
		 */
               include_once dirname(dirname(__FILE__))."/dream/nusoap.php";           
		$this->soap = new SoapClient($url);
		$this->soap ->soap_defencoding = $this->outgoingEncoding;
		$this->soap ->decode_utf8 =  false ;
		$this->soap ->xml_encoding =  $this->incomingEncoding;	
	}
	
	/**
	 * 发送短信
	 * @return int 操作结果状态码
	*/
	function dahanSMS($mobiles,$msg)
	{           
	 	$user = core::Singleton('user.member');
		$error_mobiles = $user->getBlackList();unset($user);                     
               
		if(in_array($mobiles,$error_mobiles)){
			$response = " 手机号$mobiles在不通知人名单内";
			return $response;
		}

		//发送短信		
		 $message = "<?xml version=\"1.0\" encoding=\"UTF-8\"?><message>"
		. "<account>"
		. $this->userName
		. "</account><password>"
		. $this->userPass
		. "</password>"
		. "<msgid></msgid><phones>"
		. $mobiles
		. "</phones><content>"
		. $msg
		. "</content><subcode>"
		. ""
		. "</subcode><sendtime></sendtime></message>";
		try{
			$response = $this->soap->submit($message);//
		 }
		 catch(SoapFault $soapFault){
			  file_put_contents('/tmp/sms',date('m-d H:i:s')." ".print_r($message,true)."\n",FILE_APPEND);
			  file_put_contents('/tmp/sms',date('m-d H:i:s')." ".print_r(htmlentities($this->soap->__getLastRequest()),true)."\n",FILE_APPEND);
		 }

		//$response = $this->soap->submit($message);//
		
		return $response;
	}


	/**
	 * 余额
	 * @return int 
	 */
	function dahanYU($userName,$userPass)
	{
		//发送短信
		$message = '<?xml version="1.0" encoding="UTF-8"?><message><account>'.$userName.'</account><password>'.md5($userPass).'</password></message>';
		$url = 'http://www.10690300.com/http/sms/Balance';
		$url .= '?message='.urlencode($message);
		return  file_get_contents($url);
	}
}
?>
