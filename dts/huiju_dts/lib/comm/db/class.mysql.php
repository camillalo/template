<?php
/**
 * 数据库基础类
 *
 * @version 	0.1 beta
 * @copyright  	hzjsq@staff.pchome.net
 * @package 	DataBase
 */

class dataBase {

	/**
	 * 数据库联接字
	 *
	 * @var res
	 */
	var $_linkId = null;

	/**
	 * 数据库主机
	 *
	 * @var	string
	 */
	var $_hostName = null;

	/**
	 * 数据库名称
	 *
	 * @var	integer
	 */
	var $_dataBase = null;

	/**
	 * 数据库用户名
	 *
	 * @var	string
	 */
	var $_userName = null;

	/**
	 * 数据库密码
	 *
	 * @var	string
	 */
	var $_passWord = null;

	/**
	 * 是否缓存
	 *
	 * @var boolean
	 */
	var $_isCached = false;
	
	/**
	 * 缓存时间
	 *
	 * @var integer
	 */
	var $_cacheTime = 0;

	/**
	 * 所有数据操作记录
	 *
	 * @var array
	 */
	var $_queryCache = array ();

	/**
	 * 缓存对象
	 *
	 * @var	object
	 */
	var $_cacheObj = null;

	/**
	 * 缓存所有查询命令
	 *
	 * @var  array
	 */
	var $_sqlArray = array ();
	/**
	 * 析构
	 *
	 * @return void
	 */
	function dataBase() {

	// do nothing
	}

	/**
	 * 设置数据库信息
	 *
	 * @param 	array $dsn
	 * @return 	void
	 */
	function setDsn($dsn) {
		$this->_hostName = $dsn ['HOST'];
		$this->_dataBase = $dsn ['NAME'];
		$this->_userName = $dsn ['USER'];
		$this->_passWord = $dsn ['PASS'];
	}

	/**
	 * 连接到指定数据库
	 *
	 * @param 	array $dsn
	 * @return	boolean
	 */
	function connect(&$dsn) {

		$this->setDsn($dsn);
		$this->disableCache();
	}

	/**
	 * 取得SQL的唯一ID
	 *
	 * @param 	string	$sql 	SQL语句
	 * @return 	string
	 */
	function _getSSID($sql) {

		$_flag = sprintf("%s_%s_%s_%s_%s", $this->_hostName, $this->_userName, $this->_passWord, $this->_dataBase, $sql);

		return md5($_flag);
	}

	/**
	 * 选择当前操作数据库
	 *
	 * @param 	string $db
	 * @return 	false;
	 */
	function selectDB($db = null) {

		$db = empty($db) ? $this->_dataBase : $db;
		if ($db) {

			if (is_resource($this->_linkId)) {

				$ret = @mysql_select_db($db, $this->_linkId);

				if (! $ret) {

					$this->_throwErr("USE {$this->_dataBase};");
				} else {

					$this->_addDbHistory("USE {$this->_dataBase};");
				}
			}

			$this->_dataBase = $db;
		}
	}

	/**
	 * 设置缓存参数
	 *
	 * @param integer	$time	缓存时间
	 * @return void
	 */
	function enableCache($time = 0) {

		$time = intval($time);
		$time = ($time < 0) ? 0 : $time;

		if ($time > 0) {

			$this->_isCached = true;
			$this->_cacheTime = $time;
		} else {

			$this->_isCached = false;
			$this->_cacheTime = 0;
		}
	}

	/**
	 * 禁用缓存
	 *
	 * @param 	void
	 * @return 	void
	 */
	function disableCache() {

		$this->enableCache(0);
	}

	/**
	 * 取得数据库链接
	 *
	 * @param	void
	 * @return 	res
	 */
	function _getLinkId() {

		if (! is_resource($this->_linkId)) {

			$this->_linkId = @mysql_connect($this->_hostName, $this->_userName, $this->_passWord, true);

			if (! $this->_linkId) {

				$this->_throwErr("mysql_connect({$this->_hostName}, {$this->_userName}, {$this->_passWord});");
			} else {

				if (defined('__DB_DEBUG')) {

					$this->_addDbHistory("mysql_connect({$this->_hostName}, {$this->_userName}, {$this->_passWord});");
				}
			}

			if ($this->_version() > '4.1') {

				@mysql_query("SET character_set_connection=utf8, character_set_results=utf8, character_set_client=binary", $this->_linkId);

				if ($this->_version() > '5.0.1') {
					@mysql_query("SET sql_mode=''", $this->_linkId);
				}
			}

			$ret = @mysql_select_db($this->_dataBase, $this->_linkId);
			if (! $ret) {

				$this->_throwErr("USE {$this->_dataBase};");
			} else {

				$this->_addDbHistory("USE {$this->_dataBase};");
			}
		}

		return $this->_linkId;
	}

	/**
	 * 获取版本号
	 */
	function _version() {

		return @mysql_get_server_info($this->_linkId);
	}

	/**
	 * 创建缓存对像
	 *
	 * @param void
	 * @return void
	 */
	function _createCacheObject() {

		if (! is_object($this->_cacheObj)) {

			if (isset($GLOBALS [__MEM_CACHE_OBJ])) {

				$this->_cacheObj = & $GLOBALS [__MEM_CACHE_OBJ];
			} else {

				//重新生成一个缓存对像
				if (! class_exists('memCacheClient')) {

					require_once ("pchome/config/config.php");
					require_once ("pchome/cache/memCache.php");
				}

				$GLOBALS [__MEM_CACHE_OBJ] = new memCacheClient($GLOBALS ["_MEM_SERVER"]);
				$this->_cacheObj = & $GLOBALS [__MEM_CACHE_OBJ];
			}
		}
	}

	/**
	 * 执行真实的SQL查询
	 *
	 * @param string $sql
	 * @return mxed
	 */
	function &_realQuery($sql) {

		$linkId = $this->_getLinkId();

		$result = @mysql_query($sql, $linkId);
		if (! $result) {

			$this->_throwErr($sql);
		} else {

			if (defined('__DB_DEBUG')) {

				$this->_sqlArray [] = $sql;
				$this->_addDbHistory($sql);
			}
		}

		return $result;
	}

	/**
	 * 取得缓存数据
	 *
	 * @param 	string	$ssid	缓存Key
	 * @return 	mixed
	 */
	function _getCache($ssid) {

		if (! is_object($this->_cacheObj)) {

			$this->_createCacheObject();
		}

		return $this->_cacheObj->get($ssid);
	}

	/**
	 * 保存数据到缓存
	 *
	 * @param   resource $res 数据库查询结果
	 * @return 	void
	 */
	function _saveToCache($ssid, & $res) {

		$_contents = array ();

		while ( $_row = @mysql_fetch_array($res, MYSQL_ASSOC) ) {

			$_contents [] = $_row;
		}

		$this->_cacheObj->set($ssid, $_contents, $this->_cacheTime);

		@mysql_data_seek($res, 0);
	}

	/**
	 * 检查是不是数据库缓存对像
	 *
	 * @param 	object	$obj	数据库缓存对像
	 * @return 	boolean
	 */
	function isCacheResult(& $obj) {

		if (is_object($obj)) {

			return (strtolower(get_class($obj)) == 'cacheresult');
		} else {

			return false;
		}
	}

	/**
	 * 执行SQL命令
	 *
	 * @param 	string	$sql	SQL命令
	 * @param 	string	$queryType	返回数据类型
	 * @return 	mixed
	 */
	function &query($sql, $queryType = null, $cacheTime = null) {
		if(defined('SQL_DEBUG')){echo $sql;}//debug
		
		$sql = trim($sql);

		$_needCache = ($this->_isCached && preg_match('/^select/is', $sql));
		//$_needCache = 1;
		if ($_needCache) {
		
			//执行有缓存的数据库查询
			$ssid = $this->_getSSID($sql);

			$res = $this->_getCache($ssid);

			if (! $res) {

				$result = $this->_realQuery($sql);
				$this->_saveToCache($ssid, $result);
			} else {

				if (defined('__DB_DEBUG')) {

					$this->_addDbHistory($sql, '已经缓存');
				}

				if (! class_exists('cacheresult')) {

					require_once (dirname(__FILE__) . "/class.cacheResult.php");
				}
				$result = new cacheResult($res);
			}
		} else {
			
			//实时查询
			$result = $this->_realQuery($sql);
		}

		if (empty($queryType)) {

			return $result;
		} else {

			return $this->_getValue($result, $queryType);
		}
	}

	/**
	 * 按查询类型返回对应的值
	 *
	 * @param 	mixed	$result	查询结果
	 * @param 	string	$queryType	返回类型
	 * @return 	mixed
	 */
	function &_getValue($result, $queryType) {

		$queryType = strtolower($queryType);

		switch ($queryType) {

			case '1' :

				$row = $this->fetchRow($result);
				$_ret = $row [0];
				break;
			case 'row' :

				$_ret = $this->fetchRow($result);
				break;
			default :

				$_ret = $this->fetchArray($result);
				break;
		}

		return $_ret;
	}

	/**
	 * 从结果集中返回一条数据,数字下标
	 *
	 * @param mixed	$res
	 * @return array
	 */
	function &fetchArray(& $res) {

		if (is_resource($res)) {
			
			return @mysql_fetch_array($res, MYSQL_ASSOC);
		} elseif ($this->isCacheResult($res)) {

			return $res->fetchArray();
		} else {

			if (defined(__DB_DEBUG)) {

				$this->_addDbHistory('无法识别的数据库结果集 $res => ' . gettype($res));
			}

			return NULL;
		}
	}

	/**
	 * 重结果集中返回一条数据,字段名为下标
	 *
	 * @param mixed	$res
	 * @return array
	 */
	function fetchRow(&$res) {

		if (is_resource($res)) {

			return @mysql_fetch_row($res);
		} elseif ($this->isCacheResult($res)) {

			return $res->fetchRow();
		} else {

			if (defined(__DB_DEBUG)) {

				$this->_addDbHistory('无法识别的数据库结果集 $res => ' . gettype($res));
			}

			return NULL;
		}
	}

	/**
	 * 取得返回数据集中的数据条数
	 *
	 * @param mixed	$res
	 * @return integer
	 */
	function numRows(& $res) {

		if (is_resource($res)) {

			return @mysql_num_rows($res);
		} elseif ($this->isCacheResult($res)) {

			return $res->numRows();
		} else {

			if (defined(__DB_DEBUG)) {

				$this->_addDbHistory('无法识别的数据库结果集 $res => ' . gettype($res));
			}

			return NULL;
		}
	}

	/**
	 *
	 */
	function result($res, $pos) {

		if (is_resource($res)) {

			return @mysql_result($res, $pos);
		} elseif ($this->isCacheResult($res)) {

			return $res->result($pos);
		} else {

			if (defined(__DB_DEBUG)) {

				$this->_addDbHistory('无法识别的数据库结果集 $res => ' . gettype($res));
			}

			return NULL;
		}
	}

	/**
	 * 移动当前数据指针
	 *
	 * @param 	mixed	$res 	数据结果集
	 * @param 	integer $pos	指针位置
	 * @return 	mixed
	 */
	function dataSeek($res, $pos = 0) {

		if (is_resource($res)) {

			return @mysql_data_seek($res, $pos);
		} elseif ($this->isCacheResult($res)) {

			return $res->dataSeek($pos);
		} else {

			if (defined(__DB_DEBUG)) {

				$this->_addDbHistory('无法识别的数据库结果集 $res => ' . gettype($res));
			}

			return NULL;
		}
	}

	/**
	 * 取得SQL后数据库受影响的记录条数
	 *
	 * @param 	void
	 * @return	mixed
	 */
	function affectedRows() {

		if (is_resource($this->_linkId)) {

			return @mysql_affected_rows($this->_linkId);
		} else {

			return null;
		}
	}

	/**
	 * 取得执行插入命令后的自增加字段的值
	 *
	 * @param 	void
	 * @return 	mixed
	 */
	function insertId() {

		if (is_resource($this->_linkId)) {

			return @mysql_insert_id($this->_linkId);
		}
	}

	/**
	 * 关闭当前数据库连接
	 *
	 * @param 	void
	 * @return 	void
	 */
	function close() {

		if (is_resource($this->_linkId)) {

			@mysql_close($this->_linkId);
		}
	}

	/**
	 * 删除结果局
	 *
	 * @param resource	$res
	 * @return void
	 */
	function freeResult($res) {

		if (is_resource($res)) {

			return @mysql_free_result($res);
		}
	}

	/**
	 * 取得数据库查询信息类
	 *
	 * @param 	void
	 * @return 	object
	 */
	function &_getHistoryObj() {

		if (! isset($GLOBALS [__DB_HISTORY_OBJ])) {

			require_once (dirname(__FILE__) . '/class.dbHistory.php');
			$GLOBALS [__DB_HISTORY_OBJ] = new dbHistory();
		}

		return $GLOBALS [__DB_HISTORY_OBJ];
	}

	/**
	 * 错误信息
	 *
	 * @param 	string	$sql
	 * @return 	void
	 */
	function _throwErr($sql = '') {

		$_obj = & $this->_getHistoryObj();

		if ($this->_linkId) {

			$_errInfo = @mysql_error($this->_linkId);
		}

		$_errInfo = $_errInfo ? $_errInfo : '执行错误';

		$_obj->add($this->_hostName, $this->_dataBase, $sql, $_errInfo, __DB_SQL_ERROE);
	}

	/**
	 * 增加调试信息
	 *
	 * @param 	string $sql
	 * @return	void
	 */
	function _addDbHistory($sql, $info = null) {

		$_obj = & $this->_getHistoryObj();
		$info = $info ? $info : '执行正确';

		$_obj->add("(<b><font color=red>{$this->_FLAG}</font></b>)" . $this->_hostName, $this->_dataBase, $sql, $info, __DB_SQL_INFO);
	}
}