<?php
/**
 * api统一入口
 *
 * @author $whh 185627321@qq.com
 */

class api {
	
	/**
	 * 当前时间
	 */
	protected $now = '';
	
	/**
	 * 服务名称
	 */
	protected $service_name = '';
	
	/**
	 * 合作账号
	 */
	protected $partner_name = '';
	
	/**
	 * ip
	 */
	protected $ip = '';
	
	/**
	 * $token
	 */
	protected $token = '';
	
	//已登陆用户的id
	public $comm_user_infor = null;
	public $comm_wduser_infor = null;
	
	/**
	 * dts请求入口
	 * @param 请求体 $request 格式为xml
	 */
	public function exec($request,$token) {
		
		//$request = stripcslashes($request);

		//file_put_contents('/tmp/x1',date('m-d H:i:s')." ".print_r($request,true)."\n",FILE_APPEND);
		//初始化 json格式
		$this->init($request, $token);
		
		//分解request xml 为数组
		$request_arr  = $this->decompose($request);

		$type = $request_arr['type'];
		//记录请求日志
		$log_id = $this->write_log($request);
		
		//账号验证 + token码检验
		$check_result = $this->check($request);
		unset($request);
		/**
		 * code msg data
		 */
		if($check_result['code']==0){
			
			//获取具体处理对象
			core::Singleton('api.api_class');
			$obj              = api_class::getInstance(str_replace('.','_',$this->service_name));
			
			if(!$obj){
				$check_result['code'] = 4;
				$check_result['msg']  = '接口不存在';
			}else{
				$obj->request_arr = $request_arr;
				$obj->comm_user_infor = $this->comm_user_infor;
				$obj->comm_wduser_infor = $this->comm_wduser_infor;
				$application_result = $obj->run();//获取处理结果 code ,msg , data
				unset($obj);
			}
			unset($request_arr);

			if($application_result['code']!=0){//成功的情况
				$check_result['code'] = $application_result['code'];
				$check_result['msg']  = $application_result['msg'];
			}
			$check_result['data'] = $application_result['data'];
			unset($application_result);
		}
		//file_put_contents('/tmp/debug',date('m-d H:i:s')." ".print_r($check_result,true)."\n",FILE_APPEND);
		//格式化响应数据
		$response = $this->format($check_result,$type);
		unset($check_result);
		
		//记录响应
		$this->update_log($log_id,$response);
		
		return $response;
	}
	
	/**
	 * 进行处理前做一些初始化工作
	 * 1. 初始化时间
	 * 
	 */
	public function init($request,$token) {		
		$this->now =  date('Y-m-d H:i:s');
		$this->token = $token;//如 test
	}
	
	/**
	 * 检查
	 *
	 */
	public function check($request) {
		$api_check = core::Singleton('api.api_check');
		$check_result = $api_check->get_result($this->partner_name,$this->service_name,$this->token,$request);
		$this->comm_user_infor = $api_check->comm_user_infor;//登陆用户id
		$this->comm_wduser_infor = $api_check->comm_wduser_infor;//登陆用户id
		unset($api_check);
		return $check_result;
	}
	
	/**
	 * 解析request xml
	 * @param unknown_type $request
	 * @return Array
	 */
	public function &decompose($request) {
		
		$request_arr  = @json_decode($request,true);
		$service_name = @$request_arr['sname'];
		$partner_name = @$request_arr['pname'];		
		$ip			  = @$request_arr['ip'];		
		//unset($request_arr['sname'],$request_arr['pname'],$request_arr['ver'],,$request_arr['ip']);		
		$this->service_name   = $service_name;//如 user_login
		$this->partner_name   = $partner_name;//如 test
		$this->ip   	      = $ip;//如 test
		return $request_arr;
	}
	
	/**
	 * 记录日志
	 * @param unknown_type $act：request or response 
	 * @param string $data: request xml or response xml
	 * @param unknown_type $message_id
	 */
	public function write_log(&$request_data) {
		
		$data['ip'] 		  = $this->ip;
		$data['partner_name'] = $this->partner_name;
		$data['service_name'] = $this->service_name;
		$data['request_time'] = $this->now;
		$data['request_data'] = $request_data;
		core::Singleton('api.api_log');
		return api_log::write($data);
	}
	
	/**
	 * 更新日志
	 * @param unknown_type $log_id
	 * @param unknown_type $response_data
	 */
	public function update_log($log_id,$response_data){
		$request_md5 = '';
		if(!empty($this->comm_wduser_infor['id'])) {
			$request_md5 = $this->comm_wduser_infor;
		}elseif(!empty($this->comm_user_infor['id'])) {
			$request_md5 = $this->comm_user_infor;
		}
		
		core::Singleton('api.api_log');
		api_log::update($log_id,$response_data,print_r($request_md5,true));
	}
	
	/**
	 * 格式化响应信息
	 * @param Array $response
	 */
	public function &format($response,$format) {
		switch($format) {
			case "1":
				return json_encode($response);
			break;
			case "2":
				$reponse_prefix = '<?xml version="1.0" encoding="utf-8"?>';
				$reponse_str  = core::Singleton('comm.helper.xml')->array2xml($response);
				return $reponse_prefix.$reponse_str;
			break;			
			default:
				return json_encode($response);
			break;
				
		}
	}
}


/*
public function send_post($url, $post_data,$cookie)
{
	$options = array(
			'http' => array(
					'Method' => 'POST',
					'ContentType'=>'application/x-www-form-urlencoded'.$cookie,
					'content' => $post_data,
					'timeout' => 15 * 60 // 超时时间（单位:s）
			)
	);

	$context = stream_context_create($options);
	$result = file_get_contents($url, false, $context);
	return $result;
}
*/

?>