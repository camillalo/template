<?
/**
 * 文件级缓存
 */

class file_Log extends abstract_Log {
	
	//设定缓存根目录
	private $_root = null;
	private $_open = null;
	
	/**
	 * 初始化对像
	 * 
	 * @param mixed $setting 配置参数
	 * @return void
	 */
	public function init($setting) {

		$root = $setting['PATH'];
		$open = $setting['OPEN'];

		if (is_dir($root)) {
	
			$this->_root = preg_replace('/\/$/is','',$root);
			$this->_open = $open ;
		} else {
			
			throw new Exception("Log >> file_Log :: $root not is a dir .");		
		}
	}
	
	
	/**
	 * 保存缓存内容
	 * 
	 * @param String $key 键值
	 * @param Mixed $value 要保存的内容
	 * @param integer $ttl 缓存时间
	 * 
	 * @return void
	 */
	public function write($type, $content,$has_env=false){
		
		if($this->_open !== true) return ;
		
		$dirName = $this->_root.'/'.$type. '/';

		$fileName = $dirName . date('Y-m-d').'.txt';
		
		if ($this->mkdir($dirName)) {
			
			$has_env==true ? file_put_contents($fileName, $this->env(). print_r($content,true)."\n",FILE_APPEND) :  file_put_contents($fileName, print_r($content,true)."\n",FILE_APPEND);
		}
	}
	
	
	public function env(){
		
		$env['REQUEST_TIME'] = date('Y-m-d H:i:s',$_SERVER['REQUEST_TIME']);
		$env['REMOTE_ADDR'] = $_SERVER['REMOTE_ADDR'];
		$env['REQUEST_URI'] = $_SERVER['REQUEST_URI'];
		
		$env_msg = " \n ------------------------------ ";
		foreach($env as $key => $val){
			$env_msg .=  " $key : " . $val;
		}
		$env_msg .= " ------------------------------ \n ";
		return $env_msg;
	}
	/**
	 * 检查目录
	 * 
	 * @param String $dirName
	 * return Boolean
	 */
	private function mkdir($dirName) {
		
		if (!is_dir($dirName)) {
			
			return mkdir($dirName, 0777, true);
		} else {
			
			return true;
		}
	}
	
}