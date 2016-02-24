<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ('Access Denied' );
}

class sensitiveWordMdl{
    
    private  $_db;
    
    private static  $instance = null;
    
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new sensitiveWordMdl();
        }
        
        return self::$instance;
    }
    
    private function __construct() {
        
        $this->_db = mysql::getInstance();
        
    }
    
    public  function addSensitiveWord($info){
        
        if(empty($info)) return false;
        
        return $this->_db->insert(DB_FIX . 'sensitive_word',$info);
    } 
    
    public function updateSensitiveWord($id,$info){
        
        if(empty($info)) return false;
        
        $id = (int)$id;
        
        return $this->_db->update(DB_FIX.'sensitive_word',$info," id = {$id} ");
    }
    
    public function getSensitiveWord($id){
        
        $id = (int)$id;
        
        return $this->_db->fetchRow("select  * from `".DB_FIX."sensitive_word` where id ={$id} limit 1 ");
    }
    
    
    public function getAllSensitiveWord(){
        
        return $this->_db->fetchCol(" select  `word`  from `".DB_FIX."sensitive_word` ");
    }
    
    
    public function delSensitiveWord($id){
         $id = (int)$id;
         return $this->_db->delete(DB_FIX."sensitive_word"," id = {$id} ");
    }
    
    
    public function getSensitiveWordList($col,$where,$order,$start=0,$limit=10){
        
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
        return $this->_db->fetchAll(" select {$colstr} from  `".DB_FIX."sensitive_word` {$wherestr} {$orderby} limit {$start},{$limit} ");
    }
    
    public function getSensitiveWordCount($where){
        
        $wherestr  = $this->getWhere($where);
        
        return $this->_db->fetchOne("  select count(1) from  `".DB_FIX."sensitive_word` {$wherestr}");
    }
    
    
    private function getWhere($where){
         $local = array();
         if(isset($where['keyword'])){
             $where['keyword'] = $this->_db->quote($where['keyword']);
             $local[] = " ( word like  '%{$where['keyword']}%'   ) ";
         }
         
         $wherestr = '';
         if(!empty($local)){
             $wherestr = " WHERE  ". join(' and ',$local);
         }
         return $wherestr;
    }
    
    
}