<?php

class api_check {
	
	//需要检查登陆状态的服务
	public $need_check_services = array();
	
	//已登陆用户的id
	public $comm_user_infor = null;
	public $comm_wduser_infor = null;
	
	public $result = array('code'=>0,'msg'=>'调用成功','data'=>'');
	
	public function __construct()
	{
		$this->need_check_services = include("config/config.check.php");
	}
	
	public function get_result($partner_name, $service_name, $token, $request) {
		$code = 0;
		if(empty($partner_name) || empty($service_name) ) {
			$code = 1; //无权限
		}elseif(empty($token) || strlen($token)!=32 ) {
			
			$code = 2; //token码错误
		}
		
		if(!$code){
			$account_infor = $this->get_privilege($partner_name);
			if(!$account_infor){
				$code = 1; //账号不存在
			}
		}
		
		if(!$code){
			$is_availed = $account_infor['is_availed'];
			$privileges = trim($account_infor['privileges']);
			$secret_key = trim($account_infor['secret_key']);
			$need_check = trim($account_infor['need_check']);

			if($need_check=='1') {//需要检查账号
				
				if(!$code  && $is_availed =='0' ) {
					
					$code = 3;//授权账号未启用状态
				}
				if(!$code &&  $privileges !='*') {
					$privilege_arr = @explode(',',$privileges);
					if(!in_array($service_name,$privilege_arr)) {//没权限
						$code = 1; //无权限
					}
				}
				
				if(!$code ) {
					$md5_string = md5($request.$secret_key);
					if($token != $md5_string ) {
						$code = 2;
					}
				}
				
			}
		}
		
		//登陆验证的服务 
		if(!$code){
			$need_login = in_array($service_name,$this->need_check_services) ? true : false ;
			$code = $this->check_login($need_login);
		}
		
		
		if($code) {
			$msg  = $this->get_msg($code);
			$result['code'] = $code;
			$result['msg']  = $msg;
			$result['data']  = (object)array();;
			$this->result   = $result;
		}
		unset($code);
		return $this->result;
	}
	
	public  function get_msg($code){
		$msg[0] = '接口调用成功';
		$msg[1] = '无权限调用接口';
		$msg[2] = 'token码错误';
		$msg[3] = '授权账号已关闭';
		$msg[4] = '接口不存在';
		$msg[5] = '账号未登陆';
		$msg[6] = '账号登陆已失效';
                $msg[7] = ''; //流程中错误，具体信息看流程中返回的错误信息（错误提示信息可能是动态的，因此不完全调用这里的固定信息）
                $msg[8] = '已有相同内容的模板存在';
                
		$msg[9999] = '参数错误';
		//用户登录信息50-
                $msg[50] = '验证码不能为空';
                $msg[51] = '手机号不合法';
                $msg[52] = '手机号已经注册';
                $msg[53] = '验证码不正确';
                $msg[54] = '注册失败';
                $msg[55] = '用户名或密码为空';
                $msg[56] = '用户名或密码错误';
                $msg[57] = '填写的推荐人不存在';
                $msg[58] = '密码不能小于6位';
                $msg[59] = '手机号未注册';
		//用户类 100 -- 199
		$msg[100] = 'c端账号或密码错误';
		$msg[101] = 'c端账号未激活';
		$msg[102] = '账号不存在';
		$msg[103] = 'c端账号登陆已失效';
                $msg[104] = '您的金额小于投标的金额';
                $msg[105] = '自己不能投自己';
                $msg[106] = '投的金额小于最小投标金额 或者标已经满了';
                $msg[107] = '标id不存在';
                $msg[108] = '投标金额大于了最大投资金额';
                $msg[109] = '不是首次投资，不能投新手标';
                $msg[110] = '投资金额必须是最小投资金额的整数倍';
                $msg[111] = '已经绑定过银行卡了';
                $msg[112] = '提现金额不能超过账号余额';
                $msg[113] = '该账号暂时禁止提现，详情请联系财来网';
                $msg[114] = '用户不存在';
                $msg[115] = '领取券不足';
                $msg[116] = '重复领取';
                $msg[117] = '请勿领取自己的飞单';
                $msg[118] = '实名认证失败';
                $msg[119] = '已通过实名认证';
		
		//订单类 200 -299
		$msg[201] = '请重新登录';
		$msg[202] = '缺少参数';
		$msg[203] = '字数不足';
		$msg[204] = '失败';
		$msg[205] = '订单不存在';
		$msg[206] = '修改次数不足';
		$msg[207] = '不能修改已完成的订单';
		//邮件类 300 - 399
		$msg[300] = '邮件服务器连接失败';
		$msg[301] = '邮件发送失败';
		$msg[302] = '邮件格式错误';
		$msg[303] = '邮件账号登录失败';
		//地图路线类400 - 499
		$msg[400] = '经纬度错误';
		
		
		//快递员员类 500 - 599

		//反馈类 600 - 699
		
		
		//快递员登陆类1000-1100
		$msg[1100] = 's端账号或密码错误';
		$msg[1101] = 's端账号未激活';
		$msg[1102] = 's端账号不存在';
		$msg[1103] = 's端账号登陆已失效';
		return isset($msg[$code]) ? $msg[$code] :$code.'未定义';
	}
	
	
	public function get_privilege($account_name){
		
		$accounts = include("config/config.interface.php");
		
		if(in_array($account_name,array_keys($accounts))){
			return $accounts[$account_name];
		}else{
			$db = core::db()->getConnect('DTS');
			$account_name = addslashes($account_name);
			$sql = sprintf("SELECT * FROM %s WHERE partner_name='%s'  LIMIT 1",'dts_account',$account_name);
			return $db->query($sql,'array');
		}
		
	}
	
	public function check_login($need_login=1) {
		
		$session_id = $_COOKIE['session_id'];
//		file_put_contents('/tmp/d','express_get:'.date('H:i:s').': '.print_r($_COOKIE,true)."\n",FILE_APPEND);//可以直接在服务器上查看文件日志
		
		if(empty($session_id) || strlen($session_id)<33){
			$code = 5; //账号未登陆
			//客户端多次set cookie 。 而且名字一样，会产生问题，如  c93faf2dd60beaadc3c56ce090c414ef1, session_id=c93faf2dd60beaadc3c56ce090c414ef1, session_id=c93faf2dd60beaadc3c56ce090c414ef1, session_id=c93faf2dd60beaadc3c56ce090c414ef1
			$session_id = substr($session_id,-33);
		}
		if(!$code){
			
			$real_session_id = substr($session_id,1,32);
			$user = core::Singleton('user.dts_user');//登陆状态检查
			$d  = $user->get_login_uid($real_session_id);
			file_put_contents('/tmp/d','real_session_id:'.date('H:i:s').': '.print_r($real_session_id,true)."\n",FILE_APPEND);//可以直接在服务器上查看文件日志
			file_put_contents('/tmp/d','d:'.date('H:i:s').': '.print_r($d,true)."\n",FILE_APPEND);//可以直接在服务器上查看文件日志
			if($d) {//已登陆
				$this->comm_user_infor['id'] = $d['id'];
				$this->comm_user_infor['mobile'] = $d['mobile'];
				unset($d);
			}else{
				$code = 103; //账号登陆已失效
			}
			unset($user);
			
		}
		
		//非强制需要登陆的情况下
		if(empty($need_login)){
			$code = 0;
		}
		
		return $code ;
	}
	
	
}
?>