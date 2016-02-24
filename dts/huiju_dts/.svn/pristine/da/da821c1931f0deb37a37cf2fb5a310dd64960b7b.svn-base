<?php
/**
 * 发送邮件
 */

class send_email extends api_comm  {

	/**
	 * 设置响应的消息体  返回值必须是json
	 * 
	 */
	public $smtp_user = array(//SMTP服务器的用户
		0=>array("user"=>"liyingxiaoxue01@163.com","pass"=>"q973955"),
		1=>array("user"=>"liyingxiaoxue02@163.com","pass"=>"q973955")
	);
	
 
private function objarray_to_array($obj) {  
    $ret = array();  
    foreach ($obj as $key => $value) {  
    if (gettype($value) == "array" || gettype($value) == "object"){  
            $ret[$key] =  objarray_to_array($value);  
    }else{  
        $ret[$key] = $value;  
    }  
    }  
    return $ret;  
}  
	
	public function get_response() {
		header('Content-Type:text/html;charset=UTF-8');
		ini_set('date.timezone','PRC'); //时区设置
		require_once (dirname(__FILE__).'/../../comm/email/class.smtp.php');		
			

		//获取邮件待发队列
		$email_data = $this->get_email();
		$res = array();
		if(is_array($email_data)){
			foreach($email_data as $k=>$v){
				foreach($this->smtp_user as $i=>$j){
					
					$ssl_port = $v['ssl_port']==0?'':$v['ssl_port'];
					$_smtp_class = new smtp($v['server'],$v['server_port'],$ssl_port);
					$_smtp = $this->objarray_to_array($_smtp_class);
					if($_smtp['_smtp']==false){
						$this->result['code'] = 300; 
			            $this->result['msg'] = '邮件服务器连接失败';
			            return $result = (object)array(); 			
					}
					
					$smtp = $_smtp_class->login($j['user'],$j['pass']);
					
					//邮件流水记录
					$log_id = $this->email_log($v['id'],$j['user']);

					//附件
					if($v['fujian']){
						$fujians = json_decode($v['fujian'],true);

						foreach($fujians as $a=>$b){
							$_smtp_class->addAttachment($b['url'],$b['file_name'],$a);
						}
					}
					
					$result = $_smtp_class->send($j['user'],$v['from_name'],$v['send_to'],$v['title'],$v['content']);
					//邮件流水记录
					$state = $result=='success'?$result:'fail';
					$this->email_log2($log_id,$state);
					if($result=='success'){
						//发送成功进行记录
						$res[] = $v['id'];
						$this->success_email($v['id'],$j['user']);
						break;					
					}			
				}
					
			}						
		}
		
		
		/*
		if($result!="success"){
			$this->result['code'] = 301; 
            $this->result['msg'] = '邮件发送失败';
            return $result = (object)array(); 			
		}
		*/
		
		return $res;
	}
	
	//获取邮件待发队列
	private function get_email(){
         $comm = core::Singleton('user.comm');
		 $result = $comm->get_email();unset($comm);
         return $result;
	}
	
	//邮件流水记录before
	private function email_log($email_id,$from){
         $comm = core::Singleton('user.comm');
		 $result = $comm->email_log($email_id,$from);unset($comm);
		 return $result;
	}
	
	//邮件流水记录after
	private function email_log2($log_id,$state){
         $comm = core::Singleton('user.comm');
		 $result = $comm->email_log2($log_id,$state);unset($comm);
	}
	
	//发送成功进行记录
	private function success_email($id,$from){
         $comm = core::Singleton('user.comm');
		 $comm->success_email($id,$from);unset($comm);
	}
	



}

?>