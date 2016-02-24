<?php
if ( !defined ( 'BASE_PATH') )
{
	exit ( 'Access Denied' );
}
/*
 * 短信接口 短信不再打LOG了 非自己使用
 */
class smsInt{
    
    private static  $instance = null;
    
    private $_cfg = array();
    
    private $_type = array(0,1,2); //0官方合作商的,1代表客亲通短信 2 代表沃科短信
            
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new smsInt();
        }
        
        return self::$instance;
    }
    
    private function __construct() { 
        $this->_cfg = import::getCfg('sms');
    }
    
    //对外接口
    public function send($model,$mobileArr,$data = array()){
        if(empty($model) || empty($mobileArr)) return;
        $needSend = isset($this->_cfg[$model]) ? (int)  $this->_cfg[$model] : 0;
        if(empty($needSend)) return;
        
        $smsType =  isset($this->_cfg['service']) ? (int)$this->_cfg['service'] : 0;
        if(!in_array($smsType,  $this->_type)) return;
        
        if(empty($this->_cfg['username']) || empty($this->_cfg['password'])) return;
        
        $content = $this->makeContent($model, $data);
        
        if(empty($content)) return;
        
        return $this->yunSmsSend($mobileArr, $content);
      
    }
    
    public function sendToAdmin($model,$data=array()){
        $str = isset($this->_cfg['admintel']) ? trim( $this->_cfg['admintel']) : '';
        if(empty($str)) return;
        $str = str_replace('，',',', $str);
        $mobileArr = explode(',',$str);
        foreach($mobileArr as $k=>$v){
            if(empty($v)) unset($mobileArr[$k]);            
        }
        return $this->send($model, $mobileArr, $data);
    }
    
    
    private function yunSmsSend($mobileArr,$content){
        $mobile = join(',',$mobileArr);
        $api = 'http://http.yunsms.cn/tx/?uid='.$this->_cfg['username'].'&pwd='.  strtolower(md5($this->_cfg['password'])).'&encode=utf8&mobile='.$mobile.'&content='.  urlencode($content);
        //echo $api ;
       // echo '<br />';
        $ret = (int)  file_get_contents($api);
       // echo $ret;die;
        $return = $ret === 100 ? true : false;
        return $return;
    }
    
    
    private function makeContent($model,$data = array()){
        $key = $model.'content';
        $content = isset($this->_cfg[$key]) ?  $this->_cfg[$key] : '';
        if(empty($content)) return false;
        if(empty($data)) return $content;
        $content = str_replace('{', '{',$content);
        $content = str_replace('}', '}',$content);
        foreach($data  as $k => $v){
            $k  = '{'.$k.'}';
            $content = str_replace($k, $v, $content);
        }
        return $content;
    }
    
    //系统短信后期开发
    public function system($mobileArr,$content){
        
    }
    
}    