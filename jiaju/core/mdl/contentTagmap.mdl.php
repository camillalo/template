<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ('Access Denied' );
}

class contentTagmapMdl{
    
    private  $_db;
    
    private static  $instance = null;
    
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new contentTagmapMdl();
        }
        
        return self::$instance;
    }
    
    private function __construct() {
        
        $this->_db = mysql::getInstance();
        
    }
    
    public  function replaceContentTagmap($info){
        
        if(empty($info)) return false;
        
        return $this->_db->replace(DB_FIX . 'content_tagmap',$info);
    } 
    
    public function getAllContentTagmap($content_id){
        
        $content_id = (int)$content_id;
        
        return $this->_db->fetchAll(" select  a.tag_id,b.* from ".DB_FIX ."content_tagmap a  join   ".DB_FIX ."content_tag  b on (a.tag_id =b.id)  where a.content_id = {$content_id}");
    }
    
    public function getHotTagIds($num){
        $num = (int)$num;
        
        return $this->_db->fetchAll("select a.id,a.tag from (select  tag_id,count(1) from ".DB_FIX ."content_tagmap  group by  tag_id order  by count(1) desc limit 0,{$num})tb1 
                                     join  ".DB_FIX ."content_tag a ON (tb1.tag_id = a.id)
        " );
    }
    
    
    public function delContentTagmap($id){
         $id = (int)$id;
         return $this->_db->delete(DB_FIX."content_tagmap"," tag_id = {$id} ");
    }
    
    public function delContentTagmapByContentId($id){
        $id = (int)$id;
        return $this->_db->delete(DB_FIX."content_tagmap"," content_id = {$id} ");
    }
    
    public function getContentTagMapList($col,$where,$order,$start=0,$limit=10){
        
        if(empty($col)){
            $colstr ='*';
        }else{
            $colstr = join(',',$col);
        }
        $wherestr  = $this->getWhere($where);
        if(empty($order)){
            $orderby = ' order by  b.content_id  desc ';
        }else{
            $local = array();
            foreach($order  as $k => $v){
                $local [] = " {$k} {$v} ";
            }
            $orderby = ' order by '.join(',',$local);
        }
        return $this->_db->fetchAll(" select {$colstr} from  `".DB_FIX."content_tagmap` a  JOIN  `".DB_FIX."content` b ON (a.content_id = b.content_id) {$wherestr} {$orderby} limit {$start},{$limit} ");
    }
    
    public function getContentTagMapCount($where){
        
        $wherestr  = $this->getWhere($where);
        
        return $this->_db->fetchOne("  select count(1) from  `".DB_FIX."content_tagmap` a   JOIN  `".DB_FIX."content` b ON (a.content_id = b.content_id)   {$wherestr}");
    }
    
    
    private function getWhere($where){
         $local = array();
         if(isset($where['tag_id'])){
             $where['tag_id'] = (int)$where['tag_id'];
             $local[] = " a.tag_id = {$where['tag_id']}";
         }
         if(empty($where)) return ' WHERE 1!=1 ';
         
         $wherestr = '';
         if(!empty($local)){
             $wherestr = " WHERE  ". join(' and ',$local);
         }
         return $wherestr;
    }
    
}