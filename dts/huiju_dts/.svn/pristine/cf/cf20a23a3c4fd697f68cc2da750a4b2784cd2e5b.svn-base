<?
//定义当前目录常量
define('_CACHE_CLASS_PATH', dirname(__FILE__) . '/');
//包含公用接口文件
require_once( _CACHE_CLASS_PATH . 'intface.cache.php');
require_once( _CACHE_CLASS_PATH . 'abstract.cache.php');

/**
 * 缓存类
 */
class cache {

	/**
	 * 获取实例并初始化对像
	 * 
	 * @param String $plugName 缓存插件名
	 * @return void
	 */
	public static function factory($plugName = null, $setting = '') {
		
		require_once('config/config.cache.php');

		//判断是否调用系统配置
		if (empty($plugName)) {

			$plugName = _CAHCE_SYSTEM;
            #$setting = _CACHE_PARAM;
		}
        $plugName = @strtolower($plugName);

        if ($plugName == 'memcache' || $plugName == 'mem') {
            
            $setting = strtoupper($setting);
            if (!empty($setting) && isset($GLOBALS['MEMCACHE_CONFIG'][$setting])) {

                $setting = $GLOBALS['MEMCACHE_CONFIG'][$setting];
            } else {

                $setting = $GLOBALS['MEMCACHE_CONFIG']['WWW'];    
            }
        }

		if ($plugName == 'tt') {
            
            $setting = strtoupper($setting);
            if (!empty($setting) && isset($GLOBALS['TT_CONFIG'][$setting])) {

                $setting = $GLOBALS['TT_CONFIG'][$setting];
            } else {

                $setting = $GLOBALS['TT_CONFIG']['WWW'];    
            }
        }

		
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
			
			$plugName = _DEFAULT_CACHE_ENGINE;
		}
		
		$plugFileName = self::getFileName($plugName);
		$plugClass = self::getClassName($plugName);
		
		if (file_exists($plugFileName)) {
			
			require_once($plugFileName);
			
			if (class_exists($plugClass)) {
				
				$obj = new $plugClass;
				return $obj;	
			} else {
				
				throw new Exception("ICache:: class $plugClass not found in PHP file $plugFileName .");
			}
		} else {
			
			throw new Exception("Cache:: not found cache plugin file $plugFileName .");
		}
	}
	
	/**
	 * 获取插件文件名
	 * 
	 * @param String $plugName 插件名
	 * @return String
	 */
	private static function getFileName($plugName) {
		
		return sprintf("%s/plug/class.%s.php", _CACHE_CLASS_PATH, $plugName);	
	} 
	
	/**
	 * 获取插件类名
	 * 
	 * @param String $plugName 插件名
	 * @return void
	 */
	private static function getClassName($plugName) {
		
		return sprintf("%s_cache", $plugName);
	}
}