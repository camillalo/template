<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ('Access Denied' );
}

class askMdl{
    
    private  $_db;
    
    private static  $instance = null;
    
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new askMdl();
        }
        
        return self::$instance;
    }
    
    private function __construct() {
        
        $this->_db = mysql::getInstance();
        
    }
    
    public  function addAsk($info){
        
        if(empty($info)) return false;
        
        return $this->_db->insert(DB_FIX . 'ask',$info);
    } 
    
    public function updateAsk($id,$info){
        
        if(empty($info)) return false;
        
        $id = (int)$id;
        
        return $this->_db->update(DB_FIX.'ask',$info," id = {$id} ");
    }
    
    public function getAsk($id){
        
        $id = (int)$id;
        
        return $this->_db->fetchRow("select  * from `".DB_FIX."ask` where id ={$id} limit 1 ");
    }
    public function updateOrderUp($id){
        $id = (int)$id;
        $max = $this->_db->fetchOne("select max(orderby) from `".DB_FIX."ask` ");
        $max = $max+1;
        return $this->_db->update("update  `".DB_FIX."ask` set orderby = {$max} where id ={$id} limit 1 ");
    }
    

    public function askPv($id){
        $id = (int)$id;
        return $this->_db->update("update  `".DB_FIX."ask` set pv=(pv+1) where id ={$id} limit 1 ");
    }
     public function askNum($id){
        $id = (int)$id;
        return $this->_db->update("update  `".DB_FIX."ask` set num=(num+1) where id ={$id} limit 1 ");
    }
    
    public function delAsk($id){
         $id = (int)$id;
         return $this->_db->delete(DB_FIX."ask"," id = {$id} ");
    }
    
    
    public function getAskList($col,$where,$order,$start=0,$limit=10){
        
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
        return $this->_db->fetchAll(" select {$colstr} from  `".DB_FIX."ask` {$wherestr} {$orderby} limit {$start},{$limit} ");
    }
    
    public function getAskCount($where){
        
        $wherestr  = $this->getWhere($where);
        
        return $this->_db->fetchOne("  select count(1) from  `".DB_FIX."ask` {$wherestr}");
    }
    
    public function getAsksByIds($ids){
        if(empty($ids)) return array();
        $local = array();
        foreach($ids as $v){
            $local [] = (int) $v;
        }
        $str = join(',',$local);
        return $this->_db->fetchAll("select  id,title,num,pv,description,create_time  from `".DB_FIX."ask` where id in({$str}) ");
    }
    
    private function getWhere($where){
         $local = array();
         if(isset($where['keyword'])){
             $where['keyword'] = $this->_db->quote($where['keyword']);
             $local[] = " ( title like  '%{$where['keyword']}%'   ) ";
         }
         if(isset($where['cate_id'])){
             $where['cate_id'] = (int)$where['cate_id'];
             $local[] = " cate_id = {$where['cate_id']} ";
         }
         if(isset($where['status'])){
             $where['status'] = (int)$where['status'];
             $local[] = " status = {$where['status']} ";
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