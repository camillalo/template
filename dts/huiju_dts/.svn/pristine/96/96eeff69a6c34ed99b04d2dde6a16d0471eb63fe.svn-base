<?php

/**
 * 
 *
 */
class Socket {
	
	var $_socket = null;
	var $_errno = '';
	var $_errstr = '';
	var $_buff_size = 4096;
	
	/**
	 * @param host $host
	 * @param port $port
	 * @param timeout $timeout
	 * @return boolean
	 */
	function connect($host, $port = 80, $timeout = 30){
		$_socket_tag = $host.':'.$port;
		
		if (!isset($GLOBALS['CACHE']['SOCKET'][$_socket_tag]) || !is_resource($GLOBALS['CACHE']['SOCKET'][$_socket_tag])) {
			$GLOBALS['CACHE']['SOCKET'][$_socket_tag] = @fsockopen($host, $port, $this->_errno, $this->_errstr, $timeout);
			if (!is_resource($GLOBALS['CACHE']['SOCKET'][$_socket_tag])) {
				return false;
			}
			stream_set_timeout($GLOBALS['CACHE']['SOCKET'][$_socket_tag], $timeout);
		}
		
		$this->_socket = &$GLOBALS['CACHE']['SOCKET'][$_socket_tag];
		
		return is_resource($this->_socket);
	}
	
	/**
	 * @param 被发送的原始消息 $msg
	 * @return false|number
	 */
	function send($msg){
		return fwrite($this->_socket, $msg);
	}
	
	/**
	 * @return false|string
	 */
	function recv(){
		$header = '';
		$body = '';
		
		try {
			while($line = fgets($this->_socket, $this->_buff_size)) {
				$header .= $line;
				if($line == "\r\n") {
					break;
				}
			}
			if (preg_match('/Content-Length:\s*(\d+)/is', $header,$m)){
				$contentlength = intval($m[1]);
				$_read_len = 0;
				while ($_read_len < $contentlength){
					$body .= fread($this->_socket,$contentlength-$_read_len);
					$_read_len = strlen($body);
				}
			}elseif(preg_match('/Transfer-Encoding:\s*chunked/is', $header)) {
				$_chunk_size = intval(hexdec(fgets($this->_socket, $this->_buff_size)));
				while(!feof($this->_socket) && $_chunk_size > 0) {
				    //$body .= fread($this->_socket, $_chunk_size);//这种方式有个bug，当chunk大于缓冲区容量时，读取不完
					$_read_len = 0;
					$_buff = '';
					while ($_read_len < $_chunk_size) {
				    	$_buff .= fread($this->_socket,$_chunk_size-$_read_len);
				    	$_read_len = strlen($_buff);
					}
					$body .= $_buff;
					fread($this->_socket, 2);//skip \r\n
					$_chunk_size = intval(hexdec(fgets($this->_socket, $this->_buff_size)));
				}
				fread($this->_socket, 2);//skip \r\n
			}else{
				while ($buff = fread($this->_socket,$this->_buff_size)) {
					$body .= $buff;
				}
			}
		} catch (Exception $e) {
			$this->_errstr = print_r($e, true);
			return false;
		}
		
		return $header.$body;
	}
	
	function close(){
		if (is_resource($this->_socket)) {
			return fclose($this->_socket);
		}
	}
	
	/**
	 * 
	 * @param 被发送的原始消息 $msg
	 * @param host $host
	 * @param port $port
	 * @param timeout $timeout
	 * @return error string | array('status'=>status,'header'=>header,'body'=>body)
	 */
	function sendhttp($msg, $host, $port = 80, $timeout = 30){
		if(false===$this->connect($host, $port, $timeout)){
			return "err in socket connect [errno:{$this->_errno}, errstr:{$this->_errstr}]";
		}
		if(false===$this->send($msg)){
			return "err in socket send [errno:{$this->_errno}, errstr:{$this->_errstr}]";
		}
		$recv = $this->recv();
		if (false === $recv){
			return "err in socket recv [errno:{$this->_errno}, errstr:{$this->_errstr}]";
		}
		$pos = strpos($recv, "\r\n\r\n");
		if (false===$pos) {
			$this->close();
			return "can not found \\r\\n\\r\\n in recv [$recv]";
		}
		
		$header = substr($recv, 0, $pos);
		$body = strlen($recv) > $pos+4 ? substr($recv,$pos+4) : '';
		
		if (preg_match('/connection:\s*close/is', $header)) {
			$this->close();
		}
		
		$status = substr($header,9,3);
		if (!is_numeric($status)){
			return "recv header status is not numeric [$status]";
		}
		
		return array('code'=>$status,'header'=>$header,'body'=>$body);
	}
}