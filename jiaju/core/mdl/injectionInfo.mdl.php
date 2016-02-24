<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ('Access Denied' );
}

class injectionInfoMdl{
    
    private  $_db;
    
    private static  $instance = null;
    
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new injectionInfoMdl();
        }
        
        return self::$instance;
    }
    
    private function __construct() {
        
        $this->_db = mysql::getInstance();
        
    }
    
    public  function addInjectionInfo($info){
        
        if(empty($info)) return false;
        
        return $this->_db->insert(DB_FIX . 'injection_info',$info);
    } 
    
    public function getInjectionInfoByToken($token){
        
        $token = $this->_db->quote($token);
        
        return $this->_db->fetchRow("select * from  ".DB_FIX . "injection_info where token = '{$token}' ");
    }
    
    public function updateInjectionInfo($token,$info){
        
        $token = $this->_db->quote($token);
        
        return $this->_db->update(DB_FIX . 'injection_info',$info," token = '{$token}' ");
    }
    
}