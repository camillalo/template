<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ('Access Denied' );
}

class ranksMdl{
    
    private  $_db;
    
    private static  $instance = null;
    
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new ranksMdl();
        }
        
        return self::$instance;
    }
    
    private function __construct() {
        
        $this->_db = mysql::getInstance();
        
    }
    
    public  function addRanks($info){
        
        if(empty($info)) return false;
        
        return $this->_db->insert(DB_FIX . 'user_ranks',$info);
    } 
    
    public function updateRanks($id,$info){
        
        if(empty($info)) return false;
        
        $id = (int)$id;
        
        return $this->_db->update(DB_FIX.'user_ranks',$info," rank_id = {$id} ");
    }
    
    public function getRanks($id){
        
        $id = (int)$id;
        
        return $this->_db->fetchRow("select  * from `".DB_FIX."user_ranks` where rank_id ={$id} limit 1 ");
    }
    
    public function getAllRanksPairs(){
        
         return $this->_db->fetchPair("select rank_id,rank_name from `".DB_FIX."user_ranks` ");
    }
    
    
    public function getAllRanks(){
        
        return $this->_db->fetchAll("select * from `".DB_FIX."user_ranks` ");
    }

    
    public function delRanks($id){
         $id = (int)$id;
         return $this->_db->delete(DB_FIX."user_ranks"," rank_id = {$id} ");
    }
    
    
    public function getRanksList($col,$where,$order,$start=0,$limit=10){
        
        if(empty($col)){
            $colstr ='*';
        }else{
            $colstr = join(',',$col);
        }
        $wherestr  = $this->getWhere($where);
        if(empty($order)){
            $orderby = ' order by  rank_id  desc ';
        }else{
            $local = array();
            foreach($order  as $k => $v){
                $local [] = " {$k} {$v} ";
            }
            $orderby = ' order by '.join(',',$local);
        }
        return $this->_db->fetchAll(" select {$colstr} from  `".DB_FIX."user_ranks` {$wherestr} {$orderby} limit {$start},{$limit} ");
    }
    
    public function getRanksCount($where){
        
        $wherestr  = $this->getWhere($where);
        
        return $this->_db->fetchOne("  select count(1) from  `".DB_FIX."user_ranks` {$wherestr}");
    }
    
    
    private function getWhere($where){
         $local = array();
         if(isset($where['keyword'])){
             $where['keyword'] = $this->_db->quote($where['keyword']);
             $local[] = " ( rank_name like  '%{$where['keyword']}%'   ) ";
         }
         
         $wherestr = '';
         if(!empty($local)){
             $wherestr = " WHERE  ". join(' and ',$local);
         }
         return $wherestr;
    }
    
    
}