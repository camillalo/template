<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ('Access Denied' );
}

class linksMdl{
    
    private  $_db;
    
    private static  $instance = null;
    
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new linksMdl();
        }
        
        return self::$instance;
    }
    
    private function __construct() {
        
        $this->_db = mysql::getInstance();
        
    }
    
    public  function addLinks($info){
        
        if(empty($info)) return false;
        
        return $this->_db->insert(DB_FIX . 'links',$info);
    } 
    
    public function updateLinks($id,$info){
        
        if(empty($info)) return false;
        
        $id = (int)$id;
        
        return $this->_db->update(DB_FIX.'links',$info," id = {$id} ");
    }
    
    public function getLinks($id){
        
        $id = (int)$id;
        
        return $this->_db->fetchRow("select  * from `".DB_FIX."links` where id ={$id} limit 1 ");
    }
    
    public function getAllLinks(){
        return $this->_db->fetchAll("select  * from `".DB_FIX."links` order by link_order desc ");
    }
    
    
    public function delLinks($id){
         $id = (int)$id;
         return $this->_db->delete(DB_FIX."links"," id = {$id} ");
    }
    
    
    public function getLinksList($col,$where,$order,$start=0,$limit=10){
        
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
        return $this->_db->fetchAll(" select {$colstr} from  `".DB_FIX."links` {$wherestr} {$orderby} limit {$start},{$limit} ");
    }
    
    public function getLinksCount($where){
        
        $wherestr  = $this->getWhere($where);
        
        return $this->_db->fetchOne("  select count(1) from  `".DB_FIX."links` {$wherestr}");
    }
    
    
    private function getWhere($where){
         $local = array();
         if(isset($where['keyword'])){
             $where['keyword'] = $this->_db->quote($where['keyword']);
             $local[] = " ( link_name like  '%{$where['keyword']}%'   ) ";
         }
         
         $wherestr = '';
         if(!empty($local)){
             $wherestr = " WHERE  ". join(' and ',$local);
         }
         return $wherestr;
    }
    
    
}