<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ('Access Denied' );
}

class buildingSiteMdl{
    
    private  $_db;
    
    private static  $instance = null;
    
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new buildingSiteMdl();
        }
        
        return self::$instance;
    }
    
    private function __construct() {
        
        $this->_db = mysql::getInstance();
        
    }
    
    public  function addBuildingSite($info){
        
        if(empty($info)) return false;
        
        return $this->_db->insert(DB_FIX . 'building_site',$info);
    } 
    
    public function updateBuildingSite($id,$info){
        
        if(empty($info)) return false;
        
        $id = (int)$id;
        
        return $this->_db->update(DB_FIX.'building_site',$info," id = {$id} ");
    }
    
    public function updatePv($id){
        $id = (int)$id;
        return $this->_db->update(" update  ".DB_FIX."building_site set  `pv`=(`pv`+1) where id={$id}");
    }
    
    public function getBuildingSite($id){
        
        $id = (int)$id;
        
        return $this->_db->fetchRow("select  * from `".DB_FIX."building_site` where id ={$id} limit 1 ");
    }
    public function getBuildingSiteCountPair($ids){
       if(empty($ids))  return array();
       foreach($ids as $k=>$v){
           $ids[$k] = (int)$v;
       }
       $idstr = join(',',$ids);
       return $this->_db->fetchPair("select  company_id ,count(1) as num  from `".DB_FIX."building_site`  where company_id in({$idstr})   group by company_id  ");
   }

   public function getNewCompanySite($num){
         $num = (int)$num;
        return $this->_db->fetchAll("select a.id,a.company_id,a.name,a.create_time,b.company_name from `".DB_FIX."building_site` a join `".DB_FIX."company` b ON (a.company_id = b.uid)  where  a.is_show = 1   limit 0,{$num} ");
    }
   
    public function getHotsCompanySite($num){
         $num = (int)$num;
        return $this->_db->fetchAll("select id,company_id,name from `".DB_FIX."building_site`  where is_show = 1   order by  pv  DESC  limit 0,{$num} ");
    }
    
    
    public function delBuildingSite($id){
         $id = (int)$id;
         return $this->_db->delete(DB_FIX."building_site"," id = {$id} ");
    }
    
    
    public function getBuildingSiteList($col,$where,$order,$start=0,$limit=10){
        
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
        return $this->_db->fetchAll(" select {$colstr} from  `".DB_FIX."building_site` {$wherestr} {$orderby} limit {$start},{$limit} ");
    }
    
    public function getBuildingSiteCount($where){
        
        $wherestr  = $this->getWhere($where);
        
        return $this->_db->fetchOne("  select count(1) from  `".DB_FIX."building_site` {$wherestr}");
    }
    
    
    private function getWhere($where){
         $local = array();
         if(isset($where['keyword'])){
             $where['keyword'] = $this->_db->quote($where['keyword']);
             $local[] = " ( name like  '%{$where['keyword']}%'   ) ";
         }
         if(isset($where['company_id'])){
             $where['company_id'] = (int)$where['company_id'];
             $local[] = " company_id = {$where['company_id']} ";
         }
      
         if(isset($where['area_id'])){
             $where['area_id'] = (int)$where['area_id'];
             $local[] = " area_id = {$where['area_id']} ";
         }
         if(isset($where['space_id'])){
             $where['space_id'] = (int)$where['space_id'];
             $local[] = " space_id = {$where['space_id']} ";
         }
         if(isset($where['price_id'])){
             $where['price_id'] = (int)$where['price_id'];
             $local[] = " price_id = {$where['price_id']} ";
         }
         if(isset($where['is_show'])){
             $where['is_show'] = (int)$where['is_show'];
             $local[] = " is_show = {$where['is_show']} ";
         }
         if(isset($where['a_id'])){
             $where['a_id'] = (int)$where['a_id'];
             $local[] = " a_id = {$where['a_id']} ";
         }
         if(isset($where['style_id'])){
             $where['style_id'] = (int)$where['style_id'];
             $local[] = " style_id = {$where['style_id']} ";
         }
         if(isset($where['status'])){
             $where['status'] = (int)$where['status'];
             $local[] = " status = {$where['status']} ";
         }
         
         $wherestr = '';
         if(!empty($local)){
             $wherestr = " WHERE  ". join(' and ',$local);
         }
         return $wherestr;
    }
    
    
}