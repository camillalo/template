<?php
//定义当前目录常量
define('_API_CLASS_PATH', dirname(__FILE__) . '/');
require_once(_API_CLASS_PATH . 'class.api_comm.php');

class api_class {
	
	/**
	 * 通过插件名获取对像
	 * 
	 * @param String $plugName 插件名
	 * @return object
	 */
	public static function getInstance($plugName) {
		return self::instance($plugName);
	}

	/**
	 * 通过插件名获取对像
	 * 
	 * @param String $plugName 插件名
	 * @return void
	 */
	private static function instance($plugName) {
		
		$plugFileName = self::getFileName($plugName);
		$plugClass = self::getClassName($plugName);
		//echo "\n".$plugFileName."\n";
		if (file_exists($plugFileName)) {
			
			require_once($plugFileName);
			
			if (class_exists($plugClass)) {
				
				$obj = new $plugClass;
				return $obj;	
			} else {
				return false;
				//throw new Exception(":: class $plugClass not found in PHP file $plugFileName .");
			}
		} else {
			return false;
			//throw new Exception(":: not found  plugin file $plugFileName .");
		}
	}
	
	/**
	 * 获取插件文件名
	 * 
	 * @param String $plugName 插件名
	 * @return String
	 */
	private static function getFileName($plugName) {
		
		return  sprintf("%s/plug/class.%s.php", _API_CLASS_PATH, $plugName);	
	} 
	
	/**
	 * 获取插件类名
	 * 
	 * @param String $plugName 插件名
	 * @return void
	 */
	private static function getClassName($plugName) {
		
		return sprintf("%s", $plugName);
	}
	
}