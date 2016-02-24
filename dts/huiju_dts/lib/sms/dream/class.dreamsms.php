<?php
include_once(dirname(__FILE__)."/nusoap.php");

/**
 梦网短信平台
 */
class dreamClient{
	/**
	 * 用户名
	 */
	var $userName;
	
	/**
	 * 密码
	 */
	var $userPass;
	
	//子端口号码，不带请填星号{*} 长度由账号类型定4-6位，通道号总长度不能超过20位。如：10657****主通道号，3321绑定的扩展端口，主+扩展+子端口总长度不能超过20位。
	var $pszSubPort = '*';
	
	/**
	 * webservice客户端
	 */
	var $soap;
	
	/**
	 * 默认命名空间
	 */
	var $namespace = 'http://tempuri.org/';
	
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
	function Client($url ,$userName,$userPass,$timeout = 0, $response_timeout = 30)
	{
		$this->userName = $userName;
		$this->userPass = $userPass;
		
		/**
		 * 初始化 webservice 客户端
		 */	
		$this->soap = new nusoap_client($url,true,false,false,false,false,$timeout,$response_timeout); 
		$this->soap->soap_defencoding = $this->outgoingEncoding;
		$this->soap->decode_utf8 = false;			
	}

	/**
	 * 设置发送内容 的字符编码
	 * @param string $outgoingEncoding 发送内容字符集编码
	 */
	function setOutgoingEncoding($outgoingEncoding='UTF-8')
	{
		$this->outgoingEncoding =  $outgoingEncoding;
		$this->soap->soap_defencoding = $this->outgoingEncoding;
		
	}
	
	/**
	 * 设置接收内容 的字符编码
	 * @param string $incomingEncoding 接收内容字符集编码
	 */
	function setIncomingEncoding($incomingEncoding)
	{
		$this->incomingEncoding =  $incomingEncoding;
		$this->soap->xml_encoding = $this->incomingEncoding;
	}
	
	function setNameSpace($ns)
	{
		$this->namespace = $ns;
	}
	
	function getError()
	{		
		return $this->soap->getError();
	}
	
	/**
	 * 发送短信
	 * @return int 操作结果状态码
	*/
	function dreamSMS($mobiles,$msg)
	{
		//$result = array("status"=>false,"msg"=>"发送失败");
		
		if (empty($this->pszSubPort)){
			$this->pszSubPort = '*';
		}
		$params = array(
			'userId'=>$this->userName,
			'password'=>$this->userPass,
			'pszMobis'=>$mobiles,//可以以逗号分隔的多个手机号
			'pszMsg'=>$msg,
			'iMobiCount'=>count(explode(',',$mobiles)),
			'pszSubPort'=>$this->pszSubPort
		);

		$response = $this->soap->call("MongateCsSpSendSmsNew",$params,$this->namespace);
		return $response;
		/*
		$statusCode = $response['MongateCsSpSendSmsNewResult'];
		return $statusCode;
		
		
		if(isset($this->status[$statusCode]))
		{
				$result['status'] = false;
				$result['msg'] = $this->status[$statusCode];
		}
		elseif(strlen($statusCode) >= 10 && abs(intval($statusCode)) > 999)
		{
				$result['status'] = true;
				$result['msg'] = $statusCode;//"发送成功"
		}
		return $result;
		*/
	}
}
?>
