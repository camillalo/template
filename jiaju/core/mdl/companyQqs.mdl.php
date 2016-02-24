<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ('Access Denied' );
}

class companyQqsMdl{
    
    private  $_db;
    
    private static  $instance = null;
    
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new companyQqsMdl();
        }
        
        return self::$instance;
    }
    
    private function __construct() {
        
        $this->_db = mysql::getInstance();
        
    }
    
    public  function addCompanyQqs($info){
        
        if(empty($info)) return false;
        
        return $this->_db->insert(DB_FIX . 'company_qqs',$info);
    } 
    
    public function updateCompanyQqs($id,$info){
        
        if(empty($info)) return false;
        
        $id = (int)$id;
        
        return $this->_db->update(DB_FIX.'company_qqs',$info," id = {$id} ");
    }
    
    public function getCompanyQqs($id){
        
        $id = (int)$id;
        
        return $this->_db->fetchRow("select  * from `".DB_FIX."company_qqs` where id ={$id} limit 1 ");
    }
    

    
    public function delCompanyQqs($id){
         $id = (int)$id;
         return $this->_db->delete(DB_FIX."company_qqs"," id = {$id} ");
    }
    
    public function getAllCompanyQqs($id){
        
        $id = (int)$id;
        
        return $this->_db->fetchAll("select  * from `".DB_FIX."company_qqs` where uid ={$id}"); 
    }
    
    
    
}