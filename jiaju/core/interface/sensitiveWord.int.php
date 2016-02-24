<?php

if ( !defined ( 'BASE_PATH') )
{
	exit ( 'Access Denied' );
}
/*
 * 敏感词
 */
class sensitiveWord{
    
    private static  $instance = null;
    
    private $_cache;
    
    private $_token = 'sensitiveWord';
    
    private $_datas =  null;
    
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new sensitiveWord();
        }
        
        return self::$instance;
    }
    
    private function __construct() { 
        $this->_cache = fileCache::getInstance();        
    }
    
    public function init(){
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            if(empty($_POST)) return;
            $this->load();
            if(empty($this->_datas)) return ;
            foreach($_POST as $v){
                if(!is_int($v) && !empty($v) && !is_array($v) ){
                    foreach($this->_datas as $val){
                        if(strstr($v,$val)) errorAlert ('您的内容中含有敏感词---'.$val);
                    }
                }
            }
        }
      
        return;
    }
    
    private function load(){
        
        $this->_datas = $this->_cache->load($this->_token);
        if(empty($this->_datas)) $this->put();
    }
    
    private function put(){
        import::getMdl('sensitiveWord');
        $datas= sensitiveWordMdl::getInstance()->getAllSensitiveWord();
        $this->_datas = $datas;
        $this->_cache->put($this->_token,  $this->_datas ,0); //20天
        return $this->_datas;
    }
    
}    