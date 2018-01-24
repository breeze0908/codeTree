<?php
function curl($url, $method = 'GET', $fields = [], $headers = [], $auth = false) {
	//如果是get的获取方式，拼接参数到url上
	if ($method == "GET") {
		$fields_string = http_build_query($fields);
		$url           = $url . "?" . $fields_string;
	}
	$curl = curl_init($url); //初始化
	curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method); //设定HTTP请求方式
	curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20061204 Firefox/2.0.0.1");
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_VERBOSE, 1);
	curl_setopt($curl, CURLOPT_HEADER, 1);
	$header[0] = "Accept: text/xml,application/xml,application/xhtml+xml,";
	$header[0] .= "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
	$header[] = "Cache-Control: max-age=0";
	$header[] = "Connection: keep-alive";
	$header[] = "Keep-Alive: 300";
	$header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
	$header[] = "Accept-Language: en-us,en;q=0.5";
	$header[] = "Pragma: "; // browsers keep this blank.
	curl_setopt($curl, CURLOPT_HTTPHEADER, array_merge($header, $headers)); //和参数中的header一起传递过去
	if ($auth) {
		curl_setopt($curl, CURLOPT_USERPWD, "$auth");
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	}
	if ($fields) {
		//POST
		if ($method == "POST") {
//单独对POST方法设置参数传递
			$fields_string = http_build_query($fields);
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_BINARYTRANSFER, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $fields_string);
		} else {
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_BINARYTRANSFER, true);
		}
	}
	$response      = curl_exec($curl); //执行curl
	$header_size   = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
	$header_string = substr($response, 0, $header_size);
	$body          = substr($response, $header_size);
	$header_rows   = explode(PHP_EOL, $header_string);
	foreach ($header_rows as $key => $value) {
		$header_rows[$key] = trim($header_rows[$key]);
	}
	$i = 0;
	foreach ((array) $header_rows as $hr) {
		$colonpos      = strpos($hr, ':');
		$key           = $colonpos !== false ? substr($hr, 0, $colonpos) : (int) $i++;
		$headers[$key] = $colonpos !== false ? trim(substr($hr, $colonpos + 1)) : $hr;
	}
	$j = 0;
	foreach ((array) $headers as $key => $val) {
		$vals = explode(';', $val);
		if (count($vals) >= 2) {
			unset($headers[$key]);
			foreach ($vals as $vk => $vv) {
				$equalpos             = strpos($vv, '=');
				$vkey                 = $equalpos !== false ? trim(substr($vv, 0, $equalpos)) : (int) $j++;
				$headers[$key][$vkey] = $equalpos !== false ? trim(substr($vv, $equalpos + 1)) : $vv;
			}
		}
	}
	curl_close($curl);
	return [$body, $headers]; //最终返回 result[0]为body,result[1]为header
}

function get_taobao_location($ip_address) {
	$ip_info = @file_get_contents('http://ip.taobao.com/service/getIpInfo.php?ip=' . $ip_address);
	if (empty($ip_info)) {
		return "未知";
	}
	$ip_arr = json_decode($ip_info, true);
	if ($ip_arr['code'] == 0) {
		return $ip_arr['data']['region'] . '-' . $ip_arr['data']['city'];
	}
	return "未知";
}

/**
 * curl POST
 *
 * @param   string  url
 * @param   array   数据
 * @param   int     请求超时时间
 * @param   bool    HTTPS时是否进行严格认证
 * @return  string
 */
function curlPost($url, $data = [], $timeout = 30, $CA = true) {

	$cacert = getcwd() . '/cacert.pem'; //CA根证书
	$SSL    = substr($url, 0, 8) == "https://" ? true : false;

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout - 2);

	if ($SSL && $CA) {
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // 只信任CA颁布的证书
		curl_setopt($ch, CURLOPT_CAINFO, $cacert); // CA根证书（用来验证的网站证书是否是CA颁布）
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); // 检查证书中是否设置域名，并且是否与提供的主机名匹配
	} else if ($SSL && !$CA) {
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 信任任何证书
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1); // 检查证书中是否设置域名
	}

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, ['Expect:']); //避免data数据过长问题
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	//curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data)); //data with URLEncode

	$ret = curl_exec($ch);
	//var_dump(curl_error($ch));  //查看报错信息

	curl_close($ch);
	return $ret;
}

?>