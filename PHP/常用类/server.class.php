<?php

/**
 * @date      : 2017/2/27 11:29
 * @author    : Tan
 */
class server {
	static public $errorCode    = 0;
	static public $errorMessage = '';

	static protected $http = NULL;

	/**
	 * 接口调用
	 * @param  string $request_url 接口请求地址
	 * @param  array  $options     CURL请求附加参数
	 * @return EpiCurlManager
	 */
	static public function call($request_url, $options = []) {
		self::clearErrMsg();
		if (empty(self::$http)) {
			self::$http = http::getInstance();
		}
		return self::$http->request($request_url, $options);
	}

	/**
	 * post
	 * @param       $request_url
	 * @param array $params
	 * @param array $options
	 * @return \EpiCurlManager
	 */
	static public function post($request_url, $params = [], $options = []) {
		$options['CURLOPT_POST']       = true;
		$options['CURLOPT_POSTFIELDS'] = $params;
		return self::call($request_url, $options);
	}

	/**
	 * get
	 * @param       $request_url
	 * @param array $options
	 * @return \EpiCurlManager
	 */
	static public function get($request_url, $options = []) {
		return self::call($request_url, $options);
	}

	/**
	 * @param $http
	 * @return bool|mixed
	 */
	static public function getInterfaceData($http) {
		$response = $http->response();

		if ($response['code'] == 200) {
			$data = json_decode($response['data'], true);
			if ($data['code'] === 0) {
				$data = isset($data['data']) ? $data['data'] : '';
				if ($response['time'] > 0.2) {
					// 记录慢查询接口
					self::writeLog("CURL REQUEST ERROR : HTTP_CODE=" . $response['code'] . '; TOTAL_TIME=' . $response['time'] . "; EFFECTIVE_URL=" . $response['url'] . '; Data :' . $response['data'], 'notice');
				}
			} else {
				// 记录接口返回错误数据
				self::$errorCode    = $http->error_no();
				self::$errorMessage = $http->error_msg();
				self::writeLog("CURL REQUEST ERROR : HTTP_CODE=" . $response['code'] . '; TOTAL_TIME=' . $response['time'] . "; EFFECTIVE_URL=" . $response['url'] . '; Data :' . $response['data'], 'warn');
				return false;
			}
		} else {
			// 记录接口请求错误
			self::writeLog("CURL REQUEST ERROR : HTTP_CODE=" . $response['code'] . '; TOTAL_TIME=' . $response['time'] . "; EFFECTIVE_URL=" . $response['url'] . '; Data :' . $response['data'], 'error');
			return false;
		}
		return $data;
	}

	/**
	 * 登录cookie
	 *
	 * @return string
	 */
	static public function getLoginCookie() {
		$userid    = isset($_COOKIE['userid']) ? $_COOKIE['userid'] : '';
		$sessionid = isset($_COOKIE['sessionid']) ? $_COOKIE['sessionid'] : '';
		return 'userid=' . $userid . '; sessionid=' . $sessionid;
	}

	/**
	 * 登录态用户cookie
	 * @return array
	 */
	static public function getUserCookies() {
		return ['userid' => $_COOKIE['userid'], 'sessionid' => $_COOKIE['sessionid']];
	}

	/**
	 * 获取用户提交的Cookie信息
	 */
	static public function getPostCookies() {
		$userid    = isset($_POST['userid']) ? $_POST['userid'] : '';
		$sessionid = isset($_POST['sessionid']) ? $_POST['sessionid'] : '';
		return 'userid=' . $userid . '; sessionid=' . $sessionid;
	}

	/**
	 * 清楚错误信息
	 */
	static private function clearErrMsg() {
		self::$errorCode    = 0;
		self::$errorMessage = '';
	}

	/**
	 * 写日志文件
	 *
	 * @param        $message
	 * @param string $level
	 * @param string $extra
	 */
	static function writeLog($message, $level = 'error', $extra = '') {
		defined('LOG_FILE_SIZE') or define('LOG_FILE_SIZE', 1024 * 1024 * 1024);
		$now         = date('[ c ]');
		$destination = rtrim(LOG_PATH, '/') . '/rpc/' . strtolower($level);

		if (!is_dir($destination)) {
			mkdir($destination, 0777, true);
		}

		$destination .= '/' . date('Ymd') . '.log';

		//检测日志文件大小，超过配置大小则备份日志文件重新生成
		if (is_file($destination) && floor(LOG_FILE_SIZE) <= filesize($destination)) {
			rename($destination, dirname($destination) . '/' . time() . '-' . basename($destination));
		}

		(ini_get('log_errors') != 'On') && ini_set("log_errors", 'On');
		error_log("{$now}  | {$level}: {$message}\r\n", 3, $destination, $extra);
	}
}