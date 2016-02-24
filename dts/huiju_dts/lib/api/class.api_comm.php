<?php
class api_comm  {
	
	/**
	 * request
	 * 
	 */
	public  $request_arr = array();
	
	public  $result      = array('code'=>0,'msg'=>'接口调用成功','data'=>'');
	
	public $comm_user_infor = null;
	public $comm_wduser_infor = null;
	
	/**
	 * response 数组信息
	 * @var unknown_type
	 */
	public $response = array();

	public $ip = '';
	
	public function run() {
		core::Singleton('comm.remote.remote');
		$this->prepare();
		$response_data    = $this->get_response();
		$is_new_interface = $this->is_new_interface($response_data);
		
		if($is_new_interface == true) {
			$response_data2 = json_decode($response_data,true);
			$this->result['code'] = $response_data2['code'];
			$this->result['msg']  = $response_data2['msg'];
			$this->result['data'] = $response_data2['data'];
			unset($response_data2);
		}else{
			if($this->result['code']){
				$msg = core::Singleton('api.api_check')->get_msg($this->result['code']);
				if($msg){
					$this->result['msg'] = $msg;	
				}				
			}
			$this->result['data'] =$response_data;
		}
		unset($response_data);
		return $this->result;
	}
	
	public function __set($name, $value) {
		
		$this->name = $value;
	}
	
	public function __get($name) {
	
		return $this->name;
	}
	
	public function prepare() {
	}
	

	public  function get_ip(){
		
		if (isset($HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"])){
			$ip = $HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"];
		}elseif (isset($HTTP_SERVER_VARS["HTTP_CLIENT_IP"])){
			$ip = $HTTP_SERVER_VARS["HTTP_CLIENT_IP"];
		}elseif (isset($HTTP_SERVER_VARS["REMOTE_ADDR"])){
			$ip = $HTTP_SERVER_VARS["REMOTE_ADDR"];
		}elseif (getenv("HTTP_X_FORWARDED_FOR")){
			$ip = getenv("HTTP_X_FORWARDED_FOR");
		}elseif (getenv("HTTP_CLIENT_IP")){
			$ip = getenv("HTTP_CLIENT_IP");
		}elseif (getenv("REMOTE_ADDR")) {
			$ip = getenv("REMOTE_ADDR");
		}else {
			$ip = "Unknown";
		}
		return $ip;
	}
	
	public  function get_msg($code){
	
		$msg[0] = '接口调用成功';
		$msg[9999] = '参数错误';
		return isset($msg[$code]) ? $msg[$code] :'成功';
	}
	
	/**
	 * 判断是否是新形式的内部接口
	 * 新的接口形式如下： 1. 是个json   2.
	 * @param unknown_type $result
	 * @return boolean
	 */
	public function is_new_interface($result){
		if($result && is_string($result)) {
			$arr = json_decode($result,true);
			if((json_last_error() == JSON_ERROR_NONE)) {//说明是json
				if(isset($arr['code']) || isset($arr['msg']) || isset($arr['data'])) {
					//符合新格式
					return true;
				}
			}
		}
		return false;
	}
	
}

?>