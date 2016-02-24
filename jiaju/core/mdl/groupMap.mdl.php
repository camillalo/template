<?php

if ( !defined ( 'BASE_PATH') )
{
    exit ( 'Access Denied' );
}

class groupMapMdl{
    
    private  $_db;
    
    private static  $instance = null;
    
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new groupMapMdl();
        }
        
        return self::$instance;
    }
    
    private function __construct() {
        
        $this->_db = mysql::getInstance();
        
    }
    
    public function addGroupMap($info){
        
        return $this->_db->insert(DB_FIX."group_map",$info);
    }
    
    //获取 组ID 下面的所有权限
    public function getGroupMapsPairByGroupId($group_id){
        
        $group_id = (int) $group_id;
        
        return $this->_db->fetchPair("select  a.privilege_id,b.privilege_key from `".DB_FIX."group_map` a 
                                            JOIN  `".DB_FIX."privilege` b 
                                            on (a.privilege_id = b.privilege_id)  
                                            where a.group_id = {$group_id}");
    }
    
    //获取组下面所有的权限ID
    public function getGroupMapsColByGroupId($group_id){
          
         $group_id = (int)$group_id;
         
         return $this->_db->fetchCol("select  privilege_id from `".DB_FIX."group_map`  where group_id = {$group_id}");
         
    }
    
    public function delGroupMapsByGroupId($group_id){
        
        $group_id = (int)$group_id;
        
        return $this->_db->delete(DB_FIX."group_map"," group_id = {$group_id} ");
    }
    
    public function delGroupMapsByPrivilegeId($privilegeId){
        
        $privilegeId = (int)$privilegeId;
        
        return $this->_db->delete(DB_FIX."group_map"," privilege_id = {$privilegeId} ");
    }
     
}