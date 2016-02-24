<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ('Access Denied' );
}

class designerMdl{
    
    private  $_db;
    
    private static  $instance = null;
    
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new designerMdl();
        }
        
        return self::$instance;
    }
    
    private function __construct() {
        
        $this->_db = mysql::getInstance();
        
    }
    
    public  function addDesigner($info){
        
        if(empty($info)) return false;
        
        return $this->_db->insert(DB_FIX . 'designer',$info);
    } 
    
    public function updateDesigner($id,$info){
        
        if(empty($info)) return false;
        
        $id = (int)$id;
        
        return $this->_db->update(DB_FIX.'designer',$info," id = {$id} ");
    }
    
    public function getDesigner($id){
        
        $id = (int)$id;
        
        return $this->_db->fetchRow("select  * from `".DB_FIX."designer` where id ={$id} limit 1 ");
    }
    
    public function getDesignerName($id){
        $id = (int)$id;
        
        return $this->_db->fetchOne("select  name from `".DB_FIX."designer` where id ={$id} limit 1 ");
    }

    //按用户ID取一条 设计师的时候用
    public function getDesignerByUid($uid){
         $uid = (int)$uid;
        
        return $this->_db->fetchRow("select  * from `".DB_FIX."designer` where uid ={$uid} limit 1 ");
    }
    
    public function getDesignerIdByUid($uid){
        $uid = (int)$uid;
        
        return $this->_db->fetchOne("select  id  from `".DB_FIX."designer` where uid ={$uid} limit 1 ");
    }
    
    
    public function getAllDesignerPairByUid($uid){
        $uid = (int)$uid;
        
        return $this->_db->fetchPair("select   id,name from `".DB_FIX."designer` where uid ={$uid}  ");
    }
    
    public function getDesignerByIds($ids){
        if(empty($ids)) return array();
        foreach($ids  as $k=>$v){
            $ids[$k] = (int)$v;
        }
        $str = join(',',$ids);
        return $this->_db->fetchPair("select  id,name from  `".DB_FIX."designer`  where id in({$str}) ");
    }
    

    
     public function getDesignerInfoByUids($ids){
        if(empty($ids)) return array();
        foreach($ids  as $k=>$v){
            $ids[$k] = (int)$v;
        }
        $str = join(',',$ids);
        $datas = $this->_db->fetchAll("select  id,uid,name,face_pic from  `".DB_FIX."designer`  where uid in({$str}) ");
        $return = array();
        foreach($datas as $val){
            $return[$val['uid']] = $val;
        }
        return $return;
    }
    
    
    public function getDesignersByUid($uid,$num){
        $uid = (int)$uid;
        $num = (int)$num;
        
        return $this->_db->fetchAll("select  id,name,face_pic,position from `".DB_FIX."designer` where uid ={$uid} limit 0,{$num} ");
    }

    
    public function delDesigner($id){
         $id = (int)$id;
         return $this->_db->delete(DB_FIX."designer"," id = {$id} ");
    }
    
    
    public function getDesignerList($col,$where,$order,$start=0,$limit=10){
        
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
        return $this->_db->fetchAll(" select {$colstr} from  `".DB_FIX."designer` {$wherestr} {$orderby} limit {$start},{$limit} ");
    }
    
    public function getDesignerCount($where){
        
        $wherestr  = $this->getWhere($where);
        
        return $this->_db->fetchOne("  select count(1) from  `".DB_FIX."designer` {$wherestr}");
    }
    
    
    private function getWhere($where){
         $local = array();
         if(isset($where['keyword'])){
             $where['keyword'] = $this->_db->quote($where['keyword']);
             $local[] = " ( name like  '%{$where['keyword']}%'   ) ";
         }
         if(isset($where['uid'])){
             $where['uid'] = (int)$where['uid'];
             $local[] = " uid = {$where['uid']} ";
         }
    
         if(isset($where['area_id'])){
             $where['area_id'] = (int)$where['area_id'];
             $local[] = " area_id = {$where['area_id']} ";
         }
         if(isset($where['from_time'])){
             $where['from_time'] = (int)$where['from_time'];
             $local[] = " from_time = {$where['from_time']} ";
         }
         
         
         $wherestr = '';
         if(!empty($local)){
             $wherestr = " WHERE  ". join(' and ',$local);
         }
         return $wherestr;
    }
    
    
}