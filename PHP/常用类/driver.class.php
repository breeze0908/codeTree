<?php

/**
 * @file      : driver.class.php
 * @desc      : description about this file
 * @date      : 2017/2/9 10:57
 * @author    : Tan 
 */
class DBDriver
{

    protected $exp = array('eq'=>'=','neq'=>'<>','gt'=>'>','egt'=>'>=','lt'=>'<','elt'=>'<=','notlike'=>'NOT LIKE','like'=>'LIKE','in'=>'IN','notin'=>'NOT IN','not in'=>'NOT IN','between'=>'BETWEEN','not between'=>'NOT BETWEEN','notbetween'=>'NOT BETWEEN');

    /**
     * 数据库表
     *
     * @var null
     */
    public $table = null;

    /**
     * 数据库实例
     *
     * @var null
     */
    public $dbInstance = null;

    /**
     * 最后一条执行的sql
     * @var string
     */
    protected $lastSql = '';


    public function setTable ($table)
    {
        $this->table = $table;
        return $this;
    }


    public function DBInstance ($db = '')
    {
        if($db) {
            $this->dbInstance = mysql_instance($db);
        }
        return $this->dbInstance;
    }



    public function __construct ($db = 'default')
    {
        $this->DBInstance($db);
    }

    /**
     * 插入一条记录
     *
     * @param array $data
     * @param bool  $replace
     * @return mixed
     */
    public function insert ($data, $replace = false)
    {
        $fields = $this->parseKeyArr(array_keys($data));

        foreach ($data as $key => $val) {
            $values[] = $this->parseValue($val);
        }

        $this->lastSql = (true === $replace ? 'REPLACE' : 'INSERT') . ' INTO ' . $this->table . ' (' . implode(',', $fields) . ') VALUES (' . implode(',', $values) . ')';
        return $this->dbInstance->query($this->lastSql);
    }


    /**
     * 插入多条记录
     *
     * @param      $dataSet
     * @param bool $replace
     * @return mixed
     */
    public function insertAll ($dataSet, $replace = false)
    {
        $fields = array_map(array($this, 'parseKeyArr'), array_keys($dataSet[0]));
        foreach ($dataSet as $data) {
            $value = array();
            foreach ($data as $key => $val) {
                $value[] = $this->parseValue($val);
            }
            $values[] = '(' . implode(',', $value) . ')';
        }
        $replace = (is_numeric($replace) && $replace > 0) ? true : $replace;
        $this->lastSql = (true === $replace ? 'REPLACE' : 'INSERT') . ' INTO ' . $this->table . ' (' . implode(',', $fields) . ') VALUES ' . implode(',', $values);
        return $this->dbInstance->query($this->lastSql);
    }

    /**
     * 查询数据
     *
     * @param string|array $fields
     * @param string|array  $where
     * @param string|array  $order_by
     * @param null|string|array   $limit
     * @return mixed
     */
    public function select ($fields = '*', $where = array(), $order_by = '', $limit = null)
    {
        $select = $this->parseSelectFileds($fields);
        $where = $this->parseWhere($where);
        $order_by = $this->parseOrder($order_by);
        $limit = $this->parseLimit($limit);

        $this->lastSql = "SELECT {$select} FROM {$this->table} {$where} {$order_by} {$limit}";
        return $this->dbInstance->query_all($this->lastSql);


    }

    /**
     * @param string|array $fields
     * @param string|array  $where
     * @param string|array $order_by
     * @return mixed
     */
    public function selectOne ($fields = '*', $where = array(), $order_by = '')
    {
        $select = $this->parseSelectFileds($fields);
        $where = $this->parseWhere($where);
        $order_by = $this->parseOrder($order_by);
        $this->lastSql = "SELECT {$select} FROM {$this->table} {$where} {$order_by} LIMIT 1";
        return $this->dbInstance->query_first($this->lastSql);
    }


    /**
     * 更新数据
     *
     * @param array        $data
     * @param string|array $where
     * @param null|string|array $limit
     * @return bool
     */
    public function update ($data, $where, $limit = null) {
        //更新数据为空
        if(empty($data)) {
            return false;
        }

        foreach ($data as $key => $val) {
            $val = $this->parseValue($val);
            $set_arr[] = $this->parseKey($key) . '=' .  $val;
        }
        $this->lastSql = "UPDATE {$this->table} SET " . implode(',', $set_arr) . ' ' . $this->parseWhere($where) . ' ' . $this->parseLimit($limit);
        return $this->dbInstance->query($this->lastSql);
    }

    /**
     * 直接执行
     *
     * @param $sql
     * @return mixed
     */
    public function query($sql) {
        $this->lastSql = $sql;
        return $this->dbInstance->query($sql);
    }

    /**
     * 获取最后一次插入的id
     *
     * @return mixed
     */
    public function insertId() {
        return $this->dbInstance->insert_id();
    }


    /**
     * update 时获取受影响的行数
     *
     * @return mixed
     */
    public function affectedRows() {
        return $this->dbInstance->affected_rows();
    }

    /**
     * 删除符合条件的一条记录
     *
     * @param string|array $where
     * @return bool
     * @throws \Exception
     */
    public function deleteOne($where = '') {
        //不允许不带条件的删除发送
        if(empty($where)) {
            throw new Exception("删除数据的sql必须带有条件");
            return false;
        }

        $this->lastSql = "DELETE FROM {$this->table}" . $this->parseWhere($where) . 'LIMIT 1';
        return $this->dbInstance->query($this->lastSql);
    }


    /**
     * 删除所有符合条件的记录
     *
     * @param $where
     * @return bool
     * @throws \Exception
     */
    public function deleteAll($where)
    {
        if(empty($where)) {
            throw new Exception("删除数据的sql必须带有条件");
            return false;
        }

        $this->lastSql = "DELETE FROM {$this->table}" . $this->parseWhere($where);
        return $this->dbInstance->query($this->lastSql);
    }

    /**
     * 打印最后一条执行的sql
     * @return string
     */
    public function getLastSql() {
        return $this->lastSql;
    }



    /**
     * SQL指令安全过滤
     *
     * @access protected
     * @param string $str SQL字符串
     * @return string
     */
    protected function escapeString ($str)
    {
        return addslashes($str);
        //return mysql_real_escape_string($str);
    }

    /**
     * @param $val
     * @return string
     */
    protected function  parseValue($val) {
        if(is_array($val)) {
            return array_map($this->escapeString, $val);
        }

        if (is_scalar($val)) {
            $value = "'" . $this->escapeString($val) . "'";
        } elseif (is_null($val)){
            $value = 'null';
        } else {
            $value = '';
        }
        return $value;
    }


    /**
     * 字段和表名处理
     *
     * @access protected
     * @param string $key
     * @return string
     */
    protected function parseKey (&$key)
    {
        $key = trim($key);
        if (!is_numeric($key) && !preg_match('/[,\'\"\*\(\)`.\s]/', $key)) {
            $key = '`' . $key . '`';
        }
        return $key;
    }

    /**
     * 字段和表名处理
     *
     * @access protected
     * @param string $keyArr
     * @return string
     */
    protected function parseKeyArr (&$keyArr)
    {
        if(is_array($keyArr)) {
            foreach ($keyArr as $i => $key) {
                $keyArr[$i] = $this->parseKey($key);
            }
        }
        return $keyArr;
    }

    /**
     * where 语句解析
     *
     * @param string $where
     * @return string
     */
    protected function parseWhere ($where = '')
    {
        $whereStr = '';

        if (is_string($where)) {
            $whereStr = $where;
        } else {
            // 使用数组表达式
            $operate = isset($where['_logic']) ? strtoupper($where['_logic']) : '';
            if (in_array($operate, array('AND', 'OR', 'XOR'))) {
                // 定义逻辑运算规则 例如 OR XOR AND NOT
                $operate = ' ' . $operate . ' ';
                unset($where['_logic']);
            } else {
                // 默认进行 AND 运算
                $operate = ' AND ';
            }

            foreach ($where as $key => $val) {// 多条件支持
                $whereStr .= $this->parseWhereItem($this->parseKey($key),$val);
                $whereStr .= $operate;
            }
            $whereStr = substr($whereStr,0,-strlen($operate));
        }
        return empty($whereStr) ? '' : ' WHERE ' . $whereStr;
    }


    protected function parseWhereItem($key , $val) {
        $whereStr = '';

        if(is_array($val)) {
            if(is_string($val[0])) {
                $exp = strtolower($val[0]);
                if(preg_match('/^(eq|neq|gt|egt|lt|elt)$/',$exp)) { // 比较运算
                    $whereStr .= $key.' '.$this->exp[$exp].' '.$this->parseValue($val[1]);
                }elseif(preg_match('/^(notlike|like)$/',$exp)) {// 模糊查找
                    if (is_array($val[1])) {
                        $likeLogic = isset($val[2]) ? strtoupper($val[2]) : 'OR';
                        if (in_array($likeLogic, array('AND', 'OR', 'XOR'))) {
                            $like = array();
                            foreach ($val[1] as $item) {
                                $like[] = $key . ' ' . $this->exp[$exp] . ' ' . $this->parseValue($item);
                            }
                            $whereStr .= '(' . implode(' ' . $likeLogic . ' ', $like) . ')';
                        }
                    } else {
                        $whereStr .= $key . ' ' . $this->exp[$exp] . ' ' . $this->parseValue($val[1]);
                    }
                }elseif(preg_match('/^(notin|not in|in)$/',$exp)) { // IN 运算
                    if (is_string($val[1])) {
                        $val[1] = explode(',', $val[1]);
                    }
                    $zone = implode(',', $this->parseValue($val[1]));
                    $whereStr .= $key . ' ' . $this->exp[$exp] . ' (' . $zone . ')';
                }elseif(preg_match('/^(notbetween|not between|between)$/',$exp)) { // BETWEEN运算
                    $data = is_string($val[1]) ? explode(',', $val[1]) : $val[1];
                    $whereStr .= '( ' . $key . ' ' . $this->exp[$exp] . ' ' . $this->parseValue($data[0]) . ' AND ' . $this->parseValue($data[1]) . ' )';
                }
            }else {
                $count = count($val);
                $rule  = isset($val[$count-1]) ? (is_array($val[$count-1]) ? strtoupper($val[$count-1][0]) : strtoupper($val[$count-1]) ) : '' ;
                if(in_array($rule,array('AND','OR','XOR'))) {
                    $count  = $count -1;
                }else{
                    $rule   = 'AND';
                }
                for($i=0;$i<$count;$i++) {
                    $whereStr .= $this->parseWhereItem($key,$val[$i]).' '.$rule.' ';
                }
                $whereStr = '( '.substr($whereStr,0,-4).' )';
            }
        }else {
            $whereStr .= $key.' = '.$this->parseValue($val);
        }
        return $whereStr;
    }


    /**
     * order分析
     *
     * @access protected
     * @param mixed $order
     * @return string
     */
    protected  function parseOrder ($order)
    {
        if (is_array($order)) {
            $array = array();
            foreach ($order as $key => $val) {
                if (is_numeric($key)) {
                    $array[] = $val;
                } else {
                    $array[] = $key . ' ' . $val;
                }
            }
            $order = implode(',', $array);
        }
        return !empty($order) ? ' ORDER BY ' . $order : '';
    }

    /**
     * fields分析
     *
     * @param  string|array $fields
     * @return string
     */
    protected function parseSelectFileds ($fields)
    {
        if (is_array($fields)) {
            $fields = implode(', ', $fields);
        }
        return !empty($fields) ? $fields : '*';
    }

    /**
     * limit 分析
     *
     * @param  string|array $limit
     * @return string
     */
    protected function parseLimit ($limit = '')
    {
        if (is_array($limit)) {
            if (count($limit) >= 2) {
                $limit = $limit[0] . ", " . $limit[1];
            } else {
                $limit = $limit[0];
            }

        }
        return !empty($limit) ? "LIMIT " . $limit : '';
    }

    /**
     * 计算总数
     * @param  string|array $where
     * @return
     */
    public function count($fields='*',$where) {
        $this->lastSql = "SELECT count(" . $fields . ") as num from {$this->table} " . $this->parseWhere($where);
        return $this->dbInstance->query_first($this->lastSql);
    }
}