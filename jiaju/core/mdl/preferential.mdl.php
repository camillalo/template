<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ('Access Denied' );
}

class preferentialMdl{
    
    private  $_db;
    
    private static  $instance = null;
    
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new preferentialMdl();
        }
        
        return self::$instance;
    }
    
    private function __construct() {
        
        $this->_db = mysql::getInstance();
        
    }
    
    public  function addPreferential($info){
        
        if(empty($info)) return false;
        
        return $this->_db->insert(DB_FIX . 'preferential',$info);
    } 
    
    public function updatePreferential($id,$info){
        
        if(empty($info)) return false;
        
        $id = (int)$id;
        
        return $this->_db->update(DB_FIX.'preferential',$info," id = {$id} ");
    }
    
    public function getPreferential($id){
        
        $id = (int)$id;
        
        return $this->_db->fetchRow("select  * from `".DB_FIX."preferential` where id ={$id} limit 1 ");
    }
    
    public function getPreferentialsByIds($ids){
        if(empty($ids)) return array();
        $local = array();
        foreach($ids as $v){
            $local[] = (int)$v;
        }
        $idstr = join(',',$local);
        return $this->_db->fetchAll("select id,title,create_time from `".DB_FIX."preferential`  where id  in ({$idstr}) AND is_show = 1 ");
    }
    
    public function getNewPreferentials($num){
  
        $num = (int)$num;
        return $this->_db->fetchAll("select id,title,create_time from `".DB_FIX."preferential` where  is_show = 1 order by id desc  limit 0,{$num}");
    }
    
    
    public function delPreferential($id){
         $id = (int)$id;
         return $this->_db->delete(DB_FIX."preferential"," id = {$id} ");
    }
    
    
    public function getPreferentialList($col,$where,$order,$start=0,$limit=10){
        
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
        return $this->_db->fetchAll(" select {$colstr} from  `".DB_FIX."preferential` {$wherestr} {$orderby} limit {$start},{$limit} ");
    }
    
    public function getPreferentialCount($where){
        
        $wherestr  = $this->getWhere($where);
        
        return $this->_db->fetchOne("  select count(1) from  `".DB_FIX."preferential` {$wherestr}");
    }
    
    
    private function getWhere($where){
         $local = array();
         if(isset($where['keyword'])){
             $where['keyword'] = $this->_db->quote($where['keyword']);
             $local[] = " ( title like  '%{$where['keyword']}%'   ) ";
         }
         if(isset($where['uid'])) {
             $where['uid'] = (int)$where['uid'];
             $local[] = " uid  = {$where['uid']} ";
         }
          if(isset($where['is_show'])) {
             $where['is_show'] = (int)$where['is_show'];
             $local[] = " is_show  = {$where['is_show']} ";
         }
         

         if(isset($where['area_id'])) {
             $where['area_id'] = (int)$where['area_id'];
             $local[] = " area_id  = {$where['area_id']} ";
         }
         
         $wherestr = '';
         if(!empty($local)){
             $wherestr = " WHERE  ". join(' and ',$local);
         }
         return $wherestr;
    }
    
    
}