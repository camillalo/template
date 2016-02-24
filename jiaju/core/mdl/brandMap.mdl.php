<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ('Access Denied' );
}

class brandMapMdl{
    
    private  $_db;
    
    private static  $instance = null;
    
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new brandMapMdl();
        }
        
        return self::$instance;
    }
    
    private function __construct() {
        
        $this->_db = mysql::getInstance();
        
    }
    

    public function delBrandMapByBrandId($id){
        
         $id = (int)$id;
         
         return $this->_db->delete(DB_FIX."brand_map"," brand_id = {$id} ");
    }
    

    
    
    public function addBrandMaps($info){
        
        return $this->_db->insertArr(DB_FIX."brand_map",$info);
    }
    
    public function getAllBrandMap($category_id){
        
        $category_id = (int)$category_id;
        
        return $this->_db->fetchCol("select  brand_id from  ".DB_FIX."brand_map WHERE  category_id = {$category_id}");
    }
    
    public function getAllBrandsByCateIds($category_id = array(),$limit = 0){
         $limitSql = '';
          if($limit > 0){
              $limitSql = " limit 0,{$limit} ";
          }
          foreach($category_id as $k=>$v){
              $category_id[$k] = (int)$v;
          }
          if(empty($category_id)) return array();
          $categorystr = join(',',$category_id);
         
          return $this->_db->fetchAll("select  a.brand_id,b.brand_name from  ".DB_FIX."brand_map a 
                                    join  ".DB_FIX."brand b ON (a.brand_id = b.brand_id) 
                                    WHERE  a.category_id in({$categorystr}) group by a.brand_id {$limitSql}");
    }
    
    public function getAllBrandsByMap($category_id,$limit = 0){
          $limitSql = '';
          if($limit > 0){
              $limitSql = " limit 0,{$limit} ";
          }
         $category_id = (int)$category_id;
        
        return $this->_db->fetchAll("select  a.brand_id,b.brand_name from  ".DB_FIX."brand_map a 
                                    join  ".DB_FIX."brand b ON (a.brand_id = b.brand_id) 
                                    WHERE  a.category_id = {$category_id} {$limitSql}");
    }
    
    public function getAllCategoryMap($brand_id){
        
        $brand_id = (int)$brand_id;
        
        return $this->_db->fetchCol("select  category_id from  ".DB_FIX."brand_map WHERE  brand_id = {$brand_id}");
    }
    
    
}