<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ('Access Denied' );
}

class askKeywordMapsMdl{
    
    private  $_db;
    
    private static  $instance = null;
    
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new askKeywordMapsMdl();
        }
        
        return self::$instance;
    }
    
    private function __construct() {
        
        $this->_db = mysql::getInstance();
        
    }
    
    public  function addAskKeywordMaps($info){
        
        if(empty($info)) return false;
        
        return $this->_db->insert(DB_FIX . 'ask_keyword_maps',$info);
    } 

    public function getAskKeywordCol($ask_id){
        $ask_id = (int)$ask_id;
        
        return $this->_db->fetchCol("select keyword_id from `".DB_FIX."ask_keyword_maps` where ask_id = {$ask_id} limit 0,20");
    }
    
    public function delAskKeywordMapsByAskId($id){
         $id = (int)$id;
         return $this->_db->delete(DB_FIX."ask_keyword_maps"," ask_id = {$id} ");
    }
    
    /**
     * SELECT ask_id,count(keyword_id) as num  FROM `zx_ask_keyword_maps`
     * where keyword_id in(1,2,3,4,5,7,8,9)   GROUP BY ask_id  
     * HAVING count(keyword_id) >1 
     * order by count(keyword_id) desc ;
     * **/
    public function getAskKeywordMapsList($keywordids,$start=0,$limit=10){
        $wherestr  = $this->getWhere($keywordids);
        
        return $this->_db->fetchCol(" select ask_id,count(1) as num  from  `".DB_FIX."ask_keyword_maps` {$wherestr} order by count(1) desc   limit {$start},{$limit} ");
    }
    
    public function getAskKeywordMapsCount($keywordids){
        
        $wherestr  = $this->getWhere($keywordids);
        
        return $this->_db->fetchOne(" select count(1) from  (select ask_id,count(1) as num   from  `".DB_FIX."ask_keyword_maps` {$wherestr})tb2");
    }
    
    
    private function getWhere($keywordids){
         $local = array();
         foreach($keywordids as $val){
             $local[] = (int)$val;
         }
         $str = join(',',$local);
         $wherestr = " where keyword_id  in($str) GROUP BY ask_id ";
         return $wherestr;
    }
    
    
}
