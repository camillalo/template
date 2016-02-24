<?php
/**
 * Created by PhpStorm.
 * User: chimero
 * Date: 14-6-2
 * Time: 22:33
 */
namespace Slim\Database;

class Source
{
    public $hostname;
    public $username;
    public $password;
    public $database;
    public $dbIndex;
    public $readFromSlaveDb = false;
    public $port = '3306';
    public $conn;
    public $charset = 'UTF8';
    public $pconnect = false;
    public $resource;
    public $resultArray = array();

    // 初始化
    public function init()
    {
        if(!$this->dbIndex) {
            return false;
        }
        // 获取数据库配置
        $conf = \Slim\Load::conf('database', $this->dbIndex);
        if($this->readFromSlaveDb === true && empty($conf['slave'])) {
            $currentConf = $conf['slave'];
        } else {
            $currentConf = $conf['master'];
        }

        $this->hostname = $currentConf['host'];
        $this->username = $currentConf['user'];
        $this->password = $currentConf['password'];
        $this->charset = $currentConf['charset'];
        $this->database = $currentConf['database'];
        $this->pconnect = isset($currentConf['pconnect']) ? $currentConf['pconnect'] : false;

        if($this->pconnect == true) {
            $this->conn = $this->dbPconnect();
        } else {
            $this->conn = $this->dbConnect();
        }

        $this->dbSelect();
        $this->dbCharset();
    }

    // 短链接
    private function dbConnect()
    {
        return @mysql_connect($this->hostname, $this->username, $this->password, true);
    }

    // 长链接
    private function dbPconnect()
    {
        return @mysql_connect($this->hostname, $this->username, $this->password, true);
    }

    // 选择数据库
    private function dbSelect()
    {
        return @mysql_select_db($this->database, $this->conn);
    }

    private function dbCharset()
    {
        return @mysql_set_charset($this->charset, $this->conn);
    }

    // 选择数据库
    public function close()
    {
        return @mysql_close($this->conn);
    }

    // 原始query
    private function _query($sql)
    {
        return $this->resource = mysql_query($sql, $this->conn);
    }


    /*
     ***************************************************
     * 数据库操作方法
     ***************************************************
     */

    // query
    public function query($sql, $retType = '')
    {
        // 过虑
        $ret = $this->_query($sql);

        if($retType == 'array') {
            $arr = $this->retArray();
            $ret = empty($arr) ? array() : current($arr);
        }

        if($retType === 1) {
            $ret = empty($ret) ? null : array_shift($ret);
        }

        return $ret;
    }

    //
    public function retArray()
    {
        if ($this->resource === false OR $this->affectedRows() == 0) {
            return array();
        }
        $result = array();
        while ($row = mysql_fetch_assoc($this->resource))
        {
            $result[] = $row;
        }
        return $result;
    }

    // insert
    public function insert($tbl, $array)
    {
        $fields = $values = array();
        foreach($array as $key => $val) {
            $fields[] = "`{$key}`";
            $values[] = "'{$val}'";
        }
        $field = '(' . implode(',', $fields) . ')';
        $value = '(' . implode(',', $values) . ')';
        $sql = "insert into {$tbl} {$field} values {$values}";
        $ret = $this->_query($sql);
        return $ret ? $this->lastID() : false;
    }

    // update
    public function update($tbl, $array = array(), $where = array())
    {
        if(empty($array) || empty($where)) {
            return false;
        }
        $temp = array();
        foreach($array as $key => $val) {
            $temp[] = "`{$key}`='$val'";
        }
        $set = implode(',', $temp);

        $temp = array();
        foreach($where as $key => $val) {
            $temp[] = "`{$key}`='$val'";
        }
        $condition = 'where ' . implode(' and ', $temp);
        $sql = "update {$tbl} set {$set} {$condition}";
        return $this->_query($sql);
    }

    // delete
    public function delete($tbl, $where)
    {
        if(empty($where)) {
            return false;
        }
        $temp = array();
        foreach($where as $key => $val) {
            $temp[] = "`{$key}`='$val'";
        }
        $condition = 'where ' . implode(' and ', $temp);
        $sql = "delete from {$tbl}  {$condition}";
        return $this->_query($sql);
    }

    // 上次插入ID
    public function lastID()
    {
        return @mysql_insert_id($this->conn);
    }

    // 受影响行数
    public function affectedRows()
    {
        return @mysql_affected_rows($this->conn);
    }




}