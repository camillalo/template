<?php
if ( !defined ( 'BASE_PATH') )
{
	exit ( 'Access Denied' );
}
class seoInt{
    
    private static  $instance = null;
    
    private $_cfg = array();
    
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new seoInt();
        }
        
        return self::$instance;
    }
    
    private function __construct() {
        $this->_cfg = import::getCfg('seomain');        
    }
    
    public function load($auth,$data = array()){
        global  $__SETTING  ;
        if(!isset($this->_cfg[$auth])) return $__SETTING;
        $data ['sitename'] = isset($__SETTING['site_name']) ?  $__SETTING['site_name'] : '';
        
        foreach($data as $k=>$val){
            $k = '{'.$k.'}';
            if(!empty($this->_cfg[$auth]['title'])){
                $this->_cfg[$auth]['title'] = str_replace($k, $val,$this->_cfg[$auth]['title'] );
            }
            if(!empty($this->_cfg[$auth]['keyword'])){
                $this->_cfg[$auth]['keyword'] = str_replace($k, $val,$this->_cfg[$auth]['keyword'] );
            }
            if(!empty($this->_cfg[$auth]['description'])){
                $this->_cfg[$auth]['description'] = str_replace($k, $val,$this->_cfg[$auth]['description'] );
            }
        }
        if(!empty($this->_cfg[$auth]['title'])){
            $__SETTING['title'] = $this->_cfg[$auth]['title'];
        }
        if(!empty($this->_cfg[$auth]['keyword'])){
            $__SETTING['keyword'] = $this->_cfg[$auth]['keyword'];
        }
        if(!empty($this->_cfg[$auth]['description'])){
            $__SETTING['description'] = $this->_cfg[$auth]['description'];
        }
        return $__SETTING;
    }
}    