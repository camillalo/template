<?php
//写模式
define('__MASTER', FALSE);
//读模式
define('__SLAVE', TRUE);
//定义配置文件
if (! defined('CONFIG_DB_INCLUDED')) {

	require_once("config/config.db.php");
}

class dbSource {

	//所有可用的数据库连接对像
	var $_connects = array ();

	//用于返回的空
	var $_ret = NULL;

	/**
	 * 创建对像
	 *
	 * @access  public
	 * @param	void
	 * @return  void
	 */
	function dbSource() {

	//do nothing
	}

	/**
	 * 创建新的数据库联接
	 *
	 * @access  private
	 * @param	$flag  数据库标识
	 * @return	mixed
	 */
	function _createConnect($flag) {

		$flag = strtoupper($flag);

		$_dsn = $this->getDSN($flag);

		if ($_dsn) {

			$this->_connects [$flag] = new dataBase();
			$this->_connects [$flag]->connect($_dsn);
			return $this->_connects [$flag];
		} else {

			return $this->_ret;
		}
	}

	/**
	 * 创建一个数据库链接代理
	 *
	 * @access  private
	 * @param	$flag  数据库标识
	 * @return	mixed
	 */
	function _createProxy($flag) {

		$flag = strtoupper($flag);

		if ($flag) {

			if (! class_exists('dbproxy')) {
				require_once (dirname(__FILE__) . "/class.dbProxy.php");
			}
			$this->_connects [$flag] = new dbProxy($flag);
			
			return $this->_connects [$flag];
		} else {

			return $this->_ret;
		}
	}

	/**
	 * 取得对应数据库的连接参数
	 *
	 * @access  public
	 * @param	$flag  数据库标记
	 * @return  mixed
	 */
	function getDSN($flag) {

		$flag = strtoupper($flag);

		if (isset($GLOBALS [$flag])) {

			return $GLOBALS [$flag];
		} else {

			return NULL;
		}
	}

	/**
	 * 取得指定数据库的联接
	 *
	 * @access  public
	 * @param	$flag  数据库标记
	 * @return  mixed
	 */
	function getConnect($flag, $isRead = null) {

		$flag = strtoupper($flag);
		//var_dump($flag);

		if ($isRead !== null) {
			if ($isRead) {

				$flag = sprintf("%s_%s", $flag, 'SLAVE');
			} else {

				$flag = sprintf("%s_%s", $flag, 'MASTER');
			}

			if (isset($this->_connects [$flag])) {

				return $this->_connects [$flag];
			} else {

				return $this->_createConnect($flag);
			}
		} else {
			
			
			if (isset($this->_connects [$flag])) {
				return $this->_connects [$flag];
			} else {
				return $this->_createProxy($flag);
			}
		}
	}

	/**
	 * 输出调试信息
	 */
	function toString() {

		foreach ( $this->_connects as $k => $v ) {
			$k = $k . sprintf("_%s_%s", $v->host, $v->user);
			$ret [$k] = $v->queries;
		}

		print_r($ret);
	}
}
if (!class_exists('dataBase')) {
	//如果不存在dataBase类,则自动包含同目录下的class.mysql.php文件	
	require_once(dirname(__FILE__) . '/class.mysql.php');
}