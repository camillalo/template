<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ('Access Denied' );
}

class integralShopMdl{
    
    private  $_db;
    
    private static  $instance = null;
    
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new integralShopMdl();
        }
        
        return self::$instance;
    }
    
    private function __construct() {
        
        $this->_db = mysql::getInstance();
        
    }
    
    public  function addIntegralShop($info){
        
        if(empty($info)) return false;
        
        return $this->_db->insert(DB_FIX . 'integral_shop',$info);
    } 
    
    public function updateIntegralShop($id,$info){
        
        if(empty($info)) return false;
        
        $id = (int)$id;
        
        return $this->_db->update(DB_FIX.'integral_shop',$info," id = {$id} ");
    }
    
    public function getIntegralShop($id){
        
        $id = (int)$id;
        
        return $this->_db->fetchRow("select  * from `".DB_FIX."integral_shop` where id ={$id} limit 1 ");
    }
    
    public function getAllIntegralShop(){
        return $this->_db->fetchAll("select  * from `".DB_FIX."integral_shop` where is_show = 1");
    }
    

    
    public function delIntegralShop($id){
         $id = (int)$id;
         return $this->_db->delete(DB_FIX."integral_shop"," id = {$id} ");
    }
    
    
    public function getIntegralShopList($col,$where,$order,$start=0,$limit=10){
        
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
        return $this->_db->fetchAll(" select {$colstr} from  `".DB_FIX."integral_shop` {$wherestr} {$orderby} limit {$start},{$limit} ");
    }
    
    public function getIntegralShopCount($where){
        
        $wherestr  = $this->getWhere($where);
        
        return $this->_db->fetchOne("  select count(1) from  `".DB_FIX."integral_shop` {$wherestr}");
    }
    
    
    private function getWhere($where){
         $local = array();
         if(isset($where['keyword'])){
             $where['keyword'] = $this->_db->quote($where['keyword']);
             $local[] = " ( product_name like  '%{$where['keyword']}%'   ) ";
         }
          if(isset($where['is_show'])){
             $where['is_show'] = (int)$where['is_show'];
             $local[] = " is_show = {$where['is_show']} ";
         }
         
         $wherestr = '';
         if(!empty($local)){
             $wherestr = " WHERE  ". join(' and ',$local);
         }
         return $wherestr;
    }
    
    
}