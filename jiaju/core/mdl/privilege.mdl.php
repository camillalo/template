<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ( 'Access Denied' );
}

class privilegeMdl{
    
    private  $_db;
    
    private static  $instance = null;
    
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new privilegeMdl();
        }
        
        return self::$instance;
    }
    
    private function __construct() {
        
        $this->_db = mysql::getInstance();
        
    }
    
    public  function addPrivilege($info){
        
        if(empty($info)) return false;
        
        return $this->_db->insert(DB_FIX . 'privilege',$info);
    } 
    
    public function updatePrivilege($id,$info){
        
        if(empty($info)) return false;
        
        $id = (int)$id;
        
        return $this->_db->update(DB_FIX.'privilege',$info," privilege_id = {$id} ");
    }
    
    public function replacePrivilege($info){
        
        if(empty($info)) return false;
        
        return $this->_db->replace(DB_FIX . 'privilege',$info);
    }
    
    public function getPrivilege($id){
        
        $id = (int)$id;
        
        return $this->_db->fetchRow("select  * from `".DB_FIX."privilege` where privilege_id ={$id} limit 1 ");
    }
    
    public function delPrivilege($id){
         $id = (int)$id;
         return $this->_db->delete(DB_FIX."privilege"," privilege_id = {$id} ");
    }
    
    public function getAllPrivilege(){
        
        return $this->_db->fetchAll("select  * from `".DB_FIX."privilege` ");
        
    }
    
    public function getCountPrivilegeByGroupId($id){
        
        $id = (int)$id;
        return $this->_db->fetchOne("select  count(1) from `".DB_FIX."privilege`  where group_id = {$id}");
    }
    
    
    //需要连表查询的例子
    public function getPrivilegeList($col,$where,$order,$start=0,$limit=10){
        
        if(empty($col)){
            $colstr = '*';
        }else{
            $colstr = join(',',$col);
        }
        $wherestr  = $this->getWhere($where);
        if(empty($order)){
            $orderby = ' order by  a.privilege_id  desc ';
        }else{
            $local = array();
            foreach($order  as $k => $v){
                $local [] = " {$k} {$v} ";
            }
            $orderby = ' order by '.join(',',$local);
        }
        return $this->_db->fetchAll("   select {$colstr} from  `".DB_FIX."privilege` a 
                                        JOIN `".DB_FIX."privilege_group` b
                                        ON (a.group_id = b.group_id)
                                        {$wherestr} {$orderby} limit {$start},{$limit}
                                    ");
    }
    
    public function  getPrivilegeCount($where){
        $wherestr  = $this->getWhere($where);
        return $this->_db->fetchOne("   select count(1) from  `".DB_FIX."privilege` a 
                                        JOIN `".DB_FIX."privilege_group` b
                                        ON (a.group_id = b.group_id)
                                        {$wherestr} ");
    }
    
    private function getWhere($where){
         $local = array();
         if(!empty($where['keyword'])){
             $where['keyword'] = $this->_db->quote($where['keyword']);
             $local[] = " ( a.privilege_name like '%{$where['keyword']}%') ";
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