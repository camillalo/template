<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ('Access Denied' );
}

class biddingQuickMdl{
    
    private  $_db;
    
    private static  $instance = null;
    
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new biddingQuickMdl();
        }
        
        return self::$instance;
    }
    
    private function __construct() {
        
        $this->_db = mysql::getInstance();
        
    }
    
    public  function addBiddingQuick($info){
        
        if(empty($info)) return false;
        
        return $this->_db->insert(DB_FIX . 'bidding_quick',$info);
    } 
    
    public function updateBiddingQuick($id,$info){
        
        if(empty($info)) return false;
        
        $id = (int)$id;
        
        return $this->_db->update(DB_FIX.'bidding_quick',$info," id = {$id} ");
    }
    
    public function getBiddingQuick($id){
        
        $id = (int)$id;
        
        return $this->_db->fetchRow("select  * from `".DB_FIX."bidding_quick` where id ={$id} limit 1 ");
    }
    
    public function getNewQuicks($num){
        $num = (int)$num;
        return $this->_db->fetchCol("select mobile  from `".DB_FIX."bidding_quick`  order by id DESC limit 0,{$num}");
    }

    
    public function delBiddingQuick($id){
         $id = (int)$id;
         return $this->_db->delete(DB_FIX."bidding_quick"," id = {$id} ");
    }
    
    
    public function getBiddingQuickList($col,$where,$order,$start=0,$limit=10){
        
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
        return $this->_db->fetchAll(" select {$colstr} from  `".DB_FIX."bidding_quick` {$wherestr} {$orderby} limit {$start},{$limit} ");
    }
    
    public function getBiddingQuickCount($where){
        
        $wherestr  = $this->getWhere($where);
        
        return $this->_db->fetchOne("  select count(1) from  `".DB_FIX."bidding_quick` {$wherestr}");
    }
    
    
    private function getWhere($where){
         $local = array();
         
         $wherestr = '';
         if(!empty($local)){
             $wherestr = " WHERE  ". join(' and ',$local);
         }
         return $wherestr;
    }
    
    
}