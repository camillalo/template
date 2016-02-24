<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ('Access Denied' );
}

class productsMdl{
    
    private  $_db;
    
    private static  $instance = null;
    
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new productsMdl();
        }
        
        return self::$instance;
    }
    
    private function __construct() {
        
        $this->_db = mysql::getInstance();
        
    }
    
    public  function addProducts($info){
        
        if(empty($info)) return false;
        
        return $this->_db->insert(DB_FIX . 'products',$info);
    } 
    
    public function updateProducts($id,$info){
        
        if(empty($info)) return false;
        
        $id = (int)$id;
        
        return $this->_db->update(DB_FIX.'products',$info," id = {$id} ");
    }
    
    public function getProductsByCategoryId($id,$num){
          $id = (int)$id;
          $num = (int)$num;
          return $this->_db->fetchAll("select  * from `".DB_FIX."products` where category_id ={$id} and is_show =1 limit 0,{$num} ");
    }


    public function getProducts($id){
        
        $id = (int)$id;
        
        return $this->_db->fetchRow("select  * from `".DB_FIX."products` where id ={$id}  limit 1 ");
    }
    
    public function getProuctsByCompanyId($id,$num){
        $id = (int)$id;
        $num = (int)$num;
        
        return $this->_db->fetchAll("select  id,product_name,face_pic from `".DB_FIX."products` where company_id ={$id} AND is_show =1 limit 0,{$num} ");
    }
    
    public function getNewCompanyProucts($num){
         $num = (int)$num;
        return $this->_db->fetchAll("select a.id,a.product_name,a.mall_price,b.company_name from `".DB_FIX."products` a join `".DB_FIX."company` b ON (a.company_id = b.uid)   where a.is_show =1  limit 0,{$num} ");
    }
    
    public function delProducts($id){
         $id = (int)$id;
         return $this->_db->delete(DB_FIX."products"," id = {$id} ");
    }
    
      public function getProductsByIds($ids){
        if(empty($ids)) return array();
        $local = array();
        foreach($ids as $v){
            $local [] = (int) $v;
        }
        $str = join(',',$local);
        return $this->_db->fetchAll("select id,face_pic,product_name,model,description  from `".DB_FIX."products` where id in({$str}) ");
    }
    
    
    public function getProductsCountPair($ids){
       if(empty($ids))  return array();
       foreach($ids as $k=>$v){
           $ids[$k] = (int)$v;
       }
       $idstr = join(',',$ids);
       return $this->_db->fetchPair("select  company_id ,count(1) as num  from `".DB_FIX."products`  where company_id in({$idstr}) and is_show = 1  group by company_id  ");
   }
    
    public function getProductsList($col,$where,$order,$start=0,$limit=10){
        
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
        return $this->_db->fetchAll(" select {$colstr} from  `".DB_FIX."products` {$wherestr} {$orderby} limit {$start},{$limit} ");
    }
    
    public function getProductsCount($where){
        
        $wherestr  = $this->getWhere($where);
        
        return $this->_db->fetchOne("  select count(1) from  `".DB_FIX."products` {$wherestr}");
    }
    
    
    private function getWhere($where){
         $local = array();
         if(isset($where['keyword'])){
             $where['keyword'] = $this->_db->quote($where['keyword']);
             $local[] = " ( product_name like  '%{$where['keyword']}%'   ) ";
         }
         if(isset($where['category_id'])){
             $where['category_id'] = (int)$where['category_id'];
             $local[] = " category_id = {$where['category_id']} ";
         }
         if(isset($where['brand_id'])){
             $where['brand_id'] = (int)$where['brand_id'];
             $local[] = " brand_id = {$where['brand_id']} ";
         }
         if(isset($where['company_id'])){
             $where['company_id'] = (int)$where['company_id'];
             $local[] = " company_id = {$where['company_id']} ";
         }
         if(isset($where['is_show'])){
             $where['is_show'] = (int)$where['is_show'];
             $local[] = " is_show = {$where['is_show']} ";
         }
         
         if(!empty($where['last_category_id'])){
             foreach($where['last_category_id'] as $k=>$v){
                $where['last_category_id'][$k] = (int)$v;
             }
             $idstr = join(',',$where['last_category_id']);
             $local[] = " category_id in ({$idstr}) ";
         }
         
         
         $wherestr = '';
         if(!empty($local)){
             $wherestr = " WHERE  ". join(' and ',$local);
         }
         return $wherestr;
    }
    
    
}