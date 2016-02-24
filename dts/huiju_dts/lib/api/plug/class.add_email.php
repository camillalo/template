<?php
/**
 *  将邮件加入发送队列
 */

class add_email extends api_comm  {

	/**
	 * 设置响应的消息体  返回值必须是json
	 * 
	 */
	private $base_url = "";//文件保存路径//D:/wamp/www/file/
	
	public function get_response() {
		$this->base_url = substr(dirname(__FILE__),0,-23)."/fujian/send";
		
		$send_to      = $this->request_arr['send_to']?$this->request_arr['send_to']:'';
		$title      = $this->request_arr['title']?$this->request_arr['title']:'';
		$content      = $this->request_arr['content']?$this->request_arr['content']:'';
		$fujian      = $this->request_arr['fujian']?$this->request_arr['fujian']:'';
		$server      = $this->request_arr['server']?$this->request_arr['server']:'';
		$server_port = $this->request_arr['server_port']?$this->request_arr['server_port']:'';
		$ssl_port = $this->request_arr['ssl_port']?$this->request_arr['ssl_port']:'';
		$from_name = $this->request_arr['from_name']?$this->request_arr['from_name']:$send_to;
		
		if(empty($send_to)||empty($title)||empty($content)||empty($server)||empty($server_port)) {
			$this->result['code'] = 9999; 
            $this->result['msg'] = '参数错误';
            return $result = (object)array(); 
		}
		//保存附件
		$fujian_json = "";
		if(is_array($fujian)){
			$fujian_arr = array();
			foreach($fujian as $k=>$v){
				$file_name = preg_replace("/^.*\.(.*)$/isU",".$1",$v['file_name']);
				$file_name = $this->base_url.md5(time()).rand(0,1000).$file_name;
				file_put_contents($file_name,base64_decode($v['data']));
				$fujian_arr[$k]['file_name'] = $v['file_name'];
				$fujian_arr[$k]['url'] = $file_name;
			}
			$fujian_json = json_encode($fujian_arr);
		}
		
			
		$send_tos = explode(',',$send_to);
		
		foreach($send_tos as $k=>$v){
			if(!$this->check_email($v)){
				$this->result['code'] = 302; 
	            $this->result['msg'] = '邮件格式错误';
	            return $result = (object)array();   
			}
		}
		$id = $this->addemail($title,$content,$fujian_json,$server,$server_port,$ssl_port,$from_name);
		if($id>0){
			foreach($send_tos as $k=>$v){
				$this->add_email_msg($id,$v);
			}
		}else{
			return false;
		}
		
		return true;
	}
	
	//邮件插入队列
	private function addemail($title,$content,$fujian_json,$server,$server_port,$ssl_port,$from_name){
         $comm = core::Singleton('user.comm');
		 $result = $comm->add_email($title,$content,$fujian_json,$server,$server_port,$ssl_port,$from_name);unset($comm);
         return $result;
	}
	
	//插入邮件信息表
	private function add_email_msg($id,$send_to){
         $comm = core::Singleton('user.comm');
		 $result = $comm->add_email_msg($id,$send_to);unset($comm);
         return $result;
	}
	
	
	//邮件格式检查
	private function check_email($send_to){
        $pattern = "/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i";
        if ( !preg_match( $pattern, $send_to ) )
        	return false;        	
        else
        	return true;  
	}
	



}

?>