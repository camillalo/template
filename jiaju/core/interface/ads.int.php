<?php
if ( !defined ( 'BASE_PATH') )
{
	exit ( 'Access Denied' );
}
/*
 * 分类的接口类
 */
class adsInt{
    
    private static  $instance = null;
    
    private $_cache;
    
    private $_token = 'adsInt';
    
    private $_datas =  null;
    
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new adsInt();
        }
        
        return self::$instance;
    }
    
    private function __construct() { 
        $this->_cache = fileCache::getInstance();        
    }
    
    public function load($site_id){
        
        if($this->_datas === null){     
            
            $this->_datas = $this->_cache->load($this->_token);
            
            if(empty( $this->_datas )) $this->put ();
            
        }
        if(isset($this->_datas[$site_id])) return $this->_datas[$site_id];
        
        return array();
    }
    
    public function put(){
        import::getMdl('ads'); 
        
        $ads = adsMdl::getInstance()->getAllAds();
        
        $local = array();
        
        import::getMdl('adSite');
        foreach( $ads as $v){
            if(!isset($local[$v['site_id']])){
                $local[$v['site_id']] = adSiteMdl::getInstance()->getAdSite($v['site_id']);     
                $local[$v['site_id']]['item'][] = $v;
            }else{
                $local[$v['site_id']]['item'][] = $v;
            }
        }
        $this->_cache->put($this->_token,$local);
        
        $this->_datas = $local;
        return $this->_datas;
    }
    
    
}    