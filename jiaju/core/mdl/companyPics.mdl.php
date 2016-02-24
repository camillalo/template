<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ('Access Denied' );
}

class companyPicsMdl{
    
    private  $_db;
    
    private static  $instance = null;
    
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new companyPicsMdl();
        }
        
        return self::$instance;
    }
    
    private function __construct() {
        
        $this->_db = mysql::getInstance();
        
    }
    
    public  function addCompanyPics($info){
        
        if(empty($info)) return false;
        
        return $this->_db->insert(DB_FIX . 'company_pics',$info);
    } 
    
    public function updateCompanyPics($id,$info){
        
        if(empty($info)) return false;
        
        $id = (int)$id;
        
        return $this->_db->update(DB_FIX.'company_pics',$info," id = {$id} ");
    }
    
    public function getCompanyPics($id){
        
        $id = (int)$id;
        
        return $this->_db->fetchRow("select  * from `".DB_FIX."company_pics` where id ={$id} limit 1 ");
    }
    
    public function getCompanyPicsByUidType($uid,$type,$num = 1){
        $uid = (int)$uid;
        $type = (int)$type;
        if($num == 1){
            return $this->_db->fetchRow("select  * from `".DB_FIX."company_pics` where uid ={$uid} and type={$type} limit 1 ");
        }
        return $this->_db->fetchAll("select  * from `".DB_FIX."company_pics` where uid ={$uid} and type={$type} limit 0,{$num} ");
    }
    
    public function delCompanyPics($id){
         $id = (int)$id;
         return $this->_db->delete(DB_FIX."company_pics"," id = {$id} ");
    }
    
    
    public function getCompanyPicsList($col,$where,$order,$start=0,$limit=10){
        
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
        return $this->_db->fetchAll(" select {$colstr} from  `".DB_FIX."company_pics` {$wherestr} {$orderby} limit {$start},{$limit} ");
    }
    
    public function getCompanyPicsCount($where){
        
        $wherestr  = $this->getWhere($where);
        
        return $this->_db->fetchOne("  select count(1) from  `".DB_FIX."company_pics` {$wherestr}");
    }
    
    
    private function getWhere($where){
         $local = array();
         if(isset($where['keyword'])){
             $where['keyword'] = $this->_db->quote($where['keyword']);
             $local[] = " ( title like  '%{$where['keyword']}%'   ) ";
         }
         if(isset($where['type'])){
             $where['type'] = (int)$where['type'];
             $local[] = " type = {$where['type']} ";
         }
         if(isset($where['uid'])){
             $where['uid'] = (int)$where['uid'];
             $local[] = " uid = {$where['uid']} ";
         }
         $wherestr = '';
         if(!empty($local)){
             $wherestr = " WHERE  ". join(' and ',$local);
         }
         return $wherestr;
    }
    
    
}