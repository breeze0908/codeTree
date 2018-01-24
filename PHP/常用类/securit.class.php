<?php
// +----------------------------------------------------------------------+
// | The file description                                                 |
// +----------------------------------------------------------------------+
// | Author:  Tan <tandamailzone@gmail.com>                               |
// +----------------------------------------------------------------------+
// | Copyright (c) 2017, Wondershare Inc. All rights reserved.            |
// |                                                                      |
// +----------------------------------------------------------------------+

/**
 * 加解密方法类
 */
class Securit {
	const ACCOUNT_AES_ENCRYPT_KEY = 'XXXXXX';
	const ACCOUNT_AES_ENCRYPT_IV  = 'XXXXXX';

	/**
	 * 加解密
     *
	 * @param type $input
	 * @param type $opt
	 * @return boolean
	 */
	public static function cart_encrypt($input, $opt = 'decode') {
		if ($opt == 'decode' && !empty($input)) {
			$input = self::base64UrlDecode($input);
			$data  = self::encrypt($input, 'decrypt');
			$data  = rtrim($data, "\0\7\4");

			return json_decode($data, true);
		} else if ($opt == 'encode' && is_array($input)) {
			$str          = json_encode($input);
			$encrypt_data = self::encrypt($str, 'encrypt');
			return self::base64UrlEncode($encrypt_data);
		}
		return false;
	}
	/**
	 * AES加密方法
	 *
	 * @param string $input 加解密的原文
	 * @param string $type 加解密方向，默认为加密方向
	 * @param string $key 加密密钥，可置空
	 * @return string $retutn 处理后的字串
	 */
	public static function encrypt($input, $type = 'encrypt', $key = '') {
		$default_key = self::ACCOUNT_AES_ENCRYPT_KEY;
		$iv          = self::ACCOUNT_AES_ENCRYPT_IV;

		$private_key = $key ? $key : $default_key;
		$private_key = str_pad($private_key, 32, ' ');

		if ($type == 'encrypt') {
			$str_utf8 = utf8_encode($input);
			$return   = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $private_key, $str_utf8, MCRYPT_MODE_CBC, $iv);
		} else {
			$str_utf8 = $input;
			$return   = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $private_key, $str_utf8, MCRYPT_MODE_CBC, $iv);
		}

		return $return;
	}

	/**
	 * Base64 encoding that doesn't need to be urlencode()ed.
     *
	 * @param string $string string
	 * @return string base64Url encoded string
	 */
	public static function base64UrlEncode($string) {
		$data = base64_encode($string);
		$data = str_replace(['+', '/', '='], ['-', '_', ''], $data);

		return $data;
	}

	public static function base64UrlDecode($string) {
		$data = str_replace(['-', '_'], ['+', '/'], $string);
		$mod4 = strlen($data) % 4;

		if ($mod4) {
			$data .= substr('====', $mod4);
		}

		return base64_decode($data);
	}
}