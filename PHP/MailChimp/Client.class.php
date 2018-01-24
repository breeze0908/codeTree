<?php
namespace App\Http\Lib\MailChimp;

require_once "src/MailChimp.php";
require_once "src/Webhook.php";
//require_once "src/Batch.php";

use App\Http\Lib\Log;
use DrewM\MailChimp\MailChimp;

/**
 * MailChimpAPI客户端
 * @author Tan <tanda@wondershare.cn>
 */
class Client {
	protected $APIKey = 'XXXXXX';
	protected $MailChimp;

	/**
	 * 静态实例
	 * @var array
	 */
	protected static $_instances = [];

	/**
	 * 实例化静态方法
	 *
	 * @return object
	 */
	public static function getInstance() {
		$class = get_called_class();
		if (!isset(self::$_instances[$class])) {
			self::$_instances[$class] = new $class();
		}
		return self::$_instances[$class];
	}

	/**
	 * 构造函数
	 * @param string $APIKey
	 */
	protected function __construct() {
		$this->MailChimp = new MailChimp($this->APIKey);
	}

	/**
	 * 返回错误信息
	 *
	 * @return string
	 */
	public function getLastError() {
		return $this->MailChimp->getLastError();
	}

	/**
	 * 最后一个响应信息
	 *
	 * @return array
	 */
	public function getLastResponse() {
		return $this->MailChimp->getLastResponse();
	}

	/**
	 * 最后一个请求信息
	 *
	 * @return array
	 */
	public function getLastRequest() {
		return $this->MailChimp->getLastRequest();
	}

	/**
	 * 日志输出
	 * @param  string $flag     标识
	 * @param  string $message  错误信息
	 * @param  array  $request  请求信息
	 * @param  array  $response 响应信息
	 * @return
	 */
	public function writeLog($level, $flag, $message, $request = [], $response = []) {
		$message = "{$flag} | {$message};";
		if ($request) {
			$message .= " REQUEST: " . json_encode($request, JSON_UNESCAPED_SLASHES) . ";";
		}

		if ($response) {
			$message .= " RESPONSE: " . json_encode($response, JSON_UNESCAPED_SLASHES);
		}
		self::logged($message, $level);
	}

	/**
	 * 日志写入接口
	 * @access public
	 * @param string $log 日志信息
	 * @param string $destination  写入目标
	 * @return void
	 */
	protected static function logged($message, $level = 'error', $destination = '') {
		$log         = "{$level}: {$message}";
		$destination = "/var/tmp/mailchimp/{$level}/";
		$destination .= (empty($destination) ? 'common' : $destination) . '-' . date('y_m_d') . '.log';

		// 自动创建日志目录
		$log_dir = dirname($destination);
		if (!is_dir($log_dir)) {
			mkdir($log_dir, 0755, true);
		}

		//检测日志文件大小，超过配置大小则备份日志文件重新生成
		if (is_file($destination) && floor(1000000) <= filesize($destination)) {
			rename($destination, dirname($destination) . '/' . time() . '-' . basename($destination));
		}

		error_log(date('[ c ]') . "\r\n{$log}\r\n", 3, $destination);
	}
}