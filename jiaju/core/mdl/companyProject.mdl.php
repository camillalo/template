<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ('Access Denied' );
}

class companyProjectMdl{
    
    private  $_db;
    
    private static  $instance = null;
    
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new companyProjectMdl();
        }
        
        return self::$instance;
    }
    
    private function __construct() {
        
        $this->_db = mysql::getInstance();
        
    }
    
    public  function addcompanyProject($info){
        
        if(empty($info)) return false;
        
        return $this->_db->insert(DB_FIX . 'company_project',$info);
    } 

    public function getcompanyProjectcCol($id){
        $id = (int)$id;
        return $this->_db->fetchCol("select  project_id  from ".DB_FIX."company_project where uid = {$id} ");
    }
    
    public function delcompanyProjectById($id){
        
         $id = (int)$id;
         
         return $this->_db->delete(DB_FIX."company_project"," uid = {$id} ");
    }
    
}