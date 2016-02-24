<?
//定义当前目录常量
define('_LOG_CLASS_PATH', dirname(__FILE__) . '/');
//包含公用接口文件
require_once( _LOG_CLASS_PATH . 'interface.log.php');
require_once( _LOG_CLASS_PATH . 'abstract.log.php');

/**
 * 缓存类
 */
class log {

	/**
	 * 获取实例并初始化对像
	 * 
	 * @param String $plugName 缓存插件名
	 * @return void
	 */
	public static function factory($plugName = null, $setting = '') {
		
		require_once('config/config.log.php');

		//判断是否调用系统配置
		if (empty($plugName)) {

			$plugName = _LOG_SYSTEM;
			//$setting = _LOG_PATH;	
		}
            
        $setting = strtoupper($setting);
        if (!empty($setting) && isset($GLOBALS['lOG_CONFIG'][$setting])) {

            $setting = $GLOBALS['lOG_CONFIG'][$setting];
        } else {

            $setting = $GLOBALS['lOG_CONFIG']['WWW'];    
        }
        

		$plugName = strtolower($plugName);
		$sKey = self::getSKey($plugName, $setting);
		
		if (core::registry($sKey)) {
			
			return core::register($sKey);
		} else {
			
			$obj = self::instance($plugName);
			$obj->init($setting);
			core::register($sKey, $obj);
			//print_r($obj);
			return $obj;	
		}
	} 
	
	
	/**
	 * 通过插件名和参数组成唯一识别字串
	 * 
	 * @param String $plugName 插件名
	 * @param Mixed $setting 参数
	 * @return String
	 */
	private static function getSKey($plugName, $setting) {
		
		if (is_array($setting)) {
			
			$setStr = serialize($setting);
		} else if (is_object($setting)) {
			//如$setting有 private 的变量，则系统会出现问题
			$setStr = serialize($setting);	
		} else {
			
			$setStr = strval($setting);
		}

		return md5($setStr . $plugName);
	}
	
	/**
	 * 通过插件名获取对像
	 * 
	 * @param String $plugName 插件名
	 * @return void
	 */
	private static function instance($plugName) {
		
		if (empty($plugName)) {
			
			$plugName = _DEFAULT_LOG_ENGINE;
		}
		
		$plugFileName = self::getFileName($plugName);
		$plugClass = self::getClassName($plugName);
		
		if (file_exists($plugFileName)) {
			
			require_once($plugFileName);
			
			if (class_exists($plugClass)) {
				
				$obj = new $plugClass;
				return $obj;	
			} else {
				
				throw new Exception("ILog:: class $plugClass not found in PHP file $plugFileName .");
			}
		} else {
			
			throw new Exception("Log:: not found log plugin file $plugFileName .");
		}
	}
	
	/**
	 * 获取插件文件名
	 * 
	 * @param String $plugName 插件名
	 * @return String
	 */
	private static function getFileName($plugName) {
		
		return sprintf("%s/plug/class.%s.php", _LOG_CLASS_PATH, $plugName);	
	} 
	
	/**
	 * 获取插件类名
	 * 
	 * @param String $plugName 插件名
	 * @return void
	 */
	private static function getClassName($plugName) {
		
		return sprintf("%s_log", $plugName);
	}
}