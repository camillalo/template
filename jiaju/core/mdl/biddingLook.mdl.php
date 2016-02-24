<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ('Access Denied' );
}

class biddingLookMdl{
    
    private  $_db;
    
    private static  $instance = null;
    
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new biddingLookMdl();
        }
        
        return self::$instance;
    }
    
    private function __construct() {
        
        $this->_db = mysql::getInstance();
        
    }
    
    public  function addBiddingLook($info){
        
        if(empty($info)) return false;
        
        return $this->_db->insert(DB_FIX . 'bidding_look',$info);
    } 
    
     public  function replaceBiddingLook($info){
        
        if(empty($info)) return false;
        
        return $this->_db->replace(DB_FIX . 'bidding_look',$info);
    } 
    
   
    public function getBiddingLook($uid,$bidding_id){        
        $uid = (int)$uid;        
        $bidding_id = (int)$bidding_id;        
        return $this->_db->fetchRow("select  *  from `".DB_FIX."bidding_look` where uid = {$uid} and bidding_id ={$bidding_id} limit 1 ");
    }
    

    
    public function delBiddingLook($id){
         $id = (int)$id;
         return $this->_db->delete(DB_FIX."bidding_look"," id = {$id} ");
    }
    
    public function getBiddingLookSbyId($id){
        $id = (int)$id;
        return $this->_db->fetchAll("select b.company_name,a.uid,u.username from `".DB_FIX."bidding_look` a join  ".DB_FIX."users u ON (a.uid = u.uid) left join  ".DB_FIX."company b ON (a.uid = b.uid) where a.bidding_id = {$id} and  a.type = 1 ");
    }


    public function getBiddingLookList($col,$where,$order,$start=0,$limit=10){
        
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
        return $this->_db->fetchAll(" select {$colstr} from  `".DB_FIX."bidding_look` {$wherestr} {$orderby} limit {$start},{$limit} ");
    }
    
    public function getBiddingLookCount($where){
        
        $wherestr  = $this->getWhere($where);
        
        return $this->_db->fetchOne("  select count(1) from  `".DB_FIX."bidding_look` {$wherestr}");
    }
    
    
    private function getWhere($where){
         $local = array();
         if(isset($where['uid'])){
             $where['uid'] = (int)$where['uid'];
             $local[] = " uid = {$where['uid']}";
         }
         if(isset($where['bidding_id'])){
             $where['bidding_id'] = (int)$where['bidding_id'];
             $local[] = " bidding_id = {$where['bidding_id']}";
         }
         $wherestr = '';
         if(!empty($local)){
             $wherestr = " WHERE  ". join(' and ',$local);
         }
         return $wherestr;
    }
    
    
}