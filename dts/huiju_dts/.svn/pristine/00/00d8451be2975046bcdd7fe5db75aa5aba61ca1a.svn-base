<?php
/*
 *
使用方法:
//HTTPSQS_HOST 和 HTTPSQS_PORT 定义在lib/config/config.comm.php，并且自动引入了

$host = HTTPSQS_HOST;									//ip 
$port = HTTPSQS_PORT;									//端口
$httpsqs = core::Singleton('comm.helper.httpsqs');		//创建对象
$httpsqs->init($host,$port);							//初始化
$queue_name = 'queue1';									//队列名称
$data = array('key'=>array(1,'我是汉字'),2222);			//内容，可以为字符串，数组，对象

$httpsqs->put($queue_name,$data);						//插入队列
$httpsqs->get($queue_name);								//获取队列中头条信息
$httpsqs->status($queue_name);							//队列状态
$httpsqs->view($queue_name,80);							//获取指定位置的信息
$httpsqs->gets($queue_name);							//获取队列中头条信息(包括位置)
$httpsqs->reset($queue_name);							//重设队列

----------------------------------------------------------------------------------------------------------------
*/
class httpsqs {
	
	public $httpsqs_host;
	public $httpsqs_port;
	public $httpsqs_auth;
	public $httpsqs_charset;

	public function init($host, $port=1218, $auth='', $charset='utf-8') {
		$this->httpsqs_host = $host;
		$this->httpsqs_port = $port;
		$this->httpsqs_auth = $auth;
		$this->httpsqs_charset = $charset;
	}
    public function http_get($query) {
		$host = $this->httpsqs_host;

        $socket = fsockopen($this->httpsqs_host, $this->httpsqs_port, $errno, $errstr, 5);
        if (!$socket) {
            return false;
        }
        $out = "GET ${query} HTTP/1.1\r\n";
        $out .= "Host: ${host}\r\n";
        $out .= "Connection: close\r\n";
        $out .= "\r\n";
        fwrite($socket, $out);
        $line = trim(fgets($socket));
        $header .= $line;
        list($proto, $rcode, $result) = explode(" ", $line);
        $len = -1;
        while (($line = trim(fgets($socket))) != "")  {
            $header .= $line;
            if (strstr($line, "Content-Length:")) {
                list($cl, $len) = explode(" ", $line);
 
            }
            if (strstr($line, "Pos:")) {
                list($pos_key, $pos_value) = explode(" ", $line);
            }                  
            if (strstr($line, "Connection: close")) {
                $close = true;
            }
        }
        if ($len < 0) {
            return false;
        }
       
        $body = fread($socket, $len);
        $fread_times = 0;
        while(strlen($body) < $len){
                $body1 = fread($socket, $len);
                $body .= $body1;
                unset($body1);
                if ($fread_times > 100) {
                        break;
                }
                $fread_times++;
        }
        fclose($socket);
        $result_array["pos"] = (int)$pos_value;
        $result_array["data"] = json_decode($body,true);
        return $result_array;
    }

    public function http_post($query, $body){
		$host = $this->httpsqs_host;

        $socket = fsockopen($this->httpsqs_host, $this->httpsqs_port, $errno, $errstr, 1);
        if (!$socket) {
            return false;
        }
        $out = "POST ${query} HTTP/1.1\r\n";
        $out .= "Host: ${host}\r\n";
        $out .= "Content-Length: " . strlen($body) . "\r\n";
        $out .= "Connection: close\r\n";
        $out .= "\r\n";
        $out .= $body;
        fwrite($socket, $out);
        $line = trim(fgets($socket));
        $header .= $line;
        list($proto, $rcode, $result) = explode(" ", $line);
        $len = -1;
        while (($line = trim(fgets($socket))) != "") {
            $header .= $line;
            if (strstr($line, "Content-Length:")) {
                list($cl, $len) = explode(" ", $line);
            }
            if (strstr($line, "Pos:")) {
                list($pos_key, $pos_value) = explode(" ", $line);
            }                  
            if (strstr($line, "Connection: close")) {
                $close = true;
            }
        }
        if ($len < 0) {
            return false;
        }
        $body = @fread($socket, $len);
        fclose($socket);
        $result_array["pos"] = (int)$pos_value;
        $result_array["data"] = $body;
        return $result_array;
    }
       
    public function put($queue_name, $queue_data) {
		$queue_data = json_encode($queue_data);
        $result = $this->http_post("/?auth=".$this->httpsqs_auth."&charset=".$this->httpsqs_charset."&name=".$queue_name."&opt=put", $queue_data);
        if ($result["data"] == "HTTPSQS_PUT_OK") {
            return true;
        } else if ($result["data"] == "HTTPSQS_PUT_END") {
            return $result["data"];
        }
        return false;
    }
   
    public function get($queue_name) {
        $result = $this->http_get("/?auth=".$this->httpsqs_auth."&charset=".$this->httpsqs_charset."&name=".$queue_name."&opt=get");
        if ($result == false || $result["data"] == "HTTPSQS_ERROR" || $result["data"] == false) {
            return false;
        }
        return $result["data"];
    }
       
    public function gets($queue_name) {
        $result = $this->http_get("/?auth=".$this->httpsqs_auth."&charset=".$this->httpsqs_charset."&name=".$queue_name."&opt=get");
        if ($result == false || $result["data"] == "HTTPSQS_ERROR" || $result["data"] == false) {
           return false;
        }
        return $result;
    }  
       
    public function status($queue_name){
		$url = 'http://'.$this->httpsqs_host.':'.$this->httpsqs_port."/?auth=".$this->httpsqs_auth."&charset=".$this->httpsqs_charset."&name=".$queue_name."&opt=status";
        $content = file_get_contents($url);

		$pattern ="/Queue Service([^\n]*)\n/u";
		preg_match_all($pattern, $content, $matches);
		$result['version'] =  trim($matches[1][0]);
		unset($pattern,$matches);

		$pattern ="/Maximum number of queues:([^\n]*)\n/u";
		preg_match_all($pattern, $content, $matches);
		$result['maximum_number'] =  trim($matches[1][0]);
		unset($pattern,$matches);

		$pattern ="/Put position of queue \(1st lap\):([^\n]*)\n/u";
		preg_match_all($pattern, $content, $matches);
		$result['put_position'] =  trim($matches[1][0]);
		unset($pattern,$matches);

		$pattern ="/Get position of queue \(1st lap\):([^\n]*)\n/u";
		preg_match_all($pattern, $content, $matches);
		$result['get_position'] =  trim($matches[1][0]);
		unset($pattern,$matches);
		
		$pattern ="/Number of unread queue:([^\n]*)\n/u";
		preg_match_all($pattern, $content, $matches);
		$result['unread'] =  trim($matches[1][0]);
		unset($pattern,$matches);
		
		return $result;
	
    }
       
    public function view($queue_name, $queue_pos) {
		$url = 'http://'.$this->httpsqs_host.':'.$this->httpsqs_port."/?auth=".$this->httpsqs_auth."&charset=".$this->httpsqs_charset."&name=".$queue_name."&opt=view&pos=".$queue_pos;
        $content = file_get_contents($url);
		return json_decode($content,true);
    }
       
    public function reset($queue_name) {
        $result = $this->http_get("/?auth=".$this->httpsqs_auth."&charset=".$this->httpsqs_charset."&name=".$queue_name."&opt=reset");
        if ($result["data"] == "HTTPSQS_RESET_OK") {
          return true;
        }
        return false;
    }

    public function synctime($num) {
        $result = $this->http_get("/?auth=".$this->httpsqs_auth."&charset=".$this->httpsqs_charset."&name=httpsqs_synctime&opt=synctime&num=".$num);
        if ($result["data"] == "HTTPSQS_SYNCTIME_OK") {
            return true;
        }
        return false;
    }
}


?>