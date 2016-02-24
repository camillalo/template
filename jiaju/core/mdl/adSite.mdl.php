<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ('Access Denied' );
}

class adSiteMdl{
    
    private  $_db;
    
    private static  $instance = null;
    
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new adSiteMdl();
        }
        
        return self::$instance;
    }
    
    private function __construct() {
        
        $this->_db = mysql::getInstance();
        
    }
    
    public  function addAdSite($info){
        
        if(empty($info)) return false;
        
        return $this->_db->insert(DB_FIX . 'ad_site',$info);
    } 
    
    public function updateAdSite($id,$info){
        
        if(empty($info)) return false;
        
        $id = (int)$id;
        
        return $this->_db->update(DB_FIX.'ad_site',$info," id = {$id} ");
    }
    
    public function getAdSite($id){
        
        $id = (int)$id;
        
        return $this->_db->fetchRow("select  * from `".DB_FIX."ad_site` where id ={$id} limit 1 ");
    }
    

    
    public function delAdSite($id){
         $id = (int)$id;
         return $this->_db->delete(DB_FIX."ad_site"," id = {$id} ");
    }
    
    
    public function getAdSitePair(){
        
        return $this->_db->fetchPair(" select id,name from `".DB_FIX."ad_site`  ");
    }
    
    public function getAdSites(){
        return $this->_db->fetchCol(" select * from `".DB_FIX."ad_site`  ");  
    }
    
    public function getAdSiteList($col,$where,$order,$start=0,$limit=10){
        
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
        return $this->_db->fetchAll(" select {$colstr} from  `".DB_FIX."ad_site` {$wherestr} {$orderby} limit {$start},{$limit} ");
    }
    
    public function getAdSiteCount($where){
        
        $wherestr  = $this->getWhere($where);
        
        return $this->_db->fetchOne("  select count(1) from  `".DB_FIX."ad_site` {$wherestr}");
    }
    
    
    private function getWhere($where){
         $local = array();
         if(isset($where['keyword'])){
             $where['keyword'] = $this->_db->quote($where['keyword']);
             $local[] = " ( name like  '%{$where['keyword']}%'   ) ";
         }
         
         $wherestr = '';
         if(!empty($local)){
             $wherestr = " WHERE  ". join(' and ',$local);
         }
         return $wherestr;
    }
    
    
}