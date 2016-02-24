<?php
if ( !defined ( 'BASE_PATH') )
{
	exit ( 'Access Denied' );
}

class fileCache{

    /**
     * 缓存保存目录
     * @var string
     */
    private $_cache_dir;

    /**
     * 缓存文件切割目录长度
     * @var integer
     */
    private $_sub_dir_len = 2;

    /**
     * 缓存文件切割多少级目录
     * @var integer
     */
    private $_hash_level  = 4;
    
     /**
     * 缓存默认保留时间
     * @var integer
     */
    protected $_life_time   = 7200;

    /**
     * 当前的时间戳
     * @var integer
     */
    protected $_current_time    = 0;
    
   
    private static  $instance = null;
    public static function getInstance()
    {   
        if (null == self::$instance){
            self::$instance = new fileCache();
        }
        
        return self::$instance;
    } 
    private function  __construct() {
        global  $_FILE_CACHE_CFG;
        isset ($_FILE_CACHE_CFG['cache_dir']) &&    $this->_cache_dir   = $_FILE_CACHE_CFG['cache_dir'];
        isset ($_FILE_CACHE_CFG['life_time'])   &&  $this->_life_time   = (int) $_FILE_CACHE_CFG['life_time'];
        isset ($_FILE_CACHE_CFG['sub_dir_len']) &&  $this->_sub_dir_len = (int) $_FILE_CACHE_CFG['sub_dir_len'];
        isset ($_FILE_CACHE_CFG['hash_level'])  &&  $this->_hash_level  = (int) $_FILE_CACHE_CFG['hash_level'];
        $this->_current_time    =  NOWTIME;
    }




    /**
     * 从缓存中提取一条或者多条缓存内容
     *
     * @param string|array $token
     * @return mixed
     */
    public function load($token)
    {
        if (is_array($token))
        {
            $ret    = array();
            
            foreach ($token as $key)
            {
                $ret[$key]  = $this->_load($key);
            }

            return $ret;
        }
        return $this->_load($token);
    }

    /**
     * 从缓存中提取一条缓存内容
     *
     * @param string $token
     * @return mixed
     */
    private function _load($token)
    {
        $file   = $this->_token_to_file($token);
        if (file_exists($file) && is_readable($file))
        {
            $fp = fopen($file, 'rb');
            flock($fp, LOCK_SH);
            if ($this->_isExpired($fp))
            {
                flock($fp, LOCK_UN);
                fclose($fp);
                return null;
            }
            $cache_data = fread($fp, $this->_getCacheDataLen($fp));
            flock($fp, LOCK_UN);
            fclose($fp);
            return eval($cache_data);
        }
        return null;
    }

    /**
     * 判断一个缓存是否依旧有效
     *
     * @param string $token
     * @return boolean
     */
    public function test($token)
    {
        $file   = $this->_token_to_file($token);
        if (file_exists($file) && is_readable($file))
        {
            $fp = fopen($file, 'ab');
            return $this->_isExpired($fp);
        }
        return false;
    }

    /**
     * 将内容保存到缓存中
     *
     * @param string $token
     * @param mixed $value
     * @param integer $life_time
     * @return boolean
     */
    public function put($token, $value, $life_time = -1)
    {
        //return true;    
        if($life_time < 0)
        {
            $expire_time  = $this->_life_time + $this->_current_time;
        } elseif ($life_time == 0) {
            $expire_time  = $this->_current_time + 86400 * 20;
        } else {
            $expire_time  = $this->_current_time + $life_time;
        }
        $file       = $this->_token_to_file($token);
        $dir_name   = substr($file, 0, strrpos($file, '/'));
        if (!is_dir($dir_name))
        {
            if (!mkdir($dir_name, 0700, true))  return false;
        }
        $cache_str  = $this->_format_cache_data($value);
        $this->_writeCache($file, $expire_time, $cache_str);
        return true;
    }

    /**
     * 移除一个缓存内容
     *
     * @param string $token
     * @return boolean
     */
    public function remove($token)
    {
        $file   = $this->_token_to_file($token);
        if (file_exists($file))
        {
            return unlink($file);
        }
        return false;
    }

    /**
     * 删除所有的缓存内容
     *
     * @return int 
     */
    public function flush()
    {
        $itr    = new RecursiveDirectoryIterator($this->_cache_dir);
        foreach(new RecursiveIteratorIterator($itr) as $cur)
        {
            if ($cur->isFile() && $cur->isWritable())
            {
                unlink($cur->getPathName());
            }
        }
        return true;
    }

    /**
     * 将格式化好的缓存字符串写入到文件中
     *
     * @param string $filePath
     * @param integer $expire_time
     * @param string $cache_str
     * @return integer
     */
    protected function _writeCache($filePath, $expire_time, $cache_str)
    {
        $fp     = fopen($filePath, 'wb+');
        flock($fp, LOCK_EX);
        fwrite($fp, pack('I', $expire_time));
        fwrite($fp, pack('I', strlen($cache_str)));
        $bytes  = fwrite($fp, $cache_str);
        flock($fp, LOCK_UN);
        fclose($fp);
        return $bytes;
    }

    /**
     * 判断一个文件句柄所代表的内容是否已经过期
     *
     * @param resource $fp
     * @return boolean
     */
    protected function _isExpired($fp)
    {
        $time_bite  = fread($fp, 4);
        $time_us    = unpack('I', $time_bite);
        $expires_time_t = $time_us[1];
        return $expires_time_t < $this->_current_time;
    }

    /**
     * 从一个文件句柄中读取当前缓存文件中内容的长度
     *
     * @param resource $fp
     * @return integer
     */
    protected function _getCacheDataLen($fp)
    {
        $len_bite       = fread($fp, 4);
        $data_length    = unpack('I', $len_bite);
        return $data_length[1];
    }

    /**
     * 将一个token映射为文件系统的文件名
     *
     * @param string $token
     * @return string
     */
    protected function _token_to_file($token)
    {
        $token      = md5($token);
        $fileName   = '';
        $pos        = 0;
        for ($i = 0; $i < $this->_hash_level; ++ $i)
        {
            $dir    = substr($token, $pos, $this->_sub_dir_len);
            $fileName   .=  $dir . '/';
            $pos    += $this->_sub_dir_len;
        }
        $fileName   .= substr($token, $pos);
        return $this->_cache_dir . '/' . $fileName . '.cac';
    }

    /**
     * 将一个mixed类型的值序列化为字符串
     *
     * @param mixed $data
     * @return string
     */
    protected function _format_cache_data($data)
    {
        $data   = var_export($data, true);
        return  "return {$data};";
    }
}