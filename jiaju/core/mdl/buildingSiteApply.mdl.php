<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ('Access Denied' );
}

class buildingSiteApplyMdl{
    
    private  $_db;
    
    private static  $instance = null;
    
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new buildingSiteApplyMdl();
        }
        
        return self::$instance;
    }
    
    private function __construct() {
        
        $this->_db = mysql::getInstance();
        
    }
    
    public  function addBuildingSiteApply($info){
        
        if(empty($info)) return false;
        
        return $this->_db->insert(DB_FIX . 'building_site_apply',$info);
    } 
    
    public function updateBuildingSiteApply($id,$info){
        
        if(empty($info)) return false;
        
        $id = (int)$id;
        
        return $this->_db->update(DB_FIX.'building_site_apply',$info," id = {$id} ");
    }
    
    public function getBuildingSiteApply($id){
        
        $id = (int)$id;
        
        return $this->_db->fetchRow("select  * from `".DB_FIX."building_site_apply` where id ={$id} limit 1 ");
    }
    
    
    public function getNewBuildingSiteApply($num){
        $num = (int) $num;
        
        return $this->_db->fetchAll("select  a.name,a.comment,b.name as bname,a.create_time  from `".DB_FIX."building_site_apply` a join `".DB_FIX."building_site`  b 
                    on (a.site_id = b.id)
                order by  a.id desc  limit 0,{$num} ");
        
    }
    
    public function delBuildingSiteApply($id){
         $id = (int)$id;
         return $this->_db->delete(DB_FIX."building_site_apply"," id = {$id} ");
    }
    
    
    public function getBuildingSiteApplyList($col,$where,$order,$start=0,$limit=10){
        
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
        return $this->_db->fetchAll(" select {$colstr} from  `".DB_FIX."building_site_apply` {$wherestr} {$orderby} limit {$start},{$limit} ");
    }
    
    public function getBuildingSiteApplyCount($where){
        
        $wherestr  = $this->getWhere($where);
        
        return $this->_db->fetchOne("  select count(1) from  `".DB_FIX."building_site_apply` {$wherestr}");
    }
    
    
    private function getWhere($where){
         $local = array();
         if(isset($where['keyword'])){
             $where['keyword'] = $this->_db->quote($where['keyword']);
             $local[] = " ( name like  '%{$where['keyword']}%'   or phone like  '%{$where['keyword']}%'   ) ";
         }
         if(isset($where['uid'])){
             $where['uid'] = (int)$where['uid'];
             $local[] = " uid = {$where['uid']} ";
         }
         if(isset($where['site_id'])){
             $where['site_id'] = (int)$where['site_id'];
             $local[] = " site_id = {$where['site_id']} ";
         }
         
         $wherestr = '';
         if(!empty($local)){
             $wherestr = " WHERE  ". join(' and ',$local);
         }
         return $wherestr;
    }
    
    
}