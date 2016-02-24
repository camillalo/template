<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ('Access Denied' );
}

class adsMdl{
    
    private  $_db;
    
    private static  $instance = null;
    
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new adsMdl();
        }
        
        return self::$instance;
    }
    
    private function __construct() {
        
        $this->_db = mysql::getInstance();
        
    }
    
    public  function addAds($info){
        
        if(empty($info)) return false;
        
        return $this->_db->insert(DB_FIX . 'ads',$info);
    } 
    
    public function updateAds($id,$info){
        
        if(empty($info)) return false;
        
        $id = (int)$id;
        
        return $this->_db->update(DB_FIX.'ads',$info," id = {$id} ");
    }
    
    public function getAds($id){
        
        $id = (int)$id;
        
        return $this->_db->fetchRow("select  * from `".DB_FIX."ads` where id ={$id} limit 1 ");
    }
    
    public function getAllAds(){

        $time = NOWTIME;
        return $this->_db->fetchAll("select  * from `".DB_FIX."ads`  where  bg_time <= {$time} AND end_time > {$time} order by `orderby` ASC ");
    }
    
    public function delAds($id){
         $id = (int)$id;
         return $this->_db->delete(DB_FIX."ads"," id = {$id} ");
    }
    
    
    public function getAdsList($col,$where,$order,$start=0,$limit=10){
        
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
        return $this->_db->fetchAll(" select {$colstr} from  `".DB_FIX."ads` {$wherestr} {$orderby} limit {$start},{$limit} ");
    }
    
    public function getAdsCount($where){
        
        $wherestr  = $this->getWhere($where);
        
        return $this->_db->fetchOne("  select count(1) from  `".DB_FIX."ads` {$wherestr}");
    }
    
    
    private function getWhere($where){
         $local = array();
         if(isset($where['keyword'])){
             $where['keyword'] = $this->_db->quote($where['keyword']);
             $local[] = " ( title like  '%{$where['keyword']}%'   ) ";
         }
         if(isset($where['site_id'])){
             $where['site_id'] = (int)$where['site_id'];
             $local[] = " site_id = {$where['site_id']} ";
         }
         $wherestr = '';
         if(!empty($local)){
             $wherestr = " WHERE  ". join(' and ',$local);
         }
         return $wherestr;
    }
    
    
}