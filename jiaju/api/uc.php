<?php
error_reporting(0);
define('BASE_PATH',dirname(__FILE__).'/../');
require BASE_PATH.'core/library/import.lib.php';
require BASE_PATH.'core/config.ini.php';
require BASE_PATH.'core/common.ini.php';
mb_internal_encoding("UTF-8");
import::getLib('mysql');
define('NOWTIME',$_SERVER['REQUEST_TIME']);
require  'config.uc.php';
require  'uc_client/client.php';
$code = empty($_GET['code']) ? '' : $_GET['code'];
$get = array();
parse_str(uc_authcode($code, 'DECODE', UC_KEY), $get);
 // file_put_contents('aaaaaaa.txt',  json_encode($get));
require BASE_PATH .'api/uc_client/lib/xml.class.php';
$post = xml_unserialize(file_get_contents('php://input'));
if(in_array($get['action'], array('test', 'deleteuser', 'renameuser', 'synlogin', 'synlogout', 'updatepw','updateapps','updateclient'))) {
       echo  ucNote::$get['action']($get,$post);
       die;
} else {
      die('0');
}

class ucNote {
	static function test() {
		return 1;
	}
        function updateapps($get, $post) {

		$UC_API = '';
		if($post['UC_API']) {
			$UC_API = $post['UC_API'];
			unset($post['UC_API']);
		}

		$cachefile = BASE_PATH.'api/uc_client/data/cache/apps.php';
		$fp = fopen($cachefile, 'w');
		$s = "<?php\r\n";
		$s .= '$_CACHE[\'apps\'] = '.var_export($post, TRUE).";\r\n";
		fwrite($fp, $s);
		fclose($fp);

		return 1;
	}

	function updateclient($get, $post) {
	

		$cachefile = BASE_PATH.'api/uc_client/data/cache/settings.php';
		$fp = fopen($cachefile, 'w');
		$s = "<?php\r\n";
		$s .= '$_CACHE[\'settings\'] = '.var_export($post, TRUE).";\r\n";
		fwrite($fp, $s);
		fclose($fp);

		return 1;
	}
	static function updatepw($get, $post){
		
		return 1;
	}
        
	static function synlogin($get, $post) {
		setCk('login_info',(int)$get['uid'] .'|'.NOWTIME.'|'.  getIp(),86400);        
		return 1;
	}

	static function synlogout($get, $post) {
                setCk('login_info', '');
		return 1;
	}	
}