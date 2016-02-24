<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ( 'Access Denied' );
}

class contentMdl{
    
    private  $_db;
    
    private static  $instance = null;
    
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new contentMdl();
        }
        
        return self::$instance;
    }
    
    private function __construct() {
        
        $this->_db = mysql::getInstance();
        
    }
    
    public  function addContent($info){
        
        if(empty($info)) return false;
        
        return $this->_db->insert(DB_FIX . 'content',$info);
    } 
    
    public function updateContent($id,$info){
        
        if(empty($info)) return false;
        
        $id = (int)$id;
        
        return $this->_db->update(DB_FIX.'content',$info," content_id = {$id} ");
    }
    
    public function updateContentPv($id){
         $id = (int)$id;
         return $this->_db->update("update ".DB_FIX."content set pv_num =(pv_num +1 ) where  content_id = {$id} ");
    }
    
    public function getContent($id){
        
        $id = (int)$id;
        
        return $this->_db->fetchRow("select  * from `".DB_FIX."content` where content_id ={$id} limit 1 ");
    }
    
    public function getContentsByIds($ids){
        if(empty($ids)) return array();
        $local = array();
        foreach($ids as $v){
            $local [] = (int) $v;
        }
        $str = join(',',$local);
        return $this->_db->fetchAll("select  content_id,title,description,create_time from `".DB_FIX."content` where content_id in({$str}) ");
    }
    
    public function getTopContent($id){
        $id = (int)$id;
        
        return $this->_db->fetchRow("select  * from `".DB_FIX."content` where content_id < {$id} order by content_id desc limit 1 ");
    }
    public function getNextContent($id){
        $id = (int)$id;
        
        return $this->_db->fetchRow("select  * from `".DB_FIX."content` where content_id > {$id} limit 1 ");
    }
    
    public function getContentsByCateId($category_id,$num,$order = 'desc'){
        $category_id = (int)$category_id;
        $num = (int)$num;
        $order = $this->_db->quote($order);
        return $this->_db->fetchAll("select  content_id,title,description,create_time from `".DB_FIX."content` where category_id  = {$category_id} order by content_id {$order}  limit 0,{$num} ");
    }

    
    public function delContent($id){
         $id = (int)$id;
         return $this->_db->delete(DB_FIX."content"," content_id = {$id} ");
    }
    
    
    public function getContentList($col,$where,$order,$start=0,$limit=10){
        
        if(empty($col)){
            $colstr = '*';
        }else{
            $colstr = join(',',$col);
        }
        $wherestr  = $this->getWhere($where);
        if(empty($order)){
            $orderby = ' order by  content_id  desc ';
        }else{
            $local = array();
            foreach($order  as $k => $v){
                $local [] = " {$k} {$v} ";
            }
            $orderby = ' order by '.join(',',$local);
        }
        return $this->_db->fetchAll(" select {$colstr} from  `".DB_FIX."content` {$wherestr} {$orderby} limit {$start},{$limit} ");
    }
    
    public function getContentCount($where){
        
        $wherestr  = $this->getWhere($where);
        
        return $this->_db->fetchOne("  select count(1) from  `".DB_FIX."content` {$wherestr}");
    }
    
    
    private function getWhere($where){
         $local = array();
         if(isset($where['keyword'])){
             $where['keyword'] = $this->_db->quote($where['keyword']);
             $local[] = " ( title like '%{$where['keyword']}%') ";
         }
         if(!empty($where['category_id'])){
             $where['category_id'] = (int)$where['category_id'];
             $local[] = " category_id = {$where['category_id']} ";
         }
         if(!empty($where['last_category_id'])){
             foreach($where['last_category_id'] as $k=>$v){
                $where['last_category_id'][$k] = (int)$v;
             }
             $idstr = join(',',$where['last_category_id']);
             $local[] = " category_id in ({$idstr}) ";
         }
         
         $wherestr = '';
         if(!empty($local)){
             $wherestr = " WHERE  ". join(' and ',$local);
         }
         return $wherestr;
    }
    
    
}