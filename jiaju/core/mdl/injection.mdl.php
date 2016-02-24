<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ('Access Denied' );
}

class injectionMdl{
    
    private  $_db;
    
    private static  $instance = null;
    
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new injectionMdl();
        }
        
        return self::$instance;
    }
    
    private function __construct() {
        
        $this->_db = mysql::getInstance();
        
    }
    
    public  function addInjection($info){
        
        if(empty($info)) return false;
        
        return $this->_db->insert(DB_FIX . 'injection',$info);
    } 
    
    public function updateInjection($id,$info){
        
        if(empty($info)) return false;
        
        $id = (int)$id;
        
        return $this->_db->update(DB_FIX.'injection',$info," id = {$id} ");
    }
    
    public function getInjection($id){
        
        $id = (int)$id;
        
        return $this->_db->fetchRow("select  * from `".DB_FIX."injection` where id ={$id} limit 1 ");
    }
    
    public function getAllInjection(){

        return $this->_db->fetchAll("select  * from `".DB_FIX."injection` ");
    }

    
    public function delInjection($id){
         $id = (int)$id;
         return $this->_db->delete(DB_FIX."injection"," id = {$id} ");
    }
    
    
    public function getInjectionList($col,$where,$order,$start=0,$limit=10){
        
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
        return $this->_db->fetchAll(" select {$colstr} from  `".DB_FIX."injection` {$wherestr} {$orderby} limit {$start},{$limit} ");
    }
    
    public function getInjectionCount($where){
        
        $wherestr  = $this->getWhere($where);
        
        return $this->_db->fetchOne("  select count(1) from  `".DB_FIX."injection` {$wherestr}");
    }
    
    
    private function getWhere($where){
         $local = array();
         if(isset($where['keyword'])){
             $where['keyword'] = $this->_db->quote($where['keyword']);
             $local[] = " ( name like  '%{$where['keyword']}%'   or ctl like  '%{$where['keyword']}%'   or act like  '%{$where['keyword']}%'   ) ";
         }
         
         $wherestr = '';
         if(!empty($local)){
             $wherestr = " WHERE  ". join(' and ',$local);
         }
         return $wherestr;
    }
    
    
}