<?php  
	class smtp {
		public $_smtp;
		public $_debug = true;

		function __construct($_smtp_server,$_smtp_server_port){
			$_time_out = 5;
			$this->_smtp = @fsockopen($_smtp_server,$_smtp_server_port,$_error,$_error_string,$_time_out);
			//$this->_smtp = @stream_socket_client("tcp://".$_smtp_server.":".$_smtp_server_port, $_error,  $_error_string,  $_time_out);
			if (empty($this->_smtp)){
				exit("Error: smtp 连接失败 ...");
			}
			//响应结果
			$this->smtp_log(fread($this->_smtp, 515));
			
			if (intval($this->smtp_cmd('EHLO '.$_smtp_server)) != 250){
				exit("Error: 服务器不支持 ...");
			}
		}

		function __destruct(){
			if ($this->_smtp){
				$this->smtp_cmd('QUIT');//退出
			}
		}		

		function login($_smtp_user,$_smtp_password){
			if (empty($this->_smtp)){
				exit();
			}

			$_result = $this->smtp_cmd("AUTH LOGIN");

			$_result = $this->smtp_cmd(base64_encode($_smtp_user));

			$_result = $this->smtp_cmd(base64_encode($_smtp_password));

			if (intval($_result) == 550){
				$_open_file = fopen("smtp_log.txt", "a");
				fwrite($_open_file,date("Y-m-d H:i:s")." ".$_smtp_user." 用户被锁定 ..."."\r\n");
				fclose($_open_file);
				exit();			
			}
		}

		function send($_from,$_from_name,$_to,$_subject,$_body){
			$_header = "";
			$_header .= "MIME-Version:1.0\r\n";
			$_header .= "Content-Type:text/html\r\n";
			$_header .= "To: $_to\r\n";
			$_header .= "From: $_from_name<".$_from.">\r\n";
			$_header .= "Subject: ".$_subject."\r\n"; 

			$_header .= "Date: ".date("r")."\r\n";
			//$_header .= "X-Mailer:By David (PHP/".phpversion().")\r\n";
            list($_value_1, $_value_2) = explode(" ", microtime()); 
            $_header .= "Message-ID: <".date("YmdHis", $_value_1).".".($_value_2*1000000).".".$_from.">\r\n"; 			

			$this->smtp_cmd("MAIL FROM: <".$_from.">");
			$this->smtp_cmd("RCPT TO: <".$_to.">");
			$_result = $this->smtp_cmd("DATA");
			if ($_result != 354){
				exit($_result);
			}

            fputs($this->_smtp, $_header."\r\n".$_body); 
            fputs($this->_smtp, "\r\n.\r\n");
            return "success";
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