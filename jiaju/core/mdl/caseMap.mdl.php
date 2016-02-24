<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ( 'Access Denied' );
}

class caseMapMdl{
    
    private  $_db;
    
    private static  $instance = null;
    
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new caseMapMdl();
        }
        
        return self::$instance;
    }
    
    private function __construct() {
        
        $this->_db = mysql::getInstance();
        
    }
    
    public function addCaseMaps($info){
        foreach($info as $k=>$val){
            $info[$k]['case_id'] = (int) $info[$k]['case_id'];
            $info[$k]['cate_id'] = (int) $info[$k]['cate_id'];
        }
        return $this->_db->insertArr(DB_FIX.'case_map',$info);
    }
    
    public function delCaseMaps($case_id){
        $case_id = (int)$case_id;
        return $this->_db->delete(DB_FIX.'case_map'," case_id= {$case_id}");
    }
    
    public function getCaseMapsByCaseId($case_id){
        $case_id = (int)$case_id;
        return $this->_db->fetchCol("select  cate_id from ".DB_FIX."case_map where case_id = {$case_id} ");
    }
    
}
    