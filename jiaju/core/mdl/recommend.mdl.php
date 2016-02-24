<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ( 'Access Denied' );
}

class recommendMdl{
    
    private  $_db;
    
    private static  $instance = null;
    
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new recommendMdl();
        }
        
        return self::$instance;
    }
    
    private function __construct() {
        
        $this->_db = mysql::getInstance();
        
    }
    
    public  function addRecommend($info){
        
        if(empty($info)) return false;
        
        return $this->_db->insert(DB_FIX . 'recommend',$info);
    } 
    
     public  function addRecommendArr($info){
        
        if(empty($info)) return false;
        
        return $this->_db->insertArr(DB_FIX . 'recommend',$info);
    } 
    
    
    
    public function updateRecommend($id,$info){
        
        if(empty($info)) return false;
        
        $id = (int)$id;
        
        return $this->_db->update(DB_FIX.'recommend',$info," recommend_id = {$id} ");
    }
    
    public function getRecommend($id){
        
        $id = (int)$id;
        
        return $this->_db->fetchRow("select  * from `".DB_FIX."recommend` where recommend_id ={$id} limit 1 ");
    }
    
    public function getRecommendByPageId($page_id){
        $page_id   = (int)$page_id; 
        
        return $this->_db->fetchAll("select  * from `".DB_FIX."recommend` where  page_id = {$page_id}  order by `order` asc "); 
    }
    
    public function delRecommend($id){
         $id = (int)$id;
         return $this->_db->delete(DB_FIX."recommend"," recommend_id = {$id} ");
    }
    
    
    public function getRecommendList($col,$where,$order){
        
        if(empty($col)){
            $colstr = '*';
        }else{
            $colstr = join(',',$col);
        }
        $wherestr  = $this->getWhere($where);
        if(empty($order)){
            $orderby = ' order by  recommend_id  desc ';
        }else{
            $local = array();
            foreach($order  as $k => $v){
                $local [] = " {$k} {$v} ";
            }
            $orderby = ' order by '.join(',',$local);
        }
        return $this->_db->fetchAll(" select {$colstr} from  `".DB_FIX."recommend` {$wherestr} {$orderby} ");
    }
    
    public function getRecommendCount($where){
        
        $wherestr  = $this->getWhere($where);
        
        return $this->_db->fetchOne("  select count(1) from  `".DB_FIX."recommend` {$wherestr}");
    }
    
    
    private function getWhere($where){
         $local = array();
         if(isset($where['page_id'])){
             $where['page_id'] = (int)$where['page_id'];
             $local[] = "  page_id = {$where['page_id']} ";
         }
         if(isset($where['sign_id'])){
             $where['sign_id'] = (int)$where['sign_id'];
             $local[] = "  sign_id = {$where['sign_id']} ";
         }
         $wherestr = '';
         if(!empty($local)){
             $wherestr = " WHERE  ". join(' and ',$local);
         }else{
             $wherestr = " WHERE  1!=1 ";
         }
         return $wherestr;
    }
    
    
}