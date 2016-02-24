<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ('Access Denied' );
}

class buildingSiteStatusMdl{
    
    private  $_db;
    
    private static  $instance = null;
    
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new buildingSiteStatusMdl();
        }
        
        return self::$instance;
    }
    
    private function __construct() {
        
        $this->_db = mysql::getInstance();
        
    }
    
    public  function addBuildingSiteStatus($info){
        
        if(empty($info)) return false;
        
        return $this->_db->insert(DB_FIX . 'building_site_status',$info);
    } 
    
    public function updateBuildingSiteStatus($id,$info){
        
        if(empty($info)) return false;
        
        $id = (int)$id;
        
        return $this->_db->update(DB_FIX.'building_site_status',$info," id = {$id} ");
    }
    
    public function getMaxStatusBySiteId($id){
        
         $id = (int)$id;
        
        return $this->_db->fetchOne("select  max(status) from `".DB_FIX."building_site_status` where site_id ={$id} limit 1 ");
    }
    
    public function getBuildingSiteStatus($id){
        
        $id = (int)$id;
        
        return $this->_db->fetchRow("select  * from `".DB_FIX."building_site_status` where id ={$id} limit 1 ");
    }
    
    public function getAllSiteStatusBySid($id){
        $id = (int)$id;
        return $this->_db->fetchAll("select  * from `".DB_FIX."building_site_status` where site_id ={$id} order by  status asc  limit 0,10 ");
    }

    
    public function delBuildingSiteStatus($id){
         $id = (int)$id;
         return $this->_db->delete(DB_FIX."building_site_status"," id = {$id} ");
    }
    
    
    public function getBuildingSiteStatusList($col,$where,$order,$start=0,$limit=10){
        
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
        return $this->_db->fetchAll(" select {$colstr} from  `".DB_FIX."building_site_status` {$wherestr} {$orderby} limit {$start},{$limit} ");
    }
    
    public function getBuildingSiteStatusCount($where){
        
        $wherestr  = $this->getWhere($where);
        
        return $this->_db->fetchOne("  select count(1) from  `".DB_FIX."building_site_status` {$wherestr}");
    }
    
    
    private function getWhere($where){
         $local = array();
         if(isset($where['keyword'])){
             $where['keyword'] = $this->_db->quote($where['keyword']);
         }
         if(isset($where['site_id'])){
             $where['site_id'] = (int)$where['site_id'];
             $local[] = " site_id = {$where['site_id']} ";
         }
         if(isset($where['status'])){
             $where['status'] = (int)$where['status'];
             $local[] = " status = {$where['status']} ";
         }
         
         $wherestr = '';
         if(!empty($local)){
             $wherestr = " WHERE  ". join(' and ',$local);
         }
         return $wherestr;
    }
    
    
}