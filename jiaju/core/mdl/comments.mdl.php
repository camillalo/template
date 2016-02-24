<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ('Access Denied' );
}

class commentsMdl{
    
    private  $_db;
    
    private static  $instance = null;
    
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new commentsMdl();
        }
        
        return self::$instance;
    }
    
    private function __construct() {
        
        $this->_db = mysql::getInstance();
        
    }
    
    public  function addComments($info){
        
        if(empty($info)) return false;
        
        return $this->_db->insert(DB_FIX . 'comments',$info);
    } 
    
    public function updateComments($id,$info){
        
        if(empty($info)) return false;
        
        $id = (int)$id;
        
        return $this->_db->update(DB_FIX.'comments',$info," id = {$id} ");
    }
    
    public function getComments($id){
        
        $id = (int)$id;
        
        return $this->_db->fetchRow("select  * from `".DB_FIX."comments` where id ={$id} limit 1 ");
    }
    

    
    public function delComments($id){
         $id = (int)$id;
         return $this->_db->delete(DB_FIX."comments"," id = {$id} ");
    }
    
    
    public function getCommentsList($col,$where,$order,$start=0,$limit=10){
        
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
        return $this->_db->fetchAll(" select {$colstr} from  `".DB_FIX."comments` a 
                                      join   `".DB_FIX."users` b on (a.uid = b.uid)
                                      {$wherestr} {$orderby} limit {$start},{$limit} ");
    }
    
    public function getCommentsCount($where){
        
        $wherestr  = $this->getWhere($where);
        
        return $this->_db->fetchOne("  select count(1) from  `".DB_FIX."comments`  a
                                        join   `".DB_FIX."users` b on (a.uid = b.uid)
                                        {$wherestr}");
    }
    
    
    private function getWhere($where){
         $local = array();
         if(isset($where['keyword'])){
             $where['keyword'] = $this->_db->quote($where['keyword']);
             $local[] = " ( a.uid like  '%{$where['keyword']}%'  or b.username like '%{$where['keyword']}%'  ) ";
         }
         if(isset($where['is_show'])){
             $where['is_show'] = (int)$where['is_show'];
             $local[] = " a.is_show = {$where['is_show']} ";
         }
         if(isset($where['type'])){
             $where['type'] = (int)$where['type'];
             $local[] = " a.type = {$where['type']} ";
         }
         if(isset($where['type_id'])){
             $where['type_id'] = (int)$where['type_id'];
             $local[] = " a.type_id = {$where['type_id']} ";
         }
         if(isset($where['uid'])){
             $where['uid'] = (int)$where['uid'];
             $local[] = " a.uid = {$where['uid']} ";
         }
         
         $wherestr = '';
         if(!empty($local)){
             $wherestr = " WHERE  ". join(' and ',$local);
         }
         return $wherestr;
    }
    
    
}