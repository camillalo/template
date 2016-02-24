<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ('Access Denied' );
}

class activityMdl{
    
    private  $_db;
    
    private static  $instance = null;
    
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new activityMdl();
        }
        
        return self::$instance;
    }
    
    private function __construct() {
        
        $this->_db = mysql::getInstance();
        
    }
    
    public  function addActivity($info){
        
        if(empty($info)) return false;
        
        return $this->_db->insert(DB_FIX . 'activity',$info);
    } 
    
    public function updateActivity($id,$info){
        
        if(empty($info)) return false;
        
        $id = (int)$id;
        
        return $this->_db->update(DB_FIX.'activity',$info," id = {$id} ");
    }
    
    public function getActivity($id){
        
        $id = (int)$id;
        
        return $this->_db->fetchRow("select  * from `".DB_FIX."activity` where id ={$id} limit 1 ");
    }
    
    public function getNewActivity(){
 
        
        return $this->_db->fetchRow("select  * from `".DB_FIX."activity`   order by  id  DESC limit 1 ");
    }
    

    
    public function delActivity($id){
         $id = (int)$id;
         return $this->_db->delete(DB_FIX."activity"," id = {$id} ");
    }
    
    
    public function getActivityList($col,$where,$order,$start=0,$limit=10){
        
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
        return $this->_db->fetchAll(" select {$colstr} from  `".DB_FIX."activity` {$wherestr} {$orderby} limit {$start},{$limit} ");
    }
    
    public function getActivityCount($where){
        
        $wherestr  = $this->getWhere($where);
        
        return $this->_db->fetchOne("  select count(1) from  `".DB_FIX."activity` {$wherestr}");
    }
    
    
    private function getWhere($where){
         $local = array();
         if(isset($where['keyword'])){
             $where['keyword'] = $this->_db->quote($where['keyword']);
             $local[] = " ( title like  '%{$where['keyword']}%'   ) ";
         }
         if(isset($where['type'])){
             $where['type'] = (int)$where['type'];
             $local[] = " type = {$where['type']} ";
         }
        
         if(isset($where['area_id'])){
             $where['area_id'] = (int)$where['area_id'];
             $local[] = " area_id = {$where['area_id']} ";
         }
         if(isset($where['st'])){
             $today = date('Y-m-d',NOWTIME);
             if($where['st'] === 1){
                 $local[] = " `bg_time`  >= '{$today}' ";
             }else{
                 $local[] = " `bg_time` < '{$today}' ";
             }
             
         }
         
         
         $wherestr = '';
         if(!empty($local)){
             $wherestr = " WHERE  ". join(' and ',$local);
         }
         return $wherestr;
    }
    
    
}