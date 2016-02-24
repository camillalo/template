<?php
class curl {
	function send($url,$headers=array(),$post_data='',$crt_path='',$return_with_header=1){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, (bool)$return_with_header);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
//		curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
//		curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
		
		if(!empty($headers)){
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		}
		if(!empty($post_data)){
			curl_setopt($ch, CURLOPT_POST, TRUE);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		}
		if($crt_path){
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
			curl_setopt($ch, CURLOPT_CAINFO, $crt_path);
		}else{
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		}
		$response = curl_exec($ch);
		$errmsg = curl_error($ch);
		/*var_dump($response);
		if(''!=$errmsg){
			return 'curl error: '.$errmsg;
		}*/

		//return $response;
		$pos = strpos($response,"\r\n\r\n");
		return array(
			'code'=>curl_getinfo($ch,CURLINFO_HTTP_CODE),
			'header'=>substr($response,0,$pos),
			'body'=>substr($response,$pos+4),
			'error'=>$errmsg
		);
		//curl_close($ch);
	}
/*	function getheader($response){
		$pos = strpos($response,"\r\n\r\n");
		return substr($response,0,$pos);
	}
	function getbody($response){
		$pos = strpos($response,"\r\n\r\n");
		return substr($response,$pos+4);
	}*/
}