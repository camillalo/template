<?php
/**
 * 梦网营销短信接口
 */

 class marketing {

	public $server_url = 'http://61.145.229.28:7902/MWGate/wmgw.asmx';
	public $user_name = 'J02813';
	public $password = '510052';
	public $pszSubPort = '*';
	public $sms_supplier = 'dream';
	
	//批量每次发送数，最大只允许500
	public $step  = 2;
	
	//需要发送的手机号码，多个用逗号峰
	public $mobiles = '';

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

	public  function send($mobiles,$content) {
		$step            = $this->step;
		
		include_once(dirname(__FILE__)."/class.dreamsms.php");
		$dream = new dreamClient();
		$dream->Client($this->server_url,$this->user_name,$this->password);
		$dream->pszSubPort = $this->pszSubPort;
		$dream->setOutgoingEncoding("UTF-8");
		
		$string_num = 12*$step;
		$from = $i = 0;
		while(true) {
			$mobiles_onetime = substr($mobiles,$from,$string_num);
			$from += $string_num ;
			if(!empty($mobiles_onetime)) {
				$mobiles_onetime = trim($mobiles_onetime,',');
				echo "\n".date('Y-m-d H:i:s').' : '.$mobiles_onetime."\n";
				
				$response =  $dream->dreamSMS($mobiles_onetime,$content);

				$result = $this->deal_response($response);

				$report_id   = $result['report_id'];
				$send_status = $result['send_status'];
				$this->log($mobiles_onetime,$content,print_r($response,true),$report_id,$send_status);
				
				unset($mobiles_onetime,$response,$report_id,$send_status,$result);
				$i++;
				sleep(1);
			}else{
				echo 'pages: '.$i;
				exit;
			}
		}
		unset($dream);
	}

	public function test($mobiles){
		$step       = $this->step;
		$string_num = 12*$step;
		$from = 0;
		while(true) {
			$mobiles_onetime = substr($mobiles,$from,$string_num);
			$from += $string_num ;
			if(!empty($mobiles_onetime)) {
				$mobiles_onetime = trim($mobiles_onetime,',');
				echo "\n".date('Y-m-d H:i:s').' : '.$mobiles_onetime."\n";
				sleep(1);
			}else{
				exit;
			}
		}
		exit;
	}

	public function log($mobiles,$content,$response,$report_id='',$send_status=''){
		
		$send_time = date('Y-m-d H:i:s');
		$sms_supplier = 'dream';
		$db = core::db()->getConnect('SMS');
		$sql = sprintf("insert into tbl_log_qunfa set send_time='%s',mobiles='%s',`content`='%s',reponse_data='%s',sms_supplier='%s',report_id='%s',send_status='%s'",$send_time,$mobiles,$content,$response,$sms_supplier,$report_id,$send_status);
		$db->query($sql);
	}

	public function deal_response($response){
		
		$statusCode = $response['MongateCsSpSendSmsNewResult'];
		if(strlen($statusCode) >= 10 && abs(intval($statusCode)) > 999){
			$report_id = $statusCode;
			$send_status    = '发送成功';
		}else{
			$send_status    = '发送失败';
			if(isset($this->dream_status[$statusCode])){
				$send_status .= '-'.$this->dream_status[$statusCode];
			}
		}
		return Array('report_id'=>$report_id,'send_status'=>$send_status);
	}

 }
?>