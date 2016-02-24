<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ('Access Denied' );
}

class securityMdl{
    
    private  $_db;
    
    private static  $instance = null;
    
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new securityMdl();
        }
        
        return self::$instance;
    }
    
    private function __construct() {
        
        $this->_db = mysql::getInstance();
        
    }
    
    public  function addSecurity($info){
        
        if(empty($info)) return false;
        
        return $this->_db->insert(DB_FIX . 'company_security',$info);
    } 
    
    public function updateSecurity($id,$info){
        
        if(empty($info)) return false;
        
        $id = (int)$id;
        
        return $this->_db->update(DB_FIX.'company_security',$info," uid = {$id} ");
    }
    
    public function checkIsSecurity($id){
        $id = (int)$id;
        return $this->_db->fetchOne("select  money1  from `".DB_FIX."company_security` where uid ={$id} limit 1 ");
    }
    
    public function getSecurity($id){
        
        $id = (int)$id;
        
        return $this->_db->fetchRow("select  * from `".DB_FIX."company_security` where uid ={$id} limit 1 ");
    }
    
    public function getSecuritysByids($ids){
        if(empty($ids)) return array();
        foreach($ids  as $k=>$v){
            $ids[$k] = (int)$v;
        }
        $idstr = join(',',$ids);
        return $this->_db->fetchAll("select  * from `".DB_FIX."company_security` where uid in ({$idstr}) ");
    }

    
    public function delSecurity($id){
         $id = (int)$id;
         return $this->_db->delete(DB_FIX."company_security"," uid = {$id} ");
    }
    
    
    public function getSecurityList($col,$where,$order,$start=0,$limit=10){
        
        if(empty($col)){
            $colstr ='*';
        }else{
            $colstr = join(',',$col);
        }
        $wherestr  = $this->getWhere($where);
        if(empty($order)){
            $orderby = ' order by  a.uid  desc ';
        }else{
            $local = array();
            foreach($order  as $k => $v){
                $local [] = " {$k} {$v} ";
            }
            $orderby = ' order by '.join(',',$local);
        }
        return $this->_db->fetchAll(" select {$colstr} from  `".DB_FIX."company_security` a  join `".DB_FIX."company` b ON (a.uid = b.uid) join  `".DB_FIX."users` c ON (b.uid = c.uid) {$wherestr} {$orderby} limit {$start},{$limit} ");
    }
    
    public function getSecurityCount($where){
        
        $wherestr  = $this->getWhere($where);
        
        return $this->_db->fetchOne("  select count(1) from  `".DB_FIX."company_security`  a join `".DB_FIX."company` b ON (a.uid = b.uid)  
                
                                        join  `".DB_FIX."users` c ON (b.uid = c.uid)
                                        {$wherestr}");
    }
    
    
    private function getWhere($where){
         $local = array();
         if(isset($where['keyword'])){
             $where['keyword'] = $this->_db->quote($where['keyword']);
             $local[] = " b.company_name like '%{$where['keyword']}%'  or  c.username like  '%{$where['keyword']}%' or a.uid  like  '%{$where['keyword']}%' ";
         }
         if(isset($where['uid'])){
             $where['uid'] = (int)$where['uid'];
             $local[] = " a.uid = {$where['uid']} ";
         }
         
         $wherestr = '';
         if(!empty($local)){
             $wherestr = " WHERE  ". join(' and ',$local);
         }
         return $wherestr;
    }
    
    
}