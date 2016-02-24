<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ('Access Denied' );
}

class integralMdl{
    
    private  $_db;
    
    private static  $instance = null;
    
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new integralMdl();
        }
        
        return self::$instance;
    }
    
    private function __construct() {
        
        $this->_db = mysql::getInstance();
        
    }
    
    public  function addIntegral($info){
        
        if(empty($info)) return false;
        
        return $this->_db->insert(DB_FIX . 'integral',$info);
    } 
    
    public function replaceIntegral($info){
        if(empty($info)) return false;
        
        return $this->_db->replace(DB_FIX . 'integral',$info);
    }
    
    public function updateIntegral($id,$info){
        
        if(empty($info)) return false;
        
        $id = (int)$id;
        
        return $this->_db->update(DB_FIX.'integral',$info," id = {$id} ");
    }
    
    
    
    public function getIntegral($id){
        
        $id = (int)$id;
        
        return $this->_db->fetchRow("select  * from `".DB_FIX."integral` where id ={$id} limit 1 ");
    }
    
    public function getSumIntegralByUid($id){
        $id = (int)$id;
        $time = NOWTIME;
        return $this->_db->fetchOne("select  sum(num)  from `".DB_FIX."integral` where uid ={$id} AND expires_t >= {$time} ");
    }
    
    //获取未过期的
    public function getIntegralByUid($id){
        $id = (int)$id;
        $time = NOWTIME;
        return $this->_db->fetchRow("select  * from `".DB_FIX."integral` where  `uid` ={$id} AND `expires_t` >= {$time} AND `num` > 0 order by  expires_t ASC   limit 1 ");
    }
    
    
    public function delIntegral($id){
         $id = (int)$id;
         return $this->_db->delete(DB_FIX."integral"," id = {$id} ");
    }
    
    
    public function getIntegralList($col,$where,$order,$start=0,$limit=10){
        
        if(empty($col)){
            $colstr ='*';
        }else{
            $colstr = join(',',$col);
        }
        $wherestr  = $this->getWhere($where);
        if(empty($order)){
            $orderby = ' order by  a.id  desc ';
        }else{
            $local = array();
            foreach($order  as $k => $v){
                $local [] = " {$k} {$v} ";
            }
            $orderby = ' order by '.join(',',$local);
        }
        return $this->_db->fetchAll(" select {$colstr} from  `".DB_FIX."integral` a join `".DB_FIX."users` b ON (a.uid = b.uid)  {$wherestr} {$orderby} limit {$start},{$limit} ");
    }
    
    public function getIntegralCount($where){
        
        $wherestr  = $this->getWhere($where);
        
        return $this->_db->fetchOne("  select count(1) from  `".DB_FIX."integral` a  join `".DB_FIX."users` b ON (a.uid = b.uid)  {$wherestr}");
    }
    
    
    private function getWhere($where){
         $local = array();
         if(isset($where['keyword'])){
             $where['keyword'] = $this->_db->quote($where['keyword']);
             $local[] = " b.username like '%{$where['keyword']}%' ";
         }
         
         if(isset($where['uid'])){
             $where['uid'] = (int)$where['uid'];
             $local[] = " a.uid = {$where['uid']} ";
         }
         
         $wherestr = '';
         if(!empty($local)){
             $wherestr = " WHERE  ". join(' and ',$local);
         }
         return $wherestr;
    }
    
    
}