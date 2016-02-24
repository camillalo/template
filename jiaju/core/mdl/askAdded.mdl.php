<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ('Access Denied' );
}

class askAddedMdl{
    
    private  $_db;
    
    private static  $instance = null;
    
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new askAddedMdl();
        }
        
        return self::$instance;
    }
    
    private function __construct() {
        
        $this->_db = mysql::getInstance();
        
    }
    
    public  function addAskAdded($info){
        
        if(empty($info)) return false;
        
        return $this->_db->insert(DB_FIX . 'ask_added',$info);
    } 
    
    public function updateAskAdded($id,$info){
        
        if(empty($info)) return false;
        
        $id = (int)$id;
        
        return $this->_db->update(DB_FIX.'ask_added',$info," id = {$id} ");
    }
    
    public function getAskAdded($id){
        
        $id = (int)$id;
        
        return $this->_db->fetchRow("select  * from `".DB_FIX."ask_added` where id ={$id} limit 1 ");
    }
    

    
    public function delAskAdded($id){
         $id = (int)$id;
         return $this->_db->delete(DB_FIX."ask_added"," id = {$id} ");
    }
    
    
    public function getAskAddedList($col,$where,$order,$start=0,$limit=10){
        
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
        return $this->_db->fetchAll(" select {$colstr} from  `".DB_FIX."ask_added` {$wherestr} {$orderby} limit {$start},{$limit} ");
    }
    
    public function getAskAddedCount($where){
        
        $wherestr  = $this->getWhere($where);
        
        return $this->_db->fetchOne("  select count(1) from  `".DB_FIX."ask_added` {$wherestr}");
    }
    
    
    private function getWhere($where){
         $local = array();
         if(isset($where['keyword'])){
             $where['keyword'] = $this->_db->quote($where['keyword']);
             $local[] = " ( added like  '%{$where['keyword']}%'   ) ";
         }
         if(isset($where['ask_id'])){
             $where['ask_id'] = (int)$where['ask_id'];
             $local[] = " ask_id = {$where['ask_id']} ";
         }
         
         $wherestr = '';
         if(!empty($local)){
             $wherestr = " WHERE  ". join(' and ',$local);
         }
         return $wherestr;
    }
    
    
}