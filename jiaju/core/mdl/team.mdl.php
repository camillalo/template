<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ('Access Denied' );
}

class teamMdl{
    
    private  $_db;
    
    private static  $instance = null;
    
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new teamMdl();
        }
        
        return self::$instance;
    }
    
    private function __construct() {
        
        $this->_db = mysql::getInstance();
        
    }
    
    public  function addTeam($info){
        
        if(empty($info)) return false;
        
        return $this->_db->insert(DB_FIX . 'team',$info);
    } 
    
    public function updateTeam($id,$info){
        
        if(empty($info)) return false;
        
        $id = (int)$id;
        
        return $this->_db->update(DB_FIX.'team',$info," id = {$id} ");
    }
    
    public function getTeam($id){
        
        $id = (int)$id;
        
        return $this->_db->fetchRow("select  * from `".DB_FIX."team` where id ={$id} limit 1 ");
    }
    

    
    public function delTeam($id){
         $id = (int)$id;
         return $this->_db->delete(DB_FIX."team"," id = {$id} ");
    }
    
    
    public function getTeamList($col,$where,$order,$start=0,$limit=10){
        
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
        return $this->_db->fetchAll(" select {$colstr} from  `".DB_FIX."team` {$wherestr} {$orderby} limit {$start},{$limit} ");
    }
    
    public function getTeamCount($where){
        
        $wherestr  = $this->getWhere($where);
        
        return $this->_db->fetchOne("  select count(1) from  `".DB_FIX."team` {$wherestr}");
    }
    
    
    private function getWhere($where){
         $local = array();
         if(isset($where['keyword'])){
             $where['keyword'] = $this->_db->quote($where['keyword']);
             $local[] = " ( name like  '%{$where['keyword']}%'   or tel like  '%{$where['keyword']}%'   ) ";
         }

         
         $wherestr = '';
         if(!empty($local)){
             $wherestr = " WHERE  ". join(' and ',$local);
         }
         return $wherestr;
    }
    
    
}