<?php
/**
 * HTTP 传递的参数类
 */

class request extends object {
	
	//Request 数据
	private $_request = array();
	
	//是否可以使用 AS 功能
	private $_curAs = false;
	
	//临时数据
	private $_curValue = '';
	
	/**
	 * 析构
	 *
	 */
	public function __construct() {
		
		parent::__construct();
		
		$this->_request = $_REQUEST;
		unset($_GET);
		unset($_POST);
		unset($_REQUEST);
	}
	
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $name
	 * @return unknown
	 */
	public function __get($name) {
		
		if ($this->_curAs) {
			
			die("请使用 $obj->valueName->toXxxx(); 的格式来获取参数！");
		}
		$this->_curAs = true;
		if (isset($this->_request[$name])) {
			
			$this->_curValue = $this->_request[$name];
			return $this;
		} else {
			
			$this->_curValue = null;
			return $this;
		}
	}
	
	/**
	 * 
	 */
	public function toString() {
		
		$this->chkRole();
		
		$ret =  $this->_curValue;
		if ($this->_curValue !== null) {
			
			$this->_curValue = null;
			$ret = strval($ret);	
		}
		return $ret;
	}
	
	/**
	 * 
	 */
	public function toInteger()  {
		
		$this->chkRole();
		$ret =  $this->_curValue;
		if ($this->_curValue !== null) {
			
			$this->_curValue = null;
			$ret = intval($ret);	
		}
		return $ret;	
	}
	
	/**
	 * 
	 */
	public function chkRole() {

		if (!$this->_curAs) {
			
			die("请使用 $obj->valueName->toXxxx(); 的格式来获取参数！");
		}
		
		$this->_curAs = false;
	}
}
?>