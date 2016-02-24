<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ('Access Denied' );
}

class biddingMdl{
    
    private  $_db;
    
    private static  $instance = null;
    
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new biddingMdl();
        }
        
        return self::$instance;
    }
    
    private function __construct() {
        
        $this->_db = mysql::getInstance();
        
    }
    
    public  function addBidding($info){
        
        if(empty($info)) return false;
        
        return $this->_db->insert(DB_FIX . 'bidding',$info);
    } 
    
    public function updateBidding($id,$info){
        
        if(empty($info)) return false;
        
        $id = (int)$id;
        
        return $this->_db->update(DB_FIX.'bidding',$info," id = {$id} ");
    }
    
    public function getBidding($id){
        
        $id = (int)$id;
        
        return $this->_db->fetchRow("select  * from `".DB_FIX."bidding` where id ={$id} limit 1 ");
    }
    
    public function getNewBiddings($num){
        $num = (int)$num;
       
        return $this->_db->fetchAll("select * from `".DB_FIX."bidding` WHERE is_show = 1  order by id desc limit 0,{$num}  ");        
    }
    
    public function getBiddingByIds($ids){
        if(empty($ids)) return array();
        foreach($ids as $k=>$v){
            $ids[$k] = (int)$v;
        }
        $str = join(',',$ids);
        return $this->_db->fetchAll("select * from `".DB_FIX."bidding` WHERE is_show = 1 AND id in({$str}) order by  id desc  ");
    }
    
    
    public function delBidding($id){
         $id = (int)$id;
         return $this->_db->delete(DB_FIX."bidding"," id = {$id} ");
    }
    
    
    public function getBiddingList($col,$where,$order,$start=0,$limit=10){
        
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
        return $this->_db->fetchAll(" select {$colstr} from  `".DB_FIX."bidding` {$wherestr} {$orderby} limit {$start},{$limit} ");
    }
    
    public function getBiddingCount($where){
        
        $wherestr  = $this->getWhere($where);
        
        return $this->_db->fetchOne("  select count(1) from  `".DB_FIX."bidding` {$wherestr}");
    }
    
    
    private function getWhere($where){
         $local = array();
         if(isset($where['keyword'])){
             $where['keyword'] = $this->_db->quote($where['keyword']);
             $local[] = " ( name like  '%{$where['keyword']}%'   or mobile like  '%{$where['keyword']}%'   ) ";
         }
         if(isset($where['is_key'])){
             $where['is_key'] = (int)$where['is_key'];
             $local[] = " is_key = {$where['is_key']} ";
         }
         if(isset($where['is_supervision'])){
             $where['is_supervision'] = (int)$where['is_supervision'];
             $local[] = " is_supervision = {$where['is_supervision']} ";
         }
         if(isset($where['is_material'])){
             $where['is_material'] = (int)$where['is_material'];
             $local[] = " is_material = {$where['is_material']} ";
         }
         if(isset($where['is_show'])){
             $where['is_show'] = (int)$where['is_show'];
             $local[] = " is_show = {$where['is_show']} ";
         }
         if(isset($where['type_root'])){
             $where['type_root'] = (int)$where['type_root'];
             $local[] = " type_root = {$where['type_root']} ";
         }
        
         if(isset($where['area_id'])){
             $where['area_id'] = (int)$where['area_id'];
             $local[] = " area_id = {$where['area_id']} ";
         }
          if(isset($where['uid'])){
             $where['uid'] = (int)$where['uid'];
             $local[] = " uid = {$where['uid']} ";
         }

         
         $wherestr = '';
         if(!empty($local)){
             $wherestr = " WHERE  ". join(' and ',$local);
         }
         return $wherestr;
    }
    
    
}