<?php

/**
 * PHP 算法实现
 */

//冒泡排序（数组排序）
function bubble_sort($array) {
	$count = count($array);
	if ($count <= 0) {
		return false;
	}

	for ($i = 0; $i < $count; $i++) {
		for ($j = $i; $j < $count - 1; $j++) {
			if ($array[$i] > $array[$j]) {
				$tmp       = $array[$i];
				$array[$i] = $array[$j];
				$array[$j] = $tmp;
			}
		}
	}
	return $array;
}

//快速排序（数组排序）
function quick_sort($array) {
	if (count($array) <= 1) {
		return $array;
	}

	$key       = $array[0];
	$left_arr  = [];
	$right_arr = [];
	for ($i = 1; $i < count($array); $i++) {
		if ($array[$i] <= $key) {
			$left_arr[] = $array[$i];
		} else {
			$right_arr[] = $array[$i];
		}

	}
	$left_arr  = quick_sort($left_arr);
	$right_arr = quick_sort($right_arr);
	return array_merge($left_arr, [$key], $right_arr);
}

//PHP描述顺序查找,必须考虑效率（数组里查找某个元素）
function seq_sch($array, $n, $k) {
	$array[$n] = $k;
	for ($i = 0; $i < $n; $i++) {
		if ($array[$i] == $k) {
			break;
		}
	}
	if ($i < $n) {
		return $i;
	} else {
		return -1;
	}
}

//二分查找,也叫做折半查找（数组里查找某个元素）
function bin_sch($array, $low, $high, $k) {
	if ($low <= $high) {
		$mid = intval(($low + $high) / 2);
		if ($array[$mid] == $k) {
			return $mid;
		} elseif ($k < $array[$mid]) {
			return bin_sch($array, $low, $mid - 1, $k);
		} else {
			return bin_sch($array, $mid + 1, $high, $k);
		}
	}
	return -1;
}

//二维数组排序算法函数，能够具有通用性，可以调用php内置函数
//二维数组排序， $arr是数据，$keys是排序的健值，$order是排序规则，1是升序，0是降序
function array_sort($arr, $keys, $order = 0) {
	if (!is_array($arr)) {
		return false;
	}
	$keysvalue = [];
	foreach ($arr as $key => $val) {
		$keysvalue[$key] = $val[$keys];
	}
	if ($order == 0) {
		asort($keysvalue);
	} else {
		arsort($keysvalue);
	}
	reset($keysvalue);
	foreach ($keysvalue as $key => $vals) {
		$keysort[$key] = $key;
	}
	$new_array = [];
	foreach ($keysort as $key => $val) {
		$new_array[$key] = $arr[$val];
	}
	return $new_array;
}