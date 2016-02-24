<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ('Access Denied' );
}

class keywordsMdl{
    
    private  $_db;
    
    private static  $instance = null;
    
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new keywordsMdl();
        }
        
        return self::$instance;
    }
    
    private function __construct() {
        
        $this->_db = mysql::getInstance();
        
    }
    
    public  function addKeywords($info){
        
        if(empty($info)) return false;
        
        return $this->_db->insert(DB_FIX . 'keywords',$info);
    } 
    
    public function getKeywordsIdByKeyword($keyword){
        
        $keyword = $this->_db->quote($keyword);
        
        return $this->_db->fetchOne("select id from  ".DB_FIX . "keywords where keyword = '{$keyword}' ");
    }
    
    public function getIdsByKeywords($keywords){
        if(empty($keywords)) return array();
        $local = array();
        foreach($keywords as $val){
            $val = '\''.$this->_db->quote($val).'\'';
            $local[] = $val;
        }
        $str = join(',',$local);
        return $this->_db->fetchCol("select id  from ".DB_FIX . "keywords where keyword in ({$str}) ");
    }

}