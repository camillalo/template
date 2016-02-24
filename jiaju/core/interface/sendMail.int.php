<?php
if ( !defined ( 'BASE_PATH') )
{
	exit ( 'Access Denied' );
}
/*
 * 发送邮件接口
 */
require BASE_PATH .'core/phpmailer/class.phpmailer.php';

class sendMailInt{
    
    private static  $instance = null;
    
    private $_cfg  = array(); 
    
    private $_mallObj = null;
    
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new sendMailInt();
        }
        
        return self::$instance;
    }
    
    private function __construct() { 
        $this->_cfg = import::getCfg('mail');      
    }
    
    public function setTitle ($title){
        if($this->_mallObj === null && !empty($this->_cfg['host']) && !empty($this->_cfg['port']) && !empty($this->_cfg['username'])&& !empty($this->_cfg['password']) ){          
            $this->_mallObj = new PHPMailer(true);
            $this->_mallObj->IsSMTP();
            $this->_mallObj->SetFrom($this->_cfg['email']);
            $this->_mallObj->AddReplyTo($this->_cfg['email']);
            $this->_mallObj->SMTPAuth = true;
            $this->_mallObj->Host       = $this->_cfg['host']; // sets the SMTP server
            $this->_mallObj->Port       = $this->_cfg['port'];                    // set the SMTP port for the GMAIL server
            $this->_mallObj->Username   = $this->_cfg['username']; // SMTP account username
            $this->_mallObj->Password   = $this->_cfg['password'];        // SMTP account password
            $this->_mallObj->AltBody = '您需要使用HTML查看该邮件内容！';
        }
        $this->_mallObj->Subject = $title; 
    }
    
    //直接发送HTML 
    public function setHtml($html){
        $this->_mallObj->MsgHTML($html);
    }
    
    public function setHtmlByT($template,$data=array()){
        $msg = $this->returnHtml($template, $data);
        $this->_mallObj->MsgHTML($msg);
    }
    
    private function returnHtml($template,$data = array()){
        $html = file_get_contents( BASE_PATH.'themes/mail/'.$template.'.html');
        foreach($data as $key=>$val){
            $html = str_replace('{'.$key.'}', $val, $html);
        }
        return $html;
    }
    
    public function AddAttachment($file){
        $this->_mallObj->AddAttachment($file);
    }
    
    public function send($to){
        $this->_mallObj->AddAddress($to);
        
        $this->_mallObj->Send();
     
        return true;
    }
    
    //判断是否需要发送邮件
    public function checkAuth($auth){
        
       return isset($this->_cfg[$auth]) ?  (int)$this->_cfg[$auth] : 0;
       
    }
    
  
    
}    