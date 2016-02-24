<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ('Access Denied' );
}

class companyAreaMdl{
    
    private  $_db;
    
    private static  $instance = null;
    
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new companyAreaMdl();
        }
        
        return self::$instance;
    }
    
    private function __construct() {
        
        $this->_db = mysql::getInstance();
        
    }
    
    public  function addcompanyArea($info){
        
        if(empty($info)) return false;
        
        return $this->_db->insert(DB_FIX . 'company_area',$info);
    } 
    
    public function getcompanyAreacCol($id){
        $id = (int)$id;
        return $this->_db->fetchCol("select  area_id  from ".DB_FIX."company_area where uid = {$id} ");
    }
    
    public function getcompanyAreaNameCol($id){
        $id = (int)$id;
        return $this->_db->fetchCol("select  b.area_name  from ".DB_FIX."company_area  a join ".DB_FIX."city_areas b ON (a.area_id = b.id) where a.uid = {$id} ");
    }
    
    public function delcompanyAreaById($id){
        
         $id = (int)$id;
         
         return $this->_db->delete(DB_FIX."company_area"," uid = {$id} ");
    }
    
}