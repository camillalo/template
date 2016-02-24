<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ('Access Denied' );
}

class companyMdl{
    
    private  $_db;
    
    private static  $instance = null;
    
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new companyMdl();
        }
        
        return self::$instance;
    }
    
    private function __construct() {
        
        $this->_db = mysql::getInstance();
        
    }
    
    public  function addCompany($info){
        
        if(empty($info)) return false;
        
        return $this->_db->insert(DB_FIX . 'company',$info);
    } 
    
    public function updateCompany($id,$info){
        
        if(empty($info)) return false;
        
        $id = (int)$id;
        
        return $this->_db->update(DB_FIX.'company',$info," uid = {$id} ");
    }
    
  
    
    public function updateCompanyPv($id){
         $id = (int)$id;
         return $this->_db->update("UPDATE ".DB_FIX."company set pv=(pv+1) where uid  = {$id} ");
    }
    
    public function getCompany($id){
        
        $id = (int)$id;
        
        return $this->_db->fetchRow("select  * from `".DB_FIX."company` where uid ={$id} limit 1 ");
    }
    
    public function getCompany2($id){
         $id = (int)$id;
        
        return $this->_db->fetchRow("select  a.pv,a.average_score,b.tel from `".DB_FIX."company` a left  join  `".DB_FIX."company_addrs` b  ON (a.addr_id = b.id) where a.uid ={$id} limit 1 ");
    }
    
    public function getCompanyByLngAndLat($lng,$lat){
    
        $maxLng = $lng + 50000;
        $maxLat = $lat + 50000;
        $minLng =  $lng - 50000;
        $minLat =  $lat - 50000;
        return $this->_db->fetchAll("select company_name,uid,longitude,latitude from `".DB_FIX."company`  where  longitude >= {$minLng} 
                                        and longitude <={$maxLng} 
                                        and  latitude >= {$minLat} 
                                        and  latitude <= {$maxLat}");
    }


    public function getCompanyIsAuthentication($id){
        $id = (int)$id;
        
        return $this->_db->fetchOne("select  is_authentication from `".DB_FIX."company` where uid ={$id} limit 1 ");
    }
    
   public function getCompanyName($id){
       $id = (int)$id;
        
        return $this->_db->fetchOne("select company_name from `".DB_FIX."company` where uid ={$id} limit 1 ");
   }
    
    
    public function delCompany($id){
         $id = (int)$id;
         return $this->_db->delete(DB_FIX."company"," uid = {$id} ");
    }
    
    public function getCompanysByIds($ids){
        if(empty($ids)) return array();
        $local = array();
        foreach($ids as $v){
            $local [] = (int) $v;
        }
        $str = join(',',$local);
        $datas =  $this->_db->fetchAll("select  uid,company_name,founding_year,introduce,logo from `".DB_FIX."company` where uid in({$str}) ");
        $return = array();
        foreach($datas as $v){
            $return [$v['uid']] = $v;
        }
        return $return;
    }
    
    public function getNewCompanys($num){

        $num  = (int)$num;
        return $this->_db->fetchAll("select  uid,company_name,founding_year,introduce from `".DB_FIX."company`  order by  uid desc  limit  0,{$num}");
    }
    
    public function getCompanyList($col,$where,$order,$start=0,$limit=10){
        
        if(empty($col)){
            $colstr ='*';
        }else{
            $colstr = join(',',$col);
        }
        $wherestr  = $this->getWhere($where);
        if(empty($order)){
            $orderby = ' order by  uid  desc ';
        }else{
            $local = array();
            foreach($order  as $k => $v){
                $local [] = " {$k} {$v} ";
            }
            $orderby = ' order by '.join(',',$local);
        }
        return $this->_db->fetchAll(" select {$colstr} from  `".DB_FIX."company` {$wherestr} {$orderby} limit {$start},{$limit} ");
    }
    
    public function getCompanyCount($where){
        
        $wherestr  = $this->getWhere($where);
        
        return $this->_db->fetchOne("  select count(1) from  `".DB_FIX."company` {$wherestr}");
    }
    
    
    private function getWhere($where){
         $local = array();
         if(isset($where['keyword'])){
             $where['keyword'] = $this->_db->quote($where['keyword']);
             $local[] = " ( company_name like  '%{$where['keyword']}%'   ) ";
         }
         if(isset($where['area_id'])){
             $where['area_id'] = (int)$where['area_id'];
             $local[] = "
                 EXISTS( select  1  from ".DB_FIX."company_area where area_id = {$where['area_id']}  and uid =  `".DB_FIX."company`.uid )
            ";
         }
         if(isset($where['vip'])){
             $time = NOWTIME;
             $local[] = "
                 EXISTS( select  1  from ".DB_FIX."users where `day`  > {$time}  and uid =  `".DB_FIX."company`.uid )
            ";
         }

         if(isset($where['scale_id'])){
             $where['scale_id'] = (int)$where['scale_id'];
             $local[] = " scale_id = {$where['scale_id']} ";
         }   
         
         if(isset($where['type'])){
             $where['type'] = (int)$where['type'];
             $local[] = " type = {$where['type']} ";
         }   
         
         if(isset($where['industry_id'])){
             $where['industry_id'] = (int)$where['industry_id'];
             $local[] = "
                 EXISTS( select  1  from ".DB_FIX."company_industry where industry_id = {$where['industry_id']}  and uid =  `".DB_FIX."company`.uid )
            ";
         }
         
         if(isset($where['project_id'])){
             $where['project_id'] = (int)$where['project_id'];
             $local[] = "
                 EXISTS( select  1  from ".DB_FIX."company_project where project_id = {$where['project_id']}  and uid =  `".DB_FIX."company`.uid )
            ";
         }
         
         $wherestr = '';
         if(!empty($local)){
             $wherestr = " WHERE  ". join(' and ',$local);
         }
         return $wherestr;
    }
    
    
}