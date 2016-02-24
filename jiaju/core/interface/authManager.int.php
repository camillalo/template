<?php
if ( !defined ( 'BASE_PATH') )
{
	exit ( 'Access Denied' );
}
//后台管理员权限判断 登录验证等集合
class authManager{
    
    private $_db;
        
    private static  $instance = null;
    
    private $_admininfo;
    
    private $_groupid = 1;//超级管理员的群组ID
    
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new authManager();
        }
        
        return self::$instance;
    }
    
    private function __construct() {
        
        $this->_db = mysql::getInstance();
        
        $this->_admininfo = isset($_SESSION['admin']) ? $_SESSION['admin'] :  array();
    }
     
    public function checkAuth($auth){
        
        if(empty($this->_admininfo)) return false;
        
        if((int)$this->_admininfo['group_id'] === $this->_groupid ) return true;
        
        if(empty($this->_admininfo['auth'])) return false;
        
        if(!in_array($auth, $this->_admininfo['auth'])) return false;
        
        return true;
    } 
    
    public function login($username,$password){
        
        import::getMdl('admin');
        
        $this->_admininfo = adminMdl::getInstance()->getAdminByUsername($username);
        
        if(empty($this->_admininfo)) return false;
        
        if($password !== $this->_admininfo['password']) return false;
        
        if((int)$this->_admininfo['is_lock'] === 1) return false;
        
        $admin = array(
            'admin_id' => $this->_admininfo['admin_id'],
            'realname' => $this->_admininfo['realname'],
            'group_id' => $this->_admininfo['group_id'],
            'last_t'  => $this->_admininfo['last_t'], 
            'last_ip'  => $this->_admininfo['last_ip'], 
        );
        
        if((int)  $this->_admininfo['group_id'] !== $this->_groupid){
            import::getMdl('groupMap');
            $admin['auth'] = groupMapMdl::getInstance()->getGroupMapsPairByGroupId($this->_admininfo['group_id']);
        }
        
        $info = array(
            'last_ip' => getIp(),
            'last_t'  => date('Y-m-d H:i:s',NOWTIME)  
        );
        if(!adminMdl::getInstance()->updateAdmin($this->_admininfo['admin_id'],$info)) return false;
        $admin['username'] = $username;
        $_SESSION['admin'] = $admin;
        
        return true;
    }
    
}