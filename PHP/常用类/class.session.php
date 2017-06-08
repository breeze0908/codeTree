<?php

/**
 * 数据库方式Session驱动
 */
class Session {
	/**
	 * 数据库句柄
	 */
	protected $hander = [];

	/**
	 * session数据
	 * @var array
	 */
	protected $sessionData = [];

	/**
	 * session保存的数据库名
	 * @var string
	 */
	protected $sessionTable = 'session';

	/**
	 * Session有效时间
	 * @var integer
	 */
	protected $sessionLife = 3600; // 1小时

	/**
	 * 构造函数
	 * 取得DB类的实例对象 字段检查
	 *
	 * @access public
	 * @param mixed $db 数据库实例
	 */
	public function __construct($db) {
		$this->db = $db;
		$this->execute();
	}

	/**
	 * 打开Session
	 *
	 * @access public
	 */
	public function execute() {
		session_set_save_handler(
			[&$this, "open"],
			[&$this, "close"],
			[&$this, "read"],
			[&$this, "write"],
			[&$this, "destroy"],
			[&$this, "gc"]
		);
	}

	/**
	 * 打开Session
	 *
	 * @access public
	 * @param string $savePath
	 * @param mixed  $sessName
	 * @return bool
	 */
	public function open($savePath, $sessName) {
		return true;
	}

	/**
	 * 关闭Session
	 *
	 * @access public
	 */
	public function close() {
		$this->gc(ini_get('session.gc_maxlifetime'));
		return true;
	}

	/**
	 * 读取Session
	 *
	 * @access public
	 * @param $sessionid
	 * @return mixed|string
	 */
	public function read($sessionid) {
		$sql               = "SELECT * FROM {$this->sessionTable} WHERE sessionid='{$sessionid}' LIMIT 1";
		$res               = mysql_query($sql, $this->hander);
		$this->sessionData = ($res) ? mysql_fetch_array($res) : [];

		if (!empty($this->sessionData) AND $this->sessionData['expiry'] > time()) {
			return $this->sessionData['value'];
		} else {
			return "";
		}
	}

	/**
	 * 写入Session
	 *
	 * @access public
	 * @param string $sessionid
	 * @param String $val
	 * @return mixed
	 */
	public function write($sessionid, $val) {
		$now    = time();
		$expiry = $now + $this->sessionLife;
		$value  = addslashes($val);

		$ipaddress  = addslashes($_SERVER['REMOTE_ADDR']);
		$useragent  = addslashes($_SERVER['HTTP_USER_AGENT']);
		$requesturi = addslashes($_SERVER['REQUEST_URI']);

		$userid = isset($v_userinfo['userid']) ? $v_userinfo['userid'] : '';

		$sql = "REPLACE INTO {$this->sessionTable} (sessionid,expiry,value,userid,ipaddress,useragent,location,lastactivity)
            VALUES ('{$sessionid}',{$expiry},'{$value}','{$userid}','{$ipaddress}','{$useragent}','{$requesturi}','{$now}')";
		return mysql_query($sql, $this->hander);
	}

	/**
	 * 删除Session
	 *
	 * @access public
	 * @param string $sessionid
	 */
	public function destroy($sessionid) {
		mysql_query("DELETE FROM {$this->sessionTable} WHERE sessionid='{$sessionid}'", $this->hander);
		return mysqli_affected_rows($this->hander);
	}

	/**
	 * Session 垃圾回收
	 * 清理掉表中所有过期的session
	 *
	 * @param string $sessMaxLifeTime
	 * @access public
	 */
	public function gc($sessMaxLifeTime) {
		mysql_query("DELETE FROM {$this->sessionTable} WHERE expiry<" . time(), $this->hander);
		return mysqli_affected_rows($this->hander);
	}
}