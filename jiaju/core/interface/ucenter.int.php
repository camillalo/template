<?php

if (!defined('BASE_PATH')) {
    exit('Access Denied');
}
$fileName = BASE_PATH . '/api/config.uc.php';
if (file_exists($fileName)) {
    require $fileName;
}
if (defined('UC_API') && UC_API != '') {
    require BASE_PATH.'/api/uc_client/client.php';
}
/*
 * 分类的接口类
 */

class ucenterInt {

    private static $instance = null;

    public static function getInstance() {
        if (null == self::$instance) {

            self::$instance = new ucenterInt();
        }

        return self::$instance;
    }

    private function __construct() {
        
    }
    
    public function logout(){
         if (defined('UC_API') && UC_API != '') {
            return uc_user_synlogout();
         }
    }
    
    public function login($username,$password){
        if (defined('UC_API') && UC_API != '') {
            if (defined('APP_CHARSET') && APP_CHARSET === 1) {
                $username = mb_convert_encoding($username,'gbk','utf-8');
            }
            list($uid, $username, $password, $email) = uc_user_login($username,$password);
            if($uid > 0){
                $js  = uc_user_synlogin($uid);
                return array('uid'=>$uid,'email'=>$email,'js'=>$js );
            }
            elseif($uid == -1) {
		return  '用户不存在,或者被删除';
            } elseif($uid == -2) {
                return  'UC密码错误';
            } else {
                return  'UC系统错误';
            }            
        } 
        return false;
    }
    
    
    public function edit($username,$pwd1,$pwd2){
         if (defined('UC_API') && UC_API != '') {
            if (defined('APP_CHARSET') && APP_CHARSET === 1) {
                $username =mb_convert_encoding($username,'gbk','utf-8');       
            }
            //echo $username,'----';
            //echo $pwd1,'----',$pwd2;
           $ret =  uc_user_edit($username,$pwd1,$pwd2,'');  
           
           return $ret;
         }
         return false;
    }
    
     public function edit2($username,$pwd1,$pwd2){
         if (defined('UC_API') && UC_API != '') {
            if (defined('APP_CHARSET') && APP_CHARSET === 1) {
                $username =mb_convert_encoding($username,'gbk','utf-8');       
            }
            //echo $username,'----';
            //echo $pwd1,'----',$pwd2;
           $ret =  uc_user_edit($username,$pwd1,$pwd2,'',1);  
           
           return $ret;
         }
         return false;
    }
    
    public function register($username, $password, $email) {
        if (defined('UC_API') && UC_API != '') {
            if (defined('APP_CHARSET') && APP_CHARSET === 1) {
                $username = mb_convert_encoding($username,'gbk','utf-8');
            }
            $uid = uc_user_register($username,$password, $email);
            if ($uid > 0) {
                return $uid;
            } else {
                switch ($uid) {
                    case -1:  return '用户名不合法';                    
                    case -2:  return '包含不允许注册的词语';                    
                    case -3:  return '用户名已经存在';    
                    case -4:  return 'Email 格式有误';      
                    case -5:  return 'Email 不允许注册'; 
                    case -6:  return '该 Email 已经被注册';  
                    default:  return 'uc通信失败';
                }
            }
        }
        return false;
    }

}

