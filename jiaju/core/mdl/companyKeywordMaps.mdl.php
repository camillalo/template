<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ('Access Denied' );
}

class companyKeywordMapsMdl{
    
    private  $_db;
    
    private static  $instance = null;
    
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new companyKeywordMapsMdl();
        }
        
        return self::$instance;
    }
    
    private function __construct() {
        
        $this->_db = mysql::getInstance();
        
    }
    
    public  function addCompanyKeywordMaps($info){
        
        if(empty($info)) return false;
        
        return $this->_db->insert(DB_FIX . 'company_keyword_maps',$info);
    } 

    public function delCompanyKeywordMapsByuid($id){
         $id = (int)$id;
         return $this->_db->delete(DB_FIX."company_keyword_maps"," uid = {$id} ");
    }

    public function getCompanyKeywordMapsList($keywordids,$start=0,$limit=10){
        $wherestr  = $this->getWhere($keywordids);
        
        return $this->_db->fetchCol(" select uid,count(1) as num  from  `".DB_FIX."company_keyword_maps` {$wherestr} order by count(1) desc   limit {$start},{$limit} ");
    }
    
    public function getCompanyKeywordMapsCount($keywordids){
        
        $wherestr  = $this->getWhere($keywordids);
        
        return $this->_db->fetchOne(" select count(1) from  (select uid,count(1) as num   from  `".DB_FIX."company_keyword_maps` {$wherestr})tb2");
    }
    
    
    private function getWhere($keywordids){
         $local = array();
         foreach($keywordids as $val){
             $local[] = (int)$val;
         }
         $str = join(',',$local);
         $wherestr = " where keyword_id  in($str) GROUP BY uid  ";
         return $wherestr;
    }
    
    
}