<?php
/*
 ***********************************************************************************************************************
 * 使用方法：
 * $this->db = core::Singleton('comm.db.activeRecord');
 * $this->db->select()->from()->join()->order_by()->limit()->get();
 * 注意：自己拼接检索数据时，get()方法必须最后调用。其他操作不必调用get()

$db = core::Singleton('comm.db.activeRecord');
$db->connect('SMS');
$db->get_all(array('available' => '1'), '', '', 'tbl_network_dev');
$db->update(array('id' => $dev['id']), $data, 'tbl_network_dev');
$db->get_one(array('available' => '1', 'sleep' => '0'), 'send_time asc', 'tbl_network_dev');
$db->insert($data, 'tbl_network_sms_log');

 ***********************************************************************************************************************
*/
class activeRecord
{
    public  $tbl = '';
    private $_db_read = '';
    private $_db_write = '';
    private $_where = array();
    private $_join_tbls = array();
    private $_join_ons = array();
    private $_join_types = array();
    private $_limit = array();
    private $_order_by = '';
    private $_select = '';
    private $_container = array();

    // 设置数据源
    public function connect($db)
    {
        $this->_db_read = core::db()->getConnect($db, true);
        $this->_db_write = core::db()->getConnect($db);
        return $this;
    }

    // 切换使用临时数据库
    public function use_db($db)
    {
        if(empty($db)) {
            return false;
        }
        $md = md5($db);
        if(!in_array($md, $this->_container)) {
            $clone = new activeRecord();
            $clone->connect($db);
            $this->_container[$md] = $clone;
        }

        return $this->_container[$md];
    }

    // 单表按条件检索数据
    public function get_all($where = array(), $limit = array(0, 10), $order_by = '', $tbl = '')
    {
        return $this->where($where)->limit($limit)->order_by($order_by)->get($tbl);
    }

    // 单表获取一条数据
    public function get_one($where = array(), $order_by = '', $tbl = '')
    {
        $ret = $this->where($where)->order_by($order_by)->limit(1)->get($tbl);

        if(!empty($ret) && is_array($ret)) {
            return current($ret);
        }
        return null;
    }

    // 统计
    public function count_all($where = array(), $tbl = '')
    {
        $this->tbl  = $tbl ?: $this->tbl;
        if(empty($this->tbl)) {
            return 0;
        }

        $where = $this->where($where)->_where();
        $sql = "select count(*) as num from {$tbl} {$where }";
        $ret = $this->_db_read->query($sql, 1);

        return $ret ? $ret : 0;

    }

    // 插入
    public function insert($data, $tbl = '')
    {
        $this->tbl  = $tbl ?: $this->tbl;
        if(empty($this->tbl) || empty($data)) {
            return false;
        }

        foreach($data as $key => $val) {
            $vals[] = "'" . addslashes($val) . "'";
            $keys[] = "`" . $key . "`";

        }

        $key_str = implode(',', $keys);
        $val_str = implode(',', $vals);
        $sql = "insert into {$tbl} ({$key_str}) values ({$val_str})";
        // insertId
        $ret = $this->_db_write->query($sql);
        return $ret ? $this->_db_write->insertId() : false;
    }

    // 更新
    public function update($where = array(), $data, $tbl = '', $limit = '')
    {
        if(empty($where) || empty($data)) {
            return false;
        }

        $this->tbl  = $tbl ?: $this->tbl;
        if(empty($this->tbl)) {
            return false;
        }

        // 拼接条件
        $where = $this->where($where)->_where();

        $vals = array();
        foreach($data as $key => $val) {
            $vals[] = $key . "='" . addslashes($val) . "'";
        }
        $val_str = implode(',', $vals);

        $limit = empty($limit) ? '' : " limit "  . $limit;

        $sql = "update {$tbl} set {$val_str} {$where} {$limit}";

        return $this->_db_write->query($sql) ? true : false;
    }

    // 删除
    public function delete($where = array(), $tbl = '', $limit = '')
    {
        if(empty($where)) {
            return false;
        }

        $this->tbl  = $tbl ?: $this->tbl;
        if(empty($this->tbl)) {
            return false;
        }

        // 拼接条件
        $where = $this->where($where)->_where();

        $limit = empty($limit) ? '' : " limit "  . $limit;

        $sql = "delete  from {$tbl} {$where} {$limit}";

        return $this->_db_write->query($sql) ? true : false;
    }


    /*
     ***************************************************
     * 拼接检索条件
     ***************************************************
     */
    public function where($where = array())
    {
        $this->_where = $where;
        return $this;
    }

    private function _where()
    {
        if(empty($this->_where)) {
            return '';
        }

        // 拼接条件
        foreach($this->_where as $key => $val) {

            // 去除空格
            $key = trim($key);
            //$val = preg_replace('/\s+/', '', $val);

            // 匹配
            $part_one = preg_match('/\s+[>][=]$|\s+[<][=]$|\s+[>]$|\s+[<]$|\s+[!][=]$|\s+like$|\s+in$/', $key) ? $key : $key . ' =';

            // in 查询
            if(preg_match('/\s+in$/', $key)) {

                if(is_array($val) && count($val)) {
                    foreach($val as $k => $v) {
                        $in[$k] = "'" . addslashes($v) . "'";
                    }
                    $part_two = "(" . implode(',', $in) . ")";
                } else {
                    $part_two = addslashes($val);
                }
            } else {
                $part_two = "'" . addslashes($val) . "'";
            }

            $part_one = preg_match('/[.]/', $part_one) ? $part_one : $this->tbl . '.' . $part_one;
            $condition[] = $part_one . ' ' . $part_two;
        }

        $this->_where =  array();
        return 'WHERE ' . implode(' AND ', $condition) . ' ';
    }

    public function join($join_tbl = '', $join_on = '', $join_type = 'LEFT JOIN')
    {
        if(empty($join_tbl)) {
            return $this;
        }
        $this->_join_tbls[$join_tbl] = $join_tbl;
        $this->_join_types[$join_tbl] = $join_type;
        $this->_join_ons[$join_tbl] = $join_on;
        return $this;
    }

    public function _join()
    {
        if(empty($this->_join_tbls)) {
            return '';
        }
        $join_str = ' ';
        foreach($this->_join_tbls as $val) {
            $join_str .= $this->_join_types[$val] . ' ' . $val . ' ON(' . $this->_join_ons[$val].') ';
        }
        $this->_join_tbls = array();
        $this->_join_types = array();
        $this->_join_ons = array();
        return $join_str . ' ';
    }

    public function limit($limit = array(0, 10))
    {
        $this->_limit = $limit;
        return $this;
    }

    private function _limit()
    {
        if(empty($this->_limit)) {
            return ' ';
        }
        if(is_array($this->_limit) && count($this->_limit) == 2) {
            $ret = ' LIMIT ' . $this->_limit[0] . ',' . $this->_limit[1] . ' ';
        } else {
            $ret =  ' LIMIT ' . intval($this->_limit) . ' ';
        }

        $this->_limit = array();

        return $ret;
    }

    public function order_by($order_by = '')
    {
        $this->_order_by = $order_by;
        return $this;
    }

    private function _order_by()
    {
        if(empty($this->_order_by)) {
            return '';
        }

        $parts = explode(',', $this->_order_by);
        $parts_arr = array();
        foreach($parts as $val) {
            $parts_arr[] = preg_match('/[.]/', $val) ? $val : $this->tbl . '.' .trim($val);
        }

        $this->_order_by = '';
        return 'ORDER BY ' . implode(',', $parts_arr) . ' ';
    }

    public function select($select = '')
    {
        $this->_select = $select;
        return $this;
    }

    private function _select()
    {
        if(empty($this->_select)) {
            return 'SELECT '.$this->tbl.'.* ';
        }
        $arr  = explode(',', $this->_select);
        $select = array();
        foreach($arr as $k => $v) {
            $select[$k] = preg_match('/'.$this->tbl.'/', $v) ? $v : $this->tbl . '.' . $v;
        }

        $this->_select = '';
        return 'SELECT ' . implode(',', $select) . ' ';
    }

    public function from($tbl = '')
    {
        $this->tbl = $tbl;
        return $this;
    }

    private function _from()
    {
        return 'FROM ' . $this->tbl . ' ';
    }


    // 取数据
    public function get($tbl = '')
    {
        // 设置当前db
        if(empty($this->_db_read)) {
            return false;
        }
        // 设置当前tbl
        $this->tbl = $tbl ?: $this->tbl;
        if(empty($this->tbl)) {
            return false;
        }

        // 拼接sql
        $sql = $this->_select() . $this->_from() . $this->_join() . $this->_where(). $this->_order_by() . $this->_limit();
        // 执行sql
        $rs = $this->_db_read->query($sql);

        // 组装结果
        $result = array();
        while($row = $this->_db_read->fetchArray($rs)) {
            $result[] = $row;
        }
        return $result;
    }

    // 执行数据语句
    public function query($sql)
    {
        return $this->_db_write->query($sql) ? true : false;
    }
}