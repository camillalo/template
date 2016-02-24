<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ( 'Access Denied' );
}

class categoryMdl{
    
    private  $_db;
    
    private static  $instance = null;
    
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new categoryMdl();
        }
        
        return self::$instance;
    }
    
    private function __construct() {
        
        $this->_db = mysql::getInstance();
        
    }
    
    public  function addCategory($info){
        
        if(empty($info)) return false;
        
        return $this->_db->insert(DB_FIX . 'category',$info);
    } 
    
    public function updateCategory($id,$info){
        
        if(empty($info)) return false;
        
        $id = (int)$id;
        
        return $this->_db->update(DB_FIX.'category',$info," category_id = {$id} ");
    }
    
    public function getCategory($id){
        
        $id = (int)$id;
        
        return $this->_db->fetchRow("select  * from `".DB_FIX."category` where category_id ={$id} limit 1 ");
    }
    
    public function getAllCategory(){
        return $this->_db->fetchAll("select  * from `".DB_FIX."category` ");
    }
    
    public function getCountCategoryByParentId($parent_id){
        $parent_id = (int)$parent_id;
        return $this->_db->fetchOne("select  count(1) from `".DB_FIX."category` where parent_id = {$parent_id} ");
    }
    
    public function getChildCategory($category_type,$parent_id){
        $category_type = (int)$category_type;
        $parent_id = (int)$parent_id;
        return $this->_db->fetchAll("select  category_id,category_name from `".DB_FIX."category` WHERE category_type = {$category_type} AND parent_id = {$parent_id} ");
    }
    
    public function getCategoryPairByCategoryIds($ids){
        if(empty($ids)) return array();
        foreach($ids as $k=>$v){
            $ids[$k] = (int)$v;
        }
        $idstr = join(',',$ids);
        return $this->_db->fetchPair("select  category_id,category_name from `".DB_FIX."category` WHERE category_id in({$idstr}) ");
    }
    
    public function delCategory($id){
         $id = (int)$id;
         return $this->_db->delete(DB_FIX."category"," category_id = {$id} ");
    }
    
    
    public function getCategoryList($col,$where,$order,$start=0,$limit=10){
        
        if(empty($col)){
            $colstr = '*';
        }else{
            $colstr = join(',',$col);
        }
        $wherestr  = $this->getWhere($where);
        if(empty($order)){
            $orderby = ' order by  category_id  desc ';
        }else{
            $local = array();
            foreach($order  as $k => $v){
                $local [] = " {$k} {$v} ";
            }
            $orderby = ' order by '.join(',',$local);
        }
        return $this->_db->fetchAll(" select {$colstr} from  `".DB_FIX."category` {$wherestr} {$orderby} limit {$start},{$limit} ");
    }
    
    public function getCategoryCount($where){
        
        $wherestr  = $this->getWhere($where);
        
        return $this->_db->fetchOne("  select count(1) from  `".DB_FIX."category` {$wherestr}");
    }
    
    
    private function getWhere($where){
         $local = array();
         if(isset($where['keyword'])){
             $where['keyword'] = $this->_db->quote($where['keyword']);
             $local[] = " ( category_name like '%{$where['keyword']}%') ";
         }
         if(isset($where['parent_id'])){
             $where['parent_id'] = (int)$where['parent_id'];
             $local[] = " parent_id  = {$where['parent_id']} ";             
         }
         if(isset($where['category_type'])){
             $where['category_type'] = (int)$where['category_type'];
             $local[] = " category_type  = {$where['category_type']} ";  
         }
         $wherestr = '';
         if(!empty($local)){
             $wherestr = " WHERE  ". join(' and ',$local);
         }
         return $wherestr;
    }
    
    
}