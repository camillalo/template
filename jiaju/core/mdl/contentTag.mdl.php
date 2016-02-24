<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ('Access Denied' );
}

class contentTagMdl{
    
    private  $_db;
    
    private static  $instance = null;
    
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new contentTagMdl();
        }
        
        return self::$instance;
    }
    
    private function __construct() {
        
        $this->_db = mysql::getInstance();
        
    }
    
    public  function addContentTag($info){
        
        if(empty($info)) return false;
        
        return $this->_db->insert(DB_FIX . 'content_tag',$info);
    } 
    
    
    
    public function updateContentTag($id,$info){
        
        if(empty($info)) return false;
        
        $id = (int)$id;
        
        return $this->_db->update(DB_FIX.'content_tag',$info," id = {$id} ");
    }
    
    public function getContentTag($id){
        
        $id = (int)$id;
        
        return $this->_db->fetchRow("select  * from `".DB_FIX."content_tag` where id ={$id} limit 1 ");
    }
    
    public function getContentTagIdByTagname($tag){
        
        $tag = $this->_db->quote($tag);
        
        return $this->_db->fetchOne("select  id from `".DB_FIX."content_tag`  where tag = '{$tag}'");
    }
    
    
    public function getContentTagsByTagids($ids = array()){
        if(empty($ids)) return array();
        $local  = array();
        foreach($ids as $v){
            $local [] = (int)$v;
        }
        $idstr = join(',',$local);
        return $this->_db->fetchAll(" select  * from `".DB_FIX."content_tag` where id in($idstr)");
    }
    
    
    public function delContentTag($id){
        
         $id = (int)$id;
         
         return $this->_db->delete(DB_FIX."content_tag"," id = {$id} ");
    }
    
    
    public function getContentTagList($col,$where,$order,$start=0,$limit=10){
        
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
        return $this->_db->fetchAll(" select {$colstr} from  `".DB_FIX."content_tag` {$wherestr} {$orderby} limit {$start},{$limit} ");
    }
    
    public function getContentTagCount($where){
        
        $wherestr  = $this->getWhere($where);
        
        return $this->_db->fetchOne("  select count(1) from  `".DB_FIX."content_tag` {$wherestr}");
    }
    
    
    private function getWhere($where){
         $local = array();
         if(isset($where['keyword'])){
             $where['keyword'] = $this->_db->quote($where['keyword']);
             $local[] = " ( tag like  '%{$where['keyword']}%'   ) ";
         }
         
         $wherestr = '';
         if(!empty($local)){
             $wherestr = " WHERE  ". join(' and ',$local);
         }
         return $wherestr;
    }
    
    
}