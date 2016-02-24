<?php
/**
 * Created by PhpStorm.
 * User: chimero
 * Date: 14-6-7
 * Time: 18:12
 */
namespace Slim;
class Load
{
    const confPath = '../vender/conf/';
    const helperPath = '../vender/helper/';
    static $container = array();
    /**
     * @param $dbName
     * @param bool $readFromSlaveDb
     */
    public static function db($dbName, $readFromSlaveDb = false)
    {
        $dbIndex = '__' . $dbName . ($readFromSlaveDb === false ? '' : '__');
        if(!isset(self::set()->$dbIndex)) {
            self::set()->singleton($dbIndex, function ($c) use ($dbName, $readFromSlaveDb, $dbIndex) {
                $db = new \Slim\Database\Source();
                $db->dbIndex = $dbName;
                $db->readFromSlaveDb = $readFromSlaveDb;
                $db->init();
                return $db;
            });
        }
        return self::set()->$dbIndex;
    }

    /**
     * @param $libName
     * @param array $config
     * @param bool $singleton
     */
    public static function lib($libName, $config = array(), $singleton = true)
    {
        $params = explode('.', $libName);
        $className = array_pop($params);
        $namespace = '\\lib' . (count($params) == 1 ? '\\' . current($params) :implode('\\', $params)) . '\\' . $className;
        $classIndex = strtolower($className);
        // 不需要单例模式，直接返回实例
        if($singleton === false) {
            $instance =  new $namespace();
        } else {
            if(!self::set()->$classIndex) {
                self::set()->singleton($classIndex, function ($c) use ($params, $config, $namespace) {
                    return new $namespace();
                });
            }
            $instance = self::set()->$classIndex;
        }

        // 初始化配置
        if(!empty($config)) {
            foreach($config as $key => $val) {
                $instance->$key = $val;
            }
        }

        return $instance;
    }

    /**
     * @param $fileName
     * @param string $confKey
     */
    public static function conf($fileName, $confKey = '')
    {
        $file = self::confPath . $fileName . '.php';
        $confIndex = md5($file . '_conf');

        if(!file_exists($file)) {
            return false;
        }

        if(!self::set()->$confIndex) {
            self::set()->singleton($confIndex, function ($c) use ($file) {
                return require $file;
            });
        }

        $arr = self::set()->$confIndex;
        if($confKey && isset($arr[$confKey])) {
            return $arr[$confKey];
        }

        return $arr;
    }

    /**
     * @param $fileName
     * @return bool
     */
    public static function helper($fileName)
    {
        $file = self::helperPath . $fileName . '.php';
        $helperIndex = md5($file . '_helper');
        if(!file_exists($file)) {
            return false;
        }
        if(!self::set()->$helperIndex) {
            self::set()->singleton($helperIndex, function ($c) {
                return true;
            });
            require $file;
        }
    }

    /**
     * @param $key
     * @param $value
     */
    private static function set()
    {
        return !empty(self::$container) ? self::$container : self::$container = new \Slim\Helper\Set();
    }

}
