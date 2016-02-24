<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ('Access Denied' );
}

class activityJoinMdl{
    
    private  $_db;
    
    private static  $instance = null;
    
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new activityJoinMdl();
        }
        
        return self::$instance;
    }
    
    private function __construct() {
        
        $this->_db = mysql::getInstance();
        
    }
    
    public  function addActivityJoin($info){
        
        if(empty($info)) return false;
        
        return $this->_db->insert(DB_FIX . 'activity_join',$info);
    } 
    
    public function updateActivityJoin($id,$info){
        
        if(empty($info)) return false;
        
        $id = (int)$id;
        
        return $this->_db->update(DB_FIX.'activity_join',$info," id = {$id} ");
    }
    
   public function checkIp($activity_id,$ip){
       $activity_id = (int)$activity_id;
       $ip = $this->_db->quote($ip);
       return $this->_db->fetchOne("select count(1) from  `".DB_FIX."activity_join` where activity_id ={$activity_id} and ip = '{$ip}'");
   }
   
   public function getLastJoin($activity_id,$num){
       $activity_id = (int)$activity_id;
       $num = (int)$num;
       return $this->_db->fetchAll("select  * from `".DB_FIX."activity_join` where activity_id ={$activity_id} order by id desc limit {$num} ");
   }
    public function getActivityJoin($id){
        
        $id = (int)$id;
        
        return $this->_db->fetchRow("select  * from `".DB_FIX."activity_join` where id ={$id} limit 1 ");
    }
    

    
    public function delActivityJoin($id){
         $id = (int)$id;
         return $this->_db->delete(DB_FIX."activity_join"," id = {$id} ");
    }
    
    
    public function getActivityJoinList($col,$where,$order,$start=0,$limit=10){
        
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
        return $this->_db->fetchAll(" select {$colstr} from  `".DB_FIX."activity_join` {$wherestr} {$orderby} limit {$start},{$limit} ");
    }
    
    public function getActivityJoinCount($where){
        
        $wherestr  = $this->getWhere($where);
        
        return $this->_db->fetchOne("  select count(1) from  `".DB_FIX."activity_join` {$wherestr}");
    }
    
    
    private function getWhere($where){
         $local = array();
         if(isset($where['keyword'])){
             $where['keyword'] = $this->_db->quote($where['keyword']);
         }
         if(isset($where['activity_id'])){
             $where['activity_id'] = (int)$where['activity_id'];
             $local[] = " activity_id = {$where['activity_id']} ";
         }
         
         $wherestr = '';
         if(!empty($local)){
             $wherestr = " WHERE  ". join(' and ',$local);
         }
         return $wherestr;
    }
    
    
}