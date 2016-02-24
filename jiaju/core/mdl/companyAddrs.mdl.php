<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ('Access Denied' );
}

class companyAddrsMdl{
    
    private  $_db;
    
    private static  $instance = null;
    
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new companyAddrsMdl();
        }
        
        return self::$instance;
    }
    
    private function __construct() {
        
        $this->_db = mysql::getInstance();
        
    }
    
    public  function addCompanyAddrs($info){
        
        if(empty($info)) return false;
        
        return $this->_db->insert(DB_FIX . 'company_addrs',$info);
    } 
    
    public function updateCompanyAddrs($id,$info){
        
        if(empty($info)) return false;
        
        $id = (int)$id;
        
        return $this->_db->update(DB_FIX.'company_addrs',$info," id = {$id} ");
    }
    
    public function getCompanyAddrs($id){
        
        $id = (int)$id;
        
        return $this->_db->fetchRow("select  * from `".DB_FIX."company_addrs` where id ={$id} limit 1 ");
    }
    
    public function getCompanyAddrNameById($id){
        
        $id = (int)$id;
        
        return $this->_db->fetchOne("select  addr from `".DB_FIX."company_addrs` where id ={$id} limit 1 "); 
    }
    
    
    public function getCompanyAddrByCompanyid($id){
        $id = (int)$id;
        return $this->_db->fetchRow("select  * from `".DB_FIX."company_addrs` where uid ={$id}   limit 1 ");
    }
    
    public function delCompanyAddrs($id){
         $id = (int)$id;
         return $this->_db->delete(DB_FIX."company_addrs"," id = {$id} ");
    }
    
    public function getAllCompanyAddrs($id){
        $id = (int)$id;
         return $this->_db->fetchAll("select  * from `".DB_FIX."company_addrs` where uid ={$id}  ");
    }
    
    
}