<?php  
	class smtp {
		public $_smtp;
		public $_debug = true;
		/**
		* @var string 附件
		* @access protected
		*/
		public $_attachment;

		function __construct($_smtp_server,$_smtp_server_port='',$_smtp_server_ssl_port=''){
			$_time_out = 5;
			if($_smtp_server_ssl_port !=""){ 
				$this->_smtp = @fsockopen("ssl://".$_smtp_server,$_smtp_server_ssl_port,$_error,$_error_string,$_time_out);
			}else{ 
				$this->_smtp = @fsockopen($_smtp_server,$_smtp_server_port,$_error,$_error_string,$_time_out);
			}
			
			if (empty($this->_smtp)){
				return "Error: smtp 连接失败 ...";
			}
			//响应结果
			$this->smtp_log(fread($this->_smtp, 515));
			
			if (intval($this->smtp_cmd('EHLO '.$_smtp_server)) != 250){
				return "Error: 服务器不支持 ...";
			}
		}

		function __destruct(){
			if ($this->_smtp){
				$this->smtp_cmd('QUIT');//退出
			}
		}		

		function login($_smtp_user,$_smtp_password){
			if (empty($this->_smtp)){
				return false;	
			}

			$_result = $this->smtp_cmd("AUTH LOGIN");

			$_result = $this->smtp_cmd(base64_encode($_smtp_user));

			$_result = $this->smtp_cmd(base64_encode($_smtp_password));

			if (intval($_result) == 550){
				$_open_file = fopen("smtp_log.txt", "a");
				fwrite($_open_file,date("Y-m-d H:i:s")." ".$_smtp_user." 用户被锁定 ..."."\r\n");
				fclose($_open_file);
				return false;	
			}
			return true;
		}
		
		/**
		* 设置邮件附件，多个附件，调用多次
		* @access public
		* @param string $file 文件地址
		* @return boolean
		*/
		function addAttachment($file,$file_name,$i) {
			if(!file_exists($file)) {
				$this->_errorMessage = "file " . $file . " does not exist.";
				return false;
			}

			if(isset($this->_attachment)) {				
				if(is_string($this->_attachment)) {
					$this->_attachment = array($this->_attachment);
					$this->_attachment[$i]['url'] = $file;
					$this->_attachment[$i]['file_name'] = $file_name;
					return true;
				}elseif(is_array($this->_attachment)) {
					$this->_attachment[$i]['url'] = $file;
					$this->_attachment[$i]['file_name'] = $file_name;
					return true;
				}else {
					return false;
				}
			}else {
				$this->_attachment[$i]['url'] = $file;
				$this->_attachment[$i]['file_name'] = $file_name;
				return true;
			}
		}

		function send($_from,$_from_name,$_to,$_subject,$_body){
			$separator = "----=_Part_" . md5($_from . time()) . uniqid(); //分隔符
			$_header = "";
			
			
			
			$_header .= "To: $_to\r\n";
			$_header .= "From: $_from_name<".$_from.">\r\n";			
			$_header .= "Subject: ".$_subject."\r\n";
			$_header .= "MIME-Version: 1.0\r\n";
			if(isset($this->_attachment)) {
				//含有附件的邮件头需要声明成这个
				$_header .= "Content-Type: multipart/mixed;\r\n";
			}else{
				$_header .= "Content-Type:text/html\r\n";
			}
			//邮件头分隔符
			if(isset($this->_attachment)) {
				$_header .= "\t" . 'boundary="' . $separator . '"';			
				$_header .= "\r\n--" . $separator . "\r\n";
				$_header .= "Content-Type:text/html; charset=utf-8\r\n";
				$_header .= "Content-Transfer-Encoding: base64\r\n\r\n";
				$_header .= base64_encode($_body) . "\r\n";
				$_header .= "--" . $separator . "\r\n";
			}
			
			
			


			//加入附件
			if(isset($this->_attachment) && !empty($this->_attachment)){
				if(is_array($this->_attachment)){
					$count = count($this->_attachment);
					for($i=0; $i<$count; $i++){
						$_header .= "--" . $separator . "\r\n";
						$_header .= "Content-Type: " . $this->getMIMEType($this->_attachment[$i]['url']) . '; name="' . basename($this->_attachment[$i]['file_name']) . '"' . "\r\n";
						$_header .= "Content-Transfer-Encoding: base64\r\n";
						$_header .= 'Content-Disposition: attachment; filename="' . basename($this->_attachment[$i]['file_name']) . '"' . "\r\n";
						$_header .= "\r\n";
						$_header .= $this->readFile($this->_attachment[$i]['url']);
						$_header .= "--" . $separator . "\r\n";
					}
				}
			}
			$_header .= "Content-Type: text/html; charset=utf-8\r\n";
			$this->smtp_cmd("MAIL FROM: <".$_from.">");
			$this->smtp_cmd("RCPT TO: <".$_to.">");
			$_result = $this->smtp_cmd("DATA");
			if ($_result != 354){
				return $_result;
			}

			//echo $_header;
            fputs($this->_smtp, $_header."\r\n".$_body); 
            fputs($this->_smtp, "\r\n.\r\n");
            return "success";
		}
		
		/**
		* 获取附件MIME类型
		* @access protected
		* @param string $file 文件
		* @return mixed
		*/
		function getMIMEType($file) {
			if(file_exists($file)) {
				$mime = mime_content_type($file);
				if(! preg_match("/gif|jpg|png|jpeg/", $mime)){
					$mime = "application/octet-stream";
				}
				return $mime;
			}
			else {
				return false;
			}
		}
		
		/**
		* 读取附件文件内容，返回base64编码后的文件内容
		* @access protected
		* @param string $file 文件
		* @return mixed
		*/
		protected function readFile($file) {
			if(file_exists($file)) {
				$file_obj = file_get_contents($file);
				return base64_encode($file_obj);
			}
			else {
				$this->_errorMessage = "file " . $file . " dose not exist";
				return false;
			}
		}

		private function smtp_log($_message){
			if ($this->_debug == true){
				//echo $_message."<br/>"; //显示命令行
			}
		}

		function smtp_cmd($_message){
			//命令
			fputs($this->_smtp,$_message."\r\n");
			$this->smtp_log('命令 '. iconv('GB2312', 'UTF-8', $_message)); 
			
			//响应结果
			$_result = fread($this->_smtp, 515);

			$this->smtp_log(iconv('GB2312', 'UTF-8', $_result));

			return iconv('GB2312', 'UTF-8', $_result);
		}

	}
?> 