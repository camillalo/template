<?php
if ( !defined ( 'BASE_PATH') )
{
	exit ( 'Access Denied' );
}
/*
 * 分类的接口类
 */
class areaInt{
    
    private static  $instance = null;
    
    private $_cache;
    
    private $_token = 'areaInt';
    
    private $_datas =  null;
    
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new areaInt();
        }
        
        return self::$instance;
    }
    
    private function __construct() { 
        $this->_cache = fileCache::getInstance();        
    
        $this->load();
    }
    
    public function getAreaName($id){
        if(empty($id) || empty($this->_datas)) return null;
        
        return isset($this->_datas[$id]) ? $this->_datas[$id] :  null;
    }
    
    public function getAreas(){
        if( empty($this->_datas)) return array();
        return  $this->_datas;
    }
    
    
    public function load(){
        $this->_datas = $this->_cache->load($this->_token);
        if(empty($this->_datas)) $this->put();        
    }
    
    public function put(){
        import::getMdl('area');
        $datas= areaMdl::getInstance()->getAllArea();
        $local = array();
        foreach($datas as $val){
           $local[$val['id']] = $val['area_name'];
        }
        $this->_datas = $local;
        $this->_cache->put($this->_token,  $this->_datas);
        return $this->_datas;
    }
    
}    