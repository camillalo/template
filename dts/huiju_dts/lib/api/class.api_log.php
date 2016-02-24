<?php
/**
 * 接口日志
 */

class api_log {
	
	//日志表
	public static $log_tbl = 'dts_message';
	
	//合法的字段
	public static $avail_fields  = array('ip','partner_name','service_name','request_time','request_data','request_md5','response_time','response_data');
		
	//写日志
	public static function write(&$data) {
		//file_put_contents('./debug',date('m-d H:i:s')." COOKIE".print_r($data,true)."\n",FILE_APPEND);
		
		if(is_array($data)) {
			if(isset($data['pname'])) {
				$data['partner_name'] = $data['pname'];unset($data['pname']);
			}
			if(isset($data['sname'])) {
				$data['service_name'] = $data['sname'];unset($data['sname']);
			}
		}
		
		if(!self::need_save($data)){
			return ;
		}

		if(is_array($data)) {
			$data['ip'] = isset($data['ip']) ? $data['ip'] : self::get_ip();
			foreach($data as $field => $value) {
				if(in_array($field,self::$avail_fields)) {
					if(get_magic_quotes_gpc()){
						$condition[] = "`".$field."` = '".$value."'";
					}else{
						$condition[] = "`".$field."` = '".addslashes($value)."'";
					}
					
				}
			}
		}
		unset($data);
		if(is_array($condition)) {
			$db = core::db()->getConnect('DTS');
			$sql = "INSERT INTO ".self::$log_tbl." SET ".join(',',$condition);
			$db->query($sql);
			return $db->insertId();
		}
		return NULL;
	}
	
	/**
	 * 更新响应日志
	 * @param unknown_type $log_id
	 * @param unknown_type $response_data
	 */
	public static function update($log_id,$response_data,$wdu_id=''){
	
		$db = core::db()->getConnect('DTS');
		$response_time = date('Y-m-d H:i:s');
		if(get_magic_quotes_gpc()){
			$sql = sprintf("update %s set request_md5='%s', response_time='%s',response_data='%s' where message_id='%d' limit 1",self::$log_tbl,$wdu_id,$response_time,$response_data,$log_id);
		}else{
			$sql = sprintf("update %s set request_md5='%s',  response_time='%s',response_data='%s' where message_id='%d' limit 1",self::$log_tbl,$wdu_id,$response_time,addslashes($response_data),$log_id);
		}
		$db->query($sql);
	}
	
	/**
	 * ip记录
	 * @return Ambigous <string, unknown>
	 */
	public static function get_ip(){
		
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
			$ip = "";
		}
		return $ip;
	}

	public static function need_save($data){

		return true;
		
		return ($data['service_name'] == 'multinfo' || $data['service_name'] == 'shop.match'  || $data['service_name'] == 'data.process' || $data['service_name'] == 'push.channel' || $data['service_name'] == 'push.express' || $data['service_name'] == 'liuyan.add' || $data['service_name'] == 'push.allno' ) ? true : false ;
	}
	
	
}
?>