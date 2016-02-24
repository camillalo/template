<?
/**
 * Cache 类的接口文件
 * 
 * @author hzjsq
 * @version 0.1
 */

interface interface_Log {
	
	/**
	 * 初始化对像
	 * 
	 * @param mixed $setting 配置参数
	 * @return void
	 */
	public function init($setting);
	/**
	 * 保存缓存内容
	 * 
	 * @param String $key 键值
	 * @param Mixed $value 要保存的内容
	 * @param integer $ttl 缓存时间
	 * 
	 * @return void
	 */
	public function write($type,$messge,$has_evn);
	
}