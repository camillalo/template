<?
/*
 *数据库操作一些基类
 */
class muiltDB {

	//指定开始页面数
	var $_page			= NULL;

	//每页主题数
	var $_len			= NULL;

	//最大页面数
	var $_realMaxPage	= NULL;

	//总共记录数
	var $_recNum		= NULL;

	/**
	 * 创建指定对象
	 * 
	 * @access  public
	 * @param   void
	 * @return  void
	 */
	function muiltDB(){

		//do nothing
	}

	/**
	 * 设定开始的页码
	 * 
	 * @access  public
	 * @param	$page  开始页数
	 * @return	void
	 */
	function setPage($page =1) {

		$page = intval($page);

		if ($page <= 1) {

			$this->_page = 1;
		} else {

			$this->_page = $page;
		}
	}

	/**
	 * 设定每页的主题数
	 *
	 * @access  public
	 * @param	$len   每页主题数
	 * @return  void
	 */
	function setLength($len = 20) {

		$this->_len = intval($len);

		if ($this->_len <= 0) {

			$this->_len = 20;
		}
	}

	/**
	 * 指定当前记录数, 并同时调整开始的页数
	 *
	 * @access  public
	 * @param	$recNum 总共记录数
	 * @return	void
	 */
	function setRecNum($recNum = 0) {

		$this->_recNum = intval($recNum);

		if (($this->_recNum % $this->_len) === 0) {

			$this->_realMaxPage = intval($this->_recNum/$this->_len);
		} else {

			$this->_realMaxPage = intval($this->_recNum/$this->_len) + 1;
		}

		if ($this->_page > $this->_realMaxPage) {

			$this->_page = $this->_realMaxPage;
		}
	}

	/**
	 * 取得用于SQL查询的LIMIT子句
	 *
	 * @access  public
	 * @param	void
	 * @return  string
	 */
	function getLimitStr() {

		if ($this->_page === NULL || $this->_len === NULL) {

			return NULL;
		} else {
			if ($this->_page===0) {
				$this->_page=1;
			}
			return sprintf(' LIMIT %d,%d', ($this->_page-1)*$this->_len, $this->_len);
		}
	}

	/**
	 * 取得用于分页的数据
	 *
	 * @access  public
	 * @param	$style  导航条样式
	 * @return	string
	 */
	function & getMuiltPageNav() {

		$_ret['MaxPage']	= $this->_realMaxPage;
		$_ret['CurPage']	= $this->_page;
		$_ret['PageNum']	= $this->_len;
		$_ret['TotalRec']	= $this->_recNum;
		$_ret['PrePage']	= $this->_page - 1;
		$_ret['NextPage']	= $this->_page + 1;
		return $_ret;
	}
}


/**
 * 多页面显示
 */
class dbcomm {

	//用于多页显示的数据类
	var $_muiltClass	= NULL;

	//用于检索数据的SQL
	var $sqlString		= NULL;

	//用于查询总数的SQL
	var $countString	= NULL;

	//转换函数名
	var $_convFun		= NULL;

	//数据库标记
	var $DBFlag			= NULL;

	/**
	 * 析构函数
	 *
	 * @access  public
	 * @param	void
	 * @return	void
	 */
	function dbcomm() {

		//donothing
	}

	/** 
	 * 设置查询总数的SQL
	 *
	 * @access  public
	 * @param	void
	 * @return	void
	 */ 
	function setCount($str) {

		 $this->countString = $str;
	}
	
	/** 
	 * 设置数据库FLAG
	 *
	 * @access  public
	 * @param	void
	 * @return	void
	 */ 
	function setDBFlag($str) {

		 $this->DBFlag = $str;
	}

	/** 
	 * 设置查询的SQL
	 *
	 * @access  public
	 * @param	void
	 * @return	void
	 */ 
	function setSQL($str) {
          
		 $this->sqlString = $str;
	}

	/** 
	 * 设置数据转换的过程
	 *
	 * @access  public
	 * @param	void
	 * @return	void
	 */ 
	function setFun($str) {
		 $this->_convFun = $str;
	}

	/**
	 * 检查用于多页显示的类
	 *
	 * @access  private
	 * @param	void
	 * @return	void
	 */
	function _chkMutilClass() {

		if ($this->_muiltClass === NULL) {

			$this->_muiltClass = new muiltDB();
		}
	}

	/**
	 * 获取错误记录信息
	 * 
	 * @access  public
	 * @param	$page
	 * @param	$len
	 * @return	void
	 */
	function & getList($page =0, $len =25) {

		//$db = & $GLOBALS['_DSN']->getConnect($this->DBFlag,__SLAVE);
		$db = core::db()->getConnect($this->DBFlag);
		$this->_chkMutilClass();

		$this->_muiltClass->setPage($page);
		$this->_muiltClass->setLength($len);
		//echo	$this->countString;
		$recCnt = $db->query($this->countString,1);
		$this->_muiltClass->setRecNum($recCnt);
        $_result = $db->query("{$this->sqlString} " .$this->_muiltClass->getLimitStr());

		

		$_ret = NULL;

		$convFun = $this->_convFun;
		while ($_row = $db->fetchArray($_result)) {
			
			if ($convFun){
				$convFun($_row);
				$_ret[] = $_row;
		
            }else{
                $_ret[] = $_row;
		}
		}

		return $_ret;
	}

	/**
	 * 取得导航条数据
	 * 
	 * @access  public
	 * @param   void
	 * @return  array
	 */
	function &getNavigation(){
		if (!$this->_muiltClass) {
			return NULL;
		} else {	
			return $this->_muiltClass->getMuiltPageNav();
		}
	}
	
	function & getNavData() {

		if ($this->_muiltClass === NULL) {

			return NULL;
		} else {

			return $this->_muiltClass->getMuiltPageNav();
		}
	}
	

    /**
     * 得到单行记录
     * 
     */
    function & getRecord($type = 'array') {

        //$db = & $GLOBALS['_DSN']->getConnect($this->DBFlag,__SLAVE);
		$db = core::db()->getConnect($this->DBFlag);
        $_row = $db->query("{$this->sqlString} ", $type);


        $convFun = $this->_convFun;
        if ($convFun){
			$convFun($_row);
			
			return ($_row);
        }else{
            return $_row;
	}

    }

    /**
     * 得到全部记录
     * 
     */
    function & getData() {

        //$db = & $GLOBALS['_DSN']->getConnect($this->DBFlag,__SLAVE);
		 $db = core::db()->getConnect($this->DBFlag);
		$_result = $db->query("{$this->sqlString} ");
			
		$_ret = NULL;

		$convFun = $this->_convFun;
		while ($_row = $db->fetchArray($_result)) {

			if ($convFun){
				$convFun($_row);
				$_ret[] = $_row;
            }else{
                $_ret[] = $_row;
			}
		}

		return $_ret;
    }

    /**
     * 执行插入操作
     */
    function insert($_arr, $tblName) {

        if (!is_array($_arr)) {

            return;
        }

        //$db = & $GLOBALS['_DSN']->getConnect($this->DBFlag,__MASTER);
		
		$db = core::db()->getConnect($this->DBFlag);
        foreach($_arr as $_key => $_val) {

            $_set[] = "`{$_key}` = '" .addslashes($_val). "'";
        }
        $sql="INSERT INTO {$tblName} SET ". join(',', $_set);
        $db->query($sql);
        //var_dump($sql);

        return ($db->insertId());
    }

    function replace($_arr, $tblName) {
    
    	if (!is_array($_arr)) {
    
    		return;
    	}
    
    	//$db = & $GLOBALS['_DSN']->getConnect($this->DBFlag,__MASTER);$db = core::db()->getConnect($this->DBFlag);
    	$dbs = core::Singleton('comm.db.dbSource');
    	$db = $dbs->getConnect($this->DBFlag);
    	foreach($_arr as $_key => $_val) {
    
    		$_set[] = "`{$_key}` = '" .addslashes($_val). "'";
    	}
    
    	$db->query("REPLACE INTO {$tblName} SET ". join(',', $_set));
    	//echo "REPLACE INTO {$tblName} SET ". join(',', $_set);
    	
    	return ($db->insertId());
    }    
    
    /**
     * 执行修改操作
     */
    function update($_arr, $tblName, $where) {

        if (!is_array($_arr)) {

            return;
        }

        //$db = & $GLOBALS['_DSN']->getConnect($this->DBFlag,__MASTER);
		$db = core::db()->getConnect($this->DBFlag);
        foreach($_arr as $_key => $_val) {

            $_set[] = "{$_key} = '" .addslashes($_val). "'"; 
        }

        $db->query("UPDATE {$tblName} SET ". join(',', $_set) ." $where");
        //echo "UPDATE {$tblName} SET ". join(',', $_set) ." $where";
		return $db->affectedRows();
    }

	/**
	 *执行删除操作
	 */
	function delete ($tblName, $where){

		//$db = & $GLOBALS['_DSN']->getConnect($this->DBFlag,__MASTER);
		$db = core::db()->getConnect($this->DBFlag);
		$db->query("DELETE from {$tblName} $where");
	}

	/**
	 *执行其他SQL操作
         */
	function execSQL($sql,$flag){
		//$db = & $GLOBALS['_DSN']->getConnect($this->DBFlag,$flag);
		$db = core::db()->getConnect($this->DBFlag);
		$db->query($sql);
	}


}

/*if (!isset($GLOBALS[_DBCOMM])) {
	
	$GLOBALS[_DBCOMM] = new multiRecordBase();
}*/
?>