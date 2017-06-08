
<?php
	/**
	 * @desc: 常用的PHP函数-字符串函数
	 * @date: 2015/11/25 18:11
	 * @author: Tan <tanda517886160@163.com>
	 * @version: 1.0
	 */

	/**
	 * 检测文件是否符合过滤条件
	 *
	 * @param string $file
	 * @param array  $filter
	 * @return bool
	 */
	function is_ignore($file, $filter) {
		$filter = (array) $filter;
		$ext    = pathinfo($file, PATHINFO_EXTENSION);
		if (in_array($file, $filter) || in_array('.' . $ext, $filter)) {
			return true;
		} else {
			return false;
		}

	}

	/**
	 * 当前目录下的文件
	 *
	 * @param  string $dir
	 * @param  array  $filter
	 * @action is_ignore()
	 * @return array
	 */
	function list_dir($dir, $filter = []) {
		$filter = (array) $filter;
		if (!file_exists($dir) || !is_dir($dir)) {
			return '';
		}

		$dir  = new DirectoryIterator($dir);
		$list = [];
		while ($dir->valid()) {
			if (!$dir->isDot() && !is_ignore($dir->getFilename(), $filter)) //去除.和..以及被过滤文件
			{
				$list[$dir->key()]['name']  = $dir->getFilename();
				$list[$dir->key()]['isDir'] = $dir->isDir() ? 1 : 0;
			}
			$dir->next();
		}
		return $list;
	}

	/**
	 * 递归循环列出所有目录
	 *
	 * @param string $pathName
	 * @param array  $filter
	 * @action is_ignore()
	 * @return array
	 */
	function list_recursive_dir($pathName, $filter = []) {
		$tmp    = [];
		$result = [];
		if (!is_dir($pathName) || !is_readable($pathName)) {
			return null;
		}
		$allFiles = scandir($pathName);
		foreach ($allFiles as $fileName) {
			if (in_array($fileName, ['.', '..']) || is_ignore($fileName, $filter)) {
				continue;
			}

			$fullName = $pathName . '/' . $fileName;
			if (is_dir($fullName)) {
				$result[$fileName] = list_recursive_dir($fullName, $filter);
			} else {
				$temp[] = $fileName;
			}
		}
		if (isset($temp) && $temp) {
			foreach ($temp as $f) {
				$result[] = $f;
			}
		}
		return $result;
	}

	//$deep为是否深度遍历目录 默认遍历
	//支持中文目录 中文文件
	/**
	 * 遍历文件夹
	 * @param  string  $dir
	 * @param  boolean $deep 是否深度遍历目录 默认遍历
	 * @return string
	 */
	function listDir($dir, $deep = true) {
		global $arr;
		if (is_dir($dir)) {
			$fp = opendir($dir);
			while (false !== $file = readdir($fp)) {
				if ($deep && is_dir($dir . '/' . $file) && $file != '.' && $file != '..') {
					$file1 = iconv('gb2312', 'utf-8', $file);
					$arr[] = $file1; //保存目录名 可以取消注释
					echo "<b><font color='green'>目录名：</font></b>", $file1, "<br><hr>";
					listDir($dir . '/' . $file . '/');
				} else {
					if ($file != '.' && $file != '..') {
						$file = iconv('gb2312', 'UTF-8', $file);
						echo "<b><font color='red'>文件名：</font></b>", $file, "<br><hr>";
						$arr[] = $file;
					}
				}
			}
			closedir($fp);
		} else {
			echo $dir . '不是目录';
		}
	}

	/**
	 * 读取csv到数据
	 *
	 * @param  string $csv_file
	 * @param  string $delimiter 分隔符 默认"\t"
	 * @return array
	 */
	function get_csv_data($csv_file, $delimiter = "\t") {
		$row     = 0;
		$results = [];
		if (($handle = fopen($csv_file, "r")) !== FALSE) {
			while (($data = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
				$num = count($data);
				$row++;

				for ($c = 0; $c < $num; $c++) {
					$results[$row][$c] = $data[$c];
				}
			}
			fclose($handle);
		}
		return $results;
	}

	class file {
		static public function ShowDir($path, $level = 0) {
			$dir = opendir($path);
			while (($file = readdir($dir)) !== false) {
				if ($file != '.' && $file != '..') {
					$filepath = $path . '/' . $file;
					for ($i = 0; $i < $level * 5; $i++) {
						echo "&nbsp;";
					}
					echo $file . '<br>';
					if (is_dir($filepath)) {
						static::ShowDir($filepath, $level + 1);
					}
				}
			}
			closedir($dir);
		}
		static public function DeleteDir($path) {
			$dir = opendir($path);
			while (($file = readdir($dir)) !== false) {
				if ($file != '.' && $file != '..') {
					$filepath = $path . '/' . $file;
					if (is_dir($filepath)) {
						self::DeleteDir($filepath);
					} else {
						unlink($filepath);
					}
				}
			}
			closedir($dir);
			rmdir($path);
		}
	}

	/**
	 * 写INI文件
	 *
	 * @param      $assoc_arr
	 * @param      $path
	 * @param bool $has_sections
	 * @return bool
	 */
	function write_ini_file($assoc_arr, $path, $has_sections = FALSE) {
		$content = "";
		if ($has_sections) {
			foreach ($assoc_arr as $key => $elem) {
				$content .= "[" . $key . "]" . PHP_EOL;
				foreach ($elem as $key2 => $elem2) {
					if (is_array($elem2)) {
						for ($i = 0; $i < count($elem2); $i++) {
							$content .= $key2 . "[] = " . $elem2[$i] . PHP_EOL;
						}
					} else if ($elem2 == "") {
						$content .= $key2 . " = " . PHP_EOL;
					} else {
						$content .= $key2 . " = " . $elem2 . PHP_EOL;
					}
				}
			}
		} else {
			foreach ($assoc_arr as $key => $elem) {
				if (is_array($elem)) {
					for ($i = 0; $i < count($elem); $i++) {
						$content .= $key . "[] = " . $elem[$i] . PHP_EOL;
					}
				} else if ($elem == "") {
					$content .= $key . " = " . PHP_EOL;
				} else {
					$content .= $key . " = " . $elem . PHP_EOL;
				}
			}
		}
		if (!$handle = fopen($path, 'w')) {
			return false;
		}
		if (!fwrite($handle, $content)) {
			return false;
		}
		fclose($handle);
		return true;
	}

	// 获取远程文件的大小
	function remote_filesize($url, $user = "", $pw = "") {
		ob_start();
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_NOBODY, 1);

		if (!empty($user) && !empty($pw)) {
			$headers = ['Authorization: Basic ' . base64_encode("$user:$pw")];
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		}

		$ok = curl_exec($ch);
		curl_close($ch);
		$head = ob_get_contents();
		ob_end_clean();

		$regex = '/Content-Length:\s([0-9].+?)\s/';
		$count = preg_match($regex, $head, $matches);

		return isset($matches[1]) ? $matches[1] . " 字节" : "unknown";
	}

	// 实例测试
	echo remote_filesize("http://img105.job1001.com/upload/adminnew/2014-10-14/1413272802-SEILFHG.jpg");

	//使用五种以上方式获取一个文件的扩展名
	function get_ext1($file_name) {
		return strrchr($file_name, '.');
	}
	function get_ext2($file_name) {
		return substr($file_name, strrpos($file_name, '.'));
	}
	function get_ext3($file_name) {
		return array_pop(explode('.', $file_name));
	}
	function get_ext4($file_name) {
		return pathinfo($file_name, PATHINFO_EXTENSION);
	}
	function get_ext5($file_name) {
		return strrev(substr(strrev($file_name), 0, strpos(strrev($file_name), '.')));
	}

	/**
	 * PHP文件锁定写入实例
	 * @param  string  $file_name
	 * @param  string  $text
	 * @param  string  $mode
	 * @param  integer $timeout
	 * @return bool
	 */
	function write_file($file_name, $text, $mode = 'a', $timeout = 30) {
		$handle = fopen($file_name, $mode);
		while ($timeout > 0) {
			if (flock($handle, LOCK_EX)) {
				// 排它性的锁定
				$timeout--;
				sleep(1);
			}
		}

		if ($timeout > 0) {
			    fwrite($handle, $text . '\n');
			    flock($handle, LOCK_UN);
			    fclose($handle); //释放锁定操作
			return true;
		}
		return false;
	}

	/**
	 * Tests for file writability
	 *
	 *  1、在windowns中，当文件只有只读属性时，is_writeable()函数才返回false，当返回true时，该文件不一定是可写的。
	如果是目录，在目录中新建文件并通过打开文件来判断；
	如果是文件，可以通过打开文件（fopen），来测试文件是否可写。
	2、在Unix中，当php配置文件中开启safe_mode时(safe_mode=on)，is_writeable()同样不可用。
	读取配置文件是否safe_mode是否开启。

	 * is_writable() returns TRUE on Windows servers when you really can't write to
	 * the file, based on the read-only attribute.  is_writable() is also unreliable
	 * on Unix servers if safe_mode is on.
	 *
	 * @access    private
	 * @return    void
	 */
	if (!function_exists('is_really_writable')) {
		function is_really_writable($file) {
			// If we're on a Unix server with safe_mode off we call is_writable
			if (DIRECTORY_SEPARATOR == '/' AND @ini_get("safe_mode") == FALSE) {
				return is_writable($file);
			}

			// For windows servers and safe_mode "on" installations we'll actually
			// write a file then read it.  Bah...
			if (is_dir($file)) {
				$file = rtrim($file, '/') . '/' . md5(mt_rand(1, 100) . mt_rand(1, 100));

				if (($fp = @fopen($file, FOPEN_WRITE_CREATE)) === FALSE) {
					return FALSE;
				}

				fclose($fp);
				@chmod($file, DIR_WRITE_MODE);
				@unlink($file);
				return TRUE;
			} elseif (!is_file($file) OR ($fp = @fopen($file, FOPEN_WRITE_CREATE)) === FALSE) {
				return FALSE;
			}

			fclose($fp);
			return TRUE;
	}
}