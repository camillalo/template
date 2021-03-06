<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ('Access Denied' );
}

class areaMdl{
    
    private  $_db;
    
    private static  $instance = null;
    
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new areaMdl();
        }
        
        return self::$instance;
    }
    
    private function __construct() {
        
        $this->_db = mysql::getInstance();
        
    }
    
    public  function addArea($info){
        
        if(empty($info)) return false;
        
        return $this->_db->insert(DB_FIX . 'city_areas',$info);
    } 
    
    public function updateArea($id,$info){
        
        if(empty($info)) return false;
        
        $id = (int)$id;
        
        return $this->_db->update(DB_FIX.'city_areas',$info," id = {$id} ");
    }
    
    public function getArea($id){
        
        $id = (int)$id;
        
        return $this->_db->fetchRow("select  * from `".DB_FIX."city_areas` where id ={$id} limit 1 ");
    }
    
    public function getAllArea(){
         return $this->_db->fetchAll("select  * from `".DB_FIX."city_areas` ");
    }
    
    public function getAreaPair(){
    
        return $this->_db->fetchPair(" select id,area_name from `".DB_FIX."city_areas` ");
    }
    public function getAreas(){

        return $this->_db->fetchAll(" select id,area_name from `".DB_FIX."city_areas`  ");
    }
    
    
    public function delArea($id){
         $id = (int)$id;
         return $this->_db->delete(DB_FIX."city_areas"," id = {$id} ");
    }
    
    
    public function getAreaList($col,$where,$order,$start=0,$limit=10){
        
        if(empty($col)){
            $colstr ='*';
        }else{
            $colstr = join(',',$col);
        }
        $wherestr  = $this->getWhere($where);
        if(empty($order)){
            $orderby = ' order by  id  desc ';
        }else{
            $local = array();
            foreach($order  as $k => $v){
                $local [] = " {$k} {$v} ";
            }
            $orderby = ' order by '.join(',',$local);
        }
        return $this->_db->fetchAll(" select {$colstr} from  `".DB_FIX."city_areas` {$wherestr} {$orderby} limit {$start},{$limit} ");
    }
    
    public function getAreaCount($where){
        
        $wherestr  = $this->getWhere($where);
        
        return $this->_db->fetchOne("  select count(1) from  `".DB_FIX."city_areas` {$wherestr}");
    }
    
    
    private function getWhere($where){
         $local = array();
         if(isset($where['keyword'])){
             $where['keyword'] = $this->_db->quote($where['keyword']);
             $local[] = " ( area_name like  '%{$where['keyword']}%'   ) ";
         }
       
         
         $wherestr = '';
         if(!empty($local)){
             $wherestr = " WHERE  ". join(' and ',$local);
         }
         return $wherestr;
    }
    
    
}