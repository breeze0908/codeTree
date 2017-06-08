<?php

/**
 * @date      : 2017/2/27 11:29
 * @author    : Tan
 */
class http {
	/**
	 *
	 * @var 请求句柄
	 */
	private $handle;

	/**
	 * @var返回数据
	 */
	private $responses = [];

	/**
	 * @var 当前重试次数
	 */
	private $retry = 0;

	/**
	 * curl请求返回信息
	 *
	 * @var
	 */
	private $code;
	private $curl_info;
	private $curl_errno;
	private $curl_errmsg;

	/**
	 * curl 实例
	 *
	 * @var
	 */
	private static $instance;

	/**
	 * curl option
	 *
	 * @var array
	 */
	private $options = [
		'CURLOPT_HEADER'         => 0, //是否返回响应头
		'CURLOPT_TIMEOUT'        => 30,
		'CURLOPT_CONNECTTIMEOUT' => 10,
		'CURLOPT_USERAGENT'      => 'KKCurlHttpClient/v1.0',
		'CURLOPT_AUTOREFERER'    => true, //根据 Location: 重定向时，自动设置 header 中的Referer:信息
		'CURLOPT_FOLLOWLOCATION' => true, //根据服务器返回 HTTP 头中的 "Location: " 重定向响应
		'CURLOPT_RETURNTRANSFER' => true,
	];

	/**
	 * 初始化curl句柄
	 * http constructor.
	 */
	private function __construct() {
		$this->handle = curl_init();

		$this->properties = [
			'code'   => CURLINFO_HTTP_CODE,
			'time'   => CURLINFO_TOTAL_TIME,
			'length' => CURLINFO_CONTENT_LENGTH_DOWNLOAD,
			'type'   => CURLINFO_CONTENT_TYPE,
			'url'    => CURLINFO_EFFECTIVE_URL,
		];
	}

	/**
	 * 设置ssl
	 */
	private function setSSL() {
		$this->options['CURLOPT_SSL_VERIFYPEER'] = false;
		//$this->options['CURLOPT_SSL_VERIFYSTATUS'] = false;//不验证证书状态
	}

	/**
	 * 发送http请求
	 *
	 * @param $request_url
	 * @param $options
	 * @param $retry
	 * @return $this
	 */
	public function request($request_url, $options, $retry = 1) {
		$ch = curl_init();
		preg_match('/https:/', $request_url) && $this->setSSL();
		$options['CURLOPT_URL'] = $request_url;
		$options                = array_merge($this->options, $options);

		foreach ($options as $key => $val) {
			if (is_string($key)) {
				$key = constant(strtoupper($key));
			}
			curl_setopt($ch, $key, $val);
		}

		$this->responses         = [];
		$this->responses['data'] = (string) curl_exec($ch);
		$this->curl_info         = curl_getinfo($ch);
		$this->curl_errno        = curl_errno($ch);
		$this->curl_errmsg       = $this->curl_errno ? curl_error($ch) : '';

		//返回头部信息
		foreach ($this->properties as $name => $const) {
			$this->responses[$name] = curl_getinfo($ch, $const);
		}

		curl_close($ch);

		if ($this->curl_errno && $retry < $this->retry) {
			$this->request($retry + 1);
		}

		$this->retry = 0;
		return $this;
	}

	/**
	 * 获取http返回数据
	 *
	 * @return array|null
	 */
	public function response() {
		if ($this->responses['data']) {
			return $this->responses;
		} else {
			return null;
		}
	}

	/**
	 * @return \返回数据
	 */
	public function data() {
		return $this->data;
	}

	/**
	 * @return mixed
	 */
	public function info() {
		return $this->curl_info;
	}

	/**
	 * @return mixed
	 */
	public function error_no() {
		return $this->curl_errno;
	}

	/**
	 * @return mixed
	 */
	public function error_msg() {
		return $this->curl_errmsg;
	}

	/**
	 * Instance
	 *
	 * @return self
	 */
	public static function getInstance() {
		if (self::$instance === null) {
			self::$instance = new self;
		}
		return self::$instance;
	}

}