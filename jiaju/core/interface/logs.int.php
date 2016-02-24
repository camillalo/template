<?php
if ( !defined ( 'BASE_PATH') )
{
	exit ( 'Access Denied' );
}
/*
 * 分类的接口类
 */
class logsInt{
    
    private static  $instance = null;
    
    private $_cache;
    
    private $_token = 'logsInt';
    
    private $_datas =  null;
    
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new logsInt();
        }
        
        return self::$instance;
    }
    
    private function __construct() { 
        $this->_cache = fileCache::getInstance();        
    }
    
    public function systemLogs($title,$data1 = null,$data2 = null){
        import::getMdl('systemLogs');
        $info = array(
            'username' => $_SESSION['admin']['username'],
            'url'   => $_SERVER['REQUEST_URI'] ,
            'title'    => $title,
            'raw_data' => json_encode($data1),
            'processed_data' => json_encode($data2),
            'ip' => getIp(),
            't' => NOWTIME
        );
        systemLogsMdl::getInstance()->addSystemLogs($info);
    }
    
    
    
}    