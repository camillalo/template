<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ('Access Denied' );
}

class brandMdl{
    
    private  $_db;
    
    private static  $instance = null;
    
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new brandMdl();
        }
        
        return self::$instance;
    }
    
    private function __construct() {
        
        $this->_db = mysql::getInstance();
        
    }
    
    public  function addBrand($info){
        
        if(empty($info)) return false;
        
        return $this->_db->insert(DB_FIX . 'brand',$info);
    } 
    
    public function updateBrand($id,$info){
        
        if(empty($info)) return false;
        
        $id = (int)$id;
        
        return $this->_db->update(DB_FIX.'brand',$info," brand_id = {$id} ");
    }
    
    public function getBrand($id){
        
        $id = (int)$id;
        
        return $this->_db->fetchRow("select  * from `".DB_FIX."brand` where brand_id ={$id} limit 1 ");
    }
    
     public function getBrandName($id){
        
        $id = (int)$id;
        
        return $this->_db->fetchOne("select  brand_name from `".DB_FIX."brand` where brand_id ={$id} limit 1 ");
    }
    
    
    public function getBrandsByIds($ids){
        if(empty($ids)) return array();
        $idstr = join(',',$ids);
        return $this->_db->fetchAll("select brand_id , brand_name from `".DB_FIX."brand`    WHERE brand_id in({$idstr}) ");
    }

    
    public function delBrand($id){
         $id = (int)$id;
         return $this->_db->delete(DB_FIX."brand"," brand_id = {$id} ");
    }
    
    
    public function getBrandList($col,$where,$order,$start=0,$limit=10){
        
        if(empty($col)){
            $colstr ='*';
        }else{
            $colstr = join(',',$col);
        }
        $wherestr  = $this->getWhere($where);
        if(empty($order)){
            $orderby = ' order by  brand_id  desc ';
        }else{
            $local = array();
            foreach($order  as $k => $v){
                $local [] = " {$k} {$v} ";
            }
            $orderby = ' order by '.join(',',$local);
        }
        return $this->_db->fetchAll(" select {$colstr} from  `".DB_FIX."brand` {$wherestr} {$orderby} limit {$start},{$limit} ");
    }
    
    public function getBrandCount($where){
        
        $wherestr  = $this->getWhere($where);
        
        return $this->_db->fetchOne("  select count(1) from  `".DB_FIX."brand` {$wherestr}");
    }
    
    
    private function getWhere($where){
         $local = array();
         if(isset($where['keyword'])){
             $where['keyword'] = $this->_db->quote($where['keyword']);
             $local[] = " ( brand_name like  '%{$where['keyword']}%'   ) ";
         }
         
         $wherestr = '';
         if(!empty($local)){
             $wherestr = " WHERE  ". join(' and ',$local);
         }
         return $wherestr;
    }
    
    
}