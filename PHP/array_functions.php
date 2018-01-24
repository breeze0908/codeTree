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
    $results = [];
    if (is_array($array)) {
        foreach ($array as $key => $item) {
            is_array($item) && isset($item[$field]) && $results[] = $item[$field];
        }
    }
    return $results;
}

/**
 * 检查id是否存在于数组中
 *
 * @param string|integer|array $id
 * @param array $ids
 * @param string $delimiter
 */
function array_exists($value, $array) {
    if (!is_array($array)) {
        return false;
    }
    return is_array($value) ? ($value == array_intersect($value, $array)) : in_array($value, $array);
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

/**
 * 数组深度(维度)
 *
 * @param  array  $array 数组
 * @return integer
 */
function array_depth($array) {
	if (!is_array($array)) {
		return 0;
	}

	$max_depth = 1;
	foreach ($array as $value) {
		if (is_array($value)) {
			$depth     = call_user_func(__FUNCTION__, $value) + 1;
			$max_depth = $depth > $max_depth ? $depth : $max_depth;
		}
	}
	return $max_depth;
}

/**
 * 判断数组是不是多维数组
 *
 * @param  array   $array 数组
 * @return boolean
 */
function is_multi_dimension($array) {
	//COUNT_RECURSIVE或者1 表示将递归地对数组计数
	if (count($array) == count($array, 1)) {
		return true;
	} else {
		return false;
	}
}