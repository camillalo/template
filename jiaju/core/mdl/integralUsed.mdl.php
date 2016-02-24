<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ('Access Denied' );
}

class integralUsedMdl{
    
    private  $_db;
    
    private static  $instance = null;
    
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new integralUsedMdl();
        }
        
        return self::$instance;
    }
    
    private function __construct() {
        
        $this->_db = mysql::getInstance();
        
    }
    
    public  function addIntegralUsed($info){
        
        if(empty($info)) return false;
        
        return $this->_db->insert(DB_FIX . 'integral_used',$info);
    } 
    
    public function updateIntegralUsed($id,$info){
        
        if(empty($info)) return false;
        
        $id = (int)$id;
        
        return $this->_db->update(DB_FIX.'integral_used',$info," id = {$id} ");
    }
    
    public function getIntegralUsed($id){
        
        $id = (int)$id;
        
        return $this->_db->fetchRow("select  * from `".DB_FIX."integral_used` where id ={$id} limit 1 ");
    }
    

    
    public function delIntegralUsed($id){
         $id = (int)$id;
         return $this->_db->delete(DB_FIX."integral_used"," id = {$id} ");
    }
    
    
    public function getIntegralUsedList($col,$where,$order,$start=0,$limit=10){
        
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
        return $this->_db->fetchAll(" select {$colstr} from  `".DB_FIX."integral_used` a join `".DB_FIX."users` b on (a.uid = b.uid) {$wherestr} {$orderby} limit {$start},{$limit} ");
    }
    
    public function getIntegralUsedCount($where){
        
        $wherestr  = $this->getWhere($where);
        
        return $this->_db->fetchOne("  select count(1) from  `".DB_FIX."integral_used` a join `".DB_FIX."users` b on (a.uid = b.uid) {$wherestr}");
    }
    
    
    private function getWhere($where){
         $local = array();
         if(isset($where['keyword'])){
             $where['keyword'] = $this->_db->quote($where['keyword']);
             $local[]= " b.username like '%{$where['keyword']}%' ";
         }
         if(isset($where['uid'])){
             $where['uid'] =(int)($where['uid']);
             $local[]= " a.uid = {$where['uid']} ";
         }
         $wherestr = '';
         if(!empty($local)){
             $wherestr = " WHERE  ". join(' and ',$local);
         }
         return $wherestr;
    }
    
    
}