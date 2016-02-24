<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ('Access Denied' );
}

class usersMdl{
    
    private  $_db;
    
    private static  $instance = null;
    
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new usersMdl();
        }
        
        return self::$instance;
    }
    
    private function __construct() {
        
        $this->_db = mysql::getInstance();
        
    }
    
    public  function addUsers($info){
        
        if(empty($info)) return false;
        
        return $this->_db->insert(DB_FIX . 'users',$info);
    } 
    
     public  function replaceUsers($info){
        
        if(empty($info)) return false;
        
        return $this->_db->replace(DB_FIX . 'users',$info);
    } 
    
    
    public function updateUsers($id,$info){
        
        if(empty($info)) return false;
        
        $id = (int)$id;
        
        return $this->_db->update(DB_FIX.'users',$info," uid = {$id} ");
    }
    
    public function getUsers($id){
        
        $id = (int)$id;
        
        return $this->_db->fetchRow("select  * from `".DB_FIX."users` where uid ={$id} limit 1 ");
    }
    
    public function checkIsVip($id){
        $id = (int)$id;
        $time = NOWTIME;
        return  (int) $this->_db->fetchOne("select  uid from `".DB_FIX."users` where uid ={$id} && (num > 0 or day > {$time}  or gold > 0 ) ");
    }
    
    public function checkIsAuthentication($id){
        
        $id = (int)$id;
        
        return  (int) $this->_db->fetchOne("select  is_authentication from `".DB_FIX."users_ex` where uid ={$id} ");
    }
    
    public function getUsersEx($id){
        $id = (int)$id;
        
        return $this->_db->fetchRow("select  * from `".DB_FIX."users_ex` where uid ={$id} limit 1 ");
    }
    
    public function getUserExFacePic($id){
         $id = (int)$id;
        
        return $this->_db->fetchOne("select  face_pic from `".DB_FIX."users_ex` where uid ={$id} limit 1 ");
    }
    
     public  function replaceUsersEx($info){
        
        if(empty($info)) return false;
        
        return $this->_db->replace(DB_FIX . 'users_ex',$info);
    } 
    
    
    public function updateUsersEx($id,$info){
        
        if(empty($info)) return false;
        
        $id = (int)$id;
        
        return $this->_db->update(DB_FIX.'users_ex',$info," uid = {$id} ");
    }
    
    
    function checkEmail($email){
        if(!isEmail($email)) return false;
        $email = $this->_db->quote($email);
        return $this->_db->fetchOne("select  uid from `".DB_FIX."users` where email ='{$email}' limit 1 ");
    }
    
    
    public function getUsername($id){
        $id = (int)$id;
        
        return $this->_db->fetchOne("select  username from `".DB_FIX."users` where uid ={$id} limit 1 ");
    }
    
    
    
    public function getUsersByUsername($username){
        
        $username = $this->_db->quote($username);
        
        return $this->_db->fetchRow("select  * from `".DB_FIX."users` where username ='{$username}' limit 1 ");
    }
    
    
    public function delUsers($id){
         $id = (int)$id;
         return $this->_db->delete(DB_FIX."users"," uid = {$id} ");
    }
    
    
    public function getUsersList($col,$where,$order,$start=0,$limit=10){
        
        if(empty($col)){
            $colstr ='*';
        }else{
            $colstr = join(',',$col);
        }
        $wherestr  = $this->getWhere($where);
        if(empty($order)){
            $orderby = ' order by  uid  desc ';
        }else{
            $local = array();
            foreach($order  as $k => $v){
                $local [] = " {$k} {$v} ";
            }
            $orderby = ' order by '.join(',',$local);
        }
        return $this->_db->fetchAll(" select {$colstr} from  `".DB_FIX."users` {$wherestr} {$orderby} limit {$start},{$limit} ");
    }
    
    public function getUsersCount($where){
        
        $wherestr  = $this->getWhere($where);
        
        return $this->_db->fetchOne("  select count(1) from  `".DB_FIX."users` {$wherestr}");
    }
    
    
    private function getWhere($where){
         $local = array();
         if(isset($where['keyword'])){
             $where['keyword'] = $this->_db->quote($where['keyword']);
             $local[] = " ( username like  '%{$where['keyword']}%'   or realname like  '%{$where['keyword']}%'   or mobile like  '%{$where['keyword']}%'   ) ";
         }
         if(isset($where['rank_id'])){
             $where['rank_id'] = (int)$where['rank_id'];
             $local[] = " rank_id = {$where['rank_id']} ";
         }
         if(isset($where['type'])){
             $where['type'] = (int)$where['type'];
             $local[] = " type = {$where['type']} ";
         }
         if(isset($where['uid'])){
             $where['uid'] = (int)$where['uid'];
             $local[] = " uid = {$where['uid']} ";
         }
         $wherestr = '';
         if(!empty($local)){
             $wherestr = " WHERE  ". join(' and ',$local);
         }
         return $wherestr;
    }
    
    
}