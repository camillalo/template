<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ('Access Denied' );
}

class bookingDesignMdl{
    
    private  $_db;
    
    private static  $instance = null;
    
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new bookingDesignMdl();
        }
        
        return self::$instance;
    }
    
    private function __construct() {
        
        $this->_db = mysql::getInstance();
        
    }
    
    public  function addBookingDesign($info){
        
        if(empty($info)) return false;
        
        return $this->_db->insert(DB_FIX . 'booking_design',$info);
    } 
    
    public function updateBookingDesign($id,$info){
        
        if(empty($info)) return false;
        
        $id = (int)$id;
        
        return $this->_db->update(DB_FIX.'booking_design',$info," id = {$id} ");
    }
    
    public function getBookingDesign($id){
        
        $id = (int)$id;
        
        return $this->_db->fetchRow("select  * from `".DB_FIX."booking_design` where id ={$id} limit 1 ");
    }
    

    
    public function delBookingDesign($id){
         $id = (int)$id;
         return $this->_db->delete(DB_FIX."booking_design"," id = {$id} ");
    }
    
    
    public function getBookingDesignList($col,$where,$order,$start=0,$limit=10){
        
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
        return $this->_db->fetchAll(" select {$colstr} from  `".DB_FIX."booking_design` {$wherestr} {$orderby} limit {$start},{$limit} ");
    }
    
    public function getBookingDesignCount($where){
        
        $wherestr  = $this->getWhere($where);
        
        return $this->_db->fetchOne("  select count(1) from  `".DB_FIX."booking_design` {$wherestr}");
    }
    
    
    private function getWhere($where){
         $local = array();
         if(isset($where['keyword'])){
             $where['keyword'] = $this->_db->quote($where['keyword']);
             $local[] = " ( name like  '%{$where['keyword']}%'   or tel like  '%{$where['keyword']}%'   ) ";
         }
         if(isset($where['designer_id'])){
             $where['designer_id'] = (int)$where['designer_id'];
             $local[] = " designer_id = {$where['designer_id']} ";
         }
         if(isset($where['uid'])){
             $where['uid'] = (int)$where['uid'];
             $local[] = " uid = {$where['uid']} ";
         }
         if(isset($where['company_id'])){
             $where['company_id'] = (int)$where['company_id'];
             $local[] = " company_id = {$where['company_id']} ";
         }
         
         $wherestr = '';
         if(!empty($local)){
             $wherestr = " WHERE  ". join(' and ',$local);
         }
         return $wherestr;
    }
    
    
}