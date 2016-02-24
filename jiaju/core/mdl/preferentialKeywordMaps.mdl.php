<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ('Access Denied' );
}

class preferentialKeywordMapsMdl{
    
    private  $_db;
    
    private static  $instance = null;
    
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new preferentialKeywordMapsMdl();
        }
        
        return self::$instance;
    }
    
    private function __construct() {
        
        $this->_db = mysql::getInstance();
        
    }
    
    public  function addPreferentialKeywordMaps($info){
        
        if(empty($info)) return false;
        
        return $this->_db->insert(DB_FIX . 'preferential_keyword_maps',$info);
    } 

    public function delPreferentialKeywordMapsByPreferentialId($id){
         $id = (int)$id;
         return $this->_db->delete(DB_FIX."preferential_keyword_maps"," preferential_id = {$id} ");
    }
    
    /**
     * SELECT preferential_id,count(keyword_id) as num  FROM `zx_preferential_keyword_maps`
     * where keyword_id in(1,2,3,4,5,7,8,9)   GROUP BY preferential_id  
     * HAVING count(keyword_id) >1 
     * order by count(keyword_id) desc ;
     * **/
    public function getPreferentialKeywordMapsList($keywordids,$start=0,$limit=10){
        $wherestr  = $this->getWhere($keywordids);
        
        return $this->_db->fetchCol(" select preferential_id,count(1) as num  from  `".DB_FIX."preferential_keyword_maps` {$wherestr} order by count(1) desc   limit {$start},{$limit} ");
    }
    
    public function getPreferentialKeywordMapsCount($keywordids){
        
        $wherestr  = $this->getWhere($keywordids);
        
        return $this->_db->fetchOne(" select count(1) from  (select preferential_id,count(1) as num   from  `".DB_FIX."preferential_keyword_maps` {$wherestr})tb2");
    }
    
    
    private function getWhere($keywordids){
         $local = array();
         foreach($keywordids as $val){
             $local[] = (int)$val;
         }
         $str = join(',',$local);
         $wherestr = " where keyword_id  in($str) GROUP BY preferential_id ";
         return $wherestr;
    }
    
    
}