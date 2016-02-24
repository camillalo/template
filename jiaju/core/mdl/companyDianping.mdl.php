<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ('Access Denied' );
}

class companyDianpingMdl{
    
    private  $_db;
    
    private static  $instance = null;
    
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new companyDianpingMdl();
        }
        
        return self::$instance;
    }
    
    private function __construct() {
        
        $this->_db = mysql::getInstance();
        
    }
    
    public  function addCompanyDianping($info){
        
        if(empty($info)) return false;
        
        return $this->_db->insert(DB_FIX . 'company_dianping',$info);
    } 
    
    public function updateCompanyDianping($id,$info){
        
        if(empty($info)) return false;
        
        $id = (int)$id;
        
        return $this->_db->update(DB_FIX.'company_dianping',$info," id = {$id} ");
    }
    
    public function getCompanyDianping($id){
        
        $id = (int)$id;
        
        return $this->_db->fetchRow("select  * from `".DB_FIX."company_dianping` where id ={$id} limit 1 ");
    }
    
    public function getCompanyDianpingAverageScore($id){
        
         $id = (int)$id;
         
         return $this->_db->fetchRow("SELECT sum(process)/count(1) as p,sum(service)/count(1) as s,sum(design)/count(1) as d,sum(sales)/count(1) as sa,count(1) as num  FROM  `".DB_FIX."company_dianping` where company_id = {$id}  GROUP BY company_id limit 1");
    }

    
    public function delCompanyDianping($id){
         $id = (int)$id;
         return $this->_db->delete(DB_FIX."company_dianping"," id = {$id} ");
    }
    
    public function getCompanyDianpingNewListByCompanyId($id,$num){
        $id = (int)$id;
        return $this->_db->fetchAll("SELECT * from  `".DB_FIX."company_dianping` where  company_id = {$id} AND is_show = 1 order by  id desc  limit 0,{$num}");
    }
    
    public function getCompanyDianpingList($col,$where,$order,$start=0,$limit=10){
        
        if(empty($col)){
            $colstr ='a.*';
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
        return $this->_db->fetchAll(" select {$colstr} from  `".DB_FIX."company_dianping` a 
                                      join  `".DB_FIX."company` b on (a.company_id = b.uid)  
                                      join   `".DB_FIX."users` c on (a.uid = c.uid)
                                     {$wherestr} {$orderby} limit {$start},{$limit} ");
    }
    
    public function getCompanyDianpingCount($where){
        
        $wherestr  = $this->getWhere($where);
        
        return $this->_db->fetchOne("  select count(1) from  `".DB_FIX."company_dianping`  a
                                       join  `".DB_FIX."company` b on (a.company_id = b.uid)  
                                       join   `".DB_FIX."users` c on (a.uid = c.uid)  
                                      {$wherestr}");
    }
    
    
    private function getWhere($where){
         $local = array();
         if(isset($where['keyword'])){
             $where['keyword'] = $this->_db->quote($where['keyword']);
             $local[] = " ( c.username like '%{$where['keyword']}%' or b.company_name like '%{$where['keyword']}%' )";
         }
         if(isset($where['uid'])){
             $where['uid'] = (int)$where['uid'];
             $local[] = " a.uid = {$where['uid']} ";
         }
         if(isset($where['company_id'])){
             $where['company_id'] = (int)$where['company_id'];
             $local[] = " a.company_id = {$where['company_id']} ";
         }
         if(isset($where['type'])){
             $where['type'] = (int)$where['type'];
             $local[] = " a.type = {$where['type']} ";
         }
         
         $wherestr = '';
         if(!empty($local)){
             $wherestr = " WHERE  ". join(' and ',$local);
         }
         return $wherestr;
    }
    
    
}