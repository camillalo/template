<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ('Access Denied' );
}

class outtoinMdl{
    
    private  $_db;
    
    private static  $instance = null;
    
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new outtoinMdl();
        }
        
        return self::$instance;
    }
    
    private function __construct() {
        
        $this->_db = mysql::getInstance();
        
    }
    
    public  function addOuttoin($info){
        
        if(empty($info)) return false;
        
        return $this->_db->insert(DB_FIX . 'outtoin',$info);
    } 
   
    public function getOuttoin($out){
        
        $out = $this->_db->quote($out);
        
        return $this->_db->fetchRow("select  * from `".DB_FIX."outtoin` where `out` = '{$out}' limit 1 ");
    }
    
    public  function updateOuttoin($id,$info){
        
       if(empty($info)) return false;
        
        $id = (int)$id;
        
        return $this->_db->update(DB_FIX.'outtoin',$info," id = {$id} ");
    } 
   
    public  function updateOuttoinByUid($uid,$info){
        
       if(empty($info)) return false;
        
        $uid = (int)$uid;
        
        return $this->_db->update(DB_FIX.'outtoin',$info," uid = {$uid} ");
    } 

    
}