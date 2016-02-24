<?php
if ( !defined ( 'BASE_PATH') )
{
	exit ( 'Access Denied' );
}
/*
 * 板块操作权限控制类用于前台
 */
class authorityInt{
    
    private static  $instance = null;
    
    private $_type = null;
    private $_authority = 0;
    private $_show =  0;
    
    private $_userinfo = array();
    
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new authorityInt();
        }
        
        return self::$instance;
    }
    
    private function __construct() { 
      
    }
    
    public function isAuthority($model,$uid = null){
        if(empty($uid)) return false;
        $this->checkType($uid);
        $this->finishing($model);
        return $this->_authority;
    }
    
    public function isShow(){
        return $this->_show;
    }
    
    private function finishing($model){
        global $__SITE_AUTHORITY;
        $cfg  = import::getCfg('authority');
        $status = isset($cfg[$this->_type][$model]) ? (int)$cfg[$this->_type][$model] : 0;
        switch($status){
            case $__SITE_AUTHORITY['no']:
                $this->_show = 0;
                $this->_authority = 0;
                break;
            case $__SITE_AUTHORITY['yes1']:
                $this->_show = 0;
                $this->_authority = 1;
                break;
            case $__SITE_AUTHORITY['yes2']:
                $this->_show = 1;
                $this->_authority = 1;
                break;
        }
        return;
    }
    
    private function checkType($uid){
        import::getMdl('users');
        $isVip = usersMdl::getInstance()->checkIsVip($uid);
        if($isVip) {
            $this->_type = 'vip';
            return;
        }
        $is_authentication = usersMdl::getInstance()->checkIsAuthentication($uid);
        if($is_authentication){
            $this->_type = 'rule';
            return;
        }
        $this->_type = 'general';
        return;
    }
   
    
}    