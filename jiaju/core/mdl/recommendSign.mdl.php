<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ( 'Access Denied' );
}

class recommendSignMdl{
    
    private  $_db;
    
    private static  $instance = null;
    
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new recommendSignMdl();
        }
        
        return self::$instance;
    }
    
    private function __construct() {
        
        $this->_db = mysql::getInstance();
        
    }
    
    public  function addRecommendSign($info){
        
        if(empty($info)) return false;
        
        return $this->_db->insert(DB_FIX . 'recommend_sign',$info);
    } 
    
    public function updateRecommendSign($id,$info){
        
        if(empty($info)) return false;
        
        $id = (int)$id;
        
        return $this->_db->update(DB_FIX.'recommend_sign',$info," id = {$id} ");
    }
    
    public function getRecommendSign($id){
        
        $id = (int)$id;
        
        return $this->_db->fetchRow("select  * from `".DB_FIX."recommend_sign` where id ={$id} limit 1 ");
    }
    
    public function getGroupIdbySignId($id){
        $id = (int)$id;
        
        return $this->_db->fetchOne("select  group_id from `".DB_FIX."recommend_sign` where id ={$id} limit 1 ");
        
    }
    
    
    public function getRecommendSignByGroupId($group_id){
        
        $group_id = (int)$group_id;
        
        return $this->_db->fetchAll("select  * from `".DB_FIX."recommend_sign` where group_id ={$group_id}  ");
    }
    
    public function getRecommendSignPairByGroupId($group_id){
        $group_id = (int)$group_id;
        
        return $this->_db->fetchPair("select  id,name from `".DB_FIX."recommend_sign` where group_id ={$group_id}  ");
    }
    
    
    public function delRecommendSign($id){
         $id = (int)$id;
         return $this->_db->delete(DB_FIX."recommend_sign"," id = {$id} ");
    }

    
    public function getCountRecommendSignByGroupId($id){
        
        $id = (int)$id;
        return $this->_db->fetchOne("select  count(1) from `".DB_FIX."recommend_sign`  where group_id = {$id}");
    }
    
    
    //需要连表查询的例子
    public function getRecommendSignList($col,$where,$order,$start=0,$limit=10){
        
        if(empty($col)){
            $colstr = '*';
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
        return $this->_db->fetchAll("   select {$colstr} from  `".DB_FIX."recommend_sign` a 
                                        JOIN `".DB_FIX."recommend_group` b
                                        ON (a.group_id = b.group_id)
                                        {$wherestr} {$orderby} limit {$start},{$limit}
                                    ");
    }
    
    public function  getRecommendSignCount($where){
        $wherestr  = $this->getWhere($where);
        return $this->_db->fetchOne("   select count(1) from  `".DB_FIX."recommend_sign` a 
                                        JOIN `".DB_FIX."recommend_group` b
                                        ON (a.group_id = b.group_id)
                                        {$wherestr} ");
    }
    
    private function getWhere($where){
         $local = array();
         if(!empty($where['keyword'])){
             $where['keyword'] = $this->_db->quote($where['keyword']);
             $local[] = " ( a.name like '%{$where['keyword']}%') ";
         }
         if(!empty($where['group_id'])) {
             $where['group_id'] = (int)$where['group_id'];
             $local[] = "  a.group_id = {$where['group_id']} ";
         }
         $wherestr = '';
         if(!empty($local)){
             $wherestr = " WHERE  ". join(' and ',$local);
         }
         return $wherestr;
    }
    
    
}