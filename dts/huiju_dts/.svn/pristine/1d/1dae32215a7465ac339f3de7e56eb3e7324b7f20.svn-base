<?php
/**
 * 数据库错误信息类
 * 
 * @version 	0.1 beta
 * @author 		hzjsq@staff.pchome.net
 * @package 	DataBase
 */

define('__DB_SQL_ERROE',	1);
define('__DB_SQL_INFO',		2);

class dbHistory {
	
	/**
	 * 记录信息的最多条数
	 * 
	 * @var integr
	 */
	var $_recordMax		= 256;
	
	/**
	 * 信息列表
	 * 
	 * @var array
	 */
	var	$_content		= array();
	
	
	/**
	 * 析构
	 * 
	 * @param 	void
	 * @return 	void
	 */
	function dbError() {
		
		// do nothing
	}
	
	/**
	 * 增加消息记录
	 * 
	 * @param 	string	$host	主机名
	 * @param 	string	$db		当前数据库
	 * @param 	string	$sql	查询SQL
	 * @param 	string	$info	说明信息
	 * @return 	void
	 */
	function add($host, $db, $sql, $info) {
		
		if (count($this->_content) < $this->_recordMax) {
			
			$this->_content[] = array('HOST' => $host, 'DB' => $db, 'SQL' => $sql, 'INFO' => $info);	
		}
	}
	
	/**
	 * 组合SQL信息
	 * 
	 * @param array	$info	查询信息
	 * @return 	string
	 */
	function _fetchStrFromInfo($info, $key) {
		
		$_ret = sprintf("<tr bgcolor=\"E2E2E2\"><td>%02d</td><td align=left>%s => %s</td><td align=left> %s</td> <td align=left> %s </td></tr>",$key+1, $info[HOST], $info[DB], $info[SQL], $info[INFO]);	
		return $_ret;
	}
	
	/**
	 * 显示所有信息
	 * 
	 * @param 	integer	$filter	过滤器
	 * @return 	void
	 */
	function display($filter = __DB_DISPLAY_DEFAULT)  {
		
		echo "<style> body{font-size:9pt;}	td{font-size:9pt;} </style>";
		echo "<table border=\"0\" cellpadding=\"4\" cellspacing=\"1\" width=\"100%\">";
		echo "<tr bgcolor=\"#D2D2D2\"><td width=5%>序号</td><td width=15%>主机 => 数据库名</td><td width=40%>SQL命令</td> <td width=40%>执行结果</td></tr>";
		foreach($this->_content as $key => $info) {

			echo $this->_fetchStrFromInfo($info, $key);
		}
		echo "</table>";
	}
	
	/**
	 * 析构
	 * 
	 * @param void
	 * @return void
	 */
	function __destruct() {
		
		if (defined('__DB_DEBUG')) {

			$this->display();
		}
	}
}
?>