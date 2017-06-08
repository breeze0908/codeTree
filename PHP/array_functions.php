<?php
/**
 * @desc: 常用的PHP函数-数组函数
 * @date: 2015/11/25 18:11
 * @author: Tan <tanda517886160@163.com>
 * @version: 1.0
 */

/**
 * 对象转数组,使用get_object_vars返回对象属性组成的数组
 *
 * @param  object $obj
 * @return array
 */
function objectToArray($obj) {
	$arr = is_object($obj) ? get_object_vars($obj) : $obj;
	if (is_array($arr)) {
		return array_map(__FUNCTION__, $arr);
	} else {
		return $arr;
	}
}

/**
 * 数组转对象
 *
 * @param  object $obj
 * @return array
 */
function arrayToObject($arr) {
	if (is_array($arr)) {
		return (object) array_map(__FUNCTION__, $arr);
	} else {
		return $arr;
	}
}

/**
 * 函数从数组返回指定的键值对
 *
 * @param array $array
 * @param array $keys
 * @return array
 */
function array_only($array, $keys) {
	return array_intersect_key($array, array_flip((array) $keys));
}

/**
 * 函数从数组返回指定的键以外的键值对
 *
 * @param array $array
 * @param array $keys
 * @return array
 */
function array_except($array, $keys) {
	return array_diff_key($array, array_flip((array) $keys));
}

/**
 * 从多维数组中拉出一列指定的键值对
 *
 * @param $array
 * @param $field
 * @return array
 */
function array_plunk($array, $field) {
	$tmp_array = [];
	foreach ($array as $key => $arr) {
		is_array($arr) && isset($arr[$field]) && $tmp_array[] = $arr[$field];
	}
	return $tmp_array;
}

/* 通过回调函数过滤数组
 *
 * @param  array  $array
 * @param  callable  $callback
 * @return array
 */
function array_where($array, $callback) {
	$filtered = [];

	foreach ($array as $key => $value) {
		if (call_user_func($callback, $key, $value)) {
			$filtered[$key] = $value;
		}
	}

	return $filtered;
}