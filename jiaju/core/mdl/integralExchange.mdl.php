<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ('Access Denied' );
}

class integralExchangeMdl{
    
    private  $_db;
    
    private static  $instance = null;
    
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new integralExchangeMdl();
        }
        
        return self::$instance;
    }
    
    private function __construct() {
        
        $this->_db = mysql::getInstance();
        
    }
    
    public  function addIntegralExchange($info){
        
        if(empty($info)) return false;
        
        return $this->_db->insert(DB_FIX . 'integral_exchange',$info);
    } 
    
    public function updateIntegralExchange($id,$info){
        
        if(empty($info)) return false;
        
        $id = (int)$id;
        
        return $this->_db->update(DB_FIX.'integral_exchange',$info," id = {$id} ");
    }
    
    public function getIntegralExchange($id){
        
        $id = (int)$id;
        
        return $this->_db->fetchRow("select  * from `".DB_FIX."integral_exchange` where id ={$id} limit 1 ");
    }
    
    
    public function getTopExchange($num){
        $num = (int)$num;
        return $this->_db->fetchCol("select product_id from (SELECT sum(1),product_id FROM `".DB_FIX."integral_exchange` GROUP BY product_id ORDER BY sum(1) DESC limit 0,{$num} )tbl; ");
    }
    
    
    public function delIntegralExchange($id){
         $id = (int)$id;
         return $this->_db->delete(DB_FIX."integral_exchange"," id = {$id} ");
    }
    
    
    public function getIntegralExchangeList($col,$where,$order,$start=0,$limit=10){
        
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
        return $this->_db->fetchAll(" select {$colstr} from  `".DB_FIX."integral_exchange`  a
                                join     `".DB_FIX."users` b on (a.uid = b.uid)
                                join    `".DB_FIX."integral_shop` c ON (a.product_id = c.id)
                                {$wherestr} {$orderby} limit {$start},{$limit} ");
    }
    
    public function getIntegralExchangeCount($where){
        
        $wherestr  = $this->getWhere($where);
        
        return $this->_db->fetchOne("  select count(1) from  `".DB_FIX."integral_exchange`  a 
         join     `".DB_FIX."users` b on (a.uid = b.uid)
                                join    `".DB_FIX."integral_shop` c ON (a.product_id = c.id)
        {$wherestr}");
    }
    
    
    private function getWhere($where){
         $local = array();
         if(isset($where['keyword'])){
             $where['keyword'] = $this->_db->quote($where['keyword']);
             $local[] = " ( b.username like  '%{$where['keyword']}%'   or  c.product_name like  '%{$where['keyword']}%'   or  a.name like  '%{$where['keyword']}%'   ) ";
         }
         if(isset($where['uid'])){
             $where['uid'] = (int)$where['uid'];
             $local[] = " a.uid = {$where['uid']} ";
         }
         if(isset($where['type'])){
             $where['type'] = (int)$where['type'];
             $local[] = " a.type = {$where['type']} ";
         }
         if(isset($where['status'])){
             $where['status'] = (int)$where['status'];
             $local[] = " a.status = {$where['status']} ";
         }
         
         $wherestr = '';
         if(!empty($local)){
             $wherestr = " WHERE  ". join(' and ',$local);
         }
         return $wherestr;
    }
    
    
}