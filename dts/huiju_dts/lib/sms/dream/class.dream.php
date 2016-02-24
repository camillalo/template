<?php
/**
 * 梦网短信
 * @author whh
 *
 */

class dream  {
	
	public $server_url = 'http://61.145.229.29:9003/MWGate/wmgw.asmx';
	//public $server_url = 'http://61.145.229.28:7902/MWGate/wmgw.asmx';
	public $user_name = 'J02813';
	//public $user_name = 'JS0175';
	public $password = '510052';
	//public $password = '633221';
	public $pszSubPort = '*';
	public $sms_supplier = 'dream';
	
	public $dream_status = array(
			"-1" => "参数为空。信息、电话号码等有空指针，登陆失败",
			"-2" => "电话号码个数超过100",
			"-10" => "申请缓存空间失败",
			"-11" => "电话号码中有非数字字符",
			"-12" => "有异常电话号码",
			"-13" => "电话号码个数与实际个数不相等",
			"-14" => "实际号码个数超过100",
			"-101" => "发送消息等待超时",
			"-102" => "发送或接收消息失败",
			"-103" => "接收消息超时",
			"-200" => "其他错误",
			"-999" => "web服务器内部错误",
			"-10001" => "用户登陆不成功",
			"-10002" => "提交格式不正确",
			"-10003" => "用户余额不足",
			"-10004" => "手机号码不正确",
			"-10005" => "计费用户帐号错误",
			"-10006" => "计费用户密码错",
			"-10007" => "账号已经被停用",
			"-10008" => "账号类型不支持该功能",
			"-10009" => "其它错误",
			"-10010" => "企业代码不正确",
			"-10011" => "信息内容超长",
			"-10012" => "不能发送联通号码",
			"-10013" => "操作员权限不够",
			"-10014" => "费率代码不正确",
			"-10015" => "服务器繁忙",
			"-10016" => "企业权限不够",
			"-10017" => "此时间段不允许发送",
			"-10018" => "经销商用户名或密码错",
			"-10019" => "手机列表或规则错误",
			"-10021" => "没有开停户权限",
			"-10022" => "没有转换用户类型的权限",
			"-10023" => "没有修改用户所属经销商的权限",
			"-10024" => "经销商用户名或密码错",
			"-10025" => "操作员登陆名或密码错误",
			"-10026" => "操作员所充值的用户不存在",
			"-10027" => "操作员没有充值商务版的权限",
			"-10028" => "该用户没有转正不能充值",
			"-10029" => "此用户没有权限从此通道发送信息",
			"-10030" => "不能发送移动号码",
			"-10031" => "手机号码(段)非法",
			"-10032" => "用户使用的费率代码错误",
			"-10033" => "非法关键词"
	);
		
	public  function send($mobiles,$content,$id=0) {

		$error_mobiles[] = '13567012211';
		$error_mobiles[] = '15921743399';
		$error_mobiles[] = '15921741132';
		$error_mobiles[] = '18919344113';
		$error_mobiles[] = '18116341417';
		$error_mobiles[] = '13482492354';
		$error_mobiles[] = '13162894954';
		$error_mobiles[] = '13818164224';
		$error_mobiles[] = '13524552711';
		$error_mobiles[] = '13916866929';
		$error_mobiles[] = '18930056835';
		if(in_array($mobiles,$error_mobiles)){
			$response = " 手机号$mobiles在不通知人名单内";
			return $response;
		}

		$this->send_time = date('Y-m-d H:i:s'); 
		
		include_once(dirname(__FILE__)."/class.dreamsms.php");
		$dream = new dreamClient();
		$dream->Client($this->server_url,$this->user_name,$this->password);
		$dream->pszSubPort = $this->pszSubPort;
		$dream->setOutgoingEncoding("UTF-8");
		$response =  $dream->dreamSMS($mobiles,$content);
		//print_r($response);
		$this->reponse_time =  date('Y-m-d H:i:s'); 
		//$this->log($id,$mobiles,$content,print_r($response,true));
		
		return $this->deal_response($response);
	}
	
	//这里根据返回值判断是否发送成功
	public function deal_response($response){
		
		$statusCode = $response['MongateCsSpSendSmsNewResult'];
		if(strlen($statusCode) >= 10 && abs(intval($statusCode)) > 999){
			$this->report_id = $statusCode;
			$this->send_status    = '发送成功';
		}else{
			$this->send_status    = '发送失败';
			if(isset($this->dream_status[$statusCode])){
				$this->send_status .= '-'.$this->dream_status[$statusCode];
			}
		}
		return Array('report_id'=>$this->report_id,'send_status'=>$this->send_status);
	}
}
?>
