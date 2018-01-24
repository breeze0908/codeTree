<?php
/**
 * @desc: 常用的PHP函数-字符串函数
 * @date: 2015/11/25 18:11
 * @author: Tan <tanda517886160@163.com>
 * @version: 1.0
 */

/**
* 下划线转驼峰
* 思路:
* step1.原字符串转小写,原字符串中的分隔符用空格替换,在字符串开头加上分隔符
* step2.将字符串中每个单词的首字母转换为大写,再去空格,去字符串首部附加的分隔符.
*/
function camelize($uncamelized_words,$separator='_')
{
    $uncamelized_words = $separator. str_replace($separator, " ", strtolower($uncamelized_words));
    return ltrim(str_replace(" ", "", ucwords($uncamelized_words)), $separator );
}

/**
* 驼峰命名转下划线命名
* 思路:
* 小写和大写紧挨一起的地方,加上分隔符,然后全部转小写
*/
function uncamelize($camelCaps,$separator='_')
{
    return strtolower(preg_replace('/([a-z])([A-Z])/', "$1" . $separator . "$2", $camelCaps));
}


/**
 * 将字符串转换成二进制
 *
 * @param  string $str 字符串转成二进制
 * @return
 */
function str_to_bin($str){
	$arr = preg_split('/(?<!^)(?!$)/u', $str);
	foreach($arr as &$v){
		$temp = unpack('H*', $v); $v = base_convert($temp[1], 16, 2);
		unset($temp);
	}

	return join(' ',$arr);
}

/**
 * 二进制转换成字符串
 *
 * @param  string $value
 * @return
 */
function bin_to_str($str)
{
	$arr = explode(' ', $str);
	foreach($arr as &$v){
		$v = pack("H".strlen(base_convert($v, 2, 16)), base_convert($v, 2, 16));
	}
	return join('', $arr);
}

/**
 * 字符串截取类
 *
 * @param string $string
 * @param int    $length
 * @param string $suffix
 * @return string
 */
function cut_string($string, $length = 300, $suffix = '') {
	$string   = mb_convert_encoding($string, 'UTF-8', 'gb2312, UTF-8');
	$s_length = mb_strlen($string, 'UTF-8');

	if ($length >= $s_length) {
		$string = mb_substr($string, 0, $length, 'UTF-8') . $suffix;
	}
	return $string;
}

//防SQL注入
function cleanInput($input) {

	$search = [
		'@<script[^>]*?>.*?</script>@si', // Strip out javascript
		'@<[\/\!]*?[^<>]*?>@si', // Strip out HTML tags
		'@<style[^>]*?>.*?</style>@siU', // Strip style tags properly
		'@<![\s\S]*?--[ \t\n\r]*>@', // Strip multi-line comments
	];

	$output = preg_replace($search, '', $input);
	return $output;
}

function sanitize($input) {
	if (is_array($input)) {
		foreach ($input as $var => $val) {
			$output[$var] = sanitize($val);
		}
	} else {
		if (get_magic_quotes_gpc()) {
			$input = stripslashes($input);
		}
		$input  = cleanInput($input);
		$output = mysql_real_escape_string($input);
	}
	return $output;
}

/**
 *	获取data数组中的参数的签名
 * @param  [type] $data [description]
 * @param  [type] $key  [description]
 * @return [type]       [description]
 */
function sign($data, $key) {
	if (isset($data['sign'])) {
		unset($data['sign']);
	}

	ksort($data);
	$query = http_build_query($data);
	return md5($query . $key);
}
//测试
if (sign($data, 'QXqK0w3mmsr9YUAr') != $_GET['sign']) {
	exit(ajaxResponse(500));
}

/**
 * 人性化的显示
 *
 * @param $size
 * @return string
 */
function convert($size) {
	$unit = ['b', 'kb', 'mb', 'gb', 'tb', 'pb'];
	return @round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $unit[$i];
}


function size2int($filesize) {
	$filesize = strtoupper($filesize);
	if (strpos($filesize, 'GB')) {
		$filesize = intval($filesize) * 1073741824;
	} elseif (strpos($filesize, 'MB')) {
		$filesize = intval($filesize) * 1048576;
	} elseif (strpos($filesize, 'KB')) {
		$filesize = intval($filesize) * 1024;
	} elseif (strpos($filesize, 'KB')) {
		$filesize = intval($filesize);
	}
	return $filesize;
}


/**
 * 格式化金钱值 如：12345.2 ==> 12,345.20
 * @param  string $val
 * @return string
 */
function format_money($val) {
	if ($val === "") {
		return "";
	}
	if ($val == ".00") {
		return "0.00";
	}

	if (!is_numeric($val)) {
		return '';
	}

	$tmp = explode('.', sprintf('%.2f', $val));
	return number_format($tmp[0]) . "." . $tmp[1];
}


/**
 * 字符截取 支持UTF8/GBK
 * @param $string
 * @param $length
 * @param $dot
 */
function str_cut($string, $length, $dot = '...') {
	$strlen = strlen($string);
	if ($strlen <= $length) {
		return $string;
	}

	$string = str_replace([' ', '&nbsp;', '&amp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;'], ['∵', ' ', '&', '"', "'", '“', '”', '—', '<', '>', '·', '…'], $string);
	$strcut = '';
	if (strtolower(CHARSET) == 'utf-8') {
		$length = intval($length - strlen($dot) - $length / 3);
		$n      = $tn      = $noc      = 0;
		while ($n < strlen($string)) {
			$t = ord($string[$n]);
			if ($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
				$tn = 1;
				$n++;
				$noc++;
			} elseif (194 <= $t && $t <= 223) {
				$tn = 2;
				$n += 2;
				$noc += 2;
			} elseif (224 <= $t && $t <= 239) {
				$tn = 3;
				$n += 3;
				$noc += 2;
			} elseif (240 <= $t && $t <= 247) {
				$tn = 4;
				$n += 4;
				$noc += 2;
			} elseif (248 <= $t && $t <= 251) {
				$tn = 5;
				$n += 5;
				$noc += 2;
			} elseif ($t == 252 || $t == 253) {
				$tn = 6;
				$n += 6;
				$noc += 2;
			} else {
				$n++;
			}
			if ($noc >= $length) {
				break;
			}
		}
		if ($noc > $length) {
			$n -= $tn;
		}
		$strcut = substr($string, 0, $n);
		$strcut = str_replace(['∵', '&', '"', "'", '“', '”', '—', '<', '>', '·', '…'], [' ', '&amp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;'], $strcut);
	} else {
		$dotlen      = strlen($dot);
		$maxi        = $length - $dotlen - 1;
		$current_str = '';
		$search_arr  = ['&', ' ', '"', "'", '“', '”', '—', '<', '>', '·', '…', '∵'];
		$replace_arr = ['&amp;', '&nbsp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;', ' '];
		$search_flip = array_flip($search_arr);
		for ($i = 0; $i < $maxi; $i++) {
			$current_str = ord($string[$i]) > 127 ? $string[$i] . $string[++$i] : $string[$i];
			if (in_array($current_str, $search_arr)) {
				$key         = $search_flip[$current_str];
				$current_str = str_replace($search_arr[$key], $replace_arr[$key], $current_str);
			}
			$strcut .= $current_str;
		}
	}
	return $strcut . $dot;
}

/**
 * 检查字符串是否是UTF8编码
 * @param  string    $string 字符串
 * @return Boolean
 */
function is_utf8($string) {
	return preg_match('%^(?:
       [\x09\x0A\x0D\x20-\x7E]            # ASCII
       | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
       |  \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
       | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
       |  \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
       |  \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
       | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
       |  \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
       )*$%xs', $string);
}

/**
 * 代码加亮
 * @param  String   $str  要高亮显示的字符串 或者 文件名
 * @param  Boolean  $show 是否输出
 * @return String
 */
function highlight_code($str, $show = false) {
	if (file_exists($str)) {
		$str = file_get_contents($str);
	}
	$str = stripslashes(trim($str));
	// The highlight string function encodes and highlights
	// brackets so we need them to start raw
	$str = str_replace(['&lt;', '&gt;'], ['<', '>'], $str);

	// Replace any existing PHP tags to temporary markers so they don't accidentally
	// break the string out of PHP, and thus, thwart the highlighting.

	$str = str_replace(['&lt;?php', '?&gt;', '\\'], ['phptagopen', 'phptagclose', 'backslashtmp'], $str);

	// The highlight_string function requires that the text be surrounded
	// by PHP tags.  Since we don't know if A) the submitted text has PHP tags,
	// or B) whether the PHP tags enclose the entire string, we will add our
	// own PHP tags around the string along with some markers to make replacement easier later

	$str = '<?php //tempstart' . "\n" . $str . '//tempend ?>'; // <?

	// All the magic happens here, baby!
	$str = highlight_string($str, TRUE);

	// Prior to PHP 5, the highlight function used icky font tags
	// so we'll replace them with span tags.
	if (abs(phpversion()) < 5) {
		$str = str_replace(['<font ', '</font>'], ['<span ', '</span>'], $str);
		$str = preg_replace('#color="(.*?)"#', 'style="color: \\1"', $str);
	}

	// Remove our artificially added PHP
	$str = preg_replace("#\<code\>.+?//tempstart\<br />\</span\>#is", "<code>\n", $str);
	$str = preg_replace("#\<code\>.+?//tempstart\<br />#is", "<code>\n", $str);
	$str = preg_replace("#//tempend.+#is", "</span>\n</code>", $str);

	// Replace our markers back to PHP tags.
	$str    = str_replace(['phptagopen', 'phptagclose', 'backslashtmp'], ['&lt;?php', '?&gt;', '\\'], $str); //<?
	$line   = explode("<br />", rtrim(ltrim($str, '<code>'), '</code>'));
	$result = '<div class="code"><ol>';
	foreach ($line as $key => $val) {
		$result .= '<li>' . $val . '</li>';
	}
	$result .= '</ol></div>';
	$result = str_replace("\n", "", $result);
	if ($show !== false) {
		echo ($result);
	} else {
		return $result;
	}
}