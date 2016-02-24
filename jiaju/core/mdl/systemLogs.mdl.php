<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ('Access Denied' );
}

class systemLogsMdl{
    
    private  $_db;
    
    private static  $instance = null;
    
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new systemLogsMdl();
        }
        
        return self::$instance;
    }
    
    private function __construct() {
        
        $this->_db = mysql::getInstance();
        
    }
    
    public  function addSystemLogs($info){
        
        if(empty($info)) return false;
        
        return $this->_db->insert(DB_FIX . 'system_logs',$info);
    } 
    
    public function updateSystemLogs($id,$info){
        
        if(empty($info)) return false;
        
        $id = (int)$id;
        
        return $this->_db->update(DB_FIX.'system_logs',$info," id = {$id} ");
    }
    
    public function getSystemLogs($id){
        
        $id = (int)$id;
        
        return $this->_db->fetchRow("select  * from `".DB_FIX."system_logs` where id ={$id} limit 1 ");
    }
    

    
    public function delSystemLogs($id){
         $id = (int)$id;
         return $this->_db->delete(DB_FIX."system_logs"," id = {$id} ");
    }
    
    
    public function getSystemLogsList($col,$where,$order,$start=0,$limit=10){
        
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
        return $this->_db->fetchAll(" select {$colstr} from  `".DB_FIX."system_logs` {$wherestr} {$orderby} limit {$start},{$limit} ");
    }
    
    public function getSystemLogsCount($where){
        
        $wherestr  = $this->getWhere($where);
        
        return $this->_db->fetchOne("  select count(1) from  `".DB_FIX."system_logs` {$wherestr}");
    }
    
    
    private function getWhere($where){
         $local = array();
         if(isset($where['keyword'])){
             $where['keyword'] = $this->_db->quote($where['keyword']);
             $local[] = " ( `username` like  '%{$where['keyword']}%' or `url` like  '%{$where['keyword']}%' or `title` like  '%{$where['keyword']}%'  ) ";
         }
         
         $wherestr = '';
         if(!empty($local)){
             $wherestr = " WHERE  ". join(' and ',$local);
         }
         return $wherestr;
    }
    
    
}