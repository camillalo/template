<?
/**
 * Memecache缓存
 */

class mem_Cache extends abstract_Cache {

    /**
     * Memcache 对像 
     * @var Object 
     */
    private $mObject = null;
		
	/**
	 * 析构
	 * 
	 * @param void
	 * @return void
	 */
	public function __construct() {

		//do Nothing
        $this->mObject = new Memcache;
	}
	
	/**
	 * 初始化对像
	 * 
	 * @param mixed $setting 配置参数
	 * @return void
	 */
	public function init($cfg) {

        if (is_array($cfg)) {

            foreach($cfg as $item) {

                $this->mObject->addServer($item['HOST'], $item['PORT']);
            }
        }
	}
	
	/**
	 * 获取数据
	 * 
	 * @param String $key 键值
	 * @return Mixed
	 */
	public function get($key) {

        $val = $this->mObject->get($key);

		return $val;
	}
	
	/**
	 * 设置缓存数据
	 * 
	 * @param String $key 键值
	 * @param Mixed $value 要缓存的内容
	 * @param Integer $ttl 存活时间
	 * @return void
	 */
	public function set($key, $value, $ttl = CACHE_DEFAULT_EXP_TIME2) {

        //注： 第三个参数 memcache 与 ttserver 不一样   ;  上线后 $ttl 变为0 永远不过期
		//$value = serialize($value);
		//$content = array('value' => $value, 'uptime'=>time(), 'ttl' => $ttl);
       // $this->mObject->set($key, $content, 0, $ttl);
		$this->mObject->set($key, $value, MEMCACHE_COMPRESSED, $ttl);
	}
	
	/**
	 * 删除指定Key的内容
	 * 
	 * @param String $key 
	 * @return void
	 */
	public function delete($key) {
		
        $this->mObject->delete($key);
	}
}