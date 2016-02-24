<?php 

!defined('NOW') && define('NOW', time());
!defined('IP') && define('IP', isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1');
!defined('SALTKEY') && define('SALTKEY', '9527');

require_once(dirname(__FILE__).'/class.Cookie.php');

class commSession extends Cookie {
	
	private static function _create_mark($string,$start,$end){
		//return md5($string . $start . $end . IP . SALTKEY);//如果客户端的ip是变化的，这行会导致客户端cookie经常无故失效
		return md5($string . $start . $end . SALTKEY);//这虽然解决了上面的问题，但却使得cookie可使用于别的机器，当expires=0时，安全性不可保证
	}
	
	private static function _add_mark($string,$expires){
		$end = NOW + $expires;
		return self::_create_mark($string,NOW,$end) . NOW . $end . $string;
	}
	
	private static function _remove_mark($string){
		if(!isset($string{53})){
			return null;
		}
		$mark=substr($string,0,32);
		$start=substr($string,32,10);
		$end=substr($string,42,10);
		$value=substr($string,52);
		$_mark=self::_create_mark($value,$start,$end);
		if($mark==$_mark&&($start==$end||(NOW>=$start&&NOW<=$end))){
			return $value;
		}
		return null;
	}
	
	public static function get($key){
		return self::_remove_mark(parent::get($key));
	}
	
	public static function set($key,$value,$expires=0,$path='/',$domain=false){
 		return parent::set($key,self::_add_mark($value,$expires),$expires,$path,$domain);
	}
	
}


?>