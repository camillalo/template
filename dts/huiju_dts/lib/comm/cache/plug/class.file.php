<?
/**
 * 文件级缓存
 */

class file_Cache extends abstract_Cache {
	
	//设定缓存根目录
	private $_root = null;
	
	/**
	 * 初始化对像
	 * 
	 * @param mixed $setting 配置参数
	 * @return void
	 */
	public function init($root) {
		
		if (is_dir($root)) {
			
			$this->_root = preg_replace('/\/$/is','',$root);
		} else {
			
			throw new Exception("Cache >> file_Cache :: $root not is a dir .");		
		}
	}
	
	/**
	 * 通过 Key 值获取目录
	 * @param String $key 键值
	 * @return String
	 */
	private function getFileName($key) {
		
		$hash = md5($key);
		
		return sprintf('%s/%s/%s/%s.inc', $this->_root, substr($hash,0,2), substr($hash, 2,2), substr($hash, 4,32));
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
	public function set($key, $value, $ttl){
		
		$ttl = intval($ttl);
		if ($ttl <= 60 ) {	
			$ttl = 60;
		}		
		
		$fileName = $this->getFileName($key);
		$content = serialize(array('ttl'=>$ttl, 'content' => $value));

		$dirName = dirname($fileName);
		if ($this->mkdir($dirName)) {
			
			file_put_contents($fileName, $content);
		}
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
	
	/**
	 * 获取指定内容
	 * 
	 * @param $key 键值
	 * @return Mixed
	 */
	public function get($key) {
		
		$fileName = $this->getFileName($key);
		
		if (file_exists($fileName)) {
			
			$fTime = filemtime($fileName);
			$content = unserialize(file_get_contents($fileName));
			$ttl = intval($content['ttl']);
			if (time() - $fTime >= $ttl) {
				
				unlink($fileName);
				return null;
			} else {
				
				return $content['content'];		
			}
		} else {
			
			return null;
		}	
	}
	
	/**
	 * 删除指定键值内容
	 * 
	 * @param String $key 键值
	 * @return void
	 */
	public function delete($key) {
		
		$fileName = $this->getFileName($key);
	
		if (file_exists($fileName)) {
			
			return unlink($fileName);
		}
	}
}