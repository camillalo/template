<?php
/**
 * 缓存数据结果类
 * 
 * @copyright 	hzjsq@staff.pchome.net
 * @version 	0.1 beta
 * @package 	DataBase
 */

class cacheResult {
	
	/**
	 * 缓存的内容
	 *
	 * @var array
	 */
	var $_content	= null;
	
	/**
	 * 数据指针
	 * 
	 * @var integer
	 */
	var	$_pointer	= 0;
	
	/**
	 * 记录总数
	 * 
	 * @var integer
	 */
	var $_recordNum	= 0;
	
	/**
	 * 析构
	 * 
	 * @param	mixed	$data	缓存过的数据
	 * @return 	void
	 */
	function cacheResult($data = null) {
		
		if (null !== $data) {
			
			$this->setResult($data);
			
			if (is_array($data)) {
				
				$this->_recordNum = count($data);
			} else {
				
				$this->_recordNum = 0;	
			}
		}
	}

	/**
	 * 设置数据
	 * 
	 * @param 	mixed	$data	缓存的数据
	 * @return 	void
	 */
	function setResult(& $data) {
		
		$this->_content = & $data;
		$this->_pointer	= 0;
	}
	
	/**
	 * 获取数据
	 * 
	 * @param  void
	 * @return mixed
	 */
	function & fetchArray(){

		if (isset($this->_content[$this->_pointer])) {

            $this->_pointer ++;
			
			return $this->_content[$this->_pointer-1];
		}
	}
	
	/**
	 * 获取数据
	 * 
	 * @param  void
	 * @return mixed
	 */
	function & fetchRow(){
		
		if (isset($this->_content[$this->_pointer])) {
			
			$this->_pointer ++;
			
			if (is_array($this->_content[$this->_pointer-1])) {
				
				$_ret = array();
				foreach ($this->_content[$this->_pointer-1] as $val) {
					
					$_ret[] = $val;
				}
				
				return $_ret;
			} else {
				
				return null;
			}
		}
	}
	
	/**
	 * 移动指定的数据条目
	 *
	 * @param integer $pos	指定的数据位置
	 * @return void
	 */
	function dataSeek($pos) {
		
		$pos = intval($pos);
		
		$pos = ($pos < 0) ? 0 : $pos;
		$pos = ($pos > $this->_recordNum) ? $this->_recordNum : $pos;
		
		$this->_pointer = intval($pos);
	}
	
	/**
	 * 取得记录条数
	 * 
	 * @param void
	 * @return void
	 */
	function numRows() {
		
		return $this->_recordNum;
	}

    /**
     * 
     */
    function & result($pos) {

        return $this->_content[$pos];
    }
}
?>