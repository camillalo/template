<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ('Access Denied' );
}

class biddingBidMdl{
    
    private  $_db;
    
    private static  $instance = null;
    
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new biddingBidMdl();
        }
        
        return self::$instance;
    }
    
    private function __construct() {
        
        $this->_db = mysql::getInstance();
        
    }
    
    public  function addBiddingBid($info){
        
        if(empty($info)) return false;
        
        return $this->_db->insert(DB_FIX . 'bidding_bid',$info);
    } 
    
    public function updateBiddingBid($id,$info){
        
        if(empty($info)) return false;
        
        $id = (int)$id;
        
        return $this->_db->update(DB_FIX.'bidding_bid',$info," id = {$id} ");
    }
    
    public function getBiddingBid($id){
        
        $id = (int)$id;
        
        return $this->_db->fetchRow("select  * from `".DB_FIX."bidding_bid` where id ={$id} limit 1 ");
    }
    public function getBiddingBidByUidBidId($uid,$bid){
        $uid = (int)$uid;
        $bid = (int)$bid;
        return $this->_db->fetchRow("select  * from `".DB_FIX."bidding_bid` where uid ={$uid} and bid = {$bid} limit 1 ");
    }

    public function getBiddingBidByUidBidIds($uid,$bids = array()){
        $uid = (int)$uid;
        if(empty($bids)) return array();
        foreach($bids as $k=>$v){
            $bids[$k] = (int)$v;
        }
        $bidstr = join(',',$bids);
        $datas =  $this->_db->fetchAll("select  bid,is_win from `".DB_FIX."bidding_bid` where uid ={$uid} and bid in ({$bidstr})  ");
        $return = array();
        foreach($datas as $v){
            $return [$v['bid']] = $v;
        }
        return $return;
    }
    
    public function delBiddingBid($id){
         $id = (int)$id;
         return $this->_db->delete(DB_FIX."bidding_bid"," id = {$id} ");
    }
    
    public function getCounts($uids){
        if(empty($uids)) return array();
        foreach($uids as $k=>$v){
            $uids[$k] = (int)$v;
        }
        $uidstr = join(',',$uids);
        $datas = $this->_db->fetchAll("SELECT uid,sum(is_shortlisted) as sn ,sum(is_win) as wn,count(1) as cn FROM `".DB_FIX."bidding_bid` where uid in({$uidstr}) GROUP BY uid;");
        $return  = array();
        foreach($datas as $v){
            $return[$v['uid']] = $v;
        }
        return $return;
    }
    
    public function getBiddingBidList($col,$where,$order,$start=0,$limit=10){
        
        if(empty($col)){
            $colstr ='*';
        }else{
            $colstr = join(',',$col);
        }
        $wherestr  = $this->getWhere($where);
        if(empty($order)){
            $orderby = ' order by  a.id  desc ';
        }else{
            $local = array();
            foreach($order  as $k => $v){
                $local [] = " {$k} {$v} ";
            }
            $orderby = ' order by '.join(',',$local);
        }
        return $this->_db->fetchAll(" select {$colstr} from  `".DB_FIX."bidding_bid` a 
                                    join   `".DB_FIX."users` b on  (a.uid = b.uid)
                                    {$wherestr} {$orderby} limit {$start},{$limit} ");
    }
    
    public function getBiddingBidCount($where){
        
        $wherestr  = $this->getWhere($where);
        
        return $this->_db->fetchOne("  select count(1) from  `".DB_FIX."bidding_bid` a 
                                    join   `".DB_FIX."users` b on  (a.uid = b.uid) {$wherestr}");
    }
    
    
    private function getWhere($where){
         $local = array();
         if(isset($where['bid'])){
             $where['bid'] = (int)$where['bid'];
             $local[] = " a.bid = {$where['bid']} ";
         }
         if(isset($where['uid'])){
             $where['uid'] = (int)$where['uid'];
             $local[] = " a.uid = {$where['uid']} ";
         }
         if(isset($where['is_show'])){
             $where['is_show'] = (int)$where['is_show'];
             $local[] = " a.is_show = {$where['is_show']} ";
         }
          if(isset($where['is_shortlisted'])){
             $where['is_shortlisted'] = (int)$where['is_shortlisted'];
             $local[] = " a.is_shortlisted = {$where['is_shortlisted']} ";
         }
         if(isset($where['is_win'])){
             $where['is_win'] = (int)$where['is_win'];
             $local[] = " a.is_win = {$where['is_win']} ";
         }
         $wherestr = '';
         if(!empty($local)){
             $wherestr = " WHERE  ". join(' and ',$local);
         }
         return $wherestr;
    }
    
    
}