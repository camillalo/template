<?php

if ( !defined ( 'BASE_PATH') )
{
	exit ( 'Access Denied' );
}
/*
 * 防注水 
 */
class injectionInfo{
    
    private static  $instance = null;
    
    private $_cache;
    
    private $_token = 'injection';
    
    private $_datas =  null;
    
    private $_userToken = null;
    
    private $_record = true;
    
    private $_info = array();
      
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new injectionInfo();
        }
        
        return self::$instance;
    }
    
    private function __construct() { 
            
    }
  

    
    public function init(){
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $this->load();
            if(empty($this->_datas)) return;
            if(!isset($this->_datas[$_GET['ctl']][$_GET['act']])) return;

            $this->_userToken = md5(((int)getUid()).'_'.getIp().'_'.$_GET['ctl'].'_'.$_GET['act']);
            import::getMdl('injectionInfo');
            $this->_info = injectionInfoMdl::getInstance()->getInjectionInfoByToken($this->_userToken);
            if(empty($this->_info)) return;
            if($this->_info['last_t'] > NOWTIME - $this->_datas[$_GET['ctl']][$_GET['act']]['t'] )
                errorAlert ('亲！您操作的太频繁了！先休息片刻！');
            $lastD = date('Ymd',  $this->_info['last_t']);
            $today = date('Ymd', NOWTIME);
            if($lastD  == $today){ //如果是今天 那么要判断 是否超过了最大次数
                if($this->_info['num'] > $this->_datas[$_GET['ctl']][$_GET['act']]['num'])
                errorAlert ('亲！感谢您今天的努力！不过要当心身体哦！服务器当前拒绝了您的请求！');
            }
        }
      
    }
    
    public function setNotRecord(){
        
        $this->_record = false;
        
    }
    
    public function log(){
         if(!$this->_record) return;
         if($_SERVER['REQUEST_METHOD'] === 'POST'){ 
               if(empty($this->_userToken)) return;
               import::getMdl('injectionInfo');
               if(empty($this->_info)){
                    $info = array(
                        'last_t' => NOWTIME,
                        'num' => 1,
                        'token' => $this->_userToken
                    );
                    injectionInfoMdl::getInstance()->addInjectionInfo($info);
                    return;
                }
                $num = $this->_info['num'] + 1;
                $lastD = date('Ymd',  $this->_info['last_t']);
                $today = date('Ymd', NOWTIME);
                if($lastD < $today) $num = 0;
                $info = array(
                    'last_t' => NOWTIME,
                    'num' => $num,
                );
                injectionInfoMdl::getInstance()->updateInjectionInfo($this->_userToken,$info);
                return;
            
         }
    }
    
    private function load(){
        $this->_cache = fileCache::getInstance();    
        $this->_datas = $this->_cache->load($this->_token);
        if(empty($this->_datas)) $this->put();
    }
    
    private function put(){
        import::getMdl('injection');
        $datas= injectionMdl::getInstance()->getAllInjection();
        $local = array();
        foreach($datas  as $v){
            $local[$v['ctl']][$v['act']] = array('t'=>$v['t'],'num'=>$v['num']);
        }
        $this->_datas = $local;
        $this->_cache->put($this->_token,  $this->_datas ,0); //20天
        return $this->_datas;
    }
    
      
    public function __destruct() {
        $this->log();
    }

    
}    