<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ( 'Access Denied' );
}

class recommendGroupMdl{
    
    private  $_db;
    
    private static  $instance = null;
    
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new recommendGroupMdl();
        }
        
        return self::$instance;
    }
    
    private function __construct() {
        
        $this->_db = mysql::getInstance();
        
    }
    
    public  function addRecommendGroup($info){
        
        if(empty($info)) return false;
        
        return $this->_db->insert(DB_FIX . 'recommend_group',$info);
    } 
    
    public function updateRecommendGroup($id,$info){
        
        if(empty($info)) return false;
        
        $id = (int)$id;
        
        return $this->_db->update(DB_FIX.'recommend_group',$info," group_id = {$id} ");
    }
    
    public function delRecommendGroup($id){
         $id = (int)$id;
         return $this->_db->delete(DB_FIX."recommend_group"," group_id = {$id} ");
    }
    
    public function getRecommendGroup($id){
        
        $id = (int)$id;
        
        return $this->_db->fetchRow("select  * from `".DB_FIX."recommend_group` where group_id ={$id} limit 1 ");
    }
    
    public function getAllRecommendGroup(){
        
        return $this->_db->fetchAll("select  * from `".DB_FIX."recommend_group` order by  group_id asc");
        
    }
    
    public function getRecommendGroupList($col,$where,$order,$start=0,$limit=10){
        
        if(empty($col)){
            $colstr = '*';
        }else{
            $colstr = join(',',$col);
        }
        $wherestr  = $this->getWhere($where);
        if(empty($order)){
            $orderby = ' order by  group_id  desc ';
        }else{
            $local = array();
            foreach($order  as $k => $v){
                $local [] = " {$k} {$v} ";
            }
            $orderby = ' order by '.join(',',$local);
        }
        return $this->_db->fetchAll(" select {$colstr} from  `".DB_FIX."recommend_group` {$wherestr} {$orderby} limit {$start},{$limit} ");
    }
    
    public function getRecommendGroupCount($where){
        
        $wherestr  = $this->getWhere($where);
        
        return $this->_db->fetchOne("  select count(1) from  `".DB_FIX."recommend_group` {$wherestr}");
    }
    
    
    private function getWhere($where){
         $local = array();
         if(isset($where['keyword'])){
             $where['keyword'] = $this->_db->quote($where['keyword']);
             $local[] = " ( group_name like '%{$where['keyword']}%') ";
         }
         $wherestr = '';
         if(!empty($local)){
             $wherestr = " WHERE  ". join(' and ',$local);
         }
         return $wherestr;
    }
    
    
}