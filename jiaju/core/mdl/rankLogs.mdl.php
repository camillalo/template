<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ('Access Denied' );
}

class rankLogsMdl{
    
    private  $_db;
    
    private static  $instance = null;
    
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new rankLogsMdl();
        }
        
        return self::$instance;
    }
    
    private function __construct() {
        
        $this->_db = mysql::getInstance();
        
    }
    
    public  function addRankLogs($info){
        
        if(empty($info)) return false;
        
        return $this->_db->insert(DB_FIX . 'user_rank_logs',$info);
    } 

    public function getRankLogsList($col,$where,$order,$start=0,$limit=10){
        
        if(empty($col)){
            $colstr ='*';
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
        return $this->_db->fetchAll(" select {$colstr} from  `".DB_FIX."user_rank_logs` a
                            join   `".DB_FIX."admin`  b ON (a.admin_id = b.admin_id)
                            join   `".DB_FIX."users`  c ON (a.uid = c.uid)
                            join   `".DB_FIX."user_ranks` d ON (a.rank_id= d.rank_id)
                
                            {$wherestr} {$orderby} limit {$start},{$limit} ");
    }
    
    public function getRankLogsCount($where){
        
        $wherestr  = $this->getWhere($where);
        
        return $this->_db->fetchOne("  select count(1) from  `".DB_FIX."user_rank_logs` {$wherestr}");
    }
    
    
    private function getWhere($where){
         $local = array();
         if(isset($where['keyword'])){
             $where['keyword'] = $this->_db->quote($where['keyword']);
             $local[] = " ( b.realname like  '%{$where['keyword']}%'  or c.username   like  '%{$where['keyword']}%'  or  d.rank_name like  '%{$where['keyword']}%' ) ";
         }
         
         $wherestr = '';
         if(!empty($local)){
             $wherestr = " WHERE  ". join(' and ',$local);
         }
         return $wherestr;
    }
    
    
}