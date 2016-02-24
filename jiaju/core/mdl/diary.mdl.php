<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ('Access Denied' );
}

class diaryMdl{
    
    private  $_db;
    
    private static  $instance = null;
    
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new diaryMdl();
        }
        
        return self::$instance;
    }
    
    private function __construct() {
        
        $this->_db = mysql::getInstance();
        
    }
    
    public  function addDiary($info){
        
        if(empty($info)) return false;
        
        return $this->_db->insert(DB_FIX . 'diary',$info);
    } 
    
    public function updateDiary($id,$info){
        
        if(empty($info)) return false;
        
        $id = (int)$id;
        
        return $this->_db->update(DB_FIX.'diary',$info," id = {$id} ");
    }
    
    public function getDiary($id){
        
        $id = (int)$id;
        
        return $this->_db->fetchRow("select  * from `".DB_FIX."diary` where id ={$id} limit 1 ");
    }
    

    
    public function delDiary($id){
         $id = (int)$id;
         return $this->_db->delete(DB_FIX."diary"," id = {$id} ");
    }
    
    
    public function getDiaryList($col,$where,$order,$start=0,$limit=10){
        
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
        return $this->_db->fetchAll(" select {$colstr} from  `".DB_FIX."diary` {$wherestr} {$orderby} limit {$start},{$limit} ");
    }
    
    public function getDiaryCount($where){
        
        $wherestr  = $this->getWhere($where);
        
        return $this->_db->fetchOne("  select count(1) from  `".DB_FIX."diary` {$wherestr}");
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
         if(isset($where['uid'])){
             $where['uid'] = (int)$where['uid'];
             $local[] = " uid = {$where['uid']} ";
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