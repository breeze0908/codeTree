<?php
/**
 * 数据库语句
 *
 * @param string $action
 * @param string $table
 * @param        $data
 * @param string $condition
 * @param string $field
 * @param string $limit
 * @return string
 */
function array_2_sql($action = 'update', $table = '', $data, $condition = '', $field = '*', $limit = '') {
	$tmp_data = [];
	if (is_array($data)) {
		foreach ($data as $k => $v) {
			$k          = addslashes(trim($k));
			$v          = addslashes(trim($v));
			$tmp_data[] = "{$k}='{$v}'";
		}
	} else {
		$tmp_data[] = $data;
	}

	if ($condition) {
		$tmp_cond = [];
		if (is_array($condition)) {
			foreach ($condition as $k => $v) {
				$k          = addslashes(trim($k));
				$v          = addslashes(trim($v));
				$tmp_cond[] = "{$k}='{$v}'";
			}
		} else {
			$tmp_cond[] = $condition;
		}
		$condition = ' WHERE ' . implode(' AND ', $tmp_cond);
	} else {
		$condition = '';
	}

	$limit = $limit ? (' LIMIT ' . $limit) : '';

	switch (strtolower($action)) {
	case "select":
		return 'SELECT ' . (is_array($field) ? implode(',', $field) : $field) . ' FROM ' . $table . $condition . ' ' . $limit;
	case "update":
		return 'UPDATE ' . $table . ' SET ' . implode(', ', $tmp_data) . $condition . ' ' . $limit;
	case "delete":
		return 'DELETE FROM ' . $table . $condition . ' ' . $limit;
	default:
		return $action . ' INTO ' . $table . ' SET ' . implode(', ', $tmp_data) . $condition . ' ' . $limit;
	}
}

//简单的PDO链接
class DB {
	private $pdo = null;
	public function __construct() {
		$this->connect("localhost", 'database', 'root', '123456');
	}

	/**
	 * 执行连接接
	 *
	 * @param  string $host     主机
	 * @param  string $dbname   数据库
	 * @param  string $username 用户名
	 * @param  string $password 密码
	 */
	public function connect($host, $dbname, $username, $password) {
		try {
			$dns       = "mysql:host={$host};dbname={$dbname}";
			$this->pdo = new PDO($dns, $username, $password);
		} catch (PDOException $e) {
			echo 'Databases connection failed: ' . $e->getMessage();exit;
		}
	}

	/**
	 * 查询语句
	 * @param  string $sql
	 * @return mixed
	 */
	public function query($sql) {
		return $stm = $this->pdo->query($sql);
	}

	/**
	 * @param  resource $stm
	 * @return
	 */
	public function fetch_array($stm) {
		return $stm->fetch(PDO::FETCH_ASSOC);
	}
}