<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ('Access Denied' );
}

class quantityRoomMdl{
    
    private  $_db;
    
    private static  $instance = null;
    
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new quantityRoomMdl();
        }
        
        return self::$instance;
    }
    
    private function __construct() {
        
        $this->_db = mysql::getInstance();
        
    }
    
    public  function addQuantityRoom($info){
        
        if(empty($info)) return false;
        
        return $this->_db->insert(DB_FIX . 'quantity_room',$info);
    } 
    
    public function updateQuantityRoom($id,$info){
        
        if(empty($info)) return false;
        
        $id = (int)$id;
        
        return $this->_db->update(DB_FIX.'quantity_room',$info," id = {$id} ");
    }
    public function getQuantityRoomCountPair($ids){
       if(empty($ids))  return array();
       foreach($ids as $k=>$v){
           $ids[$k] = (int)$v;
       }
       $idstr = join(',',$ids);
       return $this->_db->fetchPair("select  company_id ,count(1) as num  from `".DB_FIX."quantity_room`  where company_id in({$idstr})  group by company_id  ");
   }
    public function getQuantityRoom($id){
        
        $id = (int)$id;
        
        return $this->_db->fetchRow("select  * from `".DB_FIX."quantity_room` where id ={$id} limit 1 ");
    }
    

    
    public function delQuantityRoom($id){
         $id = (int)$id;
         return $this->_db->delete(DB_FIX."quantity_room"," id = {$id} ");
    }
    
    
    public function getQuantityRoomList($col,$where,$order,$start=0,$limit=10){
        
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
        return $this->_db->fetchAll(" select {$colstr} from  `".DB_FIX."quantity_room` a  
                                    join `".DB_FIX."company` b ON (a.company_id = b.uid)  
                                    left join `".DB_FIX."users`  c  ON (a.uid = c.uid )
                                     {$wherestr} {$orderby} limit {$start},{$limit} ");
    }
    
    public function getQuantityRoomCount($where){
        
        $wherestr  = $this->getWhere($where);
        
        return $this->_db->fetchOne("  select count(1) from  `".DB_FIX."quantity_room` a join `".DB_FIX."company` b ON (a.company_id = b.uid)   left join `".DB_FIX."users`  c  ON (a.uid = c.uid ) {$wherestr}");
    }
    
    
    private function getWhere($where){
         $local = array();
         if(isset($where['keyword'])){
             $where['keyword'] = $this->_db->quote($where['keyword']);
             $local[] = " ( a.name like  '%{$where['keyword']}%'   or  a.tel like  '%{$where['keyword']}%'   or  a.date like  '%{$where['keyword']}%' or  b.company_name like  '%{$where['keyword']}%'   ) ";
         }
         if(isset($where['uid'])){
             $where['uid'] = (int)$where['uid'];
             $local[] = " a.uid = {$where['uid']} ";
         }
         if(isset($where['company_id'])){
             $where['company_id'] = (int)$where['company_id'];
             $local[] = " a.company_id = {$where['company_id']} ";
         }
         
         $wherestr = '';
         if(!empty($local)){
             $wherestr = " WHERE  ". join(' and ',$local);
         }
         return $wherestr;
    }
    
    
}