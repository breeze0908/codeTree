<?php

//array(0=>array('name'=>'栏目1', 'pid' => 0, 'id'=>1), 
//		1=>array('name'=>'栏目2', 'pid' => 0, 'id'=>2),
//		3=>array('name'=>'栏目3', 'pid' => 1, 'id'=>3));
//		
function recursion(&$arr, $pid = 0, $level = 1)
{
	$sub_tree = array();

	foreach ($arr as $key => $value) {
		if($pid == $value['pid']) {
			unset($arr[$key]); //先从数组中摘除
			$value['level'] = $level;
			$value['subs'] = recursion(&$arr, $pid, $level+1);
			$sub_tree[] = $value;
		}
	}
	return $sub_tree;
}