<?php
if ( !defined ( 'BASE_PATH') )
{
	exit ( 'Access Denied' );
}

class Db_Expr
{
	private $_expr;
	public function __construct($expr)
	{
		$this->_expr	= $expr;
	}
	public function __toString()
	{
		return $this->_expr;
	}
}

class mysql {

    /**
     * 数据库连接句柄
     *
     * @var resource
     */
    private $_link;
    /**
     * 查询完后的结果句柄
     *
     * @var resource
     */
    private $_result;
    /**
     * 是否开启调试模式
     *
     * @var boolean
     */
    private $_isDebug = false;
    /**
     * 配置项
     *
     * @var array
     */
    private $_cfg = array();
    /**
     * 服务器配置是否已经开启魔法引用
     *
     * @var boolean
     */
    private $_isMagicQuotesOn;
    /**
     * 是否记录执行过的SQL
     *
     * @var boolean
     */
    private $_logSql = true;
    /**
     * 执行过的SQL
     *
     * @var array
     */
    private $_executedSqls = array();
    /**
     * 语句是否在事务中
     *
     * @var boolean
     */
    private $_isInTransaction = false;
    /**
     * 记录的最多执行的SQL语句条数
     *
     * @var int
     */
    private $_maxLogedSql = 1000;
    /**
     * 已经记录的SQL语句条数
     *
     * @var int
     */
    private $_logedSqlCount = 0;

    
    private static  $instance = null;
    
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new mysql();
        }
        
        return self::$instance;
    } 
    /**
     * Enter description here...
     *
     * @param array $__MYSQL_CFG
     */
    private  function __construct() {
        global $__MYSQL_CFG;
        $this->_isMagicQuotesOn = get_magic_quotes_gpc();
        $this->_cfg['pconnect'] = false;
        isset($__MYSQL_CFG['host']) && $this->_cfg['host'] = $__MYSQL_CFG['host'];
        isset($__MYSQL_CFG['username']) && $this->_cfg['username'] = $__MYSQL_CFG['username'];
        isset($__MYSQL_CFG['password']) && $this->_cfg['password'] = $__MYSQL_CFG['password'];
        isset($__MYSQL_CFG['charset']) && $this->_cfg['charset'] = $__MYSQL_CFG['charset'];
        isset($__MYSQL_CFG['pconnect']) && $this->_cfg['pconnect'] = $__MYSQL_CFG['pconnect'];
        isset($__MYSQL_CFG['dbname']) && $this->_cfg['dbname'] = $__MYSQL_CFG['dbname'];
        isset($__MYSQL_CFG['debug']) && $this->_isDebug = (boolean) $__MYSQL_CFG['debug'];
        isset($__MYSQL_CFG['logSql']) && $this->_logSql = (boolean) $__MYSQL_CFG['logSql'];
        isset($__MYSQL_CFG['maxLogedSql']) && $this->_maxLogedSql = (int) $__MYSQL_CFG['maxLogedSql'];
    }

    /**
     * 执行SQL语句，并提取一行
     *
     * @param string $sql
     * @param int $type
     * @return array
     */
    public function fetchRow($sql = '', $type = MYSQL_ASSOC) {
        $needClean = false;
        if ($sql) {
            $this->execute($sql);
            $needClean = true;
        }
        
        if (empty ($this->_result)) return array();
        
        $row = mysql_fetch_array($this->_result, $type);
        $needClean && mysql_free_result($this->_result);
        
        if (empty ($row)) return array();
        return $row;
    }

    /**
     * 执行SQL语句，并以关联数组的形式提取出所有结果
     *
     * @param string $sql
     * @param int $count
     * @param int $offset
     * @return array
     */
    public function fetchAll($sql = '', $count = -1, $offset = 0) {
        if ($sql) {
            if ($count > 0) {
                if ($offset > 0) {
                    $sql = "{$sql} LIMIT {$offset}, {$count}";
                } else {
                    $sql = "{$sql} LIMIT {$count}";
                }
            }
            $this->execute($sql);
        }
        
        if (empty($this->_result))  return array();
        
        $data = array();
        while ($r = mysql_fetch_assoc($this->_result)) {
            $data[] = $r;
        }
        mysql_free_result($this->_result);
        return $data;
    }

    /**
     * 提取出一列的所有结果
     *
     * @param string $sql
     * @return array
     */
    public function fetchCol($sql = '') {
        if ($sql) {
            $this->execute($sql);
        }
        
        if (empty($this->_result))  return array();
        
        $ret = array();
        while ($r = mysql_fetch_row($this->_result)) {
            $ret[] = $r[0];
        }
        mysql_free_result($this->_result);
        return $ret;
    }

    /**
     * 以Key Value 的形式提出两列的所有行
     *
     * @param string $sql
     * @return array
     */
    public function fetchPair($sql = '') {
        if ($sql) {
            $this->execute($sql);
        }
        
        if (empty($this->_result))  return array();
        
        $ret = array();
        while ($r = mysql_fetch_row($this->_result)) {
            $ret[$r[0]] = $r[1];
        }
        mysql_free_result($this->_result);
        return $ret;
    }

    /**
     * 取出一个查询结果，常用于 fetchOne('SELECT COUNT(*) FROM tbl')
     *
     * @param string $sql
     * @return mixed
     */
    public function fetchOne($sql = '') {
        if ($sql) {
            $this->execute($sql);
        }
        
        if (empty($this->_result))  return null;
        
        $r = mysql_fetch_row($this->_result);
        mysql_free_result($this->_result);
        if (empty($r))
            return null;
        return $r[0];
    }

    /**
     * 从数据库中删除指定记录
     *
     * @param string $tablename
     * @param string $where
     * @return int
     */
    public function delete($tablename, $where = '') {
        if ($where)
            $sql = "DELETE FROM `{$tablename}` WHERE {$where}";
        elseif (stripos($tablename, 'delete') !== false)
            $sql = $tablename;
        elseif ($this->_isInTransaction)
            $sql = "DELETE FROM `{$tablename}`"; //事务中不允许执行DDL,否则会影响事务
        else
            $sql = "TRUNCATE TABLE `{$tablename}`";
        $this->execute($sql);
        $ret = mysql_affected_rows($this->_get_link());
        if($ret< 0) return false;
        return $ret;
    }

    /**
     * 开启事务
     *
     * @return mysql
     */
    public function beginTransaction() {
        if (!$this->_isInTransaction) {
            $this->execute('BEGIN');
            $this->_isInTransaction = true;
        } elseif ($this->_isDebug) {
            $this->_showError('已经开启事务，请不要重复开启');
        }
        return $this;
    }

    /**
     * 提交事务
     *
     * @return mysql
     */
    public function commit() {
        if ($this->_isInTransaction) {
            $this->execute('commit');
            $this->_isInTransaction = false;
        } elseif ($this->_isDebug) {
            $this->_showError('未开启事务，不能提交');
        }
        return $this;
    }

    /**
     * 撤销事务
     *
     * @return mysql
     */
    public function rollBack() {
        if ($this->_isInTransaction) {
            $this->execute('rollback');
            $this->_isInTransaction = false;
        } elseif ($this->_isDebug) {
            $this->_showError('未开启事务，无法回滚');
        }
        return $this;
    }

    /**
     * 插入一条数据
     *
     * @param string $tablename
     * @param array $data
     * @return int
     */
    public function insert($tablename, Array $data = null) {
        if (!empty($data)) {
            $columns = implode('`, `', array_keys($data));
            $values = array_values($data);
            $value_str = array();
            foreach ($values as $v) {
                if (is_int($v) || $v instanceof Db_Expr) {
                    $value_str[] = $v;
                } else {
                    $value_str[] = '\'' . $this->quote($v) . '\'';
                }
            }
            $value_str = implode(', ', $value_str);
            $sql = "INSERT INTO `{$tablename}` (`{$columns}`) VALUES ({$value_str})";
        } else {
            $sql = $tablename;
        }
        $result = $this->execute($sql);
        $link = $this->_get_link();
        $ret = mysql_insert_id($link);
        if (!$ret)
            $ret = mysql_affected_rows($link);
        if($ret< 0) return false; //未知错误会小于 0  
        return $ret;
    }
    
    public function insertArr($tablename,Array $data = null){
        if (!empty($data)) {
            $localArr = array();
            $columns  = '';
            foreach($data as $val){
                $value_str = array();
                if(!$columns) $columns = implode('`, `', array_keys($val));
                foreach($val as $v){
                    if (is_int($v) || $v instanceof Db_Expr) {
                        $value_str[] = $v;
                    } else {
                        $value_str[] = '\'' . $this->quote($v) . '\'';
                    }
                }
                $localArr [] = '('.implode( ',', $value_str ).')';
            }
            $sql = "INSERT INTO `{$tablename}`  (`{$columns}`) VALUES " .implode(',',$localArr);
        } else {
            $sql = $tablename;
        }
        $result = $this->execute($sql);
        $ret = mysql_affected_rows($this->_get_link());
        if($ret < 0) return false;
        return $ret;

    }
    
    
    
    public function replace($tablename, Array $data = null) {
        if (!empty($data)) {
            $update = array();
            foreach ($data as $k => $v) {
                $col[] = "`{$k}`";
                if (is_int($v) || $v instanceof Db_Expr) {
                    $value[] = "'{$v}'";
                } else {
                    $v = $this->quote($v);
                    $value[] = "'{$v}'";
                }
                $update[] = " `{$k}` = '{$v}' ";
                
            }
            $co = '('.implode(',', $col).')';
            $val = 'VALUES('.implode(',', $value).')';
            $upd = join(',',$update);
            
            $sql = "INSERT  INTO `{$tablename}` {$co} {$val}  ON DUPLICATE KEY UPDATE {$upd} ";
            //echo $sql.'<br />';
        } else {
            $sql = $tablename;
        }
        $result = $this->execute($sql);
        $ret = mysql_affected_rows($this->_get_link());
        if($ret< 0) return false;
        return $ret;
    }
    

    /**
     * 更新数据库中的记录
     *
     * @param string $tablename
     * @param array $data
     * @param string $where
     * @return int
     */
    public function update($tablename, Array $data = null, $where = '') {
        if (!empty($data)) {
            $sets = array();
            foreach ($data as $k => $v) {
                if (is_int($v) || $v instanceof Db_Expr) {
                    $sets[] = "`{$k}` = {$v}";
                } else {
                    $v = $this->quote($v);
                    $sets[] = "`{$k}` = '{$v}'";
                }
            }
            $sets = implode(',', $sets);
            if (!empty($where))
                $where = "WHERE {$where}";
            $sql = "UPDATE `{$tablename}` SET {$sets} {$where}";
        } else {
            $sql = $tablename;
        }
        $result = $this->execute($sql);
        $ret = mysql_affected_rows($this->_get_link());
        if($ret< 0) return false;
        return $ret;
    }

    /**
     * 转义一个字符串
     *
     * @param string $val
     * @return string
     */
    public function quote($val) {
        if ($this->_isMagicQuotesOn)
            $val = stripslashes($val);
        return mysql_real_escape_string($val, $this->_get_link());
    }

    /**
     * 执行一条SQL语句
     *
     * @param string $sql
     * @return resource
     */
    public function execute($sql) {
        $this->_result = mysql_query($sql, $this->_get_link());
        if (!$this->_result && $this->_isDebug)
            $this->_showError($sql);
        if ($this->_logSql && $this->_logedSqlCount < $this->_maxLogedSql) {
            ++$this->_logedSqlCount;
            $this->_executedSqls[] = $sql;
        }
        return $this->_result;
    }

    /**
     * 设置为调试模式
     *
     * @param boolean $isDebug
     * @return mysql
     */
    public function setDebug($isDebug = true) {
        $this->_isDebug = $isDebug;
        return $this;
    }

    /**
     * 是否记录执行过的SQL语句
     *
     * @param boolean $useSqlLog
     * @return mysql
     */
    public function logSql($useSqlLog = true) {
        $this->_logSql = $useSqlLog;
        return $this;
    }

    /**
     * 是否在事物中
     *
     * @return boolean
     */
    public function isInTransaction()
    {
        return $this->_isInTransaction;
    }

    /**
     * 析构函数，撤销未完成的事务
     *
     */
    public function __destruct() {
        if ($this->_isInTransaction) {
            $this->rollBack();
        }
    }

    public function close()
    {
        if (!$this->_cfg['pconnect'] && $this->_link)
        {
            mysql_close($this->_link);
        }
    }

    /**
     * 获取当前的数据库连接
     *
     * @return resource
     */
    protected function _get_link() {
        if (empty($this->_link)) {
            if ($this->_cfg['pconnect'])
            {
                $old_level  = error_reporting(0);
                $this->_link = mysql_pconnect($this->_cfg['host'], $this->_cfg['username'], $this->_cfg['password']);
                if (empty($this->_link))
                {
                    $errno  = mysql_errno();
                    if ($errno == 2006)
                    {
                        $this->_link    = mysql_pconnect($this->_cfg['host'], $this->_cfg['username'], $this->_cfg['password']);
                    }
                }
                error_reporting($old_level);
            }
            else
                $this->_link = mysql_connect($this->_cfg['host'], $this->_cfg['username'], $this->_cfg['password']);
            if (!$this->_link && $this->_isDebug)
                $this->_showError('Connect Error');
            mysql_select_db($this->_cfg['dbname'], $this->_link);
            if (!empty($this->_cfg['charset'])) {
                if (function_exists('mysql_set_charset')) {
                    if (!mysql_set_charset($this->_cfg['charset'], $this->_link) && $this->_isDebug)
                        $this->_showError('Charset error');
                } else {
                    if (!mysql_query("SET NAMES '{$this->_cfg['charset']}'", $this->_link) && $this->_isDebug)
                        $this->_showError('Charset error');
                }
            }
        }
        return $this->_link;
    }

    /**
     * 获取执行过的SQL语句
     *
     * @return array
     */
    public function getSqls()
    {
        return $this->_executedSqls;
    }

    /**
     * 打印错误信息
     *
     * @param string $sql
     */
    protected function _showError($sql) {
        $errno = 0;
        $error = 'MySQL Connect Error';
        $time = date('Y-m-d H:i:s', time());
        if (!empty($this->_link)) {
            $errno = mysql_errno($this->_link);
            $error = mysql_error($this->_link);
        }
        echo <<<EOT
<html>
    <head>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
    <title>数据库出错提示</title>
    </head>
<body>
<style>
.main{
	font-size:12px;
	width:100%;
}
</style>
<div class="main">
	<div style="font-weight:bold;">站点错误报告：</div>
	<div width="80%" style="text-align:left">
	<p>
　　当您来到这个页面的时候，代表着这里出现了一个严重的错误。<br />
　　请您尝试 <a href="">点击这里</a> 来刷新页面。或者 <a href="/">点击这里</a>
返回站点首页。如果问题还没有解决，请尝试 <a href="mailto: somebody@nobody.com">联系管理员</a> 来解决此问题。</p>
  <ul>
    <li>错误编号:[$error:$errno]</li>
    <li>出错时间:[$time]</li>
    <li>执行操作:[$sql]</li>
  </ul>
	</div>
</div>
<pre>
EOT;
        debug_print_backtrace();
        echo <<< EOF
</pre>
</body>
</html>
EOF;
        exit(1);
    }

}

?>