<?php
/**
 * 读取邮件
 */
class del_email extends api_comm  {

	/**
	 * 设置响应的消息体  返回值必须是json
	 * 
	 */
	
	public function get_response() {
		header('Content-Type:text/html;charset=UTF-8');
		ini_set('date.timezone','PRC'); //时区设置
		require_once (dirname(__FILE__).'/../../comm/email/class.pop3.php');	
		require_once (substr(dirname(__FILE__),0,-13).'/config/config.php');		

		$pop3obj = new SocketPOP3Client($config['email'], $config['password'], $config['host'], $config['port'],$config['ssl_port']);
		$isLogin = $pop3obj->popLogin();

		//获取邮件的id

		$id      = $this->request_arr['id']?$this->request_arr['id']:'';
		if(empty($id)) {
			$this->result['code'] = 9999; 
            $this->result['msg'] = '参数错误';
            return $result = (object)array(); 
		}
		$intMailId = $id-1;
		$result = $pop3obj->delMail($intMailId);
		$pop3obj->popLogout();
		$pop3obj->closeHost();

		return $result;

	}

	//查看数据表中是否已经存在
	public function check_title($title){ 
		$comm = core::Singleton('user.comm');
		$result = $comm->check_title($title);unset($comm);
        return $result;
	}

	//添加数据
	public function insert_data($title,$content){ 
		$comm = core::Singleton('user.comm');
		$result = $comm->insert_data($title,$content);unset($comm);
        return $result;
	}

}

?>