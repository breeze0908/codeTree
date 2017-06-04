<?php
//最大子串和问题求解
//已知对于序列 {N1, N2, N3, N4,.... Nk}, 当1<= i < j <= k时存在最长子序列 {Ni,Ni+1, .... Nj}，其序列中值的和为最大值，
//比如 {1, -2, -1, 26, 0 , 13, 0, -2, 6}的最长子序列为 {26, 0 , 13}

//f(x) = max(f(x-1)+a(x)), a(x));


//只计算最长子串的和
function maxSubNum($array) {
	$sum = 0; 
	$max = 0;	
	for($i = 0; $i < count($array); $i++){
		$sum = $sum + $array[$i];
		if($sum > $max) {
			$max = $sum;
		}

		if($sum < 0){
			$sum = 0; //重新从0开始相加
		}
	}
	return $max;
}

//计算最长子串
function maxSubArr($array) {
	$sum = 0; 
	$max = 0;
	$maxArr = array();
	for($i = 0; $i < count($array); $i++){
		$sum = $sum + $array[$i];
		if($sum > $max) {
			$max = $sum;
		}

		if($sum < 0){
			$sum = 0; //重新从0开始相加
			$maxArr = array();
		}else{
			array_push($maxArr, $array[$i]);
		}
	}
	return $maxArr;
}


//穷举法
function getMaxSubStrNum($array){
	$max = $array[0];
	for($i = 0; $i < count($array); $i++) {
		$sum = $array[$i];

		for($j = $i+1; $j < count($array); $j++) {
			$sum = $sum + $array[$j];
			if($sum > $max) {
				$max = $sum;
				$start = $i;    //子串数组开始的地方
				$end = $j;  	//子串结束的地方
			}
		}
	}

	
	//print_r(array_slice($array,$start,$end-$start+1));
	return $max;
}


$array = [1, -2, -1, 26, 0 , 13, 0, -2, 6];
print_r($array);
$c1 = maxSubNum($array);
var_dump($c1);

$c2 = maxSubArr($array);
var_dump($c2);

$c3 = getMaxSubStrNum($array);
var_dump($c3);