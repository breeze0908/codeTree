<?php


/**
* 		
*/
class DB_Mysql
{
		/**
	    * 服务器名或 ip 地址
	    *
	    * @var string
	    */
	    protected $server = "localhost";

    	/**
        * 数据库名
        *
        * @var string
        */

        protected $database = "";

        /**
        * 用户名
        *
        * @var string
        */

        protected $user = "root";

        /**
        * 用户密码
        *
        * @var string
        */

        protected $password = "";

        /**
        * 是否使用持续连接
        *
        * @var bool
        */

        protected $usepconnect = false;

        /**
        * 是否打开 debug 模式
        *
        * @var bool
        */

        protected $debug = false;

             /**
        * SQL query 次数
        *
        * @var integer
        */

        protected $querycount = 0;

        /**
        * 运行 SQL 请求后返回的结果集
        *
        * @var resource
        */

        public $result;

        /**
        *
        * @var array
        */

        public $record = array();

        public $rows;

        /**
        * 最后一次 INSERT 操作所返回的自增 ID
        *
        * @var integar
        */

        public $insertid;

        /**
        * 当出错时, 是否停止运行?
        *
        * @var bool 1: 停止, 2: 继续运行
        */

        public $halt = 1;

        /**
        * 错误号
        *
        * @var integer
        */

        public $errno;

        /**
        * 错误提示
        *
        * @var string
        */

        var $error;

        /**
        * SQL运行记录
        *
        * @var array
        */

        public $querylog = array();

        /**
        * 是否 cache 结果集, query_first, fetch_one_array 有效
        *
        * @var bool
        */

        public $use_cache = true;

        /**
        * 慢查询统计时间，毫秒
        *
        * @var int
        */

        public $slowQueryTime = 500;


		function __construct($options)
		{
			$this->server = $options['server'];
			$this->user = $options['user'];
			$this->password = $options['password'];
			$this->database = $options['database'];
		}




		/**
	    * 获取错误描述
	    *
	    * @access private
	    * @return string
	    */

	    function geterrdesc() {
	            $this->error = @mysql_error($this->id);

	            return $this->error;
	    }

	    /**
	    * 获取错误号
	    *
	    * @access private
	    * @return integer
	    */

	    function geterrno() {
	            $this->errno = @mysql_errno($this->id);

	            return $this->errno;
	    }

}