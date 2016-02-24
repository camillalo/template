<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ( 'Access Denied' );
}

class adminMdl{
    
    private  $_db;
    
    private static  $instance = null;
    
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new adminMdl();
        }
        
        return self::$instance;
    }
    
    private function __construct() {
        
        $this->_db = mysql::getInstance();
        
    }
    
    public  function addAdmin($info){
        
        if(empty($info)) return false;
        
        return $this->_db->insert(DB_FIX . 'admin',$info);
    } 
    
    public function updateAdmin($id,$info){
        
        if(empty($info)) return false;
        
        $id = (int)$id;
        
        return $this->_db->update(DB_FIX.'admin',$info," admin_id = {$id} ");
    }
    
    public function getAdmin($id){
        
        $id = (int)$id;
        
        return $this->_db->fetchRow("select  * from `".DB_FIX."admin` where admin_id ={$id} limit 1 ");
    }
    
    public function getAdminByUsername($username){
        
        $username = $this->_db->quote($username);
        
        return $this->_db->fetchRow("select  * FROM `".DB_FIX."admin` where username = '{$username}' limit 1 ");
    }
    
    public function getAdminCountByGroupId($group_id){
        $group_id = (int)$group_id;
        
        return $this->_db->fetchOne("select  count(1) from `".DB_FIX."admin` where group_id ={$group_id}  ");
        
    }
    
    /*
     *@$col  array('a','b') 查询出来的列
     *@$where array('keyword'=>'尤哥','group_id'=>1)//查询条件
     *@$order array('admin_id'=>'desc');// 排序
     *@$start = 0, //开始位置
     *$limit  = 10  //取多少条 
     */
    public function getAdminList($col,$where,$order,$start=0,$limit=10){
        
        if(empty($col)){
            $colstr = '*';
        }else{
            $colstr = join(',',$col);
        }
        $wherestr  = $this->getWhere($where);
        if(empty($order)){
            $orderby = ' order by  a.admin_id  desc ';
        }else{
            $local = array();
            foreach($order  as $k => $v){
                $local [] = " {$k} {$v} ";
            }
            $orderby = ' order by '.join(',',$local);
        }
        return $this->_db->fetchAll(" select {$colstr} from  `".DB_FIX."admin` a join `".DB_FIX."group` b on (a.group_id = b.group_id) {$wherestr} {$orderby} limit {$start},{$limit} ");
    }
    
    public function getAdminCount($where){
        
        $wherestr  = $this->getWhere($where);
        
        return $this->_db->fetchOne("  select count(1) from  `".DB_FIX."admin` a join `".DB_FIX."group` b on (a.group_id = b.group_id)  {$wherestr}");
    }
    
    private function getWhere($where){
         $local = array();
         if(isset($where['keyword'])){
             $where['keyword'] = $this->_db->quote($where['keyword']);
             $local[] = " (a.username like '%{$where['keyword']}%' or a.realname like '%{$where['keyword']}%') ";
         }
         $wherestr = '';
         if(!empty($local)){
             $wherestr = " WHERE  ". join(' and ',$local);
         }
         return $wherestr;
    }
    
    
}