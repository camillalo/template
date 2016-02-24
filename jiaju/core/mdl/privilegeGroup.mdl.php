<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ( 'Access Denied' );
}

class privilegeGroupMdl{
    
    private  $_db;
    
    private static  $instance = null;
    
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new privilegeGroupMdl();
        }
        
        return self::$instance;
    }
    
    private function __construct() {
        
        $this->_db = mysql::getInstance();
        
    }
    
    public  function addPrivilegeGroup($info){
        
        if(empty($info)) return false;
        
        return $this->_db->insert(DB_FIX . 'privilege_group',$info);
    } 
    
    public function replacePrivilegeGroup($info){
        if(empty($info)) return false;
        return $this->_db->replace(DB_FIX . 'privilege_group',$info);
    }
    
    public function getPrivilegeGroupIdByName($group_name){
        $group_name = $this->_db->quote($group_name);
        
        return $this->_db->fetchOne("select  group_id  from `".DB_FIX."privilege_group` where group_name ='{$group_name}' limit 1 ");
    }
    
    public function updatePrivilegeGroup($id,$info){
        
        if(empty($info)) return false;
        
        $id = (int)$id;
        
        return $this->_db->update(DB_FIX.'privilege_group',$info," group_id = {$id} ");
    }
    
    public function delPrivilegeGroup($id){
         $id = (int)$id;
         return $this->_db->delete(DB_FIX."privilege_group"," group_id = {$id} ");
    }
    
    public function getPrivilegeGroup($id){
        
        $id = (int)$id;
        
        return $this->_db->fetchRow("select  * from `".DB_FIX."privilege_group` where group_id ={$id} limit 1 ");
    }
    
    public function getAllPrivilegeGroup(){
        
        return $this->_db->fetchAll("select  * from `".DB_FIX."privilege_group` ");
        
    }
    
    public function getPrivilegeGroupList($col,$where,$order,$start=0,$limit=10){
        
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
        return $this->_db->fetchAll(" select {$colstr} from  `".DB_FIX."privilege_group` {$wherestr} {$orderby} limit {$start},{$limit} ");
    }
    
    public function getPrivilegeGroupCount($where){
        
        $wherestr  = $this->getWhere($where);
        
        return $this->_db->fetchOne("  select count(1) from  `".DB_FIX."privilege_group` {$wherestr}");
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