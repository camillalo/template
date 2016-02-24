<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ('Access Denied' );
}

class companyIndustryMdl{
    
    private  $_db;
    
    private static  $instance = null;
    
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new companyIndustryMdl();
        }
        
        return self::$instance;
    }
    
    private function __construct() {
        
        $this->_db = mysql::getInstance();
        
    }
    
    public  function addcompanyIndustry($info){
        
        if(empty($info)) return false;
        
        return $this->_db->insert(DB_FIX . 'company_industry',$info);
    } 

    public function getcompanyIndustryCol($id){
        $id = (int)$id;
        return $this->_db->fetchCol("select  industry_id  from ".DB_FIX."company_industry where uid = {$id} ");
    }
    
    public function delcompanyIndustryById($id){
        
         $id = (int)$id;
         
         return $this->_db->delete(DB_FIX."company_industry"," uid = {$id} ");
    }
    
}