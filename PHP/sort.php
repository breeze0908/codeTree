<?php
$array = [
	[
		'a' => 'abcdef',
		'b' => '123456',
		'c' => 'TTYYUU',
	],
	[
		'a' => 'bcedfg',
		'b' => '123456',
		'c' => 'YYUUKK',
	],
	[
		'a' => 'abcdef',
		'b' => '789100',
		'c' => 'UUKKOO',
	],
];

class ArraySort {
	/**
	 * 排序的key
	 * @var array
	 */
	protected $keys = [];

	/**
	 * 构造函数
	 * @param array|string $keys 需要比较的键
	 */
	public function __construct($keys) {
		if (is_string($keys)) {
			$this->keys[] = $keys;
		} else {
			$this->keys = $keys;
		}
	}

	/**
	 * 执行排序
	 *
	 * @param  array $array
	 * @return
	 */
	public function sort($array) {
		usort($array, [$this, "_array_compare"]);
		return $array;
	}

	/**
	 * 比较两个值的大小，字符串or数字
	 *
	 * @param  string|integer $val1 [description]
	 * @param  string|integer $val2 [description]
	 * @return integer
	 */
	private function _key_compare($val1, $val2) {
		return $val1 > $val2 ? 1 : ($val1 < $val2 ? -1 : 0);
	}

	/**
	 * 比较两个数组
	 *
	 * @param  array $arr1 [description]
	 * @param  array $arr2 [description]
	 * @return integer
	 */
	private function _array_compare($arr1, $arr2) {
		foreach ($this->keys as $key) {
			$compare_val = $this->_key_compare($arr1[$key], $arr2[$key]);
			if ($compare_val != 0) {
				return $compare_val;
			}
		}
		return 0;
	}
}

$arraySort = new ArraySort(['a', 'b', 'c']);
$array     = $arraySort->sort($array);
var_dump($array);
