<?php
/**
 *  读取邮件
 */

class read_email extends api_comm  {

	/**
	 * 设置响应的消息体  返回值必须是json
	 * 
	 */
	private $base_url = "";//文件保存路径//D:/wamp/www/file2/
	private $charset = "utf-8";//邮件编码
	private $pop3obj = "";//pop3实例化的对象
	
	public function get_response() {
		header("Content-type:text/html;charset=utf-8");
		require_once (dirname(__FILE__).'/../../comm/email/class.pop3.php');
		$this->base_url = substr(dirname(__FILE__),0,-23)."/fujian/read";	
		
		$email       = $this->request_arr['email']?$this->request_arr['email']:''; //所要读取的邮箱
		$password    = $this->request_arr['password']?$this->request_arr['password']:'';
		$server      = $this->request_arr['server']?$this->request_arr['server']:'';
		$server_port = $this->request_arr['server_port']?$this->request_arr['server_port']:'';
		$ssl_port = $this->request_arr['ssl_port']?$this->request_arr['ssl_port']:'';
		
		if(empty($email)||empty($password)||empty($server)) {
			$this->result['code'] = 9999; 
            $this->result['msg'] = '参数错误';
            return $result = (object)array(); 
		}
		//邮件格式检查
		if(!$this->check_email($email)){
			$this->result['code'] = 302; 
            $this->result['msg'] = '邮件格式错误';
            return $result = (object)array();   
		}
		$this->pop3obj = $pop3obj = new SocketPOP3Client($email, $password, $server, $server_port,$ssl_port);		
		$isLogin = $pop3obj->popLogin();

		if($isLogin){	
			$ml = $pop3obj->getMailSum();//邮件数量、字节
			$mltot = $ml[0];//邮件数量
			
			for($i=0;$i<$mltot;$i++){
				
			 	$content = $pop3obj->getMailMessage(($i+1),1);
				//echo $content;

				//首先查看编码
				
				if(preg_match("/charset=\"?([^\"\s+].*)\"?\s+/isU", $content ,$match)){
					$this->charset = $match[1];					
				}
				//发送人
				$result[$i]['from'] = $email; 
				//邮件id
				$result[$i]['id'] = $i+1;				
				//获取标题	
				if(preg_match("/\s+Subject:\s+([^\s+].*)\s+/isU", $content,$match)){
					$title = trim($match[1]);unset($match);
					$result[$i]['title'] = $this->charset($title);				
				}
				

				//获取发件人及其姓名
				if(preg_match_all("/From:\s+\"?([^\"].*)\"?\s?(<(.*)>)?\s+/isU", $content,$match)){					
					$from_name = trim($match[1][0]);
					$from_name = $this->charset($from_name);
					if($match[3][0]==''){
						$from = $from_name;
						$from_name_arr = explode("@",$from_name);
						$from_name = $from_name_arr[0];
					}else{
						$from = trim($match[3][0]);
					}
					$result[$i]['from'] = $from;
					$result[$i]['from_name'] = $from_name;
					unset($match);
				}

				//获取邮件接收时间
				//Date: Thu, 22 Oct 2015 14:48:41
				if(preg_match("/Date:\s+(.*,\s+\d+\s+[a-zA-Z]+\s+\d+\s+.*)\s+/isU", $content,$match)){
					//$result[$i]['receive_time'] = date('Y-m-d H:i:s',strtotime($match[1]));
					unset($match);					
				}

				
				//获取分割符(Content-Type: multipart/mixed;说明有附件，说明没有附件)
				
	
				if(preg_match("/Content-Type:\s+multipart\/(.*);\s+boundary=\"(.*)\"/isU", $content,$match)){
					$fujian_type = trim($match[1]);
					$flag = "--".trim($match[2]);unset($match);
					
					if($fujian_type=="mixed"){
						//有附件获取内容的分隔符，
						if(preg_match("/Content-Type:\s+multipart\/alternative;\s+boundary=\"(.*)\"/isU", $content,$match)){
							//获取内容
							$content_flag = "--".trim($match[1]);unset($match);
							$contents = explode($content_flag,$content);
							$email_content = $contents[1];
							if(preg_match("/Content-Transfer-Encoding:\s+(.*)\s+([^\s+].*)\s+$/isU", $email_content,$match)){
								$email_content = $match[2];
								if($match[1]=="base64"){
									$email_content = base64_decode($email_content);
								}
								unset($match);
								if($this->charset!="utf-8"){
									$email_content = iconv("gb2312","UTF-8",$email_content);
								}
								$result[$i]['content'] = $email_content;
							}
							//获取附件
							$contents = explode($flag,$content);
							$k = 0;
							for($j=2;$j<count($contents)-1;$j++){
								if(preg_match("/Content-Disposition:\s+.*\s+filename=\"(.*)\"\s+Content-Transfer-Encoding:\s+.*\s+([^\s+].*)$|Content-Transfer-Encoding:\s+.*\s+Content-Disposition:\s+.*\s+filename=\"(.*)\"\s+([^\s+].*)$/isU", $contents[$j],$match)){
									//获取文件名									
									$file_name = $match[1]==""?$match[3]:$match[1];
							 		$result[$i]['fujian'][$k]['file_name'] = $file_name = $this->charset($file_name);
							 		$fujian = $match[2]==""?$match[4]:$match[2];
							 		$file_name = preg_replace("/^.*\.(.*)$/isU",".$1",$file_name);
									$result[$i]['fujian'][$k]['url'] = $file_name = $this->base_url.md5(time()).rand(0,1000).$file_name;
					    			//file_put_contents($file_name,base64_decode($fujian));
				 				}
				 				$k++;
							}
						}
					}else{
						//无附件获取内容
						$contents = explode($flag,$content);
						$email_content = $contents[1];
						if(preg_match("/Content-Transfer-Encoding:\s+(.*)\s+([^\s+].*)\s+$/isU", $email_content,$match)){
							$email_content = $match[2];					
							if($match[1]=="base64"){
								$email_content = base64_decode($email_content);
							}
							unset($match);
							if($this->charset!="utf-8"){
								$email_content = iconv("gb2312","UTF-8",$email_content);
							}
							$result[$i]['content'] = $email_content;
						}else{
							$email_content = $contents[2];
							if(preg_match("/Content-Transfer-Encoding:\s+(.*)\s+([^\s+].*)\s+$/isU", $email_content,$match)){
								$email_content = $match[2];
								if($match[1]=="base64"){
									$email_content = base64_decode($email_content);
								}
								unset($match);
								if($this->charset!="utf-8"){
									$email_content = iconv("gb2312","UTF-8",$email_content);
								}
								$result[$i]['content'] = $email_content;
							}
						}
					}
							
				}else{//没有分隔符的情况(一般内容在最底下)
					if(preg_match("/Content-Type:\s+text\/.*;\s+charset=.*\s+(.*)$/isU", $content,$match)){
						$result[$i]['content'] = trim($match[1]);
						
					}
					
				}			
				//var_dump($result);
				//exit;
			 }	
			 $pop3obj->popLogout();
			 $pop3obj->closeHost();
			
		}else{
			$this->result['code'] = 303; 
            $this->result['msg'] = '邮件账号登录失败';
            return $result = (object)array();   			
		}
		
		return $result;
	}
	
	//字符转码
	private function charset($string){
		$result = $string;
		$result = $this->pop3obj->decode_mime($string);
		if($this->charset!="utf-8"){
			$result = iconv("gb2312","UTF-8",$result);
		}
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