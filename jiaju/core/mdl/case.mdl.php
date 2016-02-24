<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ( 'Access Denied' );
}

class caseMdl{
    
    private  $_db;
    
    private static  $instance = null;
    
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new caseMdl();
        }
        
        return self::$instance;
    }
    
    private function __construct() {
        
        $this->_db = mysql::getInstance();
        
    }
    
    public  function addCase($info){
        
        if(empty($info)) return false;
        
        return $this->_db->insert(DB_FIX . 'case',$info);
    } 
    
    public function updateCase($id,$info){
        
        if(empty($info)) return false;
        
        $id = (int)$id;
        
        return $this->_db->update(DB_FIX.'case',$info," case_id = {$id} ");
    }
    
    public function getNextCase($id){
        $id = (int)$id;
        return $this->_db->fetchRow("select  * from `".DB_FIX."case` where case_id >{$id} order by  case_id ASC  limit 1 ");
    }
    public function getUpCase($id){
        $id = (int)$id;
        return $this->_db->fetchRow("select  * from `".DB_FIX."case` where case_id <{$id} order by  case_id desc  limit 1 ");
    }
    
    public function getHotsCase($num){
        $num = (int)$num;
        return $this->_db->fetchAll("select  case_id,title from `".DB_FIX."case` where is_show = 1 order by pv_num desc  limit 0,{$num} ");
    }
    
    public function updatePv($id){
        $id = (int)$id;
         
        return $this->_db->update("update ".DB_FIX."case  set pv_num =(pv_num+1) where case_id = {$id}");
    }
    
    public function getCase($id){
        
        $id = (int)$id;
        
        return $this->_db->fetchRow("select  * from `".DB_FIX."case` where case_id ={$id} limit 1 ");
    }
    
    public function getNewCase($num){
        $num = (int)$num;
        return $this->_db->fetchAll("select case_id,title,face_pic,pv_num,description from `".DB_FIX."case` where is_show =1  order by case_id desc limit 0,{$num} ");
        
    }
    
    public function getNewCompanyCase($num){
         $num = (int)$num;
        return $this->_db->fetchAll("select a.case_id,a.title,a.create_time,b.company_name from `".DB_FIX."case` a join `".DB_FIX."company` b ON (a.uid = b.uid)   where a.is_show =1  limit 0,{$num} ");
    }
    
    public function getNewCaseByCompanyId($id,$num){
         $id = (int)$id;
         $num = (int)$num;
         return $this->_db->fetchAll("select case_id,title,face_pic from `".DB_FIX."case` where uid = {$id} AND is_show =1  limit 0,{$num} ");
    }
    
    public function getNewCaseByDesignerId($id,$num){
         $id = (int)$id;
         $num = (int)$num;
         return $this->_db->fetchAll("select case_id,title,face_pic from `".DB_FIX."case` where designer_id = {$id} AND is_show =1   order by  case_id desc limit 0,{$num} ");
    }
    
    
   public function getCaseCountPair($ids){
       if(empty($ids))  return array();
       foreach($ids as $k=>$v){
           $ids[$k] = (int)$v;
       }
       $idstr = join(',',$ids);
       return $this->_db->fetchPair("select  uid ,count(1) as num  from `".DB_FIX."case`  where uid in({$idstr}) and is_show =1  group by uid  ");
   }
    
    public function delCase($id){
         $id = (int)$id;
         return $this->_db->delete(DB_FIX."case"," case_id = {$id} ");
    }
    
    
    public function getCaseList($col,$where,$order,$start=0,$limit=10){
        
        if(empty($col)){
            $colstr = '*';
        }else{
            $colstr = join(',',$col);
        }
        $wherestr  = $this->getWhere($where);
        if(empty($order)){
            $orderby = ' order by  case_id  desc ';
        }else{
            $local = array();
            foreach($order  as $k => $v){
                $local [] = " {$k} {$v} ";
            }
            $orderby = ' order by '.join(',',$local);
        }
        return $this->_db->fetchAll(" select {$colstr} from  `".DB_FIX."case` {$wherestr} {$orderby} limit {$start},{$limit} ");
    }
    
    public function getCaseCount($where){
        
        $wherestr  = $this->getWhere($where);
        
        return $this->_db->fetchOne("  select count(1) from  `".DB_FIX."case` {$wherestr}");
    }
    
    
    private function getWhere($where){
         $local = array();
         if(isset($where['keyword'])){
             $where['keyword'] = $this->_db->quote($where['keyword']);
             $local[] = " ( title like '%{$where['keyword']}%') ";
         }
         if(!empty($where['cateIds'])){
             foreach($where['cateIds'] as $v){
                 $cateid = (int)$v;
                 $local[] = " 
                 EXISTS( select 1 from  ".DB_FIX."case_map where cate_id = {$cateid} AND case_id = `".DB_FIX."case`.case_id )
            ";
             }

             
         }
         if(!empty($where['uid'])){
             $where['uid'] = (int)$where['uid'];
             $local[] = " uid = {$where['uid']} ";
         }
         if(!empty($where['type'])){
             $where['type'] = (int)$where['type'];
             $local[] = " type = {$where['type']} ";
         }
         
         if(!empty($where['designer_id'])){
             $where['designer_id'] = (int)$where['designer_id'];
             $local[] = " designer_id = {$where['designer_id']} ";
         }
         
         if(!empty($where['is_show'])){
             $where['is_show'] = (int)$where['is_show'];
             $local[] = " is_show = {$where['is_show']} ";
         }
         $wherestr = '';
         if(!empty($local)){
             $wherestr = " WHERE  ". join(' and ',$local);
         }
         return $wherestr;
    }
    
    
}