<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ('Access Denied' );
}

class contentKeywordMapsMdl{
    
    private  $_db;
    
    private static  $instance = null;
    
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new contentKeywordMapsMdl();
        }
        
        return self::$instance;
    }
    
    private function __construct() {
        
        $this->_db = mysql::getInstance();
        
    }
    
    public  function addContentKeywordMaps($info){
        
        if(empty($info)) return false;
        
        return $this->_db->insert(DB_FIX . 'content_keyword_maps',$info);
    } 

    public function delContentKeywordMapsByContentId($id){
         $id = (int)$id;
         return $this->_db->delete(DB_FIX."content_keyword_maps"," content_id = {$id} ");
    }
    
    /**
     * SELECT content_id,count(keyword_id) as num  FROM `zx_content_keyword_maps`
     * where keyword_id in(1,2,3,4,5,7,8,9)   GROUP BY content_id  
     * HAVING count(keyword_id) >1 
     * order by count(keyword_id) desc ;
     * **/
    public function getContentKeywordMapsList($keywordids,$start=0,$limit=10){
        $wherestr  = $this->getWhere($keywordids);
        
        return $this->_db->fetchCol(" select content_id,count(1) as num  from  `".DB_FIX."content_keyword_maps` {$wherestr} order by count(1) desc   limit {$start},{$limit} ");
    }
    
    public function getContentKeywordMapsCount($keywordids){
        
        $wherestr  = $this->getWhere($keywordids);
        
        return $this->_db->fetchOne(" select count(1) from  (select content_id,count(1) as num   from  `".DB_FIX."content_keyword_maps` {$wherestr})tb2");
    }
    
    
    private function getWhere($keywordids){
         $local = array();
         foreach($keywordids as $val){
             $local[] = (int)$val;
         }
         $str = join(',',$local);
         $wherestr = " where keyword_id  in($str) GROUP BY content_id ";
         return $wherestr;
    }
    
    
}
