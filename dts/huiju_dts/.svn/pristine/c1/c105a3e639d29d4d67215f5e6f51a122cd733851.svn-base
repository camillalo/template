<?php 



class Cookie{
	
	public static function set($key,$value,$expires=0,$path='/',$domain=false){
		
		$domain = $domain ? '.' . ltrim($domain, '.') : false;
		
		$expires = (is_numeric($expires) && $expires>0) ? NOW + $expires : 0;
		
		return setcookie($key,$value,$expires,$path,$domain);
		
	}
	
	public static function get($key) {
		
		return isset($_COOKIE[$key]) ? $_COOKIE[$key] : null;
		
	}
	
}


?>